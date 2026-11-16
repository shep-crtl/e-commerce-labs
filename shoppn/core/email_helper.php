<?php

/*
 * core/email_helper.php
 *
 * Sends order confirmation emails to customers upon successful payment .
 *
 * If PHP mail() is not configured on the local development machine (common with default XAMPP),
 * it logs the email to error.log or a dedicated mail log so the order succeeds gracefully.
 */

/**
 * Send order confirmation email to the customer.
 *
 * @param  array $order Full order structure from CartController::getOrderById()
 * @return bool
 */
function send_order_confirmation_email($order)
{
    if (empty($order) || empty($order['customer_email'])) {
        error_log('send_order_confirmation_email: Missing order or customer email.');
        return false;
    }

    $to = $order['customer_email'];
    $customerName = htmlspecialchars($order['customer_name'] ?? 'Valued Customer', ENT_QUOTES, 'UTF-8');
    $invoiceNo = htmlspecialchars((string) ($order['invoice_no'] ?? ''), ENT_QUOTES, 'UTF-8');
    $orderId = (int) ($order['order_id'] ?? 0);
    $orderDate = htmlspecialchars((string) ($order['order_date'] ?? date('Y-m-d')), ENT_QUOTES, 'UTF-8');
    $totalAmt = number_format((float) ($order['payment_amt'] ?? 0.0), 2);
    $currency = htmlspecialchars((string) ($order['payment_currency'] ?? 'GHS'), ENT_QUOTES, 'UTF-8');
    $reference = htmlspecialchars((string) ($order['payment_ref'] ?? 'N/A'), ENT_QUOTES, 'UTF-8');

    $subject = "Order Confirmation - ShopPN Invoice #" . $invoiceNo;

    // Build items rows
    $itemsText = "";
    $itemsHtml = "";
    if (!empty($order['items']) && is_array($order['items'])) {
        foreach ($order['items'] as $item) {
            $title = $item['product_title'] ?? 'Product';
            $qty = (int) ($item['qty'] ?? 1);
            $price = number_format((float) ($item['product_price'] ?? 0), 2);
            $subtotal = number_format($qty * (float) ($item['product_price'] ?? 0), 2);

            $itemsText .= sprintf("- %s | Qty: %d | Price: %s %s | Subtotal: %s %s\n", $title, $qty, $currency, $price, $currency, $subtotal);

            $itemsHtml .= "<tr>" .
                "<td style='padding: 8px; border: 1px solid #ddd;'>" . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . "</td>" .
                "<td style='padding: 8px; border: 1px solid #ddd; text-align: center;'>" . $qty . "</td>" .
                "<td style='padding: 8px; border: 1px solid #ddd; text-align: right;'>" . $currency . " " . $price . "</td>" .
                "<td style='padding: 8px; border: 1px solid #ddd; text-align: right;'>" . $currency . " " . $subtotal . "</td>" .
                "</tr>";
        }
    }

    // Build professional HTML message
    $message = "
    <html>
    <head>
        <title>ShopPN Order Receipt</title>
    </head>
    <body style='font-family: Arial, sans-serif; line-height: 1.6; color: #333; background: #f8f9fa; padding: 20px;'>
        <div style='max-width: 600px; margin: auto; background: #fff; padding: 25px; border: 1px solid #e1e4e8; border-radius: 6px;'>
            <h2 style='color: #222; border-bottom: 2px solid #f0a500; padding-bottom: 10px; margin-top: 0;'>ShopPN Order Confirmation</h2>
            <p>Dear <strong>{$customerName}</strong>,</p>
            <p>Thank you for shopping with ShopPN! Your payment has been successfully processed.</p>
            
            <table style='width: 100%; margin-bottom: 20px; font-size: 0.95rem;'>
                <tr><td><strong>Invoice #:</strong></td><td>{$invoiceNo}</td></tr>
                <tr><td><strong>Order #:</strong></td><td>#ORD-{$orderId}</td></tr>
                <tr><td><strong>Date:</strong></td><td>{$orderDate}</td></tr>
                <tr><td><strong>Payment Reference:</strong></td><td>{$reference}</td></tr>
                <tr><td><strong>Payment Status:</strong></td><td><span style='color: #198754; font-weight: bold;'>PAID</span></td></tr>
            </table>

            <h3 style='border-bottom: 1px solid #eee; padding-bottom: 6px;'>Order Summary</h3>
            <table style='width: 100%; border-collapse: collapse; margin-bottom: 20px;'>
                <thead>
                    <tr style='background: #f1f3f5;'>
                        <th style='padding: 8px; border: 1px solid #ddd; text-align: left;'>Product</th>
                        <th style='padding: 8px; border: 1px solid #ddd; text-align: center;'>Qty</th>
                        <th style='padding: 8px; border: 1px solid #ddd; text-align: right;'>Price</th>
                        <th style='padding: 8px; border: 1px solid #ddd; text-align: right;'>Total</th>
                    </tr>
                </thead>
                <tbody>
                    {$itemsHtml}
                </tbody>
                <tfoot>
                    <tr style='background: #f8f9fa; font-weight: bold;'>
                        <td colspan='3' style='padding: 10px; border: 1px solid #ddd; text-align: right;'>Total Paid:</td>
                        <td style='padding: 10px; border: 1px solid #ddd; text-align: right; color: #c0392b;'>{$currency} {$totalAmt}</td>
                    </tr>
                </tfoot>
            </table>

            <p style='font-size: 0.9rem; color: #666;'>If you have any questions about your order, please contact our support team.</p>
            <p style='margin-top: 30px; font-size: 0.85rem; color: #999; text-align: center;'>&copy; " . date('Y') . " ShopPN E-Commerce. All rights reserved.</p>
        </div>
    </body>
    </html>";

    $headers = [];
    $headers[] = 'MIME-Version: 1.0';
    $headers[] = 'Content-type: text/html; charset=UTF-8';
    $headers[] = 'From: ShopPN Store <adikoshepherd@gmail.com>';
    $headers[] = 'Reply-To: adikoshepherd@gmail.com';
    $headers[] = 'X-Mailer: PHP/' . phpversion();

    $mailSuccess = false;
    try {
        // Native mail() requires the school server to authorize this From address.
        // Success here means accepted by the local mail transport, not delivered.
        $mailSuccess = @mail($to, $subject, $message, implode("\r\n", $headers));
    } catch (Throwable $t) {
        error_log('send_order_confirmation_email exception: ' . $t->getMessage());
        $mailSuccess = false;
    }

    if ($mailSuccess) {
        error_log("Order confirmation accepted by server mail transport for {$to} for Order #{$orderId}");
    } else {
        error_log("Notice: PHP mail() could not deliver to {$to} (local SMTP may not be configured). Order #{$orderId} remains intact.");
    }

    return $mailSuccess;
}
