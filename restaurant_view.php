<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

$id = $_GET['id'] ?? 0;

// 1. Récupérer les infos du restaurant
$stmtRes = $pdo->prepare("SELECT * FROM restaurants WHERE id = ?");
$stmtRes->execute([$id]);
$resto = $stmtRes->fetch();

if(!$resto) die("Restaurant introuvable.");

// 2. Récupérer les plats du menu
$stmtMenu = $pdo->prepare("SELECT * FROM menu WHERE restaurant_id = ?");
$stmtMenu->execute([$id]);
$menus = $stmtMenu->fetchAll();

// Calculer le nombre d'articles dans le panier
$cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($resto['name']) ?> - Menu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; }
        body { background-color: #f4f7f6; }
        .navbar-custom { background: linear-gradient(90deg, var(--blue), var(--orange)); }
        .resto-banner { background: white; padding: 40px 0; border-bottom: 5px solid var(--orange); margin-bottom: 40px; }
        .dish-card { border: none; border-radius: 15px; transition: 0.3s; background: white; margin-bottom: 15px; border-left: 5px solid white; }
        .dish-card:hover { border-left: 5px solid var(--blue); transform: translateX(10px); box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .btn-add { background-color: var(--orange); color: white; border: none; font-weight: bold; border-radius: 10px; }
        .btn-add:hover { background-color: var(--blue); color: white; }
        .price-tag { color: var(--blue); font-weight: bold; font-size: 1.2rem; }
        .badge-cart { background-color: var(--orange); color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php">FoodConnect</a>
    <div class="ms-auto d-flex align-items-center">
        <a href="cart.php" class="nav-link text-white position-relative me-3">
            <i class="bi bi-cart3 fs-5"></i>
            <?php if($cartCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill badge-cart"><?= $cartCount ?></span>
            <?php endif; ?>
        </a>
        <a href="index.php" class="btn btn-sm btn-light fw-bold text-primary">Retour</a>
    </div>
  </div>
</nav>

<div class="resto-banner shadow-sm">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="display-5 fw-bold" style="color: var(--blue);"><?= htmlspecialchars($resto['name']) ?></h1>
                <p class="text-muted"><i class="bi bi-geo-alt-fill text-danger"></i> Kairouan, Tunisie • <i class="bi bi-star-fill text-warning"></i> 4.8 (Expert en goût)</p>
            </div>
            <div class="col-md-4 text-md-end">
                <span class="badge bg-success p-2 px-3">Ouvert</span>
            </div>
        </div>
    </div>
</div>

<div class="container mb-5">
    <h3 class="fw-bold mb-4" style="color: var(--orange);">Menu du jour</h3>
    
    <div class="row">
        <?php if($menus): ?>
            <?php foreach($menus as $item): ?>
            <div class="col-md-10 mx-auto">
                <div class="dish-card shadow-sm p-3 d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-1"><?= htmlspecialchars($item['name']) ?></h5>
                        <p class="text-muted small mb-0">Préparé avec des ingrédients frais et locaux.</p>
                    </div>
                    <div class="d-flex align-items-center gap-4">
                        <span class="price-tag"><?= number_format($item['price'], 3) ?> DT</span>
                        <form method="POST" action="cart.php">
                            <input type="hidden" name="id" value="<?= $item['id'] ?>">
                            <button type="submit" class="btn btn-add px-4">
                                <i class="bi bi-plus-lg"></i> Ajouter
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center p-5">
                <p class="text-muted">Aucun plat disponible pour le moment.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>