<?php
include 'auth.php';
include 'db.php';

// Fetch user's complaints
$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT id, subject FROM complaints WHERE user_id = $user_id");

// Check if success or error message should be shown
$success = isset($_GET['success']) && $_GET['success'] == 1;
$error = isset($_GET['error']) && $_GET['error'] == 1;
?>

<!DOCTYPE html>
<html>
<head>
    <title>Give Feedback</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #e0f7fa, #fff);
        }
        .card {
            max-width: 700px;
            margin: 50px auto;
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }
        .form-control, .form-select {
            border-radius: 10px;
        }
        .btn-primary {
            border-radius: 8px;
            padding: 10px 20px;
        }
        .back-btn {
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="card p-5">
        <h3 class="mb-4 text-primary text-center">💬 Submit Feedback</h3>

        <!-- Show Success or Error Message -->
        <?php if ($success): ?>
            <div class="alert alert-success text-center">
                ✅ Feedback submitted successfully!
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger text-center">
                ❌ Please fill in all fields properly.
            </div>
        <?php endif; ?>

        <form action="submit_feedback.php" method="POST">
            <div class="mb-3">
                <label for="complaint_id" class="form-label">Select Complaint</label>
                <select name="complaint_id" class="form-select" required>
                    <option value="">-- Choose Complaint --</option>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['subject']) ?></option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="feedback" class="form-label">Your Feedback</label>
                <textarea name="feedback" class="form-control" rows="4" placeholder="Write your feedback here..." required></textarea>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary">🚀 Submit Feedback</button>
            </div>
        </form>

        <div class="text-center back-btn">
            <a href="user_dashboard.php" class="btn btn-outline-secondary">⬅️ Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
