<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

header('Content-Type: application/json');

/* ---------- AUTH ---------- */
$user_id = $_SESSION['user_id'] ?? null;
$role    = $_SESSION['role'] ?? null;

if (!$user_id || $role !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

/* ---------- INPUT ---------- */
$data = json_decode(file_get_contents('php://input'), true);

$date        = $data['date'] ?? null;
$class_id    = (int)($data['class_id'] ?? 0);
$subject_id  = (int)($data['subject_id'] ?? 0);
$recorded_by = $data['recorded_by'] ?? null;

if (!$date || !$class_id || !$subject_id || !$recorded_by) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit;
}

/* ---------- GET HUMAN-READABLE NAMES ---------- */
$class_name = $conn->query("SELECT name FROM classes WHERE id = $class_id")->fetch_assoc()['name'] ?? "Unknown Class";
$subject_name = $conn->query("SELECT name FROM subjects WHERE id = $subject_id")->fetch_assoc()['name'] ?? "Unknown Subject";

/* ---------- DELETE ---------- */
$deleteStmt = $conn->prepare("
    DELETE FROM attendance
    WHERE date = ?
      AND class_id = ?
      AND subject_id = ?
      AND recorded_by = ?
");
$deleteStmt->bind_param('siis', $date, $class_id, $subject_id, $recorded_by);

if ($deleteStmt->execute()) {

    /* ---------- AUDIT LOG (HUMAN-READABLE ONLY) ---------- */
    $description = sprintf(
        "Deleted attendance session for class %s, subject %s by %s on %s",
        $class_name,
        $subject_name,
        $recorded_by,
        $date
    );

    log_audit(
        $conn,
        $user_id,
        'ATTENDANCE_DELETE',
        $description
    );

    echo json_encode([
        'success' => true
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Delete failed'
    ]);
}
?>
