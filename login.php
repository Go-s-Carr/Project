<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if($user && password_verify($password, $user['password'])){
        $_SESSION['id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role'];

        // التوجيه حسب الرتبة
        if($user['role'] == 'admin') header("Location: ../admin/dashboard.php");
        elseif($user['role'] == 'restaurant') header("Location: ../restaurant/dashboard_res.php");
        else header("Location: ../client/index.php");
        exit;
    } else {
        $error = "Email ou mot de passe incorrect.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Connexion - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #007bff 0%, #ff6600 100%); height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0; }
        .login-card { background: white; border-radius: 20px; padding: 40px; box-shadow: 0 15px 35px rgba(0,0,0,0.2); width: 100%; max-width: 400px; }
        .btn-custom { background: #ff6600; color: white; border: none; font-weight: bold; }
        .btn-custom:hover { background: #0056b3; color: white; }
        .text-orange { color: #ff6600; }
        .text-blue { color: #007bff; }
    </style>
</head>
<body>
<div class="login-card text-center">
    <h2 class="fw-bold mb-4"><span class="text-orange">Food</span><span class="text-blue">Connect</span></h2>
    <?php if($error) echo "<div class='alert alert-danger py-2'>$error</div>"; ?>
    <?php if(isset($_GET['success'])) echo "<div class='alert alert-success py-2'>Compte créé ! Connectez-vous.</div>"; ?>
    
    <form method="POST">
        <div class="mb-3 text-start">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-4 text-start">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-custom w-100 py-2">SE CONNECTER</button>
    </form>
    <div class="mt-4">
        <small>Pas de compte ? <a href="register.php" class="text-blue fw-bold text-decoration-none">S'inscrire</a></small>
    </div>
</div>
</body>
</html>