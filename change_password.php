<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle password update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    // Fetch current hashed password
    $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($currentPassword, $user['password'])) {
        $message = '<div class="alert alert-danger">Current password is incorrect.</div>';
    } elseif ($newPassword !== $confirmPassword) {
        $message = '<div class="alert alert-warning">New passwords do not match.</div>';
    } elseif (strlen($newPassword) < 6) {
        $message = '<div class="alert alert-warning">New password must be at least 6 characters.</div>';
    } else {
        // Update password
        $hashed = password_hash($newPassword, PASSWORD_DEFAULT);
        $update = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $update->execute([$hashed, $user_id]);

        $message = '<div class="alert alert-success">Password updated successfully!</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change Password</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css" />
  <script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="mb-4 text-center"><i class="fas fa-lock"></i> Change Password</h3>
      <?= $message ?>
      <form method="POST" novalidate>
        <div class="mb-3">
          <label for="current_password" class="form-label">Current Password</label>
          <input type="password" name="current_password" id="current_password" class="form-control" required>
        </div>

        <div class="mb-3">
          <label for="new_password" class="form-label">New Password</label>
          <input type="password" name="new_password" id="new_password" class="form-control" required minlength="6">
        </div>

        <div class="mb-3">
          <label for="confirm_password" class="form-label">Confirm New Password</label>
          <input type="password" name="confirm_password" id="confirm_password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100"><i class="fas fa-save"></i> Update Password</button>
        <div class="text-center mt-3">
          <a href="profile.php" class="btn btn-link">← Back to Profile</a>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
