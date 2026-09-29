<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/e-commerce-labs/shoppn/views/login.php');
}


$email = trim(strip_tags($_POST['email'] ?? ''));
$pass = $_POST['password'] ?? '';


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

    $_SESSION['error'] = 'Please enter a valid email.';

    redirect('/e-commerce-labs/shoppn/views/login.php');
}


if (strlen($email) > 50) {

    $_SESSION['error'] = 'Email must not be longer than 50 characters.';

    redirect('/e-commerce-labs/shoppn/views/login.php');
}


if ($pass === '') {

    $_SESSION['error'] = 'Please enter your password.';

    redirect('/e-commerce-labs/shoppn/views/login.php');
}


$controller = new CustomerController();

$result = $controller->login(
    $email,
    $pass
);


if (
    !is_array($result) ||
    (isset($result['success']) && $result['success'] === false) ||
    !isset($result['customer_id'])
) {

    $_SESSION['error'] = (is_array($result) && !empty($result['error'])) 
        ? $result['error'] 
        : 'Invalid email or password.';

    redirect('/e-commerce-labs/shoppn/views/login.php');
}


/*
 * Store customer information in the session.
 */
$_SESSION['customer_id'] = $result['customer_id'];

$_SESSION['customer_name'] = $result['customer_name'];

$_SESSION['customer_email'] = $result['customer_email'];

$_SESSION['user_role'] = (int) $result['user_role'];


redirect('/e-commerce-labs/shoppn/index.php');

?>