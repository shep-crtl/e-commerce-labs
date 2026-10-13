<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/admin/brand.php');
}

$brandId = filter_var($_POST['brand_id'] ?? 0, FILTER_VALIDATE_INT);
$brandName = trim(strip_tags($_POST['brand_name'] ?? ''));

if (!$brandId || $brandId <= 0) {
    $_SESSION['error'] = 'Invalid brand ID.';
    redirect('/views/admin/brand.php');
}

if ($brandName === '') {
    $_SESSION['error'] = 'Brand name cannot be empty.';
    redirect('/views/admin/brand.php?edit_id=' . $brandId);
}

if (strlen($brandName) > 100) {
    $_SESSION['error'] = 'Brand name cannot be longer than 100 characters.';
    redirect('/views/admin/brand.php?edit_id=' . $brandId);
}

$controller = new ProductController();
$result = $controller->updateBrand($brandId, $brandName);

if ($result) {
    $_SESSION['success'] = 'Brand updated successfully.';
} else {
    $_SESSION['error'] = 'Failed to update brand. Please try again.';
}

redirect('/views/admin/brand.php');
