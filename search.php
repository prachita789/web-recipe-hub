<?php
session_start();
$search = isset($_GET['q']) ? trim($_GET['q']) : '';
$recipes = [];
$apiKey = '9eee4210ca8c40f9b907ce51424a3851'; 

if (!empty($search)) {
    $api_url = "https://api.spoonacular.com/recipes/complexSearch?query=" . urlencode($search) . "&number=10&apiKey=" . $apiKey;
    
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    $recipes = $data['results'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Search Results</title>
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
</head>
<body>
  <header class="text-center p-4 bg-light">
    <h2>🔍 Search Results for: <em><?= htmlspecialchars($search) ?></em></h2>
    <a href="index.php" class="btn btn-sm btn-outline-secondary mt-2">← Back to Home</a>
  </header>

  <main class="container mt-4 mb-5">
    <div class="row">
      <?php if (!empty($recipes)): ?>
        <?php foreach ($recipes as $recipe): ?>
          <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm">
              <img src="<?= htmlspecialchars($recipe['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
              <div class="card-body">
                <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
                <a href="recipe-details.php?id=<?= urlencode($recipe['id']) ?>" class="btn btn-primary">View Recipe</a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-12 text-center">
          <p class="text-muted">No recipes found for "<strong><?= htmlspecialchars($search) ?></strong>".</p>
        </div>
      <?php endif; ?>
    </div>
  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
