<?php
// auth.php - Check if user is logged in

session_start();

if (!isset($_SESSION['user_id'])) {
    // Not logged in, redirect to login page
    header('Location: ' . '/web-recipe-hub/login.php');
    exit();
}
?>
