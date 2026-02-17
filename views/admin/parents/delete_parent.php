<?php
require_once __DIR__ . '/../../../config/db.php';
$id = $_GET['id'];
$conn->query("DELETE FROM parents WHERE id=$id");
header("Location: parents.php");
