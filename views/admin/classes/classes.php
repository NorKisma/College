<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Classes & Subjects Management | School System',
        'header_title' => 'Class Manager',
        'header_subtitle' => 'Manage classes, subjects, and assignments',
        'user' => 'User',
        'total_classes' => 'Total Classes',
        'total_subjects' => 'Total Subjects',
        'active_teachers' => 'Active Teachers',
        'page_header' => 'Classes & Subjects',
        'page_description' => 'Manage class structures and subject assignments across all sections',
        'btn_add_class' => 'Add New Class',
        'btn_old_classes' => 'Old Classes',
        'section_header' => 'Section',
        'classes_count' => 'class(es)',
        'class_name' => 'Class Name',
        'assigned_subjects' => 'Assigned Subjects',
        'actions' => 'Actions',
        'students' => 'Students',
        'no_subjects_assigned' => 'No subjects assigned',
        'btn_details' => 'Details',
        'btn_edit' => 'Edit',
        'btn_assign' => 'Assign',
        'btn_delete' => 'Delete',
        'btn_attendance' => 'Attendance',
        'empty_state_title' => 'No Classes Found',
        'empty_state_message' => 'Get started by creating your first class. Assign subjects and teachers to build your curriculum.',
        'empty_state_button' => 'Create Your First Class',
        'modal_add_title' => 'Add New Class',
        'modal_edit_title' => 'Edit Class',
        'label_class_name' => 'Class Name',
        'label_section' => 'Section',
        'btn_cancel' => 'Cancel',
        'btn_add' => 'Add Class',
        'btn_save' => 'Save Changes',
        'modal_delete_title' => 'Delete Class',
        'modal_delete_message' => 'Are you sure you want to delete',
        'modal_delete_warning' => 'This action cannot be undone.',
        'btn_yes_delete' => 'Yes, Delete Class',
        'confirm_new_class' => 'Do you want to save the changes as a NEW class?',
        'confirm_new_class_warning' => 'This will duplicate students into this class',
        'footer_copyright' => '©',
        'footer_module' => 'Classes & Subjects Module'
    ],
    2 => [ // Arabic - Simple language
        'page_title' => 'إدارة الصفوف والمواد | النظام المدرسي',
        'header_title' => 'مدير الصفوف',
        'header_subtitle' => 'إدارة الصفوف والمواد والمهام',
        'user' => 'المستخدم',
        'total_classes' => 'إجمالي الصفوف',
        'total_subjects' => 'إجمالي المواد',
        'active_teachers' => 'المعلمون النشطون',
        'page_header' => 'الصفوف والمواد',
        'page_description' => 'إدارة هيكل الصفوف وتخصيص المواد في جميع الأقسام',
        'btn_add_class' => 'إضافة صف جديد',
        'btn_old_classes' => 'الصفوف القديمة',
        'section_header' => 'القسم',
        'classes_count' => 'صفوف',
        'class_name' => 'اسم الصف',
        'assigned_subjects' => 'المواد المخصصة',
        'actions' => 'الإجراءات',
        'students' => 'الطلاب',
        'no_subjects_assigned' => 'لا توجد مواد مخصصة',
        'btn_details' => 'تفاصيل',
        'btn_edit' => 'تعديل',
        'btn_assign' => 'تعيين',
        'btn_delete' => 'حذف',
        'btn_attendance' => 'الحضور',
        'empty_state_title' => 'لم يتم العثور على صفوف',
        'empty_state_message' => 'ابدأ بإنشاء صفك الأول. قم بتعيين المواد والمعلمين لبناء المنهج الدراسي.',
        'empty_state_button' => 'إنشاء صفك الأول',
        'modal_add_title' => 'إضافة صف جديد',
        'modal_edit_title' => 'تعديل الصف',
        'label_class_name' => 'اسم الصف',
        'label_section' => 'القسم',
        'btn_cancel' => 'إلغاء',
        'btn_add' => 'إضافة الصف',
        'btn_save' => 'حفظ التغييرات',
        'modal_delete_title' => 'حذف الصف',
        'modal_delete_message' => 'هل أنت متأكد أنك تريد حذف',
        'modal_delete_warning' => 'لا يمكن التراجع عن هذا الإجراء.',
        'btn_yes_delete' => 'نعم، احذف الصف',
        'confirm_new_class' => 'هل تريد حفظ التغييرات كصف جديد؟',
        'confirm_new_class_warning' => 'سيتم نسخ الطلاب إلى هذا الصف',
        'footer_copyright' => '©',
        'footer_module' => 'وحدة الصفوف والمواد'
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

// Authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) { header('Location: ../../auth/login.php'); exit; }

// Load current user
$me_stmt = $conn->prepare("SELECT u.id, u.username, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id=?");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
$my_role_name = $me['role_name'];

// Admin check
function is_admin() { global $my_role_name; return $my_role_name === 'admin'; }
function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}
// CSRF token
$csrf = csrf_token();
if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'], $_POST['section_id']) && is_admin()) {

    $name = trim($_POST['name']);
    $section_id = intval($_POST['section_id']);

    if($name !== '' && $section_id > 0){
        $stmt = $conn->prepare("INSERT INTO classes (name, section_id) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $section_id);
        $stmt->execute();

        header("Location: classes.php?created=1");
        exit;
    }
}

// Fetch sections
$sections = [];
$res = $conn->query("SELECT * FROM sections ORDER BY name ASC");
while ($r = $res->fetch_assoc()) $sections[$r['id']] = $r['name'];

// Fetch classes with section name
// Fetch classes with section name
$classes = [];
$sql = "
    SELECT c.*, 
           s.name AS section_name,
           -- total active students in this class
           (SELECT COUNT(*) 
            FROM class_students cs 
            WHERE cs.class_id = c.id AND cs.is_active = 1) AS total_active_students,
           -- total alumni in this class
           (SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id) AS alumni_count
    FROM classes c
    LEFT JOIN sections s ON c.section_id = s.id
    ORDER BY s.name ASC, c.name ASC
";
$res = $conn->query($sql);

if (!$res) {
    die("Query Error: " . $conn->error);
}

$classes_arr = [];
while ($r = $res->fetch_assoc()) {
    $total_active_students = (int)$r['total_active_students'];
    $alumni_count = (int)$r['alumni_count'];

    // Only include classes that have at least 1 active student
    if ($total_active_students > 0) {
        $classes_arr[] = $r;
    }
}
$classes = $classes_arr;


// Fetch subjects
$subjects = [];
$res = $conn->query("SELECT * FROM subjects ORDER BY name ASC");
while ($r = $res->fetch_assoc()) $subjects[$r['id']] = $r;

// Fetch teachers
$teachers = [];
$res = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM teachers WHERE status='active' ORDER BY first_name ASC");
while ($r = $res->fetch_assoc()) $teachers[] = $r;

// Fetch subjects assigned to class
function get_class_subjects($conn, $class_id){
    $arr = [];
    $stmt = $conn->prepare("
        SELECT cs.id AS cs_id, cs.teacher_id, s.name AS subject_name, s.code, s.credit_hours,
               CONCAT(t.first_name,' ',t.last_name) AS teacher_name
        FROM class_subjects cs
        JOIN subjects s ON s.id = cs.subject_id
        LEFT JOIN teachers t ON t.id = cs.teacher_id
        WHERE cs.class_id = ?
    ");
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) $arr[] = $row;
    return $arr;
}
// ================= THEME LOAD =================
$theme_mode = 1; // default light

$themeRes = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1");

if($themeRes && $row = $themeRes->fetch_assoc()){
    $theme_mode = (int)$row['mode']; // 1 = light, 0 = dark
}

?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- JQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Theme Tweaks */
        .ts-control {
            border-radius: 0.75rem;
            padding: 0.75rem;
            border-color: #e5e7eb;
        }
        .ts-wrapper.single .ts-control {
            background-image: none;
        }
        .dark .ts-control {
            background-color: #1f2937;
            border-color: #374151;
            color: #fff;
        }
        .dark .ts-dropdown {
            background-color: #1f2937;
            border-color: #374151;
            color: #fff;
        }
        .dark .ts-dropdown .option {
            color: #fff;
        }
        .dark .ts-dropdown .active {
            background-color: #374151;
            color: #fff;
        }
        .dark .ts-control input {
            color: #fff;
        }
    </style>  
</head>
<body class="<?= $theme_mode === 0 ? 'dark bg-gray-900 text-gray-100' : 'bg-gradient-to-br from-indigo-50 to-blue-100 text-gray-800' ?>">

    
    <!-- Header/Navigation -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex justify-between items-center">
                <div class="flex items-center space-x-3">
                    <div class="bg-white/20 p-2 rounded-lg">
                        <i class="fas fa-graduation-cap text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold"><?= t('header_title') ?></h1>
                        <p class="text-white/80 text-sm"><?= t('header_subtitle') ?></p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden md:flex items-center space-x-4">
                        <div class="text-right">
                            <p class="font-medium"><?= htmlspecialchars($me['username'] ?? t('user')) ?></p>
                            <p class="text-white/80 text-sm"><?= htmlspecialchars($my_role_name) ?></p>
                        </div>
                        <div class="h-10 w-10 bg-white/20 rounded-full flex items-center justify-center">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                    <div class="hamburger-menu">
                        <i class="fas fa-bars text-2xl"></i>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium select"><?= t('total_classes') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1 select"><?= count($classes) ?></h3>
                    </div>
                    <div class="bg-blue-100 p-3 rounded-full">
                        <i class="fas fa-chalkboard-teacher text-blue-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium select"><?= t('total_subjects') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1 select"><?= count($subjects) ?></h3>
                    </div>
                    <div class="bg-cyan-100 p-3 rounded-full">
                        <i class="fas fa-book-open text-cyan-600 text-xl"></i>
                    </div>
                </div>
            </div>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 text-sm font-medium select"><?= t('active_teachers') ?></p>
                        <h3 class="text-3xl font-bold text-gray-800 mt-1 select"><?= count($teachers) ?></h3>
                    </div>
                    <div class="bg-emerald-100 p-3 rounded-full">
                        <i class="fas fa-user-tie text-emerald-600 text-xl"></i>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Page Header -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div>
                <h2 class="text-3xl font-bold text-gray-800 select"><?= t('page_header') ?></h2>
                <p class="text-gray-600 mt-2 select"><?= t('page_description') ?></p>
            </div>
            
            <?php if(is_admin()): ?>
            <div class="flex space-x-3 mt-4 md:mt-0">
                <button onclick="openModal('addClassModal')" class="btn-primary text-white px-5 py-3 rounded-xl font-medium flex items-center shadow-lg">
                    <i class="fas fa-plus-circle mr-2"></i> <?= t('btn_add_class') ?>
                </button>
                <a href="old/old_classes.php" class="btn-primary text-white px-5 py-3 rounded-xl font-medium flex items-center shadow-lg">
                    <i class="fas fa-folder-open mr-2"></i> <?= t('btn_old_classes') ?>
                </a>
            </div>
            <?php endif; ?>
        </div>
        
        <!-- Classes Table -->
        <div class="animate-in">
            <?php
// Before the loop starts, fetch student counts for all classes
$class_ids = array_column($classes, 'id');
$class_student_counts = [];
if(!empty($class_ids)){
    $ids = implode(',', $class_ids);
    $res = $conn->query("SELECT class_id, COUNT(student_id) as cnt FROM class_students GROUP BY class_id");
    while($row = $res->fetch_assoc()){
        $class_student_counts[$row['class_id']] = $row['cnt'];
    }
}
?>

            <?php 
            $current_section = '';
            $section_count = 0;
            foreach($classes as $class):
                if($class['section_name'] !== $current_section):
                    if($current_section !== '') echo '</tbody></table></div>';
                    $section_count++;
                    ?>
                    <div class="mb-10">
                        <div class="flex items-center mb-4">
                            <div class="section-header">
                                <h3 class="text-xl font-bold text-gray-800 select"><?= htmlspecialchars($class['section_name'] ?? t('section_header')) ?></h3>
                                <p class="text-gray-600 text-sm select"><?= t('section_header') ?> <?= $section_count ?></p>
                            </div>
                            <div class="ml-4 bg-blue-100 text-blue-800 text-xs font-medium px-3 py-1 rounded-full">
                                <?php 
                                $section_class_count = 0;
                                foreach($classes as $c) {
                                    if($c['section_name'] === $class['section_name']) $section_class_count++;
                                }
                                echo $section_class_count . ' ' . t('classes_count');
                                ?>
                            </div>
                        </div>
                        
            <div class="table-container glass-card">
                <table class="min-w-full classesTable">
                                <thead class="sticky-header">
                                    <tr class="bg-gray-50/80 border-b">
                                        <th class="text-left px-6 py-4 font-semibold text-gray-700"><?= t('class_name') ?></th>
                                        <th class="text-left px-6 py-4 font-semibold text-gray-700"><?= t('assigned_subjects') ?></th>
                                        <th class="text-left px-6 py-4 font-semibold text-gray-700"><?= t('actions') ?></th>
                                    </tr>
                                </thead>
            <tbody>
                    <?php 
                    $current_section = $class['section_name'];
                endif;
                ?>
                <tr class="border-b hover:bg-gray-50/50 transition-colors">
                    <td class="px-6 py-5">
                        <div class="flex items-center">
                            <div class="bg-blue-100 text-blue-800 p-2 rounded-lg mr-4">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <div>
                                <h4 class="font-semibold text-gray-800 select"><?= htmlspecialchars($class['name']) ?></h4>
                                <p class="text-gray-500 text-sm select">
                            <?= t('students') ?>: <?= $class_student_counts[$class['id']] ?? 0 ?>
                        </p>

                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-5">
                        <?php 
                        $class_subjects = get_class_subjects($conn, $class['id']);
                        if(empty($class_subjects)): ?>
                            <span class="text-gray-400 italic"><?= t('no_subjects_assigned') ?></span>
                        <?php else: ?>
                            <div class="flex flex-wrap gap-1">
                                <?php foreach($class_subjects as $cs): ?>
                                    <div class="subject-badge bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-xs px-2 py-1 rounded-lg flex flex-col items-center justify-center max-w-[80px] break-words">
                                        <div class="font-medium"><?= htmlspecialchars($cs['subject_name']) ?></div>
                                        <div class="flex items-center text-[10px] text-gray-500 dark:text-gray-300 mt-0.5">
                                            <i class="fas fa-clock mr-1"></i>
                                            <span><?= $cs['credit_hours'] ?? 0 ?>h</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                                                    <?php endif; ?>
                    </td>
                                   <?php 
$has_alumni = isset($class['alumni_count']) && $class['alumni_count'] > 0;
$btn_disabled_class = $has_alumni ? 'opacity-50 cursor-not-allowed' : '';
?>
<td class="px-6 py-5"> 
    <div class="flex flex-col md:flex-row gap-2">
        <!-- Details (always clickable) -->
        <a href="class_details.php?id=<?= $class['id'] ?>"
           class="bg-indigo-600 hover:bg-indigo-700 text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm transition">
            <i class="fas fa-info-circle mr-2"></i> <?= t('btn_details') ?>
        </a>

        <?php if(!is_finance()): ?>
            <!-- Edit -->
            <button onclick="openEditClassModal(<?= $class['id'] ?>, '<?= htmlspecialchars($class['name']) ?>', <?= $class['section_id'] ?? 0 ?>)"
                    class="btn-warning text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm <?= $btn_disabled_class ?>"
                    <?= $has_alumni ? 'disabled' : '' ?>>
                <i class="fas fa-edit mr-2"></i> <?= t('btn_edit') ?>
            </button>

            <!-- Assign -->
            <a <?= $has_alumni ? 'onclick="return false;"' : '' ?>
               href="<?= $has_alumni ? '#' : "class_subject_assign.php?class_id={$class['id']}" ?>"
               class="btn-secondary text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm <?= $btn_disabled_class ?>">
                <i class="fas fa-link mr-2"></i> <?= t('btn_assign') ?>
            </a>

            <!-- Delete -->
            <button onclick="confirmDelete(<?= $class['id'] ?>, '<?= htmlspecialchars($class['name']) ?>')"
                    class="btn-danger text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm <?= $btn_disabled_class ?>"
                    <?= $has_alumni ? 'disabled' : '' ?>>
                <i class="fas fa-trash-alt mr-2"></i> <?= t('btn_delete') ?>
            </button>
        <?php endif; ?>

        <!-- Attendance (always clickable) -->
        <a href="attendance_class_view.php?class_id=<?= (int)$class['id'] ?>"
           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg font-medium flex items-center justify-center text-sm transition-colors">
            <i class="fas fa-user-check mr-2"></i> <?= t('btn_attendance') ?>
        </a>
    </div>
</td>



                </tr>
            <?php endforeach; ?>
            </tbody></table></div>
        
        <!-- Empty State -->
        <?php if(empty($classes)): ?>
        <div class="glass-card rounded-xl p-12 text-center shadow-lg">
            <div class="text-gray-400 text-6xl mb-4">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <h3 class="text-2xl font-bold text-gray-600 mb-2"><?= t('empty_state_title') ?></h3>
            <p class="text-gray-500 max-w-md mx-auto mb-6"><?= t('empty_state_message') ?></p>
            <?php if(is_admin()): ?>
            <button onclick="openModal('addClassModal')" class="btn-primary text-white px-6 py-3 rounded-xl font-medium inline-flex items-center">
                <i class="fas fa-plus-circle mr-2"></i> <?= t('empty_state_button') ?>
            </button>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </main>
    
    <!-- Add/Edit Class Modal -->
    <!-- Add New Class Modal -->
<div id="addClassModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="gradient-bg text-white rounded-t-2xl p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold"><?= t('modal_add_title') ?></h3>
                <button onclick="closeModal('addClassModal')" class="text-white/80 hover:text-white text-2xl">&times;</button>
            </div>
        </div>
        <form method="post" action="classes.php" class="p-6" id="addClassForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

            <!-- Class Name -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2"><?= t('label_class_name') ?></label>
                <input type="text" name="name" class="w-full p-3 border rounded-xl" required>
            </div>

            <!-- Section -->
            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-medium mb-2"><?= t('label_section') ?></label>
                <select name="section_id" class="w-full p-3 border rounded-xl tom-select" required>
                    <?php foreach($sections as $id => $name): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('addClassModal')"><?= t('btn_cancel') ?></button>
                <button type="submit" class="btn-primary text-white px-5 py-3 rounded-xl font-medium"><?= t('btn_add') ?></button>
            </div>
        </form>
    </div>
</div>

<!-- Edit Class Modal -->
<div id="editClassModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
    <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
        <div class="gradient-bg text-white rounded-t-2xl p-6">
            <div class="flex justify-between items-center">
                <h3 class="text-xl font-bold"><?= t('modal_edit_title') ?></h3>
                <button onclick="closeModal('editClassModal')" class="text-white/80 hover:text-white text-2xl">&times;</button>
            </div>
        </div>
        <form method="post" action="" class="p-6" id="editClassForm">
            <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
            <input type="hidden" name="save_as_new" id="saveAsNew" value="0">

            <!-- Class Name -->
            <div class="mb-6">
                <label class="block text-gray-700 text-sm font-medium mb-2"><?= t('label_class_name') ?></label>
                <input type="text" id="editClassName" name="name" class="w-full p-3 border rounded-xl" required>
            </div>

            <!-- Section -->
            <div class="mb-8">
                <label class="block text-gray-700 text-sm font-medium mb-2"><?= t('label_section') ?></label>
                <select id="editSectionSelect" name="section_id" class="w-full p-3 border rounded-xl tom-select" required>
                    <?php foreach($sections as $id => $name): ?>
                    <option value="<?= $id ?>"><?= htmlspecialchars($name) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="flex justify-end space-x-3">
                <button type="button" onclick="closeModal('editClassModal')"><?= t('btn_cancel') ?></button>
                <button type="submit" class="btn-primary text-white px-5 py-3 rounded-xl font-medium"><?= t('btn_save') ?></button>
            </div>
        </form>
    </div>
</div>


    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 z-50 hidden items-center justify-center modal-overlay">
        <div class="modal-content bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-2"><?= t('modal_delete_title') ?></h3>
                    <p class="text-gray-600"><?= t('modal_delete_message') ?> <span id="classNameToDelete" class="font-semibold"></span>? <?= t('modal_delete_warning') ?></p>
                </div>
                <div class="flex justify-center space-x-4">
                    <button onclick="closeModal('deleteConfirmModal')" 
                            class="px-5 py-3 border border-gray-300 text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition">
                        <?= t('btn_cancel') ?>
                    </button>
                    <button id="confirmDeleteButton" 
                            class="btn-danger text-white px-5 py-3 rounded-xl font-medium">
                        <?= t('btn_yes_delete') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .dark .select{
            color: #fff;
        }
    </style>
    
    <!-- Footer -->
    <footer class="mt-12 pt-8 border-t border-gray-200">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600"><?= t('footer_copyright') ?> <?= date('Y') ?> School Management System</p>
                    <p class="text-gray-500 text-sm select"><?= t('footer_module') ?></p>
                </div>
               
            </div>
        </div>
    </footer>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.classesTable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "Search...",
                    lengthMenu: "<?= $currentLangId == 2 ? 'عرض _MENU_ سجلات' : 'Show _MENU_ entries' ?>",
                    info: "<?= $currentLangId == 2 ? 'عرض _START_ إلى _END_ من _TOTAL_ صفوف' : 'Showing _START_ to _END_ of _TOTAL_ classes' ?>",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'left' : 'right' ?>"></i>',
                        previous: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'right' : 'left' ?>"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1 }
                ]
            });
        });

        // Modal Functions
        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
            document.getElementById(id).classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
        
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
            document.getElementById(id).classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
        
     function openEditClassModal(id, name, section_id) {
    openModal('editClassModal');
    const form = document.getElementById('editClassForm');
    form.action = 'classes_update.php?id=' + id;

    document.getElementById('editClassName').value = name;
    document.getElementById('editSectionSelect').value = section_id;
    document.getElementById('saveAsNew').value = 0;

    // Store old values in data attributes for comparison
    form.dataset.originalName = name;
    form.dataset.originalSection = section_id;
}


// Form submit handler
// Form submit handler
// Edit Class Form submit handler
document.getElementById('editClassForm').addEventListener('submit', function(e) {
    const form = e.target;
    const newName = document.getElementById('editClassName').value.trim();
    const newSection = document.getElementById('editSectionSelect').value;

    const oldName = form.dataset.originalName || '';
    const oldSection = form.dataset.originalSection || '';

    // Check if changes are made
    if (newName !== oldName || newSection !== oldSection) {
        const saveAsNew = confirm(
            "<?= t('confirm_new_class') ?>\n(<?= t('confirm_new_class_warning') ?>)"
        );

        if (saveAsNew) {
            // User clicked OK → save_as_new = 1
            form.querySelector('#saveAsNew').value = 1;
        } else {
            // User clicked Cancel → save_as_new = 0
            form.querySelector('#saveAsNew').value = 0;
            // Normal edit will happen (update existing class)
        }
    } else {
        // No changes → ensure save_as_new = 0
        form.querySelector('#saveAsNew').value = 0;
    }
});



        
        function confirmDelete(classId, className) {
            document.getElementById('classNameToDelete').textContent = className;
            openModal('deleteConfirmModal');
            
            // Set up delete button
            const deleteBtn = document.getElementById('confirmDeleteButton');
            deleteBtn.onclick = function() {
                window.location.href = 'classes_delete.php?id=' + classId;
            };
        }
        
        // Close modals when clicking outside
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('modal-overlay')) {
                event.target.classList.add('hidden');
                event.target.classList.remove('flex');
                document.body.style.overflow = 'auto';
            }
        });
        
        // Add animation to table rows
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('tbody tr');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate-in');
            });
            
            // Mobile menu toggle
            const hamburger = document.querySelector('.hamburger-menu');
            if (hamburger) {
                hamburger.addEventListener('click', function() {
                    const mobileMenu = document.querySelector('.mobile-menu');
                    if (mobileMenu) {
                        mobileMenu.classList.toggle('hidden');
                    }
                });
            }
        });
        // Open Add Class Modal
function openAddClassModal() {
    openModal('addClassModal');
    document.getElementById('addClassForm').reset();
}

// Open Edit Class Modal
function openEditClassModal(id, name, section_id) {
    openModal('editClassModal');
    const form = document.getElementById('editClassForm');
    form.action = 'classes_update.php?id=' + id;

    document.getElementById('editClassName').value = name;
    
    // Update Tom Select value
    const select = document.getElementById('editSectionSelect');
    if (select.tomselect) {
        select.tomselect.setValue(section_id);
    } else {
        select.value = section_id;
    }
    
    document.getElementById('saveAsNew').value = 0;
}

// Initialize Tom Select
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.tom-select').forEach((el) => {
        new TomSelect(el, {
            create: false,
            sortField: { field: "text", direction: "asc" },
            plugins: ['remove_button']
        });
    });
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
            --gray: #64748b;
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

        .subject-badge {
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            border-left: 3px solid var(--primary);
            transition: all 0.3s ease;
        }

        .subject-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(79, 70, 229, 0.1);
        }

        .section-header {
            position: relative;
            padding-left: 1.5rem;
        }

        .section-header::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(to bottom, var(--primary), var(--secondary));
            border-radius: 2px;
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

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }

        .sticky-header {
            position: sticky;
            top: 0;
            z-index: 10;
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

        .btn-warning {
            background: linear-gradient(135deg, var(--warning) 0%, #e67700 100%);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger) 0%, #c92a2a 100%);
        }

        .btn-secondary {
            background: linear-gradient(135deg, var(--secondary) 0%, #0891b2 100%);
        }

        .stat-card {
            transition: all 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-5px);
        }

        .hamburger-menu {
            display: none;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .hamburger-menu {
                display: block;
            }
            
            .mobile-full-width {
                width: 100%;
                margin-bottom: 1rem;
            }
            
            .table-container {
                margin: 0 -1rem;
                border-radius: 0;
            }
            
            .modal-content {
                width: 95%;
                margin: 0 10px;
            }
        }
        /* ================= DARK MODE SUPPORT ================= */
            .dark {
                background: linear-gradient(135deg, #0f172a 0%, #020617 100%) !important;
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
                color: #ffffffff;
            }

            .dark td {
                color: #ffffffff;
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
                color: #ffffffff;
            }

    </style>