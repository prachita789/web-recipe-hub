<?php
session_start();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Delete user's favorites first (due to foreign key constraints)
$pdo->prepare("DELETE FROM favorites WHERE user_id = :user_id")->execute(['user_id' => $user_id]);

// Delete the user
$pdo->prepare("DELETE FROM users WHERE id = :id")->execute(['id' => $user_id]);

// Logout and redirect
session_destroy();
header('Location: register.php?msg=account_deleted');
exit;
