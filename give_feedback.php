<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$complaint_id = $_GET['id'];
$user_id = $_SESSION['user_id'];

// Check if complaint belongs to user and is resolved
$check = $conn->prepare("SELECT * FROM complaints WHERE id=? AND user_id=? AND status='resolved'");
$check->bind_param("ii", $complaint_id, $user_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    die("Invalid complaint or not resolved yet.");
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rating = $_POST['rating'];
    $feedback = $_POST['feedback'];

    $stmt = $conn->prepare("INSERT INTO complaint_feedback (complaint_id, user_id, rating, feedback) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $complaint_id, $user_id, $rating, $feedback);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully'); window.location.href='user_dashboard.php';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Give Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h3>Rate & Review Your Complaint</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="rating" class="form-label">Rating (1 to 5)</label>
            <select class="form-select" name="rating" required>
                <option value="">Choose</option>
                <?php for ($i = 1; $i <= 5; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="feedback" class="form-label">Feedback</label>
            <textarea name="feedback" class="form-control" rows="4" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Submit Feedback</button>
    </form>
</div>
</body>
</html>
