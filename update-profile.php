<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');

    if (empty($name) || empty($email)) {
        $message = '<div class="alert alert-danger">All fields are required.</div>';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = '<div class="alert alert-warning">Enter a valid email address.</div>';
    } else {
        $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ? WHERE id = ?");
        $stmt->execute([$name, $email, $user_id]);

        $_SESSION['update_success'] = true;
        header('Location: profile.php');
        exit;
    }
}

// Fetch current user info
$stmt = $pdo->prepare("SELECT name, email FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Update Profile</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
  <div class="row justify-content-center">
    <div class="col-md-6">
      <h3 class="mb-4 text-center">✏️ Update Profile</h3>
      <?= $message ?>
      <form method="POST">
        <div class="mb-3">
          <label for="name" class="form-label">Full Name</label>
          <input type="text" name="name" id="name" class="form-control" value="<?= htmlspecialchars($user['name']) ?>" required>
        </div>
        <div class="mb-3">
          <label for="email" class="form-label">Email Address</label>
          <input type="email" name="email" id="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <button type="submit" class="btn btn-success w-100">Update Profile</button>
        <div class="text-center mt-3">
          <a href="profile.php" class="btn btn-link">← Back to Profile</a>
        </div>
      </form>
    </div>
  </div>
</div>

</body>
</html>
