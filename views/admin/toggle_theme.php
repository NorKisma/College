<?php
require_once __DIR__ . '/../../config/db.php';

// Read current theme
$res = $conn->query("SELECT mode FROM theme LIMIT 1");
$row = $res->fetch_assoc();
$current = (int)$row['mode'];

// Toggle theme: 0 -> 1, 1 -> 0
$new = $current === 1 ? 0 : 1;

// Update DB
$conn->query("UPDATE theme SET mode = $new");

// Return new mode
echo $new;
