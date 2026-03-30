<?php session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">

</head>
<body>

<nav class="navbar navbar-dark bg-dark">
<div class="container">

<a class="navbar-brand" href="../client/index.php">FoodConnect</a>

<div>
<?php if(isset($_SESSION['id'])): ?>
<a href="../client/cart.php" class="btn btn-light">Panier</a>
<a href="../auth/logout.php" class="btn btn-danger">Logout</a>
<?php else: ?>
<a href="../auth/login.php" class="btn btn-success">Login</a>
<?php endif; ?>
</div>

</div>
</nav>

<div class="container mt-4">