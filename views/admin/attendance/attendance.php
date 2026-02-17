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
        'page_title' => 'Attendance Management | Admin Dashboard',
        'header_title' => 'Attendance Intelligence',
        'header_subtitle' => 'Real-time monitoring & advanced analytics dashboard',
        'new_record' => 'New Attendance Record',
        
        // Statistics Cards
        'stats_present' => 'Present',
        'stats_absent' => 'Absent',
        'stats_late' => 'Late',
        'stats_excused' => 'Excused',
        'stats_classes_marked' => 'Classes Marked',
        'stats_teachers_marked' => 'Teachers Marked',
        'stats_students_recorded' => 'Students Recorded',
        'stats_total_records' => 'Total Records',
        
        // Filter System
        'filter_title' => 'Advanced Filter System',
        'filter_subtitle' => 'Refine attendance data with precision controls',
        'filter_start_date' => 'Start Date',
        'filter_end_date' => 'End Date',
        'filter_classes' => 'Classes',
        'filter_subjects' => 'Subjects',
        'filter_recorded_by' => 'Recorded By',
        'filter_search' => 'Smart Search',
        'filter_search_placeholder' => 'Search by class, subject, or teacher...',
        'filter_all_classes' => 'All Classes',
        'filter_all_subjects' => 'All Subjects',
        'filter_all_teachers' => 'All Teachers',
        'filter_admins' => 'Admins',
        'filter_apply' => 'Apply Filters',
        'filter_reset' => 'Reset Filters',
        
        // Table Headers
        'table_date' => 'Date',
        'table_class' => 'Class',
        'table_section' => 'Section',
        'table_subject' => 'Subject',
        'table_recorded_by' => 'Recorded By',
        'table_total' => 'Total',
        'table_present' => 'Present',
        'table_absent' => 'Absent',
        'table_actions' => 'Actions',
        
        // Action Buttons
        'action_view' => 'View Session',
        'action_edit' => 'Edit Session',
        'action_delete' => 'Delete Session',
        
        // Empty State
        'empty_title' => 'No attendance records found',
        'empty_message' => 'Try adjusting your filters or add new attendance records to get started.',
        'empty_action' => 'Create First Record',
        
        // Delete Confirmation
        'delete_confirm_title' => '⚠️ This will permanently delete this attendance session.',
        'delete_confirm_message' => 'Are you sure?',
        'delete_success' => '✅ Attendance session deleted',
        'delete_error' => '❌ Server error',
        
        // Badge Labels
        'badge_today' => 'Today',
        'badge_future' => 'Future',
        
        // Access Denied
        'access_denied' => 'Access denied',
        
        // Teacher Display
        'teacher_prefix' => 'Teacher #',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة الحضور | لوحة تحكم المسؤول',
        'header_title' => 'ذكاء الحضور',
        'header_subtitle' => 'لوحة تحكم المراقبة والتحليلات المتقدمة في الوقت الحقيقي',
        'new_record' => 'سجل حضور جديد',
        
        // Statistics Cards
        'stats_present' => 'حاضر',
        'stats_absent' => 'غائب',
        'stats_late' => 'متأخر',
        'stats_excused' => 'معذور',
        'stats_classes_marked' => 'فصول مسجلة',
        'stats_teachers_marked' => 'معلمين سجلوا',
        'stats_students_recorded' => 'طلاب مسجلين',
        'stats_total_records' => 'إجمالي السجلات',
        
        // Filter System
        'filter_title' => 'نظام التصفية المتقدم',
        'filter_subtitle' => 'تحسين بيانات الحضور باستخدام عناصر تحكم دقيقة',
        'filter_start_date' => 'تاريخ البدء',
        'filter_end_date' => 'تاريخ الانتهاء',
        'filter_classes' => 'الفصول',
        'filter_subjects' => 'المواد',
        'filter_recorded_by' => 'مسجل بواسطة',
        'filter_search' => 'بحث ذكي',
        'filter_search_placeholder' => 'ابحث حسب الفصل، المادة، أو المعلم...',
        'filter_all_classes' => 'جميع الفصول',
        'filter_all_subjects' => 'جميع المواد',
        'filter_all_teachers' => 'جميع المعلمين',
        'filter_admins' => 'المسؤولين',
        'filter_apply' => 'تطبيق الفلاتر',
        'filter_reset' => 'إعادة تعيين الفلاتر',
        
        // Table Headers
        'table_date' => 'التاريخ',
        'table_class' => 'الفصل',
        'table_section' => 'القسم',
        'table_subject' => 'المادة',
        'table_recorded_by' => 'مسجل بواسطة',
        'table_total' => 'الإجمالي',
        'table_present' => 'حاضر',
        'table_absent' => 'غائب',
        'table_actions' => 'الإجراءات',
        
        // Action Buttons
        'action_view' => 'عرض الجلسة',
        'action_edit' => 'تعديل الجلسة',
        'action_delete' => 'حذف الجلسة',
        
        // Empty State
        'empty_title' => 'لم يتم العثور على سجلات حضور',
        'empty_message' => 'حاول ضبط الفلاتر أو إضافة سجلات حضور جديدة للبدء.',
        'empty_action' => 'إنشاء أول سجل',
        
        // Delete Confirmation
        'delete_confirm_title' => '⚠️ سيؤدي هذا إلى حذف جلسة الحضور هذه بشكل دائم.',
        'delete_confirm_message' => 'هل أنت متأكد؟',
        'delete_success' => '✅ تم حذف جلسة الحضور',
        'delete_error' => '❌ خطأ في الخادم',
        
        // Badge Labels
        'badge_today' => 'اليوم',
        'badge_future' => 'مستقبل',
        
        // Access Denied
        'access_denied' => 'تم رفض الوصول',
        
        // Teacher Display
        'teacher_prefix' => 'معلم #',
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

/* ---------- AUTH ---------- */
$logged_in_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$logged_in_id || !in_array($role, ['admin', 'member'])) die(t('access_denied'));

/* ---------- LOAD FILTER DATA ---------- */
$classes = $conn->query("SELECT id, name FROM classes ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT id, name FROM subjects ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$teachers = $conn->query("SELECT id, first_name, last_name FROM teachers ORDER BY first_name")->fetch_all(MYSQLI_ASSOC);

$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = (int)($theme_row['mode'] ?? 0);
$theme_class = ($theme === 0) ? 'dark-theme' : 'light-theme';

/* ---------- FILTER INPUTS ---------- */
$start_date = $_GET['start_date'] ?? date('Y-m-d');
$end_date   = $_GET['end_date'] ?? date('Y-m-d');
$filter_class   = $_GET['class_id'] ?? [];
$filter_subject = $_GET['subject_id'] ?? [];
$filter_teacher = $_GET['recorded_by'] ?? [];
$search = $_GET['search'] ?? '';

$where = ["a.date BETWEEN ? AND ?"];
$params = [$start_date, $end_date];
$types = "ss";

if (!empty($filter_class)) {
    $ids = array_map('intval', $filter_class);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $where[] = "c.id IN ($placeholders)";
    foreach ($ids as $id) {
        $params[] = $id;
        $types .= "i";
    }
}

if (!empty($filter_subject)) {
    $ids = array_map('intval', $filter_subject);
    $placeholders = implode(',', array_fill(0, count($ids), '?'));
    $where[] = "sub.id IN ($placeholders)";
    foreach ($ids as $id) {
        $params[] = $id;
        $types .= "i";
    }
}

if (!empty($filter_teacher)) {
    $conditions = [];
    $teacher_params = [];
    $teacher_types = "";

    if (in_array('admin', $filter_teacher)) {
        $conditions[] = "a.recorded_by LIKE 'Admin %'";
    }

    $teacherIds = array_filter($filter_teacher, 'is_numeric');
    if ($teacherIds) {
        $ids = array_map('intval', $teacherIds);
        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $conditions[] = "a.recorded_by IN ($placeholders)";
        foreach ($ids as $id) {
            $teacher_params[] = $id;
            $teacher_types .= "i";
        }
    }

    if ($conditions) {
        $where[] = '(' . implode(' OR ', $conditions) . ')';
        foreach ($teacher_params as $tp) {
            $params[] = $tp;
            $types .= "i";
        }
    }
}

if ($search) {
    $where[] = "(c.name LIKE ? OR sub.name LIKE ? OR a.recorded_by LIKE ?)";
    $sterm = "%$search%";
    $params[] = $sterm;
    $params[] = $sterm;
    $params[] = $sterm;
    $types .= "sss";
}

$where_sql = implode(' AND ', $where);

// Attendance summary
$summary_stmt = $conn->prepare("
    SELECT
        SUM(a.status='present') AS present_count,
        SUM(a.status='absent') AS absent_count,
        SUM(a.status='late') AS late_count,
        SUM(a.status='excused') AS excused_count,
        COUNT(DISTINCT a.class_id) AS classes_marked,
        COUNT(DISTINCT a.recorded_by) AS teachers_marked,
        COUNT(DISTINCT a.student_id) AS students_recorded,
        COUNT(*) AS total_records
    FROM attendance a
    JOIN classes c ON c.id = a.class_id
    JOIN subjects sub ON sub.id = a.subject_id
    WHERE $where_sql
");
$summary_stmt->bind_param($types, ...$params);
$summary_stmt->execute();
$summary = $summary_stmt->get_result()->fetch_assoc();

/* ---------- LOAD ATTENDANCE ---------- */
$list_stmt = $conn->prepare("
    SELECT 
        a.date,
        a.class_id,
        a.subject_id,
        a.recorded_by,
        c.name AS class_name,
        s.name AS section_name,
        sub.name AS subject_name,
        COUNT(a.student_id) AS total_students,
        SUM(a.status='present') AS present_count,
        SUM(a.status='absent') AS absent_count
    FROM attendance a
    JOIN classes c ON c.id = a.class_id
    LEFT JOIN sections s ON s.id = a.section_id
    JOIN subjects sub ON sub.id = a.subject_id
    WHERE $where_sql
    GROUP BY a.class_id, a.section_id, a.subject_id, a.date, a.recorded_by
    ORDER BY a.date DESC
");
$list_stmt->bind_param($types, ...$params);
$list_stmt->execute();
$attendance_list = $list_stmt->get_result();

/* ---------- HELPER ---------- */
function displayRecordedBy($conn, $recorded_by) {
    global $currentLangId;
    
    if (!$recorded_by) return '-';
    
    if (str_starts_with($recorded_by, 'Admin ')) return $recorded_by;
    
    // Extract numeric ID if it's in format like "teacher_12"
    if (preg_match('/(\d+)$/', $recorded_by, $matches)) {
        $teacher_id = (int)$matches[1];
        $res = $conn->query("SELECT CONCAT(first_name,' ',last_name) AS name FROM teachers WHERE id=$teacher_id");
        if ($res) {
            $row = $res->fetch_assoc();
            return $row['name'] ?? t('teacher_prefix') . $teacher_id;
        } else {
            return t('teacher_prefix') . $teacher_id;
        }
    }

    return $recorded_by; // fallback
}

?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>
<body class="<?= $theme_class ?>">

<div class="container-fluid">
    <?php foreach (get_flash_messages() as $msg): ?>
    <div class="alert alert-<?= $msg['type'] ?> alert-dismissible fade show glass-card" role="alert">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-<?= $msg['type'] == 'success' ? 'check-circle' : 'exclamation-triangle' ?>"></i>
            <span><?= htmlspecialchars($msg['message']) ?></span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endforeach; ?>

    <!-- HEADER -->
    <div class="header-section">
        <div class="header-title">
            <h3>📊 <?= t('header_title') ?></h3>
            <span class="header-subtitle"><?= t('header_subtitle') ?></span>
        </div>
        <a href="attendance_add.php" class="btn btn-success glass-card">
            <i class="bi bi-plus-circle"></i> <?= t('new_record') ?>
        </a>
    </div>

    <!-- STATISTICS GRID -->
    <div class="stats-grid">
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-check-circle"></i> <?= t('stats_present') ?></h6>
            <h3><?= $summary['present_count'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-x-circle"></i> <?= t('stats_absent') ?></h6>
            <h3><?= $summary['absent_count'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-clock-history"></i> <?= t('stats_late') ?></h6>
            <h3><?= $summary['late_count'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-shield-check"></i> <?= t('stats_excused') ?></h6>
            <h3><?= $summary['excused_count'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-building"></i> <?= t('stats_classes_marked') ?></h6>
            <h3><?= $summary['classes_marked'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-person-badge"></i> <?= t('stats_teachers_marked') ?></h6>
            <h3><?= $summary['teachers_marked'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-people"></i> <?= t('stats_students_recorded') ?></h6>
            <h3><?= $summary['students_recorded'] ?? 0 ?></h3>
        </div>
        <div class="stat-card text-white glass-card">
            <h6><i class="bi bi-database"></i> <?= t('stats_total_records') ?></h6>
            <h3><?= $summary['total_records'] ?? 0 ?></h3>
        </div>
    </div>

    <!-- FILTER SYSTEM -->
    <div class="filter-container glass-card">
        <div class="filter-header">
            <i class="bi bi-funnel"></i>
            <div>
                <h4 class="mb-1"><?= t('filter_title') ?></h4>
                <p class="text-muted mb-0"><?= t('filter_subtitle') ?></p>
            </div>
        </div>
        
        <form method="get">
            <div class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label"><i class="bi bi-calendar"></i> <?= t('filter_start_date') ?></label>
                    <input type="date" name="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label"><i class="bi bi-calendar-check"></i> <?= t('filter_end_date') ?></label>
                    <input type="date" name="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                
                <div class="expand-filter" data-placeholder="<?= t('filter_all_classes') ?>">
                    <div class="expand-header">
                        <span class="expand-title"><i class="bi bi-building"></i> <?= t('filter_classes') ?></span>
                        <span class="expand-summary"><?= t('filter_all_classes') ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="expand-body">
                        <?php foreach ($classes as $c): ?>
                        <label class="expand-item">
                            <input type="checkbox"
                                   name="class_id[]"
                                   value="<?= $c['id'] ?>"
                                   data-label="<?= htmlspecialchars($c['name']) ?>"
                                   <?= in_array($c['id'], (array)$filter_class) ? 'checked' : '' ?>>
                            <?= $c['name'] ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="expand-filter" data-placeholder="<?= t('filter_all_subjects') ?>">
                    <div class="expand-header">
                        <span class="expand-title"><i class="bi bi-book"></i> <?= t('filter_subjects') ?></span>
                        <span class="expand-summary"><?= t('filter_all_subjects') ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="expand-body">
                        <?php foreach ($subjects as $s): ?>
                        <label class="expand-item">
                            <input type="checkbox"
                                   name="subject_id[]"
                                   value="<?= $s['id'] ?>"
                                   data-label="<?= htmlspecialchars($s['name']) ?>"
                                   <?= in_array($s['id'], (array)$filter_subject) ? 'checked' : '' ?>>
                            <?= $s['name'] ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="expand-filter" data-placeholder="<?= t('filter_all_teachers') ?>">
                    <div class="expand-header">
                        <span class="expand-title"><i class="bi bi-person-vcard"></i> <?= t('filter_recorded_by') ?></span>
                        <span class="expand-summary"><?= t('filter_all_teachers') ?></span>
                        <i class="bi bi-chevron-down"></i>
                    </div>
                    <div class="expand-body">
                        <label class="expand-item">
                            <input type="checkbox"
                                   name="recorded_by[]"
                                   value="admin"
                                   data-label="<?= t('filter_admins') ?>"
                                   <?= in_array('admin', (array)$filter_teacher) ? 'checked' : '' ?>>
                            <?= t('filter_admins') ?>
                        </label>
                        <?php foreach ($teachers as $t): ?>
                        <label class="expand-item">
                            <input type="checkbox"
                                   name="recorded_by[]"
                                   value="<?= $t['id'] ?>"
                                   data-label="<?= $t['first_name'].' '.$t['last_name'] ?>"
                                   <?= in_array($t['id'], (array)$filter_teacher) ? 'checked' : '' ?>>
                            <?= $t['first_name'].' '.$t['last_name'] ?>
                        </label>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label"><i class="bi bi-search"></i> <?= t('filter_search') ?></label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="<?= t('filter_search_placeholder') ?>" 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
            </div>
            
            <div class="d-flex justify-content-end gap-2">
                 <button type="submit" class="btn btn-dark">
                    <i class="bi bi-funnel-fill"></i> <?= t('filter_apply') ?>
                </button>
                <a href="?" class="btn btn-outline-primary">
                    <i class="bi bi-arrow-clockwise"></i> <?= t('filter_reset') ?>
                </a>
            </div>
        </form>
    </div>

    <!-- DATA TABLE -->
    <div class="data-container glass-card">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><i class="bi bi-calendar"></i> <?= t('table_date') ?></th>
                        <th><i class="bi bi-building"></i> <?= t('table_class') ?></th>
                        <th><i class="bi bi-layers"></i> <?= t('table_section') ?></th>
                        <th><i class="bi bi-book"></i> <?= t('table_subject') ?></th>
                        <th><i class="bi bi-person"></i> <?= t('table_recorded_by') ?></th>
                        <th><i class="bi bi-people"></i> <?= t('table_total') ?></th>
                        <th><i class="bi bi-check-circle"></i> <?= t('table_present') ?></th>
                        <th><i class="bi bi-x-circle"></i> <?= t('table_absent') ?></th>
                        <th><i class="bi bi-gear"></i> <?= t('table_actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($attendance_list->num_rows): ?>
                        <?php while($row = $attendance_list->fetch_assoc()): ?>
                        <tr>
                            <td data-label="<?= t('table_date') ?>">
                                <span class="fw-semibold"><?= $row['date'] ?></span>
                            </td>
                            <td data-label="<?= t('table_class') ?>"><?= $row['class_name'] ?></td>
                            <td data-label="<?= t('table_section') ?>"><?= $row['section_name'] ?? '-' ?></td>
                            <td data-label="<?= t('table_subject') ?>"><?= $row['subject_name'] ?></td>
                            <td data-label="<?= t('table_recorded_by') ?>"><?= displayRecordedBy($conn, $row['recorded_by']) ?></td>
                            <td data-label="<?= t('table_total') ?>">
                                <span class="badge bg-secondary"><?= $row['total_students'] ?></span>
                            </td>
                            <td data-label="<?= t('table_present') ?>">
                                <span class="badge bg-success"><?= $row['present_count'] ?></span>
                            </td>
                            <td data-label="<?= t('table_absent') ?>">
                                <span class="badge bg-danger"><?= $row['absent_count'] ?></span>
                            </td>
                            <td data-label="<?= t('table_actions') ?>">
                               <div class="action-buttons d-flex gap-1">
                                    <!-- View -->
                                    <a href="details.php
                                        ?date=<?= urlencode($row['date']) ?>
                                        &class_id=<?= $row['class_id'] ?>
                                        &subject_id=<?= $row['subject_id'] ?>
                                        &recorded_by=<?= urlencode($row['recorded_by']) ?>"
                                       class="btn btn-sm btn-info"
                                       title="<?= t('action_view') ?>">
                                        <i class="bi bi-eye"></i>
                                    </a>

                                    <!-- Edit -->
                                   <a href="edit_session.php?<?= http_build_query([
                                        'date'        => $row['date'],
                                        'class_id'    => $row['class_id'],
                                        'subject_id'  => $row['subject_id'],
                                        'recorded_by' => $row['recorded_by']
                                    ]) ?>"
                                   class="btn btn-sm btn-gradient"
                                   title="<?= t('action_edit') ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </a>

                                    <!-- Delete -->
                                    <button type="button"
                                        class="btn btn-sm btn-danger"
                                        title="<?= t('action_delete') ?>"
                                        onclick="deleteAttendance(
                                            '<?= $row['date'] ?>',
                                            <?= $row['class_id'] ?>,
                                            <?= $row['subject_id'] ?>,
                                            '<?= addslashes($row['recorded_by']) ?>'
                                        )">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="bi bi-clipboard-x"></i>
                                <h4><?= t('empty_title') ?></h4>
                                <p><?= t('empty_message') ?></p>
                                <a href="attendance_add.php" class="btn btn-primary mt-3">
                                    <i class="bi bi-plus-circle"></i> <?= t('empty_action') ?>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script>
    // Enhanced animations and interactions
    document.addEventListener('DOMContentLoaded', function() {
        // Animate table rows with staggered delay
        const tableRows = document.querySelectorAll('.table tbody tr');
        tableRows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.03}s`;
            row.classList.add('animate__animated', 'animate__fadeIn');
        });
        
        // Add floating animation to stats cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', () => {
                card.style.transform = 'translateY(-8px) scale(1.02)';
            });
            
            card.addEventListener('mouseleave', () => {
                card.style.transform = 'translateY(0) scale(1)';
            });
        });
        
        // Enhanced filter dropdowns
        document.querySelectorAll('.expand-filter').forEach(filter => {
            const header = filter.querySelector('.expand-header');
            const summary = filter.querySelector('.expand-summary');
            const checkboxes = filter.querySelectorAll('input[type="checkbox"]');
            const placeholder = filter.dataset.placeholder;
            const icon = header.querySelector('.bi-chevron-down');

            function updateSummary() {
                const selected = Array.from(checkboxes)
                    .filter(c => c.checked)
                    .map(c => c.dataset.label);

                if (!selected.length) {
                    summary.textContent = placeholder;
                } else if (selected.length <= 2) {
                    summary.textContent = selected.join(', ');
                } else {
                    summary.textContent = `${selected[0]} +${selected.length - 1}`;
                }
            }

            updateSummary();

            header.addEventListener('click', () => {
                const isOpen = filter.classList.toggle('open');
                icon.classList.toggle('bi-chevron-down', !isOpen);
                icon.classList.toggle('bi-chevron-up', isOpen);
                
                if (isOpen) {
                    filter.style.zIndex = '1000';
                } else {
                    setTimeout(() => {
                        filter.style.zIndex = '';
                    }, 300);
                }
            });

            // Close dropdown when clicking outside
            document.addEventListener('click', (e) => {
                if (!filter.contains(e.target) && filter.classList.contains('open')) {
                    filter.classList.remove('open');
                    icon.classList.remove('bi-chevron-up');
                    icon.classList.add('bi-chevron-down');
                }
            });

            checkboxes.forEach(cb => {
                cb.addEventListener('change', function() {
                    updateSummary();
                    
                    // Add visual feedback on selection
                    const label = this.closest('.expand-item');
                    label.style.animation = 'none';
                    setTimeout(() => {
                        label.style.animation = 'pulse 0.3s ease';
                    }, 10);
                });
            });
        });
        
        // Form input focus effects
        const formControls = document.querySelectorAll('.form-control, .form-select');
        formControls.forEach(control => {
            control.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            control.addEventListener('blur', function() {
                if (!this.value) {
                    this.parentElement.classList.remove('focused');
                }
            });
        });
        
        // Add smooth scroll behavior
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
        
        // Theme transition enhancement
        document.body.style.transition = 'background-color 0.5s ease, color 0.5s ease';
        
        // Add loading state to buttons on form submission
        const form = document.querySelector('form');
        const submitBtn = form.querySelector('button[type="submit"]');
        
        form.addEventListener('submit', function() {
            submitBtn.innerHTML = '<i class="bi bi-hourglass-split"></i> Processing...';
            submitBtn.disabled = true;
            submitBtn.classList.add('loading');
        });
    });
    
    // Real-time date formatting
    document.addEventListener('DOMContentLoaded', function() {
        const dates = document.querySelectorAll('td[data-label="<?= t('table_date') ?>"] .fw-semibold');
        dates.forEach(dateElement => {
            const dateStr = dateElement.textContent;
            const date = new Date(dateStr);
            const today = new Date();
            
            if (date.toDateString() === today.toDateString()) {
                dateElement.innerHTML += ' <span class="badge bg-primary"><?= t('badge_today') ?></span>';
            } else if (date > today) {
                dateElement.innerHTML += ' <span class="badge bg-warning"><?= t('badge_future') ?></span>';
            }
        });
    });
    
    // Delete function with translations
    function deleteAttendance(date, classId, subjectId, recordedBy) {
        const confirmMessage = `<?= t('delete_confirm_title') ?>\n\n<?= t('delete_confirm_message') ?>`;
        
        if (!confirm(confirmMessage)) {
            return;
        }

        fetch('attendance_delete.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                date: date,
                class_id: classId,
                subject_id: subjectId,
                recorded_by: recordedBy
            })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                alert('<?= t('delete_success') ?>');
                location.reload();
            } else {
                alert('❌ ' + data.message);
            }
        })
        .catch(() => alert('<?= t('delete_error') ?>'));
    }
</script>

<style>
    :root {
        --success-gradient: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        --danger-gradient: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
        --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        --dark-gradient: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
        --glass-bg: rgba(255, 255, 255, 0.1);
        --glass-border: rgba(255, 255, 255, 0.2);
        --shadow-elevated: 0 20px 40px rgba(0, 0, 0, 0.1);
        --shadow-floating: 0 8px 32px rgba(0, 0, 0, 0.08);
        --transition-smooth: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .dark-theme {
        --glass-bg: rgba(15, 23, 42, 0.7);
        --glass-border: rgba(148, 163, 184, 0.1);
        --shadow-elevated: 0 20px 40px rgba(0, 0, 0, 0.3);
        --primary-gradient: linear-gradient(135deg, #0f172a 0%, #020617 100%);
        --shadow-floating: 0 8px 32px rgba(0, 0, 0, 0.2);
    }

        .dark-theme {
            color: #e5e7eb;              /* main text */
            }

            .dark-theme .header-subtitle,
            .dark-theme .filter-label,
            .dark-theme .expand-summary,
            .dark-theme .text-muted {
                color: #94a3b8;              /* muted text */
            }
            .dark-theme .expand-body{
                color: black;
            }
            .dark-theme h1,
            .dark-theme h2,
            .dark-theme h3,
            .dark-theme h4,
            .dark-theme h5,
            .dark-theme h6 {
                color: #f8fafc;              /* headings */
            }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--primary-gradient);
            min-height: 100vh;
            transition: var(--transition-smooth);
        }

        .container-fluid {
            padding: 2rem;
            max-width: 1920px;
            margin: 0 auto;
        }

        /* Glass Morphism Effects */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            box-shadow: var(--shadow-elevated);
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .glass-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-floating);
        }

        /* Animated Header */
        .header-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 3rem;
            padding: 2rem;
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            border: 1px solid var(--glass-border);
            animation: slideDown 0.6s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .header-title h3 {
            font-weight: 700;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 0.5rem;
        }

        .header-subtitle {
            color: #64748b;
            font-size: 1rem;
            font-weight: 500;
        }

        /* Statistics Cards Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 1.5rem;
            margin-bottom: 3rem;
        }

        .stat-card {
            padding: 2rem;
            border-radius: 20px;
            position: relative;
            overflow: hidden;
            transition: var(--transition-smooth);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.8), transparent);
        }

        .stat-card:nth-child(1) { background: var(--success-gradient); }
        .stat-card:nth-child(2) { background: var(--danger-gradient); }
        .stat-card:nth-child(3) { background: var(--warning-gradient); }
        .stat-card:nth-child(4) { background: var(--info-gradient); }
        .stat-card:nth-child(5) { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-card:nth-child(6) { background: linear-gradient(135deg, #868f96 0%, #596164 100%); }
        .stat-card:nth-child(7) { background: var(--dark-gradient); }
        .stat-card:nth-child(8) { background: linear-gradient(135deg, #00c9ff 0%, #92fe9d 100%); }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 20px 40px rgba(0,0,0,0.2);
        }

        .stat-card h6 {
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .stat-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0;
        }

        /* Advanced Filter System */
        .filter-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem;
            margin-bottom: 3rem;
            border: 1px solid var(--glass-border);
        }

        .filter-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .filter-header i {
            font-size: 1.5rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .filter-group {
            position: relative;
        }

        .filter-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #64748b;
            font-size: 0.875rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .form-control, .form-select {
            padding: 0.875rem 1rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: var(--transition-smooth);
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus, .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        /* Expandable Filter Dropdowns */
        .expand-filter {
            position: relative;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            background: rgba(255, 255, 255, 0.9);
            cursor: pointer;
            transition: var(--transition-smooth);
        }

        .expand-filter:hover {
            border-color: #667eea;
            transform: translateY(-2px);
        }

        .expand-filter.open {
            border-color: #667eea;
            box-shadow: 0 8px 32px rgba(102, 126, 234, 0.15);
        }

        .expand-header {
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .expand-title {
            font-weight: 600;
            color: #334155;
        }

        .expand-summary {
            color: #64748b;
            font-size: 0.875rem;
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .expand-body {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            padding: 0 1rem;
        }

        .expand-filter.open .expand-body {
            max-height: 300px;
            padding: 1rem;
            border-top: 1px solid #e2e8f0;
        }

        .expand-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            border-radius: 8px;
            transition: var(--transition-smooth);
            cursor: pointer;
        }

        .expand-item:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        .expand-item input[type="checkbox"] {
            width: 18px;
            height: 18px;
            border-radius: 4px;
            cursor: pointer;
        }

        /* Data Table Redesign */
        .data-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border-radius: 24px;
            padding: 2.5rem;
            border: 1px solid var(--glass-border);
        }

        .table-responsive {
            border-radius: 16px;
            overflow: hidden;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table thead th {
            background: rgba(102, 126, 234, 0.1);
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
            color: #64748b;
            border-bottom: 2px solid #e2e8f0;
        }

        .table tbody tr {
            transition: var(--transition-smooth);
            animation: fadeIn 0.5s ease-out;
            animation-fill-mode: both;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .table tbody tr:hover {
            background: rgba(102, 126, 234, 0.05);
            transform: translateX(4px);
        }

        .table tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid rgba(226, 232, 240, 0.3);
        }

        /* Badge Redesign */
        .badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.75rem;
            letter-spacing: 0.5px;
            border: 2px solid transparent;
        }

        .bg-success { 
            background: var(--success-gradient) !important;
            color: white !important;
        }
        .bg-danger { 
            background: var(--danger-gradient) !important;
            color: white !important;
        }
        .bg-secondary { 
            background: var(--dark-gradient) !important;
            color: white !important;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.625rem 1.25rem;
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition-smooth);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
        }

        .btn-primary {
            background: var(--primary-gradient);
            color: white;
        }

        .btn-outline-primary {
            border: 2px solid #667eea;
            color: #667eea;
            background: transparent;
        }

        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state i {
            font-size: 4rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 1.5rem;
        }

        .empty-state h4 {
            font-weight: 700;
            color: #334155;
            margin-bottom: 0.75rem;
        }

        .empty-state p {
            color: #64748b;
            max-width: 400px;
            margin: 0 auto;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .container-fluid {
                padding: 1.5rem;
            }
            
            .header-title h3 {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container-fluid {
                padding: 1rem;
            }
            
            .header-section {
                flex-direction: column;
                text-align: center;
                gap: 1.5rem;
                padding: 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .data-container {
                padding: 1.5rem;
            }
            
            .table-responsive {
                border-radius: 12px;
            }
        }

        @media (max-width: 576px) {
            .table thead {
                display: none;
            }
            
            .table tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid rgba(226, 232, 240, 0.3);
                border-radius: 12px;
                padding: 1rem;
            }
            
            .table tbody td {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0.75rem 0;
                border-bottom: 1px solid rgba(226, 232, 240, 0.1);
            }
            
            .table tbody td::before {
                content: attr(data-label);
                font-weight: 600;
                color: #64748b;
                font-size: 0.875rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 10px;
            height: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        }

        /* Loading Animation */
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.5; }
        }

        .loading {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</body>
</html>
