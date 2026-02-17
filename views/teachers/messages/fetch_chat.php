<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$teacher_id = (int)$_SESSION['user_id'];
$group_id = (int)($_GET['group_id'] ?? 0);

if (!$group_id) exit;

// Fetch messages for this conversation
$stmt = $conn->prepare("
    SELECT 
        m.message_title,
        m.message_body,
        m.sender_type,
        m.sender_id,
        m.created_at,
        CASE
            WHEN m.sender_type='admin' THEN CONCAT(u.first_name,' ',u.last_name)
            WHEN m.sender_type='teacher' THEN CONCAT(t.first_name,' ',t.last_name)
            ELSE 'Unknown'
        END AS sender_name
    FROM messages m
    LEFT JOIN users u ON m.sender_type='admin' AND m.sender_id=u.id
    LEFT JOIN teachers t ON m.sender_type='teacher' AND m.sender_id=t.id
    WHERE m.message_group_id = ?
      AND (m.recipient_type='teacher' AND m.recipient_id=? OR m.sender_type='teacher' AND m.sender_id=?)
    ORDER BY m.created_at ASC
");
$stmt->bind_param("iii", $group_id, $teacher_id, $teacher_id);
$stmt->execute();
$messages = $stmt->get_result();

while ($m = $messages->fetch_assoc()):
?>
<div class="mb-3 d-flex <?= $m['sender_type']=='teacher' && $m['sender_id']==$teacher_id ? 'justify-content-end' : 'justify-content-start' ?>">
    <div class="message-box">
        <strong><?= htmlspecialchars($m['sender_name']) ?></strong>
        <div><?= nl2br(htmlspecialchars($m['message_body'])) ?></div>
        <small class="text-muted d-block text-end">
            <?= date('d M Y, H:i', strtotime($m['created_at'])) ?>
        </small>
    </div>
</div>
<?php endwhile; ?>
