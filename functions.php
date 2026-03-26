<?php
// Protection XSS
function e($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

// Vérifier login
function isLogged(){
    return isset($_SESSION['id']);
}

// Vérifier rôle
function isRole($role){
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}
?>