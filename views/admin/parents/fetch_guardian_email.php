<?php
require_once __DIR__ . '/../../../config/db.php'; 

$phone = $_GET['phone'] ?? '';
if (!$phone) {
    echo json_encode(['success' => false, 'message' => 'Phone required']);
    exit;
}

$stmt = $conn->prepare("SELECT guardian_email FROM students WHERE guardian_phone=? LIMIT 1");
$stmt->bind_param("s", $phone);
$stmt->execute();
$res = $stmt->get_result()->fetch_assoc();

if ($res) {
    echo json_encode(['success' => true, 'email' => $res['guardian_email']]);
} else {
    echo json_encode(['success' => false, 'message' => 'No student found with this phone']);
}
?>
