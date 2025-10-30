<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Real Estate Management System</title>
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
  <header>
    <nav class="navbar">
      <div class="logo">🏠 RealEstatePro</div>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="properties.php">Properties</a></li>
        <li><a href="about.php">About</a></li>
        <li><a href="contact.php">Contact</a></li>
        <?php if(isset($_SESSION['username'])): ?>
          <li><a href="logout.php" class="btn">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php" class="btn">Login</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>
