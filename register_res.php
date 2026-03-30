<?php
if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once "../config/db.php";
require_once "../includes/functions.php";

// Sécurité : Uniquement pour l'admin
if(!isRole('admin')){
    header("Location: ../auth/login.php");
    exit;
}

$msg = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    if (!empty($name) && !empty($email) && !empty($password)) {
        try {
            $pdo->beginTransaction();

            // 1. Insertion dans la table 'users'
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $stmt1 = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'restaurant')");
            $stmt1->execute([$name, $email, $hashed_password]);
            
            // 2. Récupérer l'ID de l'utilisateur créé
            $user_id = $pdo->lastInsertId();

            // 3. Insertion dans la table 'restaurants'
            // NOTE: Vérifie bien que ta table 'restaurants' contient les colonnes 'user_id' et 'name'
            $stmt2 = $pdo->prepare("INSERT INTO restaurants (user_id, name) VALUES (?, ?)");
            $stmt2->execute([$user_id, $name]);

            $pdo->commit();
            $msg = "<div class='alert alert-success'>Le restaurant <b>$name</b> a été ajouté avec succès !</div>";
        } catch (Exception $e) {
            $pdo->rollBack();
            $msg = "<div class='alert alert-danger'>Erreur : " . $e->getMessage() . "</div>";
        }
    } else {
        $msg = "<div class='alert alert-warning'>Veuillez remplir tous les champs.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un Restaurant - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; }
        body { background-color: #f8f9fa; }
        .navbar-custom { background: linear-gradient(90deg, var(--blue), var(--orange)); }
        .logo-img { height: 40px; border-radius: 5px; }
        .card-form { border: none; border-radius: 20px; box-shadow: 0 10px 25px rgba(0,0,0,0.1); }
        .btn-orange { background-color: var(--orange); color: white; font-weight: bold; border: none; }
        .btn-orange:hover { background-color: var(--blue); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm mb-5">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center fw-bold" href="dashboard.php">
      <img src="../assets/img/logo.png.jpeg" class="logo-img me-2" alt="Logo">
      FoodConnect Admin
    </a>
    <a href="dashboard.php" class="btn btn-sm btn-outline-light">Retour</a>
  </div>
</nav>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card card-form p-4">
                <h3 class="text-center fw-bold mb-4" style="color: var(--blue);">Nouveau Restaurant</h3>
                
                <?= $msg ?>

                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label fw-bold">Nom du Restaurant</label>
                        <input type="text" name="name" class="form-control" placeholder="Nom du resto" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-bold">Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@resto.tn" required>
                    </div>

                    <div class="mb-4">
                        <label class="form-label fw-bold">Mot de passe</label>
                        <input type="password" name="password" class="form-control" placeholder="********" required>
                    </div>

                    <button type="submit" class="btn btn-orange w-100 py-2 shadow-sm">CRÉER LE RESTO</button>
                </form>
            </div>
        </div>
    </div>
</div>

</body>
</html>