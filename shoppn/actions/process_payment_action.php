<?php

/*
 * actions/process_payment_action.php (Task 13)
 *
 * Verifies Paystack transaction server-side and records the order.
 *
 * Security:
 * - Requires active customer session.
 * - Server-side verification via Paystack API (never trusts client callback alone).
 * - Verifies status == 'success', amount matches cart total, currency matches GHS,
 *   and email matches active session.
 * - Prevents duplicate order recording from replayed references.
 * - Records order inside a transactional database method.
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';
require_once __DIR__ . '/../core/email_helper.php';

// Customers must be logged in to process payment
require_login();

$customerId = (int) $_SESSION['customer_id'];
$customerEmail = trim((string) ($_SESSION['customer_email'] ?? ''));
$ip = get_ip();

// Retrieve reference from GET or POST
$reference = trim($_GET['reference'] ?? $_GET['ref'] ?? $_POST['reference'] ?? '');

if ($reference === '') {
    $_SESSION['error'] = 'Invalid payment reference received.';
    redirect('/views/payment_failed.php?reason=missing_reference');
}

$cartController = new CartController();

// 1. Prevent duplicate processing
if ($cartController->isReferenceProcessed($reference)) {
    $_SESSION['error'] = 'This payment transaction has already been processed.';
    redirect('/views/payment_failed.php?reason=duplicate_reference');
}

// 2. Fetch current cart total and items
$cartItems = $cartController->getCartItems($ip);
$expectedTotal = (float) $cartController->getCartTotal($ip);

if (empty($cartItems) || $expectedTotal <= 0) {
    $_SESSION['error'] = 'Your cart is empty. No order could be processed.';
    redirect('/views/cart.php');
}

$expectedAmountInPesewas = (int) round($expectedTotal * 100);

// 3. Verify payment with Paystack Verify API
$isVerified = false;
$verifyError = '';
$failureCode = 'unknown';
$verifiedAmount = 0.0;
$verifiedCurrency = 'GHS';

// Always verify with Paystack. Local mock references must never create paid orders.
if (!function_exists('curl_init')) {
    $verifyError = 'Payment verification is unavailable on this server. Contact the administrator.';
    $failureCode = 'curl_unavailable';
} elseif (PAYSTACK_SECRET_KEY === '' || strpos(PAYSTACK_SECRET_KEY, 'sk_test_') !== 0) {
    $verifyError = 'Paystack test mode has not been configured correctly.';
    $failureCode = 'key_configuration';
} else {
    $verifyUrl = 'https://api.paystack.co/transaction/verify/' . rawurlencode($reference);
    $ch = curl_init($verifyUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER => [
            'Authorization: Bearer ' . PAYSTACK_SECRET_KEY,
            'Cache-Control: no-cache',
            'User-Agent: ShopPN-Ecommerce/1.0'
        ],
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_TIMEOUT => 25,
    ]);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($response === false || $httpCode !== 200) {
        $verifyError = 'Paystack verification request could not be completed.';
        $failureCode = $httpCode > 0 ? 'paystack_http_' . (int) $httpCode : 'paystack_connection';
        error_log('ShopPN Paystack verification transport: HTTP ' . $httpCode . ' ' . $curlError);
    } else {
        $result = json_decode($response, true);
        $data = is_array($result) ? ($result['data'] ?? []) : [];
        $txStatus = strtolower((string) ($data['status'] ?? ''));
        $txAmount = (int) ($data['amount'] ?? 0);
        $txCurrency = strtoupper((string) ($data['currency'] ?? ''));
        $txEmail = strtolower(trim((string) ($data['customer']['email'] ?? '')));
        $txReference = (string) ($data['reference'] ?? '');
        if (empty($result['status']) || $txStatus !== 'success') {
            $verifyError = 'Paystack did not confirm a successful transaction.';
            $failureCode = 'transaction_status';
        } elseif ($txReference !== $reference) {
            $verifyError = 'The payment reference did not match.';
            $failureCode = 'reference_mismatch';
        } elseif ($txAmount !== $expectedAmountInPesewas || $txCurrency !== 'GHS') {
            $verifyError = 'The paid amount or currency did not match your cart.';
            $failureCode = ($txCurrency !== 'GHS') ? 'currency_mismatch' : 'amount_mismatch';
        } elseif ($customerEmail === '' || $txEmail !== strtolower($customerEmail)) {
            $verifyError = 'The payer email did not match your account.';
            $failureCode = 'email_mismatch';
        } else {
            $isVerified = true;
            $verifiedAmount = $txAmount / 100.0;
            $verifiedCurrency = $txCurrency;
        }
    }
}

// 4. Handle failed verification
if (!$isVerified) {
    error_log('Paystack verification failed for ref ' . $reference . ': ' . $verifyError);
    $_SESSION['error'] = 'Payment verification could not be confirmed. Your cart has been kept. If your payment went through, contact support with its reference before retrying.';
    // Expose only a non-sensitive diagnostic category; never include API keys, emails or transaction references.
    redirect('/views/payment_failed.php?reason=verification_failed&check=' . rawurlencode($failureCode));
}

// 5. Record order in database using transaction
$orderId = $cartController->processPayment(
    $customerId,
    $ip,
    $reference,
    $verifiedAmount,
    $verifiedCurrency
);

if (!$orderId || $orderId <= 0) {
    $_SESSION['error'] = 'Database error recording your order. Please contact support with reference: ' . $reference;
    redirect('/views/payment_failed.php?reason=order_record_failed');
}

// 6. Send confirmation email (non-blocking)
$orderDetails = $cartController->getOrderById($orderId);
if ($orderDetails) {
    send_order_confirmation_email($orderDetails);
}

// 7. Redirect to invoice page on success
$_SESSION['success'] = 'Payment completed successfully! Thank you for your order.';
redirect('/views/payment_success.php?order_id=' . $orderId);
