<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';
require_once __DIR__ . '/../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Analytics Dashboard - Comprehensive Reports',
        'analytics_dashboard' => 'Analytics Dashboard',
        'comprehensive_insights' => 'Comprehensive insights and visual reports',
        'teacher_suspended' => 'Teacher',
        'is_suspended' => 'is suspended — Subjects stopped:',
        'no_classes_currently' => 'No classes are currently being taught.',
        'total_users' => 'Total Users',
        'active' => 'Active',
        'teachers' => 'Teachers',
        'students' => 'Students',
        'enrolled' => 'Enrolled',
        'classes_sections' => 'Classes & Sections',
        'sections' => 'Sections',
        'teachers_salaries' => 'Teachers\' Salaries',
        'total_paid' => 'Total Paid',
        'total_expenses' => 'Total Expenses',
        'all_expenses' => 'All Expenses',
        'expected_fees_this_month' => 'Expected Fees (This Month)',
        'from_active_students' => 'From Active Students',
        'attendance_distribution' => 'Attendance Distribution',
        'gender_distribution' => 'Gender Distribution',
        'attendance_trend' => 'Attendance Trend',
        'this_week_sat_wed' => 'This Week: Sat – Wed',
        'class_distribution' => 'Class Distribution',
        'top_5' => 'Top 5',
        'attendance_heatmap' => 'Attendance Heatmap',
        'current_week' => 'Current Week',
        'present' => 'Present',
        'late' => 'Late',
        'absent' => 'Absent',
        'excused' => 'Excused',
        'top_students' => 'Top Students',
        'recent_exams' => 'Recent Exams',
        'teacher_workload' => 'Teacher Workload',
        'subjects' => 'subjects',
        'recent_updates' => 'Recent Updates',
        'detailed_statistics' => 'Detailed Statistics',
        'category' => 'Category',
        'total' => 'Total',
        'inactive' => 'Inactive',
        'growth' => 'Growth',
        'new_student_enrolled' => 'New Student Enrolled',
        'exam_published' => 'Exam Published',
        'changed_from_to' => 'Changed from',
        'to' => 'to',
        'by' => 'by',
        'enrolled_event' => 'enrolled',
        'exam_published_event' => 'exam published',
        'attendance_updated' => 'attendance updated',
        'male' => 'Male',
        'female' => 'Female',
        'other' => 'Other',
        'users' => 'Users',
        'classes' => 'Classes'
    ],
    2 => [ // Arabic
        'page_title' => 'لوحة التحليل - التقارير الشاملة',
        'analytics_dashboard' => 'لوحة التحليل',
        'comprehensive_insights' => 'رؤى شاملة وتقارير مرئية',
        'teacher_suspended' => 'المعلم',
        'is_suspended' => 'معلّق - المواد المتوقفة:',
        'no_classes_currently' => 'لا توجد فصول تُدرّس حالياً.',
        'total_users' => 'إجمالي المستخدمين',
        'active' => 'نشط',
        'teachers' => 'المعلمون',
        'students' => 'الطلاب',
        'enrolled' => 'مسجل',
        'classes_sections' => 'الفصول والأقسام',
        'sections' => 'الأقسام',
        'teachers_salaries' => 'رواتب المعلمين',
        'total_paid' => 'إجمالي المدفوع',
        'total_expenses' => 'إجمالي المصروفات',
        'all_expenses' => 'جميع المصروفات',
        'expected_fees_this_month' => 'الرسوم المتوقعة (هذا الشهر)',
        'from_active_students' => 'من الطلاب النشطين',
        'attendance_distribution' => 'توزيع الحضور',
        'gender_distribution' => 'توزيع الجنس',
        'attendance_trend' => 'اتجاه الحضور',
        'this_week_sat_wed' => 'هذا الأسبوع: سبت – أربعاء',
        'class_distribution' => 'توزيع الفصول',
        'top_5' => 'أعلى 5',
        'attendance_heatmap' => 'خريطة حرارة الحضور',
        'current_week' => 'الأسبوع الحالي',
        'present' => 'حاضر',
        'late' => 'متأخر',
        'absent' => 'غائب',
        'excused' => 'معذور',
        'top_students' => 'أفضل الطلاب',
        'recent_exams' => 'الامتحانات الأخيرة',
        'teacher_workload' => 'عبء عمل المعلم',
        'subjects' => 'مواد',
        'recent_updates' => 'التحديثات الأخيرة',
        'detailed_statistics' => 'إحصائيات مفصلة',
        'category' => 'الفئة',
        'total' => 'الإجمالي',
        'inactive' => 'غير نشط',
        'growth' => 'النمو',
        'new_student_enrolled' => 'طالب جديد مسجل',
        'exam_published' => 'امتحان منشور',
        'changed_from_to' => 'تغير من',
        'to' => 'إلى',
        'by' => 'بواسطة',
        'enrolled_event' => 'مسجل',
        'exam_published_event' => 'امتحان منشور',
        'attendance_updated' => 'تم تحديث الحضور',
        'male' => 'ذكر',
        'female' => 'أنثى',
        'other' => 'آخر',
        'users' => 'المستخدمين',
        'classes' => 'الفصول'
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

// Get theme from database
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
if($theme_res && $theme_row = $theme_res->fetch_assoc()){
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

// Get current date range (default last 30 days)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');
$report_type = isset($_GET['type']) ? $_GET['type'] : 'overview';

// Fetch all statistics
$stats = [];

// User Statistics
$stats['total_users'] = $conn->query("SELECT COUNT(*) as count FROM users")->fetch_assoc()['count'];
$stats['active_users'] = $conn->query("SELECT COUNT(*) as count FROM users WHERE status = 'active'")->fetch_assoc()['count'];
$user_roles = $conn->query("SELECT r.name, COUNT(u.id) as count FROM users u JOIN roles r ON u.role_id = r.id GROUP BY r.id");
$stats['user_roles'] = [];
while($row = $user_roles->fetch_assoc()) {
    $stats['user_roles'][] = $row;
}

// Teacher Statistics
$stats['total_teachers'] = $conn->query("SELECT COUNT(*) as count FROM teachers")->fetch_assoc()['count'];
$stats['active_teachers'] = $conn->query("SELECT COUNT(*) as count FROM teachers WHERE status = 'active'")->fetch_assoc()['count'];
$stats['teacher_gender'] = $conn->query("SELECT gender, COUNT(*) as count FROM teachers GROUP BY gender")->fetch_all(MYSQLI_ASSOC);

// Student Statistics
$stats['total_students'] = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'];
$stats['student_gender'] = $conn->query("SELECT gender, COUNT(*) as count FROM students GROUP BY gender")->fetch_all(MYSQLI_ASSOC);

// Class & Section Statistics
$stats['total_classes'] = $conn->query("SELECT COUNT(*) as count FROM classes")->fetch_assoc()['count'];
$stats['total_sections'] = $conn->query("SELECT COUNT(*) as count FROM sections")->fetch_assoc()['count'];

// Subject Statistics
$stats['total_subjects'] = $conn->query("SELECT COUNT(*) as count FROM subjects")->fetch_assoc()['count'];

// Get current week's Saturday to Wednesday
$today = new DateTime();

// Find last Saturday
$startOfWeek = clone $today;
$startOfWeek->modify('last saturday');

// If today is Saturday, use today
if ($today->format('N') == 6) {
    $startOfWeek = clone $today;
}

// End of range = Wednesday of same week
$endOfWeek = clone $startOfWeek;
$endOfWeek->modify('+4 days');

$week_start = $startOfWeek->format('Y-m-d');
$week_end   = $endOfWeek->format('Y-m-d');


// Attendance Statistics
$attendance_stats = $conn->query("
    SELECT 
        status,
        COUNT(*) as count,
        ROUND(COUNT(*) * 100.0 / (SELECT COUNT(*) FROM attendance WHERE date BETWEEN '$start_date' AND '$end_date'), 1) as percentage
    FROM attendance 
    WHERE date BETWEEN '$start_date' AND '$end_date'
    GROUP BY status
")->fetch_all(MYSQLI_ASSOC);
$stats['attendance'] = $attendance_stats;

// Daily attendance trend
$weekly_attendance = $conn->query("
    SELECT 
        DATE(date) as day,
        DAYNAME(date) as day_name,
        COUNT(CASE WHEN status = 'present' THEN 1 END) as present,
        COUNT(CASE WHEN status = 'absent' THEN 1 END) as absent,
        COUNT(CASE WHEN status = 'late' THEN 1 END) as late
    FROM attendance
    WHERE DATE(date) BETWEEN '$week_start' AND '$week_end'
    GROUP BY DATE(date)
    ORDER BY DATE(date)
")->fetch_all(MYSQLI_ASSOC);

$stats['weekly_attendance'] = $weekly_attendance;


// Exam Statistics
$stats['total_exams'] = $conn->query("SELECT COUNT(*) as count FROM exams")->fetch_assoc()['count'];
$stats['recent_exams'] = $conn->query("SELECT * FROM exams ORDER BY start_date DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

// Mark Statistics
$mark_stats = $conn->query("
    SELECT 
        s.name as subject,
        AVG(m.marks_obtained) as avg_marks,
        MAX(m.marks_obtained) as max_marks,
        MIN(m.marks_obtained) as min_marks,
        COUNT(DISTINCT m.student_id) as students_count
    FROM marks m
    JOIN subjects s ON m.subject_id = s.id
    GROUP BY m.subject_id
    ORDER BY avg_marks DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
$stats['top_subjects'] = $mark_stats;

// Parent Statistics
$stats['total_parents'] = $conn->query("SELECT COUNT(*) as count FROM parents")->fetch_assoc()['count'];

// Class-wise student count
$year_start = date('Y');
$year_end = date('Y') + 1;
$academic_year = "$year_start/$year_end";  // notice the slash!
$class_students = $conn->query("
    SELECT 
        c.id,
        c.name AS class_name,
        COUNT(DISTINCT cs.student_id) AS student_count
    FROM classes c
    LEFT JOIN class_students cs 
        ON cs.class_id = c.id
        AND cs.is_active = 1
    GROUP BY c.id
    ORDER BY student_count DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$stats['class_distribution'] = $class_students;



// Define current week Saturday → Wednesday
$today = date('Y-m-d');
$weekday = date('N'); // 1 = Monday, 7 = Sunday

// Find last Saturday
$saturday = date('Y-m-d', strtotime('last Saturday', strtotime($today)));
if ($weekday == 6) $saturday = $today; // if today is Saturday, start today

// Find next Wednesday after Saturday
$wednesday = date('Y-m-d', strtotime($saturday . ' +4 days'));

// Query attendance grouped by date and status
$attendance_query = $conn->query("
    SELECT 
        date,
        status,
        COUNT(*) as count
    FROM attendance
    WHERE date BETWEEN '$saturday' AND '$wednesday'
    GROUP BY date, status
    ORDER BY date
");

// Prepare array
// Get first and last day of current month
$first_day = date('Y-m-01');
$last_day  = date('Y-m-t'); // t = last day of the month

// Query attendance for the whole month
$attendance_query = $conn->query("
    SELECT 
        date,
        status,
        COUNT(*) as count
    FROM attendance
    WHERE date BETWEEN '$first_day' AND '$last_day'
    GROUP BY date, status
    ORDER BY date
");

// Prepare heatmap array
$heatmap_data = [];
while ($row = $attendance_query->fetch_assoc()) {
    $d = $row['date'];
    $s = $row['status'];
    $c = (int)$row['count'];
    if (!isset($heatmap_data[$d])) $heatmap_data[$d] = ['present'=>0,'absent'=>0,'late'=>0,'excused'=>0];
    $heatmap_data[$d][$s] = $c;
}

// Calculate total per day and convert to percentages
foreach ($heatmap_data as $d => $statuses) {
    $total = array_sum($statuses);
    if ($total > 0) {
        foreach ($statuses as $s => $count) {
            $heatmap_data[$d][$s] = round($count * 100 / $total); // percentage
        }
    }
}



// Teacher subject load
$teacher_load = $conn->query("
    SELECT 
        CONCAT(t.first_name, ' ', t.last_name) as teacher,
        COUNT(DISTINCT cs.subject_id) as subjects_count,
        COUNT(css.id) as classes_per_week
    FROM teachers t
    LEFT JOIN class_subjects cs ON t.id = cs.teacher_id
    LEFT JOIN class_subject_schedule css ON cs.id = css.class_subject_id
    GROUP BY t.id
    ORDER BY subjects_count DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
$stats['teacher_load'] = $teacher_load;

// Recent attendance changes
$recent_changes = $conn->query("
    SELECT 
        ah.changed_at,
        s.name as student_name,
        ah.old_status,
        ah.new_status,
        ah.recorded_by
    FROM attendance_history ah
    JOIN students s ON ah.student_id = s.id
    ORDER BY ah.changed_at DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);
$stats['recent_changes'] = $recent_changes;

// Top performing students
$top_students = $conn->query("
    SELECT 
        s.name,
        SUM(er.obtained_marks) AS total_marks
    FROM students s
    JOIN exam_results er ON s.id = er.student_id
    WHERE er.exam_id = (
        SELECT id 
        FROM exams 
        ORDER BY created_at DESC 
        LIMIT 1
    )
    GROUP BY s.id
    ORDER BY total_marks DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

$stats['top_students'] = $top_students;


// ---------- Growth helper ----------
function growth_percent($current, $previous) {
    if ($previous == 0) return 100;
    return round((($current - $previous) / $previous) * 100, 1);
}

// ---------- Previous period (same length before) ----------
$period_days = 30;
$prev_start = date('Y-m-d', strtotime("-{$period_days} days", strtotime($start_date)));
$prev_end   = date('Y-m-d', strtotime("-1 day", strtotime($start_date)));

// ---------- USERS ----------
$prev_users = $conn->query("
    SELECT COUNT(*) c FROM users 
    WHERE created_at BETWEEN '$prev_start' AND '$prev_end'
")->fetch_assoc()['c'];

$user_growth = growth_percent($stats['total_users'], $prev_users);

// ---------- TEACHERS ----------
$prev_teachers = $conn->query("
    SELECT COUNT(*) c FROM teachers 
    WHERE created_at BETWEEN '$prev_start' AND '$prev_end'
")->fetch_assoc()['c'];

$teacher_growth = growth_percent($stats['total_teachers'], $prev_teachers);

// ---------- STUDENTS ----------
$prev_students = $conn->query("
    SELECT COUNT(*) c FROM students 
    WHERE created_at BETWEEN '$prev_start' AND '$prev_end'
")->fetch_assoc()['c'];

$student_growth = growth_percent($stats['total_students'], $prev_students);

// ---------- CLASSES ----------
$prev_classes = $conn->query("
    SELECT COUNT(*) c FROM classes 
    WHERE created_at BETWEEN '$prev_start' AND '$prev_end'
")->fetch_assoc()['c'];

$class_growth = growth_percent($stats['total_classes'], $prev_classes);


// ---------- Suspended / inactive teachers ----------
$suspended_teachers = $conn->query("
    SELECT 
        t.id,
        CONCAT(t.first_name, ' ', t.last_name) AS teacher_name,
        GROUP_CONCAT(DISTINCT s.name SEPARATOR ', ') AS subjects
    FROM teachers t
    LEFT JOIN class_subjects cs ON cs.teacher_id = t.id
    LEFT JOIN subjects s ON s.id = cs.subject_id
    WHERE t.status IN ('suspended', 'inactive')
    GROUP BY t.id
")->fetch_all(MYSQLI_ASSOC);

$warning_message = '';

if (!empty($suspended_teachers)) {
    $warnings = [];
    foreach ($suspended_teachers as $t) {
        $warnings[] =
            t('teacher_suspended') . " <strong>{$t['teacher_name']}</strong> " . 
            t('is_suspended') . " <strong>{$t['subjects']}</strong>. " . 
            t('no_classes_currently');
    }
    $warning_message = implode(' &nbsp; | &nbsp; ', $warnings);
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>" 
      data-theme="<?= $theme === 1 ? 'light' : 'dark' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- Date Range Picker -->
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
 
</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?> min-h-screen">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="display-6 fw-bold select text-muted">
                            <i class="fas fa-chart-network me-2"></i>
                            <?= t('analytics_dashboard') ?>
                        </h1>
                        <p class="mb-0 text-muted select"><?= t('comprehensive_insights') ?></p>
                   <?php if (!empty($suspended_teachers)): ?>
<div class="alert alert-warning teacher-warning mt-2">
    <div class="marquee">
        <div class="marquee-content">
            <?php foreach ($suspended_teachers as $index => $t): ?>
                <a href="/admin/teachers/view.php?id=<?= $t['id'] ?>" class="warning-link">
            <i class="fas fa-exclamation-triangle me-2"></i>

                    <?= t('teacher_suspended') ?> <strong><?= htmlspecialchars($t['teacher_name']) ?></strong>
                    <?= t('is_suspended') ?>
                    <strong><?= htmlspecialchars($t['subjects']) ?></strong>.
                    <?= t('no_classes_currently') ?>
                </a>

                <?php if ($index < count($suspended_teachers) - 1): ?>
                    &nbsp; | &nbsp;
                <?php endif; ?>
            <?php endforeach; ?>

        </div>
    </div>
</div>
<?php endif; ?>
<style>
                /* ---------- Teacher Suspension Marquee ---------- */
            /* ---------- Teacher Suspension Marquee FIX ---------- */
            .teacher-warning {
                overflow: hidden;
                border-radius: 12px;
                padding: 4px 0;         /* reduce vertical padding */
                font-size: 0.9rem;      /* set a fixed font size */
                line-height: 1.2;       /* control height */
                max-height: 40px;       /* restrict total height */
            }

            .marquee {
                width: 100%;
                overflow: hidden;
                height: 100%;           /* ensure it fits parent */
            }

            .marquee-content {
                display: inline-flex;
                align-items: center;
                gap: 40px;
                padding-left: 100%;
                white-space: nowrap;
                animation: marquee-rtl 30s linear infinite;
                font-weight: 600;
                height: 100%;           /* match parent height */
            }

            .teacher-warning:hover .marquee-content {
                animation-play-state: paused;
            }

            /* Right → Left */
            @keyframes marquee-rtl {
                from { transform: translateX(0); }
                to   { transform: translateX(-100%); }
            }

            /* Link MUST stay inline */
            .warning-link {
                display: inline-block;          /* VERY IMPORTANT */
                white-space: nowrap;            /* STOP word breaking */
                color: #ff4000ff;
                text-decoration: none;
            }
            /* ---------- Mobile optimization for marquee ---------- */
            @media (max-width: 576px) {
                .marquee{
                    max-height: 50px;
                }

                .teacher-warning {
                    max-width: 20rem;   
                    padding: 4px 0;
                    border-radius: 8px;
                }

                .marquee-content {
                    font-size: 0.8rem;          /* smaller text */
                    gap: 24px;
                    animation-duration: 30s;    /* faster on mobile */
                }

                .warning-link {
                    font-weight: 500;
                }

                .teacher-warning i {
                    font-size: 0.9rem;
                }
            }

            .warning-link:hover {
                text-decoration: underline;
            }

            /* Dark mode */
            body.dark .teacher-warning {
                background-color: #000000ff;
                border: 1px solid #7f1d1d;
            }

            body.dark .warning-link {
                color: #fecaca;
            }

            /* Default (English) – icons on right */
.top-summary .stat-card .icon-wrapper {
    right: 20px;
    left: auto;
}

/* Arabic – ONLY top 4 icons move left */
body[dir="rtl"] .top-summary .stat-card .icon-wrapper {
    left: 20px;
    right: auto;
}

/* Text spacing for top cards only */

.top-summary .stat-card .card-body {
    padding-right: 90px;
}

body[dir="rtl"] .top-summary .stat-card .card-body {
    padding-left: 90px;
    padding-right: 20px;
    text-align: right;
}


</style>
                    </div>
                    <div>
                        <button class="btn btn-outline-primary" onclick="toggleTheme()">
                            <i class="fas fa-moon"></i> 
                        </button>
                      
                    </div>
                </div>
            </div>
        </div>

        <!-- Summary Statistics -->
        <div class="row mb-4 top-summary">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="select mb-2"><?= t('total_users') ?></h6>
                        <h2 class="fw-bold select"><?= number_format($stats['total_users']) ?></h2>
                        <p class="mb-0">
                            <span class="text-success">
                                <i class="fas fa-arrow-up"></i> 
                                <?= number_format($stats['active_users']) ?> <?= t('active') ?>
                            </span>
                        </p>
                        <div class="icon-wrapper users">
                            <i class="fas fa-users"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class=" select  mb-2"><?= t('teachers') ?></h6>
                        <h2 class="fw-bold select"><?= number_format($stats['total_teachers']) ?></h2>
                        <p class="mb-0">
                            <span class="text-success">
                                <i class="fas fa-chalkboard-teacher"></i> 
                                <?= number_format($stats['active_teachers']) ?> <?= t('active') ?>
                            </span>
                        </p>
                        <div class="icon-wrapper teachers">
                            <i class="fas fa-chalkboard-teacher"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class=" select  mb-2"><?= t('students') ?></h6>
                        <h2 class="fw-bold select"><?= number_format($stats['total_students']) ?></h2>
                        <p class="mb-0">
                            <span class="text-info">
                                <i class="fas fa-graduation-cap"></i> 
                                <?= number_format(array_sum(array_column($stats['student_gender'], 'count'))) ?> <?= t('enrolled') ?>
                            </span>
                        </p>
                        <div class="icon-wrapper students">
                            <i class="fas fa-graduation-cap"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card stat-card">
                    <div class="card-body">
                        <h6 class="select mb-2"><?= t('classes_sections') ?></h6>
                        <h2 class="fw-bold select"><?= number_format($stats['total_classes']) ?></h2>
                        <p class="mb-0">
                            <span class="text-primary">
                                <i class="fas fa-school"></i> 
                                <?= number_format($stats['total_sections']) ?> <?= t('sections') ?>
                            </span>
                        </p>
                        <div class="icon-wrapper classes">
                            <i class="fas fa-school"></i>
                        </div>
                    </div>
                </div>
            </div>

            <?php
                if (in_array($_SESSION['role'] ?? '', ['admin', 'Finance'])):

                    // Total teachers' salaries
                    $salary_res = $conn->query("SELECT SUM(salary) as total_salaries FROM teachers");
                    $total_salaries = $salary_res ? (float) $salary_res->fetch_assoc()['total_salaries'] : 0;

                    // Total expenses
                    $expense_res = $conn->query("SELECT SUM(amount) as total_expenses FROM expenses");
                    $total_expenses = $expense_res ? (float) $expense_res->fetch_assoc()['total_expenses'] : 0;

                    // Expected income from active students (fee_category_id = 1)
                $expected_res = $conn->query("
                    SELECT SUM(sf.amount) AS expected_income
                    FROM class_students cs
                    INNER JOIN classes c ON cs.class_id = c.id
                    INNER JOIN sections s ON c.section_id = s.id
                    INNER JOIN section_fees sf 
                        ON sf.section_id = s.id 
                        AND sf.fee_category_id = 1
                        AND sf.id = (
                            SELECT MAX(id) 
                            FROM section_fees 
                            WHERE section_id = s.id AND fee_category_id = 1
                        )
                    WHERE cs.is_active = 1
                    AND s.payable = 1
                ");
                $expected_income = $expected_res ? (float) $expected_res->fetch_assoc()['expected_income'] : 0;

                ?>
                <div class="row mb-4 mt-3">
                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="select mb-2"><?= t('teachers_salaries') ?></h6>
                                <h2 class="fw-bold select">$<?= number_format($total_salaries, 2) ?></h2>
                                <p class="mb-0">
                                    <span class="text-warning">
                                        <i class="fas fa-dollar-sign"></i> <?= t('total_paid') ?>
                                    </span>
                                </p>
                                <div class="icon-wrapper salaries">
                                    <i class="fas fa-money-bill-wave"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="select mb-2"><?= t('total_expenses') ?></h6>
                                <h2 class="fw-bold select">$<?= number_format($total_expenses, 2) ?></h2>
                                <p class="mb-0">
                                    <span class="text-danger">
                                        <i class="fas fa-wallet"></i> <?= t('all_expenses') ?>
                                    </span>
                                </p>
                                <div class="icon-wrapper expenses">
                                    <i class="fas fa-wallet"></i>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-xl-4 col-md-6 mb-4">
                        <div class="card stat-card">
                            <div class="card-body">
                                <h6 class="select mb-2"><?= t('expected_fees_this_month') ?></h6>
                                <h2 class="fw-bold select">$<?= number_format($expected_income, 2) ?></h2>
                                <p class="mb-0">
                                    <span class="text-success">
                                        <i class="fas fa-money-bill"></i> <?= t('from_active_students') ?>
                                    </span>
                                </p>
                                <div class="icon-wrapper expected-fees">
                                    <i class="fas fa-money-bill"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

        </div>

        <!-- Main Content Grid -->
        <div class="analytics-grid">
            <!-- Chart 1: Attendance Distribution -->
            <div class="card">
                <div class="card-header">
                    <h5 class="select card-title mb-0">
                        <i class="fas fa-calendar-check me-2"></i>
                        <?= t('attendance_distribution') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 2: Gender Distribution -->
            <div class="card">
                <div class="card-header">
                    <h5 class="select card-title mb-0">
                        <i class="fas fa-venus-mars me-2"></i>
                        <?= t('gender_distribution') ?>
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="genderChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Chart 3: Daily Attendance Trend -->
            <div class="card">
                <div class="card-header">
                    <h5 class="select card-title mb-0">
                        <i class="fas fa-chart-line me-2"></i>
                       <?= t('attendance_trend') ?> (<?= t('this_week_sat_wed') ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="trendChart"></canvas>
                        <small class="  "><?= date('M d', strtotime($week_start)) ?> – <?= date('M d', strtotime($week_end)) ?>
                      </small>

                    </div>
                </div>
            </div>

            <!-- Chart 4: Class Distribution -->
            <div class="card">
                <div class="card-header">
                    <h5 class="select card-title mb-0">
                        <i class="fas fa-layer-group me-2"></i>
                        <?= t('class_distribution') ?> (<?= t('top_5') ?>)
                    </h5>
                </div>
                <div class="card-body">
                    <div class="chart-container">
                        <canvas id="classChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Heatmap Section -->
        <div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="select card-title mb-0">
                    <i class="fas fa-fire me-2"></i>
                    <?= t('attendance_heatmap') ?> (<?= t('current_week') ?>)
                </h5>
            </div>
            <div class="card-body">
                <div class="heatmap-container" id="heatmap"></div>
                <div class="row mt-3">
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="heatmap-day attendance-present me-2"></div>
                            <small class="select"><?= t('present') ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="heatmap-day attendance-late me-2"></div>
                            <small class="select"><?= t('late') ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="heatmap-day attendance-absent me-2"></div>
                            <small class="select"><?= t('absent') ?></small>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex align-items-center">
                            <div class="heatmap-day attendance-excused me-2"></div>
                            <small class="select"><?= t('excused') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


        <!-- Lists Section (4 columns) -->
        <div class="row mb-4">
            <!-- Top Students -->
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="select card-title mb-0">
                            <i class="fas fa-crown me-2 text-warning"></i>
                            <?= t('top_students') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach($stats['top_students'] as $index => $student): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center px-0 border-0">
                                    <div>
                                        <span class="badge bg-primary rounded-pill me-2">#<?= $index + 1 ?></span>
                                        <?= htmlspecialchars($student['name']) ?>
                                    </div>
                                    <span class="badge bg-success"><?= number_format($student['total_marks'], 1) ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Exams -->
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="select card-title mb-0">
                            <i class="fas fa-file-alt me-2 text-info"></i>
                            <?= t('recent_exams') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach($stats['recent_exams'] as $exam): ?>
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between">
                                        <strong><?= htmlspecialchars($exam['name']) ?></strong>
                                        <small class="  "><?= date('M d', strtotime($exam['start_date'])) ?></small>
                                    </div>
                                    <small class="  "><?= $exam['exam_type'] ?></small>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teacher Workload -->
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="select card-title mb-0">
                            <i class="fas fa-briefcase me-2 text-primary"></i>
                            <?= t('teacher_workload') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach($stats['teacher_load'] as $teacher): ?>
                                <div class="list-group-item px-0 border-0">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span><?= htmlspecialchars($teacher['teacher']) ?></span>
                                        <span class="badge bg-info">
                                            <?= $teacher['subjects_count'] ?> <?= t('subjects') ?>
                                        </span>
                                    </div>
                                    <div class="progress mt-1" style="height: 5px;">
                                        <div class="progress-bar" style="width: <?= min(100, $teacher['classes_per_week'] * 5) ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Changes -->
            <div class="col-xl-3 col-lg-6 mb-4">
                <div class="card h-100">
                    <div class="card-header">
                        <h6 class="card-title mb-0 select">
                            <i class="fas fa-history me-2 text-secondary"></i>
                            <?= t('recent_updates') ?>
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="list-group list-group-flush">
                            <?php foreach($stats['recent_changes'] as $change): ?>
                                <div class="list-group-item px-0 border-0">
                                    <small class="d-block text-truncate"><?= htmlspecialchars($change['student_name']) ?></small>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="badge bg-<?= $change['old_status'] == 'present' ? 'danger' : 'secondary' ?>">
                                            <?= $change['old_status'] ?>
                                        </span>
                                        <i class="fas fa-arrow-right mx-2   "></i>
                                        <span class="badge bg-<?= $change['new_status'] == 'present' ? 'success' : 'warning' ?>">
                                            <?= $change['new_status'] ?>
                                        </span>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline and Distribution -->
     <?php
// Fetch last 5 recent activities
$timeline = [];

// 1. Recent attendance changes
$att_changes = $conn->query("
    SELECT 
        ah.changed_at as date_time,
        CONCAT(s.name, ' attendance updated') as event,
        ah.old_status,
        ah.new_status,
        ah.recorded_by
    FROM attendance_history ah
    JOIN students s ON ah.student_id = s.id
    ORDER BY ah.changed_at DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

foreach($att_changes as $row){
    $timeline[] = [
        'date_time' => $row['date_time'],
        'title' => t('attendance_updated'),
        'description' => t('changed_from_to') . " {$row['old_status']} " . t('to') . " {$row['new_status']} " . t('by') . " {$row['recorded_by']}"
    ];
}

// 2. New students enrolled
$new_students = $conn->query("
    SELECT created_at as date_time, CONCAT(name, ' enrolled') as event
    FROM students
    ORDER BY created_at DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

foreach($new_students as $row){
    $timeline[] = [
        'date_time' => $row['date_time'],
        'title' => t('new_student_enrolled'),
        'description' => $row['event']
    ];
}

// 3. Recent exams published
$recent_exams = $conn->query("
    SELECT start_date as date_time, CONCAT(name, ' exam published') as event
    FROM exams
    ORDER BY start_date DESC
    LIMIT 5
")->fetch_all(MYSQLI_ASSOC);

foreach($recent_exams as $row){
    $timeline[] = [
        'date_time' => $row['date_time'],
        'title' => t('exam_published'),
        'description' => $row['event']
    ];
}

// Sort all events by datetime descending and take top 5
usort($timeline, function($a,$b){
    return strtotime($b['date_time']) - strtotime($a['date_time']);
});

$timeline = array_slice($timeline, 0, 5);
?>
<div class="timeline text text-muted mb-4">
    <?php foreach($timeline as $item): ?>
        <div class="timeline-item">
            <h6 class="mb-1 select"><?= htmlspecialchars($item['title']) ?></h6>
            <small class="select"><?= date('d M Y • H:i', strtotime($item['date_time'])) ?> • <?= htmlspecialchars($item['description']) ?></small>
        </div>
    <?php endforeach; ?>
</div>


        <!-- Detailed Statistics Table -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 select">
                            <i class="fas fa-table me-2"></i>
                            <?= t('detailed_statistics') ?>
                        </h5>
                      
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?= t('category') ?></th>
                                        <th class="text-center"><?= t('total') ?></th>
                                        <th class="text-center"><?= t('active') ?></th>
                                        <th class="text-center"><?= t('inactive') ?></th>
                                        <th class="text-center"><?= t('growth') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><?= t('users') ?></td>
                                        <td class="text-center"><?= $stats['total_users'] ?></td>
                                        <td class="text-center"><?= $stats['active_users'] ?></td>
                                        <td class="text-center"><?= $stats['total_users'] - $stats['active_users'] ?></td>
                                        <td class="text-center">
                                            <span class="text-<?= $user_growth >= 0 ? 'success' : 'danger' ?>">
                                                <?= $user_growth >= 0 ? '+' : '' ?><?= $user_growth ?>%
                                            </span>
                                        </td>
                                      
                                    </tr>

                                    <tr>
                                        <td><?= t('teachers') ?></td>
                                        <td class="text-center"><?= $stats['total_teachers'] ?></td>
                                        <td class="text-center"><?= $stats['active_teachers'] ?></td>
                                        <td class="text-center"><?= $stats['total_teachers'] - $stats['active_teachers'] ?></td>
                                        <td class="text-center">
                                            <span class="text-<?= $teacher_growth >= 0 ? 'success' : 'danger' ?>">
                                                <?= $teacher_growth >= 0 ? '+' : '' ?><?= $teacher_growth ?>%
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= t('students') ?></td>
                                        <td class="text-center"><?= $stats['total_students'] ?></td>
                                        <td class="text-center"><?= $stats['total_students'] ?></td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">
                                            <span class="text-<?= $student_growth >= 0 ? 'success' : 'danger' ?>">
                                                <?= $student_growth >= 0 ? '+' : '' ?><?= $student_growth ?>%
                                            </span>
                                        </td>
                                    </tr>

                                    <tr>
                                        <td><?= t('classes') ?></td>
                                        <td class="text-center"><?= $stats['total_classes'] ?></td>
                                        <td class="text-center"><?= $stats['total_classes'] ?></td>
                                        <td class="text-center">0</td>
                                        <td class="text-center">
                                            <span class="text-<?= $class_growth >= 0 ? 'success' : 'danger' ?>">
                                                <?= $class_growth >= 0 ? '+' : '' ?><?= $class_growth ?>%
                                            </span>
                                        </td>
                                       
                                    </tr>
</tbody>

                            </table>
                            
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    
    <script>
        // Initialize date range picker
        $(function() {
            $('input[name="daterange"]').daterangepicker({
                opens: 'left',
                startDate: moment('<?= $start_date ?>'),
                endDate: moment('<?= $end_date ?>'),
                locale: {
                    format: 'YYYY-MM-DD'
                }
            }, function(start, end) {
                $('input[name="start_date"]').val(start.format('YYYY-MM-DD'));
                $('input[name="end_date"]').val(end.format('YYYY-MM-DD'));
            });
        });

        // Theme toggle
        function toggleTheme() {
            const body = document.body;
            const isDark = body.classList.contains('dark');
            body.classList.toggle('dark');
            body.classList.toggle('light');
            
            // Update theme in database via AJAX
            fetch('update_theme.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    mode: isDark ? 1 : 2
                })
            });
        }

        // Initialize Charts
        document.addEventListener('DOMContentLoaded', function() {
            // Attendance Distribution Chart
            const attendanceCtx = document.getElementById('attendanceChart').getContext('2d');
            const attendanceData = {
                labels: [
                    '<?= t('present') ?>',
                    '<?= t('absent') ?>',
                    '<?= t('late') ?>',
                    '<?= t('excused') ?>'
                ],
                datasets: [{
                    data: [
                        <?= isset($stats['attendance'][0]['count']) ? $stats['attendance'][0]['count'] : 0 ?>,
                        <?= isset($stats['attendance'][1]['count']) ? $stats['attendance'][1]['count'] : 0 ?>,
                        <?= isset($stats['attendance'][2]['count']) ? $stats['attendance'][2]['count'] : 0 ?>,
                        <?= isset($stats['attendance'][3]['count']) ? $stats['attendance'][3]['count'] : 0 ?>
                    ],
                    backgroundColor: [
                        '#10b981',
                        '#ef4444',
                        '#f59e0b',
                        '#3b82f6'
                    ],
                    borderWidth: 2,
                    borderColor: document.body.classList.contains('dark') ? '#1e1e1e' : '#ffffff'
                }]
            };
            
            new Chart(attendanceCtx, {
                type: 'doughnut',
                data: attendanceData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                color: document.body.classList.contains('dark') ? '#e0e0e0' : '#212529',
                                padding: 20
                            }
                        }
                    }
                }
            });

            // Gender Distribution Chart
            const genderCtx = document.getElementById('genderChart').getContext('2d');
            new Chart(genderCtx, {
                type: 'pie',
                data: {
                    labels: [
                        '<?= t('male') ?>',
                        '<?= t('female') ?>',
                        '<?= t('other') ?>'
                    ],
                    datasets: [{
                        data: [
                            <?= isset($stats['student_gender'][0]) ? $stats['student_gender'][0]['count'] : 0 ?>,
                            <?= isset($stats['student_gender'][1]) ? $stats['student_gender'][1]['count'] : 0 ?>,
                            <?= isset($stats['student_gender'][2]) ? $stats['student_gender'][2]['count'] : 0 ?>
                        ],
                        backgroundColor: ['#3b82f6', '#ec4899', '#8b5cf6']
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false
                }
            });

            // Trend Chart
            const trendCtx = document.getElementById('trendChart').getContext('2d');
            const trendData = {
              labels: <?= json_encode(array_column($stats['weekly_attendance'], 'day_name')) ?>,
              datasets: [
              {
                  label: '<?= t('present') ?>',
                  data: <?= json_encode(array_column($stats['weekly_attendance'], 'present')) ?>,
                  borderColor: '#10b981',
                  backgroundColor: 'rgba(16,185,129,0.15)',
                  fill: true
              },
              {
                  label: '<?= t('absent') ?>',
                  data: <?= json_encode(array_column($stats['weekly_attendance'], 'absent')) ?>,
                  borderColor: '#ef4444',
                  backgroundColor: 'rgba(239,68,68,0.15)',
                  fill: true
              },
              {
                  label: '<?= t('late') ?>',
                  data: <?= json_encode(array_column($stats['weekly_attendance'], 'late')) ?>,
                  borderColor: '#f59e0b',
                  backgroundColor: 'rgba(245,158,11,0.15)',
                  fill: true
              }
              ]

            };
            
            new Chart(trendCtx, {
                type: 'line',
                data: trendData,
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    interaction: {
                        mode: 'index',
                        intersect: false
                    },
                    scales: {
                        x: {
                            grid: {
                                color: document.body.classList.contains('dark') ? '#333' : '#e5e7eb'
                            },
                            ticks: {
                                color: document.body.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            }
                        },
                        y: {
                            grid: {
                                color: document.body.classList.contains('dark') ? '#333' : '#e5e7eb'
                            },
                            ticks: {
                                color: document.body.classList.contains('dark') ? '#9ca3af' : '#6b7280'
                            }
                        }
                    }
                }
            });

            // Class Distribution Chart
            const classCtx = document.getElementById('classChart').getContext('2d');
            new Chart(classCtx, {
                type: 'bar',
                data: {
                    labels: <?= json_encode(array_column($stats['class_distribution'], 'class_name')) ?>,
                    datasets: [{
                        label: '<?= t('students') ?>',
                        data: <?= json_encode(array_column($stats['class_distribution'], 'student_count')) ?>,
                        backgroundColor: '#8b5cf6',
                        borderRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: document.body.classList.contains('dark') ? '#333' : '#e5e7eb'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });

            // Sparkline charts
            createSparklines();
        });

        function generateHeatmap(heatmapData) {
            const heatmap = document.getElementById('heatmap');
            heatmap.innerHTML = ''; // clear old

            Object.keys(heatmapData).forEach(dateStr => {
                const dayElement = document.createElement('div');
                dayElement.className = 'heatmap-day d-inline-flex flex-column';
                dayElement.title = dateStr;

                // pick dominant status for color
                const data = heatmapData[dateStr];
                let maxStatus = 'present';
                let maxValue = 0;
                Object.keys(data).forEach(status => {
                    if(data[status] > maxValue){
                        maxValue = data[status];
                        maxStatus = status;
                    }
                });

                dayElement.classList.add('attendance-' + maxStatus);
                const dayNum = new Date(dateStr).getDate();
                dayElement.innerHTML = `<small>${dayNum}</small>`;

                dayElement.addEventListener('click', function() {
                    alert(`Date: ${dateStr}\n<?= t('present') ?>: ${data.present}%\n<?= t('absent') ?>: ${data.absent}%\n<?= t('late') ?>: ${data.late}%\n<?= t('excused') ?>: ${data.excused}%`);
                });

                heatmap.appendChild(dayElement);
            });
        }

        function createSparklines() {
            const sparklines = document.querySelectorAll('.sparkline');
            sparklines.forEach((canvas, index) => {
                const ctx = canvas.getContext('2d');
                const data = Array.from({length: 10}, () => Math.floor(Math.random() * 100));
                
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: data.map((_, i) => i),
                        datasets: [{
                            data: data,
                            borderColor: '#4361ee',
                            borderWidth: 2,
                            fill: false,
                            tension: 0.4,
                            pointRadius: 0
                        }]
                    },
                    options: {
                        responsive: false,
                        maintainAspectRatio: false,
                        plugins: {
                            legend: { display: false }
                        },
                        scales: {
                            x: { display: false },
                            y: { display: false }
                        }
                    }
                });
            });
        }

        function exportToCSV() {
            const data = [];
            const rows = document.querySelectorAll('table tbody tr');
            
            rows.forEach(row => {
                const rowData = [];
                row.querySelectorAll('td').forEach(cell => {
                    rowData.push(cell.textContent.trim());
                });
                data.push(rowData.join(','));
            });
            
            const csvContent = 'data:text/csv;charset=utf-8,' + data.join('\n');
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement('a');
            link.setAttribute('href', encodedUri);
            link.setAttribute('download', 'analytics_report.csv');
            document.body.appendChild(link);
            link.click();
        }

        function exportToJSON() {
            const data = {
                generated: new Date().toISOString(),
                period: {
                    start: '<?= $start_date ?>',
                    end: '<?= $end_date ?>'
                },
                statistics: <?= json_encode($stats) ?>
            };
            
            const jsonContent = 'data:text/json;charset=utf-8,' + encodeURIComponent(JSON.stringify(data, null, 2));
            const link = document.createElement('a');
            link.setAttribute('href', jsonContent);
            link.setAttribute('download', 'analytics_report.json');
            document.body.appendChild(link);
            link.click();
        }

        // Auto-refresh data every 5 minutes
        setInterval(() => {
            if (!document.hidden) {
                fetch('?type=<?= $report_type ?>&start_date=<?= $start_date ?>&end_date=<?= $end_date ?>')
                    .then(response => response.text())
                    .then(html => {
                        // Could implement partial refresh here
                        console.log('Data refresh available');
                    });
            }
        }, 300000);

        
    </script>
  <script>
const heatmapData = <?= json_encode($heatmap_data) ?>;
generateHeatmap(heatmapData);
</script>


</body>
</html><script>
    // Refresh suspension warning every 60 seconds
    setInterval(() => {
        fetch(window.location.href, { cache: "no-store" })
            .then(res => res.text())
            .then(html => {
                const temp = document.createElement('div');
                temp.innerHTML = html;
                const newWarning = temp.querySelector('.teacher-warning');
                const oldWarning = document.querySelector('.teacher-warning');

                if (newWarning && oldWarning) {
                    oldWarning.replaceWith(newWarning);
                }
            });
    }, 60000); // 1 minute
</script>

<style>
    :root {
        --primary-color: #4361ee;
        --secondary-color: #3f37c9;
        --success-color: #4cc9f0;
        --warning-color: #f72585;
        --info-color: #4895ef;
        --light-bg: #f8f9fa;
        --dark-bg: #121212;
        --card-bg-light: #ffffff;
        --card-bg-dark: #1e1e1e;
        --text-light: #212529;
        --text-dark: #e0e0e0;
        --border-light: #dee2e6;
        --border-dark: #333333;
    }

    body.light {
        background-color: var(--light-bg);
        color: #fff;
    }

    body.dark {
        background-color: var(--dark-bg);
        color: var(--text-dark);
    }

    body.dark .card {
        background-color: var(--card-bg-dark);
        border-color: var(--border-dark);
    }

    body.dark .table {
        color: var(--text-dark);
    }

    body.dark .table-hover tbody tr:hover {
        background-color: rgba(255, 255, 255, 0.05);
    }
    /* FORCE ALL TEXT TO WHITE IN DARK MODE */
    body.dark,
    body.dark .select {
        color: #ffffff !important;
    }

    body.dark .form-control {
        background-color: #2d2d2d;
        border-color: #444;
        color: var(--text-dark);
    }

    body.dark .form-control:focus {
        background-color: #333;
        border-color: var(--primary-color);
        color: var(--text-dark);
    }

    .stat-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    body.dark .stat-card {
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    }

    body.dark .stat-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.3);
    }

    .stat-card .icon-wrapper {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        position: absolute;
        top: 20px;
    }

    /* English/LTR specific icon positioning */
    body[dir="ltr"] .stat-card .icon-wrapper {
        right: 20px;
        left: auto;
    }

    /* Arabic/RTL specific icon positioning */
    body[dir="rtl"] .stat-card .icon-wrapper {
        right: auto;
        left: 20px;
    }

    .stat-card .icon-wrapper.users { background: linear-gradient(45deg, #4361ee, #3a0ca3); color: white; }
    .stat-card .icon-wrapper.teachers { background: linear-gradient(45deg, #f72585, #b5179e); color: white; }
    .stat-card .icon-wrapper.students { background: linear-gradient(45deg, #4cc9f0, #4895ef); color: white; }
    .stat-card .icon-wrapper.classes { background: linear-gradient(45deg, #7209b7, #560bad); color: white; }

    .chart-container {
        position: relative;
        height: 300px;
        width: 100%;
        background: white;
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    }

    body.dark .chart-container {
        background: var(--card-bg-dark);
        box-shadow: 0 4px 20px rgba(0,0,0,0.2);
    }

    .heatmap-container {
        height: 250px;
        overflow-x: auto;
    }

    .heatmap-day {
        width: 30px;
        height: 30px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        margin: 2px;
        transition: all 0.2s;
        cursor: pointer;
    }
    body.dark .card-body {
        background-color: #1e1e1e;
    }

    .heatmap-day:hover {
        transform: scale(1.1);
        z-index: 10;
    }

    .attendance-present { background-color: #10b981; color: white; }
    .attendance-absent { background-color: #ef4444; color: white; }
    .attendance-late { background-color: #f59e0b; color: white; }
    .attendance-excused { background-color: #3b82f6; color: white; }

    .timeline {
        position: relative;
        padding-left: 30px;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: linear-gradient(to bottom, var(--primary-color), transparent);
    }

    .timeline-item {
        position: relative;
        margin-bottom: 25px;
        padding-left: 20px;
    }

    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 5px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: var(--primary-color);
        border: 3px solid var(--light-bg);
    }

    body.dark .timeline-item::before {
        border-color: var(--dark-bg);
    }

    .distribution-bar {
        height: 20px;
        border-radius: 10px;
        overflow: hidden;
        background: #e5e7eb;
        margin-bottom: 10px;
    }

    body.dark .distribution-bar {
        background: #374151;
    }

    .distribution-segment {
        height: 100%;
        display: inline-block;
        transition: all 0.3s;
    }

    .distribution-segment:hover {
        filter: brightness(1.2);
    }

    .nav-tabs .nav-link {
        border: none;
        color: #6c757d;
        font-weight: 500;
        padding: 0.75rem 1.5rem;
    }

    .nav-tabs .nav-link.active {
        color: var(--primary-color);
        border-bottom: 3px solid var(--primary-color);
        background: transparent;
    }

    body.dark .nav-tabs .nav-link {
        color: #9ca3af;
    }

    body.dark .nav-tabs .nav-link.active {
        color: var(--primary-color);
    }

    .metric-badge {
        font-size: 0.75rem;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
    }

    .glow-effect {
        position: relative;
        overflow: hidden;
    }

    .glow-effect::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.3s;
    }

    .glow-effect:hover::after {
        opacity: 1;
    }

    .analytics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    @media (max-width: 768px) {
        .analytics-grid {
            grid-template-columns: 1fr;
        }
    }

    .glass-effect {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    body.dark .glass-effect {
        background: rgba(30, 30, 30, 0.9);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .animated-gradient {
        background: linear-gradient(-45deg, #ee7752, #e73c7e, #23a6d5, #23d5ab);
        background-size: 400% 400%;
        animation: gradient 15s ease infinite;
    }

    @keyframes gradient {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .sparkline {
        height: 40px;
        width: 100px;
        margin-left: 10px;
    }
    
</style>
</body>
</html>
