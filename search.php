<?php
session_start();
require_once "../config/db.php";
require_once "../includes/functions.php";

$q = $_GET['q'] ?? '';

// Recherche des restaurants par nom
$stmt = $pdo->prepare("SELECT * FROM restaurants WHERE name LIKE ?");
$stmt->execute(["%$q%"]);
$results = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Recherche - FoodConnect</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        :root { --orange: #ff6600; --blue: #007bff; }
        body { background-color: #f8f9fa; }
        .navbar-custom { background: linear-gradient(90deg, var(--blue), var(--orange)); }
        .search-header { background: white; padding: 30px; border-radius: 15px; margin-bottom: 30px; border-bottom: 5px solid var(--blue); }
        .btn-search { background-color: var(--orange); color: white; border: none; }
        .card-resto { border: none; border-radius: 15px; transition: 0.3s; }
        .card-resto:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-custom shadow-sm mb-4">
  <div class="container">
    <a class="navbar-brand fw-bold text-white" href="index.php">FoodConnect</a>
    <div class="ms-auto">
        <a href="index.php" class="btn btn-sm btn-light fw-bold text-primary">Retour</a>
    </div>
  </div>
</nav>

<div class="container">
    <div class="search-header shadow-sm">
        <h3 class="fw-bold mb-3">Résultats pour : <span style="color: var(--orange);">"<?= htmlspecialchars($q) ?>"</span></h3>
        <form action="search.php" method="GET" class="d-flex gap-2">
            <input name="q" value="<?= htmlspecialchars($q) ?>" class="form-control form-control-lg" placeholder="Rechercher un autre restaurant...">
            <button class="btn btn-search px-4"><i class="bi bi-search"></i></button>
        </form>
    </div>

    <div class="row g-4">
        <?php if($results): ?>
            <?php foreach($results as $r): ?>
            <div class="col-md-4">
                <div class="card card-resto h-100 shadow-sm">
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="fw-bold mb-0"><?= htmlspecialchars($r['name']) ?></h5>
                            <small class="text-muted">Kairouan, Tunisie</small>
                        </div>
                        <a href="restaurant_view.php?id=<?= $r['id'] ?>" class="btn btn-sm btn-outline-primary">Visiter</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="text-center mt-5">
                <i class="bi bi-search fs-1 text-muted"></i>
                <p class="text-muted mt-3">Aucun restaurant ne correspond à votre recherche.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>