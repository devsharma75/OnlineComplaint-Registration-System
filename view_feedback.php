<?php
session_start();
include 'db.php';

// Check if the user is an admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Get the complaint ID from the URL
if (isset($_GET['complaint_id'])) {
    $complaint_id = $_GET['complaint_id'];

    // Fetch feedback for the complaint
    $sql = "SELECT feedback FROM complaint_feedback WHERE complaint_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $complaint_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $feedback = $result->fetch_assoc()['feedback'];
    } else {
        $feedback = "No feedback available for this complaint.";
    }
} else {
    die("Complaint ID not provided.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card">
        <div class="card-header">
            <h3>Feedback for Complaint ID: <?= htmlspecialchars($complaint_id); ?></h3>
        </div>
        <div class="card-body">
            <h5>Feedback:</h5>
            <p><?= htmlspecialchars($feedback); ?></p>
            <a href="admin_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
    </div>
</div>
</body>
</html>
