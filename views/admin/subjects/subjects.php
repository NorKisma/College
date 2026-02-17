<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Subjects Management | Academic System',
        'page_subtitle' => 'Manage all academic subjects, assignments, and schedules',
        'new_subject' => 'New Subject',
        'basic_view' => 'Basic',
        'detailed_view' => 'Detailed',
        'total_subjects' => 'Total Subjects',
        'active_teachers' => 'Active Teachers',
        'available_classes' => 'Available Classes',
        'search_subjects' => 'Search subjects by name, code...',
        'filter' => 'Filter',
        'clear' => 'Clear',
        'subjects_list' => 'Subjects List',
        'showing_of' => 'Showing of',
        'subjects' => 'subjects',
        'code' => 'Code',
        'subject' => 'Subject',
        'hours' => 'Hours',
        'assigned_classes' => 'Assigned Classes',
        'teachers' => 'Teachers',
        'created' => 'Created',
        'actions' => 'Actions',
        'no_subjects_found' => 'No Subjects Found',
        'no_subjects_match' => 'No subjects match your search criteria. Try different keywords or create a new subject.',
        'create_first_subject' => 'Create First Subject',
        'edit' => 'Edit',
        'assign_to_class' => 'Assign to Class',
        'delete' => 'Delete',
        'view_assignments' => 'View Assignments',
        'delete_confirmation' => 'Are you sure you want to delete this subject? This action cannot be undone.',
        'create_new_subject' => 'Create New Subject',
        'subject_code' => 'Subject Code',
        'subject_code_placeholder' => 'e.g., MATH101',
        'subject_code_hint' => 'A unique identifier for the subject',
        'subject_name' => 'Subject Name',
        'subject_name_placeholder' => 'e.g., Mathematics',
        'credit_hours' => 'Credit Hours',
        'credit_hours_hint' => 'Enter the credit hours for this subject',
        'cancel' => 'Cancel',
        'create_subject' => 'Create Subject',
        'edit_subject' => 'Edit Subject',
        'save_changes' => 'Save Changes',
        'teaching_schedule' => 'Teaching Schedule',
        'day_of_week' => 'Day of Week',
        'select_day' => 'Select Day',
        'start_time' => 'Start Time',
        'end_time' => 'End Time',
        'assign_subject_to_class' => 'Assign Subject to Class',
        'select_class' => 'Select Class',
        'assign_teacher' => 'Assign Teacher',
        'no_teacher_assigned' => 'No Teacher Assigned',
        'class_assign_info' => 'This will assign the subject to the selected class. You can optionally assign a teacher.',
        'assign_to_class' => 'Assign to Class',
        'class_assignments' => 'Class Assignments',
        'assignments_for' => 'Assignments for',
        'no_class_assignments' => 'No class assignments found for this subject.',
        'remove_assignment' => 'Remove this assignment?',
        'remove' => 'Remove',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
        'required_field' => 'Required field',
        'optional' => 'Optional',
        'not_assigned' => 'Not assigned',
        'no_teacher' => 'No teacher',
        'loading' => 'Loading...',
        'subject_id' => 'Subject ID',
        'teacher' => 'Teacher',
        'assigned' => 'Assigned',
        'invalid_session' => 'Invalid session',
        'permission_denied' => 'Permission denied',
        'invalid_csrf' => 'Invalid CSRF token',
        'subject_created' => 'Subject created successfully',
        'subject_updated' => 'Subject updated successfully',
        'subject_deleted' => 'Subject deleted successfully',
        'assignment_removed' => 'Class assignment removed',
        'subject_assigned' => 'Subject assigned to class successfully',
        'already_assigned' => 'This subject is already assigned to the selected class',
        'invalid_input' => 'Invalid input',
        'failed_to_create' => 'Failed to create subject',
        'delete_failed' => 'Delete failed',
        'failed_to_assign' => 'Failed to assign subject to class',
        'class_time_required' => 'Class, day and time are required',
        'invalid_credit_hours' => 'Invalid credit hours value',
        'subject_name_required' => 'Subject name is required'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة المواد الدراسية | النظام الأكاديمي',
        'page_subtitle' => 'إدارة جميع المواد الدراسية، المهام والجداول',
        'new_subject' => 'مادة جديدة',
        'basic_view' => 'بسيط',
        'detailed_view' => 'مفصل',
        'total_subjects' => 'إجمالي المواد',
        'active_teachers' => 'المعلمون النشطون',
        'available_classes' => 'الصفوف المتاحة',
        'search_subjects' => 'البحث عن المواد بالاسم، الرمز...',
        'filter' => 'تصفية',
        'clear' => 'مسح',
        'subjects_list' => 'قائمة المواد',
        'showing_of' => 'عرض من',
        'subjects' => 'مواد',
        'code' => 'الرمز',
        'subject' => 'المادة',
        'hours' => 'الساعات',
        'assigned_classes' => 'الصفوف المخصصة',
        'teachers' => 'المعلمون',
        'created' => 'تاريخ الإنشاء',
        'actions' => 'الإجراءات',
        'no_subjects_found' => 'لم يتم العثور على مواد',
        'no_subjects_match' => 'لا توجد مواد تطابق معايير البحث. جرب كلمات مختلفة أو أنشئ مادة جديدة.',
        'create_first_subject' => 'إنشاء أول مادة',
        'edit' => 'تعديل',
        'assign_to_class' => 'تعيين إلى صف',
        'delete' => 'حذف',
        'view_assignments' => 'عرض المهام',
        'delete_confirmation' => 'هل أنت متأكد من حذف هذه المادة؟ لا يمكن التراجع عن هذا الإجراء.',
        'create_new_subject' => 'إنشاء مادة جديدة',
        'subject_code' => 'رمز المادة',
        'subject_code_placeholder' => 'مثال: MATH101',
        'subject_code_hint' => 'معرف فريد للمادة',
        'subject_name' => 'اسم المادة',
        'subject_name_placeholder' => 'مثال: الرياضيات',
        'credit_hours' => 'ساعات المعتمدة',
        'credit_hours_hint' => 'أدخل ساعات المعتمدة لهذه المادة',
        'cancel' => 'إلغاء',
        'create_subject' => 'إنشاء المادة',
        'edit_subject' => 'تعديل المادة',
        'save_changes' => 'حفظ التغييرات',
        'teaching_schedule' => 'جدول التدريس',
        'day_of_week' => 'يوم الأسبوع',
        'select_day' => 'اختر اليوم',
        'start_time' => 'وقت البدء',
        'end_time' => 'وقت الانتهاء',
        'assign_subject_to_class' => 'تعيين المادة إلى صف',
        'select_class' => 'اختر الصف',
        'assign_teacher' => 'تعيين معلم',
        'no_teacher_assigned' => 'لم يتم تعيين معلم',
        'class_assign_info' => 'سيتم تعيين المادة إلى الصف المختار. يمكنك اختيارياً تعيين معلم.',
        'assign_to_class' => 'تعيين إلى الصف',
        'class_assignments' => 'مهام الصف',
        'assignments_for' => 'المهام الخاصة بـ',
        'no_class_assignments' => 'لم يتم العثور على مهام صف لهذه المادة.',
        'remove_assignment' => 'إزالة هذه المهمة؟',
        'remove' => 'إزالة',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
        'sunday' => 'الأحد',
        'required_field' => 'حقل مطلوب',
        'optional' => 'اختياري',
        'not_assigned' => 'غير معين',
        'no_teacher' => 'لا يوجد معلم',
        'loading' => 'جاري التحميل...',
        'subject_id' => 'معرف المادة',
        'teacher' => 'المعلم',
        'assigned' => 'معين',
        'invalid_session' => 'جلسة غير صالحة',
        'permission_denied' => 'تم رفض الإذن',
        'invalid_csrf' => 'رمز CSRF غير صالح',
        'subject_created' => 'تم إنشاء المادة بنجاح',
        'subject_updated' => 'تم تحديث المادة بنجاح',
        'subject_deleted' => 'تم حذف المادة بنجاح',
        'assignment_removed' => 'تم إزالة مهمة الصف',
        'subject_assigned' => 'تم تعيين المادة إلى الصف بنجاح',
        'already_assigned' => 'هذه المادة معينة بالفعل للصف المحدد',
        'invalid_input' => 'إدخال غير صالح',
        'failed_to_create' => 'فشل في إنشاء المادة',
        'delete_failed' => 'فشل الحذف',
        'failed_to_assign' => 'فشل في تعيين المادة إلى الصف',
        'class_time_required' => 'الصف، اليوم والوقت مطلوبة',
        'invalid_credit_hours' => 'قيمة ساعات المعتمدة غير صالحة',
        'subject_name_required' => 'اسم المادة مطلوب'
    ]
];

function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

// Subjects management page
// Features:
// - List / search / filter subjects
// - Create / Edit / Delete subjects (admin only)
// - Assign subjects to classes with teachers
// - Toggle view between basic and detailed (with teachers, classes, sections)
// - Pagination
// - CSRF protection
// - Prepared statements everywhere

// Authentication
$logged_in_id = $_SESSION['user_id'] ?? null;


// Load current user
$me_stmt = $conn->prepare("SELECT u.id, u.username, u.role_id, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = ?");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me_res = $me_stmt->get_result();
if ($me_res->num_rows === 0) die(t('invalid_session'));
$me = $me_res->fetch_assoc();
$my_role_name = $me['role_name'];

// Authorization helper
function is_admin() {
    global $my_role_name;
    return $my_role_name === 'admin';
}

// Check if detailed view is enabled (from session or default)
$detailed_view = isset($_SESSION['subjects_detailed_view']) ? $_SESSION['subjects_detailed_view'] : false;

// Toggle view mode if requested
if (isset($_GET['toggle_view'])) {
    $_SESSION['subjects_detailed_view'] = !$detailed_view;
    header('Location: ' . str_replace('&toggle_view=1', '', $_SERVER['PHP_SELF']));
    exit;
}

// Fetch helper lists
function get_teachers($conn) {
    $arr = [];
    $res = $conn->query("SELECT id, CONCAT(first_name, ' ', last_name) AS name FROM teachers WHERE status='active' ORDER BY first_name");
    while ($r = $res->fetch_assoc()) $arr[] = $r;
    return $arr;
}

function get_classes($conn) {
    $arr = [];
    $res = $conn->query("
        SELECT c.id, c.name, s.name as section_name 
        FROM classes c 
        LEFT JOIN sections s ON s.id = c.section_id 
        ORDER BY c.name
    ");
    while ($r = $res->fetch_assoc()) $arr[] = $r;
    return $arr;
}

function get_sections($conn) {
    $arr = [];
    $res = $conn->query("SELECT id, name FROM sections ORDER BY name");
    while ($r = $res->fetch_assoc()) $arr[] = $r;
    return $arr;
}

// Handle POST actions (create, update, delete, assign to class)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // CSRF
    if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
        flash_message('error', t('invalid_csrf'));
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }

    $action = $_POST['action'] ?? '';

    // Only admins can create/update/delete and assign
    if (!is_admin()) {
        flash_message('error', t('permission_denied'));
        header('Location: ' . $_SERVER['PHP_SELF']); exit;
    }

   if ($action === 'create') {
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');

    // ✅ FIXED: handle decimal credits correctly
    $credit_hours = $_POST['credit_hours'] ?? null;
    if ($credit_hours !== null && $credit_hours !== '') {
        $credit_hours = str_replace(',', '.', $credit_hours);

        if (!is_numeric($credit_hours)) {
            flash_message('error', t('invalid_credit_hours'));
            header('Location: '.$_SERVER['PHP_SELF']);
            exit;
        }

        $credit_hours = (float)$credit_hours;
    } else {
        $credit_hours = null;
    }

    if ($name === '') {
        flash_message('error', t('subject_name_required'));
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }

    $stmt = $conn->prepare(
        "INSERT INTO subjects (code, name, credit_hours) VALUES (?, ?, ?)"
    );
    $stmt->bind_param('ssd', $code, $name, $credit_hours);

    if ($stmt->execute()) {
        log_audit(
            $conn,
            $logged_in_id,
            'SUBJECT_CREATE',
            "Created subject: $name (ID: {$stmt->insert_id})"
        );
        flash_message('success', t('subject_created'));
    } else {
        flash_message('error', t('failed_to_create') . ': '.$conn->error);
    }

    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}


   if ($action === 'update') {

    // -------- SUBJECT UPDATE --------
    $id = (int)($_POST['subject_id'] ?? 0);
    $code = trim($_POST['code'] ?? '');
    $name = trim($_POST['name'] ?? '');
    $credit_hours = $_POST['credit_hours'] === '' ? null : (float)$_POST['credit_hours'];

    if ($id <= 0 || $name === '') { 
        flash_message('error', t('invalid_input')); 
        header('Location: '.$_SERVER['PHP_SELF']); 
        exit; 
    }

    $stmt = $conn->prepare("
        UPDATE subjects 
        SET code = ?, name = ?, credit_hours = ? 
        WHERE id = ?
    ");
    $stmt->bind_param('ssdi', $code, $name, $credit_hours, $id);
    $stmt->execute();

    // -------- SCHEDULE UPDATE --------
    $class_subject_id = (int)($_POST['class_subject_id'] ?? 0);
    $day_of_week = $_POST['day_of_week'] ?? '';
    $start_time  = $_POST['start_time'] ?? '';
    $end_time    = $_POST['end_time'] ?? '';

    if ($class_subject_id && $day_of_week && $start_time && $end_time) {
        $stmt2 = $conn->prepare("
            UPDATE class_subjects
            SET day_of_week = ?, start_time = ?, end_time = ?
            WHERE id = ?
        ");
        $stmt2->bind_param(
            'sssi',
            $day_of_week,
            $start_time,
            $end_time,
            $class_subject_id
        );
        $stmt2->execute();
    }

// Fetch subject name
$sub_res = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$sub_res->bind_param('i', $id);
$sub_res->execute();
$subject_name = $sub_res->get_result()->fetch_assoc()['name'] ?? 'Unknown';

// Fetch assignment details (class & teacher)
$assign_res = $conn->prepare("
    SELECT cs.id, c.name AS class_name, t.first_name, t.last_name
    FROM class_subjects cs
    LEFT JOIN classes c ON cs.class_id = c.id
    LEFT JOIN teachers t ON cs.teacher_id = t.id
    WHERE cs.id = ?
");
$assign_res->bind_param('i', $class_subject_id);
$assign_res->execute();
$assign_row = $assign_res->get_result()->fetch_assoc();
$class_name = $assign_row['class_name'] ?? 'Unknown';
$teacher_name = ($assign_row['first_name'] ?? '') . ' ' . ($assign_row['last_name'] ?? '');

// Log with names instead of IDs
log_audit(
    $conn,
    $logged_in_id,
    'SUBJECT_UPDATE',
    "Updated subject $subject_name & schedule (Class: $class_name, Teacher: $teacher_name)"
);


    flash_message('success', t('subject_updated'));
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

   


   if ($action === 'delete') {
    $id = (int)($_POST['subject_id'] ?? 0);
    if ($id <= 0) {
        flash_message('error', t('invalid_input'));
        header('Location: '.$_SERVER['PHP_SELF']);
        exit;
    }

    // START TRANSACTION
   $conn->begin_transaction();

try {
    // Delete only the subject itself
   $stmt = $conn->prepare("DELETE FROM subjects WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();


    $conn->commit();

    log_audit(
        $conn,
        $logged_in_id,
        'SUBJECT_DELETE',
        "Deleted subject ID $id (assignments not deleted)"
    );

    flash_message('success', t('subject_deleted'));

} catch (Exception $e) {
    $conn->rollback();
    flash_message('error', t('delete_failed') . ': ' . $e->getMessage());
}

header('Location: '.$_SERVER['PHP_SELF']);
exit;
   }


    if ($action === 'assign_to_class') {
        $subject_id = (int)($_POST['subject_id'] ?? 0);
        $class_id = (int)($_POST['class_id'] ?? 0);
        $teacher_id = (int)($_POST['teacher_id'] ?? 0);
        $day_of_week = $_POST['day_of_week'] ?? '';
        $start_time = $_POST['start_time'] ?? '';
        $end_time   = $_POST['end_time'] ?? '';

        if ($subject_id <= 0 || $class_id <= 0 || !$day_of_week || !$start_time || !$end_time) {
            flash_message('error', t('class_time_required'));
            header('Location: '.$_SERVER['PHP_SELF']); 
            exit;
        }


        // Check if already assigned
        $check_stmt = $conn->prepare("SELECT id FROM class_subjects WHERE class_id = ? AND subject_id = ?");
        $check_stmt->bind_param('ii', $class_id, $subject_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            flash_message('error', t('already_assigned'));
            header('Location: '.$_SERVER['PHP_SELF']); exit;
        }
// For assigning to class
$sub_res = $conn->prepare("SELECT name FROM subjects WHERE id = ?");
$sub_res->bind_param('i', $subject_id);
$sub_res->execute();
$subject_name = $sub_res->get_result()->fetch_assoc()['name'] ?? 'Unknown';

$class_res = $conn->prepare("SELECT name FROM classes WHERE id = ?");
$class_res->bind_param('i', $class_id);
$class_res->execute();
$class_name = $class_res->get_result()->fetch_assoc()['name'] ?? 'Unknown';

$teacher_res = $conn->prepare("SELECT CONCAT(first_name,' ',last_name) AS name FROM teachers WHERE id = ?");
$teacher_res->bind_param('i', $teacher_id);
$teacher_res->execute();
$teacher_name = $teacher_res->get_result()->fetch_assoc()['name'] ?? 'Unknown';

       $stmt = $conn->prepare("
                INSERT INTO class_subjects 
                (class_id, subject_id, teacher_id, day_of_week, start_time, end_time)
                VALUES (?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                'iiisss',
                $class_id,
                $subject_id,
                $teacher_id,
                $day_of_week,
                $start_time,
                $end_time
            );

        if ($stmt->execute()) {
            log_audit(
    $conn,
    $logged_in_id,
    'SUBJECT_ASSIGN_CLASS',
    "Assigned subject $subject_name to class $class_name with teacher $teacher_name"
);

            flash_message('success', t('subject_assigned'));
        } else {
            flash_message('error', t('failed_to_assign'));
        }
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }

    if ($action === 'remove_assignment') {
        $assignment_id = (int)($_POST['assignment_id'] ?? 0);
        
        if ($assignment_id <= 0) { 
            flash_message('error', t('invalid_input')); 
            header('Location: '.$_SERVER['PHP_SELF']); exit; 
        }

        $stmt = $conn->prepare("DELETE FROM class_subjects WHERE id = ?");
        $stmt->bind_param('i', $assignment_id);
        if ($stmt->execute()) {
            log_audit($conn, $logged_in_id, 'SUBJECT_REMOVE_ASSIGNMENT', "Removed class assignment ID: $assignment_id");
            flash_message('success', t('assignment_removed'));
        } else {
            flash_message('error', t('failed_to_assign'));
        }
        header('Location: '.$_SERVER['PHP_SELF']); exit;
    }
}

// Fetch theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = (int)($theme_row['mode'] ?? 0);

// GET: list subjects with filters
$search = trim($_GET['search'] ?? '');
$page = max(1, (int)($_GET['page'] ?? 1));
$limit = 12;
$offset = ($page - 1) * $limit;

// Build query based on view mode (DataTables will handle search/paging)
if ($detailed_view) {
    // Detailed view: Show subjects with their class assignments
    $sql = "
        SELECT 
            s.*,
            GROUP_CONCAT(DISTINCT CONCAT(c.name, ' (', sec.name, ')') SEPARATOR ', ') as assigned_classes,
            GROUP_CONCAT(DISTINCT CONCAT(t.first_name, ' ', t.last_name) SEPARATOR ', ') as assigned_teachers,
            COUNT(DISTINCT cs.id) as assignment_count
        FROM subjects s
        LEFT JOIN class_subjects cs ON cs.subject_id = s.id
        LEFT JOIN classes c ON c.id = cs.class_id
        LEFT JOIN sections sec ON sec.id = c.section_id
        LEFT JOIN teachers t ON t.id = cs.teacher_id
        GROUP BY s.id
        ORDER BY s.created_at DESC
    ";
} else {
    // Basic view: Just subjects
    $sql = "SELECT s.* FROM subjects s ORDER BY s.created_at DESC";
}

$stmt = $conn->prepare($sql);
if (!$stmt) { 
    die('SQL prepare error: '.$conn->error.' -- SQL: '.$sql); 
}
$stmt->execute();
$result = $stmt->get_result();
$subjects = [];
while ($r = $result->fetch_assoc()) $subjects[] = $r;

// Get assignments for each subject (for both views)
function get_subject_assignments($conn, $subject_id) {
    $assignments = [];
    $stmt = $conn->prepare("
        SELECT cs.id, c.name as class_name, sec.name as section_name,
       CONCAT(t.first_name, ' ', t.last_name) as teacher_name,
       cs.day_of_week, cs.start_time, cs.end_time,
       cs.created_at

        FROM class_subjects cs
        JOIN classes c ON c.id = cs.class_id
        LEFT JOIN sections sec ON sec.id = c.section_id
        LEFT JOIN teachers t ON t.id = cs.teacher_id
        WHERE cs.subject_id = ?
        ORDER BY c.name
    ");
    $stmt->bind_param('i', $subject_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $assignments[] = $row;
    }
    return $assignments;
}

function get_total_hours($assignments) {
    $total = 0;
    foreach ($assignments as $a) {
        if (!empty($a['start_time']) && !empty($a['end_time'])) {
            $start = strtotime($a['start_time']);
            $end = strtotime($a['end_time']);
            $diff = $end - $start; // in seconds
            if ($diff > 0) $total += $diff;
        }
    }
    return round($total / 3600, 2); // total hours as float
}


// total
$total_res = $conn->query("SELECT FOUND_ROWS() AS total");
$total_row = $total_res->fetch_assoc();
$total = (int)$total_row['total'];
$total_pages = max(1, ceil($total / $limit));

$teachers = get_teachers($conn);
$classes = get_classes($conn);

// CSRF token
$csrf = csrf_token();
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
        }
    </style>
    
    <!-- JQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
</head>
<body class="<?= $theme === 0 ? 'dark-theme' : 'light-theme' ?>">
    <div class="container-xl py-4">
        <!-- Professional Header -->
        <div class="professional-header fade-in">
            <div class="d-flex flex-column flex-lg-row justify-content-between align-items-start align-items-lg-center">
                <div class="mb-4 mb-lg-0">
                    <h1 class="header-title"><?= t('page_title') ?></h1>
                    <p class="header-subtitle"><?= t('page_subtitle') ?></p>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="?<?= http_build_query(array_merge($_GET, ['toggle_view' => 1])) ?>" 
                       class="view-toggle <?= $detailed_view ? 'detailed' : '' ?> text-decoration-none">
                        <span class="view-toggle-text basic"><?= t('basic_view') ?></span>
                        <div class="view-toggle-slider"></div>
                        <span class="view-toggle-text detailed"><?= t('detailed_view') ?></span>
                    </a>

                    <?php if (is_admin()): ?>
                        <button class="btn-modern btn-primary-modern" data-bs-toggle="modal" data-bs-target="#createModal">
                            <i class="bi bi-plus-lg"></i>
                            <span><?= t('new_subject') ?></span>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Statistics -->
            <div class="row g-4 mt-4">
                <div class="col-md-4">
                    <div class="stat-card fade-in">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #3498db 0%, #2c3e50 100%);">
                            <i class="bi bi-book"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= $total ?></div>
                            <div class="stat-label"><?= t('total_subjects') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card fade-in" style="animation-delay: 0.1s">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #27ae60 0%, #3498db 100%);">
                            <i class="bi bi-people"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= count($teachers) ?></div>
                            <div class="stat-label"><?= t('active_teachers') ?></div>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="stat-card fade-in" style="animation-delay: 0.2s">
                        <div class="stat-icon" style="background: linear-gradient(135deg, #f39c12 0%, #b5179e 100%);">
                            <i class="bi bi-building"></i>
                        </div>
                        <div>
                            <div class="stat-number"><?= count($classes) ?></div>
                            <div class="stat-label"><?= t('available_classes') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Flash Messages -->
        <?php foreach(get_flash_messages() as $m): ?>
            <div class="alert alert-<?= $m['type'] == 'error' ? 'danger' : $m['type'] ?> alert-dismissible fade show mb-4" 
                 role="alert"
                 style="border-left: 4px solid <?= $m['type'] == 'error' ? 'var(--danger)' : ($m['type'] == 'success' ? 'var(--success)' : 'var(--warning)') ?>">
                <div class="d-flex align-items-center">
                    <i class="bi bi-<?= $m['type'] == 'error' ? 'exclamation-triangle' : ($m['type'] == 'success' ? 'check-circle' : 'info-circle') ?> me-3 fs-4"></i>
                    <div class="flex-grow-1">
                        <?= htmlspecialchars($m['message']) ?>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        <?php endforeach; ?>


        <!-- Subjects Table -->
        <div class="table-container fade-in">
            <div class="p-4 border-bottom">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0"><?= t('subjects_list') ?></h5>
                    <div class="text-primary">
                        <?= t('showing_of') ?> <span class="fw-bold"><?= count($subjects) ?></span> <?= t('of') ?> <span class="fw-bold"><?= $total ?></span> <?= t('subjects') ?>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-modern mb-0" id="subjectsTable">
                    <thead>
                        <tr>
                            <th><?= t('code') ?></th>
                            <th><?= t('subject') ?></th>
                            <th><?= t('hours') ?></th>
                            <?php if ($detailed_view): ?>
                                <th><?= t('assigned_classes') ?></th>
                                <th><?= t('teachers') ?></th>
                            <?php else: ?>
                                <th><?= t('created') ?></th>
                            <?php endif; ?>
                            <th class="text-end"><?= t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($subjects)): ?>
                            <tr>
                                <td colspan="<?= $detailed_view ? 6 : 5 ?>">
                                    <div class="empty-state">
                                        <i class="bi bi-book empty-state-icon"></i>
                                        <h4 class="fw-bold mb-3"><?= t('no_subjects_found') ?></h4>
                                        <p class="text-muted mb-4"><?= t('no_subjects_match') ?></p>
                                        <?php if (is_admin()): ?>
                                            <button class="btn-modern btn-primary-modern" data-bs-toggle="modal" data-bs-target="#createModal">
                                                <i class="bi bi-plus-lg me-2"></i>
                                                <?= t('create_first_subject') ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach($subjects as $s): 
                                $assignments = get_subject_assignments($conn, $s['id']);
                                $total_hours = get_total_hours($assignments);
                            ?>
                                <tr>
                                    <td data-label="<?= t('code') ?>">
                                        <span class="badge-modern badge-class"><?= htmlspecialchars($s['code'] ?: 'N/A') ?></span>
                                    </td>
                                    
                                    <td data-label="<?= t('subject') ?>">
                                        <div class="fw-bold"><?= htmlspecialchars($s['name']) ?></div>
                                        <small class="text-primary"><?= t('subject_id') ?>: #<?= $s['id'] ?></small>
                                    </td>
                                    
                                    <td data-label="<?= t('hours') ?>">
                                        <span class="badge-modern badge-credits"><?= htmlspecialchars($s['credit_hours']) ?> <?= t('hours') ?></span>
                                    </td>
                                    
                                    <?php if ($detailed_view): ?>
                                        <td data-label="<?= t('assigned_classes') ?>">
                                            <?php if (!empty($s['assigned_classes'])): ?>
                                                <?php 
                                                    $class_list = explode(', ', $s['assigned_classes']);
                                                    $display_classes = array_slice($class_list, 0, 2);
                                                    $extra_count = count($class_list) - 2;
                                                ?>
                                                
                                                <?php foreach ($display_classes as $class): ?>
                                                    <span class="badge badge-class mb-1 d-inline-block"><?= htmlspecialchars($class) ?></span>
                                                <?php endforeach; ?>
                                                
                                                <?php if ($extra_count > 0): ?>
                                                    <span class="badge bg-secondary" 
                                                        data-bs-toggle="tooltip" 
                                                        data-bs-placement="top" 
                                                        title="<?= htmlspecialchars(implode(', ', array_slice($class_list, 2))) ?>">
                                                        +<?= $extra_count ?> <?= t('more') ?>
                                                    </span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted"><?= t('not_assigned') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        
                                        <td data-label="<?= t('teachers') ?>">
                                            <?php if (!empty($s['assigned_teachers'])): ?>
                                                <?php 
                                                    $teacher_list = explode(', ', $s['assigned_teachers']);
                                                    foreach (array_slice($teacher_list, 0, 2) as $teacher): 
                                                ?>
                                                    <span class="badge badge-teacher mb-1 d-inline-block"><?= htmlspecialchars($teacher) ?></span>
                                                <?php endforeach; ?>
                                                <?php if (count($teacher_list) > 2): ?>
                                                    <span class="badge bg-secondary">+<?= count($teacher_list) - 2 ?> <?= t('more') ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted"><?= t('no_teacher') ?></span>
                                            <?php endif; ?>
                                        </td>
                                    <?php else: ?>
                                        <td data-label="<?= t('created') ?>">
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-calendar3 me-2 text-primary"></i>
                                                <span class="text-primary"><?= date('M d, Y', strtotime($s['created_at'])) ?></span>
                                            </div>
                                        </td>
                                    <?php endif; ?>
                                    
                                    <td data-label="<?= t('actions') ?>" class="text-end">
                                        <div class="action-buttons">
                                            <?php if (is_admin()): ?>
                                                <?php $first_assignment = $assignments[0] ?? null; ?>
                                                <button class="action-btn edit"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#editModal"
                                                    data-id="<?= $s['id'] ?>"
                                                    data-code="<?= htmlspecialchars($s['code']) ?>"
                                                    data-name="<?= htmlspecialchars($s['name']) ?>"
                                                    data-credits="<?= $s['credit_hours'] ?>"
                                                    data-cs-id="<?= $first_assignment['id'] ?? '' ?>"
                                                    data-day="<?= $first_assignment['day_of_week'] ?? '' ?>"
                                                    data-start="<?= $first_assignment['start_time'] ?? '' ?>"
                                                    data-end="<?= $first_assignment['end_time'] ?? '' ?>"
                                                    title="<?= t('edit') ?>">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                
                                                <button class="action-btn assign"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#assignModal"
                                                    data-id="<?= $s['id'] ?>"
                                                    title="<?= t('assign_to_class') ?>">
                                                    <i class="bi bi-link-45deg"></i>
                                                </button>
                                                
                                                <form method="post" class="d-inline"
                                                    onsubmit="return confirm('<?= t('delete_confirmation') ?>');">
                                                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="subject_id" value="<?= $s['id'] ?>">
                                                    <button class="action-btn delete" type="submit" title="<?= t('delete') ?>">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($assignments)): ?>
                                                <button class="action-btn view"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewAssignmentsModal"
                                                    data-subject-id="<?= $s['id'] ?>"
                                                    data-subject-name="<?= htmlspecialchars($s['name']) ?>"
                                                    data-assignments='<?= json_encode($assignments) ?>'
                                                    title="<?= t('view_assignments') ?>">
                                                    <i class="bi bi-list-check"></i>
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
        </div>
    </div>

    <!-- Floating Action Button -->
    <?php if (is_admin()): ?>
        <button class="floating-action" data-bs-toggle="modal" data-bs-target="#createModal" title="<?= t('new_subject') ?>">
            <i class="bi bi-plus-lg"></i>
        </button>
    <?php endif; ?>

    <!-- Create Subject Modal -->
    <div class="modal fade modal-modern" id="createModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('create_new_subject') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('subject_code') ?> 
                                <span class="text-muted">(<?= t('optional') ?>)</span>
                            </label>
                            <input type="text" class="form-control" name="code" placeholder="<?= t('subject_code_placeholder') ?>">
                            <small class="text-muted"><?= t('subject_code_hint') ?></small>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('subject_name') ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="name" required placeholder="<?= t('subject_name_placeholder') ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><?= t('credit_hours') ?></label>
                            <div class="input-group">
                                <input type="number" step="0.25" class="form-control" name="credit_hours" value="0.00">
                                <span class="input-group-text"><?= t('hours') ?></span>
                            </div>
                            <small class="text-muted"><?= t('credit_hours_hint') ?></small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modern btn-outline-modern" data-bs-dismiss="modal">
                            <?= t('cancel') ?>
                        </button>
                        <button type="submit" class="btn-modern btn-primary-modern">
                            <i class="bi bi-plus-lg me-2"></i>
                            <?= t('create_subject') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Subject Modal -->
    <div class="modal fade modal-modern" id="editModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('edit_subject') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post" id="editForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="update">
                        <input type="hidden" name="subject_id" id="edit_subject_id">
                        
                        <div class="mb-4">
                            <label class="form-label"><?= t('subject_code') ?></label>
                            <input type="text" class="form-control" name="code" id="edit_code" placeholder="<?= t('subject_code_placeholder') ?>">
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('subject_name') ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label"><?= t('credit_hours') ?></label>
                            <div class="input-group">
                                <input type="number" step="0.25" class="form-control" name="credit_hours" id="edit_credits">
                                <span class="input-group-text"><?= t('hours') ?></span>
                            </div>
                        </div>

                        <hr class="my-4">
                        <h6 class="text-muted mb-3"><?= t('teaching_schedule') ?></h6>

                        <input type="hidden" name="class_subject_id" id="edit_class_subject_id">

                        <div class="mb-3">
                            <label class="form-label text-primary"><?= t('day_of_week') ?></label>
                            <select name="day_of_week" id="edit_day_of_week" class="form-select tom-select">
                                <option value="">-- <?= t('select_day') ?> --</option>
                                <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $d): ?>
                                    <option value="<?= t(strtolower($d)) ?>"><?= t(strtolower($d)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-primary"><?= t('start_time') ?></label>
                                <input type="time" name="start_time" id="edit_start_time" class="form-control">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-primary"><?= t('end_time') ?></label>
                                <input type="time" name="end_time" id="edit_end_time" class="form-control">
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modern btn-outline-modern" data-bs-dismiss="modal">
                            <?= t('cancel') ?>
                        </button>
                        <button type="submit" class="btn-modern btn-primary-modern">
                            <i class="bi bi-check-lg me-2"></i>
                            <?= t('save_changes') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Assign to Class Modal -->
    <div class="modal fade modal-modern" id="assignModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('assign_subject_to_class') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="post">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                        <input type="hidden" name="action" value="assign_to_class">
                        <input type="hidden" name="subject_id" id="assign_subject_id">
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('select_class') ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="class_id" id="assign_class_id" class="form-select tom-select" required>
                                <option value="">-- <?= t('select_class') ?> --</option>
                                <?php foreach($classes as $c): ?>
                                    <option value="<?= $c['id'] ?>">
                                        <?= htmlspecialchars($c['name']) ?>
                                        <?php if ($c['section_name']): ?>
                                            (<?= htmlspecialchars($c['section_name']) ?>)
                                        <?php endif; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('assign_teacher') ?> 
                                <span class="text-muted">(<?= t('optional') ?>)</span>
                            </label>
                            <select name="teacher_id" id="assign_teacher_id" class="form-select tom-select">
                                <option value="">-- <?= t('no_teacher_assigned') ?> --</option>
                                <?php foreach($teachers as $t): ?>
                                    <option value="<?= $t['id'] ?>">
                                        <?= htmlspecialchars($t['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">
                                <?= t('day_of_week') ?> 
                                <span class="text-danger">*</span>
                            </label>
                            <select name="day_of_week" id="assign_day_of_week" class="form-select tom-select" required>
                                <option value="">-- <?= t('select_day') ?> --</option>
                                <?php foreach (['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $day): ?>
                                    <option value="<?= t(strtolower($day)) ?>"><?= t(strtolower($day)) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label">
                                    <?= t('start_time') ?> 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="start_time" class="form-control" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">
                                    <?= t('end_time') ?> 
                                    <span class="text-danger">*</span>
                                </label>
                                <input type="time" name="end_time" class="form-control" required>
                            </div>
                        </div>
                        
                        <div class="alert alert-info border-0" style="background: rgba(52, 152, 219, 0.1);">
                            <i class="bi bi-info-circle me-2"></i>
                            <?= t('class_assign_info') ?>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn-modern btn-outline-modern" data-bs-dismiss="modal">
                            <?= t('cancel') ?>
                        </button>
                        <button type="submit" class="btn-modern btn-primary-modern">
                            <i class="bi bi-link-45deg me-2"></i>
                            <?= t('assign_to_class') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Assignments Modal -->
    <div class="modal fade modal-modern" id="viewAssignmentsModal" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><?= t('class_assignments') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6 id="assignmentSubjectName" class="fw-bold mb-4 text-primary"></h6>
                    <div id="assignmentsList" class="list-group border-0"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-modern btn-outline-modern" data-bs-dismiss="modal">
                        <?= t('cancel') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Fill edit modal with subject data
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#subjectsTable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "<?= t('search_subjects') ?>",
                    lengthMenu: "<?= $currentLangId == 2 ? 'عرض _MENU_ سجلات' : 'Show _MENU_ entries' ?>",
                    info: "<?= $currentLangId == 2 ? 'عرض _START_ إلى _END_ من _TOTAL_ مواد' : 'Showing _START_ to _END_ of _TOTAL_ subjects' ?>",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        previous: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'right' : 'left' ?>"></i>',
                        next: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'left' : 'right' ?>"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1 }
                ],
                order: [[0, 'asc']]
            });
        });

        // Existing scripts...
        document.addEventListener('DOMContentLoaded', function() {
            const editModal = document.getElementById('editModal');
            if (editModal) {
                editModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    
                    document.getElementById('edit_subject_id').value = btn.dataset.id;
                    document.getElementById('edit_code').value = btn.dataset.code || '';
                    document.getElementById('edit_name').value = btn.dataset.name || '';
                    document.getElementById('edit_credits').value = btn.dataset.credits || '';
                    
                    document.getElementById('edit_class_subject_id').value = btn.dataset.csId || '';
                    document.getElementById('edit_class_subject_id').value = btn.dataset.csId || '';
                    
                    const dayVal = btn.dataset.day || '';
                    const daySelect = document.getElementById('edit_day_of_week');
                    if (daySelect.tomselect) {
                        daySelect.tomselect.setValue(dayVal);
                    } else {
                        daySelect.value = dayVal;
                    }

                    document.getElementById('edit_start_time').value = btn.dataset.start || '';
                    document.getElementById('edit_end_time').value = btn.dataset.end || '';
                });
            }
            
            // Fill assign modal with subject ID
            const assignModal = document.getElementById('assignModal');
            if (assignModal) {
                assignModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    document.getElementById('assign_subject_id').value = btn.getAttribute('data-id');
                });
            }
            
            // Fill view assignments modal
            const viewAssignmentsModal = document.getElementById('viewAssignmentsModal');
            if (viewAssignmentsModal) {
                viewAssignmentsModal.addEventListener('show.bs.modal', function (event) {
                    const btn = event.relatedTarget;
                    const subjectId = btn.getAttribute('data-subject-id');
                    const subjectName = btn.getAttribute('data-subject-name');
                    const assignments = JSON.parse(btn.getAttribute('data-assignments'));
                    
                    document.getElementById('assignmentSubjectName').textContent = 
                        '<?= t("assignments_for") ?>: ' + subjectName;
                    
                    const assignmentsList = document.getElementById('assignmentsList');
                    assignmentsList.innerHTML = '';
                    
                    if (assignments.length === 0) {
                        assignmentsList.innerHTML = `
                            <div class="alert alert-info border-0" style="background: rgba(52, 152, 219, 0.1);">
                                <i class="bi bi-info-circle me-2"></i>
                                <?= t("no_class_assignments") ?>
                            </div>
                        `;
                    } else {
                        assignments.forEach(function(assignment) {
                            const item = document.createElement('div');
                            item.className = 'list-group-item border-0 mb-3 p-3 rounded-3';
                            item.style.backgroundColor = 'rgba(52, 152, 219, 0.05)';
                            
                            const dayTranslations = {
                                'Monday': '<?= t("monday") ?>',
                                'Tuesday': '<?= t("tuesday") ?>',
                                'Wednesday': '<?= t("wednesday") ?>',
                                'Thursday': '<?= t("thursday") ?>',
                                'Friday': '<?= t("friday") ?>',
                                'Saturday': '<?= t("saturday") ?>',
                                'Sunday': '<?= t("sunday") ?>'
                            };
                            
                            const translatedDay = dayTranslations[assignment.day_of_week] || assignment.day_of_week;
                            
                            item.innerHTML = `
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="bi bi-building me-2 text-primary"></i>
                                            <strong class="fs-6">${assignment.class_name}</strong>
                                            ${assignment.section_name ? `
                                                <span class="badge bg-secondary ms-2">${assignment.section_name}</span>
                                            ` : ''}
                                        </div>
                                        <div class="d-flex align-items-center text-muted">
                                            <i class="bi bi-person me-2"></i>
                                            <small><?= t("teacher") ?>: ${assignment.teacher_name || '<?= t("no_teacher_assigned") ?>'}</small>
                                        </div>
                                        <div class="d-flex align-items-center text-muted mt-1">
                                            <i class="bi bi-calendar3 me-2"></i>
                                            <small><?= t("assigned") ?>: ${new Date(assignment.created_at).toLocaleDateString()}</small>
                                        </div>
                                        <div class="d-flex align-items-center text-muted mt-1">
                                            <i class="bi bi-clock me-2"></i>
                                            <small>${translatedDay} ${assignment.start_time} – ${assignment.end_time}</small>
                                        </div>
                                    </div>
                                    <form method="post" class="d-inline" onsubmit="return confirm('<?= t("remove_assignment") ?>');">
                                        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                                        <input type="hidden" name="action" value="remove_assignment">
                                        <input type="hidden" name="assignment_id" value="${assignment.id}">
                                        <?php if (is_admin()): ?>
                                            <button type="submit" class="action-btn delete" title="<?= t("remove") ?>">
                                                <i class="bi bi-x-lg"></i>
                                            </button>
                                        <?php endif; ?>
                                    </form>
                                </div>
                            `;
                            assignmentsList.appendChild(item);
                        });
                    }
                });
            }
            
            // Initialize tooltips
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            tooltipTriggerList.forEach(tooltipTriggerEl => {
                new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Add keyboard shortcut for toggling view (Ctrl+Shift+V)
            document.addEventListener('keydown', function(e) {
                if (e.ctrlKey && e.shiftKey && e.key === 'V') {
                    window.location.href = '?<?= http_build_query(array_merge($_GET, ['toggle_view' => 1])) ?>';
                }
            });
            
            // Add loading state for form submissions
            document.querySelectorAll('form').forEach(form => {
                form.addEventListener('submit', function() {
                    const submitBtn = this.querySelector('button[type="submit"]');
                    if (submitBtn) {
                        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?= t("loading") ?>';
                        submitBtn.disabled = true;
                    }
                });
            });
            
            // Add fade-in animation to elements
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
                el.style.animationDelay = `${index * 0.1}s`;
            });

            // Initialize Tom Select
            document.querySelectorAll('.tom-select').forEach((el) => {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    plugins: ['remove_button'],
                    dropdownParent: 'body'
                });
            });
        });
        
        // Theme toggle simulation
        const themeToggle = document.createElement('button');
        themeToggle.className = 'floating-action';
        themeToggle.style.bottom = '80px';
        themeToggle.style.<?= $currentLangId == 2 ? 'left' : 'right' ?> = '2rem';
        themeToggle.innerHTML = '<i class="bi bi-<?= $theme === 0 ? 'sun' : 'moon' ?>"></i>';
        themeToggle.title = 'Toggle Theme';
        themeToggle.onclick = function() {
            alert('Theme toggle would require page reload with different theme mode');
        };
        document.body.appendChild(themeToggle);
    </script>

    <!-- Professional Custom CSS -->
    <style>
        /* ===== PROFESSIONAL STYLING SYSTEM ===== */
        :root {
            /* Professional Color Palette */
            --primary: #2c3e50;
            --primary-dark: #1a252f;
            --primary-light: #34495e;
            --secondary: #3498db;
            --secondary-dark: #2980b9;
            --accent: #27ae60;
            --danger: #e74c3c;
            --warning: #f39c12;
            --success: #27ae60;
            --info: #3498db;
            
            /* Neutral Colors */
            --light-bg: #f8f9fa;
            --light-card: #ffffff;
            --light-border: #e9ecef;
            --light-text: #2c3e50;
            --light-muted: #6c757d;
            
            --dark-bg: #121212;
            --dark-card: #1e1e1e;
            --dark-border: #2d2d2d;
            --dark-text: #e9ecef;
            --dark-muted: #adb5bd;
            
            /* Typography */
            --font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
            --box-shadow-lg: 0 10px 25px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* ===== BASE STYLES ===== */
        body {
            font-family: var(--font-family);
            font-weight: 400;
            line-height: 1.6;
            background-color: var(--light-bg);
            color: var(--light-text);
            min-height: 100vh;
        }

        body.dark-theme {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }

        /* ===== PROFESSIONAL HEADER ===== */
        .professional-header {
            background: linear-gradient(135deg, var(--light-card) 0%, #f8f9fa 100%);
            border-radius: var(--border-radius);
            padding: 2.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--box-shadow);
            border: 1px solid var(--light-border);
        }

        .dark-theme .professional-header {
            background: linear-gradient(135deg, var(--dark-card) 0%, #1a1a1a 100%);
            border-color: var(--dark-border);
        }

        .header-title {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 0.5rem;
            letter-spacing: -0.02em;
        }

        .dark-theme .header-title {
            color: var(--dark-text);
        }

        .header-subtitle {
            font-size: 1rem;
            color: var(--light-muted);
            font-weight: 400;
        }

        .dark-theme .header-subtitle {
            color: var(--dark-muted);
        }

        /* ===== STATISTICS CARDS ===== */
        .stat-card {
            background: var(--light-card);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1.25rem;
            border: 1px solid var(--light-border);
            transition: var(--transition);
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--box-shadow-lg);
            border-color: var(--secondary);
        }

        .dark-theme .stat-card {
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .stat-number {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--primary);
            line-height: 1;
        }

        .dark-theme .stat-number {
            color: var(--dark-text);
        }

        .stat-label {
            font-size: 0.875rem;
            color: var(--light-muted);
            margin-top: 0.25rem;
        }

        .dark-theme .stat-label {
            color: var(--dark-muted);
        }

        /* ===== MODERN BUTTONS ===== */
        .btn-modern {
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 0.875rem;
            letter-spacing: 0.02em;
            border: 2px solid transparent;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none !important;
        }

        .btn-primary-modern {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .btn-primary-modern:hover {
            background: var(--primary-dark);
            border-color: var(--primary-dark);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(44, 62, 80, 0.15);
        }

        .btn-outline-modern {
            background: transparent;
            color: var(--primary);
            border-color: var(--light-border);
        }

        .btn-outline-modern:hover {
            background: rgba(44, 62, 80, 0.05);
            border-color: var(--primary);
            transform: translateY(-1px);
        }

        .dark-theme .btn-outline-modern {
            color: var(--dark-text);
            border-color: var(--dark-border);
        }

        .dark-theme .btn-outline-modern:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--secondary);
        }

        /* ===== VIEW TOGGLE ===== */
        .view-toggle {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.5rem;
            background: var(--light-card);
            border-radius: var(--border-radius);
            border: 1px solid var(--light-border);
        }

        .dark-theme .view-toggle {
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .view-toggle-slider {
            width: 40px;
            height: 20px;
            background: var(--light-border);
            border-radius: 20px;
            position: relative;
            cursor: pointer;
            transition: var(--transition);
        }

        .view-toggle-slider:before {
            content: '';
            position: absolute;
            width: 16px;
            height: 16px;
            background: white;
            border-radius: 50%;
            top: 2px;
            left: 2px;
            transition: var(--transition);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .view-toggle.detailed .view-toggle-slider {
            background: var(--primary);
        }

        .view-toggle.detailed .view-toggle-slider:before {
            transform: translateX(20px);
        }

        .view-toggle-text {
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--light-muted);
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .view-toggle.detailed .view-toggle-text.detailed {
            color: var(--primary);
            font-weight: 700;
        }

        .view-toggle:not(.detailed) .view-toggle-text.basic {
            color: var(--primary);
            font-weight: 700;
        }

        .dark-theme .view-toggle-text {
            color: var(--dark-muted);
        }

        /* ===== MODERN TABLE ===== */
        .table-container {
            background: var(--light-card);
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: 1px solid var(--light-border);
            overflow: hidden;
        }

        .dark-theme .table-container {
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .table-modern {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table-modern thead th {
            background: rgba(44, 62, 80, 0.03);
            color: var(--primary);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem;
            border-bottom: 2px solid var(--light-border);
            white-space: nowrap;
        }

        .dark-theme .table-modern thead th {
            background: rgba(255, 255, 255, 0.03);
            color: var(--dark-text);
            border-color: var(--dark-border);
        }

        .table-modern tbody td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border-bottom: 1px solid var(--light-border);
            transition: var(--transition);
        }

        .dark-theme .table-modern tbody td {
            border-color: var(--dark-border);
        }

        .table-modern tbody tr:hover td {
            background: rgba(44, 62, 80, 0.02);
        }

        .dark-theme .table-modern tbody tr:hover td {
            background: rgba(255, 255, 255, 0.02);
        }

        /* ===== BADGES ===== */
        .badge-modern {
            padding: 0.35rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.02em;
            border: 1px solid transparent;
        }

        .badge-class {
            background: rgba(52, 152, 219, 0.1);
            color: var(--secondary);
            border-color: rgba(52, 152, 219, 0.2);
        }

        .badge-credits {
            background: rgba(39, 174, 96, 0.1);
            color: var(--success);
            border-color: rgba(39, 174, 96, 0.2);
        }

        .badge-teacher {
            background: rgba(243, 156, 18, 0.1);
            color: var(--warning);
            border-color: rgba(243, 156, 18, 0.2);
        }

        /* ===== ACTION BUTTONS ===== */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: flex-end;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            border: 1px solid var(--light-border);
            background: transparent;
            color: var(--light-muted);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
            cursor: pointer;
            text-decoration: none !important;
        }

        .action-btn:hover {
            transform: translateY(-1px);
            box-shadow: var(--box-shadow);
        }

        .action-btn.edit:hover {
            border-color: var(--secondary);
            color: var(--secondary);
            background: rgba(52, 152, 219, 0.1);
        }

        .action-btn.assign:hover {
            border-color: var(--success);
            color: var(--success);
            background: rgba(39, 174, 96, 0.1);
        }

        .action-btn.delete:hover {
            border-color: var(--danger);
            color: var(--danger);
            background: rgba(231, 76, 60, 0.1);
        }

        .action-btn.view:hover {
            border-color: var(--warning);
            color: var(--warning);
            background: rgba(243, 156, 18, 0.1);
        }

        .dark-theme .action-btn {
            border-color: var(--dark-border);
            color: var(--dark-muted);
        }

        /* ===== FORM ELEMENTS ===== */
        .form-modern .form-control,
        .form-modern .form-select {
            padding: 0.75rem 1rem;
            border: 1px solid var(--light-border);
            border-radius: var(--border-radius);
            background: var(--light-card);
            color: var(--light-text);
            font-size: 0.875rem;
            transition: var(--transition);
        }

        .form-modern .form-control:focus,
        .form-modern .form-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
            background: var(--light-card);
        }

        .dark-theme .form-modern .form-control,
        .dark-theme .form-modern .form-select {
            background: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }

        .dark-theme .form-modern .form-control:focus,
        .dark-theme .form-modern .form-select:focus {
            border-color: var(--secondary);
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }

        .search-box {
            position: relative;
        }

        .search-box:before {
            content: '\F52A';
            font-family: 'bootstrap-icons';
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-muted);
            font-size: 1rem;
            pointer-events: none;
        }

        .dark-theme .search-box:before {
            color: var(--dark-muted);
        }

        .search-box input {
            padding-left: 2.75rem !important;
        }

        /* ===== MODAL STYLING ===== */
        .modal-modern .modal-content {
            border-radius: var(--border-radius);
            border: 1px solid var(--light-border);
            background: var(--light-card);
        }

        .dark-theme .modal-modern .modal-content {
            background: var(--dark-card);
            border-color: var(--dark-border);
        }

        .modal-modern .modal-header {
            border-bottom: 1px solid var(--light-border);
            padding: 1.5rem 1.5rem 1rem;
        }

        .dark-theme .modal-modern .modal-header {
            border-color: var(--dark-border);
        }

        .modal-modern .modal-body {
            padding: 1rem 1.5rem;
        }

        .modal-modern .modal-footer {
            border-top: 1px solid var(--light-border);
            padding: 1rem 1.5rem 1.5rem;
        }

        .dark-theme .modal-modern .modal-footer {
            border-color: var(--dark-border);
        }

        /* ===== PAGINATION ===== */
        .pagination-modern .page-item .page-link {
            padding: 0.5rem 0.75rem;
            border: 1px solid var(--light-border);
            color: var(--primary);
            background: transparent;
            margin: 0 0.25rem;
            border-radius: 6px;
            font-weight: 500;
            transition: var(--transition);
        }

        .pagination-modern .page-item.active .page-link {
            background: var(--primary);
            border-color: var(--primary);
            color: white;
        }

        .pagination-modern .page-item .page-link:hover {
            background: rgba(44, 62, 80, 0.05);
            border-color: var(--primary);
        }

        .dark-theme .pagination-modern .page-item .page-link {
            border-color: var(--dark-border);
            color: var(--dark-text);
            background: transparent;
        }

        .dark-theme .pagination-modern .page-item .page-link:hover {
            background: rgba(255, 255, 255, 0.05);
            border-color: var(--secondary);
        }

        /* ===== EMPTY STATE ===== */
        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-state-icon {
            font-size: 4rem;
            color: var(--light-border);
            margin-bottom: 1.5rem;
            opacity: 0.5;
        }

        .dark-theme .empty-state-icon {
            color: var(--dark-border);
        }

        /* ===== FLOATING ACTION BUTTON ===== */
        .floating-action {
            position: fixed;
            bottom: 2rem;
            <?= $currentLangId == 2 ? 'left' : 'right' ?>: 2rem;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: var(--primary);
            color: white;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            box-shadow: 0 6px 20px rgba(44, 62, 80, 0.2);
            transition: var(--transition);
            cursor: pointer;
            z-index: 1000;
        }

        .floating-action:hover {
            background: var(--primary-dark);
            transform: translateY(-2px) scale(1.05);
            box-shadow: 0 10px 25px rgba(44, 62, 80, 0.3);
        }

        /* ===== ANIMATIONS ===== */
        .fade-in {
            opacity: 0;
            transform: translateY(10px);
            animation: fadeIn 0.5s ease forwards;
        }

        @keyframes fadeIn {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* ===== RESPONSIVE DESIGN ===== */
        @media (max-width: 768px) {
            .professional-header {
                padding: 1.5rem;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
            
            .stat-card {
                padding: 1.25rem;
            }
            
            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
            
            .table-modern thead {
                display: none;
            }
            
            .table-modern tbody tr {
                display: block;
                margin-bottom: 1rem;
                border: 1px solid var(--light-border);
                border-radius: var(--border-radius);
                padding: 1rem;
            }
            
            .table-modern tbody td {
                display: block;
                padding: 0.75rem 0;
                border: none;
            }
            
            .table-modern tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                display: block;
                margin-bottom: 0.25rem;
                color: var(--light-muted);
                font-size: 0.75rem;
                text-transform: uppercase;
                letter-spacing: 0.05em;
            }
            
            .dark-theme .table-modern tbody td:before {
                color: var(--dark-muted);
            }
            
            .action-buttons {
                justify-content: flex-start;
                margin-top: 1rem;
                padding-top: 1rem;
                border-top: 1px solid var(--light-border);
            }
            
            .dark-theme .action-buttons {
                border-color: var(--dark-border);
            }
        }

        @media (max-width: 576px) {
            .professional-header {
                padding: 1.25rem;
            }
            
            .header-title {
                font-size: 1.25rem;
            }
            
            .header-subtitle {
                font-size: 0.875rem;
            }
            
            .floating-action {
                width: 50px;
                height: 50px;
                font-size: 1.25rem;
                bottom: 1.5rem;
                <?= $currentLangId == 2 ? 'left' : 'right' ?>: 1.5rem;
            }
            
            .modal-modern .modal-dialog {
                margin: 0.5rem;
            }
        }

        /* ===== RTL SUPPORT ===== */
        [dir="rtl"] .search-box:before {
            left: auto;
            right: 1rem;
        }
        
        [dir="rtl"] .search-box input {
            padding-left: 1rem !important;
            padding-right: 2.75rem !important;
        }
        
        [dir="rtl"] .action-buttons {
            justify-content: flex-start;
        }
        
        [dir="rtl"] .table-modern thead th {
            text-align: right;
        }
        
        [dir="rtl"] .table-modern tbody td {
            text-align: right;
        }
    </style>
</body>
</html>
