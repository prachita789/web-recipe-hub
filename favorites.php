<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Handle Remove request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_favorite'])) {
    $recipe_id = $_POST['recipe_id'];
    $stmt = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);
    $message = "Recipe removed from favorites.";
}

// Fetch favorite recipes
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ?");
$stmt->execute([$user_id]);
$favorites = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Favorites - Recipe App</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
    <div class="container my-5">
        <h2>🍲 My Favorite Recipes</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
        <?php endif; ?>

        <?php if (count($favorites) === 0): ?>
            <p class="text-muted">You have not added any favorite recipes yet.</p>
        <?php else: ?>
            <div class="row">
                <?php foreach ($favorites as $fav): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card h-100">
                            <img src="<?= htmlspecialchars($fav['recipe_image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($fav['recipe_title']) ?>">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($fav['recipe_title']) ?></h5>
                                <a href="recipe-details.php?id=<?= $fav['recipe_id'] ?>" class="btn btn-primary mb-2">View Recipe</a>

                                <form method="post" onsubmit="return confirm('Remove from favorites?');">
                                    <input type="hidden" name="recipe_id" value="<?= $fav['recipe_id'] ?>">
                                    <button type="submit" name="remove_favorite" class="btn btn-danger w-100">Remove</button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <a href="index.php" class="btn btn-secondary mt-4">🔙 Back to Home</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
