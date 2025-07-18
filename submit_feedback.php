<?php
session_start();
include 'db.php';

$user_id = $_SESSION['user_id'];
$complaint_id = intval($_POST['complaint_id']);
$feedback_text = trim($_POST['feedback']);

if (!empty($feedback_text) && $complaint_id > 0) {
    $stmt = $conn->prepare("INSERT INTO complaint_feedback (complaint_id, feedback_text) VALUES (?, ?)");
    $stmt->bind_param("is", $complaint_id, $feedback_text);
    if ($stmt->execute()) {
        header("Location: feedback.php?success=1");
        exit();
    } else {
        header("Location: feedback.php?error=1");
        exit();
    }
} else {
    header("Location: feedback.php?error=1");
    exit();
}
