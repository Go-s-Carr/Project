<?php
// 1. SECURITE SESSION (Evite l'erreur "Session already active")
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once "../config/db.php";
require_once "../includes/functions.php";

// 2. VERIFICATION DU PANIER
if (!isset($_SESSION['id']) || empty($_SESSION['cart'])) {
    header("Location: index.php");
    exit;
}

$client_id = $_SESSION['id'];
$total = $_POST['total'] ?? 0;

try {
    $pdo->beginTransaction();

    // Récupérer le premier resto du panier
    $firstDishId = array_key_first($_SESSION['cart']);
    $stmtRes = $pdo->prepare("SELECT restaurant_id FROM menu WHERE id = ?");
    $stmtRes->execute([$firstDishId]);
    $res = $stmtRes->fetch();
    $restaurant_id = $res['restaurant_id'];

    // Insertion de la commande
    $stmtOrder = $pdo->prepare("INSERT INTO orders (client_id, restaurant_id, total, status) VALUES (?, ?, ?, 'pending')");
    $stmtOrder->execute([$client_id, $restaurant_id, $total]);
    $order_id = $pdo->lastInsertId();

    // Insertion des articles
    foreach ($_SESSION['cart'] as $id => $qty) {
        $stmtPrice = $pdo->prepare("SELECT price FROM menu WHERE id = ?");
        $stmtPrice->execute([$id]);
        $dish = $stmtPrice->fetch();

        $stmtItem = $pdo->prepare("INSERT INTO order_items (order_id, dish_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmtItem->execute([$order_id, $id, $qty, $dish['price']]);
    }

    $pdo->commit();
    $_SESSION['cart'] = []; // Vider le panier après commande réussie

} catch (Exception $e) {
    $pdo->rollBack();
    die("Erreur de commande : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Commande Validée - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f8f9fa; }
        .navbar-orange { background-color: #ff6600; }
        .logo-img { height: 40px; width: auto; margin-right: 10px; }
        .success-card { background: white; border-radius: 20px; padding: 40px; border-top: 6px solid #007bff; }
        .btn-confirm { background-color: #ff6600; color: white; border: none; font-weight: bold; }
        .btn-confirm:hover { background-color: #007bff; color: white; }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-orange shadow-sm mb-5">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img" alt="FC Logo">
      FoodConnect
    </a>
  </div>
</nav>

<div class="container text-center">
    <div class="success-card shadow-sm mx-auto" style="max-width: 500px;">
        <i class="bi bi-check-circle-fill text-success display-1 mb-3"></i>
        <h2 class="fw-bold" style="color: #007bff;">Commande #<?= $order_id ?> Reçue !</h2>
        <p class="text-muted">Votre commande a été envoyée au restaurant. Préparez-vous à déguster votre repas !</p>
        <div class="alert alert-info py-2">Total à payer : <strong><?= number_format($total, 3) ?> DT</strong></div>
        <hr>
        <a href="index.php" class="btn btn-confirm w-100 py-2 mt-2">Retour à l'accueil</a>
    </div>
</div>

</body>
</html>