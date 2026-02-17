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
        'page_title' => 'Send Message - Admin',
        'header_title' => 'Send New Message',
        'back_button' => 'Back to Dashboard',
        
        // Card Titles
        'card_recipient_type' => '1. Who are you sending to?',
        'card_filter_recipients' => '2. Filter Recipients',
        'card_compose_message' => '3. Compose Message',
        'card_target_audience' => 'Target Audience',
        
        // Radio Options
        'radio_teachers' => 'Teachers',
        'radio_parents' => 'Parents',
        'radio_both' => 'Both',
        
        // Filter Sections
        'filter_subjects' => 'Subjects',
        'filter_days' => 'Days of Week (Class Schedule)',
        'filter_sections' => 'Sections',
        'filter_classes' => 'Classes',
        
        // Form Labels
        'label_message_title' => 'Message Title *',
        'label_message_title_placeholder' => 'e.g. Important Notice',
        'label_message_body' => 'Message Body *',
        'label_message_body_placeholder' => 'Type your message here...',
        
        // Search & Selection
        'search_placeholder' => 'Search name...',
        'button_select_all' => 'All',
        'button_select_none' => 'None',
        'counter_selected' => 'Selected',
        'button_send_message' => 'Send Message',
        
        // Status Messages
        'status_loading' => 'Updating list...',
        'status_no_recipients' => 'Select filters to find recipients.',
        'status_no_recipients_found' => 'No recipients found matching filters.',
        'status_no_subjects' => 'No subjects found.',
        'status_no_sections' => 'No sections found.',
        'status_no_classes' => 'No classes found.',
        
        // Success/Error Messages
        'success_message_sent' => 'Message sent successfully to {count} recipients.',
        'error_title_body_required' => 'Title and Body are required.',
        'error_recipients_required' => 'Please select at least one recipient.',
        
        // Validation Messages (JavaScript)
        'js_validation_recipients_required' => 'Please select at least one recipient from the list.',
        'js_validation_confirm_large_send' => 'You are about to send this message to {count} recipients. Are you sure?',
        
        // Days of Week
        'day_monday' => 'Monday',
        'day_tuesday' => 'Tuesday',
        'day_wednesday' => 'Wednesday',
        'day_thursday' => 'Thursday',
        'day_friday' => 'Friday',
        'day_saturday' => 'Saturday',
        'day_sunday' => 'Sunday',
        
        // Recipient Labels
        'label_teacher' => 'Teacher',
        'label_parent' => 'Parent',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إرسال رسالة - المسؤول',
        'header_title' => 'إرسال رسالة جديدة',
        'back_button' => 'رجوع إلى لوحة التحكم',
        
        // Card Titles
        'card_recipient_type' => '1. لمن ترسل الرسالة؟',
        'card_filter_recipients' => '2. تصفية المستلمين',
        'card_compose_message' => '3. كتابة الرسالة',
        'card_target_audience' => 'الجمهور المستهدف',
        
        // Radio Options
        'radio_teachers' => 'المعلمين',
        'radio_parents' => 'أولياء الأمور',
        'radio_both' => 'كلاهما',
        
        // Filter Sections
        'filter_subjects' => 'المواد',
        'filter_days' => 'أيام الأسبوع (جدول الحصص)',
        'filter_sections' => 'الأقسام',
        'filter_classes' => 'الفصول',
        
        // Form Labels
        'label_message_title' => 'عنوان الرسالة *',
        'label_message_title_placeholder' => 'مثال: إشعار مهم',
        'label_message_body' => 'محتوى الرسالة *',
        'label_message_body_placeholder' => 'اكتب رسالتك هنا...',
        
        // Search & Selection
        'search_placeholder' => 'بحث بالاسم...',
        'button_select_all' => 'الكل',
        'button_select_none' => 'لا شيء',
        'counter_selected' => 'محدد',
        'button_send_message' => 'إرسال الرسالة',
        
        // Status Messages
        'status_loading' => 'جاري تحديث القائمة...',
        'status_no_recipients' => 'حدد الفلاتر للعثور على المستلمين.',
        'status_no_recipients_found' => 'لم يتم العثور على مستلمين مطابقين للفلاتر.',
        'status_no_subjects' => 'لم يتم العثور على مواد.',
        'status_no_sections' => 'لم يتم العثور على أقسام.',
        'status_no_classes' => 'لم يتم العثور على فصول.',
        
        // Success/Error Messages
        'success_message_sent' => 'تم إرسال الرسالة بنجاح إلى {count} مستلم.',
        'error_title_body_required' => 'العنوان والمحتوى مطلوبان.',
        'error_recipients_required' => 'الرجاء تحديد مستلم واحد على الأقل.',
        
        // Validation Messages (JavaScript)
        'js_validation_recipients_required' => 'الرجاء تحديد مستلم واحد على الأقل من القائمة.',
        'js_validation_confirm_large_send' => 'أنت على وشك إرسال هذه الرسالة إلى {count} مستلم. هل أنت متأكد؟',
        
        // Days of Week
        'day_monday' => 'الإثنين',
        'day_tuesday' => 'الثلاثاء',
        'day_wednesday' => 'الأربعاء',
        'day_thursday' => 'الخميس',
        'day_friday' => 'الجمعة',
        'day_saturday' => 'السبت',
        'day_sunday' => 'الأحد',
        
        // Recipient Labels
        'label_teacher' => 'معلم',
        'label_parent' => 'ولي أمر',
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

// Check Auth


/* ================= THEME & HELPER ================= */
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0); // 1 = Dark, 0 = Light

/* ================= AJAX HANDLER: FETCH RECIPIENTS ================= */
if (isset($_GET['action']) && $_GET['action'] === 'fetch_recipients') {
    header('Content-Type: application/json');
    
    $type = $_GET['recipient_type'] ?? 'teacher';
    $class_ids = isset($_GET['class_ids']) ? explode(',', $_GET['class_ids']) : [];
    $section_ids = isset($_GET['section_ids']) ? explode(',', $_GET['section_ids']) : [];
    $subject_ids = isset($_GET['subject_ids']) ? explode(',', $_GET['subject_ids']) : [];
    $days = isset($_GET['days']) ? explode(',', $_GET['days']) : [];
    $search = $_GET['search'] ?? '';

    $recipients = [];

    // Helper to sanitize array for SQL IN clause
    function sanitize_ids($conn, $ids) {
        return implode(',', array_map('intval', array_filter($ids, 'is_numeric')));
    }

    $c_in = sanitize_ids($conn, $class_ids);
    $s_in = sanitize_ids($conn, $section_ids);
    $sub_in = sanitize_ids($conn, $subject_ids);
    
    // 1. FETCH TEACHERS
    if ($type === 'teacher' || $type === 'both') {
        $sql = "SELECT DISTINCT t.id, t.first_name, t.last_name, 'teacher' as role 
                FROM teachers t 
                LEFT JOIN class_subjects cs ON t.id = cs.teacher_id
                WHERE t.status = 'active'";

        $conditions = [];
        
        if (!empty($c_in)) {
            $conditions[] = "cs.class_id IN ($c_in)";
        }
        if (!empty($sub_in)) {
            $conditions[] = "cs.subject_id IN ($sub_in)";
        }
        if (!empty($days)) {
            $day_str = implode("','", array_map([$conn, 'real_escape_string'], $days));
            $conditions[] = "cs.day_of_week IN ('$day_str')";
        }
        
        if (!empty($conditions)) {
            $sql .= " AND (" . implode(' AND ', $conditions) . ")";
        }

        if (!empty($search)) {
            $safe_search = $conn->real_escape_string($search);
            $sql .= " AND (t.first_name LIKE '%$safe_search%' OR t.last_name LIKE '%$safe_search%')";
        }

        $res = $conn->query($sql);
        if($res) {
            while ($row = $res->fetch_assoc()) {
                $recipients[] = [
                    'id' => $row['id'],
                    'name' => $row['first_name'] . ' ' . $row['last_name'] . ' (' . t('label_teacher') . ')',
                    'type' => 'teacher',
                    'val' => 'teacher_' . $row['id']
                ];
            }
        }
    }

    // 2. FETCH PARENTS
    if ($type === 'parent' || $type === 'both') {
        // Link: parents.phone -> students.guardian_phone -> class_students.student_id -> class_students.class_id
        $sql = "SELECT DISTINCT p.id, p.name, 'parent' as role
                FROM parents p
                JOIN students s ON p.phone = s.guardian_phone
                JOIN class_students cs ON s.id = cs.student_id
                JOIN classes c ON cs.class_id = c.id
                WHERE 1=1";

        $conditions = [];

        if (!empty($c_in)) {
            $conditions[] = "cs.class_id IN ($c_in)";
        }
        if (!empty($s_in)) {
            // Filter classes by section
            $conditions[] = "c.section_id IN ($s_in)";
        }

        if (!empty($conditions)) {
            $sql .= " AND (" . implode(' AND ', $conditions) . ")";
        }

        if (!empty($search)) {
            $safe_search = $conn->real_escape_string($search);
            $sql .= " AND p.name LIKE '%$safe_search%'";
        }

        $res = $conn->query($sql);
        if($res) {
            while ($row = $res->fetch_assoc()) {
                $recipients[] = [
                    'id' => $row['id'],
                    'name' => $row['name'] . ' (' . t('label_parent') . ')',
                    'type' => 'parent',
                    'val' => 'parent_' . $row['id']
                ];
            }
        }
    }

    echo json_encode($recipients);
    exit;
}

/* ================= FORM SUBMISSION HANDLER ================= */
$msg_status = '';
$msg_count = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_message'])) {
    $title = trim($_POST['message_title']);
    $body = trim($_POST['message_body']);
    $sender_id = $_SESSION['user_id']; // Logged in Admin ID
    $recipients_raw = $_POST['recipients'] ?? []; // Array of strings "teacher_1", "parent_5"

    if (empty($title) || empty($body)) {
        $msg_status = "error";
        $feedback = t('error_title_body_required');
    } elseif (empty($recipients_raw)) {
        $msg_status = "error";
        $feedback = t('error_recipients_required');
    } else {

    $message_group_id = time(); // 🔥 one ID for all recipients

    $stmt = $conn->prepare("
        INSERT INTO messages 
        (
            sender_id,
            sender_type,
            message_group_id,
            recipient_type,
            recipient_id,
            message_title,
            message_body,
            created_at
        )
        VALUES (?, 'admin', ?, ?, ?, ?, ?, NOW())
    ");

    $success_count = 0;

    foreach ($recipients_raw as $r_str) {
        list($r_type, $r_id) = explode('_', $r_str);
        $r_id = (int)$r_id;

        $stmt->bind_param(
            "iisiss",
            $sender_id,
            $message_group_id,
            $r_type,
            $r_id,
            $title,
            $body
        );

        if ($stmt->execute()) {
            $success_count++;
        }
    }

    $stmt->close();

    $msg_status = "success";
    $msg_count  = $success_count;
    $feedback   = t('success_message_sent', ['count' => $success_count]);
}

}

/* ================= FETCH INITIAL FILTER DATA ================= */
// Classes
$classes = $conn->query("SELECT id, name FROM classes ORDER BY name ASC");
// Sections
$sections = $conn->query("SELECT id, name FROM sections ORDER BY name ASC");
// Subjects (Assuming table exists, otherwise fetch from class_subjects distinct)
$subjects = $conn->query("SELECT id, name FROM subjects ORDER BY name ASC"); 
if (!$subjects) {
    // Fallback if subjects table not queried correctly or doesn't exist standardly
    // We try to get from class_subjects but we need names. 
    // For now assuming subjects table exists as per standard logic.
}
// Days
$days = [
    t('day_monday'),
    t('day_tuesday'),
    t('day_wednesday'),
    t('day_thursday'),
    t('day_friday'),
    t('day_saturday'),
    t('day_sunday')
];

?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <!-- Simple CSS Reset & Flexbox system -->
</head>
<body>

<div class="container">
    <div class="header">
        <h1><?= t('header_title') ?></h1>
        <a href="javascript:history.back()" class="btn btn-outline"><?= t('back_button') ?></a>
    </div>

    <?php if ($msg_status == 'success'): ?>
        <div class="alert alert-success"><?php echo $feedback; ?></div>
    <?php elseif ($msg_status == 'error'): ?>
        <div class="alert alert-error"><?php echo $feedback; ?></div>
    <?php endif; ?>

    <form method="POST" action="" id="messageForm">
        <div class="layout-grid">
            
            <!-- LEFT COLUMN: Filters & Message Content -->
            <div class="left-col">
                
                <!-- 1. Recipient Type -->
                <div class="card" style="margin-bottom: 20px;">
                    <h3 class="card-title"><?= t('card_recipient_type') ?></h3>
                    <div class="form-group">
                        <label class="radio-inline">
                            <input type="radio" name="target_type" value="teacher" checked onchange="toggleFilters()"> <?= t('radio_teachers') ?>
                        </label>
                        <label class="radio-inline" style="margin-left: 15px;">
                            <input type="radio" name="target_type" value="parent" onchange="toggleFilters()"> <?= t('radio_parents') ?>
                        </label>
                        <label class="radio-inline" style="margin-left: 15px;">
                            <input type="radio" name="target_type" value="both" onchange="toggleFilters()"> <?= t('radio_both') ?>
                        </label>
                    </div>
                </div>

                <!-- 2. Dynamic Filters -->
                <div class="card" style="margin-bottom: 20px;">
                    <h3 class="card-title"><?= t('card_filter_recipients') ?></h3>
                    
                    <!-- Subject Filter (Teachers Only) -->
                    <div id="filter-subjects" class="filter-section">
                        <div class="filter-title"><?= t('filter_subjects') ?></div>
                        <div class="checkbox-grid">
                            <?php if ($subjects && $subjects->num_rows > 0): ?>
                                <?php while($sub = $subjects->fetch_assoc()): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" class="filter-input" name="filter_subject[]" value="<?php echo $sub['id']; ?>">
                                        <?php echo htmlspecialchars($sub['name']); ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small><?= t('status_no_subjects') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Day Filter (Teachers Only) -->
                    <div id="filter-days" class="filter-section">
                        <div class="filter-title"><?= t('filter_days') ?></div>
                        <div class="checkbox-grid">
                            <?php foreach($days as $day): ?>
                                <div class="checkbox-item">
                                    <input type="checkbox" class="filter-input" name="filter_day[]" value="<?php echo $day; ?>">
                                    <?php echo $day; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>

                    <!-- Section Filter (Parents Only) -->
                    <div id="filter-sections" class="filter-section hidden">
                        <div class="filter-title"><?= t('filter_sections') ?></div>
                        <div class="checkbox-grid">
                            <?php if ($sections && $sections->num_rows > 0): ?>
                                <?php while($sec = $sections->fetch_assoc()): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" class="filter-input" name="filter_section[]" value="<?php echo $sec['id']; ?>">
                                        <?php echo htmlspecialchars($sec['name']); ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small><?= t('status_no_sections') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Class Filter (Both) -->
                    <div id="filter-classes" class="filter-section">
                        <div class="filter-title"><?= t('filter_classes') ?></div>
                        <div class="checkbox-grid">
                            <?php if ($classes && $classes->num_rows > 0): ?>
                                <?php while($cls = $classes->fetch_assoc()): ?>
                                    <div class="checkbox-item">
                                        <input type="checkbox" class="filter-input" name="filter_class[]" value="<?php echo $cls['id']; ?>">
                                        <?php echo htmlspecialchars($cls['name']); ?>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <small><?= t('status_no_classes') ?></small>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- 3. Message Content -->
                <div class="card">
                    <h3 class="card-title"><?= t('card_compose_message') ?></h3>
                    <div class="form-group">
                        <label class="form-label"><?= t('label_message_title') ?></label>
                        <input type="text" name="message_title" class="form-control" required placeholder="<?= t('label_message_title_placeholder') ?>">
                    </div>
                    <div class="form-group">
                        <label class="form-label"><?= t('label_message_body') ?></label>
                        <textarea name="message_body" class="form-control" required placeholder="<?= t('label_message_body_placeholder') ?>"></textarea>
                    </div>
                </div>

            </div>

            <!-- RIGHT COLUMN: Recipient List -->
            <div class="right-col">
                <div class="card" style="position: sticky; top: 20px;">
                    <h3 class="card-title">
                        <?= t('card_target_audience') ?>
                        <span id="recipient-counter" style="float: right; font-size: 0.8em; background: var(--primary); color: white; padding: 2px 8px; border-radius: 10px;">0 <?= t('counter_selected') ?></span>
                    </h3>
                    
                    <div class="recipient-tools">
                        <input type="text" id="search-box" class="form-control" placeholder="<?= t('search_placeholder') ?>" style="width: 60%;" onkeyup="filterListUI()">
                        <div>
                            <button type="button" class="btn btn-sm btn-outline" onclick="selectAll(true)"><?= t('button_select_all') ?></button>
                            <button type="button" class="btn btn-sm btn-outline" onclick="selectAll(false)"><?= t('button_select_none') ?></button>
                        </div>
                    </div>

                    <div id="loading-indicator" class="loader hidden"><?= t('status_loading') ?></div>
                    <div id="recipient-container" class="recipient-list">
                        <!-- Recipients injected via JS -->
                        <div style="text-align:center; padding: 20px; color: var(--text-muted);"><?= t('status_no_recipients') ?></div>
                    </div>

                    <div style="margin-top: 20px;">
                        <button type="submit" name="send_message" class="btn btn-primary" style="width: 100%;" onclick="return validateForm()"><?= t('button_send_message') ?></button>
                    </div>
                </div>
            </div>
            
        </div>
    </form>
</div>

<script>
    // Translations for JavaScript
    const translations = {
        status_no_recipients_found: "<?= t('status_no_recipients_found') ?>",
        validation_recipients_required: "<?= t('js_validation_recipients_required') ?>",
        validation_confirm_large_send: "<?= t('js_validation_confirm_large_send') ?>"
    };

    // Initial State
    document.addEventListener('DOMContentLoaded', function() {
        toggleFilters();
        fetchRecipients();
        
        // Attach Event Listeners to all filter inputs
        const inputs = document.querySelectorAll('.filter-input');
        inputs.forEach(input => {
            input.addEventListener('change', fetchRecipients);
        });
    });

    // Toggle Visibility based on Type
    function toggleFilters() {
        const type = document.querySelector('input[name="target_type"]:checked').value;
        const subjDiv = document.getElementById('filter-subjects');
        const daysDiv = document.getElementById('filter-days');
        const sectDiv = document.getElementById('filter-sections');
        
        if (type === 'teacher') {
            subjDiv.classList.remove('hidden');
            daysDiv.classList.remove('hidden');
            sectDiv.classList.add('hidden');
        } else if (type === 'parent') {
            subjDiv.classList.add('hidden');
            daysDiv.classList.add('hidden');
            sectDiv.classList.remove('hidden');
        } else { // Both
            // For 'Both', we usually just filter by Class (shared attribute)
            // But if you want to allow filtering teachers by subject while sending to parents of that class, keep them.
            // Simplified: Show all relevant.
            subjDiv.classList.remove('hidden');
            daysDiv.classList.remove('hidden');
            sectDiv.classList.remove('hidden');
        }
        
        // Refresh list when type changes
        fetchRecipients();
    }

    // AJAX Fetch
    function fetchRecipients() {
        const type = document.querySelector('input[name="target_type"]:checked').value;
        
        // Gather IDs
        const getIds = (name) => {
            return Array.from(document.querySelectorAll(`input[name="${name}[]"]:checked`)).map(cb => cb.value).join(',');
        };

        const classes = getIds('filter_class');
        const sections = getIds('filter_section');
        const subjects = getIds('filter_subject');
        const days = getIds('filter_day');
        
        const container = document.getElementById('recipient-container');
        const loader = document.getElementById('loading-indicator');
        
        loader.classList.remove('hidden');
        container.style.opacity = '0.5';

        // Construct URL
        const url = `?action=fetch_recipients&recipient_type=${type}&class_ids=${classes}&section_ids=${sections}&subject_ids=${subjects}&days=${days}`;

        fetch(url)
            .then(response => response.json())
            .then(data => {
                renderRecipients(data);
                loader.classList.add('hidden');
                container.style.opacity = '1';
            })
            .catch(error => {
                console.error('Error:', error);
                loader.classList.add('hidden');
                container.style.opacity = '1';
            });
    }

    // Render List
    function renderRecipients(data) {
        const container = document.getElementById('recipient-container');
        container.innerHTML = '';

        if (data.length === 0) {
            container.innerHTML = `<div style="text-align:center; padding: 20px; color: var(--text-muted);">${translations.status_no_recipients_found}</div>`;
            updateCounter();
            return;
        }

        data.forEach(person => {
            const div = document.createElement('div');
            div.className = 'recipient-item';
            div.innerHTML = `
                <input type="checkbox" name="recipients[]" value="${person.val}" class="recipient-checkbox" checked onchange="updateCounter()">
                <label>${person.name}</label>
            `;
            // Add click listener to the div (excluding input) to toggle check
            div.addEventListener('click', function(e) {
                if(e.target.tagName !== 'INPUT') {
                    const cb = this.querySelector('input');
                    cb.checked = !cb.checked;
                    updateCounter();
                }
            });
            container.appendChild(div);
        });
        updateCounter();
    }

    // Client-side Search filter inside the list
    function filterListUI() {
        const term = document.getElementById('search-box').value.toLowerCase();
        const items = document.querySelectorAll('.recipient-item');
        
        items.forEach(item => {
            const name = item.querySelector('label').innerText.toLowerCase();
            if (name.includes(term)) {
                item.style.display = 'flex';
            } else {
                item.style.display = 'none';
            }
        });
    }

    // Select All / None
    function selectAll(check) {
        const cbs = document.querySelectorAll('.recipient-checkbox');
        // Only select visible ones (in case user searched)
        cbs.forEach(cb => {
            if(cb.closest('.recipient-item').style.display !== 'none') {
                cb.checked = check;
            }
        });
        updateCounter();
    }

    // Update Counter
    function updateCounter() {
        const count = document.querySelectorAll('.recipient-checkbox:checked').length;
        document.getElementById('recipient-counter').innerText = count + " <?= t('counter_selected') ?>";
    }

    // Validation before submit
    function validateForm() {
        const count = document.querySelectorAll('.recipient-checkbox:checked').length;
        if (count === 0) {
            alert(translations.validation_recipients_required);
            return false;
        }
        
        if (count > 50) {
            const message = translations.validation_confirm_large_send.replace('{count}', count);
            return confirm(message);
        }
        
        return true;
    }
</script>

</body>
</html>

   <style>
        :root {
            --bg-body: <?php echo $is_dark ? '#1a1b1e' : '#f4f6f9'; ?>;
            --bg-card: <?php echo $is_dark ? '#25262b' : '#ffffff'; ?>;
            --text-main: <?php echo $is_dark ? '#c1c2c5' : '#333333'; ?>;
            --text-muted: <?php echo $is_dark ? '#909296' : '#666666'; ?>;
            --border-color: <?php echo $is_dark ? '#373a40' : '#ddd'; ?>;
            --primary: #3b82f6;
            --primary-hover: #2563eb;
            --danger: #ef4444;
            --success: #10b981;
        }
        * { box-sizing: border_box; }
        body { font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif; background-color: var(--bg-body); color: var(--text-main); margin: 0; padding: 20px; }
        
        .container { max-width: 1200px; margin: 0 auto; }
        .header { margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; }
        .header h1 { margin: 0; font-size: 24px; }
        
        /* Layout Grid */
        .layout-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        @media (max-width: 768px) { .layout-grid { grid-template-columns: 1fr; } }

        /* Card Styles */
        .card { background: var(--bg-card); border: 1px solid var(--border-color); border-radius: 8px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); }
        .card-title { margin-top: 0; border-bottom: 1px solid var(--border-color); padding-bottom: 10px; margin-bottom: 15px; font-size: 18px; }

        /* Forms */
        .form-group { margin-bottom: 15px; }
        .form-label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 0.9rem; }
        .form-control { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 4px; background: var(--bg-body); color: var(--text-main); }
        textarea.form-control { resize: vertical; min-height: 100px; }
        
        /* Filters */
        .filter-section { margin-bottom: 15px; padding: 10px; border: 1px solid var(--border-color); border-radius: 6px; }
        .filter-title { font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); margin-bottom: 8px; }
        .checkbox-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(120px, 1fr)); gap: 8px; max-height: 150px; overflow-y: auto; }
        .checkbox-item { display: flex; align-items: center; font-size: 0.9rem; }
        .checkbox-item input { margin-right: 8px; }

        /* Recipient List */
        .recipient-tools { display: flex; justify-content: space-between; margin-bottom: 10px; }
        .recipient-list { height: 400px; overflow-y: auto; border: 1px solid var(--border-color); border-radius: 4px; padding: 10px; background: var(--bg-body); }
        .recipient-item { padding: 5px; border-bottom: 1px solid var(--border-color); display: flex; align-items: center; }
        .recipient-item:last-child { border-bottom: none; }
        .recipient-item label { margin-left: 10px; cursor: pointer; flex: 1; }
        
        /* Buttons */
        .btn { padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block; }
        .btn-primary { background: var(--primary); color: white; }
        .btn-primary:hover { background: var(--primary-hover); }
        .btn-sm { padding: 5px 10px; font-size: 0.8rem; }
        .btn-outline { background: transparent; border: 1px solid var(--border-color); color: var(--text-main); }
        
        /* Alerts */
        .alert { padding: 15px; margin-bottom: 20px; border-radius: 4px; }
        .alert-success { background: rgba(16, 185, 129, 0.2); color: var(--success); border: 1px solid var(--success); }
        .alert-error { background: rgba(239, 68, 68, 0.2); color: var(--danger); border: 1px solid var(--danger); }

        .hidden { display: none; }
        .loader { text-align: center; padding: 20px; color: var(--text-muted); }
    </style>