<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm = $_POST['confirm'];

    // Server-side validation
    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/', $password)) {
        $error = "Password must be at least 8 characters, include 1 uppercase letter, 1 number, and 1 special character.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        // Check for existing email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        if ($stmt->rowCount() > 0) {
            $error = "Email already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $insert = $pdo->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
            $insert->execute([$name, $email, $hashed]);
            $success = "Registration successful. You can now <a href='login.php'>login</a>.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register | Recipe Hub</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>
  <style>
    body {
        background: #f0f2f5;
    }
    .register-box {
        max-width: 420px;
        margin: 80px auto;
        background: #fff;
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 0 25px rgba(0,0,0,0.1);
    }
    .form-control {
        padding-left: 38px;
    }
    .form-group {
        position: relative;
        margin-bottom: 20px;
    }
    .form-group i {
        position: absolute;
        top: 12px;
        left: 12px;
        color: #999;
    }
    .btn-primary {
        width: 100%;
    }
    .msg {
        margin-bottom: 15px;
    }
    .password-hint {
        font-size: 0.85em;
        color: #888;
    }
  </style>
</head>
<body>

<div class="register-box">
    <h3 class="text-center mb-4">Create Account</h3>

    <?php if ($error): ?>
        <div class="alert alert-danger msg"><?= $error ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success msg"><?= $success ?></div>
    <?php endif; ?>

    <form method="POST" onsubmit="return validateForm();">
        <div class="form-group">
            <i class="fas fa-user"></i>
            <input type="text" class="form-control" name="name" placeholder="Full Name" required />
        </div>

        <div class="form-group">
            <i class="fas fa-envelope"></i>
            <input type="email" class="form-control" name="email" placeholder="Email" required />
        </div>

        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required />
        </div>

        <div class="form-group">
            <i class="fas fa-lock"></i>
            <input type="password" class="form-control" id="confirm" name="confirm" placeholder="Confirm Password" required />
        </div>

        <div class="password-hint">
            Password must be at least 8 characters, include 1 uppercase letter, 1 number, and 1 special character.
        </div>

        <button type="submit" class="btn btn-primary mt-3">Register</button>

        <p class="mt-3 text-center">
            Already have an account? <a href="login.php">Login here</a>
        </p>
    </form>
</div>

<script>
function validateForm() {
    const password = document.getElementById('password').value;
    const confirm = document.getElementById('confirm').value;

    const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*]).{8,}$/;

    if (!regex.test(password)) {
        alert("Password must be at least 8 characters and include 1 uppercase letter, 1 number, and 1 special character.");
        return false;
    }

    if (password !== confirm) {
        alert("Passwords do not match.");
        return false;
    }

    return true;
}
</script>

</body>
</html>
