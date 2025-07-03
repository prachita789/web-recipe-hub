<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Redirect to login if not logged in
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

// Fetch user's favorite recipes
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = :user_id ORDER BY added_on DESC");
$stmt->execute(['user_id' => $user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Your Profile</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>
</head>
<body>
  
  <header class="text-center p-4 bg-light">
    <h2>👤 Welcome, <?= htmlspecialchars($user_name) ?>!</h2>
    <p>Email: <strong><?= htmlspecialchars($user_email) ?></strong></p>
    <p><a href="logout.php" class="btn btn-sm btn-danger">Logout</a></p>
  </header>

  <main class="container mt-4">
    <a href="index.php" class="btn btn-outline-primary mb-3">
      <i class="fas fa-home"></i> Home
    </a>
    <h3>Your Favorite Recipes</h3>
    <?php if (count($favorites) > 0): ?>
      <div class="row">
        <?php foreach ($favorites as $fav): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="<?= htmlspecialchars($fav['image_url']) ?>" class="card-img-top" alt="<?= htmlspecialchars($fav['recipe_name']) ?>" />
              <div class="card-body d-flex flex-column">
                <h5 class="card-title"><?= htmlspecialchars($fav['recipe_name']) ?></h5>
                <a href="recipe-details.php?id=<?= htmlspecialchars($fav['recipe_id']) ?>&source=profile" class="btn btn-outline-success btn-sm">
  <i class="fas fa-eye"></i> View Recipe
</a>



              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <p>You have not added any favorites yet.</p>
    <?php endif; ?>

    <div class="text-center mt-5">
      <form action="delete-account.php" method="post" onsubmit="return confirm('Are you sure you want to delete your account? This cannot be undone.');">
        <button type="submit" class="btn btn-outline-danger">🗑️ Delete My Account</button>
      </form>
    </div>
  </main>
</body>
</html>
