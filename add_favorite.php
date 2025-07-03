<?php 
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $recipe_id = $_POST['recipe_id'];
    $recipe_name = $_POST['recipe_name'];
    $image_url = $_POST['image_url']; // Get image URL

    // Check if already added
    $stmt = $pdo->prepare("SELECT * FROM favorites WHERE user_id = ? AND recipe_id = ?");
    $stmt->execute([$user_id, $recipe_id]);

    if ($stmt->rowCount() == 0) {
        $stmt = $pdo->prepare("INSERT INTO favorites (user_id, recipe_id, recipe_name, image_url) VALUES (?, ?, ?, ?)");
        $stmt->execute([$user_id, $recipe_id, $recipe_name, $image_url]);
    }

    // Redirect back
    header("Location: recipe-details.php?id=" . urlencode($recipe_id));
    exit;
}
?>
