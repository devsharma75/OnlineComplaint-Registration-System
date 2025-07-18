<?php include 'db.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User Signup</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f0f2f5;
        }
        .signup-box {
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        .login-link {
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="signup-box">
        <h3 class="text-center mb-4">Create Your Account</h3>
        <form method="POST">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" name="name" pattern="[A-Za-z\s]+" title="Only alphabets and spaces allowed" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Age</label>
                <input type="number" name="age" class="form-control" required min="18" max="100">
            </div>

            <div class="mb-3">
                <label class="form-label">Gender</label>
                <select name="gender" class="form-select" required>
                    <option value="">--Select--</option>
                    <option>Male</option>
                    <option>Female</option>
                    <option>Other</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Mobile Number</label>
                <input type="text" name="mobile" pattern="^[6-9]\d{9}$" class="form-control" required title="Mobile must be 10 digits and start with 6-9">
            </div>

            <div class="mb-3">
                <label class="form-label">Email address</label>
                <input type="email" name="email" class="form-control" required>
            </div>

            <!-- New Identity Type Field -->
            <div class="mb-3">
                <label class="form-label">Identity Type</label>
                <select name="id_type" class="form-select" required>
                    <option value="">--Select ID Type--</option>
                    <option>Aadhar</option>
                    <option>PAN</option>
                    <option>Voter ID</option>
                </select>
            </div>

            <!-- New Identity Number Field -->
            <div class="mb-3">
                <label class="form-label">Identity Number</label>
                <input type="text" name="govt_id" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Address</label>
                <textarea name="address" rows="3" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>

            <button type="submit" name="signup" class="btn btn-primary w-100">Sign Up</button>
        </form>

        <div class="login-link">
            <p>Already have an account?</p>
            <a href="login.php" class="btn btn-outline-secondary">Go to Login</a>
        </div>

        <div class="mt-3">
            <?php
            if (isset($_POST['signup'])) {
                $name = $_POST['name'];
                $age = $_POST['age'];
                $gender = $_POST['gender'];
                $mobile = $_POST['mobile'];
                $email = $_POST['email'];
                $id_type = $_POST['id_type'];
                $govt_id = $_POST['govt_id'];
                $address = $_POST['address'];
                $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

                // Name validation (only letters and spaces)
                if (!preg_match('/^[A-Za-z\s]+$/', $name)) {
                    echo "<div class='alert alert-danger'>Name must contain only letters and spaces.</div>";
                }
                // Mobile number validation
                elseif (!preg_match('/^[6-9]\d{9}$/', $mobile)) {
                    echo "<div class='alert alert-danger'>Mobile number must be valid and start with 6-9.</div>";
                } else {
                    $sql = "INSERT INTO users (name, age, gender, mobile, email, id_type, govt_id, address, password, role) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'user')";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("sisssssss", $name, $age, $gender, $mobile, $email, $id_type, $govt_id, $address, $password);
                    if ($stmt->execute()) {
                        echo "<div class='alert alert-success'>Signup successful! <a href='login.php'>Login here</a></div>";
                    } else {
                        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
                    }
                }
            }
            ?>
        </div>
    </div>
</div>

</body>
</html>
