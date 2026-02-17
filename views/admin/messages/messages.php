<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Sent Messages Management | Communications',
        'header_title' => 'Sent Messages Management',
        'header_subtitle' => 'View, manage, and export sent communications',
        
        // Action Buttons
        'action_new_message' => 'New Message',
        'action_export_selected' => 'Export Selected',
        'action_delete_selected' => 'Delete Selected',
        
        // Table Headers
        'table_title' => 'Sent Message Groups',
        'table_select_all' => 'Select All',
        'table_message_details' => 'Message Details',
        'table_recipients' => 'Recipients',
        'table_recipient_type' => 'Recipient Type',
        'table_sent_date' => 'Sent Date',
        'table_actions' => 'Actions',
        
        // Stats & Info
        'stats_showing' => 'Showing latest 100 message groups',
        'recipients_count' => 'recipients',
        'read_count' => 'read',
        'unread_count' => 'unread',
        
        // Recipient Types
        'recipient_teachers' => 'Teachers',
        'recipient_parents' => 'Parents',
        'recipient_unknown' => 'Unknown',
        
        // Action Buttons in Table
        'action_view' => 'View',
        'action_delete' => 'Delete',
        
        // Success Messages
        'success_group_deleted' => 'Message group deleted successfully.',
        'success_bulk_deleted' => 'message group(s) deleted successfully.',
        
        // Confirmation Messages
        'confirm_bulk_delete' => 'Delete {count} selected message group(s)? This action cannot be undone.',
        'confirm_single_delete' => 'Are you sure you want to delete this message group? This will delete the message for all recipients.',
        'confirm_select_one' => 'Please select at least one message group to delete.',
        'confirm_select_one_export' => 'Please select at least one message group to export.',
        
        // Empty State
        'empty_title' => 'No sent messages found',
        'empty_message' => 'Start by sending your first message to teachers or parents.',
        'empty_action' => 'Create New Message',
        
        // Modal
        'modal_title' => 'Message Preview',
        
        // CSV Export Headers
        'csv_group_id' => 'Group ID',
        'csv_title' => 'Title',
        'csv_content' => 'Content',
        'csv_recipient_type' => 'Recipient Type',
        'csv_total_recipients' => 'Total Recipients',
        'csv_recipients_list' => 'Recipients List',
        'csv_sent_date' => 'Sent Date',
        
        // File Name
        'export_filename' => 'sent_messages_',
        
        // Login Redirect
        'login_required' => 'Login required',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة الرسائل المرسلة | الاتصالات',
        'header_title' => 'إدارة الرسائل المرسلة',
        'header_subtitle' => 'عرض وإدارة وتصدير الاتصالات المرسلة',
        
        // Action Buttons
        'action_new_message' => 'رسالة جديدة',
        'action_export_selected' => 'تصدير المحدد',
        'action_delete_selected' => 'حذف المحدد',
        
        // Table Headers
        'table_title' => 'مجموعات الرسائل المرسلة',
        'table_select_all' => 'تحديد الكل',
        'table_message_details' => 'تفاصيل الرسالة',
        'table_recipients' => 'المستلمين',
        'table_recipient_type' => 'نوع المستلم',
        'table_sent_date' => 'تاريخ الإرسال',
        'table_actions' => 'الإجراءات',
        
        // Stats & Info
        'stats_showing' => 'عرض أحدث 100 مجموعة رسائل',
        'recipients_count' => 'مستلم',
        'read_count' => 'مقروء',
        'unread_count' => 'غير مقروء',
        
        // Recipient Types
        'recipient_teachers' => 'المعلمين',
        'recipient_parents' => 'أولياء الأمور',
        'recipient_unknown' => 'غير معروف',
        
        // Action Buttons in Table
        'action_view' => 'عرض',
        'action_delete' => 'حذف',
        
        // Success Messages
        'success_group_deleted' => 'تم حذف مجموعة الرسائل بنجاح.',
        'success_bulk_deleted' => 'مجموعة رسائل تم حذفها بنجاح.',
        
        // Confirmation Messages
        'confirm_bulk_delete' => 'حذف {count} مجموعة رسائل محددة؟ لا يمكن التراجع عن هذا الإجراء.',
        'confirm_single_delete' => 'هل أنت متأكد أنك تريد حذف مجموعة الرسائل هذه؟ سيؤدي هذا إلى حذف الرسالة لجميع المستلمين.',
        'confirm_select_one' => 'الرجاء تحديد مجموعة رسائل واحدة على الأقل للحذف.',
        'confirm_select_one_export' => 'الرجاء تحديد مجموعة رسائل واحدة على الأقل للتصدير.',
        
        // Empty State
        'empty_title' => 'لم يتم العثور على رسائل مرسلة',
        'empty_message' => 'ابدأ بإرسال رسالتك الأولى إلى المعلمين أو أولياء الأمور.',
        'empty_action' => 'إنشاء رسالة جديدة',
        
        // Modal
        'modal_title' => 'معاينة الرسالة',
        
        // CSV Export Headers
        'csv_group_id' => 'معرف المجموعة',
        'csv_title' => 'العنوان',
        'csv_content' => 'المحتوى',
        'csv_recipient_type' => 'نوع المستلم',
        'csv_total_recipients' => 'إجمالي المستلمين',
        'csv_recipients_list' => 'قائمة المستلمين',
        'csv_sent_date' => 'تاريخ الإرسال',
        
        // File Name
        'export_filename' => 'الرسائل_المرسلة_',
        
        // Login Redirect
        'login_required' => 'مطلوب تسجيل الدخول',
    ]
];

// Translation helper function with string replacement
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

// Simple auth guard



// Theme configuration
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0);

// Theme colors
$primaryColor = $is_dark ? '#3b82f6' : '#2563eb';
$successColor = $is_dark ? '#10b981' : '#059669';
$dangerColor = $is_dark ? '#ef4444' : '#dc2626';
$warningColor = $is_dark ? '#f59e0b' : '#d97706';
$infoColor = $is_dark ? '#0ea5e9' : '#0284c7';
$bgColor = $is_dark ? '#0f172a' : '#f8fafc';
$cardBg = $is_dark ? '#1e293b' : '#ffffff';
$textColor = $is_dark ? '#f1f5f9' : '#1e293b';
$borderColor = $is_dark ? '#334155' : '#e2e8f0';
$mutedColor = $is_dark ? '#94a3b8' : '#64748b';

// Handle group deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_POST['action'] === 'delete_group' && !empty($_POST['group_id'])) {
    $gid = (int)$_POST['group_id'];
    $stmt = $conn->prepare("DELETE FROM messages WHERE message_group_id = ?");
    $stmt->bind_param('i', $gid);
    $stmt->execute();
    $stmt->close();
    
    $_SESSION['success'] = t('success_group_deleted');
    header('Location: messages.php');
    exit;
}

// Handle bulk delete
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'bulk_delete' && !empty($_POST['group_ids'])) {
    $groupIds = array_map('intval', $_POST['group_ids']);
    
    if (!empty($groupIds)) {
        $placeholders = implode(',', array_fill(0, count($groupIds), '?'));
        $types = str_repeat('i', count($groupIds));
        
        $sql = "DELETE FROM messages WHERE message_group_id IN ($placeholders)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$groupIds);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['success'] = count($groupIds) . ' ' . t('success_bulk_deleted');
    }
    
    header('Location: messages.php');
    exit;
}

// Handle CSV export
if ((isset($_GET['action']) && $_GET['action'] === 'export') || (isset($_POST['action']) && $_POST['action'] === 'export')) {
    $selected = [];
    if (!empty($_POST['group_ids'])) $selected = array_map('intval', (array)$_POST['group_ids']);
    if (!empty($_GET['group_ids'])) $selected = array_map('intval', explode(',', $_GET['group_ids']));
    
    $sql = "
        SELECT 
            m.message_group_id,
            m.message_title,
            m.message_body,
            m.recipient_type,
            COUNT(*) as total_recipients,
            MIN(m.created_at) as sent_date,
            GROUP_CONCAT(
                CASE 
                    WHEN m.recipient_type = 'teacher' THEN CONCAT(t.first_name, ' ', t.last_name)
                    WHEN m.recipient_type = 'parent' THEN p.name
                    ELSE '" . t('recipient_unknown') . "'
                END SEPARATOR '; '
            ) as recipients_list
        FROM messages m
        LEFT JOIN teachers t ON m.recipient_type = 'teacher' AND m.recipient_id = t.id
        LEFT JOIN parents p ON m.recipient_type = 'parent' AND m.recipient_id = p.id
        WHERE m.sender_type = 'admin'
    ";
    
    if (!empty($selected)) {
        $place = implode(',', array_fill(0, count($selected), '?'));
        $sql .= " AND m.message_group_id IN ($place)";
    }
    
    $sql .= " GROUP BY m.message_group_id ORDER BY sent_date DESC";
    
    $stmt = $conn->prepare($sql);
    if (!empty($selected)) {
        $types = str_repeat('i', count($selected));
        $stmt->bind_param($types, ...$selected);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . t('export_filename') . date('Ymd_His') . '.csv"');
    
    $out = fopen('php://output', 'w');
    // Add UTF-8 BOM for Arabic compatibility
    if ($currentLangId == 2) {
        fwrite($out, "\xEF\xBB\xBF");
    }
    
    fputcsv($out, [
        t('csv_group_id'),
        t('csv_title'),
        t('csv_content'),
        t('csv_recipient_type'),
        t('csv_total_recipients'),
        t('csv_recipients_list'),
        t('csv_sent_date')
    ]);
    
    while ($row = $res->fetch_assoc()) {
        fputcsv($out, [
            $row['message_group_id'],
            $row['message_title'],
            $row['message_body'],
            $row['recipient_type'],
            $row['total_recipients'],
            $row['recipients_list'],
            $row['sent_date']
        ]);
    }
    
    fclose($out);
    exit;
}

// Fetch messages list
$sql = "
    SELECT
        m.message_group_id,
        m.message_title,
        m.message_body,
        m.recipient_type,
        MIN(m.created_at) AS created_at,
        COUNT(*) AS total_recipients,
        SUM(m.is_read = 1) AS total_read,
        SUM(m.is_read = 0) AS total_unread
    FROM messages m
    WHERE m.sender_type = 'admin'
    GROUP BY m.message_group_id, m.message_title, m.message_body, m.recipient_type
    ORDER BY created_at DESC
    LIMIT 100
";
$res = $conn->query($sql);

// Check for success messages
$successMsg = '';
if (isset($_SESSION['success'])) {
    $successMsg = $_SESSION['success'];
    unset($_SESSION['success']);
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <div class="page-title">
                    <h1><i class="fas fa-paper-plane"></i> <?= t('header_title') ?></h1>
                    <p><?= t('header_subtitle') ?></p>
                </div>
                <div class="header-actions">
                    <a href="send_messages.php" class="btn btn-primary">
                        <i class="fas fa-plus"></i> <?= t('action_new_message') ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <?php if ($successMsg): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($successMsg) ?>
            </div>
        <?php endif; ?>

        <!-- Action Bar -->
        <div class="action-bar">
            <div class="action-group">
                <button class="btn btn-success" id="exportBtn">
                    <i class="fas fa-file-export"></i> <?= t('action_export_selected') ?>
                </button>
                <button class="btn btn-danger" id="bulkDeleteBtn">
                    <i class="fas fa-trash"></i> <?= t('action_delete_selected') ?>
                </button>
            </div>
            <div class="stats-badge">
                <i class="fas fa-info-circle"></i>
                <?= t('stats_showing') ?>
            </div>
        </div>

        <!-- Data Table -->
        <section class="data-table-container">
            <div class="table-header">
                <h2><i class="fas fa-inbox"></i> <?= t('table_title') ?></h2>
                <div class="table-controls">
                    <label style="display: flex; align-items: center; gap: 8px; font-size: 13px;">
                        <input type="checkbox" id="selectAll" class="checkbox">
                        <?= t('table_select_all') ?>
                    </label>
                </div>
            </div>

            <form id="messagesForm" method="POST">
                <input type="hidden" name="action" id="formAction">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th class="checkbox-cell"></th>
                                <th><?= t('table_message_details') ?></th>
                                <th><?= t('table_recipients') ?></th>
                                <th><?= t('table_recipient_type') ?></th>
                                <th><?= t('table_sent_date') ?></th>
                                <th><?= t('table_actions') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($res && $res->num_rows > 0): ?>
                                <?php while ($row = $res->fetch_assoc()): ?>
                                    <tr>
                                        <td class="checkbox-cell">
                                            <input type="checkbox" name="group_ids[]" value="<?= $row['message_group_id'] ?>" class="checkbox">
                                        </td>
                                        <td>
                                            <div class="message-preview">
                                                <div class="message-title">
                                                    <?= htmlspecialchars($row['message_title']) ?>
                                                </div>
                                                <div class="message-excerpt" title="<?= htmlspecialchars($row['message_body']) ?>">
                                                    <?= htmlspecialchars(mb_substr($row['message_body'], 0, 100)) ?><?= mb_strlen($row['message_body']) > 100 ? '...' : '' ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="recipient-stats">
                                                <div class="stat-value"><?= $row['total_recipients'] ?> <?= t('recipients_count') ?></div>
                                                <div class="stat-label read-stat">
                                                    <i class="fas fa-check-circle"></i>
                                                    <?= $row['total_read'] ?> <?= t('read_count') ?>
                                                </div>
                                                <div class="stat-label unread-stat">
                                                    <i class="fas fa-clock"></i>
                                                    <?= $row['total_unread'] ?> <?= t('unread_count') ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="recipient-badge <?= $row['recipient_type'] ?>">
                                                <i class="fas fa-<?= $row['recipient_type'] === 'teacher' ? 'chalkboard-teacher' : 'user-friends' ?>"></i>
                                                <?= $row['recipient_type'] === 'teacher' ? t('recipient_teachers') : t('recipient_parents') ?>
                                            </span>
                                        </td>
                                        <td class="date-cell">
                                            <?= date('M d, Y \a\t h:i A', strtotime($row['created_at'])) ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="message_view.php?group_id=<?= $row['message_group_id'] ?>" 
                                                   class="action-btn view">
                                                    <i class="fas fa-eye"></i> <?= t('action_view') ?>
                                                </a>
                                                <button type="button" 
                                                        class="action-btn delete"
                                                        onclick="confirmDelete(<?= $row['message_group_id'] ?>)">
                                                    <i class="fas fa-trash"></i> <?= t('action_delete') ?>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6">
                                        <div class="empty-state">
                                            <i class="fas fa-inbox"></i>
                                            <h3><?= t('empty_title') ?></h3>
                                            <p><?= t('empty_message') ?></p>
                                            <a href="send_messages.php" class="btn btn-primary" style="margin-top: 16px;">
                                                <i class="fas fa-plus"></i> <?= t('empty_action') ?>
                                            </a>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </form>
        </section>
    </main>

    <!-- Modal for Message Preview -->
    <div id="messageModal" class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <h3 id="modalTitle"><?= t('modal_title') ?></h3>
                <button class="modal-close" onclick="closeModal()">×</button>
            </div>
            <div class="modal-body" id="modalContent"></div>
        </div>
    </div>

    <script>
        // Translations for JavaScript
        const translations = {
            confirm_bulk_delete: "<?= t('confirm_bulk_delete') ?>",
            confirm_single_delete: "<?= t('confirm_single_delete') ?>",
            confirm_select_one: "<?= t('confirm_select_one') ?>",
            confirm_select_one_export: "<?= t('confirm_select_one_export') ?>"
        };

        // Select All functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('input[name="group_ids[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Bulk Delete
        document.getElementById('bulkDeleteBtn').addEventListener('click', function() {
            const selected = document.querySelectorAll('input[name="group_ids[]"]:checked');
            
            if (selected.length === 0) {
                alert(translations.confirm_select_one);
                return;
            }
            
            const message = translations.confirm_bulk_delete.replace('{count}', selected.length);
            
            if (confirm(message)) {
                document.getElementById('formAction').value = 'bulk_delete';
                document.getElementById('messagesForm').submit();
            }
        });

        // Export Selected
        document.getElementById('exportBtn').addEventListener('click', function() {
            const selected = document.querySelectorAll('input[name="group_ids[]"]:checked');
            
            if (selected.length === 0) {
                alert(translations.confirm_select_one_export);
                return;
            }
            
            const groupIds = Array.from(selected).map(cb => cb.value).join(',');
            window.location.href = `messages.php?action=export&group_ids=${groupIds}`;
        });

        // Individual Delete
        function confirmDelete(groupId) {
            if (confirm(translations.confirm_single_delete)) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = 'messages.php';
                form.innerHTML = `
                    <input type="hidden" name="action" value="delete_group">
                    <input type="hidden" name="group_id" value="${groupId}">
                `;
                document.body.appendChild(form);
                form.submit();
            }
        }

        // Modal Functions
        function showMessageModal(title, content) {
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('modalContent').textContent = content;
            document.getElementById('messageModal').style.display = 'flex';
        }

        function closeModal() {
            document.getElementById('messageModal').style.display = 'none';
        }

        // Close modal on ESC
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeModal();
        });

        // Close modal on overlay click
        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) closeModal();
        });

        // Show message preview on row click
        document.querySelectorAll('.message-preview').forEach(preview => {
            preview.addEventListener('click', function(e) {
                if (e.target.tagName === 'A' || e.target.tagName === 'BUTTON') return;
                
                const title = this.querySelector('.message-title').textContent;
                const excerpt = this.querySelector('.message-excerpt').getAttribute('title');
                showMessageModal(title, excerpt);
            });
        });
    </script>

   <style>
        /* Professional Corporate Design System */
        :root {
            --primary: <?= $primaryColor ?>;
            --primary-hover: <?= $is_dark ? '#2563eb' : '#1d4ed8' ?>;
            --success: <?= $successColor ?>;
            --warning: <?= $warningColor ?>;
            --danger: <?= $dangerColor ?>;
            --info: <?= $infoColor ?>;
            --bg-primary: <?= $bgColor ?>;
            --bg-card: <?= $cardBg ?>;
            --text-primary: <?= $textColor ?>;
            --text-secondary: <?= $mutedColor ?>;
            --border-color: <?= $borderColor ?>;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .app-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            padding: 24px 0;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title p {
            opacity: 0.9;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        /* Alert Messages */
        .alert {
            padding: 16px;
            border-radius: 8px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideDown 0.3s ease;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: var(--success);
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main Content */
        .main-content {
            margin-bottom: 48px;
        }

        /* Action Bar */
        .action-bar {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 16px;
        }

        .action-group {
            display: flex;
            gap: 12px;
            align-items: center;
            flex-wrap: wrap;
        }

        .stats-badge {
            background: var(--bg-primary);
            border: 1px solid var(--border-color);
            padding: 8px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--bg-primary);
        }

        .btn-danger {
            background: var(--danger);
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        /* Data Table */
        .data-table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-controls {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .data-table thead {
            background: linear-gradient(180deg, var(--bg-card), rgba(59, 130, 246, 0.05));
        }

        .data-table th {
            padding: 16px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 14px;
            vertical-align: top;
        }

        .data-table tbody tr {
            transition: all 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Checkbox */
        .checkbox-cell {
            width: 50px;
            text-align: center;
        }

        .checkbox {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            border: 2px solid var(--border-color);
            background: var(--bg-card);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .checkbox:checked {
            background: var(--primary);
            border-color: var(--primary);
        }

        /* Message Preview */
        .message-preview {
            max-width: 400px;
        }

        .message-title {
            font-weight: 600;
            color: var(--text-primary);
            margin-bottom: 8px;
            line-height: 1.4;
        }

        .message-excerpt {
            color: var(--text-secondary);
            font-size: 13px;
            line-height: 1.5;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        /* Recipient Stats */
        .recipient-stats {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .stat-value {
            font-weight: 600;
            color: var(--text-primary);
        }

        .stat-label {
            font-size: 12px;
            color: var(--text-secondary);
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .read-stat {
            color: var(--success);
        }

        .unread-stat {
            color: var(--warning);
        }

        /* Recipient Type Badge */
        .recipient-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .recipient-badge.teachers {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border-color: rgba(16, 185, 129, 0.2);
        }

        .recipient-badge.parents {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border-color: rgba(245, 158, 11, 0.2);
        }

        /* Date Cell */
        .date-cell {
            white-space: nowrap;
            color: var(--text-secondary);
            font-size: 13px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 13px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            text-decoration: none;
        }

        .action-btn.view {
            background: rgba(59, 130, 246, 0.1);
            color: var(--primary);
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .action-btn.view:hover {
            background: rgba(59, 130, 246, 0.2);
        }

        .action-btn.delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        .action-btn.delete:hover {
            background: rgba(239, 68, 68, 0.2);
        }

        /* Empty State */
        .empty-state {
            padding: 64px 24px;
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--border-color);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container {
                padding: 0 16px;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .action-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .action-group {
                justify-content: center;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
        }

        @media (max-width: 480px) {
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-btn {
                width: 100%;
                justify-content: center;
            }
        }

        /* Modal */
        .modal-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0, 0, 0, 0.5);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            backdrop-filter: blur(4px);
        }

        .modal {
            background: var(--bg-card);
            width: 600px;
            max-width: 90%;
            border-radius: 12px;
            padding: 24px;
            box-shadow: var(--shadow-xl);
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .modal-close {
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 20px;
            padding: 4px;
        }

        .modal-body {
            color: var(--text-primary);
            line-height: 1.6;
            white-space: pre-wrap;
        }
    </style>
</body>
</html>
