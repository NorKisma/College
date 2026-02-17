<?php
require_once __DIR__ . '/../../../config/db.php';

$class_id = (int)$_GET['class_id'];

$out = [];

// Join class_subjects with subjects to get the subject names for this class
$res = $conn->query("
    SELECT s.id, s.name
    FROM class_subjects cs
    JOIN subjects s ON cs.subject_id = s.id
    WHERE cs.class_id = $class_id
");

while ($r = $res->fetch_assoc()) $out[] = $r;

echo json_encode($out);
