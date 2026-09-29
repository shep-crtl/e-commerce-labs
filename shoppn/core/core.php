<?php

session_start();

date_default_timezone_set('Africa/Accra');

require_once __DIR__ . '/db_class.php';


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

?>