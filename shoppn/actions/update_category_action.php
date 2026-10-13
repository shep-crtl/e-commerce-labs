<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/admin/category.php');
}

$catId = filter_var($_POST['cat_id'] ?? 0, FILTER_VALIDATE_INT);
$catName = trim(strip_tags($_POST['cat_name'] ?? ''));

if (!$catId || $catId <= 0) {
    $_SESSION['error'] = 'Invalid category ID.';
    redirect('/views/admin/category.php');
}

if ($catName === '') {
    $_SESSION['error'] = 'Category name cannot be empty.';
    redirect('/views/admin/category.php?edit_id=' . $catId);
}

if (strlen($catName) > 100) {
    $_SESSION['error'] = 'Category name cannot be longer than 100 characters.';
    redirect('/views/admin/category.php?edit_id=' . $catId);
}

$controller = new ProductController();
$result = $controller->updateCategory($catId, $catName);

if ($result) {
    $_SESSION['success'] = 'Category updated successfully.';
} else {
    $_SESSION['error'] = 'Failed to update category. Please try again.';
}

redirect('/views/admin/category.php');
