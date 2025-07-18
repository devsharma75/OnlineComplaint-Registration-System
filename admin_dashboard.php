<?php
session_start();
include 'db.php';

// Admin Auth Check
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: admin_login.php");
    exit();
}

// Handle Status Message
$statusMessage = '';
if (isset($_GET['status']) && $_GET['status'] === 'updated') {
    $statusMessage = 'Complaint status updated successfully!';
}

// Fetch Complaints
$sql = "SELECT c.id, c.subject, c.description, c.status, c.created_at, u.name AS user_name,
        (SELECT COUNT(*) FROM complaint_feedback f WHERE f.complaint_id = c.id) AS has_feedback
        FROM complaints c
        JOIN users u ON c.user_id = u.id
        ORDER BY c.created_at DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Inter', sans-serif;
        }

        .header-gradient {
            background: linear-gradient(90deg, #0d6efd, #6610f2);
            color: #fff;
            padding: 20px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 24px rgba(0,0,0,0.08);
        }

        .header-gradient h3 {
            margin: 0;
            font-weight: 700;
        }

        .dashboard-card {
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            margin-top: 30px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.06);
        }

        .table th {
            background-color: #f3f6fa;
            color: #333;
            font-weight: 600;
        }

        .badge-status {
            font-size: 13px;
            padding: 6px 12px;
            border-radius: 20px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .status-pending { background-color: #ffc1071a; color: #856404; }
        .status-progress { background-color: #0dcaf01a; color: #055160; }
        .status-resolved { background-color: #1987541a; color: #0f5132; }

        .form-select, .btn {
            border-radius: 12px;
        }

        .search-bar {
            max-width: 300px;
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1055;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f5ff;
            transition: 0.3s;
        }
    </style>
</head>
<body>
<div class="container mt-5">

    <!-- Header -->
    <div class="header-gradient d-flex justify-content-between align-items-center">
        <h3>Admin Dashboard</h3>
        <div>
            <a href="view_feedbacks.php" class="btn btn-light btn-sm me-2">
                <i class="bi bi-chat-left-dots-fill"></i> Feedbacks
            </a>
            <a href="admin_logout.php" class="btn btn-outline-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>

    <!-- Toast Success -->
    <?php if ($statusMessage): ?>
        <div class="toast-container">
            <div class="toast show align-items-center text-bg-success border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body"><?= $statusMessage; ?></div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Search -->
    <div class="mt-4 mb-3 d-flex justify-content-between">
        <h5 class="fw-semibold">All Complaints</h5>
        <input type="text" id="searchInput" class="form-control search-bar" placeholder="Search by Subject...">
    </div>

    <!-- Complaints Table -->
    <div class="dashboard-card">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center" id="complaintsTable">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Subject</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>User</th>
                        <th>Submitted At</th>
                        <th>Update</th>
                        <th>Feedback</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <?php
                            $statusClass = match($row['status']) {
                                'pending' => 'status-pending',
                                'in_progress' => 'status-progress',
                                'resolved' => 'status-resolved',
                                default => 'bg-secondary'
                            };
                            $icon = match($row['status']) {
                                'pending' => 'bi-clock',
                                'in_progress' => 'bi-arrow-repeat',
                                'resolved' => 'bi-check2-circle',
                                default => 'bi-question-circle'
                            };
                        ?>
                        <tr>
                            <td><?= $row['id']; ?></td>
                            <td><?= htmlspecialchars($row['subject']); ?></td>
                            <td><?= htmlspecialchars($row['description']); ?></td>
                            <td>
                                <span class="badge-status <?= $statusClass ?>">
                                    <i class="bi <?= $icon ?>"></i> <?= ucfirst(str_replace('_', ' ', $row['status'])); ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($row['user_name']); ?></td>
                            <td><?= $row['created_at']; ?></td>
                            <td>
                                <form method="POST" action="update_complaint_status.php" class="d-flex flex-column align-items-center">
                                    <input type="hidden" name="complaint_id" value="<?= $row['id']; ?>">
                                    <select name="status" class="form-select mb-2" required>
                                        <option value="pending" <?= $row['status'] == 'pending' ? 'selected' : ''; ?>>Pending</option>
                                        <option value="in_progress" <?= $row['status'] == 'in_progress' ? 'selected' : ''; ?>>In Progress</option>
                                        <option value="resolved" <?= $row['status'] == 'resolved' ? 'selected' : ''; ?>>Resolved</option>
                                    </select>
                                    <button type="submit" class="btn btn-primary btn-sm w-100">Update</button>
                                </form>
                            </td>
                            <td>
                                <?php if ($row['has_feedback'] > 0): ?>
                                    <a href="view_feedbacks.php?complaint_id=<?= $row['id']; ?>#feedback-<?= $row['id']; ?>" class="btn btn-outline-info btn-sm">
                                        <i class="bi bi-eye"></i> View
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">No Feedback</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="8">No complaints found.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Search Filter
    document.getElementById('searchInput').addEventListener('keyup', function () {
        const value = this.value.toLowerCase();
        const rows = document.querySelectorAll("#complaintsTable tbody tr");
        rows.forEach(row => {
            const subject = row.children[1].textContent.toLowerCase();
            row.style.display = subject.includes(value) ? "" : "
