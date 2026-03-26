<?php
// 1. إدارة الجلسة والحماية
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية: الأدمن فقط يدخل هنا
if(!isRole('admin')){ 
    header("Location: ../auth/login.php"); 
    exit; 
}

try {
    // 2. حساب عدد المستخدمين الحقيقيين
    $countUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();

    // 3. حساب المطاعم النشطة (فقط التي تملك حساب مستخدم موجود)
    // ملاحظة: إذا كان عندك Column اسمه user_id استعمله، هنا استعملت الربط بالاسم لتفادي الـ Error السابق
    $countRestos = $pdo->query("
        SELECT COUNT(r.id) 
        FROM restaurants r 
        INNER JOIN users u ON r.name = u.username 
        WHERE u.role = 'restaurant'
    ")->fetchColumn();

    // 4. جلب قائمة المستخدمين لعرضها في جدول (اختياري)
    $allUsers = $pdo->query("SELECT id, username, email, role FROM users ORDER BY id DESC LIMIT 10")->fetchAll();

} catch (PDOException $e) {
    die("Erreur Base de données : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FoodConnect Admin - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root { --orange: #ff6600; --blue: #007bff; --dark: #212529; }
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        
        /* Navbar */
        .navbar-admin { background: linear-gradient(135deg, var(--blue), var(--orange)); padding: 15px 0; }
        .logo-img { height: 45px; border-radius: 8px; background: white; padding: 2px; }

        /* Stats Cards */
        .card-stat { border: none; border-radius: 20px; color: white; padding: 35px; transition: 0.4s; position: relative; overflow: hidden; }
        .card-stat:hover { transform: translateY(-10px); }
        .card-users { background: linear-gradient(45deg, #007bff, #00c6ff); }
        .card-restos { background: linear-gradient(45deg, #ff6600, #ff9933); }
        
        .stat-icon { position: absolute; right: 20px; top: 20px; font-size: 5rem; opacity: 0.2; }
        .stat-number { font-size: 4.5rem; font-weight: 900; line-height: 1; }
        .stat-label { font-size: 1.3rem; font-weight: 500; text-transform: uppercase; letter-spacing: 1px; }

        .btn-action { 
            background: rgba(255, 255, 255, 0.2); 
            border: 1px solid rgba(255, 255, 255, 0.4); 
            color: white; 
            border-radius: 12px; 
            font-weight: bold; 
            padding: 10px;
            transition: 0.3s;
        }
        .btn-action:hover { background: white; color: var(--dark); }

        /* Table Style */
        .table-container { background: white; border-radius: 20px; padding: 25px; box-shadow: 0 10px 30px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-admin shadow-sm mb-5">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img me-2" alt="Logo">
      FoodConnect <span class="ms-2 badge bg-dark">ADMIN PANEL</span>
    </a>
    <div class="ms-auto d-flex align-items-center">
        <span class="text-white me-3 d-none d-md-block">Bienvenue, <strong>Admin</strong></span>
        <a href="../auth/logout.php" class="btn btn-sm btn-light text-danger fw-bold rounded-pill px-3">Déconnexion</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="row g-4 mb-5 justify-content-center">
        <div class="col-md-5">
            <div class="card card-stat card-users shadow">
                <i class="bi bi-people-fill stat-icon"></i>
                <div class="stat-label">Total Utilisateurs</div>
                <div class="stat-number"><?= $countUsers ?></div>
                <div class="mt-4">
                    <a href="manage_users.php" class="btn btn-action w-100">Gérer les comptes <i class="bi bi-arrow-right-short"></i></a>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card card-stat card-restos shadow">
                <i class="bi bi-shop stat-icon"></i>
                <div class="stat-label">Restaurants Actifs</div>
                <div class="stat-number"><?= $countRestos ?></div>
                <div class="mt-4">
                    <a href="register_res.php" class="btn btn-action w-100">Ajouter un Restaurant <i class="bi bi-plus-lg"></i></a>
                </div>
            </div>
        </div>
    </div>

    <div class="table-container mb-5">
        <h4 class="fw-bold mb-4 text-dark"><i class="bi bi-clock-history text-primary"></i> Dernières Inscriptions</h4>
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Utilisateur</th>
                        <th>Email</th>
                        <th>Rôle</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($allUsers as $user): ?>
                    <tr>
                        <td>#<?= $user['id'] ?></td>
                        <td class="fw-bold"><?= htmlspecialchars($user['username']) ?></td>
                        <td><?= htmlspecialchars($user['email']) ?></td>
                        <td>
                            <span class="badge rounded-pill bg-<?= $user['role'] == 'admin' ? 'dark' : ($user['role'] == 'restaurant' ? 'warning' : 'info') ?>">
                                <?= strtoupper($user['role']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary border-0"><i class="bi bi-pencil-square"></i></a>
                            <a href="delete_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-danger border-0" onclick="return confirm('Supprimer cet utilisateur ?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>