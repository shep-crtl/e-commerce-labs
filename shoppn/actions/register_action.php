<?php

require_once __DIR__ . '/../core/core.php';
require_once __DIR__ . '/../controllers/CustomerController.php';


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


$name = trim(strip_tags($_POST['name'] ?? ''));
$email = trim(strip_tags($_POST['email'] ?? ''));
$pass = $_POST['password'] ?? '';
$country = trim(strip_tags($_POST['country'] ?? ''));
$city = trim(strip_tags($_POST['city'] ?? ''));
$contact = trim(strip_tags($_POST['contact'] ?? ''));


if ($name === '' || strlen($name) < 2) {
    $_SESSION['error'] = 'Please enter a valid name.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($name) > 100) {
    $_SESSION['error'] = 'Name is too long.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION['error'] = 'Please enter a valid email.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($email) > 50) {
    $_SESSION['error'] = 'Email must not be longer than 50 characters.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($pass) < 8) {
    $_SESSION['error'] = 'Password must be at least 8 characters.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if ($country === '') {
    $_SESSION['error'] = 'Please select a country.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($country) > 30) {
    $_SESSION['error'] = 'Country name is too long.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if ($city === '' || strlen($city) < 2) {
    $_SESSION['error'] = 'Please enter a valid city.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($city) > 30) {
    $_SESSION['error'] = 'City name is too long.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if ($contact === '') {
    $_SESSION['error'] = 'Please enter your contact number.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


if (strlen($contact) > 15) {
    $_SESSION['error'] = 'Contact number is too long.';
    redirect('/e-commerce-labs/shoppn/views/register.php');
}


$controller = new CustomerController();


$result = $controller->register([
    'name' => $name,
    'email' => $email,
    'pass' => $pass,
    'country' => $country,
    'city' => $city,
    'contact' => $contact
]);


if ($result['success']) {

    $_SESSION['customer_id'] = $result['customer_id'];

    /*
     * New registrations are regular customers.
     * The SQL database defaults user_role to 2.
     */
    $_SESSION['user_role'] = 2;

    /*
     * We also store the basic customer information
     * so the header can use it.
     */
    $_SESSION['customer_name'] = $name;
    $_SESSION['customer_email'] = $email;

    redirect('/e-commerce-labs/shoppn/views/account/my_account.php');

}


$_SESSION['error'] = $result['error'];

redirect('/e-commerce-labs/shoppn/views/register.php');

?>