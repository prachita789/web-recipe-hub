<?php
require_once 'includes/config.php';

header('Content-Type: application/json');

$apiKey = SPOONACULAR_API_KEY;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$number = 6; // recipes per batch

$url = "https://api.spoonacular.com/recipes/random?number={$number}&apiKey={$apiKey}";
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
$response = curl_exec($ch);
curl_close($ch);
$data = json_decode($response, true);

$recipes = $data['recipes'] ?? [];

echo json_encode(['recipes' => $recipes]);
