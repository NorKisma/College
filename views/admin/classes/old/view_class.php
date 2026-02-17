<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
require_once __DIR__ . '/../../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Class Students | School Management System',
        'class_details' => 'Class Details',
        'section' => 'Section',
        'class_id' => 'Class ID',
        'total_students' => 'Total Students',
        'all_time_in_class' => 'All time in this class',
        'active_students' => 'Active Students',
        'currently_enrolled' => 'Currently enrolled',
        'graduated' => 'Graduated',
        'alumni_from_class' => 'Alumni from this class',
        'active_percentage' => 'Active Percentage',
        'still_studying' => 'Still studying',
        'filter_students' => 'Filter Students',
        'search_by_name' => 'Search by student name...',
        'all_students' => 'All Students',
        'active_only' => 'Active Only',
        'graduated_only' => 'Graduated Only',
        'apply_filters' => 'Apply Filters',
        'active_filters' => 'Active filters',
        'clear_filters' => 'Clear Filters',
        'student_name' => 'Student Name',
        'status' => 'Status',
        'current_class' => 'Current Class',
        'no_students_found' => 'No students found',
        'adjust_filters' => 'Try adjusting your filters to see more results.',
        'no_students_assigned' => 'No students are assigned to this class yet.',
        'active' => 'Active',
        'showing_students' => 'Showing student(s)',
        'last_updated' => 'Last updated',
        'back_to_classes' => 'Back to Classes',
        'export_data' => 'Export Data',
        // 'print_report' => 'Print Report',
        'refresh_data' => 'Refresh Data',
        'student_id' => 'ID',
        'class_not_found' => 'Class not found',
        'unauthorized_access' => 'Unauthorized access.',
        'invalid_class_id' => 'Invalid class ID.'
    ],
    2 => [ // Arabic
        'page_title' => 'طلاب الصف | نظام إدارة المدرسة',
        'class_details' => 'تفاصيل الصف',
        'section' => 'القسم',
        'class_id' => 'معرف الصف',
        'total_students' => 'إجمالي الطلاب',
        'all_time_in_class' => 'جميع الأوقات في هذا الصف',
        'active_students' => 'الطلاب النشطين',
        'currently_enrolled' => 'مسجلون حالياً',
        'graduated' => 'متخرجون',
        'alumni_from_class' => 'خريجون من هذا الصف',
        'active_percentage' => 'النسبة النشطة',
        'still_studying' => 'ما زالوا يدرسون',
        'filter_students' => 'تصفية الطلاب',
        'search_by_name' => 'البحث باسم الطالب...',
        'all_students' => 'جميع الطلاب',
        'active_only' => 'النشطين فقط',
        'graduated_only' => 'المتخرجين فقط',
        'apply_filters' => 'تطبيق الفلاتر',
        'active_filters' => 'الفلاتر النشطة',
        'clear_filters' => 'مسح الفلاتر',
        'student_name' => 'اسم الطالب',
        'status' => 'الحالة',
        'current_class' => 'الصف الحالي',
        'no_students_found' => 'لم يتم العثور على طلاب',
        'adjust_filters' => 'حاول تعديل فلاتر البحث لرؤية المزيد من النتائج.',
        'no_students_assigned' => 'لا يوجد طلاب معينين لهذا الصف بعد.',
        'active' => 'نشط',
        'showing_students' => 'عرض الطلاب',
        'last_updated' => 'آخر تحديث',
        'back_to_classes' => 'العودة إلى الصفوف',
        // 'export_data' => 'تصدير البيانات',
        // 'print_report' => 'طباعة التقرير',
        'refresh_data' => 'تحديث البيانات',
        'student_id' => 'الرقم',
        'class_not_found' => 'الصف غير موجود',
        'unauthorized_access' => 'وصول غير مصرح به.',
        'invalid_class_id' => 'معرف الصف غير صالح.'
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

// Auth check
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: ../../auth/login.php');
    exit;
}

// Load user
$me_stmt = $conn->prepare("SELECT u.id, u.username, r.name AS role_name 
                           FROM users u 
                           JOIN roles r ON r.id = u.role_id 
                           WHERE u.id=?");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
$my_role_name = $me['role_name'];

// Admin check
function is_admin() { 
    global $my_role_name; 
    return $my_role_name === 'admin'; 
}

if (!is_admin()) {
    die("<div class='alert alert-danger'>" . t('unauthorized_access') . "</div>");
}

// Get class ID from GET
$class_id = intval($_GET['id'] ?? 0);
if ($class_id <= 0) die("<div class='alert alert-danger'>" . t('invalid_class_id') . "</div>");

// Search/filter
$search = trim($_GET['search'] ?? '');
$status_filter = $_GET['status'] ?? 'all'; // all / active / graduated

// Fetch class info
$class_stmt = $conn->prepare("SELECT c.id, c.name, s.name AS section_name
                              FROM classes c
                              LEFT JOIN sections s ON c.section_id = s.id
                              WHERE c.id=?");
$class_stmt->bind_param('i', $class_id);
$class_stmt->execute();
$class = $class_stmt->get_result()->fetch_assoc();
if (!$class) die("<div class='alert alert-danger'>" . t('class_not_found') . "</div>");

// ---------------- Fetch students (Active + Alumni) ----------------

$sql = "

SELECT *
FROM (

    /* STUDENTS FROM THIS CLASS (PAST OR PRESENT) */
    SELECT DISTINCT
        st.id AS student_id,
        st.name AS student_name,

        CASE
            WHEN cs_active.id IS NOT NULL THEN 1
            ELSE 0
        END AS is_active,

        c_current.name AS current_class_name,
        s_current.name AS current_section_name

    FROM students st

    /* Was in this class */
    LEFT JOIN class_students cs_history 
        ON cs_history.student_id = st.id
        AND cs_history.class_id = ?

    /* Alumni from this class */
    LEFT JOIN alumni a 
        ON a.student_id = st.id
        AND a.class_id = ?

    /* Current active class (any class) */
    LEFT JOIN class_students cs_active 
        ON cs_active.student_id = st.id
        AND cs_active.is_active = 1

    LEFT JOIN classes c_current ON c_current.id = cs_active.class_id
    LEFT JOIN sections s_current ON s_current.id = c_current.section_id

    WHERE cs_history.id IS NOT NULL
       OR a.id IS NOT NULL

) AS combined
WHERE 1
";

$params = [$class_id, $class_id];
$types = "ii";


// Search
if ($search !== '') {
    $sql .= " AND student_name LIKE ?";
    $params[] = "%$search%";
    $types .= "s";
}

// Status filter
if ($status_filter === 'active') {
    $sql .= " AND is_active = 1";
} elseif ($status_filter === 'graduated') {
    $sql .= " AND is_active = 0";
}

$sql .= " ORDER BY student_name ASC";

$stmt = $conn->prepare($sql);
if (!$stmt) die("Prepare failed: " . $conn->error);

$stmt->bind_param($types, ...$params);
$stmt->execute();
$students_res = $stmt->get_result();


// Get statistics
$total_students = 0;
$active_students = 0;
$graduated_students = 0;
$students = [];

while ($row = $students_res->fetch_assoc()) {
    $students[] = $row;
    $total_students++;
    if ($row['is_active'] == 1) {
        $active_students++;
    } else {
        $graduated_students++;
    }
}

// Theme detection
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
    <title><?= htmlspecialchars($class['name']) ?> | <?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        :root {
            /* Light Theme Colors */
            --light-primary: #4361ee;
            --light-secondary: #3a0ca3;
            --light-success: #4cc9f0;
            --light-info: #4895ef;
            --light-warning: #f72585;
            --light-danger: #7209b7;
            --light-bg: #f8f9fa;
            --light-card: #ffffff;
            --light-text: #212529;
            --light-border: #dee2e6;
            --light-muted: #6c757d;
            
            /* Dark Theme Colors */
            --dark-primary: #4361ee;
            --dark-secondary: #3a0ca3;
            --dark-success: #4cc9f0;
            --dark-info: #4895ef;
            --dark-warning: #f72585;
            --dark-danger: #7209b7;
            --dark-bg: #121826;
            --dark-card: #1e293b;
            --dark-text: #e2e8f0;
            --dark-border: #334155;
            --dark-muted: #94a3b8;
        }

        <?php if ($theme_mode === 0): ?>
        /* Dark Theme */
        body {
            background-color: var(--dark-bg);
            color: var(--dark-text);
        }
        
        .card {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }
        
        .text-muted {
            color: var(--dark-muted) !important;
        }
        
        .table {
            color: var(--dark-text);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }
        
        .form-control, .form-select {
            background-color: #1e293b;
            border-color: var(--dark-border);
            color: var(--dark-text);
        }
        
        .form-control:focus, .form-select:focus {
            background-color: #1e293b;
            border-color: var(--dark-primary);
            color: var(--dark-text);
        }
        
        .badge.bg-light {
            background-color: #334155 !important;
            color: var(--dark-text) !important;
        }
        
        .header-gradient {
            background: linear-gradient(135deg, var(--dark-primary) 0%, var(--dark-secondary) 100%);
        }
        
        .stats-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
        }
        
        .filter-card {
            background-color: var(--dark-card);
            border: 1px solid var(--dark-border);
        }
        
        .empty-state {
            background-color: rgba(30, 41, 59, 0.5);
        }
        
        .student-avatar {
            background-color: #334155;
            color: var(--dark-primary);
        }
        
        .status-badge.active-status {
            background-color: rgba(76, 201, 240, 0.2);
            color: #4cc9f0;
        }
        
        .status-badge.graduated-status {
            background-color: rgba(114, 9, 183, 0.2);
            color: #7209b7;
        }
        
        .pagination .page-link {
            background-color: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--dark-primary);
            border-color: var(--dark-primary);
        }
        
        .btn-outline-secondary {
            color: var(--dark-muted);
            border-color: var(--dark-muted);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--dark-muted);
            color: var(--dark-bg);
        }
        <?php else: ?>
        /* Light Theme */
        body {
            background-color: var(--light-bg);
            color: var(--light-text);
        }
        
        .header-gradient {
            background: linear-gradient(135deg, var(--light-primary) 0%, var(--light-secondary) 100%);
        }
        
        .stats-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .status-badge.active-status {
            background-color: rgba(76, 201, 240, 0.1);
            color: #0d6efd;
        }
        
        .status-badge.graduated-status {
            background-color: rgba(114, 9, 183, 0.1);
            color: #7209b7;
        }
        
        .student-avatar {
            background-color: #e9ecef;
            color: var(--light-primary);
        }
        <?php endif; ?>
        
        /* Common Styles */
        .header-gradient {
            padding: 2rem;
            border-radius: 12px;
            color: white;
            margin-bottom: 1.5rem;
        }
        
        .stats-card {
            border-radius: 10px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }
        
        .filter-card {
            border-radius: 12px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .search-input-group {
            position: relative;
        }
        
        .search-icon {
            position: absolute;
            left: <?= $currentLangId == 2 ? 'auto' : '15px' ?>;
            right: <?= $currentLangId == 2 ? '15px' : 'auto' ?>;
            top: 50%;
            transform: translateY(-50%);
            color: var(--light-muted);
            z-index: 10;
        }
        
        .form-control.ps-5 {
            padding-<?= $currentLangId == 2 ? 'right' : 'left' ?>: 45px !important;
        }
        
        .table-container {
            background-color: <?= $theme_mode === 0 ? 'var(--dark-card)' : 'white' ?>;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        
        .student-row {
            transition: background-color 0.2s ease;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
        }
        
        .section-badge {
            background-color: rgba(255, 255, 255, 0.2);
            color: white;
            font-weight: 500;
        }
        
        .empty-state {
            padding: 4rem 2rem;
        }
        
        .empty-icon {
            font-size: 4rem;
            color: var(--light-muted);
            margin-bottom: 1.5rem;
        }
        
        .student-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }
        
        /* Responsive Design */
        @media (max-width: 768px) {
            .header-gradient {
                padding: 1.5rem;
            }
            
            .stats-card .d-flex {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-icon {
                margin-top: 1rem;
                width: 40px;
                height: 40px;
                font-size: 1.2rem;
            }
            
            .filter-card .row > div {
                margin-bottom: 1rem;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .status-badge {
                font-size: 0.8rem;
                padding: 4px 8px;
            }
        }
        
        @media (max-width: 576px) {
            .header-gradient {
                padding: 1rem;
            }
            
            h1, h2, h3 {
                font-size: 1.5rem;
            }
            
            .stats-card {
                margin-bottom: 1rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .student-avatar {
                width: 35px;
                height: 35px;
                font-size: 1rem;
            }
        }
        
        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            body {
                background-color: white !important;
                color: black !important;
            }
            
            .card, .table-container {
                border: 1px solid #ddd !important;
                box-shadow: none !important;
            }
            
            .header-gradient {
                background: none !important;
                color: black !important;
                border-bottom: 2px solid black;
            }
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body class="<?= $theme_mode === 0 ? 'dark-mode' : 'light-mode' ?>">
    <div class="container-fluid py-4 px-3 px-md-4">
        <!-- Header Section -->
        <div class="header-card mb-4 fade-in">
            <div class="header-gradient">
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                    <div class="mb-3 mb-md-0">
                        <div class="d-flex align-items-center mb-2">
                            <i class="bi bi-people-fill me-2 fs-4"></i>
                            <h1 class="h3 mb-0 fw-bold"><?= t('class_details') ?></h1>
                        </div>
                        <div class="d-flex flex-wrap align-items-center gap-3">
                            <div>
                                <h2 class="h2 fw-bold mb-0"><?= htmlspecialchars($class['name']) ?></h2>
                                <div class="d-flex align-items-center mt-1">
                                    <span class="badge section-badge py-2 px-3">
                                        <i class="bi bi-layers me-1"></i>
                                        <?= htmlspecialchars($class['section_name']) ?> <?= t('section') ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="text-md-end mt-3 mt-md-0">
                        <div class="text-white-50 mb-1"><?= t('class_id') ?></div>
                        <div class="h5 fw-bold">#<?= $class['id'] ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex flex-wrap gap-2 mb-4 fade-in">
            <a href="../classes.php" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> <?= t('back_to_classes') ?>
            </a>
            <!-- <button class="btn btn-outline-primary" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> <?= t('print_report') ?>
            </button>
            <button class="btn btn-outline-success" id="exportBtn">
                <i class="bi bi-download me-1"></i> <?= t('export_data') ?>
            </button> -->
            <a href="?id=<?= $class_id ?>" class="btn btn-outline-info">
                <i class="bi bi-arrow-clockwise me-1"></i> <?= t('refresh_data') ?>
            </a>
        </div>

        <!-- Statistics Cards -->
        <div class="row g-4 mb-4 fade-in">
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-start border-primary border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('total_students') ?></h6>
                                <h2 class="fw-bold mb-0"><?= $total_students ?></h2>
                                <small class="text-muted"><?= t('all_time_in_class') ?></small>
                            </div>
                            <div class="stats-icon bg-primary bg-opacity-10 text-primary">
                                <i class="bi bi-people-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-start border-success border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('active_students') ?></h6>
                                <h2 class="fw-bold mb-0"><?= $active_students ?></h2>
                                <small class="text-muted"><?= t('currently_enrolled') ?></small>
                            </div>
                            <div class="stats-icon bg-success bg-opacity-10 text-success">
                                <i class="bi bi-person-check-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-start border-dark border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('graduated') ?></h6>
                                <h2 class="fw-bold mb-0"><?= $graduated_students ?></h2>
                                <small class="text-muted"><?= t('alumni_from_class') ?></small>
                            </div>
                            <div class="stats-icon bg-dark bg-opacity-10 text-dark">
                                <i class="bi bi-mortarboard-fill"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6 col-lg-3">
                <div class="card stats-card h-100 border-start border-info border-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('active_percentage') ?></h6>
                                <h2 class="fw-bold mb-0">
                                    <?= $total_students > 0 ? round(($active_students / $total_students) * 100) : 0 ?>%
                                </h2>
                                <small class="text-muted"><?= t('still_studying') ?></small>
                            </div>
                            <div class="stats-icon bg-info bg-opacity-10 text-info">
                                <i class="bi bi-percent"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-card p-4 mb-4 fade-in">
            <h5 class="fw-bold mb-3">
                <i class="bi bi-funnel me-2"></i><?= t('filter_students') ?>
            </h5>
            <form method="GET" class="row g-3">
                <input type="hidden" name="id" value="<?= $class_id ?>">
                
                <div class="col-lg-5 col-md-6">
                    <div class="search-input-group">
                        <i class="bi bi-search search-icon"></i>
                        <input type="text" 
                               name="search" 
                               class="form-control ps-5" 
                               placeholder="<?= t('search_by_name') ?>" 
                               value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>
                
                <div class="col-lg-3 col-md-4">
                    <select name="status" class="form-select">
                        <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>><?= t('all_students') ?></option>
                        <option value="active" <?= $status_filter === 'active' ? 'selected' : '' ?>><?= t('active_only') ?></option>
                        <option value="graduated" <?= $status_filter === 'graduated' ? 'selected' : '' ?>><?= t('graduated_only') ?></option>
                    </select>
                </div>
                
                <div class="col-lg-2 col-md-2">
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter me-1"></i> <?= t('apply_filters') ?>
                        </button>
                    </div>
                </div>
                
                <div class="col-lg-2 col-md-2">
                    <div class="d-grid">
                        <a href="?id=<?= $class_id ?>" class="btn btn-outline-secondary">
                            <i class="bi bi-x-circle me-1"></i> <?= t('clear_filters') ?>
                        </a>
                    </div>
                </div>
            </form>
            
            <?php if ($search || $status_filter !== 'all'): ?>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex flex-column flex-md-row align-items-start align-items-md-center">
                        <small class="text-muted me-2 mb-2 mb-md-0"><?= t('active_filters') ?>:</small>
                        <div class="d-flex flex-wrap gap-2">
                            <?php if ($search): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-search me-1"></i> "<?= htmlspecialchars($search) ?>"
                                </span>
                            <?php endif; ?>
                            <?php if ($status_filter !== 'all'): ?>
                                <span class="badge bg-light text-dark border">
                                    <i class="bi bi-funnel me-1"></i> 
                                    <?= $status_filter === 'active' ? t('active_only') : t('graduated_only') ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Students Table -->
        <div class="table-container fade-in">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th width="5%" class="text-center">#</th>
                            <th width="40%"><?= t('student_name') ?></th>
                            <th width="20%"><?= t('status') ?></th>
                            <th width="35%"><?= t('current_class') ?></th> 
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="4" class="text-center empty-state">
                                    <div class="py-5">
                                        <i class="bi bi-people empty-icon"></i>
                                        <h5 class="text-muted mb-2"><?= t('no_students_found') ?></h5>
                                        <p class="text-muted mb-0">
                                            <?php if ($search || $status_filter !== 'all'): ?>
                                                <?= t('adjust_filters') ?>
                                            <?php else: ?>
                                                <?= t('no_students_assigned') ?>
                                            <?php endif; ?>
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php $i = 1; ?>
                            <?php foreach ($students as $row): ?>
                                <tr class="student-row">
                                    <td class="text-center fw-semibold text-muted"><?= $i++ ?></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="flex-shrink-0">
                                                <div class="student-avatar">
                                                    <i class="bi bi-person"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h6 class="mb-0 fw-semibold"><?= htmlspecialchars($row['student_name']) ?></h6>
                                                <small class="text-muted"><?= t('student_id') ?>: <?= $row['student_id'] ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                       <?php if ($row['is_active'] == 1): ?>

    <span class="status-badge active-status">
        <i class="bi bi-check-circle me-1"></i> <?= t('active') ?>
    </span>

<?php else: ?>

    <?php if (!empty($row['current_class_name'])): ?>
        <span class="status-badge graduated-status">
            <i class="bi bi-mortarboard me-1"></i>
            Graduated (Active elsewhere)
        </span>
    <?php else: ?>
        <span class="status-badge graduated-status">
            <i class="bi bi-mortarboard me-1"></i> <?= t('graduated') ?>
        </span>
    <?php endif; ?>

<?php endif; ?>

                                    </td>
                                    <td>
<?php if ($row['current_class_name']): ?>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-2">
                                                    <i class="bi bi-building text-warning"></i>
                                                </div>
                                                <div>
                                                    <span class="fw-medium"><?= htmlspecialchars($row['current_class_name']) ?></span>
                                                    <div class="text-muted small">
                                                        <?= htmlspecialchars($row['current_section_name']) ?>
                                                    </div>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-muted fst-italic">-</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="mt-4 text-center text-muted small fade-in">
            <div class="d-flex flex-wrap justify-content-center align-items-center gap-3">
                <div>
                    <i class="bi bi-info-circle me-1"></i> 
                    <?= t('showing_students') ?>: <?= count($students) ?>
                </div>
                <div>•</div>
                <div>
                    <i class="bi bi-clock me-1"></i> 
                    <?= t('last_updated') ?>: <?= date('F j, Y, g:i a') ?>
                </div> 
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Add row hover effect
        document.addEventListener('DOMContentLoaded', function() {
            const rows = document.querySelectorAll('.student-row');
            rows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = '<?= $theme_mode === 0 ? 'rgba(255, 255, 255, 0.05)' : '#f8f9fa' ?>';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            });
            
            // Auto-focus search input
            const searchInput = document.querySelector('input[name="search"]');
            if (searchInput) {
                searchInput.focus();
            }
            
            // Export functionality
            const exportBtn = document.getElementById('exportBtn');
            if (exportBtn) {
                exportBtn.addEventListener('click', function() {
                    // Simple CSV export
                    const table = document.querySelector('table');
                    const rows = table.querySelectorAll('tr');
                    let csvContent = "data:text/csv;charset=utf-8,";
                    
                    rows.forEach(row => {
                        const cols = row.querySelectorAll('td, th');
                        const rowData = [];
                        cols.forEach(col => {
                            let text = col.innerText.replace(/,/g, '');
                            // Remove icons and extra whitespace
                            text = text.replace(/[\n\r]+/g, ' ').trim();
                            rowData.push(text);
                        });
                        csvContent += rowData.join(",") + "\n";
                    });
                    
                    const encodedUri = encodeURI(csvContent);
                    const link = document.createElement("a");
                    link.setAttribute("href", encodedUri);
                    link.setAttribute("download", "class_<?= $class['id'] ?>_students.csv");
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                });
            }
            
            // Add fade-in animation to elements
            const fadeElements = document.querySelectorAll('.fade-in');
            fadeElements.forEach((el, index) => {
                el.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Theme toggle simulation (for demo purposes)
          
           
        });
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Ctrl+F to focus search
            if (e.ctrlKey && e.key === 'f') {
                e.preventDefault();
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.focus();
                    searchInput.select();
                }
            }
            
            // Ctrl+P for print
            if (e.ctrlKey && e.key === 'p') {
                e.preventDefault();
                window.print();
            }
            
            // Escape to clear search
            if (e.key === 'Escape') {
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput && searchInput.value) {
                    searchInput.value = '';
                    searchInput.form.submit();
                }
            }
        });
    </script>
</body>
</html>