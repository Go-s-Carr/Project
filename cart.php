<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

if(!isLogged()){ header("Location: ../auth/login.php"); exit; }

// Traitement pour vider un article ou modifier
if(isset($_GET['remove'])){
    unset($_SESSION['cart'][$_GET['remove']]);
    header("Location: cart.php");
    exit;
}

$total = 0;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Mon Panier - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .navbar-orange { background-color: #ff6600; }
        .logo-img { height: 40px; border-radius: 5px; }
        .cart-card { border: none; border-radius: 15px; background: white; }
    </style>
</head>
<body style="background-color: #f4f7f6;">

<nav class="navbar navbar-expand-lg navbar-dark navbar-orange shadow-sm sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="index.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img" alt="Logo">
      FoodConnect
    </a>
    <div class="ms-auto">
        <a href="index.php" class="btn btn-sm btn-outline-light">Continuer mes achats</a>
    </div>
  </div>
</nav>

<div class="container mt-5">
    <h2 class="fw-bold mb-4" style="color: #007bff;">Votre Panier</h2>

    <?php if(!empty($_SESSION['cart'])): ?>
    <div class="row">
        <div class="col-md-8">
            <div class="card cart-card shadow-sm p-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Plat</th>
                            <th>Prix</th>
                            <th>Quantité</th>
                            <th>Total</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($_SESSION['cart'] as $id => $qty): 
                            $stmt = $pdo->prepare("SELECT * FROM menu WHERE id = ?");
                            $stmt->execute([$id]);
                            $dish = $stmt->fetch();
                            $subtotal = $dish['price'] * $qty;
                            $total += $subtotal;
                        ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($dish['name']) ?></td>
                            <td><?= number_format($dish['price'], 3) ?> DT</td>
                            <td><span class="badge bg-light text-dark p-2 px-3 border"><?= $qty ?></span></td>
                            <td class="fw-bold text-primary"><?= number_format($subtotal, 3) ?> DT</td>
                            <td><a href="?remove=<?= $id ?>" class="text-danger fs-5"><i class="bi bi-trash"></i></a></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card cart-card shadow-sm p-4">
                <h4 class="fw-bold mb-4 text-center">Résumé</h4>
                <div class="d-flex justify-content-between mb-2">
                    <span>Sous-total:</span>
                    <span class="fw-bold"><?= number_format($total, 3) ?> DT</span>
                </div>
                <div class="d-flex justify-content-between mb-4">
                    <span>Livraison:</span>
                    <span class="text-success fw-bold">Gratuite</span>
                </div>
                <hr>
                <div class="d-flex justify-content-between fs-5 mb-4">
                    <strong>TOTAL:</strong>
                    <strong style="color: #ff6600;"><?= number_format($total, 3) ?> DT</strong>
                </div>
                <form action="checkout.php" method="POST">
                    <input type="hidden" name="total" value="<?= $total ?>">
                    <button type="submit" class="btn btn-primary w-100 py-2 fw-bold shadow">COMMANDER MAINTENANT</button>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
        <div class="text-center p-5 bg-white rounded shadow-sm">
            <i class="bi bi-cart-x display-1 text-muted"></i>
            <h4 class="mt-3">Votre panier est vide.</h4>
            <a href="index.php" class="btn btn-primary mt-3 px-5">Aller au menu</a>
        </div>
    <?php endif; ?>
</div>

</body>
</html>