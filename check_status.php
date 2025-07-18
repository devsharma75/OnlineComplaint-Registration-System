<?php
include 'auth.php';
include 'db.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT * FROM complaints WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Check Complaint Status</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .status-container {
            max-width: 900px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
        }
        .table th {
            background-color: #343a40;
            color: #fff;
        }
        .table td, .table th {
            vertical-align: middle;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="status-container">
        <h2 class="text-center text-primary mb-4">Complaint Status</h2>
        <div class="back-link">
            <a href="user_dashboard.php" class="btn btn-outline-secondary">Back to Dashboard</a>
        </div>

        <div class="table-responsive mt-4">
            <table class="table table-bordered table-hover text-center align-middle">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Status</th>
                        <th>Submitted At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= $row['id']; ?></td>
                                <td><?= htmlspecialchars($row['subject']); ?></td>
                                <td>
                                    <?php
                                    // Assign colors to statuses
                                    $status = $row['status'];
                                    $badge = "secondary";
                                    if ($status === "pending") $badge = "warning";
                                    elseif ($status === "in_progress") $badge = "info";
                                    elseif ($status === "resolved") $badge = "success";
                                    ?>
                                    <span class="badge bg-<?= $badge ?>"><?= ucfirst($status); ?></span>
                                </td>
                                <td><?= $row['created_at']; ?></td>
                            </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">No complaints found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

</body>
</html>
