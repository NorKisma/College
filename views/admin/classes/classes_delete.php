<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// Authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) { header('Location: ../../auth/login.php'); exit; }

$class_id = intval($_GET['id']);
if ($class_id <= 0) die("Invalid class ID.");

// Optional: delete other child records (subjects, teachers, students)
$conn->query("DELETE FROM class_subjects WHERE class_id = $class_id");
$conn->query("DELETE FROM class_teachers WHERE class_id = $class_id");
$conn->query("DELETE FROM class_students WHERE class_id = $class_id");

// Delete the class itself
$stmt = $conn->prepare("DELETE FROM classes WHERE id=?");
$stmt->bind_param('i', $class_id);

if ($stmt->execute()) {
    header('Location: classes.php?success=Class+deleted+successfully');
    exit;
} else {
    die("Error deleting class: " . $conn->error);
}
?>
