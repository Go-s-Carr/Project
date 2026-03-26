<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية
if(!isRole('restaurant')){ header("Location: ../auth/login.php"); exit; }

$user_id = $_SESSION['id'];

try {
  
    $stmtRes = $pdo->prepare("SELECT id FROM restaurants WHERE name = (SELECT username FROM users WHERE id = ?)");
    $stmtRes->execute([$user_id]);
    $resto_id = $stmtRes->fetchColumn();

    if (!$resto_id) { die("Erreur : Aucun restaurant lié à ce compte."); }

    
    if(isset($_POST['update_status'])){
        $stmtUpdate = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ? AND restaurant_id = ?");
        $stmtUpdate->execute([$_POST['new_status'], $_POST['order_id'], $resto_id]);
        header("Location: ./manage_orders.php"); 
        exit;
    }


    $stmtOrders = $pdo->prepare("
        SELECT o.*, u.username as client_name 
        FROM orders o 
        JOIN users u ON o.client_id = u.id 
        WHERE o.restaurant_id = ? 
        ORDER BY o.id DESC
    ");
    $stmtOrders->execute([$resto_id]);
    $all_orders = $stmtOrders->fetchAll();

} catch (PDOException $e) {
    
    die("Erreur SQL : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Orders - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --blue: #007bff; --dark: #1a1d20; }
        body { background-color: #f4f7f6; }
        .navbar-custom { background: linear-gradient(135deg, var(--blue), var(--dark)); padding: 15px; }
        .order-card { border: none; border-radius: 15px; border-left: 6px solid #ddd; margin-bottom: 20px; background: white; }
        .status-pending { border-left-color: #ffc107; }
        .status-completed { border-left-color: #28a745; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-custom mb-5 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="./dashboard_res.php">
            <img src="../assets/img/logo.png.jpeg" height="40" class="me-2 rounded bg-white p-1"> Orders
        </a>
        <a href="./dashboard_res.php" class="btn btn-light btn-sm rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-house-door-fill me-1"></i> Dashboard
        </a>
    </div>
</nav>

<div class="container">
    <div class="row">
        <?php if(empty($all_orders)): ?>
            <div class="col-12 text-center p-5">
                <p class="text-muted">Aucune commande trouvée.</p>
            </div>
        <?php else: ?>
            <?php foreach($all_orders as $order): ?>
            <div class="col-md-6">
                <div class="card order-card shadow-sm p-4 status-<?= $order['status'] ?>">
                    <div class="d-flex justify-content-between mb-2">
                        <h6 class="fw-bold text-primary">ID: #<?= $order['id'] ?></h6>
                        <span class="badge bg-<?= $order['status'] == 'pending' ? 'warning text-dark' : 'success' ?>">
                            <?= strtoupper($order['status']) ?>
                        </span>
                    </div>
                    <p class="mb-1"><strong>Client:</strong> <?= htmlspecialchars($order['client_name']) ?></p>
                    <p class="mb-3"><strong>Total:</strong> <?= number_format($order['total'], 3) ?> DT</p>

                    <form method="POST" class="d-flex gap-2">
                        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                        <select name="new_status" class="form-select form-select-sm">
                            <option value="pending" <?= $order['status'] == 'pending' ? 'selected' : '' ?>>En attente</option>
                            <option value="completed" <?= $order['status'] == 'completed' ? 'selected' : '' ?>>Terminée</option>
                        </select>
                        <button type="submit" name="update_status" class="btn btn-sm btn-primary">Mettre à jour</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

</body>
</html>