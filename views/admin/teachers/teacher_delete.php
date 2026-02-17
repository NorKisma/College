<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// 🔐 Optional: only admins can delete
// if ($_SESSION['role'] !== 'admin') {
//     die('Unauthorized access.');
// }

// Validate ID
$teacher_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($teacher_id <= 0) {
    die("Invalid teacher ID.");
}

// 🔍 Check if teacher exists
$check = $conn->prepare("SELECT first_name, last_name FROM teachers WHERE id = ?");
$check->bind_param("i", $teacher_id);
$check->execute();
$res = $check->get_result();

if ($res->num_rows === 0) {
    $check->close();
    die("Teacher not found.");
}

$teacher = $res->fetch_assoc();
$check->close();

// 🗑️ DELETE teacher
$delete = $conn->prepare("DELETE FROM teachers WHERE id = ?");
$delete->bind_param("i", $teacher_id);

if ($delete->execute()) {
    $delete->close();

    // ✅ Redirect back with success flag
    header("Location: teachers.php?deleted=1");
    exit;
} else {
    $delete->close();
    die("Failed to delete teacher.");
}
