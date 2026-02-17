<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'];
$parent_phone = $_POST['parent_phone'];

// Get messages sent to this parent (by phone) and to all parents
$query = $conn->prepare("
    SELECT 
        m.*,
        u.first_name as sender_name,
        'admin' as sender_type
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.id
    WHERE m.recipient_type = 'parent' 
    AND (m.recipient_id IN (SELECT id FROM parents WHERE phone = ?) OR m.recipient_id IS NULL)
    ORDER BY m.created_at DESC
    LIMIT 50
");
$query->bind_param("s", $parent_phone);
$query->execute();
$result = $query->get_result();

$messages = [];
while ($message = $result->fetch_assoc()) {
    $messages[] = $message;
}
?>

<div class="messages-list">
    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $message): ?>
            <div class="message-item <?php echo $message['is_read'] ? '' : 'message-unread'; ?> p-3 mb-3 rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <i class="bi bi-envelope-fill"></i>
                            <?php echo htmlspecialchars($message['message_title']); ?>
                        </h6>
                        <small class="text-muted">
                            Ka socota: <?php echo htmlspecialchars($message['sender_name']); ?> 
                            (<?php echo ucfirst($message['sender_type']); ?>)
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?>
                            <?php if (!$message['is_read']): ?>
                                <span class="badge bg-danger ms-2">Cusub</span>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="message-body">
                    <?php echo nl2br(htmlspecialchars($message['message_body'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Waxfariin ah lama soo dirin...
        </div>
    <?php endif; ?>
</div><?php

$child_id = $_POST['child_id'];
$parent_phone = $_POST['parent_phone'];

// Get messages sent to this parent (by phone) and to all parents
$query = $conn->prepare("
    SELECT 
        m.*,
        u.first_name as sender_name,
        'admin' as sender_type
    FROM messages m
    LEFT JOIN users u ON m.sender_id = u.id
    WHERE m.recipient_type = 'parent' 
    AND (m.recipient_id IN (SELECT id FROM parents WHERE phone = ?) OR m.recipient_id IS NULL)
    ORDER BY m.created_at DESC
    LIMIT 50
");
$query->bind_param("s", $parent_phone);
$query->execute();
$result = $query->get_result();

$messages = [];
while ($message = $result->fetch_assoc()) {
    $messages[] = $message;
}
?>

<div class="messages-list">
    <?php if (count($messages) > 0): ?>
        <?php foreach ($messages as $message): ?>
            <div class="message-item <?php echo $message['is_read'] ? '' : 'message-unread'; ?> p-3 mb-3 rounded">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h6 class="mb-1">
                            <i class="bi bi-envelope-fill"></i>
                            <?php echo htmlspecialchars($message['message_title']); ?>
                        </h6>
                        <small class="text-muted">
                            Ka socota: <?php echo htmlspecialchars($message['sender_name']); ?> 
                            (<?php echo ucfirst($message['sender_type']); ?>)
                        </small>
                    </div>
                    <div class="text-end">
                        <small class="text-muted">
                            <?php echo date('M d, Y h:i A', strtotime($message['created_at'])); ?>
                            <?php if (!$message['is_read']): ?>
                                <span class="badge bg-danger ms-2">Cusub</span>
                            <?php endif; ?>
                        </small>
                    </div>
                </div>
                <div class="message-body">
                    <?php echo nl2br(htmlspecialchars($message['message_body'])); ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> Wax fariin ah lama soo dirin...
        </div>
    <?php endif; ?>
</div>