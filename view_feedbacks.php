<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

$filterComplaintId = isset($_GET['complaint_id']) ? intval($_GET['complaint_id']) : null;

$sql = "SELECT f.id, f.feedback_text, f.created_at, 
               c.id AS complaint_id, c.subject, u.name AS user_name
        FROM complaint_feedback f
        JOIN complaints c ON f.complaint_id = c.id
        JOIN users u ON c.user_id = u.id";

if ($filterComplaintId) {
    $sql .= " WHERE f.complaint_id = $filterComplaintId";
}

$sql .= " ORDER BY f.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Feedbacks</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .feedback-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.08);
        }
        .feedback-box {
            padding: 15px;
            margin-bottom: 20px;
            background: #eef7ff;
            border-left: 5px solid #0d6efd;
            border-radius: 8px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="feedback-container">
        <a href="admin_dashboard.php" class="btn btn-secondary mb-3">⬅ Back to Dashboard</a>
        <h3 class="mb-4">📝 User Feedbacks</h3>

        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="feedback-box" id="feedback-<?= $row['complaint_id']; ?>">
                    <strong>Complaint #<?= $row['complaint_id']; ?>:</strong> <?= htmlspecialchars($row['subject']); ?><br>
                    <strong>User:</strong> <?= htmlspecialchars($row['user_name']); ?><br>
                    <strong>Feedback:</strong><br>
                    <p><?= nl2br(htmlspecialchars($row['feedback_text'])); ?></p>
                    <small class="text-muted">Submitted on: <?= $row['created_at']; ?></small>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-warning">No feedback found.</div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
