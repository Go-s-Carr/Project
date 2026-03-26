<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية
if(!isRole('restaurant')){ header("Location: ../auth/login.php"); exit; }

$user_id = $_SESSION['id'];

try {
    // 1. جلب بيانات المطعم
    $stmtResto = $pdo->prepare("SELECT id, name FROM restaurants WHERE name = (SELECT username FROM users WHERE id = ?)");
    $stmtResto->execute([$user_id]);
    $resto = $stmtResto->fetch();

    if (!$resto) { die("Erreur : Aucun restaurant lié à ce compte."); }

    $resto_id = $resto['id'];

    // 2. الإحصائيات
    $countOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE restaurant_id = $resto_id")->fetchColumn();
    $totalSales = $pdo->query("SELECT SUM(total) FROM orders WHERE restaurant_id = $resto_id AND status = 'completed'")->fetchColumn() ?? 0;
    $countMenu = $pdo->query("SELECT COUNT(*) FROM menu WHERE restaurant_id = $resto_id")->fetchColumn();

} catch (PDOException $e) { die("Erreur SQL : " . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard Resto - <?= htmlspecialchars($resto['name']) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; --dark: #1a1d20; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .navbar-custom { background: linear-gradient(135deg, var(--dark), var(--orange)); padding: 15px 0; }
        .card-stat { border: none; border-radius: 25px; color: white; padding: 40px 20px; transition: 0.4s; position: relative; overflow: hidden; }
        .card-stat:hover { transform: translateY(-10px); }
        .card-blue { background: linear-gradient(45deg, #007bff, #00d4ff); }
        .card-green { background: linear-gradient(45deg, #28a745, #a8e063); }
        .card-orange { background: linear-gradient(45deg, #ff6600, #ffb347); }
        .stat-icon { position: absolute; right: 20px; top: 20px; font-size: 5rem; opacity: 0.2; }
        .stat-number { font-size: 4rem; font-weight: 900; }
        .btn-manage { background: white; color: var(--dark); border-radius: 15px; font-weight: bold; padding: 10px; width: 100%; display: block; text-decoration: none; margin-top: 15px; text-align: center; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom shadow-sm mb-5">
  <div class="container">
    <a class="navbar-brand fw-bold" href="./dashboard_res.php">
        <img src="../assets/img/logo.png.jpeg" height="40" class="me-2 rounded bg-white p-1"> FoodConnect PRO
    </a>
    <a href="../auth/logout.php" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-4">Déconnexion</a>
  </div>
</nav>

<div class="container">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-stat card-blue shadow">
                <i class="bi bi-cart-check-fill stat-icon"></i>
                <div class="h5 text-uppercase">Commandes</div>
                <div class="stat-number"><?= $countOrders ?></div>
                <a href="./manage_orders.php" class="btn-manage">Voir les commandes</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat card-green shadow">
                <i class="bi bi-cash-stack stat-icon"></i>
                <div class="h5 text-uppercase">Revenus (DT)</div>
                <div class="stat-number"><?= number_format($totalSales, 3) ?></div>
                <a href="./manage_orders.php" class="btn-manage">Détails Ventes</a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-stat card-orange shadow">
                <i class="bi bi-menu-button-wide stat-icon"></i>
                <div class="h5 text-uppercase">Plats Menu</div>
                <div class="stat-number"><?= $countMenu ?></div>
                <a href="./manage_menu.php" class="btn-manage">Modifier Carte</a>
            </div>
        </div>
    </div>
</div>

</body>
</html>