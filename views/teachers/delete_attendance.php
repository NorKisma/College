<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$class_id = (int)($_GET['class_id'] ?? 0);
$subject_id = (int)($_GET['subject_id'] ?? 0);
$date = $_GET['date'] ?? '';

if ($class_id <= 0 || $subject_id <= 0 || !$date) {
    die("Invalid parameters.");
}

// Optional: Check if the teacher actually has this subject in this class
$check = $conn->query("
    SELECT 1 
    FROM class_subjects 
    WHERE class_id=$class_id AND subject_id=$subject_id AND teacher_id=$teacher_id 
    LIMIT 1
");
if (!$check || $check->num_rows == 0) {
    die("You are not authorized to delete attendance for this subject/class.");
}

// Make sure date is in correct format
$date = date('Y-m-d', strtotime($date));

// Get record ID
$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) die("Invalid record.");

// Prepare delete
$stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $id);

// Execute
if ($stmt->execute()) {
    $message = "Attendance record successfully deleted.";
} else {
    $message = "Failed to delete attendance: " . $stmt->error;
}

// Redirect back
header("Location: attendance_history.php?class_id=$class_id&msg=" . urlencode($message));
exit;
?>
