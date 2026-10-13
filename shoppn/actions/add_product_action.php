<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/ProductController.php';

require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/views/admin/product.php');
}

$cat = filter_var($_POST['product_cat'] ?? 0, FILTER_VALIDATE_INT);
$brand = filter_var($_POST['product_brand'] ?? 0, FILTER_VALIDATE_INT);
$title = trim(strip_tags($_POST['product_title'] ?? ''));
$priceInput = $_POST['product_price'] ?? '';
$price = filter_var($priceInput, FILTER_VALIDATE_FLOAT);
$desc = trim(strip_tags($_POST['product_desc'] ?? ''));
$keywords = trim(strip_tags($_POST['product_keywords'] ?? ''));

if ($title === '') {
    $_SESSION['error'] = 'Product title is required.';
    redirect('/views/admin/product.php');
}

if ($price === false || $price < 0) {
    $_SESSION['error'] = 'Please enter a valid positive product price.';
    redirect('/views/admin/product.php');
}

if (!$cat || $cat <= 0) {
    $_SESSION['error'] = 'Please select a valid category.';
    redirect('/views/admin/product.php');
}

if (!$brand || $brand <= 0) {
    $_SESSION['error'] = 'Please select a valid brand.';
    redirect('/views/admin/product.php');
}

// Helper function to safely process file uploads across server environments
function processProductImageUpload($file, $targetDirectory, &$errorMsg)
{
    if (!isset($file) || !is_array($file)) {
        $errorMsg = 'No image file was submitted.';
        return false;
    }

    if ($file['error'] !== UPLOAD_ERR_OK) {
        switch ($file['error']) {
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errorMsg = 'Uploaded file exceeds the maximum upload size allowed by the server php.ini.';
                break;
            case UPLOAD_ERR_PARTIAL:
                $errorMsg = 'The file was only partially uploaded. Please try again.';
                break;
            case UPLOAD_ERR_NO_FILE:
                $errorMsg = 'Please select an image file to upload.';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $errorMsg = 'Server error: Temporary upload directory is missing on the web server.';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $errorMsg = 'Server error: Failed to write file to temp disk. Check server disk permissions.';
                break;
            default:
                $errorMsg = 'File upload failed with error code: ' . $file['error'];
                break;
        }
        return false;
    }

    // Size limit check (2MB)
    $maxSize = 2 * 1024 * 1024;
    if ($file['size'] > $maxSize) {
        $errorMsg = 'Uploaded image exceeds the 2MB size limit.';
        return false;
    }

    $tmpName = $file['tmp_name'];

    // Multi-fallback MIME type detection
    $mimeType = false;
    if (function_exists('getimagesize')) {
        $imageInfo = @getimagesize($tmpName);
        if ($imageInfo && !empty($imageInfo['mime'])) {
            $mimeType = $imageInfo['mime'];
        }
    }

    if (!$mimeType && function_exists('finfo_open')) {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        if ($finfo) {
            $mimeType = finfo_file($finfo, $tmpName);
            finfo_close($finfo);
        }
    }

    if (!$mimeType && function_exists('mime_content_type')) {
        $mimeType = @mime_content_type($tmpName);
    }

    if (!$mimeType) {
        $mimeType = $file['type'] ?? '';
    }

    $allowedTypes = [
        'image/jpeg' => 'jpg',
        'image/jpg'  => 'jpg',
        'image/pjpeg'=> 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists(strtolower((string)$mimeType), $allowedTypes)) {
        $errorMsg = 'Invalid image format (' . htmlspecialchars((string)$mimeType) . '). Allowed formats are JPEG, PNG, GIF, and WebP.';
        return false;
    }

    $ext = $allowedTypes[strtolower((string)$mimeType)];
    $imageFilename = uniqid('prod_', true) . '.' . $ext;

    // Ensure target directory exists and permissions are writable
    if (!is_dir($targetDirectory)) {
        @mkdir($targetDirectory, 0777, true);
    }

    if (is_dir($targetDirectory) && !is_writable($targetDirectory)) {
        @chmod($targetDirectory, 0777);
    }

    if (!is_dir($targetDirectory) || !is_writable($targetDirectory)) {
        $errorMsg = 'Server permission error: The folder (images/products/) is not writable by PHP. Please set permissions on images/products/ to 777 or 775.';
        return false;
    }

    $targetPath = rtrim($targetDirectory, '/\\') . '/' . $imageFilename;

    if (!move_uploaded_file($tmpName, $targetPath)) {
        $errorMsg = 'Failed to move uploaded file. Check server write permissions on images/products/.';
        return false;
    }

    return $imageFilename;
}

$targetDirectory = __DIR__ . '/../images/products/';
$uploadError = '';
$image_filename = processProductImageUpload($_FILES['product_image'] ?? null, $targetDirectory, $uploadError);

if (!$image_filename) {
    $_SESSION['error'] = $uploadError;
    redirect('/views/admin/product.php');
}

$controller = new ProductController();
$result = $controller->addProduct(
    $cat,
    $brand,
    $title,
    $price,
    $desc,
    $image_filename,
    $keywords
);

if ($result) {
    $_SESSION['success'] = 'Product added successfully.';
} else {
    // If database insert fails, remove uploaded image
    $targetPath = $targetDirectory . $image_filename;
    if (file_exists($targetPath)) {
        unlink($targetPath);
    }
    $_SESSION['error'] = 'Failed to add product to database.';
}

redirect('/views/admin/product.php');
