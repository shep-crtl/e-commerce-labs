<?php

$host = "localhost";
$db_user = "YOUR_USERNAME";
$db_pass = "YOUR_PASSWORD";
$db_name = "YOUR_DATABASE_NAME";

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
