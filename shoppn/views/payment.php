<?php

/*
 * views/payment.php (Task 13)
 *
 * Customer-facing payment page with Paystack integration.
 * - Displays order summary (products, thumbnails, quantities, prices, total).
 * - Enforces customer authentication (require_login).
 * - Launches Paystack Inline modal with customer email and total in pesewas.
 */

require_once __DIR__ . '/../core/core.php';
header('Cache-Control: private, no-store, max-age=0');
require_once __DIR__ . '/../controllers/CartController.php';

// Enforce customer login before payment
require_login();

$cartController = new CartController();
$ip = get_ip();
$cartItems = $cartController->getCartItems($ip);
$cartTotal = (float) $cartController->getCartTotal($ip);

// If cart is empty, redirect to cart page
if (empty($cartItems) || $cartTotal <= 0) {
    $_SESSION['error'] = 'Your cart is empty. Please add products before proceeding to payment.';
    redirect('/views/cart.php');
}

$customerEmail = $_SESSION['customer_email'] ?? '';
$customerName = $_SESSION['customer_name'] ?? 'Customer';
$amountInPesewas = (int) round($cartTotal * 100);
$paystackReady = PAYSTACK_PUBLIC_KEY !== '' && strpos(PAYSTACK_PUBLIC_KEY, 'pk_test_') === 0;

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="payment-section" style="flex: 1; padding: 24px 28px; min-width: 0;">

        <h2>Order Checkout & Payment</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error">
                <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <div class="payment-container" style="background: #fff; border: 1px solid #e0e0e0; border-radius: 6px; padding: 25px; margin-top: 15px;">

            <div class="customer-summary" style="background: #f8f9fa; border: 1px solid #e9ecef; border-radius: 4px; padding: 15px; margin-bottom: 25px;">
                <h3 style="margin-top: 0; font-size: 1.1rem; color: #333;">Customer Information</h3>
                <p style="margin: 4px 0;"><strong>Name:</strong> <?= htmlspecialchars($customerName, ENT_QUOTES, 'UTF-8') ?></p>
                <p style="margin: 4px 0;"><strong>Email:</strong> <?= htmlspecialchars($customerEmail, ENT_QUOTES, 'UTF-8') ?></p>
                <p style="margin: 4px 0;"><strong>Currency:</strong> GHS (Ghana Cedis)</p>
            </div>

            <h3 style="font-size: 1.1rem; margin-bottom: 12px; color: #333;">Order Items</h3>

            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-center" style="width: 100px;">Quantity</th>
                            <th class="text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                            <?php
                            $pPrice = (float) $item['product_price'];
                            $pQty = (int) $item['qty'];
                            $pSubtotal = $pPrice * $pQty;
                            $imgUrl = get_product_image_url($item['product_image'] ?? '');
                            ?>
                            <tr>
                                <td>
                                    <div class="cart-product-cell">
                                        <?php if ($imgUrl): ?>
                                            <img
                                                src="<?= $imgUrl ?>"
                                                alt="<?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                                class="cart-product-thumb"
                                                onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';"
                                            >
                                            <div class="cart-product-thumb cart-product-thumb--placeholder" style="display: none;">No image</div>
                                        <?php else: ?>
                                            <div class="cart-product-thumb cart-product-thumb--placeholder">No image</div>
                                        <?php endif; ?>

                                        <div class="cart-product-info">
                                            <span class="cart-product-title">
                                                <?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    GHS <?= number_format($pPrice, 2) ?>
                                </td>
                                <td class="text-center">
                                    <?= $pQty ?>
                                </td>
                                <td class="text-right cart-line-total">
                                    GHS <?= number_format($pSubtotal, 2) ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                        <tr class="cart-subtotal-row">
                            <td colspan="3" class="text-right">
                                <strong>Order Total:</strong>
                            </td>
                            <td class="text-right cart-total-amount">
                                <strong>GHS <?= number_format($cartTotal, 2) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <div class="payment-actions" style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <a href="<?= BASE_URL ?>/views/cart.php" class="btn-cart-continue">
                    &larr; Back to Cart
                </a>

                <button
                    type="button"
                    id="btn-paystack"
                    <?= $paystackReady ? '' : 'disabled title="Configure your Paystack test public key on the server"' ?>
                    onclick="payWithPaystack()"
                    style="padding: 14px 28px; background: #09a5db; color: white; border: none; border-radius: 4px; font-size: 1.05rem; font-weight: bold; cursor: pointer; display: flex; align-items: center; gap: 10px;"
                >
                    <span>Pay with Paystack &mdash; GHS <?= number_format($cartTotal, 2) ?></span>
                </button>
            </div>

            <?php if (!$paystackReady): ?>
                <p class="error">Paystack Test Mode is not configured. Ask the site administrator to set the test API keys before making payments.</p>
            <?php endif; ?>
            <div class="paystack-notice" style="margin-top: 25px; padding: 12px; background: #e7f5ff; border: 1px solid #bce2f5; border-radius: 4px; font-size: 0.88rem; color: #0c5460;">
                <p style="margin: 0;"><strong>Test Payment Mode:</strong> You can test payment using Paystack's test card number: <code>4084 0840 8408 4081</code> (any future expiry, any 3-digit CVV).</p>
            </div>

        </div>

    </section>

</div>

<!-- Paystack Inline JS -->
<script src="https://js.paystack.co/v1/inline.js"></script>

<script>
function payWithPaystack() {
    var payBtn = document.getElementById('btn-paystack');
    payBtn.disabled = true;
    payBtn.innerText = 'Opening Paystack...';

    try {
        var handler = PaystackPop.setup({
            key: '<?= htmlspecialchars(PAYSTACK_PUBLIC_KEY, ENT_QUOTES, 'UTF-8') ?>',
            email: '<?= htmlspecialchars($customerEmail, ENT_QUOTES, 'UTF-8') ?>',
            amount: <?= $amountInPesewas ?>,
            currency: 'GHS',
            callback: function(response) {
                // Redirect to server-side verification action with received reference
                window.location.href = '<?= BASE_URL ?>/actions/process_payment_action.php?reference=' + encodeURIComponent(response.reference);
            },
            onClose: function() {
                payBtn.disabled = false;
                payBtn.innerText = 'Pay with Paystack — GHS <?= number_format($cartTotal, 2) ?>';
                alert('Payment was cancelled. You may try again when ready.');
            }
        });

        handler.openIframe();
    } catch (err) {
        payBtn.disabled = false;
        payBtn.innerText = 'Pay with Paystack — GHS <?= number_format($cartTotal, 2) ?>';
        alert('Could not initialize Paystack popup: ' + err.message);
    }
}
</script>

<?php include __DIR__ . '/layout/footer.php'; ?>
