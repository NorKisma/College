<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Edit Attendance Session | Dashboard',
        'header_title' => 'Edit Attendance Session',
        'access_denied' => 'Access denied',
        'user_not_found' => 'User not found',
        'invalid_session' => 'Invalid session',
        
        // Session Info Cards
        'info_class' => 'Class',
        'info_subject' => 'Subject',
        'info_section' => 'Section',
        'info_date' => 'Date',
        'info_time_slot' => 'Time Slot',
        
        // Statistics
        'stat_present' => 'Present',
        'stat_absent' => 'Absent',
        'stat_late' => 'Late',
        'stat_excused' => 'Excused',
        'stat_percentage' => '% of total',
        
        // Form Elements
        'form_title' => 'Session Editor',
        'form_subtitle' => 'Update attendance status for each student',
        'label_recorded_by' => 'Recorded By',
        'optgroup_users' => 'Users',
        'optgroup_teachers' => 'Teachers',
        
        // Table Headers
        'table_number' => '#',
        'table_student' => 'Student',
        'table_attendance_status' => 'Attendance Status',
        
        // Status Options
        'option_present' => 'Present',
        'option_absent' => 'Absent',
        'option_late' => 'Late',
        'option_excused' => 'Excused',
        
        // History
        'history_button' => 'History',
        'view_all_changes' => 'View all {count} changes',
        'history_modal_title' => 'Attendance History',
        'history_table_previous' => 'Previous Status',
        'history_table_new' => 'New Status',
        'history_table_edited_by' => 'Edited By',
        'history_table_date_time' => 'Date & Time',
        'history_table_number' => '#',
        'history_not_available' => 'N/A',
        
        // Action Buttons
        'btn_save_changes' => 'Save Changes',
        'btn_saving' => 'Saving Changes...',
        'btn_saving_with_count' => 'Saving {count} changes...',
        'btn_cancel' => 'Cancel',
        'btn_close' => 'Close',
        
        // Tooltips
        'tooltip_history' => 'View attendance history',
        
        // Status Icons
        'icon_present' => 'check-circle',
        'icon_absent' => 'x-circle',
        'icon_late' => 'clock-history',
        'icon_excused' => 'shield-check',
        
        // Role Labels
        'label_admin' => 'Admin',
        'label_member' => 'Member',
        'label_teacher' => 'Teacher',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'تعديل جلسة الحضور | لوحة التحكم',
        'header_title' => 'تعديل جلسة الحضور',
        'access_denied' => 'الوصول مرفوض',
        'user_not_found' => 'المستخدم غير موجود',
        'invalid_session' => 'جلسة غير صالحة',
        
        // Session Info Cards
        'info_class' => 'الصف',
        'info_subject' => 'المادة',
        'info_section' => 'القسم',
        'info_date' => 'التاريخ',
        'info_time_slot' => 'الفترة الزمنية',
        
        // Statistics
        'stat_present' => 'حاضر',
        'stat_absent' => 'غائب',
        'stat_late' => 'متأخر',
        'stat_excused' => 'معذور',
        'stat_percentage' => '% من الإجمالي',
        
        // Form Elements
        'form_title' => 'محرر الجلسة',
        'form_subtitle' => 'تحديث حالة الحضور لكل طالب',
        'label_recorded_by' => 'مسجل بواسطة',
        'optgroup_users' => 'المستخدمين',
        'optgroup_teachers' => 'المعلمين',
        
        // Table Headers
        'table_number' => '#',
        'table_student' => 'الطالب',
        'table_attendance_status' => 'حالة الحضور',
        
        // Status Options
        'option_present' => 'حاضر',
        'option_absent' => 'غائب',
        'option_late' => 'متأخر',
        'option_excused' => 'معذور',
        
        // History
        'history_button' => 'السجل',
        'view_all_changes' => 'عرض جميع التغييرات {count}',
        'history_modal_title' => 'سجل الحضور',
        'history_table_previous' => 'الحالة السابقة',
        'history_table_new' => 'الحالة الجديدة',
        'history_table_edited_by' => 'تم التعديل بواسطة',
        'history_table_date_time' => 'التاريخ والوقت',
        'history_table_number' => '#',
        'history_not_available' => 'غير متوفر',
        
        // Action Buttons
        'btn_save_changes' => 'حفظ التغييرات',
        'btn_saving' => 'جاري الحفظ...',
        'btn_saving_with_count' => 'جاري حفظ {count} تغييرات...',
        'btn_cancel' => 'إلغاء',
        'btn_close' => 'إغلاق',
        
        // Tooltips
        'tooltip_history' => 'عرض سجل الحضور',
        
        // Status Icons
        'icon_present' => 'check-circle',
        'icon_absent' => 'x-circle',
        'icon_late' => 'clock-history',
        'icon_excused' => 'shield-check',
        
        // Role Labels
        'label_admin' => 'مدير',
        'label_member' => 'عضو',
        'label_teacher' => 'معلم',
    ]
];

// Translation helper function
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

/* ---------- AUTH ---------- */
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) die(t('access_denied'));

/* ---------- LOAD CURRENT USER ---------- */
$current_user = $conn->query("
    SELECT u.id, u.first_name, u.last_name, r.name AS role_name
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.id = $logged_in_id
")->fetch_assoc();

if (!$current_user) die(t('user_not_found'));
$is_admin = $current_user['role_name'] === 'admin';

/* ---------- BUILD USERS DROPDOWN ---------- */
$users_dropdown = [
    [
        'id' => $current_user['id'],
        'first_name' => $current_user['first_name'] ?? '',
        'last_name' => $current_user['last_name'] ?? '',
        'role' => $is_admin ? 'admin' : 'member'
    ]
];

/* ---------- THEME ---------- */
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = (int)($theme_row['mode'] ?? 0); // 0=dark,1=light
$theme_class = ($theme === 0) ? 'dark-theme' : 'light-theme';

/* ---------- INPUT ---------- */
$date        = $_GET['date'] ?? null;
$class_id    = (int)($_GET['class_id'] ?? 0);
$subject_id  = (int)($_GET['subject_id'] ?? 0);
$recorded_by = $_GET['recorded_by'] ?? null;
if (!$date || !$class_id || !$subject_id) die(t('invalid_session'));

/* ---------- LOAD SESSION META ---------- */
$session = $conn->query("
    SELECT 
        c.name AS class_name,
        sec.name AS section_name,
        sub.name AS subject_name,
        cs.start_time,
        cs.end_time
    FROM classes c
    LEFT JOIN sections sec ON sec.id = c.section_id
    JOIN subjects sub ON sub.id = $subject_id
    JOIN class_subjects cs ON cs.class_id = c.id AND cs.subject_id = sub.id
    WHERE c.id = $class_id
")->fetch_assoc();

/* ---------- LOAD TEACHERS ---------- */
$teachers = [];
$tr = $conn->query("SELECT id, first_name, last_name FROM teachers ORDER BY first_name");
if ($tr) while ($t = $tr->fetch_assoc()) $teachers[] = $t;

/* ---------- LOAD STUDENTS ---------- */
$students = [];
$res = $conn->query("
    SELECT st.id, st.name, a.id AS attendance_id, a.status
    FROM students st
    JOIN class_students cs ON cs.student_id = st.id AND cs.class_id = $class_id
    LEFT JOIN attendance a
        ON a.student_id = st.id
        AND a.class_id = $class_id
        AND a.subject_id = $subject_id
        AND a.date = '$date'
    ORDER BY st.name
");
if ($res) while ($r = $res->fetch_assoc()) $students[] = $r;

/* ---------- PREPARE STATS ---------- */
$stats = ['present'=>0,'absent'=>0,'late'=>0,'excused'=>0];
foreach ($students as $s) if ($s['status']) $stats[$s['status']]++;
$total = array_sum($stats);
$percent = fn($v) => $total ? round(($v / $total) * 100) : 0;

/* ---------- LOAD HISTORY ---------- */
$history = [];
foreach ($students as $s) {
    if (!$s['attendance_id']) continue;
    $h = $conn->query("
        SELECT old_status, new_status, recorded_by, changed_at
        FROM attendance_history
        WHERE attendance_id = {$s['attendance_id']}
        ORDER BY changed_at DESC
    ");
    $history[$s['id']] = $h ? $h->fetch_all(MYSQLI_ASSOC) : [];
}

/* ---------- HANDLE POST ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $raw = $_POST['recorded_by'] ?? '';
    $new_recorded_by = 'Unknown';
    if (str_starts_with($raw, 'admin_')) {
        $id = (int)str_replace('admin_', '', $raw);
        $u = $conn->query("SELECT first_name,last_name FROM users WHERE id=$id")->fetch_assoc();
        $new_recorded_by = t('label_admin') . ' ' . trim($u['first_name'].' '.$u['last_name']);
    } elseif (str_starts_with($raw, 'member_')) {
        $id = (int)str_replace('member_', '', $raw);
        $u = $conn->query("SELECT first_name,last_name FROM users WHERE id=$id")->fetch_assoc();
        $new_recorded_by = t('label_member') . ' ' . trim($u['first_name'].' '.$u['last_name']);
    } elseif (str_starts_with($raw, 'teacher_')) {
        $id = (int)str_replace('teacher_', '', $raw);
        $t = $conn->query("SELECT first_name,last_name FROM teachers WHERE id=$id")->fetch_assoc();
        $new_recorded_by = t('label_teacher') . ' ' . trim($t['first_name'].' '.$t['last_name']);
    }

    foreach ($_POST['status'] as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $conn->real_escape_string($status);

        $exists = $conn->query("
            SELECT id, status FROM attendance
            WHERE student_id=$student_id AND class_id=$class_id AND subject_id=$subject_id AND date='$date'
        ")->fetch_assoc();

        if ($exists) {
            if ($exists['status'] !== $status || $exists['recorded_by'] !== $new_recorded_by) {
                $conn->query("
                    INSERT INTO attendance_history
                    (attendance_id, student_id, old_status, new_status, recorded_by)
                    VALUES ({$exists['id']}, $student_id, '{$exists['status']}', '$status', '$new_recorded_by')
                ");
            }
            $conn->query("UPDATE attendance SET status='$status', recorded_by='$new_recorded_by' WHERE id={$exists['id']}");
        } else {
            $conn->query("
                INSERT INTO attendance
                (student_id,class_id,subject_id,date,status,recorded_by)
                VALUES ($student_id,$class_id,$subject_id,'$date','$status','$new_recorded_by')
            ");
        }
    }

    header("Location: details.php?date=$date&class_id=$class_id&subject_id=$subject_id&recorded_by=" . urlencode($new_recorded_by));
    exit;
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=JetBrains+Mono:wght@300;400;500&display=swap" rel="stylesheet">
   
</head>
<body class="<?= $theme_class ?>">
    <div class="editor-container">
        <!-- HEADER SECTION -->
        <div class="editor-header">
            <h1 class="editor-title">
                <i class="bi bi-pencil-square"></i>
                <?= t('header_title') ?>
            </h1>
            
            <div class="session-info-grid">
                <div class="info-card shadow-hover">
                    <div class="info-label">
                        <i class="bi bi-building"></i>
                        <?= t('info_class') ?>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($session['class_name']) ?></div>
                </div>
                
                <div class="info-card shadow-hover">
                    <div class="info-label">
                        <i class="bi bi-book"></i>
                        <?= t('info_subject') ?>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($session['subject_name']) ?></div>
                </div>
                
                <?php if($session['section_name']): ?>
                <div class="info-card shadow-hover">
                    <div class="info-label">
                        <i class="bi bi-layers"></i>
                        <?= t('info_section') ?>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($session['section_name']) ?></div>
                </div>
                <?php endif; ?>
                
                <div class="info-card shadow-hover">
                    <div class="info-label">
                        <i class="bi bi-calendar-date"></i>
                        <?= t('info_date') ?>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($date) ?></div>
                </div>
                
                <?php if($session['start_time'] && $session['end_time']): ?>
                <div class="info-card shadow-hover">
                    <div class="info-label">
                        <i class="bi bi-clock"></i>
                        <?= t('info_time_slot') ?>
                    </div>
                    <div class="info-value"><?= htmlspecialchars($session['start_time']) ?> - <?= htmlspecialchars($session['end_time']) ?></div>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- STATISTICS OVERVIEW -->
        <div class="stats-container">
            <?php foreach($stats as $k=>$v): ?>
            <div class="stat-card" data-status="<?= $k ?>">
                <div class="stat-label">
                    <i class="bi bi-<?= t('icon_' . $k) ?>"></i>
                    <?= t('stat_' . $k) ?>
                </div>
                <div class="stat-value"><?= $v ?></div>
                <div class="stat-percentage"><?= $percent($v) ?><?= t('stat_percentage') ?></div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- EDITOR FORM -->
        <form method="post" class="editor-form-wrapper">
            <div class="form-header">
                <div>
                    <h5 class="mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-badge"></i>
                        <?= t('form_title') ?>
                    </h5>
                    <p class="text-primary mb-0"><?= t('form_subtitle') ?></p>
                </div>
                
                <div class="recorder-select-wrapper">
                    <label class="form-label mb-2 d-flex align-items-center gap-2">
                        <i class="bi bi-person-vcard"></i>
                        <?= t('label_recorded_by') ?>
                    </label>
                    <select name="recorded_by" class="recorder-select">
                        <optgroup label="<?= t('optgroup_users') ?>" class="text-secondary">
                        <?php foreach ($users_dropdown as $u): ?>
                            <option value="<?= $u['role'].'_'.$u['id'] ?>" <?= $recorded_by === $u['role'].'_'.$u['id'] ? 'selected' : '' ?>>
                                👤 <?= htmlspecialchars($u['first_name'].' '.$u['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                        </optgroup>
                        <optgroup label="<?= t('optgroup_teachers') ?>" class="text-primary">
                        <?php foreach ($teachers as $t): ?>
                            <option value="teacher_<?= $t['id'] ?>" <?= $recorded_by === 'teacher_'.$t['id'] ? 'selected' : '' ?>>
                                👨‍🏫 <?= htmlspecialchars($t['first_name'].' '.$t['last_name']) ?>
                            </option>
                        <?php endforeach; ?>
                        </optgroup>
                    </select>
                </div>
            </div>

            <!-- STUDENTS TABLE -->
            <div class="students-table-container">
                <div class="table-responsive">
                    <table class="table-modern">
                        <thead>
                            <tr>
                                <th><?= t('table_number') ?></th>
                                <th><?= t('table_student') ?></th>
                                <th><?= t('table_attendance_status') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $i=1; foreach($students as $s):
                                $cls = 'tr-'.$s['status'];
                            ?>
                            <tr class="<?= $cls ?>" data-student-id="<?= $s['id'] ?>">
                                <td>
                                    <span class="text-primary"><?= $i++ ?></span>
                                </td>
                                <td>
                                    <div class="fw-medium mb-1"><?= htmlspecialchars($s['name']) ?></div>
                                    <?php if(!empty($history[$s['id']])): ?>
                                        <button type="button" class="history-btn"
                                            data-bs-toggle="modal" data-bs-target="#historyModal<?= $s['id'] ?>"
                                            title="<?= t('tooltip_history') ?>">
                                            <i class="bi bi-clock-history"></i>
                                            <?= t('history_button') ?>
                                            <span class="history-badge"><?= count($history[$s['id']]) ?></span>
                                        </button>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <select name="status[<?= $s['id'] ?>]" 
                                            class="status-select"
                                            data-status-indicator>
                                        <?php foreach(['present','absent','late','excused'] as $st): ?>
                                            <option value="<?= $st ?>" <?= $s['status']===$st?'selected':'' ?>>
                                                <?= t('option_' . $st) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>

                                    <!-- History Preview -->
                                    <?php if(!empty($history[$s['id']])): ?>
                                    <div class="history-preview">
                                        <div class="history-item">
                                            <span class="history-change">
                                                <?php if(isset($history[$s['id']][0])): ?>
                                                <span class="badge bg-secondary"><?= $history[$s['id']][0]['old_status'] ?: t('history_not_available') ?></span>
                                                <i class="bi bi-arrow-right change-arrow"></i>
                                                <span class="badge bg-primary"><?= $history[$s['id']][0]['new_status'] ?></span>
                                                <?php endif; ?>
                                            </span>
                                            <span class="history-meta">
                                                <?php if(isset($history[$s['id']][0])): ?>
                                                <?= $history[$s['id']][0]['changed_at'] ?>
                                                <?php endif; ?>
                                            </span>
                                        </div>
                                        <?php if(count($history[$s['id']]) > 1): ?>
                                        <div class="text-center mt-2">
                                            <a href="#" class="small d-inline-flex align-items-center gap-1"
                                               data-bs-toggle="modal" data-bs-target="#historyModal<?= $s['id'] ?>">
                                                <i class="bi bi-plus-circle"></i>
                                                <?= t('view_all_changes', ['count' => count($history[$s['id']])]) ?>
                                            </a>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- ACTION BUTTONS -->
            <div class="action-bar">
                <button type="submit" class="btn-modern btn-save">
                    <i class="bi bi-check-circle"></i>
                    <?= t('btn_save_changes') ?>
                </button>
                <a href="javascript:history.back()" class="btn-modern btn-cancel">
                    <i class="bi bi-x-circle"></i>
                    <?= t('btn_cancel') ?>
                </a>
            </div>
        </form>
    </div>

    <!-- HISTORY MODALS -->
    <?php foreach($students as $s): if(empty($history[$s['id']])) continue; ?>
    <div class="modal fade" id="historyModal<?= $s['id'] ?>" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-clock-history me-2"></i>
                        <?= t('history_modal_title') ?> – <?= htmlspecialchars($s['name']) ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="history-table">
                            <thead>
                                <tr>
                                    <th><?= t('history_table_number') ?></th>
                                    <th><?= t('history_table_previous') ?></th>
                                    <th><?= t('history_table_new') ?></th>
                                    <th><?= t('history_table_edited_by') ?></th>
                                    <th><?= t('history_table_date_time') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $i=1; foreach($history[$s['id']] as $h): ?>
                                <tr>
                                    <td><?= $i++ ?></td>
                                    <td>
                                        <span class="badge bg-secondary">
                                            <?= $h['old_status'] ?: t('history_not_available') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary">
                                            <?= $h['new_status'] ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($h['recorded_by']) ?></td>
                                    <td>
                                        <span class="text-muted"><?= $h['changed_at'] ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>
                        <?= t('btn_close') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // JavaScript translations
        const jsTranslations = {
            btn_saving: "<?= t('btn_saving') ?>",
            btn_saving_with_count: "<?= t('btn_saving_with_count', ['count' => '{count}']) ?>"
        };
        
        // Enhanced UI Interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Status select real-time feedback
            document.querySelectorAll('.status-select').forEach(select => {
                const originalValue = select.value;
                
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    const status = this.value;
                    
                    // Remove existing status classes
                    ['tr-present', 'tr-absent', 'tr-late', 'tr-excused'].forEach(cls => {
                        row.classList.remove(cls);
                    });
                    
                    // Add new status class with animation
                    row.classList.add(`tr-${status}`);
                    row.style.animation = 'none';
                    setTimeout(() => {
                        row.style.animation = 'slideInRow 0.3s ease-out';
                    }, 10);
                    
                    // Visual feedback
                    this.style.transform = 'scale(1.05)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
            
            // Form submission enhancement
            const form = document.querySelector('form');
            const saveBtn = form.querySelector('button[type="submit"]');
            
            form.addEventListener('submit', function(e) {
                // Add loading state
                const originalContent = saveBtn.innerHTML;
                saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> ' + jsTranslations.btn_saving;
                saveBtn.disabled = true;
                
                // Count changes
                const changes = document.querySelectorAll('.status-select[data-original-value]').length;
                if (changes > 0) {
                    setTimeout(() => {
                        saveBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> ' + 
                                          jsTranslations.btn_saving_with_count.replace('{count}', changes);
                    }, 1000);
                }
            });
            
            // Store original values
            document.querySelectorAll('.status-select').forEach(select => {
                select.dataset.originalValue = select.value;
            });
            
            // Highlight unsaved changes
            document.addEventListener('change', function(e) {
                if (e.target.classList.contains('status-select')) {
                    const select = e.target;
                    if (select.value !== select.dataset.originalValue) {
                        select.style.borderColor = '#6366f1';
                        select.style.boxShadow = '0 0 0 2px rgba(99, 102, 241, 0.2)';
                    } else {
                        select.style.borderColor = '';
                        select.style.boxShadow = '';
                    }
                }
            });
            
            // Quick navigation with arrow keys
            document.addEventListener('keydown', function(e) {
                const focused = document.activeElement;
                if (focused.classList.contains('status-select')) {
                    const currentRow = focused.closest('tr');
                    const allRows = Array.from(document.querySelectorAll('tr[data-student-id]'));
                    const currentIndex = allRows.indexOf(currentRow);
                    
                    if (e.key === 'ArrowDown' && currentIndex < allRows.length - 1) {
                        e.preventDefault();
                        allRows[currentIndex + 1].querySelector('.status-select').focus();
                    }
                    
                    if (e.key === 'ArrowUp' && currentIndex > 0) {
                        e.preventDefault();
                        allRows[currentIndex - 1].querySelector('.status-select').focus();
                    }
                }
            });
            
            // Add hover effects to stat cards
            document.querySelectorAll('.stat-card').forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-6px) scale(1.02)';
                });
                
                card.addEventListener('mouseleave', () => {
                    card.style.transform = 'translateY(0) scale(1)';
                });
            });
            
            // Auto-focus first select on page load
            setTimeout(() => {
                const firstSelect = document.querySelector('.status-select');
                if (firstSelect) {
                    firstSelect.focus();
                }
            }, 500);
            
            // Add click animation to history buttons
            document.querySelectorAll('.history-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 200);
                });
            });
            
            // RTL-specific adjustments for Arabic
            const isRTL = document.documentElement.dir === 'rtl';
            if (isRTL) {
                // Adjust arrow key navigation for RTL
                document.addEventListener('keydown', function(e) {
                    const focused = document.activeElement;
                    if (focused.classList.contains('status-select')) {
                        const currentRow = focused.closest('tr');
                        const allRows = Array.from(document.querySelectorAll('tr[data-student-id]'));
                        const currentIndex = allRows.indexOf(currentRow);
                        
                        // In RTL, ArrowLeft moves down, ArrowRight moves up
                        if (e.key === 'ArrowLeft' && currentIndex < allRows.length - 1) {
                            e.preventDefault();
                            allRows[currentIndex + 1].querySelector('.status-select').focus();
                        }
                        
                        if (e.key === 'ArrowRight' && currentIndex > 0) {
                            e.preventDefault();
                            allRows[currentIndex - 1].querySelector('.status-select').focus();
                        }
                    }
                });
                
                // Adjust modal arrow direction
                document.querySelectorAll('.change-arrow').forEach(arrow => {
                    arrow.className = 'bi bi-arrow-left change-arrow';
                });
            }
        });
    </script>
</body>
</html>

 <style>
        /* ====== CUSTOM DESIGN SYSTEM ====== */
        :root {
            --primary-gradient: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            --success-gradient: linear-gradient(135deg, #10b981 0%, #059669 100%);
            --danger-gradient: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            --warning-gradient: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
            --info-gradient: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --shadow-elevated: 0 20px 60px rgba(0, 0, 0, 0.15);
            --shadow-floating: 0 10px 40px rgba(0, 0, 0, 0.1);
            --transition-smooth: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --radius-xl: 24px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
        }

        .dark-theme {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --border-color: rgba(148, 163, 184, 0.1);
            --glass-bg: rgba(15, 23, 42, 0.6);
            --glass-border: rgba(148, 163, 184, 0.1);
        }

        .light-theme {
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-color: rgba(203, 213, 225, 0.3);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(0, 0, 0, 0.05);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            transition: var(--transition-smooth);
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 400px;
            background: var(--primary-gradient);
            opacity: 0.03;
            z-index: -1;
            clip-path: polygon(0 0, 100% 0, 100% 30%, 0 60%);
        }

        /* ====== MAIN CONTAINER ====== */
        .editor-container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 2rem 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* ====== HEADER SECTION ====== */
        .editor-header {
            margin-bottom: 3rem;
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .editor-title {
            font-size: 2.75rem;
            font-weight: 800;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .session-info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .info-card {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            padding: 1.5rem;
            transition: var(--transition-smooth);
        }

        .info-card:hover {
            transform: translateY(-4px);
            border-color: rgba(99, 102, 241, 0.3);
        }

        .info-label {
            font-size: 0.875rem;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .info-value {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--text-primary);
        }

        /* ====== STATISTICS CARDS ====== */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            padding: 2rem;
            border-radius: var(--radius-lg);
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
            border: 1px solid transparent;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.8), transparent);
        }

        .stat-card[data-status="present"] { background: var(--success-gradient); }
        .stat-card[data-status="absent"] { background: var(--danger-gradient); }
        .stat-card[data-status="late"] { background: var(--warning-gradient); }
        .stat-card[data-status="excused"] { background: var(--info-gradient); }

        .stat-card:hover {
            transform: translateY(-6px) scale(1.02);
            box-shadow: var(--shadow-elevated);
        }

        .stat-value {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 0.25rem;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .stat-percentage {
            font-size: 0.875rem;
            font-weight: 500;
            color: rgba(255, 255, 255, 0.8);
        }

        /* ====== EDITOR FORM ====== */
        .editor-form-wrapper {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-xl);
            padding: 2.5rem;
            margin-bottom: 3rem;
            box-shadow: var(--shadow-floating);
            transition: var(--transition-smooth);
        }

        .editor-form-wrapper:hover {
            box-shadow: var(--shadow-elevated);
        }

        .form-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid var(--border-color);
        }

        .recorder-select-wrapper {
            min-width: 320px;
            position: relative;
        }

        .recorder-select {
            width: 100%;
            padding: 1rem 1.25rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition-smooth);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%236366f1' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
        }

        .recorder-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        /* ====== STUDENTS TABLE ====== */
        .students-table-container {
            margin-top: 2rem;
            animation: fadeIn 0.8s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .table-modern {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            background: transparent;
        }

        .table-modern thead {
            background: rgba(99, 102, 241, 0.05);
            backdrop-filter: blur(10px);
        }

        .table-modern th {
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            color: var(--text-secondary);
            border-bottom: 2px solid var(--border-color);
        }

        .table-modern tbody tr {
            transition: var(--transition-smooth);
            animation: slideInRow 0.5s ease-out;
            animation-fill-mode: both;
        }

        @keyframes slideInRow {
            from {
                opacity: 0;
                transform: translateX(-10px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .table-modern tbody tr:hover {
            background: rgba(99, 102, 241, 0.03);
            transform: translateX(4px);
        }

        .table-modern td {
            padding: 1.25rem 1.5rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        /* Status Row Styles */
        .tr-present {
            background: linear-gradient(90deg, rgba(16, 185, 129, 0.03), transparent);
            border-left: 4px solid #10b981;
        }
        .tr-absent {
            background: linear-gradient(90deg, rgba(239, 68, 68, 0.03), transparent);
            border-left: 4px solid #ef4444;
        }
        .tr-late {
            background: linear-gradient(90deg, rgba(245, 158, 11, 0.03), transparent);
            border-left: 4px solid #f59e0b;
        }
        .tr-excused {
            background: linear-gradient(90deg, rgba(14, 165, 233, 0.03), transparent);
            border-left: 4px solid #0ea5e9;
        }

        /* Status Selector */
        .status-select {
            padding: 0.75rem 1rem;
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-weight: 500;
            transition: var(--transition-smooth);
            width: 140px;
            cursor: pointer;
        }

        .status-select:focus {
            outline: none;
            border-color: #6366f1;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        .status-select option[value="present"] { color: #10b981; }
        .status-select option[value="absent"] { color: #ef4444; }
        .status-select option[value="late"] { color: #f59e0b; }
        .status-select option[value="excused"] { color: #0ea5e9; }

        /* History Button */
        .history-btn {
            padding: 0.5rem 1rem;
            border-radius: var(--radius-sm);
            font-weight: 500;
            font-size: 0.875rem;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            background: rgba(99, 102, 241, 0.1);
            border: 1px solid rgba(99, 102, 241, 0.2);
            color: var(--text-primary);
            text-decoration: none;
            transition: var(--transition-smooth);
            margin-left: 0.75rem;
        }

        .history-btn:hover {
            background: rgba(99, 102, 241, 0.2);
            border-color: rgba(99, 102, 241, 0.4);
            transform: translateY(-2px);
        }

        .history-badge {
            background: var(--primary-gradient);
            color: white;
            padding: 0.25rem 0.5rem;
            border-radius: 100px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* History Preview */
        .history-preview {
            margin-top: 1rem;
            padding: 1rem;
            background: rgba(15, 23, 42, 0.3);
            border-left: 3px solid #6366f1;
            border-radius: var(--radius-sm);
            font-size: 0.875rem;
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .history-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.5rem 0;
            border-bottom: 1px solid var(--border-color);
        }

        .history-item:last-child {
            border-bottom: none;
        }

        .history-change {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .change-arrow {
            color: var(--text-secondary);
        }

        .history-meta {
            font-size: 0.75rem;
            color: var(--text-secondary);
        }

        /* ====== ACTION BUTTONS ====== */
        .action-bar {
            display: flex;
            gap: 1rem;
            margin-top: 3rem;
            padding-top: 2rem;
            border-top: 2px solid var(--border-color);
        }

        .btn-modern {
            padding: 1rem 2rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            border: none;
            transition: var(--transition-smooth);
            position: relative;
            overflow: hidden;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.6s;
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        }

        .btn-save {
            background: var(--success-gradient);
            color: white;
        }

        .btn-cancel {
            background: var(--glass-bg);
            border: 1px solid var(--glass-border);
            color: var(--text-primary);
        }

        .btn-cancel:hover {
            background: rgba(99, 102, 241, 0.1);
            border-color: rgba(99, 102, 241, 0.3);
        }

        /* ====== MODAL STYLING ====== */
        .modal-content {
            background: var(--bg-primary);
            border: 1px solid var(--glass-border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-elevated);
        }

        .modal-header {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1), transparent);
            border-bottom: 1px solid var(--border-color);
            padding: 1.5rem 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .history-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .history-table th {
            padding: 1rem;
            background: rgba(99, 102, 241, 0.05);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            color: var(--text-secondary);
        }

        .history-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
        }

        /* ====== ANIMATION DELAYS ====== */
        .table-modern tbody tr:nth-child(1) { animation-delay: 0.1s; }
        .table-modern tbody tr:nth-child(2) { animation-delay: 0.2s; }
        .table-modern tbody tr:nth-child(3) { animation-delay: 0.3s; }
        .table-modern tbody tr:nth-child(4) { animation-delay: 0.4s; }
        .table-modern tbody tr:nth-child(5) { animation-delay: 0.5s; }
        .table-modern tbody tr:nth-child(6) { animation-delay: 0.6s; }
        .table-modern tbody tr:nth-child(7) { animation-delay: 0.7s; }
        .table-modern tbody tr:nth-child(8) { animation-delay: 0.8s; }
        .table-modern tbody tr:nth-child(9) { animation-delay: 0.9s; }
        .table-modern tbody tr:nth-child(10) { animation-delay: 1s; }

        /* ====== RESPONSIVE DESIGN ====== */
        @media (max-width: 1200px) {
            .editor-container {
                padding: 1.5rem;
            }
            
            .editor-title {
                font-size: 2.25rem;
            }
            
            .form-header {
                flex-direction: column;
                gap: 1.5rem;
                align-items: stretch;
            }
            
            .recorder-select-wrapper {
                min-width: 100%;
            }
        }

        @media (max-width: 768px) {
            .editor-title {
                font-size: 1.75rem;
            }
            
            .session-info-grid {
                grid-template-columns: 1fr;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .editor-form-wrapper {
                padding: 1.5rem;
            }
            
            .table-modern {
                display: block;
                overflow-x: auto;
            }
            
            .action-bar {
                flex-direction: column;
            }
            
            .btn-modern {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .editor-container {
                padding: 1rem;
            }
            
            .editor-title {
                font-size: 1.5rem;
            }
            
            .table-modern th,
            .table-modern td {
                padding: 1rem;
            }
            
            .info-card {
                padding: 1rem;
            }
        }

        /* ====== CUSTOM SCROLLBAR ====== */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #8b5cf6 0%, #6366f1 100%);
        }

        /* ====== UTILITY CLASSES ====== */
        .glass-effect {
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
        }

        .text-gradient {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .shadow-hover {
            transition: var(--transition-smooth);
        }

        .shadow-hover:hover {
            box-shadow: var(--shadow-floating);
        }
    </style>