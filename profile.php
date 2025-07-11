<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch user info
$stmtUser = $pdo->prepare("SELECT name, email FROM users WHERE id = :id");
$stmtUser->execute(['id' => $user_id]);
$user = $stmtUser->fetch(PDO::FETCH_ASSOC);

$user_name = $user['name'] ?? 'User';
$user_email = $user['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Your Profile</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="assets/css/style.css" />

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>
  <style>
    body.dark-mode {
      background-color: #121212;
      color: #f1f1f1;
    }
    body.dark-mode .card,
    body.dark-mode .form-control {
      background-color: #1e1e1e;
      color: #ddd;
      border-color: #444;
    }
    .toggle-switch {
      position: absolute;
      top: 1rem;
      right: 1rem;
    }
  </style>
</head>
<body>

<?php include 'includes/navbar.php'; ?>

<div class="container mt-4 position-relative">
  <!-- 🌙 Dark Mode Toggle -->
  <div class="form-check form-switch toggle-switch">
    <input class="form-check-input" type="checkbox" id="darkModeSwitch">
    <label class="form-check-label" for="darkModeSwitch">Dark Mode</label>
  </div>

  <div class="card shadow-sm mb-4">
    <div class="card-body">
      <h3 class="card-title mb-3"><i class="fas fa-user-circle"></i> Welcome, <?= htmlspecialchars($user_name) ?>!</h3>

      <!-- Update Profile Form -->
      <form action="update-profile.php" method="post" class="row g-3">
        <div class="col-md-6">
          <label for="name" class="form-label">Name</label>
          <input type="text" name="name" id="name" value="<?= htmlspecialchars($user_name) ?>" class="form-control" required>
        </div>
        <div class="col-md-6">
          <label for="email" class="form-label">Email</label>
          <input type="email" name="email" id="email" value="<?= htmlspecialchars($user_email) ?>" class="form-control" required>
        </div>
        <a href="update-profile.php" class="btn btn-outline-info mb-2">
  <i class="fas fa-user-edit"></i> Edit Profile
</a>

        <div class="col-12 text-end">
          <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Save Changes</button>
        </div>
      </form>

      <div class="mt-4 d-flex justify-content-between">
        <a href="change_password.php" class="btn btn-warning">
          <i class="fas fa-key"></i> Change Password
        </a>
        <form action="delete-account.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account?');">
          <button type="submit" class="btn btn-outline-danger">
            <i class="fas fa-trash"></i> Delete Account
          </button>
        </form>
      </div>
    </div>
  </div>

  <a href="index.php" class="btn btn-outline-primary">
    <i class="fas fa-home"></i> Back to Home
  </a>
</div>

<script>
  const toggle = document.getElementById('darkModeSwitch');
  toggle.addEventListener('change', () => {
    document.body.classList.toggle('dark-mode');
    localStorage.setItem('darkMode', toggle.checked);
  });

  // Restore theme preference
  window.addEventListener('DOMContentLoaded', () => {
    if (localStorage.getItem('darkMode') === 'true') {
      document.body.classList.add('dark-mode');
      toggle.checked = true;
    }
  });
</script>

</body>
</html>
