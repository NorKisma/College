<?php

require_once __DIR__ . '/../../../config/db.php';

$year = trim($_GET['year'] ?? '');

if (!$year) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT DISTINCT
        c.id,
        c.name,
        s.name AS section
    FROM class_students cs
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN sections s ON s.id = c.section_id
    WHERE cs.academic_year = ?
    ORDER BY c.name
");

$stmt->bind_param("s", $year);
$stmt->execute();

$result = $stmt->get_result();

$data = [];

while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data);
