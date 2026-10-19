<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

/*
 * actions/remove_from_cart_action.php (Task 12)
 *
 * POST only.
 * Removes one or more items from the cart for the current user's IP.
 * Supports both checkboxes named remove[] and a direct product_id field.
 * Redirects back to views/cart.php with a session message.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/cart.php');
}

$ip = get_ip();
$cartController = new CartController();
$removedCount = 0;

// Handle remove[] checkbox array
if (isset($_POST['remove']) && is_array($_POST['remove'])) {
    foreach ($_POST['remove'] as $rawId) {
        $productId = filter_var($rawId, FILTER_VALIDATE_INT);
        if ($productId && $productId > 0) {
            if ($cartController->removeFromCart($productId, $ip)) {
                $removedCount++;
            }
        }
    }
}

// Handle single product_id parameter (e.g., individual item remove button)
if (isset($_POST['product_id'])) {
    $productId = filter_var($_POST['product_id'], FILTER_VALIDATE_INT);
    if ($productId && $productId > 0) {
        if ($cartController->removeFromCart($productId, $ip)) {
            $removedCount++;
        }
    }
}

if ($removedCount > 0) {
    $_SESSION['success'] = ($removedCount === 1)
        ? 'Item removed from cart successfully.'
        : $removedCount . ' items removed from cart successfully.';
} else {
    $_SESSION['error'] = 'Please select at least one item to remove.';
}

redirect('/views/cart.php');
