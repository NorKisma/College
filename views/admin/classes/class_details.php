<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Class Details',
        'back_to_classes' => 'Back to Classes',
        'section' => 'Section',
        'created' => 'Created',
        'total_students' => 'Total Students',
        'attendance_rate' => 'Attendance Rate (30 days)',
        'present_of' => 'present of',
        'days' => 'days',
        'alumni_graduated' => 'Alumni Graduated',
        'from_this_class' => 'From this class',
        'subjects' => 'Subjects',
        'taught_in_this_class' => 'Taught in this class',
        'students_tab' => 'Students',
        'subjects_tab' => 'Subjects & Schedule',
        'attendance_tab' => 'Attendance',
        'exams_tab' => 'Exams',
        'performance_tab' => 'Performance',
        'fees_tab' => 'Fees',
        'alumni_tab' => 'Alumni',
        'class_students' => 'Class Students',
        'click_student_for_details' => 'Click student for details',
        'admitted' => 'Admitted',
        'attendance' => 'Attendance',
        'add_students' => 'Add students through the class management system',
        'class_subjects_schedule' => 'Class Subjects & Schedule',
        'classes' => 'classes',
        'all_subjects' => 'All Subjects',
        'subject' => 'Subject',
        'teacher' => 'Teacher',
        'day' => 'Day',
        'time' => 'Time',
        'not_assigned' => 'Not assigned',
        'add_subjects' => 'Add subjects through the timetable management system',
        'class_attendance_records' => 'Class Attendance Records',
        'last_30_days' => 'Last 30 days',
        'date' => 'Date',
        'student' => 'Student',
        'status' => 'Status',
        'remarks' => 'Remarks',
        'recorded_by' => 'Recorded By',
        'time' => 'Time',
        'general' => 'General',
        'attendance_will_appear' => 'Attendance will appear here once recorded',
        'class_exams' => 'Class Exams',
        'recent_exam_performance' => 'Recent Exam Performance',
        'exam' => 'Exam',
        'average_score' => 'Average Score',
        'performance' => 'Performance',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'needs_improvement' => 'Needs Improvement',
        'no_recent_exam_data' => 'No recent exam performance data available',
        'exams_will_appear' => 'Exams will appear here once created',
        'class_fees_structure' => 'Class Fees Structure',
        'total' => 'Total',
        'potential' => 'Potential',
        'fee_category' => 'Fee Category',
        'amount' => 'Amount',
        'month_year' => 'Month/Year',
        'due_date' => 'Due Date',
        'potential_revenue' => 'Potential Revenue',
        'one_time' => 'One-time',
        'not_set' => 'Not set',
        'define_fees' => 'Define class fees through the finance management system',
        'graduated' => 'Graduated',
        'graduation_date' => 'Graduation Date',
        'reason' => 'Reason',
        'exam_score' => 'Exam Score',
        'attendance_percentage' => 'Attendance %',
        'criteria_met' => 'Criteria Met',
        'yes' => 'Yes',
        'partial' => 'Partial',
        'students_will_appear' => 'Students will appear here after graduation',
        'class_performance_analytics' => 'Class Performance Analytics',
        'overall_class_metrics' => 'Overall class metrics',
        'attendance_trend' => 'Attendance Trend',
        'last_30_days' => 'Last 30 days',
        'no_attendance_data' => 'No attendance data available for analysis',
        'student_demographics' => 'Student Demographics',
        'total_students' => 'Total Students',
        'no_student_data' => 'No student data available',
        'recent_exam_performance' => 'Recent Exam Performance',
        'average_exam_score' => 'Average Exam Score',
        'no_recent_exam_data' => 'No recent exam data available',
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'male' => 'Male',
        'female' => 'Female',
        'no_students_enrolled' => 'No students enrolled in this class yet'
    ],
    2 => [ // Arabic - Simple language
        'page_title' => 'تفاصيل الصف',
        'back_to_classes' => 'العودة إلى الصفوف',
        'section' => 'القسم',
        'created' => 'تم الإنشاء',
        'total_students' => 'إجمالي الطلاب',
        'attendance_rate' => 'معدل الحضور (30 يومًا)',
        'present_of' => 'حاضر من',
        'days' => 'يوم',
        'alumni_graduated' => 'الخريجون',
        'from_this_class' => 'من هذا الصف',
        'subjects' => 'المواد',
        'taught_in_this_class' => 'المدرجة في هذا الصف',
        'students_tab' => 'الطلاب',
        'subjects_tab' => 'المواد والجدول',
        'attendance_tab' => 'الحضور',
        'exams_tab' => 'الامتحانات',
        'performance_tab' => 'الأداء',
        'fees_tab' => 'الرسوم',
        'alumni_tab' => 'الخريجون',
        'class_students' => 'طلاب الصف',
        'click_student_for_details' => 'انقر على الطالب للتفاصيل',
        'admitted' => 'تم التسجيل',
        'attendance' => 'الحضور',
        'add_students' => 'أضف الطلاب من خلال نظام إدارة الصفوف',
        'class_subjects_schedule' => 'مواد وجدول الصف',
        'classes' => 'حصص',
        'all_subjects' => 'جميع المواد',
        'subject' => 'المادة',
        'teacher' => 'المعلم',
        'day' => 'اليوم',
        'time' => 'الوقت',
        'not_assigned' => 'غير معين',
        'add_subjects' => 'أضف المواد من خلال نظام الجدول',
        'class_attendance_records' => 'سجلات حضور الصف',
        'last_30_days' => 'آخر 30 يومًا',
        'date' => 'التاريخ',
        'student' => 'الطالب',
        'status' => 'الحالة',
        'remarks' => 'ملاحظات',
        'recorded_by' => 'سجل بواسطة',
        'time' => 'الوقت',
        'general' => 'عام',
        'attendance_will_appear' => 'سيظهر الحضور هنا عند التسجيل',
        'class_exams' => 'امتحانات الصف',
        'recent_exam_performance' => 'أداء الامتحانات الأخيرة',
        'exam' => 'الامتحان',
        'average_score' => 'متوسط النتيجة',
        'performance' => 'الأداء',
        'excellent' => 'ممتاز',
        'good' => 'جيد',
        'needs_improvement' => 'يحتاج تحسين',
        'no_recent_exam_data' => 'لا توجد بيانات حديثة لأداء الامتحانات',
        'exams_will_appear' => 'ستظهر الامتحانات هنا عند إنشائها',
        'class_fees_structure' => 'هيكل رسوم الصف',
        'total' => 'المجموع',
        'potential' => 'المحتمل',
        'fee_category' => 'فئة الرسوم',
        'amount' => 'المبلغ',
        'month_year' => 'الشهر/السنة',
        'due_date' => 'تاريخ الاستحقاق',
        'potential_revenue' => 'الإيراد المحتمل',
        'one_time' => 'مرة واحدة',
        'not_set' => 'غير محدد',
        'define_fees' => 'حدد رسوم الصف من خلال نظام المالية',
        'graduated' => 'خريج',
        'graduation_date' => 'تاريخ التخرج',
        'reason' => 'السبب',
        'exam_score' => 'نتيجة الامتحان',
        'attendance_percentage' => 'نسبة الحضور',
        'criteria_met' => 'تحقق المعايير',
        'yes' => 'نعم',
        'partial' => 'جزئي',
        'students_will_appear' => 'سيظهر الطلاب هنا بعد التخرج',
        'class_performance_analytics' => 'تحليلات أداء الصف',
        'overall_class_metrics' => 'مقاييس الصف الإجمالية',
        'attendance_trend' => 'اتجاه الحضور',
        'last_30_days' => 'آخر 30 يومًا',
        'no_attendance_data' => 'لا توجد بيانات حضور متاحة للتحليل',
        'student_demographics' => 'ديموغرافيا الطلاب',
        'total_students' => 'إجمالي الطلاب',
        'no_student_data' => 'لا توجد بيانات للطلاب',
        'recent_exam_performance' => 'أداء الامتحانات الأخيرة',
        'average_exam_score' => 'متوسط نتيجة الامتحان',
        'no_recent_exam_data' => 'لا توجد بيانات حديثة للامتحانات',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'male' => 'ذكر',
        'female' => 'أنثى',
        'no_students_enrolled' => 'لا يوجد طلاب مسجلين في هذا الصف بعد'
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
 
// Check if user is logged in (admin/teacher)
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}
function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}

// Get theme setting
$theme_mode = 1; // default light
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1"); 
if($themeRes && $themeRes->num_rows > 0){
    $themeRow = $themeRes->fetch_assoc();
    $theme_mode = (int)$themeRow['mode'];
}
$isDark = ($theme_mode === 0);

// Get class ID from URL
$class_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($class_id <= 0) {
    header("Location: classes.php");
    exit();
}

// Get class basic information
$classStmt = $conn->prepare("
    SELECT c.*, s.name as section_name, s.payable as section_payable,
           t.first_name as teacher_first, t.last_name as teacher_last
    FROM classes c
    LEFT JOIN sections s ON c.section_id = s.id
    LEFT JOIN teachers t ON c.id = t.id
    WHERE c.id = ?
");
$classStmt->bind_param("i", $class_id);
$classStmt->execute();
$classResult = $classStmt->get_result();

if ($classResult->num_rows === 0) {
    header("Location: classes.php");
    exit();
}

$class = $classResult->fetch_assoc();
$classStmt->close();

// Function to get class statistics
function getClassStatistics($conn, $class_id) {
    $stats = [];
    
    // Get student count
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM class_students WHERE class_id = ?");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['total_students'] = $result->fetch_assoc()['total'];
    $stmt->close();
    
    // Get male/female count
    $stmt = $conn->prepare("
        SELECT s.gender, COUNT(*) as count
        FROM class_students cs
        JOIN students s ON cs.student_id = s.id
        WHERE cs.class_id = ?
        GROUP BY s.gender
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['male'] = 0;
    $stats['female'] = 0;
    while ($row = $result->fetch_assoc()) {
        if ($row['gender'] == 'male') {
            $stats['male'] = $row['count'];
        } else if ($row['gender'] == 'female') {
            $stats['female'] = $row['count'];
        }
    }
    $stmt->close();
    
    // Get attendance summary
    $stmt = $conn->prepare("
        SELECT 
            COUNT(*) as total_days,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present_days,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent_days,
            SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late_days,
            SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused_days
        FROM attendance a
        WHERE a.class_id = ? AND a.date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = $result->fetch_assoc();
    $stats['attendance'] = $attendance;
    $stmt->close();
    
    // Get recent exam performance
    $stmt = $conn->prepare("
        SELECT 
            e.name as exam_name,
            AVG(er.obtained_marks / es.max_marks * 100) as avg_percentage
        FROM exams e
        JOIN exam_results er ON e.id = er.exam_id
        JOIN exam_subjects es ON e.id = es.exam_id AND er.subject_id = es.subject_id
        WHERE e.class_id = ? AND e.end_date >= DATE_SUB(CURDATE(), INTERVAL 90 DAY)
        GROUP BY e.id
        ORDER BY e.end_date DESC
        LIMIT 3
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $stats['recent_exams'] = [];
    while ($row = $result->fetch_assoc()) {
        $stats['recent_exams'][] = $row;
    }
    $stmt->close();
    
    return $stats;
}

// Function to get class students
function getClassStudents($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT s.*, cs.academic_year,
               (SELECT COUNT(*) FROM attendance WHERE student_id = s.id AND status = 'present') as present_count,
               (SELECT COUNT(*) FROM attendance WHERE student_id = s.id) as total_attendance
        FROM class_students cs
        JOIN students s ON cs.student_id = s.id
        WHERE cs.class_id = ?
        ORDER BY s.name
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $students = [];
    while ($row = $result->fetch_assoc()) {
        $row['attendance_percentage'] = $row['total_attendance'] > 0 
            ? round(($row['present_count'] / $row['total_attendance']) * 100, 2)
            : 0;
        $students[] = $row;
    }
    $stmt->close();
    return $students;
}

// Function to get class subjects and schedule
function getClassSubjects($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT cs.*, sub.name as subject_name, 
               t.first_name as teacher_first, t.last_name as teacher_last,
               css.day_of_week as schedule_day, css.start_time as schedule_start, 
               css.end_time as schedule_end
        FROM class_subjects cs
        JOIN subjects sub ON cs.subject_id = sub.id
        LEFT JOIN teachers t ON cs.teacher_id = t.id
        LEFT JOIN class_subject_schedule css ON cs.id = css.class_subject_id
        WHERE cs.class_id = ?
        ORDER BY cs.day_of_week, cs.start_time
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $subjects = [];
    while ($row = $result->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
    return $subjects;
}

// Function to get class attendance
function getClassAttendance($conn, $class_id, $limit = 20) {
    $stmt = $conn->prepare("
        SELECT a.*, s.name as student_name,
               sub.name as subject_name
        FROM attendance a
        JOIN students s ON a.student_id = s.id
        LEFT JOIN subjects sub ON a.subject_id = sub.id
        WHERE a.class_id = ?
        ORDER BY a.date DESC, a.recorded_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $class_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $attendance = [];
    while ($row = $result->fetch_assoc()) {
        $attendance[] = $row;
    }
    $stmt->close();
    return $attendance;
}

// Function to get class exams
function getClassExams($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT e.*,
               COUNT(DISTINCT er.student_id) as students_taken,
               COUNT(DISTINCT es.subject_id) as total_subjects
        FROM exams e
        LEFT JOIN exam_results er ON e.id = er.exam_id
        LEFT JOIN exam_subjects es ON e.id = es.exam_id
        WHERE e.class_id = ? AND e.is_deleted = 0
        GROUP BY e.id
        ORDER BY e.start_date DESC
    ");
    $stmt->bind_param("i", $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $exams = [];
    while ($row = $result->fetch_assoc()) {
        $exams[] = $row;
    }
    $stmt->close();
    return $exams;
}

// Function to get class fees
function getClassFees($conn, $class_id) {
    $stmt = $conn->prepare("
        SELECT cf.*, fc.name as fee_category,
               (SELECT COUNT(DISTINCT cs.student_id) 
                FROM class_students cs 
                WHERE cs.class_id = ?) as total_students,
               (cf.amount * (SELECT COUNT(DISTINCT cs.student_id) 
                           FROM class_students cs 
                           WHERE cs.class_id = ?)) as potential_revenue
        FROM class_fees cf
        JOIN fee_categories fc ON cf.fee_category_id = fc.id
        WHERE cf.class_id = ?
        ORDER BY cf.year DESC, cf.month DESC
    ");
    $stmt->bind_param("iii", $class_id, $class_id, $class_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $fees = [];
    while ($row = $result->fetch_assoc()) {
        $fees[] = $row;
    }
    $stmt->close();
    return $fees;
}

// Get all data
$class_stats = getClassStatistics($conn, $class_id);
$class_students = getClassStudents($conn, $class_id);
$class_subjects = getClassSubjects($conn, $class_id);
$class_attendance = getClassAttendance($conn, $class_id, 30);
$class_exams = getClassExams($conn, $class_id);
$class_fees = getClassFees($conn, $class_id);

// Get alumni from this class
$alumniStmt = $conn->prepare("
    SELECT a.*, s.name as student_name, ac.min_exam_score, ac.min_attendance_percentage
    FROM alumni a
    JOIN students s ON a.student_id = s.id
    LEFT JOIN alumni_criteria ac ON a.class_id = ac.class_id AND a.section_id = ac.section_id
    WHERE a.class_id = ?
    ORDER BY a.graduation_date DESC
");
$alumniStmt->bind_param("i", $class_id);
$alumniStmt->execute();
$alumniResult = $alumniStmt->get_result();
$class_alumni = [];
while ($row = $alumniResult->fetch_assoc()) {
    $class_alumni[] = $row;
}
$alumniStmt->close();
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($class['name']); ?> - <?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
</head>
<body class="<?php echo $isDark ? 'dark' : 'light'; ?>">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="classes.php" class="back-btn">
                <i class="fas fa-arrow-left"></i> <?= t('back_to_classes') ?>
            </a>
            <h1 class="class-title">
                <i class="fas fa-chalkboard-teacher"></i>
                <?php echo htmlspecialchars($class['name']); ?>
                <?php if ($class['section_name']): ?>
                    <span class="badge badge-primary"><?= t('section') ?>: <?php echo htmlspecialchars($class['section_name']); ?></span>
                <?php endif; ?>
                <span class="badge badge-success"><?= t('created') ?>: <?php echo date('M d, Y', strtotime($class['created_at'])); ?></span>
            </h1>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-value"><?php echo $class_stats['total_students']; ?></div>
                <div class="stat-label"><?= t('total_students') ?></div>
                <div style="margin-top: 10px; font-size: 0.9em;">
                    <span>♂ <?php echo $class_stats['male']; ?></span> | 
                    <span>♀ <?php echo $class_stats['female']; ?></span>
                </div>
            </div>
            
            <div class="stat-card blue">
                <div class="stat-icon"><i class="fas fa-calendar-check"></i></div>
                <div class="stat-value">
                    <?php if ($class_stats['attendance']['total_days'] > 0): ?>
                        <?php echo round(($class_stats['attendance']['present_days'] / $class_stats['attendance']['total_days']) * 100, 1); ?>%
                    <?php else: ?>
                        0%
                    <?php endif; ?>
                </div>
                <div class="stat-label"><?= t('attendance_rate') ?></div>
                <div style="margin-top: 10px; font-size: 0.85em;">
                    <?php echo $class_stats['attendance']['present_days']; ?> <?= t('present_of') ?> <?php echo $class_stats['attendance']['total_days']; ?> <?= t('days') ?>
                </div>
            </div>
            
            <div class="stat-card green">
                <div class="stat-icon"><i class="fas fa-graduation-cap"></i></div>
                <div class="stat-value"><?php echo count($class_alumni); ?></div>
                <div class="stat-label"><?= t('alumni_graduated') ?></div>
                <div style="margin-top: 10px; font-size: 0.85em;">
                    <?= t('from_this_class') ?>
                </div>
            </div>
            
            <div class="stat-card purple">
                <div class="stat-icon"><i class="fas fa-book-open"></i></div>
                <div class="stat-value"><?php echo count($class_subjects); ?></div>
                <div class="stat-label"><?= t('subjects') ?></div>
                <div style="margin-top: 10px; font-size: 0.85em;">
                    <?= t('taught_in_this_class') ?>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
    <button class="tab-btn active" onclick="showTab('students')">
        <i class="fas fa-users"></i> <?= t('students_tab') ?>
    </button>

    <button class="tab-btn" onclick="showTab('subjects')">
        <i class="fas fa-book"></i> <?= t('subjects_tab') ?>
    </button>

    <?php if(!is_finance()): ?>
        <button class="tab-btn" onclick="showTab('attendance')">
            <i class="fas fa-calendar-alt"></i> <?= t('attendance_tab') ?>
        </button>

        <button class="tab-btn" onclick="showTab('exams')">
            <i class="fas fa-file-alt"></i> <?= t('exams_tab') ?>
        </button>

        <button class="tab-btn" onclick="showTab('performance')">
            <i class="fas fa-chart-line"></i> <?= t('performance_tab') ?>
        </button>
    <?php endif; ?>

    <button class="tab-btn" onclick="showTab('fees')">
        <i class="fas fa-money-bill-wave"></i> <?= t('fees_tab') ?>
    </button>

    <button class="tab-btn" onclick="showTab('alumni')">
        <i class="fas fa-graduation-cap"></i> <?= t('alumni_tab') ?>
    </button>
</div>


        <!-- Students Tab -->
        <div id="students-tab" class="tab-content active">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_students') ?> (<?php echo count($class_students); ?>)</h3>
                    <span style="color: var(--secondary-color); font-size: 0.9em;">
                        <i class="fas fa-info-circle"></i> <?= t('click_student_for_details') ?>
                    </span>
                </div>
                
                <?php if (!empty($class_students)): ?>
                    <div class="student-grid">
                        <?php foreach ($class_students as $student): ?>
                            <div class="student-card">
                                <div class="student-header">
                                    <div class="student-avatar">
                                        <?php echo strtoupper(substr($student['name'], 0, 2)); ?>
                                    </div>
                                    <div class="student-info">
                                        <h4><?php echo htmlspecialchars($student['name']); ?></h4>
                                        <p>ID: <?php echo $student['id']; ?></p>
                                        <p><?= t('admitted') ?>: <?php echo date('M d, Y', strtotime($student['admission_date'])); ?></p>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 15px;">
                                    <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                        <span><?= t('attendance') ?></span>
                                        <span><?php echo $student['attendance_percentage']; ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill progress-green" 
                                             style="width: <?php echo min($student['attendance_percentage'], 100); ?>%"></div>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 15px; display: flex; gap: 10px; color: var(--secondary-color); font-size: 0.9em;">
                                    <span><i class="fas fa-<?php echo $student['gender'] == 'male' ? 'mars' : 'venus'; ?>"></i> <?php echo $student['gender'] == 'male' ? t('male') : t('female'); ?></span>
                                    <span>|</span>
                                    <span><i class="fas fa-phone"></i> <?php echo htmlspecialchars($student['guardian_phone']); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-user-slash"></i></div>
                        <div class="empty-text"><?= t('no_students_enrolled') ?></div>
                        <p><?= t('add_students') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Subjects Tab -->
        <div id="subjects-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_subjects_schedule') ?></h3>
                    <span class="badge badge-primary"><?php echo count($class_subjects); ?> <?= t('subjects') ?></span>
                </div>
                
                <?php if (!empty($class_subjects)): ?>
                    <!-- Group subjects by day -->
                    <?php 
                    $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                    $subjectsByDay = [];
                    foreach ($class_subjects as $subject) {
                        $day = $subject['day_of_week'];
                        if (!isset($subjectsByDay[$day])) {
                            $subjectsByDay[$day] = [];
                        }
                        $subjectsByDay[$day][] = $subject;
                    }
                    ?>
                    
                    <div class="schedule-grid">
                        <?php foreach ($days as $day): ?>
                            <?php if (isset($subjectsByDay[$day])): ?>
                                <div class="day-card">
                                    <h4 class="day-title">
                                        <i class="fas fa-calendar-day"></i> <?php echo $day; ?>
                                        <span class="badge badge-info"><?php echo count($subjectsByDay[$day]); ?> <?= t('classes') ?></span>
                                    </h4>
                                    
                                    <?php 
                                    // Sort by start time
                                    usort($subjectsByDay[$day], function($a, $b) {
                                        return strtotime($a['start_time']) - strtotime($b['start_time']);
                                    });
                                    ?>
                                    
                                    <?php foreach ($subjectsByDay[$day] as $subject): ?>
                                        <div class="schedule-item">
                                            <div class="schedule-time">
                                            <i class="far fa-clock"></i>
                                            <?php echo htmlspecialchars($subject['start_time']); ?> - 
                                            <?php echo htmlspecialchars($subject['end_time']); ?>
                                        </div>

                                            <div class="schedule-subject">
                                                <i class="fas fa-book"></i> <?php echo htmlspecialchars($subject['subject_name']); ?>
                                            </div>
                                            <?php if ($subject['teacher_first']): ?>
                                                <div class="schedule-teacher">
                                                    <i class="fas fa-chalkboard-teacher"></i> 
                                                    <?php echo htmlspecialchars($subject['teacher_first'] . ' ' . $subject['teacher_last']); ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Subjects Summary -->
                    <div style="margin-top: 30px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 15px;"><?= t('all_subjects') ?></h4>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><?= t('subject') ?></th>
                                        <th><?= t('teacher') ?></th>
                                        <th><?= t('day') ?></th>
                                        <th><?= t('time') ?></th>
                                        <th><?= t('created') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_subjects as $subject): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($subject['subject_name']); ?></td>
                                            <td>
                                                <?php if ($subject['teacher_first']): ?>
                                                    <?php echo htmlspecialchars($subject['teacher_first'] . ' ' . $subject['teacher_last']); ?>
                                                <?php else: ?>
                                                    <span style="color: var(--secondary-color);"><?= t('not_assigned') ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($subject['day_of_week']); ?></td>
                                            <td>
                                                <?php echo date('h:i A', strtotime($subject['start_time'])); ?> - 
                                                <?php echo date('h:i A', strtotime($subject['end_time'])); ?>
                                            </td>
                                            <td><?php echo date('M d, Y', strtotime($subject['created_at'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-book"></i></div>
                        <div class="empty-text">No subjects scheduled for this class.</div>
                        <p><?= t('add_subjects') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Attendance Tab -->
        <div id="attendance-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_attendance_records') ?></h3>
                    <span style="color: var(--secondary-color);">
                        <?= t('last_30_days') ?>
                    </span>
                </div>
                
                <?php if (!empty($class_attendance)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><?= t('date') ?></th>
                                    <th><?= t('student') ?></th>
                                    <th><?= t('status') ?></th>
                                    <th><?= t('subject') ?></th>
                                    <th><?= t('remarks') ?></th>
                                    <th><?= t('recorded_by') ?></th>
                                    <th><?= t('time') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($class_attendance as $record): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo date('M d, Y', strtotime($record['date'])); ?></strong>
                                            <div style="font-size: 0.85em; color: var(--secondary-color);">
                                                <?php echo date('l', strtotime($record['date'])); ?>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($record['student_name']); ?></td>
                                        <td>
                                            <span class="attendance-dot dot-<?php echo $record['status']; ?>"></span>
                                            <span class="badge badge-<?php 
                                                switch($record['status']) {
                                                    case 'present': echo 'success'; break;
                                                    case 'absent': echo 'danger'; break;
                                                    case 'late': echo 'warning'; break;
                                                    case 'excused': echo 'info'; break;
                                                    default: echo 'secondary';
                                                }
                                            ?>">
                                                <?php echo ucfirst($record['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($record['subject_name']): ?>
                                                <?php echo htmlspecialchars($record['subject_name']); ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);"><?= t('general') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($record['remarks'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($record['recorded_by'] ?? 'N/A'); ?></td>
                                        <td><?php echo date('h:i A', strtotime($record['recorded_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-calendar-times"></i></div>
                        <div class="empty-text">No attendance records found for this class.</div>
                        <p><?= t('attendance_will_appear') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Exams Tab -->
        <div id="exams-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_exams') ?></h3>
                    <span class="badge badge-primary"><?php echo count($class_exams); ?> <?= t('exams') ?></span>
                </div>
                
                <?php if (!empty($class_exams)): ?>
                    <div style="margin-bottom: 30px;">
                        <?php foreach ($class_exams as $exam): ?>
                            <div class="exam-card">
                                <div class="exam-header">
                                    <div>
                                        <h4 class="exam-title"><?php echo htmlspecialchars($exam['name']); ?></h4>
                                        <div style="color: var(--secondary-color); font-size: 0.9em; margin-top: 5px;">
                                            <i class="fas fa-calendar-alt"></i> 
                                            <?php echo date('M d, Y', strtotime($exam['start_date'])); ?> - 
                                            <?php echo date('M d, Y', strtotime($exam['end_date'])); ?>
                                            <span style="margin: 0 10px;">•</span>
                                            <i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($exam['exam_type']); ?>
                                            <span style="margin: 0 10px;">•</span>
                                            <i class="fas fa-users"></i> <?php echo $exam['students_taken']; ?> <?= t('students') ?>
                                            <span style="margin: 0 10px;">•</span>
                                            <i class="fas fa-book"></i> <?php echo $exam['total_subjects']; ?> <?= t('subjects') ?>
                                        </div>
                                    </div>
                                    <div>
                                        <?php 
                                        $status = strtolower($exam['status']);
                                        $status_class = 'status-' . $status;
                                        ?>
                                        <span class="exam-status <?php echo $status_class; ?>">
                                            <?php echo ucfirst($status); ?>
                                        </span>
                                    </div>
                                </div>
                                
                                <div style="margin-top: 15px; display: flex; gap: 20px;">
                                    <div>
                                        <div style="font-size: 0.9em; color: var(--secondary-color);"><?= t('academic_year') ?></div>
                                        <div style="font-weight: 600;"><?php echo htmlspecialchars($exam['academic_year']); ?></div>
                                    </div>
                                    <div>
                                        <div style="font-size: 0.9em; color: var(--secondary-color);"><?= t('created') ?></div>
                                        <div style="font-weight: 600;"><?php echo date('M d, Y', strtotime($exam['created_at'])); ?></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Exam Summary -->
                    <h4 style="color: var(--primary-color); margin-bottom: 15px;"><?= t('recent_exam_performance') ?></h4>
                    <?php if (!empty($class_stats['recent_exams'])): ?>
                        <div class="table-container">
                            <table>
                                <thead>
                                    <tr>
                                        <th><?= t('exam') ?></th>
                                        <th><?= t('average_score') ?></th>
                                        <th><?= t('performance') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($class_stats['recent_exams'] as $exam): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($exam['exam_name']); ?></td>
                                            <td>
                                                <strong><?php echo round($exam['avg_percentage'], 1); ?>%</strong>
                                            </td>
                                            <td>
                                                <div class="progress-bar" style="width: 200px; display: inline-block; margin-right: 10px;">
                                                    <div class="progress-fill 
                                                        <?php 
                                                        if ($exam['avg_percentage'] >= 80) echo 'progress-green';
                                                        elseif ($exam['avg_percentage'] >= 60) echo 'progress-yellow';
                                                        else echo 'progress-red';
                                                        ?>" 
                                                        style="width: <?php echo min($exam['avg_percentage'], 100); ?>%">
                                                    </div>
                                                </div>
                                                <?php 
                                                if ($exam['avg_percentage'] >= 80) echo t('excellent');
                                                elseif ($exam['avg_percentage'] >= 60) echo t('good');
                                                else echo t('needs_improvement');
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <p style="color: var(--secondary-color);"><?= t('no_recent_exam_data') ?></p>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-file-alt"></i></div>
                        <div class="empty-text">No exams scheduled for this class.</div>
                        <p><?= t('exams_will_appear') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Fees Tab -->
        <div id="fees-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_fees_structure') ?></h3>
                    <?php if (!empty($class_fees)): ?>
                        <?php 
                        $total_fees = 0;
                        $total_potential = 0;
                        foreach ($class_fees as $fee) {
                            $total_fees += $fee['amount'];
                            $total_potential += $fee['potential_revenue'];
                        }
                        ?>
                        <div>
                            <span class="badge badge-success"><?= t('total') ?>: $<?php echo number_format($total_fees, 2); ?></span>
                            <span class="badge badge-info" style="margin-left: 10px;"><?= t('potential') ?>: $<?php echo number_format($total_potential, 2); ?></span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <?php if (!empty($class_fees)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><?= t('fee_category') ?></th>
                                    <th><?= t('amount') ?></th>
                                    <th><?= t('month_year') ?></th>
                                    <th><?= t('due_date') ?></th>
                                    <th><?= t('students') ?></th>
                                    <th><?= t('potential_revenue') ?></th>
                                    <th><?= t('created') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($class_fees as $fee): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($fee['fee_category']); ?></td>
                                        <td style="font-weight: 600;">$<?php echo number_format($fee['amount'], 2); ?></td>
                                        <td>
                                            <?php if ($fee['month']): ?>
                                                <?php echo date('F', mktime(0, 0, 0, $fee['month'], 1)); ?> <?php echo $fee['year']; ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);"><?= t('one_time') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($fee['due_date']): ?>
                                                <?php echo date('M d, Y', strtotime($fee['due_date'])); ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);"><?= t('not_set') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo $fee['total_students']; ?></td>
                                        <td style="font-weight: 600; color: var(--success-color);">$<?php echo number_format($fee['potential_revenue'], 2); ?></td>
                                        <td><?php echo date('M d, Y', strtotime($fee['created_at'])); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-money-bill-wave"></i></div>
                        <div class="empty-text">No fee structure defined for this class.</div>
                        <p><?= t('define_fees') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Alumni Tab -->
        <div id="alumni-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_alumni') ?></h3>
                    <span class="badge badge-success"><?php echo count($class_alumni); ?> <?= t('graduated') ?></span>
                </div>
                
                <?php if (!empty($class_alumni)): ?>
                    <div class="table-container">
                        <table>
                            <thead>
                                <tr>
                                    <th><?= t('student') ?></th>
                                    <th><?= t('graduation_date') ?></th>
                                    <th><?= t('reason') ?></th>
                                    <th><?= t('exam_score') ?></th>
                                    <th><?= t('attendance_percentage') ?></th>
                                    <th><?= t('criteria_met') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($class_alumni as $alumni): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($alumni['student_name']); ?></strong>
                                        </td>
                                        <td>
                                            <?php echo date('M d, Y', strtotime($alumni['graduation_date'])); ?>
                                        </td>
                                        <td>
                                            <span class="badge badge-info">
                                                <?php echo str_replace('_', ' ', ucfirst($alumni['graduation_reason'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($alumni['exam_score']): ?>
                                                <?php echo $alumni['exam_score']; ?> 
                                                <?php if ($alumni['min_exam_score']): ?>
                                                    / <?php echo $alumni['min_exam_score']; ?>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($alumni['attendance_percentage']): ?>
                                                <?php echo $alumni['attendance_percentage']; ?>%
                                                <?php if ($alumni['min_attendance_percentage']): ?>
                                                    / <?php echo $alumni['min_attendance_percentage']; ?>%
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php 
                                            $criteria_met = true;
                                            if ($alumni['min_exam_score'] && $alumni['exam_score'] < $alumni['min_exam_score']) {
                                                $criteria_met = false;
                                            }
                                            if ($alumni['min_attendance_percentage'] && $alumni['attendance_percentage'] < $alumni['min_attendance_percentage']) {
                                                $criteria_met = false;
                                            }
                                            ?>
                                            <span class="badge <?php echo $criteria_met ? 'badge-success' : 'badge-warning'; ?>">
                                                <?php echo $criteria_met ? t('yes') : t('partial'); ?>
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-graduation-cap"></i></div>
                        <div class="empty-text">No alumni from this class yet.</div>
                        <p><?= t('students_will_appear') ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Performance Tab -->
        <div id="performance-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><?= t('class_performance_analytics') ?></h3>
                    <span style="color: var(--secondary-color);"><?= t('overall_class_metrics') ?></span>
                </div>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
                    <!-- Attendance Performance -->
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 15px;">
                            <i class="fas fa-calendar-check"></i> <?= t('attendance_trend') ?>
                        </h4>
                        <?php if ($class_stats['attendance']['total_days'] > 0): ?>
                            <?php 
                            $attendance_rate = ($class_stats['attendance']['present_days'] / $class_stats['attendance']['total_days']) * 100;
                            ?>
                            <div style="text-align: center;">
                                <div style="font-size: 3em; font-weight: bold; color: 
                                    <?php 
                                    if ($attendance_rate >= 90) echo 'var(--success-color)';
                                    elseif ($attendance_rate >= 80) echo 'var(--warning-color)';
                                    else echo 'var(--danger-color)';
                                    ?>;">
                                    <?php echo round($attendance_rate, 1); ?>%
                                </div>
                                <div style="color: var(--secondary-color); margin-top: 10px;">
                                    <?= t('last_30_days') ?>
                                </div>
                            </div>
                            <div style="margin-top: 20px;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><?= t('present') ?></span>
                                    <span><?php echo $class_stats['attendance']['present_days']; ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-green" 
                                         style="width: <?php echo ($class_stats['attendance']['present_days'] / $class_stats['attendance']['total_days']) * 100; ?>%"></div>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; margin: 15px 0 5px 0;">
                                    <span><?= t('absent') ?></span>
                                    <span><?php echo $class_stats['attendance']['absent_days']; ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-red" 
                                         style="width: <?php echo ($class_stats['attendance']['absent_days'] / $class_stats['attendance']['total_days']) * 100; ?>%"></div>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; margin: 15px 0 5px 0;">
                                    <span><?= t('late') ?></span>
                                    <span><?php echo $class_stats['attendance']['late_days']; ?></span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-yellow" 
                                         style="width: <?php echo ($class_stats['attendance']['late_days'] / $class_stats['attendance']['total_days']) * 100; ?>%"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="color: var(--secondary-color); text-align: center; padding: 30px;">
                                <?= t('no_attendance_data') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Student Demographics -->
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 15px;">
                            <i class="fas fa-users"></i> <?= t('student_demographics') ?>
                        </h4>
                        <?php if ($class_stats['total_students'] > 0): ?>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="font-size: 3em; font-weight: bold; color: var(--primary-color);">
                                    <?php echo $class_stats['total_students']; ?>
                                </div>
                                <div style="color: var(--secondary-color);"><?= t('total_students') ?></div>
                            </div>
                            
                            <div>
                                <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                    <span><?= t('male') ?></span>
                                    <span>
                                        <?php echo $class_stats['male']; ?> 
                                        (<?php echo $class_stats['total_students'] > 0 ? round(($class_stats['male'] / $class_stats['total_students']) * 100, 1) : 0; ?>%)
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-blue" 
                                         style="width: <?php echo $class_stats['total_students'] > 0 ? ($class_stats['male'] / $class_stats['total_students']) * 100 : 0; ?>%"></div>
                                </div>
                                
                                <div style="display: flex; justify-content: space-between; margin: 15px 0 5px 0;">
                                    <span><?= t('female') ?></span>
                                    <span>
                                        <?php echo $class_stats['female']; ?> 
                                        (<?php echo $class_stats['total_students'] > 0 ? round(($class_stats['female'] / $class_stats['total_students']) * 100, 1) : 0; ?>%)
                                    </span>
                                </div>
                                <div class="progress-bar">
                                    <div class="progress-fill progress-green" 
                                         style="width: <?php echo $class_stats['total_students'] > 0 ? ($class_stats['female'] / $class_stats['total_students']) * 100 : 0; ?>%"></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <p style="color: var(--secondary-color); text-align: center; padding: 30px;">
                                <?= t('no_student_data') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Academic Performance -->
                    <div>
                        <h4 style="color: var(--primary-color); margin-bottom: 15px;">
                            <i class="fas fa-chart-line"></i> <?= t('recent_exam_performance') ?>
                        </h4>
                        <?php if (!empty($class_stats['recent_exams'])): ?>
                            <?php 
                            $avg_performance = 0;
                            foreach ($class_stats['recent_exams'] as $exam) {
                                $avg_performance += $exam['avg_percentage'];
                            }
                            $avg_performance = count($class_stats['recent_exams']) > 0 ? $avg_performance / count($class_stats['recent_exams']) : 0;
                            ?>
                            <div style="text-align: center; margin-bottom: 20px;">
                                <div style="font-size: 3em; font-weight: bold; color: 
                                    <?php 
                                    if ($avg_performance >= 80) echo 'var(--success-color)';
                                    elseif ($avg_performance >= 60) echo 'var(--warning-color)';
                                    else echo 'var(--danger-color)';
                                    ?>;">
                                    <?php echo round($avg_performance, 1); ?>%
                                </div>
                                <div style="color: var(--secondary-color);"><?= t('average_exam_score') ?></div>
                            </div>
                            
                            <div>
                                <?php foreach ($class_stats['recent_exams'] as $exam): ?>
                                    <div style="margin-bottom: 15px;">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="font-size: 0.9em;"><?php echo htmlspecialchars($exam['exam_name']); ?></span>
                                            <span><?php echo round($exam['avg_percentage'], 1); ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill 
                                                <?php 
                                                if ($exam['avg_percentage'] >= 80) echo 'progress-green';
                                                elseif ($exam['avg_percentage'] >= 60) echo 'progress-yellow';
                                                else echo 'progress-red';
                                                ?>" 
                                                style="width: <?php echo min($exam['avg_percentage'], 100); ?>%">
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <p style="color: var(--secondary-color); text-align: center; padding: 30px;">
                                <?= t('no_recent_exam_data') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tab switching functionality
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.remove('active');
            });
            
            // Remove active class from all buttons
            document.querySelectorAll('.tab-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Activate clicked button
            event.target.classList.add('active');
            
            // Update URL without reloading page
            const url = new URL(window.location);
            url.searchParams.set('tab', tabName);
            window.history.pushState({}, '', url);
        }
        
        // Check for tab parameter in URL on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                showTabByName(tabParam);
            }
        });
        
        function showTabByName(tabName) {
            const tab = document.getElementById(tabName + '-tab');
            const button = document.querySelector(`.tab-btn[onclick*="${tabName}"]`);
            if (tab && button) {
                // Hide all tabs
                document.querySelectorAll('.tab-content').forEach(tab => {
                    tab.classList.remove('active');
                });
                
                // Remove active class from all buttons
                document.querySelectorAll('.tab-btn').forEach(btn => {
                    btn.classList.remove('active');
                });
                
                // Show selected tab
                tab.classList.add('active');
                button.classList.add('active');
            }
        }
        
        // Add tooltip functionality
        document.querySelectorAll('[data-tooltip]').forEach(element => {
            element.addEventListener('mouseenter', function(e) {
                const tooltip = document.createElement('div');
                tooltip.className = 'tooltip';
                tooltip.textContent = this.getAttribute('data-tooltip');
                tooltip.style.position = 'absolute';
                tooltip.style.background = 'var(--gray-700)';
                tooltip.style.color = 'white';
                tooltip.style.padding = '8px 12px';
                tooltip.style.borderRadius = '6px';
                tooltip.style.fontSize = '0.9em';
                tooltip.style.zIndex = '1000';
                tooltip.style.maxWidth = '250px';
                tooltip.style.whiteSpace = 'nowrap';
                
                document.body.appendChild(tooltip);
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + 'px';
                tooltip.style.top = (rect.top - tooltip.offsetHeight - 5) + 'px';
                
                this.tooltipElement = tooltip;
            });
            
            element.addEventListener('mouseleave', function() {
                if (this.tooltipElement) {
                    this.tooltipElement.remove();
                }
            });
        });
        
        // Auto-refresh attendance data every 5 minutes
        setInterval(() => {
            const activeTab = document.querySelector('.tab-content.active').id;
            if (activeTab === 'attendance-tab') {
                // You can add AJAX here to refresh only attendance data
                // location.reload(); // Simple reload for now
            }
        }, 300000); // 5 minutes
    </script>
</body>
</html>

  <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #333;
            --card-bg: #ffffff;
            --border-color: #dee2e6;
            --primary-color: #4f46e5;
            --secondary-color: #6b7280;
            --success-color: #10b981;
            --danger-color: #ef4444;
            --warning-color: #f59e0b;
            --info-color: #3b82f6;
            --indigo: #4f46e5;
            --purple: #8b5cf6;
            --pink: #ec4899;
            --gray-100: #f3f4f6;
            --gray-200: #e5e7eb;
            --gray-300: #d1d5db;
            --gray-700: #374151;
        }

        .dark {
            --bg-color: #111827;
            --text-color: #f9fafb;
            --card-bg: #1f2937;
            --border-color: #374151;
            --primary-color: #6366f1;
            --secondary-color: #9ca3af;
            --success-color: #34d399;
            --danger-color: #f87171;
            --warning-color: #fbbf24;
            --info-color: #60a5fa;
            --indigo: #818cf8;
            --purple: #a78bfa;
            --pink: #f472b6;
            --gray-100: #1f2937;
            --gray-200: #374151;
            --gray-300: #4b5563;
            --gray-700: #d1d5db;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            margin: 0;
            padding: 0;
            transition: all 0.3s ease;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--primary-color);
        }

        .back-btn {
            background-color: var(--secondary-color);
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background-color: var(--gray-700);
            transform: translateX(-3px);
        }

        .class-title {
            color: var(--primary-color);
            margin: 0;
            font-size: 2em;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            margin-left: 10px;
        }

        .badge-primary { background-color: var(--primary-color); color: white; }
        .badge-success { background-color: var(--success-color); color: white; }
        .badge-warning { background-color: var(--warning-color); color: black; }
        .badge-danger { background-color: var(--danger-color); color: white; }
        .badge-info { background-color: var(--info-color); color: white; }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: linear-gradient(135deg, var(--primary-color), var(--indigo));
            color: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s, box-shadow 0.3s;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
        }

        .stat-card.blue { background: linear-gradient(135deg, var(--info-color), #3b82f6); }
        .stat-card.green { background: linear-gradient(135deg, var(--success-color), #10b981); }
        .stat-card.orange { background: linear-gradient(135deg, var(--warning-color), #f59e0b); }
        .stat-card.red { background: linear-gradient(135deg, var(--danger-color), #ef4444); }
        .stat-card.purple { background: linear-gradient(135deg, var(--purple), #8b5cf6); }

        .stat-icon {
            font-size: 2.5em;
            margin-bottom: 15px;
            opacity: 0.9;
        }

        .stat-value {
            font-size: 2.2em;
            font-weight: bold;
            margin: 10px 0;
        }

        .stat-label {
            font-size: 1em;
            opacity: 0.9;
        }

        .nav-tabs {
            display: flex;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 25px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .tab-btn {
            padding: 15px 25px;
            background: none;
            border: none;
            color: var(--text-color);
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            border-bottom: 3px solid transparent;
            margin-bottom: -2px;
            white-space: nowrap;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .tab-btn:hover {
            color: var(--primary-color);
            background-color: rgba(79, 70, 229, 0.1);
        }

        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: rgba(79, 70, 229, 0.05);
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .tab-content.active {
            display: block;
        }

        .section-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .section-title {
            color: var(--primary-color);
            margin-top: 0;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .section-title h3 {
            margin: 0;
            font-size: 1.4em;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 15px;
            text-align: left;
            position: sticky;
            top: 0;
        }

        td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
        }

        tr:hover {
            background-color: rgba(79, 70, 229, 0.05);
        }

        .attendance-dot {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }

        .dot-present { background-color: var(--success-color); }
        .dot-absent { background-color: var(--danger-color); }
        .dot-late { background-color: var(--warning-color); }
        .dot-excused { background-color: var(--info-color); }

        .progress-bar {
            height: 8px;
            background-color: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
            margin: 5px 0;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-green { background-color: var(--success-color); }
        .progress-yellow { background-color: var(--warning-color); }
        .progress-red { background-color: var(--danger-color); }
        .progress-blue { background-color: var(--info-color); }

        .schedule-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }

        .day-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
        }

        .day-title {
            color: var(--primary-color);
            margin-top: 0;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .schedule-item {
            padding: 15px;
            margin: 10px 0;
            border-left: 4px solid var(--primary-color);
            background-color: rgba(79, 70, 229, 0.05);
            border-radius: 0 8px 8px 0;
        }

        .schedule-time {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 5px;
        }

        .schedule-subject {
            font-weight: 600;
            margin-bottom: 5px;
        }

        .schedule-teacher {
            color: var(--secondary-color);
            font-size: 0.9em;
        }

        .exam-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .exam-card:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .exam-title {
            color: var(--primary-color);
            font-size: 1.2em;
            margin: 0;
        }

        .exam-status {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
        }

        .status-active { background-color: rgba(16, 185, 129, 0.2); color: var(--success-color); }
        .status-upcoming { background-color: rgba(245, 158, 11, 0.2); color: var(--warning-color); }
        .status-completed { background-color: rgba(59, 130, 246, 0.2); color: var(--info-color); }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }

        .empty-icon {
            font-size: 4em;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-text {
            font-size: 1.2em;
            margin-bottom: 10px;
        }

        .student-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .student-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
        }

        .student-card:hover {
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
            transform: translateY(-5px);
        }

        .student-header {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }

        .student-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5em;
            font-weight: bold;
            margin-right: 15px;
        }

        .student-info h4 {
            margin: 0 0 5px 0;
            color: var(--text-color);
        }

        .student-info p {
            margin: 0;
            color: var(--secondary-color);
            font-size: 0.9em;
        }

        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .student-grid {
                grid-template-columns: 1fr;
            }
            
            .tab-btn {
                padding: 12px 15px;
                font-size: 14px;
            }
            
            th, td {
                padding: 10px;
            }
        }
    </style>