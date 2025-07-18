<?php
include 'db.php';

$name = "Admin";
$email = "admin@gmail.com";
$password = "admin123"; // Simple password for test

$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$role = "admin";

$sql = "INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $email, $hashed_password, $role);

if ($stmt->execute()) {
    echo "Admin user created successfully!";
} else {
    echo "Error: " . $stmt->error;
}
?>
