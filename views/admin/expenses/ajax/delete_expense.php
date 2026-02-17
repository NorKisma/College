<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$data = json_decode(file_get_contents('php://input'), true);

$id = intval($data['id']);

// Verify ownership before deleting
$check_stmt = $conn->prepare("SELECT id FROM expenses WHERE id = ? AND recorded_by = ?");
$check_stmt->bind_param("ii", $id, $user_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Expense not found or unauthorized']);
    exit();
}

$stmt = $conn->prepare("DELETE FROM expenses WHERE id = ? AND recorded_by = ?");
$stmt->bind_param("ii", $id, $user_id);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $conn->error]);
}
?>