<?php
require_once __DIR__ . '/includes/config.php';
if (isLoggedIn()) {
    header('Location: ' . APP_URL . '/index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Welcome — CityFix</title>
<link rel="stylesheet" href="<?= APP_URL ?>/public/css/style.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
</head>
<body>
<div class="welcome-page">
  <div class="welcome-illustration">🏙️</div>
  <h1>Welcome<br>to CityFix</h1>
  <p>Report Local Issues Easily.<br>Help make your community better.</p>
  <div class="welcome-actions">
    <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-white">Get Started</a>
    <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-outline" style="border-color:rgba(255,255,255,.4);color:#fff">Log In</a>
  </div>
</div>
</body>
</html>
