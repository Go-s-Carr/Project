<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

if(!isLogged()){ header("Location: ../auth/login.php"); exit; }

$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FoodConnect - Map View</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .navbar-orange { background-color: #ff6600; }
        .logo-img { height: 40px; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-orange shadow-sm">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img me-2" alt="Logo">
      FoodConnect
    </a>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item"><a class="nav-link active" href="map_view.php"><i class="bi bi-map"></i> Map</a></li>
        <li class="nav-item"><a class="nav-link" href="cart.php"><i class="bi bi-cart3"></i> Panier (<?= $cartCount ?>)</a></li>
        <li class="nav-item ms-3"><a class="btn btn-sm btn-light text-primary" href="../auth/logout.php">Déconnexion</a></li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5 text-center">
    <h2 class="fw-bold mb-4" style="color: #007bff;">Restaurants autour de vous</h2>
    <div id="map" style="height: 500px; background: #eee; border-radius: 20px;" class="shadow-sm d-flex align-items-center justify-content-center">
        <p class="text-muted"><i class="bi bi-geo-alt-fill"></i> Carte interactive en cours de chargement...</p>
    </div>
</div>

</body>
</html>