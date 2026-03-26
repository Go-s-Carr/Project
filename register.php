<?php
require_once "../config/db.php";

$error = "";

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // التحقق من الإيميل
    $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $check->execute([$email]);

    if($check->rowCount() > 0){
        $error = "Cet email est déjà utilisé !";
    } else {
        $hashed = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users(username, email, password, role) VALUES(?,?,?,'client')");
        if($stmt->execute([$name, $email, $hashed])){
            header("Location: login.php?success=1");
            exit;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Inscription Client - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #ff6600 0%, #007bff 100%); height: 100vh; display: flex; align-items: center; }
        .reg-card { background: white; border-radius: 20px; padding: 40px; width: 100%; max-width: 400px; margin: auto; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
        .btn-custom { background: #007bff; color: white; border: none; font-weight: bold; }
        .btn-custom:hover { background: #ff6600; color: white; }
    </style>
</head>
<body>
<div class="reg-card">
    <h3 class="text-center mb-4 fw-bold">Créer un <span style="color:#ff6600">Compte</span></h3>
    <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Nom d'utilisateur</label>
            <input type="text" name="name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Mot de passe</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" class="btn btn-custom w-100 py-2 mt-2">S'INSCRIRE</button>
    </form>
    <p class="text-center mt-3 small">Déjà inscrit ? <a href="login.php" class="text-decoration-none fw-bold" style="color:#ff6600">Connexion</a></p>
</div>
</body>
</html>