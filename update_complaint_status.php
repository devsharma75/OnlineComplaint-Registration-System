<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['complaint_id']) && isset($_POST['status'])) {
    $complaint_id = $_POST['complaint_id'];
    $status = $_POST['status'];

    // Prepare the SQL query to update the complaint status
    $sql = "UPDATE complaints SET status = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    
    // Bind the parameters to the query
    $stmt->bind_param("si", $status, $complaint_id);
    
    // Execute the query
    if ($stmt->execute()) {
        // Redirect back to the dashboard with a success message
        header("Location: admin_dashboard.php?status=updated");
    } else {
        // Error handling
        echo "Error: " . $stmt->error;
    }
} else {
    // If the request method is not POST or the necessary data is not set
    header("Location: admin_dashboard.php");
}
?>
