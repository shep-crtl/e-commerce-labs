<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

/*
 * actions/add_to_cart_action.php (Task 11)
 *
 * Receives ?add_cart=N from GET (link trigger).
 * Checks if the item is already in the cart for the user's IP.
 * - If already in cart: redirects with 'This item is already in your cart. Go to cart to update quantity.'
 * - Otherwise: adds the item to the cart with quantity 1 and redirects back to the referring page.
 */

// Safe referer determination
$referer = $_SERVER['HTTP_REFERER'] ?? '';
$redirectUrl = BASE_URL . '/index.php';

if (!empty($referer)) {
    // Only use referer if it matches current host
    $host = $_SERVER['HTTP_HOST'] ?? '';
    if (empty($host) || strpos($referer, $host) !== false) {
        $redirectUrl = $referer;
    }
}

// Read and validate ?add_cart=N from GET
$productId = filter_var($_GET['add_cart'] ?? 0, FILTER_VALIDATE_INT);

if (!$productId || $productId <= 0) {
    $_SESSION['error'] = 'Invalid product selected.';
    header('Location: ' . $redirectUrl);
    exit();
}

$ip = get_ip();
$customerId = $_SESSION['customer_id'] ?? null;

$cartController = new CartController();

// Check if item is already in cart
if ($cartController->isInCart($productId, $ip)) {
    $_SESSION['error'] = 'This item is already in your cart. Go to cart to update quantity.';
    header('Location: ' . $redirectUrl);
    exit();
}

// Add product to cart (default initial quantity: 1)
$success = $cartController->addToCart($productId, 1, $ip, $customerId);

if ($success) {
    $_SESSION['success'] = 'Product added to cart successfully.';
} else {
    $_SESSION['error'] = 'Failed to add product to cart. Please try again.';
}

header('Location: ' . $redirectUrl);
exit();
