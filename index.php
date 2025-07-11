<?php
session_start();
require_once 'includes/config.php';



$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$recipes = [];

$apiKey = SPOONACULAR_API_KEY;

if (!empty($search)) {
    // Search by name or ingredient
    $api_url = "https://api.spoonacular.com/recipes/complexSearch?query=" . urlencode($search) . "&number=12&addRecipeInformation=true&apiKey={$apiKey}";
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    $recipes = $data['results'] ?? [];
} else {
    // Show random recipes only once per session
    if (!isset($_SESSION['recipes'])) {
        $url = "https://api.spoonacular.com/recipes/random?number=9&apiKey={$apiKey}";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);
        $data = json_decode($response, true);
        $_SESSION['recipes'] = $data['recipes'] ?? [];
    }

    $recipes = $_SESSION['recipes'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Recipe Finder - Home</title>
  
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="assets/css/style.css" />
  
<script src="https://kit.fontawesome.com/f141e1b7ac.js" crossorigin="anonymous"></script>

</head>
<body>
  <header class="text-center p-4 bg-light text-dark" id="mainHeader">

    <h1>🍽️ Recipe Finder</h1>
    <p>Select ingredients or search recipes by name</p>
  </header>
   <?php include 'includes/navbar.php'; ?>
   <main class="container mb-5">
    <form method="GET" class="d-flex justify-content-center mb-4" role="search" aria-label="Recipe search form">
      <input 
        type="text" 
        name="search" 
        value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>" 
        placeholder="Search recipes by name or ingredient" 
        class="form-control w-50 me-2"
        required
        aria-required="true"
      />
      <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <div class="row" id="recipeContainer">
      <?php foreach ($recipes as $index => $recipe): ?>
        <div class="col-md-4 mb-4 recipe-card">

          <div class="card h-100 shadow-sm">
            <img src="<?= htmlspecialchars($recipe['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
            <div class="card-body d-flex flex-column">
              <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
              <p class="card-text"><strong>Ready in:</strong> <?= htmlspecialchars($recipe['readyInMinutes']) ?> min</p>
              <a href="recipe-details.php?id=<?= $recipe['id'] ?>&source=<?= $search ? 'search' : 'home' ?>" class="btn btn-sm btn-primary">
                <i class="fas fa-eye"></i> View Details
              </a>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-3">
  <button id="seeMoreBtn" class="btn btn-outline-primary">See More</button>
  <div id="loading" class="mt-2" style="display:none;">Loading...</div>
</div>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    
let page = 1;

document.getElementById('seeMoreBtn').addEventListener('click', function () {
  const btn = this;
  const loader = document.getElementById('loading');
  btn.disabled = true;
  loader.style.display = 'block';

  fetch(`load_more.php?page=${page + 1}`)
    .then(res => res.json())
    .then(data => {
      if (data.recipes && data.recipes.length > 0) {
        const container = document.getElementById('recipeContainer');
        data.recipes.forEach(recipe => {
          const col = document.createElement('div');
          col.className = 'col-md-4 mb-4';

          col.innerHTML = `
            <div class="card h-100 shadow-sm">
              <img src="${recipe.image}" class="card-img-top" alt="${recipe.title}">
              <div class="card-body d-flex flex-column">
                <h5 class="card-title">${recipe.title}</h5>
                <p class="card-text"><strong>Ready in:</strong> ${recipe.readyInMinutes} min</p>
                <a href="recipe-details.php?id=${recipe.id}&source=home" class="btn btn-sm btn-primary">
                  <i class="fas fa-eye"></i> View Details
                </a>
              </div>
            </div>
          `;
          container.appendChild(col);
        });

        page++;
        btn.disabled = false;
        loader.style.display = 'none';
      } else {
        btn.style.display = 'none';
        loader.innerText = 'No more recipes to load.';
      }
    })
    .catch(err => {
      console.error('Error loading more:', err);
      loader.innerText = 'Error loading more recipes.';
    });
});
</script>

    
</body>
</html>
