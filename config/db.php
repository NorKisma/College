



<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$dbname = 'opy';
$port = 3307; // port cusub

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die('Database Connection Failed: ' . $conn->connect_error);
}
?>
