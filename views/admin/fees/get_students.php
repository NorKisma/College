<?php
require_once __DIR__ . '/../../../config/db.php';
$class_id = (int)$_GET['class_id'];
$students = $conn->query("
    SELECT st.id, st.name
    FROM class_students cs
    JOIN students st ON st.id = cs.student_id
    WHERE cs.class_id = $class_id
    ORDER BY st.name
");

$data = [];
while($s = $students->fetch_assoc()) $data[] = $s;
echo json_encode($data);
