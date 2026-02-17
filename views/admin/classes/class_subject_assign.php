<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Subject Assignment',
        'back_to_classes' => 'Back to Classes',
        'manage_assignments' => 'Manage subject assignments for',
        'class_id' => 'Class ID',
        'subjects_assigned' => 'Subjects Assigned',
        'total_subjects' => 'Total Subjects',
        'assigned_subjects' => 'Assigned Subjects',
        'available_teachers' => 'Available Teachers',
        'assign_new_subject' => 'Assign New Subject',
        'add_new_subject' => 'Add a new subject to this class',
        'warning_overlap' => 'Warning: One or more of the scheduled times overlaps with another subject in this class.',
        'select_subject' => 'Select Subject',
        'choose_subject' => 'Choose a subject...',
        'assign_teacher' => 'Assign Teacher',
        'no_teacher_assigned' => 'No teacher assigned',
        'schedule' => 'Schedule',
        'select_day' => 'Select Day',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday',
        'add_multiple_days' => 'You can add multiple days with their start and end times.',
        'total_time_warning' => 'Total scheduled time exceeds recommended credit hours for this subject.',
        'conflict_warning' => 'One or more scheduled times overlap with another subject in this class.',
        'assign' => 'Assign',
        'current_assignments' => 'Current Subject Assignments',
        'manage_teacher_assignments' => 'Manage teacher assignments for each subject',
        'assignments' => 'Assignments',
        'no_subjects_assigned' => 'No Subjects Assigned',
        'no_subjects_assigned_desc' => 'This class doesn\'t have any subjects assigned yet. Use the form above to add subjects.',
        'subject' => 'Subject',
        'schedule' => 'Schedule',
        'current_teacher' => 'Current Teacher',
        'action' => 'Action',
        'teacher_id' => 'Teacher ID',
        'unassigned' => 'Unassigned',
        'assigned' => 'Assigned',
        'pending' => 'Pending',
        'save_changes_note' => 'Changes will be saved immediately after clicking "Save All Changes"',
        'save_all_changes' => 'Save All Changes',
        'changes_saved' => 'Changes Saved Successfully!',
        'assignments_updated' => 'Teacher assignments have been updated.',
        'subject_assigned' => 'Subject Assigned Successfully!',
        'subject_added' => 'New subject has been added to the class.',
        'copyright' => 'School Management System',
        'module_version' => 'Subject Assignment Module v2.0',
        'last_updated' => 'Last updated',
        'no_section' => 'No Section'
    ],
    2 => [ // Arabic
        'page_title' => 'تخصيص المواد',
        'back_to_classes' => 'العودة إلى الصفوف',
        'manage_assignments' => 'إدارة تخصيص المواد لـ',
        'class_id' => 'معرف الصف',
        'subjects_assigned' => 'المواد المخصصة',
        'total_subjects' => 'إجمالي المواد',
        'assigned_subjects' => 'المواد المخصصة',
        'available_teachers' => 'المعلمون المتاحون',
        'assign_new_subject' => 'تخصيص مادة جديدة',
        'add_new_subject' => 'إضافة مادة جديدة لهذا الصف',
        'warning_overlap' => 'تحذير: واحد أو أكثر من الأوقات المجدولة يتداخل مع مادة أخرى في هذا الصف.',
        'select_subject' => 'اختر المادة',
        'choose_subject' => 'اختر مادة...',
        'assign_teacher' => 'تعيين معلم',
        'no_teacher_assigned' => 'لا يوجد معلم معين',
        'schedule' => 'الجدول',
        'select_day' => 'اختر اليوم',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
        'sunday' => 'الأحد',
        'add_multiple_days' => 'يمكنك إضافة أيام متعددة مع أوقات البداية والنهاية.',
        'total_time_warning' => 'الوقت المجموع المجدول يتجاوز ساعات الاعتماد الموصى بها لهذه المادة.',
        'conflict_warning' => 'واحد أو أكثر من الأوقات المجدولة يتداخل مع مادة أخرى في هذا الصف.',
        'assign' => 'تخصيص',
        'current_assignments' => 'التخصيصات الحالية',
        'manage_teacher_assignments' => 'إدارة تعيينات المعلمين لكل مادة',
        'assignments' => 'تخصيصات',
        'no_subjects_assigned' => 'لا توجد مواد مخصصة',
        'no_subjects_assigned_desc' => 'لا توجد مواد مخصصة لهذا الصف بعد. استخدم النموذج أعلاه لإضافة مواد.',
        'subject' => 'المادة',
        'schedule' => 'الجدول',
        'current_teacher' => 'المعلم الحالي',
        'action' => 'إجراء',
        'teacher_id' => 'معرف المعلم',
        'unassigned' => 'غير معين',
        'assigned' => 'مخصص',
        'pending' => 'قيد الانتظار',
        'save_changes_note' => 'سيتم حفظ التغييرات فور النقر على "حفظ جميع التغييرات"',
        'save_all_changes' => 'حفظ جميع التغييرات',
        'changes_saved' => 'تم حفظ التغييرات بنجاح!',
        'assignments_updated' => 'تم تحديث تعيينات المعلمين.',
        'subject_assigned' => 'تم تخصيص المادة بنجاح!',
        'subject_added' => 'تمت إضافة مادة جديدة إلى الصف.',
        'copyright' => 'نظام إدارة المدرسة',
        'module_version' => 'وحدة تخصيص المواد الإصدار 2.0',
        'last_updated' => 'آخر تحديث',
        'no_section' => 'لا يوجد قسم'
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

// Auth
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) { header('Location: ../../auth/login.php'); exit; }

// Load user
$me_stmt = $conn->prepare("
    SELECT u.id, u.username, r.name AS role_name 
    FROM users u 
    JOIN roles r ON r.id = u.role_id 
    WHERE u.id=?
");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
$my_role_name = $me['role_name'];
function is_admin(){ global $my_role_name; return $my_role_name === 'admin'; }

// Theme
$theme_mode = 1; 
$themeRes = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1");
if($themeRes && $row = $themeRes->fetch_assoc()){
    $theme_mode = (int)$row['mode'];
}

// CSRF
$csrf = csrf_token();

// Get class_id
$class_id = intval($_GET['class_id'] ?? 0);
if($class_id <= 0) die("Invalid class");

// Fetch class + section
$stmt = $conn->prepare("
    SELECT c.*, s.name AS section_name
    FROM classes c 
    LEFT JOIN sections s ON s.id = c.section_id 
    WHERE c.id=?
");
$stmt->bind_param('i', $class_id);
$stmt->execute();
$class = $stmt->get_result()->fetch_assoc();
if(!$class) die("Class not found");

// Fetch subjects
$subjects = [];
$res = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
while($r = $res->fetch_assoc()) $subjects[$r['id']] = $r;

// Fetch teachers
$teachers = [];
$res = $conn->query("
    SELECT id, CONCAT(first_name,' ',last_name) AS name 
    FROM teachers 
    WHERE status='active' 
    ORDER BY first_name ASC
");
while($r = $res->fetch_assoc()) $teachers[$r['id']] = $r['name'];

// Fetch assigned subjects with schedules
$assigned_subjects = [];
$stmt = $conn->prepare("
    SELECT 
        cs.id AS cs_id,
        cs.subject_id,
        cs.teacher_id,
        s.name AS subject_name,
        CONCAT(t.first_name,' ',t.last_name) AS teacher_name,
        sched.id AS sched_id,
        sched.day_of_week,
        sched.start_time,
        sched.end_time
    FROM class_subjects cs
    JOIN subjects s ON s.id = cs.subject_id
    LEFT JOIN teachers t ON t.id = cs.teacher_id
    LEFT JOIN class_subject_schedule sched ON sched.class_subject_id = cs.id
    WHERE cs.class_id=?
    ORDER BY cs.subject_id, FIELD(sched.day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), sched.start_time
");
$stmt->bind_param('i', $class_id);
$stmt->execute();
$res = $stmt->get_result();

while($r = $res->fetch_assoc()){
    $sub_id = $r['subject_id'];
    if(!isset($assigned_subjects[$sub_id])){
        $assigned_subjects[$sub_id] = [
            'cs_id'        => $r['cs_id'],
            'subject_id'   => $sub_id,
            'subject_name' => $r['subject_name'],
            'teacher_id'   => $r['teacher_id'],
            'teacher_name' => $r['teacher_name'],
            'schedules'    => []
        ];
    }

    if($r['sched_id']){
        $assigned_subjects[$sub_id]['schedules'][] = [
            'sched_id' => $r['sched_id'],
            'day'      => substr($r['day_of_week'],0,10),
            'start'    => substr($r['start_time'],0,5),
            'end'      => substr($r['end_time'],0,5)
        ];
    }
}

// Build list of assigned subject IDs
$assigned_subject_ids = array_keys($assigned_subjects);

// Build unassigned subjects ONLY for this class
$unassigned_subjects = [];
foreach ($subjects as $id => $sub) {
    if (!in_array($id, $assigned_subject_ids)) {
        $unassigned_subjects[$id] = $sub;
    }
}

/* ================= ASSIGN NEW SUBJECT WITH MULTIPLE DAYS ================= */
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assign_new']) && is_admin()){
    $new_subject = intval($_POST['new_subject_id']);
    $new_teacher = !empty($_POST['new_teacher_id']) ? intval($_POST['new_teacher_id']) : null;
    $days = $_POST['days'] ?? [];
    $start_times = $_POST['start_times'] ?? [];
    $end_times = $_POST['end_times'] ?? [];
    
    if(!isset($assigned_subjects[$new_subject]) && count($days) > 0){
        // 1. Fetch existing schedules for this class
        $existing_schedules = [];
        $stmt = $conn->prepare("
            SELECT cs.teacher_id, sched.day_of_week, sched.start_time, sched.end_time
            FROM class_subject_schedule sched
            JOIN class_subjects cs ON cs.id = sched.class_subject_id
            WHERE cs.class_id=?
        ");
        $stmt->bind_param('i', $class_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while($row = $result->fetch_assoc()){
            $existing_schedules[] = $row;
        }

        // 2. Fetch all other schedules for the selected teacher (across all classes)
        $teacher_schedules = [];
        if($new_teacher){
            $stmt = $conn->prepare("
                SELECT cs.class_id, sched.day_of_week, sched.start_time, sched.end_time
                FROM class_subject_schedule sched
                JOIN class_subjects cs ON cs.id = sched.class_subject_id
                WHERE cs.teacher_id = ? AND sched.start_time IS NOT NULL AND sched.end_time IS NOT NULL
            ");
            $stmt->bind_param('i', $new_teacher);
            $stmt->execute();
            $res = $stmt->get_result();
            while($r = $res->fetch_assoc()){
                $teacher_schedules[] = $r;
            }
        }

        // 3. Check for conflicts
        $conflict = false;
        for($i=0; $i<count($days); $i++){
            $day = $days[$i];
            $start = $start_times[$i];
            $end = $end_times[$i];

            if($day && $start && $end){
                list($sh1,$sm1) = explode(':', $start);
                list($eh1,$em1) = explode(':', $end);
                $new_start = $sh1*60+$sm1;
                $new_end = $eh1*60+$em1;

                // Conflict with existing schedules in same class
                foreach($existing_schedules as $sched){
                    if($sched['day_of_week'] === $day){
                        list($sh2,$sm2) = explode(':', $sched['start_time']);
                        list($eh2,$em2) = explode(':', $sched['end_time']);
                        $exist_start = $sh2*60+$sm2;
                        $exist_end = $eh2*60+$em2;

                        if($new_start < $exist_end && $new_end > $exist_start){
                            $conflict = true;
                            $conflict_msg = "Conflict with another subject in this class on $day ($start-$end)";
                            break 2;
                        }
                    }
                }

                foreach($teacher_schedules as $sched){
                    if($sched['day_of_week'] === $day){
                        $schedStart = (int)date('H', strtotime($sched['start_time'])) * 60 + (int)date('i', strtotime($sched['start_time']));
                        $schedEnd = (int)date('H', strtotime($sched['end_time'])) * 60 + (int)date('i', strtotime($sched['end_time']));

                        if ($new_start < $schedEnd && $new_end > $schedStart) {
                            die("Conflict: Teacher already assigned to another class on $day ($start-$end)");
                        }
                    }
                }
            }
        }

        if($conflict){
            die("Error: $conflict_msg");
        }

        // 4. Insert subject assignment
        $stmt = $conn->prepare("INSERT INTO class_subjects (class_id, subject_id, teacher_id) VALUES (?,?,?)");
        $stmt->bind_param('iii', $class_id, $new_subject, $new_teacher);
        $stmt->execute();
        $class_subject_id = $stmt->insert_id;

        // 5. Insert schedules into new table
        $stmt_sched = $conn->prepare("
            INSERT INTO class_subject_schedule (class_subject_id, day_of_week, start_time, end_time)
            VALUES (?,?,?,?)
        ");
        for($i=0;$i<count($days);$i++){
            $day = $days[$i];
            $start = $start_times[$i];
            $end = $end_times[$i];
            if($day && $start && $end){
                $stmt_sched->bind_param('isss', $class_subject_id, $day, $start, $end);
                $stmt_sched->execute();
            }
        }
    }

    header("Location: class_subject_assign.php?class_id=$class_id&added=1");
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> | <?= htmlspecialchars($class['name']) ?> | School System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Tailwind Tweaks */
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            border-color: #d1d5db !important;
            min-height: 50px;
            display: flex;
            align-items: center;
        }
        .dark .ts-control {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-dropdown .option {
            color: #fff !important;
        }
        .dark .ts-dropdown .active {
            background-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-control input {
            color: #fff !important;
        }
    </style>
<body class="<?= $theme_mode === 0 ? 'dark bg-gray-900 text-gray-100' : 'bg-gradient-to-br from-indigo-50 to-blue-100 text-gray-800' ?>">

    <!-- Header/Navigation -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="mb-4 md:mb-0">
                    <a href="classes.php" class="inline-flex items-center text-white/80 hover:text-white mb-2 transition">
                        <i class="fas fa-arrow-left mr-2"></i> <?= t('back_to_classes') ?>
                    </a>
                    <h1 class="text-2xl font-bold"><?= t('page_title') ?></h1>
                    <p class="text-white/80"><?= t('manage_assignments') ?> <?= htmlspecialchars($class['name']) ?></p>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-3 bg-white/10 px-4 py-2 rounded-xl">
                        <div class="text-right">
                            <p class="font-medium"><?= htmlspecialchars($me['username'] ?? 'User') ?></p>
                            <p class="text-white/80 text-sm"><?= htmlspecialchars($my_role_name) ?></p>
                        </div>
                        <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user-tie"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Class Info Card -->
        <div class="glass-card rounded-2xl p-6 shadow-lg mb-8 animate-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center mb-3">
                        <div class="bg-blue-100 text-blue-800 p-3 rounded-xl mr-4">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800"><?= htmlspecialchars($class['name']) ?></h2>
                            <div class="flex flex-wrap gap-2 mt-2">
                                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                    <i class="fas fa-layer-group mr-1"></i>
                                    <?= htmlspecialchars($class['section_name'] ?? t('no_section')) ?>
                                </span>
                                <span class="bg-emerald-100 text-emerald-800 text-xs font-medium px-3 py-1 rounded-full">
                                    <i class="fas fa-book mr-1"></i>
                                    <?= count($assigned_subjects) ?> <?= t('subjects_assigned') ?>
                                </span>
                                <span class="bg-purple-100 text-purple-800 text-xs font-medium px-3 py-1 rounded-full">
                                    <?= t('class_id') ?>: <?= $class['id'] ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if(is_admin()): ?>
                <a href="classes.php" class="btn-primary text-white px-6 py-3 rounded-xl font-medium inline-flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> <?= t('back_to_classes') ?>
                </a>
                <?php endif; ?>
            </div>
        </div>

        <style>
            .dark .total{
                color: white;
            }
            .dark .text-gray-800,
            .dark .text-gray-500,
            .dark .fa-user-tie,
            .dark .fa-book,
            .dark .select {
                color: white;
            }
        </style>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium total"><?= t('total_subjects') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?= count($subjects) ?></h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-book-open text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium"><?= t('assigned_subjects') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?= count($assigned_subjects) ?></h3>
                    </div>
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-check-circle text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium"><?= t('available_teachers') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1"><?= count($teachers) ?></h3>
                    </div>
                    <div class="bg-amber-100 p-3 rounded-full">
                        <i class="fas fa-user-tie text-amber-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assign New Subject Card -->
        <?php if(is_admin()): ?>
        <div class="glass-card rounded-2xl p-6 shadow-lg mb-8 animate-in" style="animation-delay: 0.1s">
            <div class="flex items-center mb-6">
                <div class="bg-gradient-to-r from-blue-500 to-cyan-500 text-white p-3 rounded-xl mr-4">
                    <i class="fas fa-plus-circle text-2xl"></i>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800"><?= t('assign_new_subject') ?></h3>
                    <p class="text-gray-600"><?= t('add_new_subject') ?></p>
                </div>
            </div>

            <?php if(!empty($_GET['warning'])): ?>
            <div class="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-xl text-amber-700 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i>
                ⚠ <?= t('warning_overlap') ?>
            </div>
            <?php endif; ?>

            <form method="post" class="grid grid-cols-1 md:grid-cols-12 gap-4 items-end" id="assignSubjectForm">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                <!-- Subject -->
                <div class="md:col-span-5">
                    <label class="block text-gray-700 text-sm font-medium mb-2 select">
                        <i class="fas fa-book text-gray-400 mr-2"></i><?= t('select_subject') ?>
                    </label>
                    <select id="new_subject_id" name="new_subject_id" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none select-styled tom-select-search" required>
                        <option value="" data-credit="0"><?= t('choose_subject') ?></option>
                        <?php foreach($subjects as $s): if(!isset($assigned_subjects[$s['id']])): ?>
                            <option value="<?= $s['id'] ?>" data-credit="<?= $s['credit_hours'] ?>">
                                <?= htmlspecialchars($s['name']) ?>
                                <?php if(isset($s['code'])): ?> (<?= htmlspecialchars($s['code']) ?>)<?php endif; ?>
                            </option>
                        <?php endif; endforeach; ?>
                    </select>
                </div>

                <!-- Teacher -->
                <div class="md:col-span-5">
                    <label class="block text-gray-700 text-sm font-medium mb-2 select">
                        <i class="fas fa-user-tie text-gray-400 mr-2"></i><?= t('assign_teacher') ?> (<?= t('optional') ?>)
                    </label>
                    <select name="new_teacher_id" class="w-full p-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none select-styled tom-select-search">
                        <option value=""><?= t('no_teacher_assigned') ?></option>
                        <?php foreach($teachers as $tid => $tname): ?>
                            <option value="<?= $tid ?>"><?= htmlspecialchars($tname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Days & Times -->
                <div class="md:col-span-12 mt-4" id="daysTimesContainer" style="display:none;">
                    <label class="block text-gray-700 text-sm font-medium mb-2">
                        <i class="fas fa-calendar-day text-gray-400 mr-2"></i><?= t('schedule') ?>
                    </label>
                    <div id="daysTimesWrapper" class="space-y-3">
                        <div class="flex gap-3 items-center day-time-row">
                            <select name="days[]" class="p-2 border rounded-lg">
                                <option value=""><?= t('select_day') ?></option>
                                <option value="Monday"><?= t('monday') ?></option>
                                <option value="Tuesday"><?= t('tuesday') ?></option>
                                <option value="Wednesday"><?= t('wednesday') ?></option>
                                <option value="Thursday"><?= t('thursday') ?></option>
                                <option value="Friday"><?= t('friday') ?></option>
                                <option value="Saturday"><?= t('saturday') ?></option>
                                <option value="Sunday"><?= t('sunday') ?></option>
                            </select>
                            <input type="time" name="start_times[]" class="p-2 border rounded-lg">
                            <input type="time" name="end_times[]" class="p-2 border rounded-lg">
                            <button type="button" class="addDayBtn text-green-500 font-bold">+</button>
                            <button type="button" class="removeDayBtn text-red-500 font-bold">-</button>
                        </div>
                    </div>

                    <!-- Warnings -->
                    <p id="timeWarning" class="text-red-500 text-sm mt-1 hidden">
                        ⚠ <?= t('total_time_warning') ?>
                    </p>
                    <p id="conflictWarning" class="text-red-600 text-sm mt-1 hidden">
                        ⚠ <?= t('conflict_warning') ?>
                    </p>

                    <p class="text-gray-500 text-sm mt-1"><?= t('add_multiple_days') ?></p>
                </div>

                <!-- Assign button -->
                <div class="md:col-span-2">
                    <button type="submit" name="assign_new" class="btn-success text-white w-full p-3 rounded-xl font-medium flex items-center justify-center hover:shadow-lg transition">
                        <i class="fas fa-link mr-2"></i> <?= t('assign') ?>
                    </button>
                </div>
            </form>
        </div>

        <script>
        document.addEventListener('DOMContentLoaded', function() {
            const subjectSelect = document.getElementById('new_subject_id');
            const container = document.getElementById('daysTimesContainer');
            const wrapper = document.getElementById('daysTimesWrapper');
            const timeWarning = document.getElementById('timeWarning');
            const conflictWarning = document.getElementById('conflictWarning');

            // Existing schedules from PHP
            const existingSchedules = <?php
                $existing = [];
                foreach($assigned_subjects as $as){
                    foreach($as['schedules'] as $sched){
                        $existing[] = [
                            'day' => $sched['day'],
                            'start' => $sched['start'],
                            'end' => $sched['end']
                        ];
                    }
                }
                echo json_encode($existing);
            ?>;

            function checkWarnings() {
                // Modified to work with Tom Select (which might hide options from select.selectedOptions)
                let creditHours = 0;
                if(subjectSelect.value) {
                    const selectedOpt = subjectSelect.querySelector(`option[value="${subjectSelect.value}"]`);
                    creditHours = parseFloat(selectedOpt?.dataset.credit || 0);
                } else {
                    creditHours = 0;
                }
                
                let totalMinutes = 0;
                let conflict = false;

                wrapper.querySelectorAll('.day-time-row').forEach(row => {
                    const day = row.querySelector('select[name="days[]"]').value;
                    const start = row.querySelector('input[name="start_times[]"]').value;
                    const end = row.querySelector('input[name="end_times[]"]').value;

                    if(start && end){
                        const [sh, sm] = start.split(':').map(Number);
                        const [eh, em] = end.split(':').map(Number);
                        totalMinutes += (eh*60 + em) - (sh*60 + sm);

                        if(day){
                            const newStart = sh*60 + sm;
                            const newEnd = eh*60 + em;
                            existingSchedules.forEach(sched => {
                                if(sched.day === day){
                                    const [sh2, sm2] = sched.start.split(':').map(Number);
                                    const [eh2, em2] = sched.end.split(':').map(Number);
                                    const existStart = sh2*60 + sm2;
                                    const existEnd = eh2*60 + em2;
                                    if(newStart < existEnd && newEnd > existStart){
                                        conflict = true;
                                    }
                                }
                            });
                        }
                    }
                });

                timeWarning.classList.toggle('hidden', !(creditHours > 0 && (totalMinutes/60) > creditHours));
                conflictWarning.classList.toggle('hidden', !conflict);
                return !conflict;
            }

            // Event hooks
            subjectSelect.addEventListener('change', () => { container.style.display = subjectSelect.value ? 'block' : 'none'; checkWarnings(); });
            wrapper.addEventListener('click', () => setTimeout(checkWarnings, 50));
            wrapper.addEventListener('input', checkWarnings);

            // Prevent submission if conflict
            const form = document.getElementById('assignSubjectForm');
            form.addEventListener('submit', function(e){
                if(!checkWarnings()){
                    e.preventDefault();
                    alert("Cannot assign: one or more times overlap with existing subjects.");
                }
            });
            });

            // Initialize Tom Select
            document.querySelectorAll('.tom-select-search').forEach(el => {
                new TomSelect(el, {
                    create: false,
                    sortField: {field: "text", direction: "asc"},
                    // Force the dropdown to append to body to avoid clipping if necessary
                    dropdownParent: 'body'
                });
            });
        });
        </script>
        <?php endif; ?>

        <!-- Current Assignments -->
        <div class="glass-card rounded-2xl overflow-hidden shadow-lg animate-in" style="animation-delay: 0.2s">
            <div class="gradient-bg text-white px-6 py-4">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <div>
                        <h3 class="text-xl font-bold"><?= t('current_assignments') ?></h3>
                        <p class="text-white/80 text-sm"><?= t('manage_teacher_assignments') ?></p>
                    </div>
                    <?php if(is_admin() && !empty($assigned_subjects)): ?>
                    <div class="mt-2 md:mt-0">
                        <span class="bg-white/20 px-3 py-1 rounded-full text-sm">
                            <?= count($assigned_subjects) ?> <?= t('assignments') ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <form method="post">
                <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
                
                <?php if(empty($assigned_subjects)): ?>
                <div class="p-12 text-center">
                    <div class="text-gray-400 text-6xl mb-4">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-600 mb-2"><?= t('no_subjects_assigned') ?></h3>
                    <p class="text-gray-500 max-w-md mx-auto mb-6"><?= t('no_subjects_assigned_desc') ?></p>
                </div>
                <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                       <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">
                                <i class="fas fa-book mr-2"></i><?= t('subject') ?>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">
                                <i class="fas fa-clock mr-2"></i><?= t('schedule') ?>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">
                                <i class="fas fa-user-tie mr-2"></i><?= t('current_teacher') ?>
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-700 uppercase">
                                <i class="fas fa-edit mr-2"></i><?= t('action') ?>
                            </th>
                        </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            <?php foreach($assigned_subjects as $s): ?>
                            <tr data-cs-id="<?= $s['cs_id'] ?>" class="table-row-hover transition-colors">
                                <td class="px-6 py-4 text-muted">
                                    <div class="flex items-center">
                                        <div class="bg-blue-100 text-blue-800 p-2 rounded-lg mr-3">
                                            <i class="fas fa-book"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 subject-name cursor-pointer select"
                                                data-subject-id="<?= $s['subject_id'] ?>">
                                                <?= htmlspecialchars($s['subject_name']) ?>
                                            </div>
                                            <div class="text-gray-500 text-sm"><?= t('subject_id') ?>: <?= $s['subject_id'] ?></div>
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="px-6 py-4">
                                    <?php if (!empty($s['schedules'])): ?>
                                        <div class="space-y-1 text-sm">
                                            <?php foreach ($s['schedules'] as $sch): ?>
                                                <div class="flex items-center text-gray-700 select">
                                                    <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                                                    <strong class="select"><?= htmlspecialchars($sch['day']) ?></strong>
                                                    <span class="mx-2">•</span>
                                                    <i class="fas fa-clock text-emerald-500 mr-1 select"></i>
                                                    <?= substr($sch['start'], 0, 5) ?> – <?= substr($sch['end'], 0, 5) ?>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-gray-400 italic"><?= t('no_schedule') ?></span>
                                    <?php endif; ?>
                                </td>

                                <td class="px-6 py-4">
                                    <?php if(!empty($s['teacher_name'])): ?>
                                    <div class="flex items-center">
                                        <div class="bg-emerald-100 text-emerald-800 p-2 rounded-lg mr-3">
                                            <i class="fas fa-user-tie"></i>
                                        </div>
                                        <div>
                                            <div class="font-medium text-gray-900 select"><?= htmlspecialchars($s['teacher_name']) ?></div>
                                            <div class="text-gray-500 text-sm"><?= t('teacher_id') ?>: <?= $s['teacher_id'] ?></div>
                                        </div>
                                    </div>
                                    <?php else: ?>
                                    <div class="flex items-center">
                                        <div class="bg-amber-100 text-amber-800 p-2 rounded-lg mr-3 select">
                                            <i class="fas fa-user-slash"></i>
                                        </div>
                                        <div class="text-amber-700 font-medium select"><?= t('unassigned') ?></div>
                                    </div>
                                    <?php endif; ?>
                                </td>
                              
                                <td class="px-6 py-4">
                                    <?php if(!empty($s['teacher_name'])): ?>
                                    <span class="status-badge status-assigned">
                                        <i class="fas fa-check-circle mr-1"></i> <?= t('assigned') ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="status-badge status-unassigned">
                                        <i class="fas fa-clock mr-1"></i> <?= t('pending') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>

                               <td class="px-6 py-4 text-center">
                                    <a href="edit_schedule.php?cs_id=<?= $s['cs_id'] ?>"
                                    class="text-blue-600 hover:text-blue-800"
                                    title="Edit schedule">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if(is_admin()): ?>
                <div class="px-6 py-6 bg-gray-50 border-t">
                    <div class="flex flex-col md:flex-row justify-between items-center">
                        <div class="mb-4 md:mb-0 text-gray-600">
                            <i class="fas fa-exclamation-circle text-blue-500 mr-2"></i>
                            <?= t('save_changes_note') ?>
                        </div>
                        <div class="flex space-x-3">
                            <button type="submit" name="save_assignments"
                                    class="btn-primary text-white px-5 py-3 rounded-xl font-medium flex items-center hover:shadow-lg transition">
                                <i class="fas fa-save mr-2"></i> <?= t('save_all_changes') ?>
                            </button>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
                <?php endif; ?>
            </form>
        </div>

        <!-- Success Messages -->
        <?php if(isset($_GET['saved']) && $_GET['saved'] == '1'): ?>
        <div id="successMessage" class="fixed top-4 right-4 z-50 animate-in">
            <div class="bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                <i class="fas fa-check-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold"><?= t('changes_saved') ?></p>
                    <p class="text-emerald-100 text-sm"><?= t('assignments_updated') ?></p>
                </div>
                <button onclick="document.getElementById('successMessage').remove()" class="ml-6 text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>

        <?php if(isset($_GET['added']) && $_GET['added'] == '1'): ?>
        <div id="addedMessage" class="fixed top-4 right-4 z-50 animate-in">
            <div class="bg-blue-500 text-white px-6 py-4 rounded-xl shadow-lg flex items-center">
                <i class="fas fa-plus-circle text-2xl mr-3"></i>
                <div>
                    <p class="font-semibold"><?= t('subject_assigned') ?></p>
                    <p class="text-blue-100 text-sm"><?= t('subject_added') ?></p>
                </div>
                <button onclick="document.getElementById('addedMessage').remove()" class="ml-6 text-white/80 hover:text-white">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <?php endif; ?>
    </main>
    
    <!-- Footer -->
    <footer class="mt-12 pt-8 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600">&copy; <?= date('Y') ?> <?= t('copyright') ?></p>
                    <p class="text-gray-500 text-sm"><?= t('module_version') ?></p>
                </div>
                <div class="text-gray-500 text-sm">
                    <?= t('last_updated') ?>: <?= date('F j, Y') ?>
                </div>
            </div>
        </div>
    </footer>

    <script>
        // Auto-hide success messages after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            const successMessages = document.querySelectorAll('[id$="Message"]');
            successMessages.forEach(message => {
                setTimeout(() => {
                    if (message && message.parentNode) {
                        message.style.opacity = '0';
                        message.style.transition = 'opacity 0.5s';
                        setTimeout(() => {
                            if (message && message.parentNode) {
                                message.parentNode.removeChild(message);
                            }
                        }, 500);
                    }
                }, 5000);
            });
            
            // Add animation to table rows
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate-in');
            });
            
            // Add form ID for floating action button
            const form = document.querySelector('form');
            if (form) {
                form.id = 'assignmentsForm';
            }
        });
    </script>
</body>
</html>

 <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --dark: #1e293b;
            --light: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --hover-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.15);
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f4ff 100%);
            min-height: 100vh;
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .modal-overlay {
            background: rgba(0, 0, 0, 0.7);
            backdrop-filter: blur(5px);
        }

        .modal-content {
            animation: modalSlideIn 0.3s ease;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.2);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success) 0%, #0ca678 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, #0891b2 100%);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-assigned {
            background: #d1fae5;
            color: #065f46;
        }

        .status-unassigned {
            background: #fef3c7;
            color: #92400e;
        }

        .select-styled {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e");
            background-position: right 0.5rem center;
            background-repeat: no-repeat;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .table-row-hover:hover {
            background: linear-gradient(90deg, rgba(79, 70, 229, 0.03) 0%, rgba(6, 182, 212, 0.03) 100%);
        }

        .floating-action {
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            z-index: 40;
            animation: float 3s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        @media (max-width: 768px) {
            .floating-action {
                bottom: 1rem;
                right: 1rem;
            }
            
            .mobile-full-width {
                width: 100%;
                margin-bottom: 1rem;
            }
        }
        /* ================= DARK MODE SUPPORT ================= */
                .dark {
                    background: linear-gradient(135deg, #0f172a 0%, #020617 100%) !important;
                }
                .select{
                    color: #000;
                }

                .dark .glass-card {
                    background: rgba(15, 23, 42, 0.85);
                    border: 1px solid rgba(255, 255, 255, 0.05);
                }

                .dark table {
                    background: #020617;
                }

                .dark th {
                    background: #020617 !important;
                    color: #e5e7eb;
                }

                .dark td {
                    color: #e5e7eb;
                }

                .dark .gradient-bg {
                    background: linear-gradient(135deg, #020617 0%, #020617 100%);
                }

                .dark select,
                .dark input {
                    background: #020617;
                    color: #fff;
                    border-color: #334155;
                }

                .dark footer {
                    border-color: #334155;
                }

                .dark .status-assigned {
                    background: #064e3b;
                    color: #6ee7b7;
                }

                .dark .status-unassigned {
                    background: #451a03;
                    color: #fde68a;
                }

</style>