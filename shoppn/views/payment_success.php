<?php

/*
 * views/payment_success.php (Task 13)
 *
 * Displays printable customer invoice after successful payment.
 *
 * Security & Access Control:
 * - Requires active customer authentication.
 * - Customers can only view invoices belonging to their own account (unless admin).
 */

require_once __DIR__ . '/../core/core.php';
header('Cache-Control: private, no-store, max-age=0');
require_once __DIR__ . '/../controllers/CartController.php';

// Authentication required
require_login();

$orderId = filter_var($_GET['order_id'] ?? 0, FILTER_VALIDATE_INT);

if (!$orderId || $orderId <= 0) {
    $_SESSION['error'] = 'Invalid order ID specified.';
    redirect('/index.php');
}

$cartController = new CartController();
$order = $cartController->getOrderById($orderId);

if (!$order) {
    $_SESSION['error'] = 'Order not found.';
    redirect('/index.php');
}

// Access Control: Customer can only view their own invoice (admin can view any)
$currentUserId = (int) $_SESSION['customer_id'];
if ((int) $order['customer_id'] !== $currentUserId && !is_admin()) {
    $_SESSION['error'] = 'You do not have authorization to view this invoice.';
    redirect('/index.php');
}

include __DIR__ . '/layout/header.php';

?>

<style>
@media print {
    header, footer, .sidebar, .invoice-actions, .no-print {
        display: none !important;
    }
    .container, .page-layout, .invoice-container {
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        border: none !important;
        box-shadow: none !important;
    }
    body {
        background: #fff !important;
        color: #000 !important;
    }
}
</style>

<div class="page-layout">

    <div class="no-print">
        <?php include __DIR__ . '/layout/sidebar.php'; ?>
    </div>

    <section class="invoice-section" style="flex: 1; padding: 24px 28px; min-width: 0;">

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success no-print">
                <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <div class="invoice-container" style="background: #fff; border: 1px solid #ddd; border-radius: 6px; padding: 30px; max-width: 850px; margin: 0 auto; box-shadow: 0 2px 8px rgba(0,0,0,0.06);">

            <!-- Invoice Header -->
            <div style="display: flex; justify-content: space-between; align-items: flex-start; border-bottom: 2px solid #222; padding-bottom: 15px; margin-bottom: 20px; flex-wrap: wrap; gap: 15px;">
                <div>
                    <h1 style="margin: 0; font-size: 1.8rem; color: #222; letter-spacing: -0.5px;">ShopPN</h1>
                    <p style="margin: 4px 0 0; color: #666; font-size: 0.9rem;">Official Sales Receipt & Invoice</p>
                </div>
                <div style="text-align: right;">
                    <span style="display: inline-block; padding: 4px 12px; background: #d1e7dd; color: #0f5132; font-weight: bold; border-radius: 4px; font-size: 0.95rem; text-transform: uppercase;">
                        Status: <?= htmlspecialchars($order['order_status'], ENT_QUOTES, 'UTF-8') ?>
                    </span>
                    <p style="margin: 8px 0 0; font-size: 0.9rem; color: #555;">
                        <strong>Date:</strong> <?= htmlspecialchars($order['order_date'], ENT_QUOTES, 'UTF-8') ?>
                    </p>
                </div>
            </div>

            <!-- Invoice Meta Information -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; margin-bottom: 25px; font-size: 0.92rem; background: #fafafa; padding: 15px; border-radius: 4px;">
                <div>
                    <h4 style="margin: 0 0 8px; color: #555; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Order Details</h4>
                    <p style="margin: 3px 0;"><strong>Invoice No:</strong> #INV-<?= htmlspecialchars((string)$order['invoice_no'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p style="margin: 3px 0;"><strong>Order ID:</strong> #ORD-<?= (int) $order['order_id'] ?></p>
                    <p style="margin: 3px 0;"><strong>Payment Ref:</strong> <code><?= htmlspecialchars($order['payment_ref'] ?? 'N/A', ENT_QUOTES, 'UTF-8') ?></code></p>
                </div>

                <div>
                    <h4 style="margin: 0 0 8px; color: #555; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.05em;">Billed To</h4>
                    <p style="margin: 3px 0;"><strong>Name:</strong> <?= htmlspecialchars($order['customer_name'], ENT_QUOTES, 'UTF-8') ?></p>
                    <p style="margin: 3px 0;"><strong>Email:</strong> <?= htmlspecialchars($order['customer_email'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php if (!empty($order['customer_contact'])): ?>
                        <p style="margin: 3px 0;"><strong>Contact:</strong> <?= htmlspecialchars($order['customer_contact'], ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                    <?php if (!empty($order['customer_city']) || !empty($order['customer_country'])): ?>
                        <p style="margin: 3px 0;"><strong>Location:</strong> <?= htmlspecialchars(trim($order['customer_city'] . ', ' . $order['customer_country']), ENT_QUOTES, 'UTF-8') ?></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Line Items Table -->
            <h3 style="font-size: 1.1rem; margin-bottom: 12px; color: #333;">Purchased Items</h3>

            <div class="table-responsive">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Item Description</th>
                            <th class="text-right" style="width: 120px;">Unit Price</th>
                            <th class="text-center" style="width: 80px;">Qty</th>
                            <th class="text-right" style="width: 130px;">Line Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $calculatedTotal = 0.0;
                        if (!empty($order['items']) && is_array($order['items'])):
                            foreach ($order['items'] as $item):
                                $itemPrice = (float) $item['product_price'];
                                $itemQty = (int) $item['qty'];
                                $lineTotal = $itemPrice * $itemQty;
                                $calculatedTotal += $lineTotal;
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
                                            <strong><?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-right">
                                    GHS <?= number_format($itemPrice, 2) ?>
                                </td>
                                <td class="text-center">
                                    <?= $itemQty ?>
                                </td>
                                <td class="text-right cart-line-total">
                                    GHS <?= number_format($lineTotal, 2) ?>
                                </td>
                            </tr>
                        <?php
                            endforeach;
                        else:
                        ?>
                            <tr>
                                <td colspan="4" style="text-align: center; padding: 15px; color: #777;">No item breakdown available.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot>
                        <tr class="cart-subtotal-row">
                            <td colspan="3" class="text-right">
                                <strong>Total Paid (GHS):</strong>
                            </td>
                            <td class="text-right cart-total-amount">
                                <strong>GHS <?= number_format((float) ($order['payment_amt'] ?: $calculatedTotal), 2) ?></strong>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>

            <!-- Footer & Actions -->
            <div class="invoice-actions" style="margin-top: 30px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
                <a href="<?= BASE_URL ?>/index.php" class="btn-cart-continue">
                    &larr; Continue Shopping
                </a>

                <button
                    type="button"
                    onclick="window.print()"
                    style="padding: 10px 20px; background: #222; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;"
                >
                    &#128438; Print Invoice
                </button>
            </div>

        </div>

    </section>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
