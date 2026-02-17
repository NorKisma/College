<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

// 1️⃣ Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];

// 2️⃣ Check POST data
if (!isset($_POST['class_id'], $_POST['note']) || empty(trim($_POST['note']))) {
    $_SESSION['error'] = "Note cannot be empty.";
    header("Location: my_classes.php");
    exit;
}

$class_id = (int)$_POST['class_id'];
$note = trim($_POST['note']);

// 3️⃣ Insert note into database
$stmt = $conn->prepare("
    INSERT INTO class_notes (class_id, teacher_id, note)
    VALUES (?, ?, ?)
");

$stmt->bind_param("iis", $class_id, $teacher_id, $note);

if ($stmt->execute()) {
    $_SESSION['success'] = "Note saved successfully!";
} else {
    $_SESSION['error'] = "Failed to save note: " . $conn->error;
}

$stmt->close();

// 4️⃣ Redirect back to classes page
header("Location: classes.php");
exit;
