<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/admin/category.php');
}

$catName = trim(strip_tags($_POST['cat_name'] ?? ''));

if ($catName === '') {
    $_SESSION['error'] = 'Category name cannot be empty.';
    redirect('/views/admin/category.php');
}

if (strlen($catName) > 100) {
    $_SESSION['error'] = 'Category name cannot be longer than 100 characters.';
    redirect('/views/admin/category.php');
}

$controller = new ProductController();
$result = $controller->addCategory($catName);

if ($result) {
    $_SESSION['success'] = 'Category added successfully.';
} else {
    $_SESSION['error'] = 'Failed to add category. Please try again.';
}

redirect('/views/admin/category.php');
