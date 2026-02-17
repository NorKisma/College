<?php
require_once __DIR__ . '/../../../config/db.php';

$student_id = (int)($_GET['student_id'] ?? 0);
$fee_category_id = (int)($_GET['fee_category_id'] ?? 0);

if (!$student_id || !$fee_category_id) {
    echo json_encode([]);
    exit;
}

$stmt = $conn->prepare("
    SELECT discount_percentage, discount_amount, reason 
    FROM student_discounts
    WHERE student_id = ? AND fee_category_id = ? AND active = 1
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->bind_param("ii", $student_id, $fee_category_id);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    echo json_encode($row);
} else {
    echo json_encode([]);
}
$stmt->close();
?>
