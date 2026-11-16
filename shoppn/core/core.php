<?php

session_start();

date_default_timezone_set('Africa/Accra');

require_once __DIR__ . '/db_class.php';
require_once __DIR__ . '/paystack_config.php';


if (!defined('BASE_URL')) {
    $scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
    $pos = strpos($scriptName, '/shoppn');
    if ($pos !== false) {
        $baseUrl = substr($scriptName, 0, $pos + 7);
    } else {
        $baseUrl = '/e-commerce-labs/shoppn';
    }
    define('BASE_URL', $baseUrl);
}


function get_ip()
{
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    }

    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    }

    return $_SERVER['REMOTE_ADDR'];
}


function redirect($path)
{
    if (strpos($path, 'http://') === 0 || strpos($path, 'https://') === 0) {
        header("Location: $path");
        exit();
    }

    $cleanPath = '/' . ltrim($path, '/');

    // If $cleanPath already starts with BASE_URL, use as-is
    if (strpos($cleanPath, BASE_URL) === 0) {
        header("Location: " . $cleanPath);
        exit();
    }

    // Strip legacy hardcoded prefix if present
    if (strpos($cleanPath, '/e-commerce-labs/shoppn') === 0) {
        $cleanPath = substr($cleanPath, strlen('/e-commerce-labs/shoppn'));
    }

    header("Location: " . BASE_URL . $cleanPath);
    exit();
}


function is_logged_in()
{
    return isset($_SESSION['customer_id']);
}


function is_admin()
{
    return isset($_SESSION['user_role']) &&
           (int) $_SESSION['user_role'] === 1;
}


function require_login()
{
    if (!is_logged_in()) {
        redirect('/views/login.php');
    }
}


function require_admin()
{
    if (!is_admin()) {
        $_SESSION['error'] = 'You do not have permission to access this page.';

        redirect('/index.php');
    }
}


/**
 * Helper to get a safe product image URL or null if file does not exist on disk.
 */
function get_product_image_url($filename)
{
    if (empty($filename)) {
        return null;
    }

    $clean = basename(trim((string) $filename));
    if ($clean === '') {
        return null;
    }

    $fullPath = __DIR__ . '/../images/products/' . $clean;
    if (file_exists($fullPath) && is_file($fullPath)) {
        return BASE_URL . '/images/products/' . rawurlencode($clean);
    }

    return null;
}


/**
 * Helper to get a safe customer profile image URL or default avatar.
 */
function get_customer_image_url($filename = null)
{
    if (!empty($filename)) {
        $clean = basename(trim((string) $filename));
        if ($clean !== '') {
            $fullPath = __DIR__ . '/../images/customers/' . $clean;
            if (file_exists($fullPath) && is_file($fullPath)) {
                return BASE_URL . '/images/customers/' . rawurlencode($clean);
            }
        }
    }

    $defaultAvatar = __DIR__ . '/../images/customers/default_avatar.png';
    if (file_exists($defaultAvatar)) {
        return BASE_URL . '/images/customers/default_avatar.png';
    }

    return null;
}

?>