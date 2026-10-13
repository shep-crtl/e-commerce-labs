<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/admin/brand.php');
}

$brandName = trim(strip_tags($_POST['brand_name'] ?? ''));

if ($brandName === '') {
    $_SESSION['error'] = 'Brand name cannot be empty.';
    redirect('/views/admin/brand.php');
}

if (strlen($brandName) > 100) {
    $_SESSION['error'] = 'Brand name cannot be longer than 100 characters.';
    redirect('/views/admin/brand.php');
}

$controller = new ProductController();
$result = $controller->addBrand($brandName);

if ($result) {
    $_SESSION['success'] = 'Brand added.';
} else {
    $_SESSION['error'] = 'Failed to add brand. Please try again.';
}

redirect('/views/admin/brand.php');
