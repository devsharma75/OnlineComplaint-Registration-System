<?php
include 'auth.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

// Handle profile image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_image'])) {
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file = $_FILES['profile_image'];
    $file_name = basename($file['name']);
    $target_path = $upload_dir . time() . '_' . $file_name;

    $file_type = strtolower(pathinfo($target_path, PATHINFO_EXTENSION));
    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($file['tmp_name'], $target_path)) {
            $stmt_update = $conn->prepare("UPDATE users SET profile_image = ? WHERE id = ?");
            if ($stmt_update) {
                $stmt_update->bind_param("si", $target_path, $user_id);
                $stmt_update->execute();
            }
        }
    }
}

// Fetch user info
$sql_user = "SELECT name, profile_image FROM users WHERE id = ?";
$stmt_user = $conn->prepare($sql_user);
if (!$stmt_user) {
    die("Error preparing user query: " . $conn->error);
}
$stmt_user->bind_param("i", $user_id);
$stmt_user->execute();
$user_result = $stmt_user->get_result();
$user = $user_result->fetch_assoc();

$user_name = $user['name'] ?? 'User';
$profile_image = !empty($user['profile_image']) ? $user['profile_image'] : 'https://i.pravatar.cc/60?u=default';

// Fetch complaints
$sql = "SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing complaints query: " . $conn->error);
}
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$no_complaints = ($result->num_rows === 0);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #dbeafe, #f0f4ff);
            font-family: 'Segoe UI', sans-serif;
        }

        .dashboard-container {
            max-width: 1100px;
            margin: 60px auto;
            padding: 40px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 25px 40px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .btn-custom {
            margin: 10px;
            border-radius: 12px;
            font-weight: 600;
            padding: 10px 20px;
            transition: all 0.3s ease;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }

        h2 {
            text-align: center;
            font-weight: 700;
            color: #1d4ed8;
            margin-bottom: 40px;
        }

        table th {
            background-color: #1d4ed8;
            color: #fff;
            font-size: 15px;
            text-transform: uppercase;
        }

        table td {
            vertical-align: middle;
            font-size: 15px;
        }

        .badge {
            font-size: 13px;
            padding: 6px 14px;
            border-radius: 50px;
        }

        .no-complaints {
            text-align: center;
            font-style: italic;
            color: #6c757d;
            padding: 20px;
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .profile-header img {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            border: 2px solid #1d4ed8;
            object-fit: cover;
        }

        .profile-header .info {
            margin-left: 15px;
        }

        .profile-upload {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-container {
            border-radius: 12px;
            overflow: hidden;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="dashboard-container">

        <!-- Profile Section -->
        <div class="profile-header">
            <div class="d-flex align-items-center">
                <img src="<?= htmlspecialchars($profile_image); ?>" alt="Profile">
                <div class="info ms-3">
                    <h5>Welcome, <strong><?= htmlspecialchars($user_name); ?></strong></h5>
                    <small>Glad to see you back!</small>
                </div>
            </div>

            <!-- Upload Form -->
            <form action="" method="POST" enctype="multipart/form-data" class="profile-upload">
                <input type="file" name="profile_image" accept="image/*" class="form-control form-control-sm" required>
                <button type="submit" class="btn btn-sm btn-outline-primary">Upload</button>
            </form>
        </div>

        <!-- Header -->
        <h2><i class="bi bi-speedometer2"></i> User Dashboard</h2>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap justify-content-center mb-4">
            <a href="register_complaint.php" class="btn btn-success btn-custom">
                <i class="bi bi-plus-circle"></i> Register Complaint
            </a>
            <a href="check_status.php" class="btn btn-primary btn-custom">
                <i class="bi bi-card-checklist"></i> Check Status
            </a>
            <a href="feedback.php" class="btn btn-warning btn-custom text-white">
                <i class="bi bi-chat-dots"></i> Feedback
            </a>
            <a href="logout.php" class="btn btn-danger btn-custom">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>

        <!-- Complaints Table -->
        <h4 class="mb-3 text-dark fw-semibold">Your Complaints</h4>
        <div class="table-responsive table-container">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($no_complaints): ?>
                    <tr>
                        <td colspan="4" class="no-complaints">No complaints found.</td>
                    </tr>
                <?php else: ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['subject']); ?></td>
                            <td>
                                <?php
                                    $status = $row['status'];
                                    $badge = match($status) {
                                        'pending' => 'warning',
                                        'in_progress' => 'info',
                                        'resolved' => 'success',
                                        default => 'secondary'
                                    };
                                ?>
                                <span class="badge bg-<?= $badge ?>"><?= ucfirst(str_replace('_', ' ', $status)); ?></span>
                            </td>
                            <td><?= date("d M Y", strtotime($row['created_at'])); ?></td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
