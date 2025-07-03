<?php
session_start();
require_once 'includes/config.php';

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$recipes = [];

$apiKey = SPOONACULAR_API_KEY;

if (!empty($search)) {
    // Search by query (name or ingredient)
    $api_url = "https://api.spoonacular.com/recipes/complexSearch?query=" . urlencode($search) . "&number=12&addRecipeInformation=true&apiKey={$apiKey}";
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    $recipes = $data['results'] ?? [];
} else {
    // Default curated recipes (vegetarian + popular)
    $api_url = "https://api.spoonacular.com/recipes/random?number=12&tags=vegetarian&apiKey={$apiKey}";
    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    $data = json_decode($response, true);
    $recipes = $data['recipes'] ?? [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Recipe Finder - Home</title>
  <link rel="stylesheet" href="assets/css/style.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body>
  <header class="text-center p-4 bg-light">
    <h1>🍽️ Recipe Finder</h1>
    <p>Select ingredients or search recipes by name</p>
  </header>

  <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container">
      <a class="navbar-brand" href="index.php">RecipeHub</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
          <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ms-auto">
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item"><a class="nav-link" href="profile.php">Profile</a></li>
            <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
              <li class="nav-item"><a class="nav-link" href="admin/dashboard.php">Admin Panel</a></li>
            <?php endif; ?>
            <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
            <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
          <?php endif; ?>
        </ul>
      </div>
    </div>
  </nav>

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
    <div class="col-md-4 mb-4 recipe-card <?= $index >= 6 ? 'd-none' : '' ?>">
      <div class="card h-100 shadow-sm">
        <img src="<?= htmlspecialchars($recipe['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($recipe['title']) ?>">
        <div class="card-body d-flex flex-column">
          <h5 class="card-title"><?= htmlspecialchars($recipe['title']) ?></h5>
          <p class="card-text"><strong>Ready in:</strong> <?= htmlspecialchars($recipe['readyInMinutes']) ?> min</p>
          <a href="recipe-details.php?id=<?= $recipe['id'] ?>&source=home" class="btn btn-sm btn-primary">
    <i class="fas fa-eye"></i> View Details
</a>

        </div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<?php if (count($recipes) > 6): ?>
  <div class="text-center mt-3">
    <button id="seeMoreBtn" class="btn btn-outline-primary">See More</button>
  </div>
<?php endif; ?>

  </main>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
  const seeMoreBtn = document.getElementById('seeMoreBtn');
  const recipeCards = document.querySelectorAll('.recipe-card');
  let visible = 6;

  if (seeMoreBtn) {
    seeMoreBtn.addEventListener('click', () => {
      let shown = 0;
      for (let i = visible; i < recipeCards.length && shown < 3; i++) {
        recipeCards[i].classList.remove('d-none');
        shown++;
      }
      visible += shown;
      if (visible >= recipeCards.length) {
        seeMoreBtn.style.display = 'none';
      }
    });
  }
</script>
</body>
</html>
