<?php
$host = 'localhost';
$user = 'root'; // Change if you have a database password
$pass = '';
$dbname = 'sales_system';

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>