<?php
$currentPage = basename($_SERVER['PHP_SELF']);
if ($currentPage === 'login.php' || $currentPage === 'register.php') {
  return; // Hide navbar on login/register pages
}
?>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom mb-4 sticky-top">

  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">🍽️ RecipeHub</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
      aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
      <ul class="navbar-nav ms-auto">

        <?php if (isset($_SESSION['user_id'])): ?>
          <li class="nav-item"><a class="nav-link" href="profile.php"><i class="fas fa-user"></i> Profile</a></li>
          <li class="nav-item"><a class="nav-link" href="favorites.php"><i class="fas fa-heart"></i> Favorites</a></li>

          <?php if (!empty($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
            <li class="nav-item"><a class="nav-link" href="admin/dashboard.php"><i class="fas fa-cog"></i> Admin Panel</a></li>
          <?php endif; ?>

          <li class="nav-item"><a class="nav-link" href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>

        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt"></i> Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php"><i class="fas fa-user-plus"></i> Register</a></li>
        <?php endif; ?>
      </ul>

      <button class="btn btn-sm btn-outline-light ms-3" id="darkToggle" title="Toggle dark mode">
        🌙 Dark Mode
      </button>
    </div>
  </div>
</nav>

<script>
  const toggle = document.getElementById('darkToggle');
  const body = document.body;
  const header = document.getElementById('mainHeader');

  // Apply dark mode if previously enabled
  if (localStorage.getItem('darkMode') === 'enabled') {
    enableDarkMode();
  }

  function enableDarkMode() {
    body.classList.add('bg-dark', 'text-light');
    header?.classList.remove('bg-light', 'text-dark');
    header?.classList.add('bg-dark', 'text-light');
    localStorage.setItem('darkMode', 'enabled');
  }

  function disableDarkMode() {
    body.classList.remove('bg-dark', 'text-light');
    header?.classList.remove('bg-dark', 'text-light');
    header?.classList.add('bg-light', 'text-dark');
    localStorage.setItem('darkMode', 'disabled');
  }

  toggle?.addEventListener('click', () => {
    if (body.classList.contains('bg-dark')) {
      disableDarkMode();
    } else {
      enableDarkMode();
    }
  });
</script>
