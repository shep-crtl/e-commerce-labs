<?php

/*
 * views/cart.php — Customer-facing shopping cart view (Tasks 11 & 12).
 *
 * Calls CartController to retrieve cart items and total.
 * Displays cart items in a table with:
 *   - Remove checkbox (name="remove[]")
 *   - Product image and title
 *   - Unit price
 *   - Quantity input (name="qty[]" and hidden name="update[]")
 *   - Line subtotal
 *
 * Provides action buttons:
 *   - Remove from Cart (POST to actions/remove_from_cart_action.php)
 *   - Update Quantity (POST to actions/update_qty_action.php)
 *   - Continue Shopping (links to index.php)
 *   - Checkout (links to views/checkout.php)
 *
 * No SQL lives in this view.
 */

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

$cartController = new CartController();
$ip = get_ip();

$cartItems = $cartController->getCartItems($ip);
$cartTotal = $cartController->getCartTotal($ip);

include __DIR__ . '/layout/header.php';

?>

<div class="page-layout">

    <?php include __DIR__ . '/layout/sidebar.php'; ?>

    <section class="cart-section">

        <h2>Your Shopping Cart</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <p class="success">
                <?= htmlspecialchars($_SESSION['success'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['success']); ?>
            </p>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <p class="error">
                <?= htmlspecialchars($_SESSION['error'], ENT_QUOTES, 'UTF-8') ?>
                <?php unset($_SESSION['error']); ?>
            </p>
        <?php endif; ?>

        <?php if (empty($cartItems)): ?>

            <div class="empty-state">
                <p>Your shopping cart is empty.</p>
                <a href="<?= BASE_URL ?>/index.php" class="btn-cart-continue">
                    &larr; Start Shopping
                </a>
            </div>

        <?php else: ?>

            <form method="POST" action="<?= BASE_URL ?>/actions/update_qty_action.php" id="cart-form">

                <div class="table-responsive">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th class="text-center" style="width: 80px;">
                                    <label for="select-all-checkbox" style="cursor: pointer; font-size: 0.85rem;">Remove</label>
                                </th>
                                <th>Product</th>
                                <th class="text-right">Unit Price</th>
                                <th class="text-center" style="width: 120px;">Quantity</th>
                                <th class="text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <?php
                                $itemPrice = (float) $item['product_price'];
                                $itemQty = (int) $item['qty'];
                                $lineTotal = $itemPrice * $itemQty;
                                ?>
                                <tr>
                                    <td class="text-center">
                                        <input
                                            type="checkbox"
                                            name="remove[]"
                                            value="<?= (int) $item['p_id'] ?>"
                                            aria-label="Remove <?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                            class="cart-checkbox"
                                        >
                                    </td>
                                    <td>
                                        <div class="cart-product-cell">
                                            <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $item['p_id'] ?>">
                                                <?php if (!empty($item['product_image'])): ?>
                                                    <img
                                                        src="<?= BASE_URL ?>/images/products/<?= htmlspecialchars($item['product_image'], ENT_QUOTES, 'UTF-8') ?>"
                                                        alt="<?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                                        class="cart-product-thumb"
                                                    >
                                                <?php else: ?>
                                                    <div class="cart-product-thumb cart-product-thumb--placeholder">No image</div>
                                                <?php endif; ?>
                                            </a>
                                            <div class="cart-product-info">
                                                <a href="<?= BASE_URL ?>/views/single_product.php?pro_id=<?= (int) $item['p_id'] ?>" class="cart-product-title">
                                                    <?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-right">
                                        $<?= number_format($itemPrice, 2) ?>
                                    </td>
                                    <td class="text-center">
                                        <input
                                            type="hidden"
                                            name="update[]"
                                            value="<?= (int) $item['p_id'] ?>"
                                        >
                                        <input
                                            type="number"
                                            name="qty[]"
                                            value="<?= $itemQty ?>"
                                            min="1"
                                            max="99"
                                            class="cart-qty-input"
                                            aria-label="Quantity for <?= htmlspecialchars($item['product_title'], ENT_QUOTES, 'UTF-8') ?>"
                                        >
                                    </td>
                                    <td class="text-right cart-line-total">
                                        $<?= number_format($lineTotal, 2) ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr class="cart-subtotal-row">
                                <td colspan="4" class="text-right">
                                    <strong>Cart Total / Subtotal:</strong>
                                </td>
                                <td class="text-right cart-total-amount">
                                    <strong>$<?= number_format((float) $cartTotal, 2) ?></strong>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <div class="cart-actions-toolbar">
                    <div class="cart-actions-left">
                        <button
                            type="submit"
                            formaction="<?= BASE_URL ?>/actions/remove_from_cart_action.php"
                            class="btn-cart-remove"
                        >
                            Remove Selected
                        </button>

                        <button
                            type="submit"
                            formaction="<?= BASE_URL ?>/actions/update_qty_action.php"
                            class="btn-cart-update"
                        >
                            Update Quantity
                        </button>
                    </div>

                    <div class="cart-actions-right">
                        <a href="<?= BASE_URL ?>/index.php" class="btn-cart-continue">
                            Continue Shopping
                        </a>

                        <a href="<?= BASE_URL ?>/views/checkout.php" class="btn-cart-checkout">
                            Proceed to Checkout &rarr;
                        </a>
                    </div>
                </div>

            </form>

        <?php endif; ?>

    </section>

</div>

<?php include __DIR__ . '/layout/footer.php'; ?>
