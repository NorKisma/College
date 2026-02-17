<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user_id'];

// Fetch conversation list
$stmt = $conn->prepare("
    SELECT 
        m.message_group_id,
        MAX(m.created_at) AS last_time,
        MAX(m.message_title) AS title,
        SUBSTRING_INDEX(MAX(CONCAT(m.created_at,'||',m.message_body)),'||',-1) AS last_message,
        SUM(CASE WHEN m.is_read = 0 AND m.recipient_id = ? THEN 1 ELSE 0 END) AS unread,
        m.sender_type,
        m.sender_id
    FROM messages m
    WHERE (m.sender_id = ? AND m.sender_type = 'teacher')
       OR (m.recipient_type = 'teacher' AND m.recipient_id = ?)
    GROUP BY m.message_group_id
    ORDER BY last_time DESC
");
$stmt->bind_param("iii", $teacher_id, $teacher_id, $teacher_id);
$stmt->execute();
$conversations = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Helper to get sender name
function getSenderName($conn, $type, $id) {
    if ($type === 'admin') {
        $stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id=? LIMIT 1");
    } else if ($type === 'teacher') {
        $stmt = $conn->prepare("SELECT first_name, last_name FROM teachers WHERE id=? LIMIT 1");
    } else {
        return "Unknown";
    }
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    return $res ? trim($res['first_name'] . ' ' . $res['last_name']) : "Unknown";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages | Teacher Portal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --message-sent: #dcf8c6;
            --message-received: #ffffff;
            --sidebar-bg: #ffffff;
            --chat-bg: #f0f2f5;
            --border-color: #e0e0e0;
            --unread-color: #25d366;
            --online-color: #4CAF50;
            --offline-color: #95a5a6;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        
        .chat-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            height: 90vh;
            display: flex;
            max-width: 1400px;
            margin: 0 auto;
        }
        
        /* Sidebar Styles */
        .chat-sidebar {
            width: 350px;
            background: var(--sidebar-bg);
            border-right: 1px solid var(--border-color);
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
        }
        
        .sidebar-header {
            padding: 25px 20px;
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar-header h4 {
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
        }
        
        .sidebar-header h4 i {
            margin-right: 10px;
        }
        
        .conversation-search {
            padding: 20px;
            background: var(--sidebar-bg);
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box input {
            padding: 12px 20px 12px 45px;
            border-radius: 25px;
            border: 1px solid var(--border-color);
            width: 100%;
            font-size: 0.9rem;
            transition: all 0.3s;
        }
        
        .search-box input:focus {
            outline: none;
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #95a5a6;
        }
        
        .conversations-list {
            flex: 1;
            overflow-y: auto;
            padding: 10px 0;
        }
        
        .conversation-item {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            cursor: pointer;
            transition: all 0.2s;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .conversation-item:hover {
            background: rgba(52, 152, 219, 0.05);
        }
        
        .conversation-item.active {
            background: rgba(52, 152, 219, 0.1);
            border-left: 4px solid var(--secondary-color);
        }
        
        .conversation-avatar {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 1.2rem;
            margin-right: 15px;
            flex-shrink: 0;
        }
        
        .conversation-info {
            flex: 1;
            min-width: 0;
        }
        
        .conversation-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .conversation-preview {
            color: #666;
            font-size: 0.85rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .conversation-time {
            font-size: 0.75rem;
            color: #95a5a6;
            margin-top: 3px;
        }
        
        .unread-badge {
            background: var(--unread-color);
            color: white;
            font-size: 0.75rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: 12px;
            min-width: 20px;
            text-align: center;
            margin-left: 5px;
        }
        
        /* Chat Area Styles */
        .chat-area {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: var(--chat-bg);
        }
        
        .chat-header {
            padding: 20px 30px;
            background: white;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        
        .chat-header-info {
            display: flex;
            align-items: center;
        }
        
        .chat-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-right: 15px;
            font-size: 1.1rem;
        }
        
        .chat-title h5 {
            margin: 0;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .chat-title small {
            color: #95a5a6;
            font-size: 0.85rem;
        }
        
        .chat-actions {
            display: flex;
            gap: 10px;
        }
        
        .chat-actions button {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 1px solid var(--border-color);
            background: white;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }
        
        .chat-actions button:hover {
            background: var(--secondary-color);
            color: white;
            border-color: var(--secondary-color);
        }
        
        .messages-container {
            flex: 1;
            padding: 30px;
            overflow-y: auto;
            background: var(--chat-bg);
            display: flex;
            flex-direction: column;
        }
        
        .empty-state {
            text-align: center;
            color: #95a5a6;
            margin: auto;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #d1d8e0;
        }
        
        .empty-state h4 {
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--primary-color);
        }
        
        .message-wrapper {
            display: flex;
            margin-bottom: 25px;
            max-width: 70%;
        }
        
        .message-wrapper.sent {
            align-self: flex-end;
            flex-direction: row-reverse;
        }
        
        .message-wrapper.received .message-content {
            background: var(--message-received);
            border-radius: 0 18px 18px 18px;
        }
        
        .message-wrapper.sent .message-content {
            background: var(--message-sent);
            border-radius: 18px 0 18px 18px;
        }
        
        .message-avatar {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: linear-gradient(135deg, #95a5a6, #7f8c8d);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 0.9rem;
            margin: 0 10px;
            flex-shrink: 0;
        }
        
        .message-content {
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            position: relative;
        }
        
        .message-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
        }
        
        .message-sender {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 0.95rem;
        }
        
        .message-time {
            font-size: 0.75rem;
            color: #95a5a6;
        }
        
        .message-title {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 8px;
            font-size: 1rem;
        }
        
        .message-body {
            color: #333;
            line-height: 1.5;
            margin: 0;
        }
        
        .typing-indicator {
            display: none;
            padding: 15px;
            background: white;
            border-radius: 18px;
            margin-bottom: 15px;
            align-self: flex-start;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .typing-indicator span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: #ccc;
            margin: 0 2px;
            animation: typing 1.4s infinite;
        }
        
        .typing-indicator span:nth-child(2) { animation-delay: 0.2s; }
        .typing-indicator span:nth-child(3) { animation-delay: 0.4s; }
        
        @keyframes typing {
            0%, 60%, 100% { transform: translateY(0); }
            30% { transform: translateY(-5px); }
        }
        
        /* Responsive Design */
        @media (max-width: 992px) {
            .chat-container {
                height: 95vh;
                border-radius: 10px;
            }
            
            .chat-sidebar {
                width: 300px;
            }
            
            .message-wrapper {
                max-width: 85%;
            }
        }
        
        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .chat-container {
                flex-direction: column;
                height: 95vh;
            }
            
            .chat-sidebar {
                width: 100%;
                height: 40%;
                border-right: none;
                border-bottom: 1px solid var(--border-color);
            }
            
            .chat-area {
                height: 60%;
            }
            
            .message-wrapper {
                max-width: 90%;
            }
        }
        
        /* Scrollbar Styling */
        .conversations-list::-webkit-scrollbar,
        .messages-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .conversations-list::-webkit-scrollbar-track,
        .messages-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        .conversations-list::-webkit-scrollbar-thumb,
        .messages-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 10px;
        }
        
        .conversations-list::-webkit-scrollbar-thumb:hover,
        .messages-container::-webkit-scrollbar-thumb:hover {
            background: #a8a8a8;
        }
        
        /* Animation for new messages */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .message-wrapper {
            animation: fadeIn 0.3s ease-out;
        }
    </style>
</head>
<body>
    <div class="chat-container">
        <!-- Left Sidebar -->
        <div class="chat-sidebar">
            <div class="sidebar-header">
                <h4><i class="fas fa-comments"></i> Messages</h4>
                <p class="mb-0 mt-2 small opacity-75">Your conversations and messages</p>
            </div>
            
            <div class="conversation-search">
                <div class="search-box">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="Search conversations..." id="searchConversations">
                </div>
            </div>
            
            <div class="conversations-list">
                <?php if (empty($conversations)): ?>
                    <div class="text-center py-5">
                        <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                        <p class="text-muted">No conversations yet</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($conversations as $c): 
                        $senderName = getSenderName($conn, $c['sender_type'], $c['sender_id']);
                        $firstLetter = strtoupper($senderName[0] ?? 'U');
                        $lastTime = date('M j, g:i A', strtotime($c['last_time']));
                    ?>
                    <div class="conversation-item" 
                         data-id="<?= $c['message_group_id'] ?>" 
                         data-title="<?= htmlspecialchars($c['title']) ?>"
                         data-sender-name="<?= htmlspecialchars($senderName) ?>">
                        <div class="conversation-avatar">
                            <?= $firstLetter ?>
                        </div>
                        <div class="conversation-info">
                            <div class="conversation-title">
                                <span class="title-text"><?= htmlspecialchars($c['title']) ?></span>
                                <?php if ($c['unread'] > 0): ?>
                                    <span class="unread-badge"><?= $c['unread'] ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="conversation-preview">
                                <?= htmlspecialchars(mb_strimwidth($c['last_message'], 0, 40, '...')) ?>
                            </div>
                            <div class="conversation-time">
                                <?= $lastTime ?>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Right Chat Area -->
        <div class="chat-area">
            <div class="chat-header">
                <div class="chat-header-info">
                    <div class="chat-avatar" id="currentChatAvatar">?</div>
                    <div class="chat-title">
                        <h5 id="currentChatTitle">Select a conversation</h5>
                        <small id="currentChatStatus">Click on a conversation to view messages</small>
                    </div>
                </div>
                <div class="chat-actions">
                    <button type="button" id="refreshChat" title="Refresh">
                        <i class="fas fa-sync-alt"></i>
                    </button> 
                </div>
            </div>
            
            <div class="messages-container" id="messagesContainer">
                <div class="empty-state" id="emptyState">
                    <i class="fas fa-comments"></i>
                    <h4>No conversation selected</h4>
                    <p>Select a conversation from the sidebar to view messages</p>
                </div>
                
                <div class="typing-indicator" id="typingIndicator">
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
                
                <div id="messagesList"></div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            let currentGroupId = null;
            let autoRefreshInterval = null;
            
            // Search conversations
            $('#searchConversations').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.conversation-item').each(function() {
                    const title = $(this).find('.title-text').text().toLowerCase();
                    const preview = $(this).find('.conversation-preview').text().toLowerCase();
                    const matches = title.includes(searchTerm) || preview.includes(searchTerm);
                    $(this).toggle(matches);
                });
            });
            
            // Conversation item click
            $('.conversation-item').on('click', function() {
                $('.conversation-item').removeClass('active');
                $(this).addClass('active');
                
                const groupId = $(this).data('id');
                const title = $(this).data('title');
                const senderName = $(this).data('sender-name');
                const firstLetter = senderName.charAt(0).toUpperCase();
                
                currentGroupId = groupId;
                
                // Update header
                $('#currentChatTitle').text(title);
                $('#currentChatAvatar').text(firstLetter);
                $('#currentChatStatus').text(`Conversation with ${senderName}`);
                
                // Show loading
                $('#emptyState').hide();
                $('#messagesList').empty();
                $('#typingIndicator').show();
                
                // Load messages
                loadMessages(groupId);
                
                // Mark as read
                markAsRead(groupId);
                
                // Remove unread badge
                $(this).find('.unread-badge').remove();
                
                // Start auto-refresh for this conversation
                if (autoRefreshInterval) {
                    clearInterval(autoRefreshInterval);
                }
                autoRefreshInterval = setInterval(() => {
                    if (currentGroupId === groupId) {
                        loadMessages(groupId, false); // silent refresh
                    }
                }, 10000); // Refresh every 10 seconds
            });
            
            // Refresh button
            $('#refreshChat').on('click', function() {
                if (currentGroupId) {
                    loadMessages(currentGroupId);
                }
            });
            
            // New message button
            $('#newMessage').on('click', function() {
                alert('New message functionality would open here');
                // You would typically open a modal or redirect to a new message form
            });
            
            // Function to load messages
            function loadMessages(groupId, showLoading = true) {
                if (showLoading) {
                    $('#typingIndicator').show();
                }
                
                $.get('fetch_chat.php', { group_id: groupId })
                    .done(function(data) {
                        $('#messagesList').html(data);
                        $('#typingIndicator').hide();
                        
                        // Scroll to bottom of messages
                        const container = $('#messagesContainer');
                        container.scrollTop(container[0].scrollHeight);
                        
                        // Add animation to new messages
                        $('#messagesList .message-wrapper').css('animation', 'fadeIn 0.3s ease-out');
                    })
                    .fail(function() {
                        $('#typingIndicator').hide();
                        $('#messagesList').html(`
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                Failed to load messages. Please try again.
                            </div>
                        `);
                    });
            }
            
            // Function to mark messages as read
            function markAsRead(groupId) {
                $.post('mark_read.php', { group_id: groupId })
                    .fail(function() {
                        console.error('Failed to mark messages as read');
                    });
            }
            
            // Auto-refresh conversations list every 30 seconds
            setInterval(() => {
                if (!currentGroupId) {
                    // Refresh conversations list if no chat is open
                    location.reload();
                }
            }, 30000);
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl/Cmd + F to focus search
                if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                    e.preventDefault();
                    $('#searchConversations').focus();
                }
                
                // Escape to clear search
                if (e.key === 'Escape') {
                    $('#searchConversations').val('').trigger('input');
                }
            });
            
            // Smooth scroll for messages
            $('#messagesContainer').on('scroll', function() {
                const container = $(this);
                if (container.scrollTop() === 0 && currentGroupId) {
                    // Load more messages when scrolling to top
                    console.log('Load older messages...');
                }
            });
        });
    </script>
</body>
</html>