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
        'page_title' => 'Teachers Management | School System',
        'header_title' => 'Teachers Management',
        'header_subtitle' => 'Manage faculty members and their assignments',
        'total_teachers' => 'Teachers',
        'total_faculty' => 'Total Faculty',
        
        // View Toggle
        'view_list' => 'List View',
        'view_sections' => 'Section View',
        
        
        
        // Stats Cards
        'stat_active_teachers' => 'Active Teachers',
        'stat_total_subjects' => 'Total Subjects',
        'stat_inactive' => 'Inactive',
        'stat_suspended' => 'Suspended',
        'stat_of_total' => '% of total',
        'stat_average' => 'Average',
        
        // Table Headers
        'table_teacher' => 'Teacher',
        'table_contact' => 'Contact',
        'table_salary' => 'Salary',
        'table_status' => 'Status',
        'table_workload' => 'Workload',
        'table_actions' => 'Actions',
        'table_monthly' => 'Monthly',
        'table_subjects' => 'Subjects',
        'table_classes' => 'Classes',
        
        // Table Actions
        'btn_quick_view' => 'Quick View',
        'btn_full' => 'Full',
        'btn_edit' => 'Edit',
        'btn_delete' => 'Delete',
        'btn_view' => 'View',
        
        // Status Badges
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'status_suspended' => 'Suspended',
        
        // Section View
        'section_overview' => 'Section Overview',
        'teachers_count' => 'teacher(s)',
        
        // Empty States
        'no_teachers_found' => 'No Teachers Found',
        'empty_teachers_message' => 'Add teachers to start managing your faculty.',
        'no_section_data' => 'No Section Data Available',
        'empty_section_message' => 'Assign teachers to classes to see the section view.',
        
        // Modal
        'modal_profile' => 'Teacher Profile',
        'modal_close' => 'Close',
        'modal_full_profile' => 'View Full Profile',
        'modal_delete_teacher' => 'Delete Teacher',
        'modal_delete_message' => 'Are you sure you want to delete',
        'modal_cancel' => 'Cancel',
        'modal_confirm_delete' => 'Delete Teacher',
        
        // Modal Sections
        'modal_personal_info' => 'Personal Info',
        'modal_contact' => 'Contact',
        'modal_workload' => 'Workload',
        'modal_additional_info' => 'Additional Info',
        
        // Labels
        'label_gender' => 'Gender',
        'label_qualification' => 'Qualification',
        'label_hire_date' => 'Hire Date',
        'label_phone' => 'Phone',
        'label_email' => 'Email',
        'label_address' => 'Address',
        'label_emergency_contact' => 'Emergency Contact',
        'label_blood_group' => 'Blood Group',
        'label_joined' => 'Joined',
        'label_hired' => 'Hired',
        
        // Search
        'search_placeholder' => 'Search teachers...',
        
        // Footer
        'footer_copyright' => '©',
        'footer_version' => 'Teachers Management v2.0',
        'footer_stats' => 'teachers • total subjects'
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة المعلمين | النظام المدرسي',
        'header_title' => 'إدارة المعلمين',
        'header_subtitle' => 'إدارة أعضاء هيئة التدريس ومهامهم',
        'total_teachers' => 'معلمين',
        'total_faculty' => 'إجمالي أعضاء هيئة التدريس',
        
        // View Toggle
        'view_list' => 'عرض القائمة',
        'view_sections' => 'عرض الأقسام',
        
       
        
        // Stats Cards
        'stat_active_teachers' => 'المعلمون النشطون',
        'stat_total_subjects' => 'إجمالي المواد',
        'stat_inactive' => 'غير نشط',
        'stat_suspended' => 'معلق',
        'stat_of_total' => '% من الإجمالي',
        'stat_average' => 'المتوسط',
        
        // Table Headers
        'table_teacher' => 'المعلم',
        'table_contact' => 'الاتصال',
        'table_salary' => 'الراتب',
        'table_status' => 'الحالة',
        'table_workload' => 'عبء العمل',
        'table_actions' => 'الإجراءات',
        'table_monthly' => 'شهري',
        'table_subjects' => 'المواد',
        'table_classes' => 'الصفوف',
        
        // Table Actions
        'btn_quick_view' => 'عرض سريع',
        'btn_full' => 'كامل',
        'btn_edit' => 'تعديل',
        'btn_delete' => 'حذف',
        'btn_view' => 'عرض',
        
        // Status Badges
        'status_active' => 'نشط',
        'status_inactive' => 'غير نشط',
        'status_suspended' => 'معلق',
        
        // Section View
        'section_overview' => 'نظرة عامة على القسم',
        'teachers_count' => 'معلم(ين)',
        
        // Empty States
        'no_teachers_found' => 'لم يتم العثور على معلمين',
        'empty_teachers_message' => 'أضف معلمين لبدء إدارة أعضاء هيئة التدريس.',
        'no_section_data' => 'لا توجد بيانات قسم',
        'empty_section_message' => 'قم بتعيين معلمين للصفوف لرؤية عرض الأقسام.',
        
        // Modal
        'modal_profile' => 'ملف المعلم',
        'modal_close' => 'إغلاق',
        'modal_full_profile' => 'عرض الملف الكامل',
        'modal_delete_teacher' => 'حذف المعلم',
        'modal_delete_message' => 'هل أنت متأكد أنك تريد حذف',
        'modal_cancel' => 'إلغاء',
        'modal_confirm_delete' => 'حذف المعلم',
        
        // Modal Sections
        'modal_personal_info' => 'المعلومات الشخصية',
        'modal_contact' => 'الاتصال',
        'modal_workload' => 'عبء العمل',
        'modal_additional_info' => 'معلومات إضافية',
        
        // Labels
        'label_gender' => 'الجنس',
        'label_qualification' => 'المؤهل',
        'label_hire_date' => 'تاريخ التعيين',
        'label_phone' => 'الهاتف',
        'label_email' => 'البريد الإلكتروني',
        'label_address' => 'العنوان',
        'label_emergency_contact' => 'جهة اتصال الطوارئ',
        'label_blood_group' => 'فصيلة الدم',
        'label_joined' => 'انضم',
        'label_hired' => 'تم التعيين',
        
        // Search
        'search_placeholder' => 'بحث في المعلمين...',
        
        // Footer
        'footer_copyright' => '©',
        'footer_version' => 'إدارة المعلمين الإصدار 2.0',
        'footer_stats' => 'معلمين • إجمالي المواد'
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

$view = $_GET['view'] ?? 'list'; // list | sections
$logged_in_id = $_SESSION['user_id'] ?? null;


function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}
function is_member() {
    return ($_SESSION['role'] ?? '') === 'member';
}

// ================= NORMAL TEACHERS LIST =================
$teachers = [];
$res = $conn->query("
    SELECT t.*,
           COUNT(DISTINCT cs.id) AS total_subjects,
           COUNT(DISTINCT cs.class_id) AS total_classes
    FROM teachers t
    LEFT JOIN class_subjects cs ON cs.teacher_id = t.id
    GROUP BY t.id
    ORDER BY t.created_at DESC
");
while($row = $res->fetch_assoc()) $teachers[] = $row;

// ================= THEME =================
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");

if($theme_res && $theme_row = $theme_res->fetch_assoc()){
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

// ================= SECTION VIEW =================
$sectionData = [];
if($view === 'sections'){
    $res = $conn->query("
    SELECT DISTINCT
           sec.id AS section_id,
           sec.name AS section_name,
           c.id AS class_id,
           c.name AS class_name,
           t.id AS teacher_id,
           CONCAT(t.first_name,' ',t.last_name) AS teacher_name,
           s.name AS subject_name
    FROM sections sec
    INNER JOIN classes c ON c.section_id = sec.id

    -- Only classes with ACTIVE students
    INNER JOIN class_students cls 
        ON cls.class_id = c.id AND cls.is_active = 1

    LEFT JOIN class_subjects cs ON cs.class_id = c.id
    LEFT JOIN teachers t ON t.id = cs.teacher_id
    LEFT JOIN subjects s ON s.id = cs.subject_id

    ORDER BY sec.name, c.name, t.first_name
");


    while($r = $res->fetch_assoc()){
      $sectionData[$r['section_name']]
    [$r['class_name']]
    [$r['teacher_id']]['name'] = $r['teacher_name'];

$sectionData[$r['section_name']]
    [$r['class_name']]
    [$r['teacher_id']]['subjects'][] = $r['subject_name'];

    }
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>"
      class="<?= $theme === 1 ? 'light' : '' ?> min-h-screen">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <!-- JQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
   
</head>
<body class="<?= $theme === 1 ? 'light' : '' ?> min-h-screen">

    <!-- Header/Navigation -->
    <header class="gradient-bg text-white shadow-lg">
        <div class="container mx-auto px-4 py-4">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                <div class="mb-4 md:mb-0">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-3 rounded-xl mr-4">
                            <i class="fas fa-chalkboard-teacher text-2xl"></i>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold"><?= t('header_title') ?></h1>
                            <p class="text-white/80 text-sm"><?= t('header_subtitle') ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <div class="hidden md:flex items-center space-x-3 bg-white/10 px-4 py-2 rounded-xl">
                        <div class="text-right">
                            <p class="font-medium"><?= count($teachers) ?> <?= t('total_teachers') ?></p>
                            <p class="text-white/80 text-sm"><?= t('total_faculty') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-8">
        <!-- View Toggle & Stats -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
            <div class="mb-4 md:mb-0">
                <div class="flex space-x-1 border-b border-gray-200 dark:border-gray-700">
                    <a href="?view=list" 
                       class="px-4 py-2 font-medium <?= $view === 'list' ? 'tab-active' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' ?>">
                        <i class="fas fa-list mr-2"></i> <?= t('view_list') ?>
                    </a>
                    <a href="?view=sections" 
                       class="px-4 py-2 font-medium <?= $view === 'sections' ? 'tab-active' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-300' ?>">
                        <i class="fas fa-layer-group mr-2"></i> <?= t('view_sections') ?>
                    </a>
                </div>
            </div>
            
            
        </div>

        <!-- Quick Stats -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <?php
            $active_count = array_filter($teachers, fn($t) => $t['status'] === 'active');
            $inactive_count = array_filter($teachers, fn($t) => $t['status'] === 'inactive');
            $suspended_count = array_filter($teachers, fn($t) => $t['status'] === 'suspended');
            $total_subjects = array_sum(array_column($teachers, 'total_subjects'));
            ?>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_active_teachers') ?></p>
                        <h3 class="text-3xl font-bold mt-1"><?= count($active_count) ?></h3>
                    </div>
                    <div class="bg-emerald-100 dark:bg-emerald-900/30 p-3 rounded-full">
                        <i class="fas fa-user-check text-emerald-600 dark:text-emerald-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-emerald-600 dark:text-emerald-400">
                    <i class="fas fa-arrow-up mr-1"></i> <?= count($teachers) > 0 ? round((count($active_count)/count($teachers))*100, 1) : 0 ?>%
                </div>
            </div>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_total_subjects') ?></p>
                        <h3 class="text-3xl font-bold mt-1"><?= $total_subjects ?></h3>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900/30 p-3 rounded-full">
                        <i class="fas fa-book-open text-blue-600 dark:text-blue-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <?= t('stat_average') ?>: <?= count($teachers) > 0 ? round($total_subjects/count($teachers), 1) : 0 ?>
                </div>
            </div>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_inactive') ?></p>
                        <h3 class="text-3xl font-bold mt-1"><?= count($inactive_count) ?></h3>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 p-3 rounded-full">
                        <i class="fas fa-user-slash text-gray-600 dark:text-gray-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                    <?= count($inactive_count) > 0 ? round((count($inactive_count)/count($teachers))*100, 1) : 0 ?>% <?= t('stat_of_total') ?>
                </div>
            </div>
            
            <div class="stat-card glass-card rounded-xl p-6 shadow-lg">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_suspended') ?></p>
                        <h3 class="text-3xl font-bold mt-1"><?= count($suspended_count) ?></h3>
                    </div>
                    <div class="bg-red-100 dark:bg-red-900/30 p-3 rounded-full">
                        <i class="fas fa-user-times text-red-600 dark:text-red-400 text-xl"></i>
                    </div>
                </div>
                <div class="mt-2 text-sm text-red-600 dark:text-red-400">
                    <?= count($suspended_count) > 0 ? round((count($suspended_count)/count($teachers))*100, 1) : 0 ?>%
                </div>
            </div>
        </div>

        <?php if($view === 'list'): ?>
        <!-- LIST VIEW -->
        <div class="glass-card rounded-2xl shadow-lg overflow-hidden animate-in">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
                    <h2 class="text-xl font-bold"><?= t('header_title') ?></h2>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700" id="teachersTable">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_teacher') ?>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_contact') ?>
                            </th>
                                                    <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_salary') ?>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_status') ?>
                            </th>
    
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_workload') ?>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider">
                                <?= t('table_actions') ?>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                        <?php foreach($teachers as $i => $t): 
                            $initials = substr($t['first_name'], 0, 1) . substr($t['last_name'], 0, 1);
                        ?>
                        <tr class="hover:bg-gray-30 dark:hover:bg-gray-800/30 transition-colors teacher-row" 
                            data-search="<?= strtolower($t['first_name'] . ' ' . $t['last_name'] . ' ' . $t['employee_code'] . ' ' . $t['email']) ?>">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <div class="teacher-avatar">
                                        <?= $initials ?>
                                    </div>
                                    <div class="ml-4">
                                        <div class="font-semibold"><?= $t['first_name'] . ' ' . $t['last_name'] ?></div>
                                        <div class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-id-card mr-1"></i> <?= $t['employee_code'] ?>
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-1">
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-envelope mr-2 text-gray-400 w-4"></i>
                                        <span class="truncate max-w-xs"><?= $t['email'] ?></span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas fa-phone mr-2 text-gray-400 w-4"></i>
                                        <span><?= $t['phone'] ?></span>
                                    </div>
                                    <div class="flex items-center text-sm">
                                        <i class="fas <?= $t['gender'] === 'female' ? 'fa-venus' : 'fa-mars' ?> mr-2 text-gray-400 w-4"></i>
                                        <span><?= ucfirst($t['gender'] ?? 'N/A') ?></span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                            <div class="text-sm font-semibold">
                                $<?= number_format((float)$t['salary'], 2) ?>
                            </div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">
                                <?= t('table_monthly') ?>
                            </div>
                        </td>

                            <td class="px-6 py-4">
                                <?php if($t['status'] == 'active'): ?>
                                    <span class="status-badge status-active">
                                        <i class="fas fa-check-circle mr-1"></i> <?= t('status_active') ?>
                                    </span>
                                <?php elseif($t['status'] == 'inactive'): ?>
                                    <span class="status-badge status-inactive">
                                        <i class="fas fa-pause-circle mr-1"></i> <?= t('status_inactive') ?>
                                    </span>
                                <?php else: ?>
                                    <span class="status-badge status-suspended">
                                        <i class="fas fa-times-circle mr-1"></i> <?= t('status_suspended') ?>
                                    </span>
                                <?php endif; ?>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    <?= t('label_hired') ?>: <?= date('M j, Y', strtotime($t['hire_date'])) ?>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm"><?= t('table_subjects') ?></span>
                                        <span class="font-semibold"><?= $t['total_subjects'] ?></span>
                                    </div>
                                    <div class="flex items-center justify-between">
                                        <span class="text-sm"><?= t('table_classes') ?></span>
                                        <span class="font-semibold"><?= $t['total_classes'] ?></span>
                                    </div>
                                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                                        <div class="bg-blue-600 h-1.5 rounded-full" 
                                             style="width: <?= min(($t['total_subjects'] / max(array_column($teachers, 'total_subjects'))) * 100, 100) ?>%">
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="flex flex-col space-y-2">
                                    <button onclick='openViewModal(<?= json_encode($t) ?>)'
                                            class="btn-primary text-white px-3 py-2 rounded-lg text-sm font-medium flex items-center justify-center">
                                        <i class="fas fa-eye mr-2"></i> <?= t('btn_quick_view') ?>
                                    </button>
                                    <div class="grid grid-cols-2 gap-2">
                                        <a href="teacher_view.php?id=<?= $t['id'] ?>"
                                           class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center justify-center">
                                            <i class="fas fa-external-link-alt mr-1"></i> <?= t('btn_full') ?>
                                        </a>
                                        <?php if(!is_finance() && !is_member()): ?>
                                        <a href="teacher_edit.php?id=<?= $t['id'] ?>"
                                           class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center justify-center">
                                            <i class="fas fa-edit mr-1"></i> <?= t('btn_edit') ?>
                                        </a> 
                                    </div>
                                    <button onclick="confirmDelete(<?= $t['id'] ?>, '<?= addslashes($t['first_name'] . ' ' . $t['last_name']) ?>')"
                                            class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded-lg text-xs font-medium flex items-center justify-center">
                                        <i class="fas fa-trash-alt mr-1"></i> <?= t('btn_delete') ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if(empty($teachers)): ?>
            <div class="p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 dark:text-gray-300 mb-2"><?= t('no_teachers_found') ?></h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6"><?= t('empty_teachers_message') ?></p>
            </div>
            <?php endif; ?>
        </div>

        <?php else: ?>
        <!-- SECTION VIEW -->
        <div class="space-y-8 animate-in">
            <?php foreach($sectionData as $section => $classes): ?>
            <div class="glass-card rounded-2xl shadow-lg overflow-hidden hover-card">
                <div class="gradient-bg text-white px-6 py-4">
                    <div class="flex items-center">
                        <div class="bg-white/20 p-2 rounded-lg mr-4">
                            <i class="fas fa-layer-group text-xl"></i>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold"><?= htmlspecialchars($section) ?></h2>
                            <p class="text-white/80 text-sm"><?= t('section_overview') ?></p>
                        </div>
                    </div>
                </div>
                
                <div class="p-6">
                    <?php foreach($classes as $className => $teachers): 
                        $teacherCount = count($teachers);
                    ?>
                    <div class="mb-6 last:mb-0">
                        <div class="flex items-center mb-4">
                            <div class="bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 p-2 rounded-lg mr-3">
                                <i class="fas fa-school"></i>
                            </div>
                            <div>
                                <h3 class="font-bold text-lg"><?= htmlspecialchars($className) ?></h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm">
                                    <?= $teacherCount ?> <?= t('teachers_count') ?>
                                </p>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 ml-8">
                            <?php foreach($teachers as $teacherId => $teacherData):
                    $teacherName = $teacherData['name'];
                    $subjects = $teacherData['subjects'];

                                $subjectCount = count($subjects);
                            ?>
                            <div class="bg-gray-50 dark:bg-gray-800/50 p-4 rounded-xl border border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <h4 class="font-semibold text-black">
                <?= htmlspecialchars($teacherName) ?>
            </h4>

                                        <p class="text-sm text-gray-500 dark:text-gray-400">
                                            <i class="fas fa-book mr-1"></i> <?= $subjectCount ?> <?= $subjectCount !== 1 ? t('table_subjects') : substr(t('table_subjects'), 0, -1) ?>
                                        </p>
                                    </div>
                                    <div class="teacher-avatar text-xs">
                                        <?= substr(explode(' ', $teacherName)[0], 0, 1) . substr(explode(' ', $teacherName)[1] ?? '', 0, 1) ?>
                                    </div>
                                </div>
                                
                                <div class="mb-4">
                                    <?php foreach($subjects as $sub): ?>
                                        <span class="subject-chip">
                                            <i class="fas fa-bookmark mr-1 text-xs"></i> <?= htmlspecialchars($sub) ?>
                                        </span>
                                    <?php endforeach; ?>
                                </div>
                                
                               <div class="flex space-x-2">
                        <a href="teacher_view.php?id=<?= $teacherId ?>"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg text-xs font-medium flex-1 flex items-center justify-center">
                            <i class="fas fa-eye mr-1"></i> <?= t('btn_view') ?>
                        </a>

                        <a href="teacher_edit.php?id=<?= $teacherId ?>"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg text-xs font-medium flex-1 flex items-center justify-center">
                            <i class="fas fa-edit mr-1"></i> <?= t('btn_edit') ?>
                        </a>
                    </div>

                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if(empty($sectionData)): ?>
            <div class="glass-card rounded-2xl p-12 text-center">
                <div class="text-gray-400 text-6xl mb-4">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h3 class="text-2xl font-bold text-gray-600 dark:text-gray-300 mb-2"><?= t('no_section_data') ?></h3>
                <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto mb-6"><?= t('empty_section_message') ?></p>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Floating Add Button -->
         <?php if(!is_finance() && !is_member()): ?>
    <div class="floating-btn">
        <a href="add_teachers.php"
           class="btn-primary text-white px-6 py-4 rounded-full shadow-xl flex items-center justify-center">
            <i class="fas fa-plus text-xl"></i>
        </a>
    </div>
<?php endif; ?>

    </main>

    <!-- Enhanced View Modal -->
    <div id="viewModal" class="fixed inset-0 hidden items-center justify-center z-50 modal-overlay">
        <div class="modal-content glass-card rounded-2xl w-full max-w-2xl mx-4 max-h-[90vh] overflow-y-auto">
            <div class="gradient-bg text-white rounded-t-2xl p-6">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold"><?= t('modal_profile') ?></h2>
                    <button onclick="closeViewModal()" class="text-white/80 hover:text-white text-2xl">&times;</button>
                </div>
            </div>
            <div id="modalContent" class="p-6"></div>
            <div class="p-6 pt-0 flex justify-end space-x-3">
                <button onclick="closeViewModal()" 
                        class="px-5 py-2 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <?= t('modal_close') ?>
                </button>
                <a id="fullViewBtn" 
                   class="btn-primary text-white px-5 py-2 rounded-xl font-medium flex items-center">
                    <i class="fas fa-external-link-alt mr-2"></i> <?= t('modal_full_profile') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div id="deleteConfirmModal" class="fixed inset-0 hidden items-center justify-center z-50 modal-overlay">
        <div class="modal-content glass-card rounded-2xl w-full max-w-md mx-4 p-6">
            <div class="text-center mb-6">
                <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 dark:bg-red-900/30 mb-4">
                    <i class="fas fa-exclamation-triangle text-red-600 dark:text-red-400 text-2xl"></i>
                </div>
                <h3 class="text-xl font-bold mb-2"><?= t('modal_delete_teacher') ?></h3>
                <p class="text-gray-600 dark:text-gray-300" id="deleteMessage"></p>
            </div>
            <div class="flex justify-center space-x-4">
                <button onclick="closeModal('deleteConfirmModal')" 
                        class="px-5 py-2 border border-gray-300 dark:border-gray-600 rounded-xl hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                    <?= t('modal_cancel') ?>
                </button>
                
                <a id="confirmDeleteButton" 
                   class="bg-red-600 hover:bg-red-700 text-white px-5 py-2 rounded-xl font-medium">
                    <?= t('modal_confirm_delete') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="mt-12 pt-8 border-t border-gray-200 dark:border-gray-700">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <p class="text-gray-600 dark:text-gray-400"><?= t('footer_copyright') ?> <?= date('Y') ?> School Management System</p>
                    <p class="text-gray-500 dark:text-gray-500 text-sm"><?= t('footer_version') ?></p>
                </div>
                <div class="text-gray-500 dark:text-gray-400 text-sm">
                    <?= count($teachers) ?> <?= t('footer_stats') ?> • <?= $total_subjects ?>
                </div>
            </div>
        </div>
    </footer>

    <script>
        function openViewModal(data) {
            document.getElementById('viewModal').classList.remove('hidden');
            const initials = data.first_name.charAt(0) + (data.last_name ? data.last_name.charAt(0) : '');
            
            let statusBadge;
            if(data.status === 'active') {
                statusBadge = '<span class="status-badge status-active"><i class="fas fa-check-circle mr-1"></i> <?= t('status_active') ?></span>';
            } else if(data.status === 'inactive') {
                statusBadge = '<span class="status-badge status-inactive"><i class="fas fa-pause-circle mr-1"></i> <?= t('status_inactive') ?></span>';
            } else {
                statusBadge = '<span class="status-badge status-suspended"><i class="fas fa-times-circle mr-1"></i> <?= t('status_suspended') ?></span>';
            }
            
            document.getElementById('modalContent').innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div class="md:col-span-1">
                        <div class="teacher-avatar w-24 h-24 mx-auto mb-4 text-2xl">${initials}</div>
                        <div class="text-center">
                            <h3 class="text-xl font-bold">${data.first_name} ${data.last_name}</h3>
                            <p class="text-gray-600 dark:text-gray-400">${data.employee_code}</p>
                            <div class="mt-2">${statusBadge}</div>
                        </div>
                    </div>
                    
                   <div class="md:col-span-2">
    <div class="grid grid-cols-2 gap-4">

        <!-- Personal Info -->
        <div class="bg-gray-50 dark:bg-black p-4 rounded-xl text-gray-900 dark:text-white">
            <h4 class="font-semibold mb-2">
                <i class="fas fa-id-card mr-2 text-blue-500"></i><?= t('modal_personal_info') ?>
            </h4>
            <p class="text-sm"><b><?= t('label_gender') ?>:</b> ${data.gender || 'N/A'}</p>
            <p class="text-sm"><b><?= t('label_qualification') ?>:</b> ${data.qualification || 'N/A'}</p>
            <p class="text-sm"><b><?= t('label_hire_date') ?>:</b> ${data.hire_date || 'N/A'}</p>
        </div>

        <!-- Contact -->
        <div class="bg-gray-50 dark:bg-black p-4 rounded-xl text-gray-900 dark:text-white">
            <h4 class="font-semibold mb-2">
                <i class="fas fa-phone mr-2 text-green-500"></i><?= t('modal_contact') ?>
            </h4>
            <p class="text-sm"><b><?= t('label_phone') ?>:</b> ${data.phone || 'N/A'}</p>
            <p class="text-sm"><b><?= t('label_email') ?>:</b> ${data.email || 'N/A'}</p>
            <p class="text-sm"><b><?= t('label_address') ?>:</b> ${data.address || 'N/A'}</p>
        </div>

        <!-- Workload -->
        <div class="bg-gray-50 dark:bg-black p-4 rounded-xl text-gray-900 dark:text-white">
            <h4 class="font-semibold mb-2">
                <i class="fas fa-chart-bar mr-2 text-purple-500"></i><?= t('modal_workload') ?>
            </h4>
            <p class="text-sm"><b><?= t('table_subjects') ?>:</b> ${data.total_subjects}</p>
            <p class="text-sm"><b><?= t('table_classes') ?>:</b> ${data.total_classes}</p>
            <p class="text-sm"><b><?= t('label_joined') ?>:</b> ${data.created_at}</p>
        </div>

        <!-- Additional Info -->
        <div class="bg-gray-50 dark:bg-black p-4 rounded-xl text-gray-900 dark:text-white">
            <h4 class="font-semibold mb-2">
                <i class="fas fa-info-circle mr-2 text-amber-500"></i><?= t('modal_additional_info') ?>
            </h4>
            <p class="text-sm"><b><?= t('label_emergency_contact') ?>:</b> ${data.emergency_contact || 'N/A'}</p>
            <p class="text-sm"><b><?= t('label_blood_group') ?>:</b> ${data.blood_group || 'N/A'}</p>
        </div>

    </div>
</div>

                </div>
            `;
            
            document.getElementById('fullViewBtn').href = "teacher_view.php?id=" + data.id;
        }
        
        function closeViewModal() {
            document.getElementById('viewModal').classList.add('hidden');
        }
        
        function confirmDelete(teacherId, teacherName) {
            document.getElementById('deleteMessage').textContent = 
                `<?= t('modal_delete_message') ?> "${teacherName}"? This action cannot be undone and will remove all associated data.`;
            
            document.getElementById('confirmDeleteButton').href = "teacher_delete.php?id=" + teacherId;
            openModal('deleteConfirmModal');
        }
        
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
        
        function printPage() {
            window.print();
        }
        
        function exportData() {
            alert('Export feature would be implemented here');
        }
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#teachersTable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "<?= t('search_placeholder') ?>",
                    lengthMenu: "<?= $currentLangId == 2 ? 'عرض _MENU_ سجلات' : 'Show _MENU_ entries' ?>",
                    info: "<?= $currentLangId == 2 ? 'عرض _START_ إلى _END_ من _TOTAL_ معلمين' : 'Showing _START_ to _END_ of _TOTAL_ teachers' ?>",
                    paginate: {
                        first: '<i class="fas fa-angle-double-left"></i>',
                        last: '<i class="fas fa-angle-double-right"></i>',
                        next: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'left' : 'right' ?>"></i>',
                        previous: '<i class="fas fa-angle-<?= $currentLangId == 2 ? 'right' : 'left' ?>"></i>'
                    }
                },
                columnDefs: [
                    { orderable: false, targets: -1 } // Disable sorting on Actions
                ],
                order: [[0, 'asc']]
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Add animation to table rows
            const rows = document.querySelectorAll('.teacher-row');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.add('animate-in');
            });
            
            // Close modals when clicking outside
            document.addEventListener('click', function(event) {
                if (event.target.classList.contains('modal-overlay')) {
                    event.target.classList.add('hidden');
                    event.target.classList.remove('flex');
                    document.body.style.overflow = 'auto';
                }
            });
        });
    </script>

 <style>
        :root {
            --primary: #4f46e5;
            --primary-dark: #4338ca;
            --secondary: #06b6d4;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark: #1e293b;
            --light: #f8fafc;
            --card-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.01);
            --hover-shadow: 0 20px 40px -10px rgba(79, 70, 229, 0.15);
        }

        body.light {
            background: linear-gradient(135deg, #f5f7ff 0%, #f0f4ff 100%);
            color: #1e293b;
        }

        body:not(.light) {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #f1f5f9;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        body:not(.light) .glass-card {
            background: rgba(30, 41, 59, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
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
            box-shadow: var(--hover-shadow);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background: linear-gradient(135deg, #10b981 0%, #0ca678 100%);
            color: white;
        }

        .status-inactive {
            background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
            color: white;
        }

        .status-suspended {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
            color: white;
        }

        .teacher-avatar {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 1.25rem;
        }

        .subject-chip {
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 500;
            background: linear-gradient(135deg, #e0e7ff 0%, #dbeafe 100%);
            color: var(--primary-dark);
            margin: 0.125rem;
        }

        body:not(.light) .subject-chip {
            background: linear-gradient(135deg, #3730a3 0%, #312e81 100%);
            color: #e0e7ff;
        }

        .hover-card {
            transition: all 0.3s ease;
        }

        .hover-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--hover-shadow);
        }

        .tab-active {
            position: relative;
            color: var(--primary);
        }

        .tab-active::after {
            content: '';
            position: absolute;
            bottom: -2px;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 1px;
        }

        .floating-btn {
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

        .stat-card {
            position: relative;
            overflow: hidden;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            pointer-events: none;
        }

        body:not(.light) .stat-card::before {
            background: radial-gradient(circle, rgba(255,255,255,0.05) 0%, transparent 70%);
        }

        @media (max-width: 768px) {
            .floating-btn {
                bottom: 1rem;
                right: 1rem;
            }
            
            .mobile-full-width {
                width: 100%;
            }
        }
    </style>
</body>
</html>
