<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) exit;

$teacher_id = (int)$_SESSION['user_id'];
$group_id = (int)($_POST['group_id'] ?? 0);

if (!$group_id) exit;

// Only mark as read messages **sent to this teacher**
$stmt = $conn->prepare("
    UPDATE messages
    SET is_read = 1, read_at = NOW()
    WHERE message_group_id = ? AND recipient_type='teacher' AND recipient_id=?
");
$stmt->bind_param("ii", $group_id, $teacher_id);
$stmt->execute();
