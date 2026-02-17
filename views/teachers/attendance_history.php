<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$msg = '';
$success = false;

/* ===================== TRANSLATIONS ===================== */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Attendance History | Class Management',
        'dashboard_title' => 'Attendance History',
        'dashboard_subtitle' => 'Track and manage class attendance records',
        'back_to_classes' => 'Back to Classes',
        'success_message' => 'Attendance record deleted successfully.',
        'error_message' => 'Failed to delete attendance: ',
        'record_not_found' => 'Record not found.',
        'invalid_record' => 'Invalid record.',
        'present_students' => 'Present Students',
        'absent_students' => 'Absent Students',
        'late_arrivals' => 'Late Arrivals',
        'excused_absences' => 'Excused Absences',
        'overall_rate' => 'Overall Rate',
        'rate' => 'Rate',
        'filter_records' => 'Filter Attendance Records',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'subject' => 'Subject',
        'all_subjects' => 'All Subjects',
        'recorded_by' => 'Recorded By',
        'all_users' => 'All Users',
        'apply_filters' => 'Apply Filters',
        'reset_filters' => 'Reset Filters',
        'daily_attendance_rate' => 'Daily Attendance Rate',
        'attendance' => 'Attendance',
        'subject_performance' => 'Subject Performance',
        'no_records_found' => 'No Attendance Records Found',
        'no_records_message' => 'No attendance records match your current filters.',
        'date' => 'Date',
        'recorded_by_col' => 'Recorded By',
        'attendance_col' => 'Attendance',
        'rate_col' => 'Rate',
        'actions' => 'Actions',
        'students' => 'students',
        'total' => 'Total',
        'view_details' => 'View Details',
        'delete_record' => 'Delete Record',
        'delete_confirm' => 'Are you sure you want to delete this attendance record? This action cannot be undone.',
        'export_pdf' => 'Export to PDF',
        'export_excel' => 'Export to Excel',
        'print_report' => 'Print Report',
        'attendance_history' => 'Attendance History',
        'view' => 'View',
        'delete' => 'Delete'
    ],
    2 => [ // Arabic
        'page_title' => 'سجل الحضور | إدارة الفصول',
        'dashboard_title' => 'سجل الحضور',
        'dashboard_subtitle' => 'تتبع وإدارة سجلات حضور الفصل',
        'back_to_classes' => 'العودة إلى الفصول',
        'success_message' => 'تم حذف سجل الحضور بنجاح.',
        'error_message' => 'فشل في حذف الحضور: ',
        'record_not_found' => 'السجل غير موجود.',
        'invalid_record' => 'سجل غير صالح.',
        'present_students' => 'الطلاب الحاضرين',
        'absent_students' => 'الطلاب الغائبين',
        'late_arrivals' => 'الوصول المتأخر',
        'excused_absences' => 'الغيابات المبررة',
        'overall_rate' => 'المعدل العام',
        'rate' => 'النسبة',
        'filter_records' => 'تصفية سجلات الحضور',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'subject' => 'المادة',
        'all_subjects' => 'جميع المواد',
        'recorded_by' => 'مسجل بواسطة',
        'all_users' => 'جميع المستخدمين',
        'apply_filters' => 'تطبيق الفلاتر',
        'reset_filters' => 'إعادة تعيين الفلاتر',
        'daily_attendance_rate' => 'معدل الحضور اليومي',
        'attendance' => 'الحضور',
        'subject_performance' => 'أداء المواد',
        'no_records_found' => 'لم يتم العثور على سجلات حضور',
        'no_records_message' => 'لا توجد سجلات حضور تطابق الفلاتر الحالية.',
        'date' => 'التاريخ',
        'recorded_by_col' => 'مسجل بواسطة',
        'attendance_col' => 'الحضور',
        'rate_col' => 'النسبة',
        'actions' => 'الإجراءات',
        'students' => 'طلاب',
        'total' => 'الإجمالي',
        'view_details' => 'عرض التفاصيل',
        'delete_record' => 'حذف السجل',
        'delete_confirm' => 'هل أنت متأكد أنك تريد حذف سجل الحضور هذا؟ لا يمكن التراجع عن هذا الإجراء.',
        'export_pdf' => 'تصدير إلى PDF',
        'export_excel' => 'تصدير إلى Excel',
        'print_report' => 'طباعة التقرير',
        'attendance_history' => 'سجل الحضور',
        'view' => 'عرض',
        'delete' => 'حذف'
    ],
    3 => [ // Somali
        'page_title' => 'Taariikhda Xuduurka | Maamulka Fasalka',
        'dashboard_title' => 'Taariikhda Xuduurka',
        'dashboard_subtitle' => 'Raadi oo maamul xuduuraha fasalka',
        'back_to_classes' => 'Dib u noqo',
        'success_message' => 'Xogta xuduurka si guul leh ayaa loo tirtiray.',
        'error_message' => 'Khalad ayaa dhacay tirtirida xuduurka: ',
        'record_not_found' => 'Xog lama helin.',
        'invalid_record' => 'Xog aan sax ahayn.',
        'present_students' => 'Ardayda Joogtada ah',
        'absent_students' => 'Ardayda Maqan',
        'late_arrivals' => 'Soo Daahay',
        'excused_absences' => 'La Fasaxay',
        'overall_rate' => 'Boqolkiiba',
        'rate' => 'Boqolkiiba',
        'filter_records' => 'Xuduuraha Filter Xuduuraha',
        'from_date' => 'Taariikhda Laga Bilaabo',
        'to_date' => 'Taariikhda Ilaa',
        'subject' => 'Maadada',
        'all_subjects' => 'Dhammaan Maadooyinka',
        'recorded_by' => 'saxay',
        'all_users' => 'Dhammaan Isticmaalayaasha',
        'apply_filters' => 'Codsiga Filters',
        'reset_filters' => 'Dib u Dejinta Filters',
        'daily_attendance_rate' => 'Heerka Xuduurka Maalinta',
        'attendance' => 'Xuduurka',
        'subject_performance' => 'Waxqabadka Qaybta',
        'no_records_found' => 'Xog Xuduuro Ah Lama Helin',
        'no_records_message' => 'Xog xuduuro ah oo ku habboon filters-kaaga hadda jira lama helin.',
        'date' => 'Taariikhda',
        'recorded_by_col' => 'Ku Qoran',
        'attendance_col' => 'Xuduurka',
        'rate_col' => 'Heerka',
        'actions' => 'Ficillo',
        'students' => 'ardayda',
        'total' => 'Wadarta',
        'view_details' => 'Faahfaahin Arag',
        'delete_record' => 'Tirtir Xogta',
        'delete_confirm' => 'Ma hubtaa inaad rabto inaad tirtirto xogta xuduurkaan? Tallaabadan dib looma noqon karo.',
        'export_pdf' => 'U Dhoofin PDF',
        'export_excel' => 'U Dhoofin Excel',
        'print_report' => 'Daabac Warbixin',
        'attendance_history' => 'Taariikhda Xuduurka',
        'view' => 'Arag',
        'delete' => 'Tirtir'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

// --- Handle deletion ---
if (isset($_GET['delete_id'])) {
    $delete_id = (int)$_GET['delete_id'];
    if ($delete_id > 0) {
        // Optional: ensure the teacher actually recorded this attendance
        $check = $conn->query("SELECT recorded_by FROM attendance WHERE id=$delete_id LIMIT 1");
        if ($check && $check->num_rows) {
            $row = $check->fetch_assoc();
            $stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
            $stmt->bind_param("i", $delete_id);
            if ($stmt->execute()) {
                $msg = t('success_message');
                $success = true;
            } else {
                $msg = t('error_message') . $stmt->error;
            }
        } else {
            $msg = t('record_not_found');
        }
    } else {
        $msg = t('invalid_record');
    }
}

// --- Filters ---
$class_id = (int)($_GET['class_id'] ?? 0);
$from_date = $_GET['from'] ?? date('Y-m-d', strtotime('-30 days'));
$to_date   = $_GET['to'] ?? date('Y-m-d');
$subject_search = $_GET['subject'] ?? '';
$recorded_by_filter = $_GET['recorded_by'] ?? '';
$status_filter = $_GET['status'] ?? '';

// --- Fetch class name ---
$class_name = $conn->query("SELECT name FROM classes WHERE id=$class_id")->fetch_assoc()['name'] ?? 'Unknown';

// --- Build WHERE clause ---
$whereClauses = ["cs.teacher_id = $teacher_id", "cs.class_id = $class_id"];
if ($from_date) $whereClauses[] = "a.date >= '$from_date'";
if ($to_date) $whereClauses[] = "a.date <= '$to_date'";
if ($subject_search) $whereClauses[] = "a.subject_id = " . (int)$subject_search;
if ($recorded_by_filter) $whereClauses[] = "a.recorded_by LIKE '%" . $conn->real_escape_string($recorded_by_filter) . "%'";
$whereSQL = implode(" AND ", $whereClauses);

// --- Fetch attendance records (aggregated) ---
$attendanceRes = $conn->query("
    SELECT a.id, a.date, a.subject_id, s.name AS subject_name, s.code as subject_code, a.recorded_by,
           SUM(a.status='present') AS present_count,
           SUM(a.status='absent') AS absent_count,
           SUM(a.status='late') AS late_count,
           SUM(a.status='excused') AS excused_count,
           COUNT(*) AS total_records,
           ROUND((SUM(a.status='present')/COUNT(*))*100, 1) as attendance_rate
    FROM attendance a
    JOIN subjects s ON s.id = a.subject_id
    JOIN class_subjects cs ON cs.subject_id = a.subject_id AND cs.class_id = $class_id
    WHERE $whereSQL
    GROUP BY a.date, a.subject_id, a.recorded_by
    ORDER BY a.date DESC
");

$records = [];
$totalSummary = ['present'=>0,'absent'=>0,'late'=>0,'excused'=>0,'total'=>0];
$dailyAttendance = [];
$subjectPerformance = [];

while ($row = $attendanceRes->fetch_assoc()) {
    $records[] = $row;
    $totalSummary['present'] += $row['present_count'];
    $totalSummary['absent'] += $row['absent_count'];
    $totalSummary['late'] += $row['late_count'];
    $totalSummary['excused'] += $row['excused_count'];
    $totalSummary['total'] += $row['total_records'];
    
    // For charts
    $date = $row['date'];
    if (!isset($dailyAttendance[$date])) {
        $dailyAttendance[$date] = ['present'=>0, 'total'=>0];
    }
    $dailyAttendance[$date]['present'] += $row['present_count'];
    $dailyAttendance[$date]['total'] += $row['total_records'];
    
    // Subject performance
    $subject = $row['subject_name'];
    if (!isset($subjectPerformance[$subject])) {
        $subjectPerformance[$subject] = ['present'=>0, 'total'=>0];
    }
    $subjectPerformance[$subject]['present'] += $row['present_count'];
    $subjectPerformance[$subject]['total'] += $row['total_records'];
}

// Calculate overall rate
$overallRate = $totalSummary['total'] > 0 ? 
    round(($totalSummary['present'] / $totalSummary['total']) * 100, 1) : 0;

// --- Subjects dropdown ---
$subjectOptions = [];
$subjectRes = $conn->query("SELECT DISTINCT s.id, s.name, s.code
                            FROM subjects s
                            JOIN class_subjects cs ON cs.subject_id = s.id
                            WHERE cs.teacher_id = $teacher_id AND cs.class_id = $class_id
                            ORDER BY s.name");
while ($s = $subjectRes->fetch_assoc()) $subjectOptions[] = $s;

// --- Recorded by dropdown ---
$recordedByOptions = [];
$rbRes = $conn->query("SELECT DISTINCT recorded_by
                       FROM attendance a
                       JOIN class_subjects cs ON cs.subject_id = a.subject_id AND cs.class_id = a.class_id
                       WHERE cs.teacher_id = $teacher_id AND cs.class_id = $class_id
                       ORDER BY recorded_by");
while ($r = $rbRes->fetch_assoc()) $recordedByOptions[] = $r['recorded_by'];

// Get class students count for context
$studentCount = $conn->query("SELECT COUNT(*) as cnt FROM class_students WHERE class_id=$class_id")->fetch_assoc()['cnt'];
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> - <?= htmlspecialchars($class_name) ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-lg-11 col-xl-10">
                <!-- Header -->
                <div class="dashboard-header">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h1 class="header-title">
                                <i class="fas fa-history me-2"></i>
                                <?= t('dashboard_title') ?>
                            </h1>
                            <div class="header-subtitle">
                                <i class="fas fa-users me-1"></i>
                                <?= htmlspecialchars($class_name) ?> • 
                                <span class="text-primary"><?= $studentCount ?> <?= t('students') ?></span>
                            </div>
                        </div>
                        <div>
                            <a href="classes.php" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>
                                <?= t('back_to_classes') ?>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Success/Error Message -->
                <?php if($msg): ?>
                    <div class="alert alert-<?= $success ? 'success' : 'danger' ?> alert-dismissible fade show alert-animated" role="alert">
                        <i class="fas <?= $success ? 'fa-check-circle' : 'fa-exclamation-circle' ?> me-2"></i>
                        <?= htmlspecialchars($msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Stats Overview -->
                <div class="stats-grid">
                    <div class="stat-card present">
                        <div class="stat-icon present">
                            <i class="fas fa-check-circle"></i>
                        </div>
                        <div class="stat-number"><?= $totalSummary['present'] ?></div>
                        <div class="stat-label"><?= t('present_students') ?></div>
                        <div class="stat-percentage">
                            <i class="fas fa-chart-line me-1"></i>
                            <?= $overallRate ?>% <?= t('overall_rate') ?>
                        </div>
                    </div>
                    
                    <div class="stat-card absent">
                        <div class="stat-icon absent">
                            <i class="fas fa-times-circle"></i>
                        </div>
                        <div class="stat-number"><?= $totalSummary['absent'] ?></div>
                        <div class="stat-label"><?= t('absent_students') ?></div>
                        <div class="stat-percentage">
                            <i class="fas fa-exclamation-triangle me-1"></i>
                            <?= $totalSummary['total'] > 0 ? round(($totalSummary['absent'] / $totalSummary['total']) * 100, 1) : 0 ?>% <?= t('rate') ?>
                        </div>
                    </div>
                    
                    <div class="stat-card late">
                        <div class="stat-icon late">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="stat-number"><?= $totalSummary['late'] ?></div>
                        <div class="stat-label"><?= t('late_arrivals') ?></div>
                        <div class="stat-percentage">
                            <i class="fas fa-tachometer-alt me-1"></i>
                            <?= $totalSummary['total'] > 0 ? round(($totalSummary['late'] / $totalSummary['total']) * 100, 1) : 0 ?>% <?= t('rate') ?>
                        </div>
                    </div>
                    
                    <div class="stat-card excused">
                        <div class="stat-icon excused">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stat-number"><?= $totalSummary['excused'] ?></div>
                        <div class="stat-label"><?= t('excused_absences') ?></div>
                        <div class="stat-percentage">
                            <i class="fas fa-user-shield me-1"></i>
                            <?= $totalSummary['total'] > 0 ? round(($totalSummary['excused'] / $totalSummary['total']) * 100, 1) : 0 ?>% <?= t('rate') ?>
                        </div>
                    </div>
                </div>

                <!-- Filters -->
                <div class="filter-card">
                    <div class="filter-title">
                        <i class="fas fa-filter"></i>
                        <?= t('filter_records') ?>
                    </div>
                    <form method="get" class="row g-3">
                        <input type="hidden" name="class_id" value="<?= $class_id ?>">
                        
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-500"><?= t('from_date') ?></label>
                            <input type="date" name="from" class="form-control" value="<?= htmlspecialchars($from_date) ?>">
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-500"><?= t('to_date') ?></label>
                            <input type="date" name="to" class="form-control" value="<?= htmlspecialchars($to_date) ?>">
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-500"><?= t('subject') ?></label>
                            <select name="subject" class="form-select">
                                <option value=""><?= t('all_subjects') ?></option>
                                <?php foreach($subjectOptions as $s): ?>
                                    <option value="<?= $s['id'] ?>" <?= $s['id']==$subject_search?'selected':'' ?>>
                                        <?= htmlspecialchars($s['name']) ?> (<?= htmlspecialchars($s['code']) ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-md-6 col-lg-3">
                            <label class="form-label fw-500"><?= t('recorded_by') ?></label>
                            <select name="recorded_by" class="form-select">
                                <option value=""><?= t('all_users') ?></option>
                                <?php foreach($recordedByOptions as $r): ?>
                                    <option value="<?= htmlspecialchars($r) ?>" <?= $r==$recorded_by_filter?'selected':'' ?>>
                                        <?= htmlspecialchars($r) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="col-12">
                            <div class="d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-search me-2"></i>
                                    <?= t('apply_filters') ?>
                                </button>
                                <a href="?class_id=<?= $class_id ?>" class="btn btn-outline-secondary">
                                    <i class="fas fa-redo me-2"></i>
                                    <?= t('reset_filters') ?>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Charts -->
                <?php if(!empty($records)): ?>
                <div class="row">
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <div class="chart-title"><?= t('daily_attendance_rate') ?></div>
                            <canvas id="dailyChart" height="250"></canvas>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="chart-container">
                            <div class="chart-title"><?= t('subject_performance') ?></div>
                            <canvas id="subjectChart" height="250"></canvas>
                        </div>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Attendance Table -->
                <div class="table-container">
                    <?php if(empty($records)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-clipboard-list"></i>
                            </div>
                            <h4><?= t('no_records_found') ?></h4>
                            <p class="text-muted"><?= t('no_records_message') ?></p>
                            <a href="?class_id=<?= $class_id ?>" class="btn btn-primary mt-3">
                                <i class="fas fa-redo me-2"></i>
                                <?= t('reset_filters') ?>
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th><?= t('date') ?></th>
                                        <th><?= t('subject') ?></th>
                                        <th><?= t('recorded_by_col') ?></th>
                                        <th class="text-center"><?= t('attendance_col') ?></th>
                                        <th class="text-center"><?= t('rate_col') ?></th>
                                        <th class="text-center"><?= t('actions') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($records as $rec): ?>
                                    <tr>
                                        <td>
                                            <div class="fw-500"><?= date('M d, Y', strtotime($rec['date'])) ?></div>
                                            <small class="text-muted"><?= date('l', strtotime($rec['date'])) ?></small>
                                        </td>
                                        <td>
                                            <div class="fw-500"><?= htmlspecialchars($rec['subject_name']) ?></div>
                                            <small class="text-muted"><?= htmlspecialchars($rec['subject_code']) ?></small>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="rounded-circle bg-primary-light text-primary d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px;">
                                                    <i class="fas fa-user"></i>
                                                </div>
                                                <span><?= htmlspecialchars($rec['recorded_by']) ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-wrap gap-2 justify-content-center">
                                                <span class="attendance-badge badge-present">
                                                    <i class="fas fa-check me-1"></i><?= $rec['present_count'] ?>P
                                                </span>
                                                <span class="attendance-badge badge-absent">
                                                    <i class="fas fa-times me-1"></i><?= $rec['absent_count'] ?>A
                                                </span>
                                                <span class="attendance-badge badge-late">
                                                    <i class="fas fa-clock me-1"></i><?= $rec['late_count'] ?>L
                                                </span>
                                                <span class="attendance-badge badge-excused">
                                                    <i class="fas fa-user-check me-1"></i><?= $rec['excused_count'] ?>E
                                                </span>
                                            </div>
                                            <div class="text-center text-muted mt-1">
                                                <small><?= t('total') ?>: <?= $rec['total_records'] ?> <?= t('students') ?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="fw-600 fs-5 <?= $rec['attendance_rate'] >= 80 ? 'text-success' : ($rec['attendance_rate'] >= 60 ? 'text-warning' : 'text-danger') ?>">
                                                <?= $rec['attendance_rate'] ?>%
                                            </div>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="view_attendance.php?class_id=<?= $class_id ?>&date=<?= $rec['date'] ?>&subject_id=<?= $rec['subject_id'] ?>" 
                                                   class="btn btn-sm btn-view" 
                                                   title="<?= t('view_details') ?>">
                                                    <i class="fas fa-eye"></i>
                                                    <?= t('view') ?>
                                                </a>
                                                <a href="?class_id=<?= $class_id ?>&delete_id=<?= $rec['id'] ?>" 
                                                   class="btn btn-sm btn-delete" 
                                                   title="<?= t('delete_record') ?>"
                                                   onclick="return confirm('<?= t('delete_confirm') ?>');">
                                                    <i class="fas fa-trash"></i>
                                                    <?= t('delete') ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Export Buttons -->
                 

                <!-- Pagination -->
                <?php if(!empty($records)): ?>
                <div class="pagination-container">
                    <nav>
                        <a href="#" class="page-link"><i class="fas fa-chevron-left"></i></a>
                        <a href="#" class="page-link active">1</a>
                        <a href="#" class="page-link">2</a>
                        <a href="#" class="page-link">3</a>
                        <a href="#" class="page-link"><i class="fas fa-chevron-right"></i></a>
                    </nav>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Charts Script -->
    <script>
        <?php if(!empty($records)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            // Daily Attendance Chart
            const dailyCtx = document.getElementById('dailyChart').getContext('2d');
            const dailyDates = <?= json_encode(array_keys($dailyAttendance)) ?>;
            const dailyRates = dailyDates.map(date => {
                const data = <?= json_encode($dailyAttendance) ?>[date];
                return data.total > 0 ? Math.round((data.present / data.total) * 100) : 0;
            });
            
            new Chart(dailyCtx, {
                type: 'line',
                data: {
                    labels: dailyDates,
                    datasets: [{
                        label: '<?= t('attendance') ?> %',
                        data: dailyRates,
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '<?= t('attendance') ?>: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        x: {
                            ticks: {
                                maxRotation: 45
                            }
                        }
                    }
                }
            });
            
            // Subject Performance Chart
            const subjectCtx = document.getElementById('subjectChart').getContext('2d');
            const subjects = <?= json_encode(array_keys($subjectPerformance)) ?>;
            const subjectRates = subjects.map(subject => {
                const data = <?= json_encode($subjectPerformance) ?>[subject];
                return data.total > 0 ? Math.round((data.present / data.total) * 100) : 0;
            });
            
            new Chart(subjectCtx, {
                type: 'bar',
                data: {
                    labels: subjects,
                    datasets: [{
                        label: '<?= t('attendance') ?> %',
                        data: subjectRates,
                        backgroundColor: subjects.map((_, i) => 
                            i % 3 === 0 ? '#10b981' : 
                            i % 3 === 1 ? '#3b82f6' : '#8b5cf6'
                        ),
                        borderWidth: 0,
                        borderRadius: 6
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return '<?= t('attendance') ?>: ' + context.parsed.y + '%';
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            max: 100,
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        }
                    }
                }
            });
        });
        <?php endif; ?>
        
        // Export Functions
        function exportToPDF() {
            alert('<?= t('export_pdf') ?> ' + '<?= t('attendance_history') ?>');
            // In production, this would generate and download a PDF report
        }
        
        function exportToExcel() {
            alert('<?= t('export_excel') ?> ' + '<?= t('attendance_history') ?>');
            // In production, this would generate and download an Excel report
        }
        
        function printReport() {
            window.print();
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

    <style>
        :root {
            --primary-color: #3b82f6;
            --primary-light: #eff6ff;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #8b5cf6;
            --light-bg: #f8fafc;
            --dark-text: #1e293b;
            --light-text: #64748b;
            --border-color: #e2e8f0;
            --shadow-sm: 0 2px 4px rgba(0,0,0,0.05);
            --shadow-md: 0 4px 20px rgba(0,0,0,0.08);
            --shadow-lg: 0 10px 40px rgba(0,0,0,0.12);
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--light-bg);
            color: var(--dark-text);
            padding-top: 20px;
            padding-bottom: 40px;
        }

        .dashboard-header {
            background: white;
            border-radius: var(--radius-lg);
            padding: 25px 30px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
            border-left: 5px solid var(--primary-color);
        }

        .header-title {
            font-weight: 700;
            font-size: 1.8rem;
            color: var(--dark-text);
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: var(--light-text);
            font-size: 0.95rem;
            margin-bottom: 15px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 20px;
            box-shadow: var(--shadow-sm);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-top: 4px solid transparent;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card.present { border-top-color: var(--success-color); }
        .stat-card.absent { border-top-color: var(--danger-color); }
        .stat-card.late { border-top-color: var(--warning-color); }
        .stat-card.excused { border-top-color: var(--info-color); }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            margin-bottom: 15px;
        }

        .stat-icon.present { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
        .stat-icon.absent { background: rgba(239, 68, 68, 0.1); color: var(--danger-color); }
        .stat-icon.late { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }
        .stat-icon.excused { background: rgba(139, 92, 246, 0.1); color: var(--info-color); }

        .stat-number {
            font-size: 2.2rem;
            font-weight: 700;
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--light-text);
            font-size: 0.9rem;
            font-weight: 500;
        }

        .stat-percentage {
            font-size: 0.85rem;
            padding: 3px 10px;
            border-radius: 20px;
            background: var(--primary-light);
            color: var(--primary-color);
            display: inline-block;
            margin-top: 8px;
        }

        .filter-card {
            background: white;
            border-radius: var(--radius-md);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
        }

        .filter-title {
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
            font-size: 1.1rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .form-control, .form-select {
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            padding: 10px 15px;
            font-size: 0.95rem;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 12px 25px;
            border-radius: var(--radius-sm);
            font-weight: 600;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(37, 99, 235, 0.3);
        }

        .btn-outline-secondary {
            border: 1px solid var(--border-color);
            color: var(--light-text);
            padding: 12px 25px;
            border-radius: var(--radius-sm);
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-secondary:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: var(--primary-light);
        }

        .table-container {
            background: white;
            border-radius: var(--radius-md);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-bottom: 30px;
        }

        .table {
            margin-bottom: 0;
        }

        .table thead {
            background: var(--primary-light);
        }

        .table th {
            font-weight: 600;
            color: var(--dark-text);
            border-bottom: 2px solid var(--border-color);
            padding: 15px;
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        .attendance-badge {
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
            display: inline-block;
        }

        .badge-present { background: rgba(16, 185, 129, 0.1); color: var(--success-color); }
        .badge-absent { background: rgba(239, 68, 68, 0.1); color: var(--danger-color); }
        .badge-late { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); }
        .badge-excused { background: rgba(139, 92, 246, 0.1); color: var(--info-color); }

        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .btn-sm {
            padding: 6px 12px;
            font-size: 0.85rem;
            border-radius: var(--radius-sm);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view { background: var(--primary-light); color: var(--primary-color); border: none; }
        .btn-edit { background: rgba(245, 158, 11, 0.1); color: var(--warning-color); border: none; }
        .btn-delete { background: rgba(239, 68, 68, 0.1); color: var(--danger-color); border: none; }

        .btn-view:hover { background: var(--primary-color); color: white; }
        .btn-edit:hover { background: var(--warning-color); color: white; }
        .btn-delete:hover { background: var(--danger-color); color: white; }

        .chart-container {
            background: white;
            border-radius: var(--radius-md);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow-md);
        }

        .chart-title {
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--dark-text);
            font-size: 1.1rem;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--light-text);
        }

        .empty-icon {
            font-size: 3rem;
            color: var(--border-color);
            margin-bottom: 20px;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 30px;
        }

        .page-link {
            border: 1px solid var(--border-color);
            color: var(--dark-text);
            padding: 8px 16px;
            margin: 0 4px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .page-link:hover, .page-link.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .export-buttons {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-export {
            background: white;
            border: 1px solid var(--border-color);
            color: var(--dark-text);
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .btn-export:hover {
            border-color: var(--primary-color);
            color: var(--primary-color);
            background: var(--primary-light);
        }

        @media (max-width: 768px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .table {
                font-size: 0.9rem;
            }
            
            .table th, .table td {
                padding: 10px;
            }
            
            .header-title {
                font-size: 1.5rem;
            }
        }

        /* Animation for success message */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-animated {
            animation: slideIn 0.5s ease-out;
        }
    </style>