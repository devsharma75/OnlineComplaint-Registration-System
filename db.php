<?php
$host = 'localhost';
$db   = 'complaint_system';
$user = 'root';
$pass = ''; // XAMPP/MAMP users ke liye mostly blank hota hai

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
