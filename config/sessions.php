<?php
session_start();

// Avoid redirect loop if we are already on login.php
if (!isset($_SESSION['user_id']) && basename($_SERVER['PHP_SELF']) != 'login.php') {
    header('Location: /College/public/login.php');
    exit;
}
?>