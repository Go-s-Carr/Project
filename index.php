<?php
// Gestion de la session
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// Sécurité : Vérifier si l'utilisateur est connecté
if(!isLogged()){ header("Location: ../auth/login.php"); exit; }

// Récupération des restaurants
$restos = $pdo->query("SELECT * FROM restaurants")->fetchAll();
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>FoodConnect - Accueil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; }
        body { background-color: #f8f9fa; }
        .navbar-orange { background-color: var(--orange); padding: 10px 0; }
        .logo-img { height: 40px; width: auto; margin-right: 10px; border-radius: 5px; background: white; padding: 2px; }
        
        /* Style de la barre de recherche */
        .search-input { 
            border-radius: 20px 0 0 20px; 
            border: none; 
            padding-left: 20px;
            width: 300px !important;
        }
        .btn-search { 
            border-radius: 0 20px 20px 0; 
            background-color: var(--blue); 
            color: white; 
            border: none;
        }
        .btn-search:hover { background-color: #0056b3; color: white; }

        .card-resto { border: none; border-radius: 15px; transition: 0.3s; overflow: hidden; }
        .card-resto:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
        .badge-cart { background-color: #dc3545; font-size: 0.7rem; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-orange shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img" alt="Logo">
      <span>FoodConnect</span>
    </a>

    <div class="collapse navbar-collapse" id="navbarContent">
      <form action="search.php" method="GET" class="d-flex mx-auto shadow-sm" style="border-radius: 20px; overflow: hidden;">
        <input name="q" class="form-control search-input" type="search" placeholder="Chercher un restaurant..." aria-label="Search" required>
        <button class="btn btn-search px-3" type="submit">
            <i class="bi bi-search"></i>
        </button>
      </form>

      <ul class="navbar-nav ms-auto align-items-center">
        <li class="nav-item">
            <a class="nav-link" href="map_view.php"><i class="bi bi-map fs-5"></i> Map</a>
        </li>
        <li class="nav-item px-3">
            <a class="nav-link position-relative" href="cart.php">
                <i class="bi bi-cart3 fs-5"></i>
                <?php if($cartCount > 0): ?>
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart">
                        <?= $cartCount ?>
                    </span>
                <?php endif; ?>
            </a>
        </li>
        <li class="nav-item ms-2">
            <a class="btn btn-sm btn-light text-primary fw-bold px-3 shadow-sm" href="../auth/logout.php">Déconnexion</a>
        </li>
      </ul>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <div class="p-4 mb-5 bg-white shadow-sm rounded-4 border-start border-5 border-primary">
        <h2 class="fw-bold mb-1">Salut, <span style="color: var(--orange);"><?= htmlspecialchars($_SESSION['username']) ?></span> 👋</h2>
        <p class="text-muted mb-0">Qu'est-ce qu'on mange aujourd'hui à Kairouan ?</p>
    </div>

    <h4 class="fw-bold mb-4"><i class="bi bi-shop text-primary"></i> Nos Restaurants</h4>
    <div class="row g-4 mb-5">
        <?php foreach($restos as $resto): ?>
        <div class="col-md-4">
            <div class="card h-100 card-resto shadow-sm">
                <img src="../assets/img/restaurants/<?= $resto['image'] ?? 'default.jpg' ?>" class="card-img-top" style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="fw-bold"><?= htmlspecialchars($resto['name']) ?></h5>
                    <p class="text-muted small">Cuisine tunisienne et internationale.</p>
                    <a href="restaurant_view.php?id=<?= $resto['id'] ?>" class="btn btn-outline-primary w-100 py-2 fw-bold">Consulter Menu</a>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>