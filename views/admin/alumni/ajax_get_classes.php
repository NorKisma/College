<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (empty($_SESSION['user_id'])) {
    exit(json_encode([]));
}

$section_id = isset($_GET['section']) ? intval($_GET['section']) : 0;

if ($section_id) {
    $query = $conn->prepare("SELECT id, name FROM classes WHERE section_id = ? ORDER BY name");
    $query->bind_param("i", $section_id);
    $query->execute();
    $result = $query->get_result();
    
    $classes = [];
    while ($row = $result->fetch_assoc()) {
        $classes[] = $row;
    }
    
    echo json_encode($classes);
} else {
    echo json_encode([]);
}
?>