<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$message = "";

// Get source page for back button
$source = isset($_GET['source']) && $_GET['source'] === 'profile' ? 'profile.php' : 'index.php';

// Fetch recipe ID
$recipeId = isset($_GET['id']) ? trim($_GET['id']) : '';
if (empty($recipeId)) {
    echo "<p>Recipe ID not provided.</p>";
    exit;
}

// Fetch recipe info from Spoonacular API
$api_url = "https://api.spoonacular.com/recipes/{$recipeId}/information?apiKey=" . SPOONACULAR_API_KEY;
$ch = curl_init($api_url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

if (!$data || isset($data['code'])) {
    echo "<p>Recipe not found or error fetching data.</p>";
    exit;
}

// Check if recipe is favorited
$stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
$stmt->execute([$user_id, $data['id']]);
$is_favorited = $stmt->rowCount() > 0;

// Handle form submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $recipe_id = $_POST['recipe_id'];
    $recipe_name = $_POST['recipe_name'];
    $image_url = $_POST['image_url'];

    if (isset($_POST['add_to_favorites'])) {
        $insert = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id, recipe_name, image_url) VALUES (?, ?, ?, ?)");
        $insert->execute([$user_id, $recipe_id, $recipe_name, $image_url]);
        $message = "Recipe added to favorites!";
        $is_favorited = true;
    } elseif (isset($_POST['remove_from_favorites'])) {
        $delete = $pdo->prepare("DELETE FROM favorites WHERE user_id = ? AND recipe_id = ?");
        $delete->execute([$user_id, $recipe_id]);
        $message = "Recipe removed from favorites.";
        $is_favorited = false;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['title']) ?> - Recipe Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>
    <style>
        body {
            background-color: #fafafa;
        }
        .recipe-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2rem;
            align-items: start;
        }
        .recipe-img {
            width: 100%;
            object-fit: cover;
            aspect-ratio: 1 / 1;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .favorite-btn {
            background: none;
            border: none;
            cursor: pointer;
        }
        .favorite-btn i {
            font-size: 1.8rem;
            color: #dc3545;
        }
        .favorite-btn i.far {
            color: #999;
        }
        .info-label i {
            color: #dc3545;
            margin-right: 6px;
        }
    </style>
</head>
<body>
<div class="container my-5">
    <h1><?= htmlspecialchars($data['title']) ?></h1>

    <div class="recipe-layout mt-4">
        <img src="<?= htmlspecialchars($data['image']) ?>" alt="<?= htmlspecialchars($data['title']) ?>" class="recipe-img">

        <div>
            <p class="info-label"><i class="fas fa-clock"></i> <strong>Ready in:</strong> <?= $data['readyInMinutes'] ?> minutes</p>
            <p class="info-label"><i class="fas fa-users"></i> <strong>Servings:</strong> <?= $data['servings'] ?></p>

            <?php if (!empty($data['summary'])): ?>
                <div class="mb-3"><?= $data['summary'] ?></div>
            <?php endif; ?>

            <?php if (!empty($data['extendedIngredients'])): ?>
                <h4 class="info-label"><i class="fas fa-carrot"></i> Ingredients:</h4>
                <ul>
                    <?php foreach ($data['extendedIngredients'] as $ingredient): ?>
                        <li><?= htmlspecialchars($ingredient['original']) ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if (!empty($data['instructions'])): ?>
                <h4 class="info-label"><i class="fas fa-list-ol"></i> Instructions:</h4>
                <p><?= nl2br($data['instructions']) ?></p>
            <?php endif; ?>

            <form method="post" class="mt-3 position-relative">
                <input type="hidden" name="recipe_id" value="<?= $data['id'] ?>">
                <input type="hidden" name="recipe_name" value="<?= htmlspecialchars($data['title']) ?>">
                <input type="hidden" name="image_url" value="<?= htmlspecialchars($data['image']) ?>">
                <input type="hidden" name="source" value="<?= $source ?>">

                <button type="submit" name="<?= $is_favorited ? 'remove_from_favorites' : 'add_to_favorites' ?>" class="favorite-btn" title="<?= $is_favorited ? 'Remove from Favorites' : 'Add to Favorites' ?>">
                    <i class="<?= $is_favorited ? 'fas' : 'far' ?> fa-heart"></i>
                </button>

                <?php if (!empty($message)): ?>
                    <div class="alert alert-info mt-2" id="fav-alert"><?= $message ?></div>
                <?php endif; ?>
            </form>

            <p class="mt-4">
                <a href="<?= $source ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </p>
        </div>
    </div>
</div>

<script>
    setTimeout(function () {
        const alertBox = document.getElementById('fav-alert');
        if (alertBox) {
            alertBox.style.transition = 'opacity 0.5s ease';
            alertBox.style.opacity = '0';
            setTimeout(() => alertBox.remove(), 500);
        }
    }, 3000);
</script>
</body>
</html>
