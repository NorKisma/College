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
        'page_title' => 'Students Management | Academic Portal',
        'header_title' => 'Students Management',
        'header_subtitle' => 'Manage student records, classes, and Parent information',
        'btn_add_student' => 'Add New Student',
        
        // Statistics Cards
        'stat_total_students' => 'Total Students',
        'stat_male_students' => 'Male Students',
        'stat_female_students' => 'Female Students',
        'stat_active_classes' => 'Active Classes',
        'stat_current_year' => 'Current academic year',
        'stat_percentage_total' => '% of total',
        'stat_updated_now' => 'Updated just now',
        
        // Search and Filters
        'search_placeholder' => 'Search students, guardians, or phone numbers...',
        'btn_search' => 'Search',
        'filter_all_classes' => 'All Classes',
        'filter_all_genders' => 'All Genders',
        'filter_all_sections' => 'All Sections',
        'filter_male' => 'Male',
        'filter_female' => 'Female',
        'btn_apply_filters' => 'Apply Filters',
        'btn_clear_filters' => 'Clear',
        
        // Table
        'table_title' => 'Student Records',
        'table_showing' => 'Showing',
        'table_students' => 'students',
        'table_column_id' => '#',
        'table_column_student_info' => 'Student Info',
        'table_column_gender' => 'Gender',
        'table_column_dob' => 'Date of Birth',
        'table_column_parent' => 'Parent',
        'table_column_contact' => 'Contact',
        'table_column_class' => 'Class',
        'table_column_actions' => 'Actions',
        'label_admitted' => 'Admitted:',
        'label_years_old' => 'years old',
        'label_not_assigned' => 'Not Assigned',
        'label_not_paid' => 'Not paid this month',
        
        // Table Actions
        'btn_edit' => 'Edit',
        'btn_details' => 'Details',
        'btn_delete' => 'Delete',
        
        // Empty State
        'empty_title' => 'No students found',
        'empty_message' => 'No students match your current search or filters. Try adjusting your search or add a new student.',
        'btn_add_first_student' => 'Add Your First Student',
        
        // Pagination
        'pagination_previous' => 'Previous',
        'pagination_next' => 'Next',
        'pagination_page' => 'Page',
        'pagination_of' => 'of',
        
        // Modals - Add Student
        'modal_add_title' => 'Add New Student',
        'modal_add_subtitle' => 'Fill in the student\'s details below',
        'label_student_name' => 'Student Name *',
        'label_admission_date' => 'Admission Date',
        'label_dob' => 'Date of Birth',
        'label_gender' => 'Gender',
        'label_class' => 'Class *',
        'label_parent_name' => 'Parent Name',
        'label_address' => 'Address',
        'label_parent_phone' => 'Parent Phone',
        'label_parent_email' => 'Parent Email',
        'placeholder_student_name' => 'Enter full name',
        'placeholder_parent_name' => 'Guardian\'s full name',
        'placeholder_address' => 'Student\'s address',
        'placeholder_phone' => 'Phone number',
        'placeholder_email' => 'Email address',
        'option_select_class' => 'Select Class',
        'option_select_gender' => 'Select Gender',
        'btn_cancel' => 'Cancel',
        'btn_save_student' => 'Save Student',
        
        // Modals - Edit Student
        'modal_edit_title' => 'Edit Student',
        'modal_edit_subtitle' => 'Update student information',
        'btn_update_student' => 'Update Student',
        
        // Confirmation
        'confirm_delete' => 'Are you sure you want to delete this student? This action cannot be undone.',
        
        // Database Messages
        'db_error' => 'SQL ERROR: ',
        
        // Age Calculation
        'years_old' => 'years old',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة الطلاب | البوابة الأكاديمية',
        'header_title' => 'إدارة الطلاب',
        'header_subtitle' => 'إدارة سجلات الطلاب، الصفوف، ومعلومات أولياء الأمور',
        'btn_add_student' => 'إضافة طالب جديد',
        
        // Statistics Cards
        'stat_total_students' => 'إجمالي الطلاب',
        'stat_male_students' => 'الطلاب الذكور',
        'stat_female_students' => 'الطالبات الإناث',
        'stat_active_classes' => 'الصفوف النشطة',
        'stat_current_year' => 'السنة الدراسية الحالية',
        'stat_percentage_total' => '% من الإجمالي',
        'stat_updated_now' => 'تم التحديث الآن',
        
        // Search and Filters
        'search_placeholder' => 'البحث عن طلاب، أولياء أمور، أو أرقام الهواتف...',
        'btn_search' => 'بحث',
        'filter_all_classes' => 'جميع الصفوف',
        'filter_all_genders' => 'جميع الجنسين',
        'filter_all_sections' => 'جميع الأقسام',
        'filter_male' => 'ذكر',
        'filter_female' => 'أنثى',
        'btn_apply_filters' => 'تطبيق الفلاتر',
        'btn_clear_filters' => 'مسح',
        
        // Table
        'table_title' => 'سجلات الطلاب',
        'table_showing' => 'عرض',
        'table_students' => 'طالب',
        'table_column_id' => '#',
        'table_column_student_info' => 'معلومات الطالب',
        'table_column_gender' => 'الجنس',
        'table_column_dob' => 'تاريخ الميلاد',
        'table_column_parent' => 'ولي الأمر',
        'table_column_contact' => 'الاتصال',
        'table_column_class' => 'الصف',
        'table_column_actions' => 'الإجراءات',
        'label_admitted' => 'مسجل في:',
        'label_years_old' => 'سنة',
        'label_not_assigned' => 'غير معين',
        'label_not_paid' => 'غير مدفوع هذا الشهر',
        
        // Table Actions
        'btn_edit' => 'تعديل',
        'btn_details' => 'تفاصيل',
        'btn_delete' => 'حذف',
        
        // Empty State
        'empty_title' => 'لم يتم العثور على طلاب',
        'empty_message' => 'لا توجد طلاب تطابق بحثك أو الفلاتر الحالية. حاول تعديل بحثك أو إضافة طالب جديد.',
        'btn_add_first_student' => 'أضف طالبك الأول',
        
        // Pagination
        'pagination_previous' => 'السابق',
        'pagination_next' => 'التالي',
        'pagination_page' => 'صفحة',
        'pagination_of' => 'من',
        
        // Modals - Add Student
        'modal_add_title' => 'إضافة طالب جديد',
        'modal_add_subtitle' => 'املأ تفاصيل الطالب أدناه',
        'label_student_name' => 'اسم الطالب *',
        'label_admission_date' => 'تاريخ التسجيل',
        'label_dob' => 'تاريخ الميلاد',
        'label_gender' => 'الجنس',
        'label_class' => 'الصف *',
        'label_parent_name' => 'اسم ولي الأمر',
        'label_address' => 'العنوان',
        'label_parent_phone' => 'هاتف ولي الأمر',
        'label_parent_email' => 'بريد ولي الأمر',
        'placeholder_student_name' => 'أدخل الاسم الكامل',
        'placeholder_parent_name' => 'الاسم الكامل لولي الأمر',
        'placeholder_address' => 'عنوان الطالب',
        'placeholder_phone' => 'رقم الهاتف',
        'placeholder_email' => 'البريد الإلكتروني',
        'option_select_class' => 'اختر الصف',
        'option_select_gender' => 'اختر الجنس',
        'btn_cancel' => 'إلغاء',
        'btn_save_student' => 'حفظ الطالب',
        
        // Modals - Edit Student
        'modal_edit_title' => 'تعديل الطالب',
        'modal_edit_subtitle' => 'تحديث معلومات الطالب',
        'btn_update_student' => 'تحديث الطالب',
        
        // Confirmation
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذا الطالب؟ لا يمكن التراجع عن هذا الإجراء.',
        
        // Database Messages
        'db_error' => 'خطأ في SQL: ',
        
        // Age Calculation
        'years_old' => 'سنة',
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

$logged_in_id = $_SESSION['user_id'] ?? null;

function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}
function is_member() {
    return ($_SESSION['role'] ?? '') === 'member';
}

/* =========================
   FETCH THEME FROM DATABASE
========================= */
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1");
$themeRow = $themeRes->fetch_assoc();
$theme = $themeRow['mode'] == 0 ? 'dark' : 'light';

/* =========================
   ADD STUDENT
========================= */
if(isset($_POST['add_student'])){
    // 1. Insert student
    $stmt = $conn->prepare(
        "INSERT INTO students 
        (name, admission_date, dob, gender, address, guardian_name, guardian_phone, guardian_email)
        VALUES (?,?,?,?,?,?,?,?)"
    );

    $stmt->bind_param(
        "ssssssss",
        $_POST['name'],
        $_POST['admission_date'],
        $_POST['dob'],
        $_POST['gender'],
        $_POST['address'],
        $_POST['guardian_name'],
        $_POST['guardian_phone'],
        $_POST['guardian_email']
    );

    $stmt->execute();
    $student_id = $stmt->insert_id;

    // 2. Insert class assignment
    $class_id = (int)$_POST['class_id'];
    $year = date("Y") . "/" . (date("Y") + 1);

    $stmt2 = $conn->prepare(
        "INSERT INTO class_students (class_id, student_id, academic_year)
         VALUES (?, ?, ?)"
    );
    $stmt2->bind_param("iis", $class_id, $student_id, $year);
    $stmt2->execute();

    header("Location: students.php");
    exit;
}

/* =========================
   UPDATE STUDENT
========================= */
if(isset($_POST['update_student'])){
    // 1. Update main student table
    $stmt = $conn->prepare(
        "UPDATE students SET
            name=?,
            admission_date=?,
            dob=?,
            gender=?,
            address=?,
            guardian_name=?,
            guardian_phone=?,
            guardian_email=?
        WHERE id=?"
    );

    $stmt->bind_param(
        "ssssssssi",
        $_POST['name'],
        $_POST['admission_date'],
        $_POST['dob'],
        $_POST['gender'],
        $_POST['address'],
        $_POST['guardian_name'],
        $_POST['guardian_phone'],
        $_POST['guardian_email'],
        $_POST['id']
    );

    $stmt->execute();

    // 2. Update class assignment
    $student_id = (int)$_POST['id'];
    $class_id = (int)$_POST['class_id'];
    $year = date("Y") . "/" . (date("Y") + 1);
    $year = date("Y") . "/" . (date("Y") + 1);

    // Check if student already assigned for this year
    $check = $conn->prepare(
        "SELECT id FROM class_students 
         WHERE student_id=? AND academic_year=?"
    );
    $check->bind_param("is", $student_id, $year);
    $check->execute();
    $result = $check->get_result();

    if ($result->num_rows > 0) {
        // Update class
        $stmt2 = $conn->prepare(
            "UPDATE class_students 
             SET class_id=? 
             WHERE student_id=? AND academic_year=?"
        );
        $stmt2->bind_param("iis", $class_id, $student_id, $year);
        $stmt2->execute();
    } else {
        // Insert new class assignment
        $stmt2 = $conn->prepare(
            "INSERT INTO class_students (class_id, student_id, academic_year)
            VALUES (?, ?, ?)"
        );
        $stmt2->bind_param("iis", $class_id, $student_id, $year);
        $stmt2->execute();
    }

    header("Location: students.php");
    exit;
}

/* =========================
   DELETE STUDENT
========================= */
if(isset($_GET['delete'])){
    $id = (int)$_GET['delete'];

    // delete from class_students also using prepared statements
    $stmt1 = $conn->prepare("DELETE FROM class_students WHERE student_id=?");
    $stmt1->bind_param("i", $id);
    $stmt1->execute();

    $stmt2 = $conn->prepare("DELETE FROM students WHERE id=?");
    $stmt2->bind_param("i", $id);
    $stmt2->execute();

    header("Location: students.php");
    exit;
}

/* =========================
   SEARCH + FETCH STUDENTS
 ========================= */
$search = $_GET['search'] ?? '';
$where = [];
$params = [];
$types = "";

if ($search) {
    $where[] = "(s.name LIKE ? 
                 OR s.guardian_name LIKE ? 
                 OR s.guardian_phone LIKE ?)";
    $sterm = "%$search%";
    $params[] = $sterm; 
    $params[] = $sterm; 
    $params[] = $sterm;
    $types .= "sss";
}

if (!empty($_GET['class_id'])) {
    $cid = (int)$_GET['class_id'];
    $where[] = "cs.class_id = ?";
    $params[] = $cid;
    $types .= "i";
}

if (!empty($_GET['gender'])) {
    $g = $_GET['gender'];
    $where[] = "s.gender = ?";
    $params[] = $g;
    $types .= "s";
}

if (!empty($_GET['section_id'])) {
    $sid = (int)$_GET['section_id'];
    $where[] = "c.section_id = ?";
    $params[] = $sid;
    $types .= "i";
}

// SQL with alumni filter
$sql = "
    SELECT DISTINCT
        s.*, 
        cs.class_id,
        cs.academic_year,
        c.name AS class_name,
        sec.name AS section_name
    FROM students s
    LEFT JOIN class_students cs 
        ON cs.student_id = s.id
       AND cs.is_active = 1
    LEFT JOIN classes c 
        ON c.id = cs.class_id
    LEFT JOIN sections sec
        ON sec.id = c.section_id
    LEFT JOIN alumni a
        ON a.student_id = s.id
       AND a.class_id = cs.class_id
";

// add conditions
if (!empty($where)) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

// exclude alumni in the same class
$sql .= (!empty($where) ? " AND " : " WHERE ") . "(a.student_id IS NULL)";

$sql .= " ORDER BY s.id DESC";

$stmt = $conn->prepare($sql);
if ($types) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$students = $stmt->get_result();

if (!$students) {
    die(t('db_error') . $conn->error);
}

function calculateAge($dob) {
    if (!$dob || $dob === '0000-00-00') return null;

    try {
        $birthDate = new DateTime($dob);
        $today = new DateTime();
        return $today->diff($birthDate)->y;
    } catch (Exception $e) {
        return null;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>" 
      class="<?= $theme == 'dark' ? 'dark' : '' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    
    <!-- Fonts & Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    
    <!-- JQuery & DataTables -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Tailwind Tweaks */
        .ts-control {
            border-radius: 0.75rem !important;
            padding: 0.75rem 1rem !important;
            border-color: #d1d5db !important;
            background-color: #ffffff;
            color: #111827;
            font-size: 1rem;
            line-height: 1.5;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }
        .dark .ts-control {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #fff !important;
        }
        .ts-control > input {
            color: inherit !important;
            display: inline-block !important; /* Ensure input is visible */
            min-width: 4rem;
        }
        .dark .ts-dropdown {
            background-color: #1f2937 !important;
            border-color: #374151 !important;
            color: #fff !important;
        }
        .dark .ts-dropdown .option {
            color: #fff !important;
        }
        /* Restore standard caret styling */
        .ts-control::after {
            border-color: #6b7280 transparent transparent transparent !important;
            border-width: 5px 5px 0 5px !important;
        }
        .dark .ts-control::after {
            border-color: #9ca3af transparent transparent transparent !important;
        }
        .dark .ts-dropdown .active {
            background-color: #374151 !important;
            color: #fff !important;
        }
        /* Ensure dropdown sits above modals */
        .ts-dropdown {
            z-index: 9999 !important;
        }
    </style>
    
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#4f46e5',
                            600: '#4338ca',
                            700: '#3730a3',
                        }
                    },
                    animation: {
                        'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                        'bounce-slow': 'bounce 2s infinite',
                    }
                }
            }
        }
    </script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 text-gray-800 dark:text-gray-200 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 py-6 sm:px-6 lg:px-8">
        
        <!-- Header Section -->
        <header class="mb-8 animate-fade-in">
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-primary-500 to-purple-600 shadow-lg">
                            <i class="fas fa-user-graduate text-white text-xl"></i>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900 dark:text-white"><?= t('header_title') ?></h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1"><?= t('header_subtitle') ?></p>
                        </div>
                    </div>
                </div>
                
                <button onclick="openAdd()" class="group flex items-center gap-2 bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 text-white font-medium px-5 py-3 rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-0.5">
                    <i class="fas fa-plus-circle"></i>
                    <span><?= t('btn_add_student') ?></span>
                </button>
            </div>
        </header>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-5 mb-8 animate-fade-in" style="animation-delay: 0.1s">
            <?php
            $totalStudents = $conn->query("SELECT COUNT(*) as total FROM students")->fetch_assoc()['total'];
            $maleStudents = $conn->query("SELECT COUNT(*) as male FROM students WHERE gender='male'")->fetch_assoc()['male'];
            $femaleStudents = $conn->query("SELECT COUNT(*) as female FROM students WHERE gender='female'")->fetch_assoc()['female'];
            $activeClasses = $conn->query("SELECT COUNT(DISTINCT class_id) as classes FROM class_students WHERE academic_year = CONCAT(YEAR(CURDATE()), '/', YEAR(CURDATE())+1)")->fetch_assoc()['classes'];
            ?>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_total_students') ?></p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $totalStudents ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-calendar-alt mr-1"></i> <?= t('stat_current_year') ?></p>
                </div>
            </div>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_male_students') ?></p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $maleStudents ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                        <i class="fas fa-male text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= $totalStudents > 0 ? round(($maleStudents/$totalStudents)*100, 1) : 0 ?><?= t('stat_percentage_total') ?></p>
                </div>
            </div>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_female_students') ?></p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $femaleStudents ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-600 dark:text-pink-400">
                        <i class="fas fa-female text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><?= $totalStudents > 0 ? round(($femaleStudents/$totalStudents)*100, 1) : 0 ?><?= t('stat_percentage_total') ?></p>
                </div>
            </div>
            
            <div class="glass dark:glass-dark p-5 rounded-2xl shadow-sm hover-card border border-gray-200 dark:border-gray-800">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-gray-500 dark:text-gray-400 text-sm font-medium"><?= t('stat_active_classes') ?></p>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white mt-1"><?= $activeClasses ?></h3>
                    </div>
                    <div class="p-3 rounded-full bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400">
                        <i class="fas fa-chalkboard-teacher text-xl"></i>
                    </div>
                </div>
                <div class="mt-4 pt-4 border-t border-gray-100 dark:border-gray-800">
                    <p class="text-xs text-gray-500 dark:text-gray-400"><i class="fas fa-sync-alt mr-1"></i> <?= t('stat_updated_now') ?></p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="glass dark:glass-dark rounded-2xl p-5 mb-8 shadow-sm border border-gray-200 dark:border-gray-800 animate-fade-in" style="animation-delay: 0.2s">
            <div class="flex flex-col lg:flex-row gap-5">
                <!-- Filters -->
                <div class="flex flex-wrap gap-3 w-full">
                    <form method="GET" class="flex flex-wrap gap-3">
                        <div class="relative w-full md:w-64">
                            <select name="class_id" class="tom-select-class">
                                <option value=""><?= t('filter_all_classes') ?></option>
                                <?php
                                $cls = $conn->query("SELECT * FROM classes ORDER BY name");
                                while($c = $cls->fetch_assoc()):
                                ?>
                                    <option value="<?= $c['id'] ?>" 
                                        <?= ($_GET['class_id'] ?? '') == $c['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="relative w-full md:w-64">
                            <select name="gender" class="tom-select-class">
                                <option value=""><?= t('filter_all_genders') ?></option>
                                <option value="male" <?= (($_GET['gender'] ?? '') == "male") ? 'selected' : '' ?>><?= t('filter_male') ?></option>
                                <option value="female" <?= (($_GET['gender'] ?? '') == "female") ? 'selected' : '' ?>><?= t('filter_female') ?></option>
                            </select>
                        </div>
                        
                       <div class="relative w-full md:w-64">
                            <select name="section_id" class="tom-select-class">
                                <option value=""><?= t('filter_all_sections') ?></option>
                                <?php
                                $secs = $conn->query("SELECT id, name FROM sections ORDER BY name");
                                while($s = $secs->fetch_assoc()):
                                ?>
                                    <option value="<?= $s['id'] ?>"
                                        <?= (($_GET['section_id'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <button type="submit" class="flex items-center gap-2 bg-gradient-to-r from-gray-700 to-gray-800 dark:from-gray-800 dark:to-gray-900 hover:from-gray-800 hover:to-gray-900 dark:hover:from-gray-700 dark:hover:to-gray-800 text-white px-5 py-3 rounded-xl transition-colors">
                            <i class="fas fa-filter"></i>
                            <span><?= t('btn_apply_filters') ?></span>
                        </button>
                        
                        <?php if(isset($_GET['class_id']) || isset($_GET['gender']) || isset($_GET['section_id'])): ?>
                            <a href="students.php" class="flex items-center gap-2 bg-gradient-to-r from-gray-300 to-gray-400 dark:from-gray-700 dark:to-gray-800 hover:from-gray-400 hover:to-gray-500 dark:hover:from-gray-600 dark:hover:to-gray-700 text-gray-800 dark:text-white px-5 py-3 rounded-xl transition-colors">
                                <i class="fas fa-times"></i>
                                <span><?= t('btn_clear_filters') ?></span>
                            </a>
                        <?php endif; ?>
                    </form>
                </div>
            </div>
        </div>

        <!-- Students Table -->
        <div class="glass dark:glass-dark rounded-2xl overflow-hidden shadow-sm border border-gray-200 dark:border-gray-800 animate-fade-in" style="animation-delay: 0.3s"> 
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-800 flex justify-between items-center">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white"><?= t('table_title') ?></h2>
                <span class="text-sm text-gray-500 dark:text-gray-400">
                    <?= t('table_showing') ?> <span class="font-semibold"><?= $students->num_rows ?></span> <?= t('table_students') ?>
                </span>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full" id="studentsTable">
                    <thead class="bg-gray-50 dark:bg-gray-800/50">
                        <tr>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_id') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_student_info') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_gender') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_dob') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_parent') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_contact') ?></th>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_class') ?></th>
                            <?php if(!is_finance()): ?>
                            <th class="py-4 px-6 text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider"><?= t('table_column_actions') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 dark:divide-gray-800">
                        <?php 
                        $currentMonth = date('m');
                        $currentYear  = date('Y');

                        $paidStmt = $conn->prepare("
                            SELECT 1
                            FROM student_fees
                            WHERE student_id = ?
                            AND payment_date IS NOT NULL
                            AND MONTH(payment_date) = ?
                            AND YEAR(payment_date) = ?
                            LIMIT 1
                        ");
                         $i=1; while($row=$students->fetch_assoc()): 
                           $paidStmt->bind_param("iii", $row['id'], $currentMonth, $currentYear);
                                $paidStmt->execute();
                                $paidStmt->store_result();

                                $paidThisMonth = $paidStmt->num_rows > 0;
                        ?>
                        <tr class="table-row-hover bg-white dark:bg-gray-900/50 hover:bg-gray-50 dark:hover:bg-gray-800/70 animate-slide-in" style="animation-delay: <?= $i*0.05 ?>s">
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= $i++ ?></div>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10 rounded-full bg-gradient-to-r from-primary-500 to-purple-600 flex items-center justify-center text-white font-bold">
                                        <?= substr($row['name'], 0, 1) ?>
                                    </div>
                                    <div class="ml-4">
                                        <div style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                            <?= htmlspecialchars($row['name']) ?>

                                            <?php if(!$paidThisMonth): ?>
                                                <span class="relative group ml-1">
                                                    <i class="fas fa-exclamation-triangle text-yellow-500 text-xs"></i>

                                                    <!-- Hover message -->
                                                    <span class="absolute hidden group-hover:block bg-gray-900 text-white text-xs rounded px-2 py-1
                                                                -top-7 left-1/2 -translate-x-1/2 whitespace-nowrap z-50">
                                                        <?= t('label_not_paid') ?>
                                                    </span>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400"><?= t('label_admitted') ?> <?= $row['admission_date'] ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium 
                                    <?= $row['gender'] == 'male' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-pink-100 text-pink-800 dark:bg-pink-900/30 dark:text-pink-300' ?>">
                                    <i class="fas fa-<?= $row['gender'] == 'male' ? 'male' : 'female' ?> mr-1"></i>
                                    <?= $row['gender'] == 'male' ? t('filter_male') : t('filter_female') ?>
                                </span>
                            </td>
                           <td class="py-4 px-6 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <!-- Date of Birth -->
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">
                                        <?= htmlspecialchars($row['dob']) ?>
                                    </span>

                                    <!-- Calculated Age -->
                                    <?php $age = calculateAge($row['dob']); ?>
                                    <?php if($age !== null): ?>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                                            <i class="fas fa-birthday-cake mr-1"></i>
                                            <?= $age ?> <?= t('years_old') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </td>

                            <td class="py-4 px-6">
                                <div class="text-sm font-medium text-gray-900 dark:text-white"><?= htmlspecialchars($row['guardian_name']) ?></div>
                                <div class="text-xs text-gray-500 dark:text-gray-400"><?= $row['guardian_email'] ?></div>
                            </td>
                            <td class="py-4 px-6 whitespace-nowrap">
                                <div class="text-sm text-gray-900 dark:text-white">
                                    <i class="fas fa-phone-alt mr-2 text-gray-400"></i>
                                    <?= $row['guardian_phone'] ?>
                                </div>
                            </td>
                           <td class="py-4 px-6 whitespace-nowrap">
                                <?php if($row['class_name']): ?>
                                <div class="inline-flex flex-col gap-1 px-3 py-2 rounded-lg bg-indigo-100 text-indigo-800 
                                            dark:bg-indigo-900/30 dark:text-indigo-300">
                                    
                                    <!-- Class Name -->
                                    <div class="flex items-center text-sm font-medium">
                                        <i class="fas fa-chalkboard-teacher mr-2"></i>
                                        <?= htmlspecialchars($row['class_name']) ?>
                                    </div>

                                    <!-- Section Name -->
                                    <?php if(!empty($row['section_name'])): ?>
                                        <div class="text-xs text-indigo-600 dark:text-indigo-400 pl-6">
                                            <i class="fas fa-layer-group mr-1"></i><?= htmlspecialchars($row['section_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-sm font-medium 
                                                bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-300">
                                        <i class="fas fa-times mr-2"></i>
                                        <?= t('label_not_assigned') ?>
                                    </span>
                                <?php endif; ?>
                             </td>

                            <td class="py-4 px-6 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center gap-2">
                                    <?php if(!is_finance()): ?>
                                    <button onclick='openEdit(<?= json_encode($row) ?>)' 
                                            class="inline-flex items-center gap-1 bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-600 hover:to-amber-700 text-white px-4 py-2 rounded-lg transition-colors group">
                                        <i class="fas fa-edit group-hover:rotate-12 transition-transform"></i>
                                        <span><?= t('btn_edit') ?></span>
                                    </button>
                                    <a href="student_details.php?id=<?= $row['id'] ?>"
                                       class="inline-flex items-center gap-1 bg-gradient-to-r from-primary-500 to-primary-600 hover:from-primary-600 hover:to-primary-700 text-white px-4 py-2 rounded-lg transition-colors group">
                                        <i class="fas fa-eye group-hover:scale-110 transition-transform"></i>
                                        <span><?= t('btn_details') ?></span>
                                    </a>
                                     <?php if(!is_finance() && !is_member()): ?>
                                    <a onclick="return confirmDelete()"
                                       href="?delete=<?= $row['id'] ?>"
                                       class="inline-flex items-center gap-1 bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-4 py-2 rounded-lg transition-colors group">
                                        <i class="fas fa-trash-alt group-hover:scale-110 transition-transform"></i>
                                        <span><?= t('btn_delete') ?></span>
                                    </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        
                        <?php if($students->num_rows == 0): ?>
                        <tr>
                            <td colspan="8" class="py-12 px-6 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="rounded-full bg-gray-100 dark:bg-gray-800 p-6 mb-4">
                                        <i class="fas fa-user-slash text-3xl text-gray-400"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2"><?= t('empty_title') ?></h3>
                                    <p class="text-gray-500 dark:text-gray-400 max-w-md mx-auto">
                                        <?= t('empty_message') ?>
                                    </p>
                                    <button onclick="openAdd()" class="mt-6 bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 text-white font-medium px-5 py-3 rounded-xl shadow-lg transition-colors">
                                        <i class="fas fa-plus-circle mr-2"></i>
                                        <?= t('btn_add_first_student') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Footer -->
            <?php if($students->num_rows > 0): ?>
            <div class="px-6 py-4 border-t border-gray-200 dark:border-gray-800 flex flex-col sm:flex-row justify-between items-center">
                <div class="text-sm text-gray-500 dark:text-gray-400 mb-4 sm:mb-0">
                    <?= t('table_showing') ?> <span class="font-semibold"><?= $students->num_rows ?></span> <?= t('table_of') ?> <span class="font-semibold"><?= $totalStudents ?></span> <?= t('table_students') ?>
                </div>
                <div class="flex items-center space-x-2">
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <i class="fas fa-chevron-left mr-2"></i>
                        <?= t('pagination_previous') ?>
                    </button>
                    <span class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300"><?= t('pagination_page') ?> 1 <?= t('pagination_of') ?> 1</span>
                    <button class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                        <?= t('pagination_next') ?>
                        <i class="fas fa-chevron-right ml-2"></i>
                    </button>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- =========================
         ADD MODAL (Enhanced)
    ========================= -->
    <div id="addModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/70 dark:bg-gray-900/90 backdrop-blur-sm transition-opacity" onclick="closeAdd()"></div>
            
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all modal-enter w-full max-w-2xl">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-primary-500 to-purple-600"></div>
                
                <div class="px-6 pt-6 pb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-gradient-to-br from-primary-500 to-primary-700">
                                <i class="fas fa-user-plus text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?= t('modal_add_title') ?></h3>
                        </div>
                        <button onclick="closeAdd()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 ml-11"><?= t('modal_add_subtitle') ?></p>
                </div>
                
                <form method="POST" class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_student_name') ?></label>
                            <input name="name" required 
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                                   placeholder="<?= t('placeholder_student_name') ?>">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_admission_date') ?></label>
                            <input name="admission_date" type="date"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_dob') ?></label>
                            <input name="dob" type="date" required
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_gender') ?></label>
                            <select name="gender" required class="tom-select-class">
                                <option value=""><?= t('option_select_gender') ?></option>
                                <option value="male"><?= t('filter_male') ?></option>
                                <option value="female"><?= t('filter_female') ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_class') ?></label>
                            <select name="class_id" required class="tom-select-class">
                                <option value=""><?= t('option_select_class') ?></option>
                                <?php
                                $classes = $conn->query("
                                    SELECT c.*
                                    FROM classes c
                                    ORDER BY c.name
                                ");
                                while ($c = $classes->fetch_assoc()):
                                ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_name') ?></label>
                            <input name="guardian_name"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                                   placeholder="<?= t('placeholder_parent_name') ?>">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_address') ?></label>
                            <textarea name="address" rows="2"
                                      class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                                      placeholder="<?= t('placeholder_address') ?>"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_phone') ?></label>
                            <input name="guardian_phone"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                                   placeholder="<?= t('placeholder_phone') ?>" required>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_email') ?></label>
                            <input name="guardian_email" type="email"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"
                                   placeholder="<?= t('placeholder_email') ?>">
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                        <button type="button" onclick="closeAdd()" 
                                class="px-6 py-3 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <?= t('btn_cancel') ?>
                        </button>
                        <button name="add_student" type="submit"
                                class="px-6 py-3 rounded-xl font-medium text-white bg-gradient-to-r from-primary-500 to-primary-700 hover:from-primary-600 hover:to-primary-800 transition-colors shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            <?= t('btn_save_student') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =========================
         EDIT MODAL (Enhanced)
    ========================= -->
    <div id="editModal" class="hidden fixed inset-0 z-50 overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:p-0">
            <!-- Background overlay -->
            <div class="fixed inset-0 bg-gray-900/70 dark:bg-gray-900/90 backdrop-blur-sm transition-opacity" onclick="closeEdit()"></div>
            
            <!-- Modal panel -->
            <div class="relative bg-white dark:bg-gray-900 rounded-2xl shadow-2xl overflow-hidden transform transition-all modal-enter w-full max-w-2xl">
                <div class="absolute top-0 left-0 right-0 h-1 bg-gradient-to-r from-amber-500 to-orange-600"></div>
                
                <div class="px-6 pt-6 pb-2">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="p-2 rounded-lg bg-gradient-to-br from-amber-500 to-amber-700">
                                <i class="fas fa-user-edit text-white"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white"><?= t('modal_edit_title') ?></h3>
                        </div>
                        <button onclick="closeEdit()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-800 transition-colors">
                            <i class="fas fa-times text-xl"></i>
                        </button>
                    </div>
                    <p class="text-gray-600 dark:text-gray-400 mt-2 ml-11"><?= t('modal_edit_subtitle') ?></p>
                </div>
                
                <form method="POST" class="px-6 py-4">
                    <input type="hidden" id="edit_id" name="id">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_student_name') ?></label>
                            <input id="edit_name" name="name" required 
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_admission_date') ?></label>
                            <input id="edit_admission" name="admission_date" type="date"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_dob') ?></label>
                            <input id="edit_dob" name="dob" type="date" required
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_gender') ?></label>
                            <select id="edit_gender" name="gender" required class="tom-select-class">
                                <option value=""><?= t('option_select_gender') ?></option>
                                <option value="male"><?= t('filter_male') ?></option>
                                <option value="female"><?= t('filter_female') ?></option>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_class') ?></label>
                            <select id="edit_class" name="class_id" required class="tom-select-class">
                                <option value=""><?= t('option_select_class') ?></option>
                                <?php
                                $classes->data_seek(0); 
                                while ($c = $classes->fetch_assoc()):
                                ?>
                                    <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_name') ?></label>
                            <input id="edit_gname" name="guardian_name"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_address') ?></label>
                            <textarea id="edit_address" name="address" rows="2"
                                      class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none"></textarea>
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_phone') ?></label>
                            <input id="edit_gphone" name="guardian_phone"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                        
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2"><?= t('label_parent_email') ?></label>
                            <input id="edit_gemail" name="guardian_email" type="email"
                                   class="input-focus w-full px-4 py-3 rounded-xl border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none">
                        </div>
                    </div>
                    
                    <div class="mt-8 pt-6 border-t border-gray-200 dark:border-gray-800 flex justify-end gap-3">
                        <button type="button" onclick="closeEdit()" 
                                class="px-6 py-3 rounded-xl font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-800 hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                            <?= t('btn_cancel') ?>
                        </button>
                        <button name="update_student" type="submit"
                                class="px-6 py-3 rounded-xl font-medium text-white bg-gradient-to-r from-amber-500 to-amber-700 hover:from-amber-600 hover:to-amber-800 transition-colors shadow-md">
                            <i class="fas fa-save mr-2"></i>
                            <?= t('btn_update_student') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- =========================
         JS
    ========================= -->
    <script>
        // JavaScript translations
        const jsTranslations = {
            confirm_delete: "<?= t('confirm_delete') ?>",
            label_not_paid: "<?= t('label_not_paid') ?>"
        };

        function openAdd() {
            document.getElementById("addModal").classList.remove("hidden");
            document.body.style.overflow = 'hidden';
        }

        function closeAdd() {
            document.getElementById("addModal").classList.add("hidden");
            document.body.style.overflow = 'auto';
        }

        function openEdit(data) {
            document.getElementById("editModal").classList.remove("hidden");
            document.body.style.overflow = 'hidden';
            
            document.getElementById("edit_id").value = data.id;
            document.getElementById("edit_name").value = data.name;
            document.getElementById("edit_admission").value = data.admission_date;
            document.getElementById("edit_dob").value = data.dob;
            // document.getElementById("edit_gender").value = data.gender; // Use Tom Select instead (handled below)
            document.getElementById("edit_address").value = data.address;
            document.getElementById("edit_gname").value = data.guardian_name;
            document.getElementById("edit_gphone").value = data.guardian_phone;
            document.getElementById("edit_gemail").value = data.guardian_email;
            
            // Set Tom Select value for Class
            if (window.tomSelectEditClass) {
                 window.tomSelectEditClass.setValue(data.class_id || '');
            } else {
                 document.getElementById("edit_class").value = data.class_id || '';
            }

            // Set Tom Select value for Gender
            if (window.tomSelectEditGender) {
                 window.tomSelectEditGender.setValue(data.gender || '');
            } else {
                 document.getElementById("edit_gender").value = data.gender || '';
            }
        }

        function closeEdit() {
            document.getElementById("editModal").classList.add("hidden");
            document.body.style.overflow = 'auto';
        }
        
        function confirmDelete() {
            return confirm(jsTranslations.confirm_delete);
        }
        
        // Add keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // ESC to close modals
            if (e.key === 'Escape') {
                closeAdd();
                closeEdit();
            }
            
            // Ctrl/Cmd + N to open add modal
            if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
                e.preventDefault();
                openAdd();
            }
        });
        
        // Add some interactive feedback for buttons
    </script>

    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

    <script>
        $(document).ready(function() {
            $('#studentsTable').DataTable({
                responsive: true,
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "<?= t('search_placeholder') ?>",
                    lengthMenu: "<?= $currentLangId == 2 ? 'عرض _MENU_ سجلات' : 'Show _MENU_ entries' ?>",
                    info: "<?= $currentLangId == 2 ? 'عرض _START_ إلى _END_ من _TOTAL_ طلاب' : 'Showing _START_ to _END_ of _TOTAL_ students' ?>",
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
                order: [[0, 'desc']] // Sort by ID initially
            });
        });

        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('button, a[href]');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    // Add a subtle ripple effect
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    
                    ripple.style.cssText = `
                        position: absolute;
                        border-radius: 50%;
                        background: rgba(255, 255, 255, 0.7);
                        transform: scale(0);
                        animation: ripple 0.6s linear;
                        width: ${size}px;
                        height: ${size}px;
                        top: ${y}px;
                        left: ${x}px;
                        pointer-events: none;
                    `;
                    
                    this.style.position = 'relative';
                    this.style.overflow = 'hidden';
                    this.appendChild(ripple);
                    
                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
            
            // Add CSS for ripple animation
            const style = document.createElement('style');
            style.textContent = `
                @keyframes ripple {
                    to {
                        transform: scale(4);
                        opacity: 0;
                    }
                }
            `;
            document.head.appendChild(style);
            
            // RTL-specific adjustments
            const isRTL = document.documentElement.dir === 'rtl';
            if (isRTL) {
                // Adjust modal close button position
                document.querySelectorAll('#addModal button:last-child, #editModal button:last-child').forEach(btn => {
                    btn.style.left = '15px';
                    btn.style.right = 'auto';
                });
                
                // Adjust table text alignment
                document.querySelectorAll('table th, table td').forEach(cell => {
                    if (!cell.classList.contains('text-center')) {
                        cell.style.textAlign = 'right';
                    }
                });
                
                // Adjust icon positions in stats cards
                document.querySelectorAll('.stats-grid .stat-icon').forEach(icon => {
                    icon.parentNode.classList.add('flex-row-reverse');
                });
                
                // Adjust search icon position
                const searchIcon = document.querySelector('form[method="GET"] .absolute');
                if (searchIcon) {
                    searchIcon.style.left = 'auto';
                    searchIcon.style.right = '0.75rem';
                }
            }
            // RTL-specific adjustments
            const isRTL = document.documentElement.dir === 'rtl';
            if (isRTL) {
                // ... existing RTL logic
            }
            
            // Initialize Tom Select
            document.querySelectorAll('.tom-select-class').forEach((el) => {
                const ts = new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    plugins: [], // Removed remove_button to match screenshot (caret style)
                    searchField: ['text'], // Ensure text search works
                    dropdownParent: 'body' // Fix for modal clipping
                });
                
                // Store instance for edit modal if this is the edit select
                if (el.id === 'edit_class') {
                    window.tomSelectEditClass = ts;
                }
                if (el.id === 'edit_gender') {
                    window.tomSelectEditGender = ts;
                }
            });
        });
    </script>
</body>
</html>