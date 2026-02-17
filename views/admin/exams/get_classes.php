<?php
require_once __DIR__ . '/../../../config/db.php';

$section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

if ($section_id > 0) {
    // Only return classes without any alumni
    $res = $conn->query("
        SELECT c.id, c.name
        FROM classes c
        LEFT JOIN alumni a ON a.class_id = c.id
        WHERE c.section_id = $section_id
        GROUP BY c.id
        HAVING COUNT(a.student_id) = 0
        ORDER BY c.name
    ");

    $out = [];
    while ($r = $res->fetch_assoc()) $out[] = $r;

    echo json_encode($out);
} else {
    echo json_encode([]);
}
