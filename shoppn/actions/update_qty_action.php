<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CartController.php';

/*
 * actions/update_qty_action.php (Task 12)
 *
 * POST only.
 * Loops through submitted qty[] and update[] arrays.
 * Validates that each quantity is a positive integer (>= 1) before updating.
 * Redirects back to views/cart.php with a session message.
 */

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/cart.php');
}

$ip = get_ip();
$cartController = new CartController();

$qtyList = $_POST['qty'] ?? [];
$updateList = $_POST['update'] ?? [];

$updatedCount = 0;
$invalidQtyFound = false;

if (is_array($updateList) && is_array($qtyList)) {
    $count = count($updateList);
    for ($i = 0; $i < $count; $i++) {
        $productId = filter_var($updateList[$i] ?? 0, FILTER_VALIDATE_INT);
        $newQty = filter_var($qtyList[$i] ?? 0, FILTER_VALIDATE_INT);

        if (!$productId || $productId <= 0) {
            continue;
        }

        // Validate that new_qty is a positive integer (>= 1)
        if ($newQty === false || $newQty < 1) {
            $invalidQtyFound = true;
            continue;
        }

        if ($cartController->updateQty($productId, $newQty, $ip)) {
            $updatedCount++;
        }
    }
}

if ($invalidQtyFound) {
    $_SESSION['error'] = 'Quantities must be positive numbers of at least 1.';
} elseif ($updatedCount > 0) {
    $_SESSION['success'] = 'Cart quantities updated successfully.';
} else {
    $_SESSION['error'] = 'No quantity updates were made.';
}

redirect('/views/cart.php');
