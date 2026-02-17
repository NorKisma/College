<?php
session_start();
session_unset();
session_destroy();
header('Location: /College/public/login.php');
exit;
?>
