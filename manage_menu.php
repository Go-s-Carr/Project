<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية
if(!isRole('restaurant')){ header("Location: ../auth/login.php"); exit; }

$user_id = $_SESSION['id'];

try {
    // جلب ID المطعم
    $stmtRes = $pdo->prepare("SELECT id FROM restaurants WHERE name = (SELECT username FROM users WHERE id = ?)");
    $stmtRes->execute([$user_id]);
    $resto_id = $stmtRes->fetchColumn();

    if (!$resto_id) { die("Erreur : Restaurant introuvable."); }

    // إضافة طبق
    if(isset($_POST['add_dish'])){
        $name = trim($_POST['dish_name']);
        $price = $_POST['price'];
        if(!empty($name) && !empty($price)){
            $stmt = $pdo->prepare("INSERT INTO menu (restaurant_id, name, price) VALUES (?, ?, ?)");
            $stmt->execute([$resto_id, $name, $price]);
            header("Location: manage_menu.php"); exit;
        }
    }

    // حذف طبق
    if(isset($_GET['delete'])){
        $stmt = $pdo->prepare("DELETE FROM menu WHERE id = ? AND restaurant_id = ?");
        $stmt->execute([$_GET['delete'], $resto_id]);
        header("Location: manage_menu.php"); exit;
    }

    $items = $pdo->prepare("SELECT * FROM menu WHERE restaurant_id = ? ORDER BY id DESC");
    $items->execute([$resto_id]);
    $menu_items = $items->fetchAll();

} catch (PDOException $e) { die("Erreur : " . $e->getMessage()); }
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Menu Manage - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; --dark: #212529; }
        body { background-color: #f8f9fa; }
        .navbar-custom { background: linear-gradient(135deg, var(--dark), var(--orange)); padding: 15px; }
        .card-menu { border: none; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); }
        .btn-add { background: var(--orange); color: white; font-weight: bold; border: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-5 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="./dashboard_res.php">
            <img src="../assets/img/logo.png.jpeg" height="40" class="me-2 rounded bg-white"> 
            Gestion Menu
        </a>
        
        <div class="ms-auto">
            <a href="./dashboard_res.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold shadow-sm">
                <i class="bi bi-house-door-fill me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container">
    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-menu p-4">
                <h5 class="fw-bold mb-3">Ajouter un plat</h5>
                <form method="POST">
                    <div class="mb-3">
                        <label class="small fw-bold">Nom du plat</label>
                        <input type="text" name="dish_name" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Prix (DT)</label>
                        <input type="number" step="0.001" name="price" class="form-control" required>
                    </div>
                    <button type="submit" name="add_dish" class="btn btn-add w-100 py-2">AJOUTER</button>
                </form>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card card-menu p-4">
                <h5 class="fw-bold mb-4">Votre Carte</h5>
                <table class="table align-middle">
                    <thead>
                        <tr><th>Plat</th><th>Prix</th><th class="text-center">Action</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach($menu_items as $item): ?>
                        <tr>
                            <td class="fw-bold"><?= htmlspecialchars($item['name']) ?></td>
                            <td class="text-primary fw-bold"><?= number_format($item['price'], 3) ?> DT</td>
                            <td class="text-center">
                                <a href="./manage_menu.php?delete=<?= $item['id'] ?>" class="text-danger fs-5">
                                    <i class="bi bi-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>