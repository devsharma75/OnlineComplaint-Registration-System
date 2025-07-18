<?php
include 'auth.php';
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $subject = $_POST['subject'];
    $description = $_POST['description'];
    $user_id = $_SESSION['user_id'];

    $sql = "INSERT INTO complaints (user_id, subject, description) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iss", $user_id, $subject, $description);
    
    if ($stmt->execute()) {
        echo "<div class='alert alert-success'>Complaint submitted successfully! <a href='user_dashboard.php'>Go to Dashboard</a></div>";
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register Complaint</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .complaint-form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .form-title {
            text-align: center;
            color: #007bff;
            margin-bottom: 20px;
        }
        .btn-custom {
            margin-top: 15px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="complaint-form-container">
        <h2 class="form-title">Register Complaint</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="subject" class="form-label">Subject</label>
                <input type="text" name="subject" class="form-control" required>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="5" required></textarea>
            </div>
            
            <button type="submit" class="btn btn-primary btn-custom w-100">Submit Complaint</button>
        </form>

        <div class="mt-3 text-center">
            <a href="user_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>
    </div>
</div>

</body>
</html>
