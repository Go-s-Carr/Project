<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// الحماية: التأكد أن المستخدم أدمن
if(!isRole('admin')){ 
    header("Location: ../auth/login.php"); 
    exit; 
}

// جلب الـ ID من الرابط (URL)
$id = isset($_GET['id']) ? $_GET['id'] : null;

if(!$id){
    header("Location: manage_users.php");
    exit;
}

// 1. جلب بيانات المستخدم الحالية من القاعدة
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    $user = $stmt->fetch();

    if(!$user){
        die("Utilisateur introuvable !");
    }

    // 2. تحديث البيانات عند الضغط على الزر
    if(isset($_POST['update_btn'])){
        $new_username = $_POST['username'];
        $new_role = $_POST['role'];

        $updateStmt = $pdo->prepare("UPDATE users SET username = ?, role = ? WHERE id = ?");
        if($updateStmt->execute([$new_username, $new_role, $id])){
            // الرجوع لصفحة المستخدمين مع رسالة نجاح
            header("Location: manage_users.php?msg=success");
            exit;
        }
    }
} catch (PDOException $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier Utilisateur - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }
        .navbar-admin { background: #212529; padding: 15px; }
        .card-edit { border: none; border-radius: 20px; box-shadow: 0 10px 30px rgba(0,0,0,0.1); background: white; }
        .btn-update { background: #007bff; color: white; font-weight: bold; border: none; }
    </style>
</head>
<body>

<nav class="navbar navbar-dark navbar-admin mb-5 shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php">Admin FoodConnect</a>
        <a href="manage_users.php" class="btn btn-outline-light btn-sm rounded-pill px-3">Annuler</a>
    </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-edit p-4 mt-3">
                <h4 class="fw-bold mb-4 text-center text-primary">Modifier Profil</h4>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label small fw-bold">Nom d'utilisateur</label>
                        <input type="text" name="username" class="form-control rounded-3" 
                               value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label small fw-bold">Rôle sur la plateforme</label>
                        <select name="role" class="form-select rounded-3">
                            <option value="client" <?= $user['role'] == 'client' ? 'selected' : '' ?>>Client</option>
                            <option value="restaurant" <?= $user['role'] == 'restaurant' ? 'selected' : '' ?>>Restaurant</option>
                            <option value="admin" <?= $user['role'] == 'admin' ? 'selected' : '' ?>>Admin</option>
                        </select>
                    </div>

                    <button type="submit" name="update_btn" class="btn btn-update w-100 py-2 rounded-3 shadow-sm">
                        ENREGISTRER LES MODIFICATIONS
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>