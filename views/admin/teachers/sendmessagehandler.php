<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if($_SERVER['REQUEST_METHOD'] !== 'POST'){
    die("Invalid request");
}

$teacher_id = (int)$_POST['teacher_id'];
$to_email   = trim($_POST['to_email']);
$subject    = trim($_POST['subject']);
$message    = trim($_POST['message']);
$sender_id  = $_SESSION['user_id'];

/* ===== SAVE MESSAGE TO DATABASE ===== */
$stmt = $conn->prepare("
    INSERT INTO messages (sender_id, teacher_id, to_email, subject, message)
    VALUES (?, ?, ?, ?, ?)
");
$stmt->bind_param("iisss", $sender_id, $teacher_id, $to_email, $subject, $message);
$stmt->execute();

/* ===== OPTIONAL: SEND REAL EMAIL (IF MAIL IS CONFIGURED) ===== */
mail($to_email, $subject, $message);

/* ===== REDIRECT BACK ===== */
header("Location: teacher_view.php?id=$teacher_id&msg=sent");
exit;
?>
