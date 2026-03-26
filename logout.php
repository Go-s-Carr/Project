<?php
session_start();

// حذف جميع بيانات session
$_SESSION = [];

// تدمير session
session_destroy();

// (اختياري) حذف cookie متاع session
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// إعادة التوجيه لصفحة login
header("Location: login.php");
exit;