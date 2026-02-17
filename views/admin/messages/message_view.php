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
        'page_title' => 'Message Details | View Message',
        'breadcrumb_messages' => 'Messages',
        'breadcrumb_details' => 'Message Details',
        'header_title' => 'Message Details',
        
        // Message Header
        'message_title_placeholder' => 'Enter message title...',
        'message_content_placeholder' => 'Enter your message content here...',
        'message_id' => 'ID',
        
        // Stats Grid
        'stats_total_recipients' => 'Total Recipients',
        'stats_read_messages' => 'Read Messages',
        'stats_unread_messages' => 'Unread Messages',
        'stats_read_rate' => 'Read Rate',
        
        // Message Body Section
        'section_message_content' => 'Message Content',
        
        // Recipients Section
        'section_recipients' => 'Recipients',
        'table_recipient' => 'Recipient',
        'table_type' => 'Type',
        'table_status' => 'Status',
        'table_read_at' => 'Read At',
        'table_actions' => 'Actions',
        
        // Status Badges
        'status_teacher' => 'Teacher',
        'status_parent' => 'Parent',
        'status_read' => 'Read',
        'status_unread' => 'Unread',
        
        // Empty State
        'empty_recipients_title' => 'No recipients found',
        'empty_recipients_message' => 'This message doesn\'t have any recipients.',
        
        // Action Buttons
        'action_back' => 'Back to Messages',
        'action_edit' => 'Edit Message',
        'action_save' => 'Save Changes',
        'action_cancel' => 'Cancel',
        'action_delete' => 'Delete for Everyone',
        'action_processing' => 'Processing...',
        
        // Confirmation Messages
        'confirm_discard' => 'Discard all changes?',
        'confirm_save' => 'Save changes for ALL {count} recipients?',
        'confirm_delete' => '⚠️ Warning: This will delete this message for ALL {count} recipients. This action cannot be undone.\n\nAre you sure you want to continue?',
        'confirm_delete_single' => 'Delete this message for {name} only?',
        
        // Validation Messages
        'validation_title_required' => 'Please enter a message title',
        'validation_body_required' => 'Please enter message content',
        
        // Error Messages
        'error_invalid_group' => 'Invalid message group.',
        'error_not_found' => 'Message not found.',
        
        // Date/Time Formats
        'date_format' => 'F j, Y',
        'time_format' => 'h:i A',
        'month_day_format' => 'M j, Y',
        
        // Placeholder Text
        'placeholder_not_available' => '—',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'تفاصيل الرسالة | عرض الرسالة',
        'breadcrumb_messages' => 'الرسائل',
        'breadcrumb_details' => 'تفاصيل الرسالة',
        'header_title' => 'تفاصيل الرسالة',
        
        // Message Header
        'message_title_placeholder' => 'أدخل عنوان الرسالة...',
        'message_content_placeholder' => 'أدخل محتوى الرسالة هنا...',
        'message_id' => 'المعرف',
        
        // Stats Grid
        'stats_total_recipients' => 'إجمالي المستلمين',
        'stats_read_messages' => 'الرسائل المقروءة',
        'stats_unread_messages' => 'الرسائل غير المقروءة',
        'stats_read_rate' => 'معدل القراءة',
        
        // Message Body Section
        'section_message_content' => 'محتوى الرسالة',
        
        // Recipients Section
        'section_recipients' => 'المستلمين',
        'table_recipient' => 'المستلم',
        'table_type' => 'النوع',
        'table_status' => 'الحالة',
        'table_read_at' => 'وقت القراءة',
        'table_actions' => 'الإجراءات',
        
        // Status Badges
        'status_teacher' => 'معلم',
        'status_parent' => 'ولي أمر',
        'status_read' => 'مقروء',
        'status_unread' => 'غير مقروء',
        
        // Empty State
        'empty_recipients_title' => 'لم يتم العثور على مستلمين',
        'empty_recipients_message' => 'هذه الرسالة ليس لها أي مستلمين.',
        
        // Action Buttons
        'action_back' => 'رجوع إلى الرسائل',
        'action_edit' => 'تعديل الرسالة',
        'action_save' => 'حفظ التغييرات',
        'action_cancel' => 'إلغاء',
        'action_delete' => 'حذف للجميع',
        'action_processing' => 'جاري المعالجة...',
        
        // Confirmation Messages
        'confirm_discard' => 'تجاهل جميع التغييرات؟',
        'confirm_save' => 'حفظ التغييرات لجميع {count} المستلمين؟',
        'confirm_delete' => '⚠️ تحذير: سيؤدي هذا إلى حذف هذه الرسالة لجميع {count} المستلمين. لا يمكن التراجع عن هذا الإجراء.\n\nهل أنت متأكد أنك تريد المتابعة؟',
        'confirm_delete_single' => 'حذف هذه الرسالة لـ {name} فقط؟',
        
        // Validation Messages
        'validation_title_required' => 'الرجاء إدخال عنوان الرسالة',
        'validation_body_required' => 'الرجاء إدخال محتوى الرسالة',
        
        // Error Messages
        'error_invalid_group' => 'مجموعة رسائل غير صالحة.',
        'error_not_found' => 'الرسالة غير موجودة.',
        
        // Date/Time Formats
        'date_format' => 'F j, Y',
        'time_format' => 'h:i A',
        'month_day_format' => 'M j, Y',
        
        // Placeholder Text
        'placeholder_not_available' => '—',
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

if (empty($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

// theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;

// 1 = light, 0 = dark
$is_dark = ($theme === 0);

$group_id = isset($_GET['group_id']) ? (int)$_GET['group_id'] : 0;
if ($group_id <= 0) {
    die(t('error_invalid_group'));
}

// Edit message group (title & body)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && $_POST['action'] === 'edit_group'
    && !empty($_POST['group_id'])) {

    $gid = (int)$_POST['group_id'];
    $title = trim($_POST['title']);
    $body  = trim($_POST['body']);

    $stmt = $conn->prepare("
        UPDATE messages
        SET message_title = ?, message_body = ?
        WHERE message_group_id = ?
    ");
    $stmt->bind_param('ssi', $title, $body, $gid);
    $stmt->execute();
    $stmt->close();

    header("Location: message_view.php?group_id=$gid");
    exit;
}

/* ---------------- FETCH MESSAGE GROUP ---------------- */
$stmt = $conn->prepare("
    SELECT 
        m.message_group_id,
        m.message_title,
        m.message_body,
        m.created_at,
        COUNT(*) AS total_sent,
        SUM(m.is_read = 1) AS total_read
    FROM messages m
    WHERE m.message_group_id = ?
    GROUP BY m.message_group_id
");
$stmt->bind_param('i', $group_id);
$stmt->execute();
$group = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$group) {
    die(t('error_not_found'));
}

/* ---------------- FETCH RECIPIENTS ---------------- */
$stmt = $conn->prepare("
    SELECT 
        m.id,
        m.recipient_type,
        m.is_read,
        m.read_at,
        t.first_name AS teacher_first,
        t.last_name AS teacher_last,
        p.name AS parent_name
    FROM messages m
    LEFT JOIN teachers t 
        ON m.recipient_type = 'teacher' AND m.recipient_id = t.id
    LEFT JOIN parents p 
        ON m.recipient_type = 'parent' AND m.recipient_id = p.id
    WHERE m.message_group_id = ?
    ORDER BY m.is_read ASC, m.created_at DESC
");
$stmt->bind_param('i', $group_id);
$stmt->execute();
$recipients = $stmt->get_result();
$stmt->close();

$total_unread = $group['total_sent'] - $group['total_read'];
?>
<!doctype html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <title><?= t('page_title') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="<?= $is_dark ? 'dark' : '' ?>">

<div class="container">

    <!-- Page Header -->
    <div class="page-header">
        <div>
            <div class="breadcrumb">
                <a href="messages.php"><i class="fas fa-arrow-left"></i> <?= t('breadcrumb_messages') ?></a>
                <i class="fas fa-chevron-right"></i>
                <span><?= t('breadcrumb_details') ?></span>
            </div>
            <h1 class="page-title">
                <i class="fas fa-envelope-open-text"></i>
                <?= t('header_title') ?>
            </h1>
        </div>
    </div>

    <!-- Main Card -->
    <div class="main-card">

        <!-- Message Header -->
        <div class="message-header">
            <h1 id="viewTitle">
                <i class="fas fa-heading"></i>
                <?= htmlspecialchars($group['message_title']) ?>
            </h1>
            
            <input type="text" id="editTitle"
                   value="<?= htmlspecialchars($group['message_title']) ?>"
                   placeholder="<?= t('message_title_placeholder') ?>">
            
            <div class="message-meta">
                <span>
                    <i class="fas fa-calendar-alt"></i>
                    <?= date(t('date_format'), strtotime($group['created_at'])) ?>
                </span>
                <span>
                    <i class="fas fa-clock"></i>
                    <?= date(t('time_format'), strtotime($group['created_at'])) ?>
                </span>
                <span>
                    <i class="fas fa-hashtag"></i>
                    <?= t('message_id') ?>: <?= $group_id ?>
                </span>
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-value"><?= $group['total_sent'] ?></div>
                <div class="stat-label"><?= t('stats_total_recipients') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-success"><?= $group['total_read'] ?></div>
                <div class="stat-label"><?= t('stats_read_messages') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value text-danger"><?= $total_unread ?></div>
                <div class="stat-label"><?= t('stats_unread_messages') ?></div>
            </div>
            <div class="stat-card">
                <div class="stat-value">
                    <?= $group['total_sent'] > 0 ? round(($group['total_read'] / $group['total_sent']) * 100) : 0 ?>%
                </div>
                <div class="stat-label"><?= t('stats_read_rate') ?></div>
            </div>
        </div>

        <!-- Message Body -->
        <div class="message-body-container">
            <h3 class="section-title">
                <i class="fas fa-align-left"></i>
                <?= t('section_message_content') ?>
            </h3>
            
            <div id="viewBody">
                <?= nl2br(htmlspecialchars($group['message_body'])) ?>
            </div>
            
            <textarea id="editBody"
                      placeholder="<?= t('message_content_placeholder') ?>"
                      rows="8"><?= htmlspecialchars($group['message_body']) ?></textarea>
            
            <!-- Edit Actions -->
            <div class="edit-actions" id="editActions">
                <button class="btn btn-primary" onclick="saveEdit()">
                    <i class="fas fa-save"></i> <?= t('action_save') ?>
                </button>
                <button class="btn btn-outline" onclick="cancelEdit()">
                    <i class="fas fa-times"></i> <?= t('action_cancel') ?>
                </button>
            </div>
        </div>

        <!-- Recipients Section -->
        <div class="recipients-section">
            <h3 class="section-title">
                <i class="fas fa-users"></i>
                <?= t('section_recipients') ?> (<?= $group['total_sent'] ?>)
            </h3>

            <?php if($recipients->num_rows > 0): ?>
                <div class="table-container">
                    <table>
                        <thead>
                            <tr>
                                <th><?= t('table_recipient') ?></th>
                                <th><?= t('table_type') ?></th>
                                <th><?= t('table_status') ?></th>
                                <th><?= t('table_read_at') ?></th>
                                <!-- <th><?= t('table_actions') ?></th> -->
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($r = $recipients->fetch_assoc()):
                                $name = $r['recipient_type'] === 'teacher'
                                    ? trim($r['teacher_first'].' '.$r['teacher_last'])
                                    : $r['parent_name'];
                                $initials = $r['recipient_type'] === 'teacher'
                                    ? substr($r['teacher_first'], 0, 1) . substr($r['teacher_last'], 0, 1)
                                    : substr($r['parent_name'], 0, 2);
                                $type_label = $r['recipient_type'] === 'teacher' ? t('status_teacher') : t('status_parent');
                                $status_label = $r['is_read'] ? t('status_read') : t('status_unread');
                            ?>
                            <tr>
                                <td>
                                    <div class="flex items-center gap-4">
                                        <div style="width: 36px; height: 36px; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600; font-size: 14px;">
                                            <?= strtoupper($initials) ?>
                                        </div>
                                        <div>
                                            <div style="font-weight: 500;"><?= htmlspecialchars($name) ?></div>
                                            <div class="text-muted" style="font-size: 12px;">ID: <?= $r['id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge <?= $r['recipient_type'] ?>">
                                        <?= $type_label ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="flex items-center gap-2">
                                        <span class="status-indicator <?= $r['is_read'] ? 'status-read' : 'status-unread' ?>"></span>
                                        <span class="badge <?= $r['is_read'] ? 'read' : 'unread' ?>">
                                            <?= $status_label ?>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    <?php if($r['read_at']): ?>
                                        <div class="text-success">
                                            <i class="fas fa-check-circle"></i>
                                            <?= date(t('month_day_format'), strtotime($r['read_at'])) ?>
                                        </div>
                                        <div class="text-muted" style="font-size: 12px;">
                                            <?= date(t('time_format'), strtotime($r['read_at'])) ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted"><?= t('placeholder_not_available') ?></span>
                                    <?php endif; ?>
                                </td>
                                <!-- <td>
                                    <form method="POST" action="message_view.php"
                                          onsubmit="return confirm('<?= t('confirm_delete_single', ['name' => htmlspecialchars($name)]) ?>')">
                                        <input type="hidden" name="action" value="delete">
                                        <input type="hidden" name="id" value="<?= $r['id'] ?>">
                                        <button class="btn btn-danger btn-sm">
                                            <i class="fas fa-trash-alt"></i> <?= t('action_delete') ?>
                                        </button>
                                    </form>
                                </td> -->
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-users-slash"></i>
                    <h3><?= t('empty_recipients_title') ?></h3>
                    <p><?= t('empty_recipients_message') ?></p>
                </div>
            <?php endif; ?>

            <!-- Action Buttons -->
            <div class="action-buttons">
                <a href="messages.php" class="btn btn-back">
                    <i class="fas fa-arrow-left"></i> <?= t('action_back') ?>
                </a>
                
                <button class="btn btn-outline" onclick="enableEdit()">
                    <i class="fas fa-edit"></i> <?= t('action_edit') ?>
                </button>
                
                <form method="POST" action="message_view.php"
                      onsubmit="return confirm('<?= t('confirm_delete', ['count' => $group['total_sent']]) ?>')"
                      style="margin: 0;">
                    <input type="hidden" name="action" value="delete_group">
                    <input type="hidden" name="group_id" value="<?= $group_id ?>">
                    <button class="btn btn-danger">
                        <i class="fas fa-trash"></i> <?= t('action_delete') ?>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<!-- JavaScript -->
<script>
// Translations for JavaScript
const translations = {
    confirm_discard: "<?= t('confirm_discard') ?>",
    confirm_save: "<?= t('confirm_save', ['count' => $group['total_sent']]) ?>",
    validation_title_required: "<?= t('validation_title_required') ?>",
    validation_body_required: "<?= t('validation_body_required') ?>",
    action_processing: "<?= t('action_processing') ?>",
    delete_confirmation: "<?= t('confirm_delete', ['count' => $group['total_sent']]) ?>"
};

function enableEdit() {
    document.getElementById('viewTitle').style.display = 'none';
    document.getElementById('viewBody').style.display = 'none';
    document.getElementById('editTitle').style.display = 'block';
    document.getElementById('editBody').style.display = 'block';
    document.getElementById('editActions').style.display = 'flex';
    
    // Focus on title field
    document.getElementById('editTitle').focus();
}

function cancelEdit() {
    if (confirm(translations.confirm_discard)) {
        location.reload();
    }
}

function saveEdit() {
    const title = document.getElementById('editTitle').value.trim();
    const body = document.getElementById('editBody').value.trim();
    
    if (!title) {
        alert(translations.validation_title_required);
        document.getElementById('editTitle').focus();
        return;
    }
    
    if (!body) {
        alert(translations.validation_body_required);
        document.getElementById('editBody').focus();
        return;
    }
    
    if (!confirm(translations.confirm_save)) {
        return;
    }
    
    // Create and submit form
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'message_view.php';
    
    form.innerHTML = `
        <input type="hidden" name="action" value="edit_group">
        <input type="hidden" name="group_id" value="<?= $group_id ?>">
        <input type="hidden" name="title" value="${encodeURIComponent(title)}">
        <input type="hidden" name="body" value="${encodeURIComponent(body)}">
    `;
    
    document.body.appendChild(form);
    form.submit();
}

// Add keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+E to edit (Cmd+E on Mac)
    if ((e.ctrlKey || e.metaKey) && e.key === 'e') {
        e.preventDefault();
        enableEdit();
    }
    
    // Escape to cancel edit
    if (e.key === 'Escape' && document.getElementById('editActions').style.display === 'flex') {
        cancelEdit();
    }
});

// Add loading state to buttons
document.querySelectorAll('form').forEach(form => {
    form.addEventListener('submit', function() {
        const button = this.querySelector('button[type="submit"]');
        if (button) {
            button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> ' + translations.action_processing;
            button.disabled = true;
        }
    });
});
</script>

</body>
</html>

  <style>
        :root {
            /* Light Theme Variables */
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --light-bg: #f8fafc;
            --card-bg: #ffffff;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --border-color: #e2e8f0;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.08);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --transition: all 0.3s ease;
        }

        /* Dark Theme Variables */
        .dark {
            --primary-color: #6366f1;
            --primary-dark: #4f46e5;
            --secondary-color: #8b5cf6;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --light-bg: #0f172a;
            --card-bg: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-muted: #94a3b8;
            --border-color: #334155;
            --shadow-sm: 0 1px 3px rgba(0,0,0,0.3);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.4), 0 2px 4px -1px rgba(0,0,0,0.2);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.4), 0 4px 6px -2px rgba(0,0,0,0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background: var(--light-bg);
            color: var(--text-primary);
            line-height: 1.6;
            transition: var(--transition);
            min-height: 100vh;
        }

        .dark body {
            background: var(--light-bg);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 24px 16px;
        }

        @media (min-width: 768px) {
            .container {
                padding: 32px 24px;
            }
        }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 32px;
            flex-wrap: wrap;
            gap: 16px;
        }

        .page-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .page-title i {
            color: var(--primary-color);
            font-size: 24px;
        }

        /* Breadcrumb */
        .breadcrumb {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-secondary);
            font-size: 14px;
            margin-bottom: 8px;
        }

        .breadcrumb a {
            color: var(--primary-color);
            text-decoration: none;
            transition: var(--transition);
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .breadcrumb i {
            font-size: 12px;
        }

        /* Main Card */
        .main-card {
            background: var(--card-bg);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-lg);
            overflow: hidden;
            border: 1px solid var(--border-color);
            transition: var(--transition);
        }

        /* Message Header */
        .message-header {
            padding: 24px 32px;
            border-bottom: 1px solid var(--border-color);
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            color: white;
            position: relative;
        }

        .message-header h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 8px;
            word-break: break-word;
            line-height: 1.4;
        }

        .message-header h1 i {
            margin-right: 12px;
            font-size: 20px;
        }

        #editTitle {
            display: none;
            width: 100%;
            font-size: 24px;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            border: 2px solid rgba(255,255,255,0.3);
            background: rgba(255,255,255,0.1);
            color: white;
            font-weight: 600;
            margin-bottom: 8px;
        }

        #editTitle:focus {
            outline: none;
            border-color: rgba(255,255,255,0.6);
            background: rgba(255,255,255,0.15);
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 16px;
            font-size: 14px;
            opacity: 0.9;
        }

        .message-meta i {
            margin-right: 6px;
        }

        /* Message Body */
        .message-body-container {
            padding: 32px;
        }

        #viewBody {
            white-space: pre-wrap;
            font-size: 16px;
            line-height: 1.8;
            color: var(--text-primary);
            padding: 24px;
            background: var(--light-bg);
            border-radius: var(--radius-md);
            border-left: 4px solid var(--primary-color);
        }

        #editBody {
            display: none;
            width: 100%;
            min-height: 200px;
            padding: 20px;
            font-size: 16px;
            line-height: 1.6;
            border: 2px solid var(--border-color);
            border-radius: var(--radius-md);
            background: var(--card-bg);
            color: var(--text-primary);
            font-family: inherit;
            resize: vertical;
            transition: var(--transition);
        }

        #editBody:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 16px;
            padding: 24px 32px;
            background: var(--light-bg);
            border-bottom: 1px solid var(--border-color);
        }

        .stat-card {
            background: var(--card-bg);
            padding: 20px;
            border-radius: var(--radius-md);
            text-align: center;
            box-shadow: var(--shadow-sm);
            transition: var(--transition);
            border: 1px solid var(--border-color);
        }

        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 4px;
        }

        .stat-label {
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Recipients Section */
        .recipients-section {
            padding: 32px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 600;
            margin-bottom: 24px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .section-title i {
            color: var(--primary-color);
        }

        /* Table Styling */
        .table-container {
            overflow-x: auto;
            border-radius: var(--radius-md);
            border: 1px solid var(--border-color);
            margin-bottom: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        thead {
            background: var(--light-bg);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border-color);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--light-bg);
        }

        /* Badges */
        .badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .badge.read {
            background: rgba(46, 204, 113, 0.15);
            color: var(--success-color);
        }

        .badge.unread {
            background: rgba(231, 76, 60, 0.15);
            color: var(--danger-color);
        }

        .badge.teacher {
            background: rgba(67, 97, 238, 0.15);
            color: var(--primary-color);
        }

        .badge.parent {
            background: rgba(114, 9, 183, 0.15);
            color: var(--secondary-color);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: var(--transition);
            white-space: nowrap;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-back {
            background: var(--light-bg);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-back:hover {
            background: var(--border-color);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
            border: none;
        }

        .btn-primary:hover {
            background: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .btn-danger {
            background: var(--danger-color);
            color: white;
            border: none;
        }

        .btn-danger:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 12px;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 32px;
            padding-top: 24px;
            border-top: 1px solid var(--border-color);
        }

        .edit-actions {
            display: none;
            gap: 12px;
            margin-top: 20px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 48px 24px;
            color: var(--text-muted);
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 16px;
            opacity: 0.5;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .page-title {
                font-size: 24px;
            }
            
            .message-header {
                padding: 20px;
            }
            
            .message-header h1 {
                font-size: 20px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
                padding: 20px;
            }
            
            .message-body-container,
            .recipients-section {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .action-buttons .btn {
                width: 100%;
                justify-content: center;
            }
            
            th, td {
                padding: 12px 16px;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 16px 12px;
            }
            
            .message-meta {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }
            
            .stat-card {
                padding: 16px;
            }
            
            .stat-value {
                font-size: 24px;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .main-card {
            animation: fadeIn 0.5s ease-out;
        }

        /* Status Indicators */
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 8px;
        }

        .status-read {
            background-color: var(--success-color);
        }

        .status-unread {
            background-color: var(--danger-color);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--light-bg);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--border-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--text-muted);
        }

        /* Utility Classes */
        .text-muted {
            color: var(--text-muted);
        }

        .text-success {
            color: var(--success-color);
        }

        .text-danger {
            color: var(--danger-color);
        }

        .mb-4 {
            margin-bottom: 16px;
        }

        .mt-4 {
            margin-top: 16px;
        }

        .flex {
            display: flex;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .gap-2 {
            gap: 8px;
        }

        .gap-4 {
            gap: 16px;
        }

        .w-full {
            width: 100%;
        }
    </style>