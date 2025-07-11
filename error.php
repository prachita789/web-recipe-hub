<?php
session_start();

$error = $_SESSION['error'] ?? 'Oops! Something went wrong.';
unset($_SESSION['error']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Error</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <style>
    body {
      background: linear-gradient(to right, #ffe6e6, #fff0f0);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      font-family: 'Segoe UI', sans-serif;
    }

    .error-box {
      text-align: center;
      background: white;
      padding: 3rem;
      border-radius: 1rem;
      box-shadow: 0 0 20px rgba(0,0,0,0.1);
      max-width: 400px;
    }

    .error-box i {
      font-size: 4rem;
      color: #dc3545;
      margin-bottom: 1rem;
      animation: pulse 1.2s infinite;
    }

    .btn-home {
      margin-top: 1.5rem;
    }

    @keyframes pulse {
      0% { transform: scale(1); }
      50% { transform: scale(1.1); }
      100% { transform: scale(1); }
    }
  </style>

  <script>
    setTimeout(() => {
      window.location.href = 'index.php'; // change this to your desired redirect
    }, 5000); // Redirect after 5 seconds
  </script>
</head>
<body>
  <div class="error-box">
    <i class="fas fa-exclamation-circle"></i>
    <h2>Error</h2>
    <p><?= htmlspecialchars($error) ?></p>
    <a href="index.php" class="btn btn-danger btn-home"><i class="fas fa-home"></i> Back to Home</a>
    <p class="mt-2 text-muted"><small>Redirecting in 5 seconds...</small></p>
  </div>
</body>
</html>
