<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية: السماح فقط للأدمن
if(!isRole('admin')){ 
    header("Location: ../auth/login.php"); 
    exit; 
}

// 1. حذف مستخدم
if(isset($_GET['delete'])){
    $id_to_delete = $_GET['delete'];
    // حماية: الأدمن ما ينجمش يفسخ روحو
    if($id_to_delete != $_SESSION['id']){
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id_to_delete]);
        header("Location: manage_users.php?msg=deleted");
        exit;
    }
}

// 2. جلب قائمة كل المستخدمين
$stmtUsers = $pdo->query("SELECT * FROM users ORDER BY id DESC");
$all_users = $stmtUsers->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion Utilisateurs - FoodConnect Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    
    <style>
        :root { --admin-blue: #007bff; --admin-dark: #212529; }
        body { background-color: #f0f2f5; font-family: 'Segoe UI', sans-serif; }
        
        .navbar-admin { background: linear-gradient(135deg, var(--admin-dark), var(--admin-blue)); padding: 15px 0; }
        .card-table { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.08); background: white; }
        
        .badge-role { padding: 6px 12px; border-radius: 10px; font-weight: 600; font-size: 0.8rem; }
        .role-admin { background-color: #ffe8e8; color: #dc3545; }
        .role-restaurant { background-color: #e7f3ff; color: #007bff; }
        .role-client { background-color: #e8fadf; color: #28a745; }
        
        .btn-action { border-radius: 10px; transition: 0.3s; }
        .btn-action:hover { transform: translateY(-2px); }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-admin shadow-sm mb-5">
    <div class="container">
        <a class="navbar-brand fw-bold d-flex align-items-center" href="dashboard.php">
            <i class="bi bi-shield-lock-fill me-2"></i> FoodConnect Admin
        </a>
        <div class="ms-auto">
            <a href="dashboard.php" class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold">
                <i class="bi bi-speedometer2 me-1"></i> Dashboard
            </a>
        </div>
    </div>
</nav>

<div class="container mb-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-dark"><i class="bi bi-people-fill me-2 text-primary"></i>Liste des utilisateurs</h3>
        <?php if(isset($_GET['msg'])): ?>
            <div class="alert alert-success py-2 px-4 rounded-pill shadow-sm small mb-0">
                Action effectuée avec succès !
            </div>
        <?php endif; ?>
    </div>

    <div class="card card-table p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="border-0">ID</th>
                        <th class="border-0">Nom d'utilisateur</th>
                        <th class="border-0">Rôle</th>
                        <th class="border-0 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($all_users as $user): ?>
                    <tr>
                        <td class="text-muted fw-bold">#<?= $user['id'] ?></td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                    <?= strtoupper(substr($user['username'], 0, 1)) ?>
                                </div>
                                <span class="fw-bold text-secondary"><?= htmlspecialchars($user['username']) ?></span>
                            </div>
                        </td>
                        <td>
                            <span class="badge-role role-<?= $user['role'] ?>">
                                <i class="bi bi-person-badge me-1"></i> <?= strtoupper($user['role']) ?>
                            </span>
                        </td>
                        <td class="text-center">
                            <a href="./edit_user.php?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-warning btn-action me-2 border-0 shadow-sm">
                                <i class="bi bi-pencil-square"></i> Modifier
                            </a>
                            
                            <?php if($user['id'] != $_SESSION['id']): ?>
                                <a href="manage_users.php?delete=<?= $user['id'] ?>" 
                                   class="btn btn-sm btn-outline-danger btn-action border-0 shadow-sm"
                                   onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')">
                                    <i class="bi bi-trash3"></i>
                                </a>
                            <?php else: ?>
                                <span class="text-muted small italic">Vous (Admin)</span>
                            <?php endif; ?>
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