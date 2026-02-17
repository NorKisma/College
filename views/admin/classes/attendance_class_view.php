<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Class Attendance',
        'back_to_classes' => 'Back to Classes',
        'filter_attendance' => 'Filter Attendance',
        'start_date' => 'Start Date',
        'end_date' => 'End Date',
        'apply_filter' => 'Apply Filter',
        'reset' => 'Reset',
        'showing' => 'Showing',
        'students' => 'students',
        'total_students' => 'Total Students',
        'average_attendance' => 'Average Attendance',
        'date_range' => 'Date Range',
        'class_sessions' => 'Class Sessions',
        'detailed_view' => 'Detailed View',
        'student_summary' => 'Student Summary',
        'class_timetable' => 'Class Timetable',
        'detailed_attendance_records' => 'Detailed Attendance Records',
        'student_name' => 'Student Name',
        'id' => 'ID',
        'attendance_rate' => 'Attendance Rate',
        'student_attendance_summary' => 'Student Attendance Summary',
        'total_classes' => 'Total Classes',
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'excused' => 'Excused',
        'status' => 'Status',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'poor' => 'Poor',
       
        'mon' => 'Mon',
        'tue' => 'Tue',
        'wed' => 'Wed',
        'thu' => 'Thu',
        'fri' => 'Fri',
        'sat' => 'Sat',
        'sun' => 'Sun',
        'jan' => 'Jan',
        'feb' => 'Feb',
        'mar' => 'Mar',
        'apr' => 'Apr',
        'may' => 'May',
        'jun' => 'Jun',
        'jul' => 'Jul',
        'aug' => 'Aug',
        'sep' => 'Sep',
        'oct' => 'Oct',
        'nov' => 'Nov',
        'dec' => 'Dec',
        'no_record' => 'No Record',
        'class_not_found' => 'Class not found!'
    ],
    2 => [ // Arabic
        'page_title' => 'حضور الصف',
        'back_to_classes' => 'العودة إلى الصفوف',
        'filter_attendance' => 'تصفية الحضور',
        'start_date' => 'تاريخ البداية',
        'end_date' => 'تاريخ النهاية',
        'apply_filter' => 'تطبيق التصفية',
        'reset' => 'إعادة تعيين',
        'showing' => 'عرض',
        'students' => 'طالب',
        'total_students' => 'إجمالي الطلاب',
        'average_attendance' => 'متوسط الحضور',
        'date_range' => 'النطاق الزمني',
        'class_sessions' => 'حصص الصف',
        'detailed_view' => 'عرض مفصل',
        'student_summary' => 'ملخص الطالب',
        'class_timetable' => 'جدول الصف',
        'detailed_attendance_records' => 'سجلات الحضور المفصلة',
        'student_name' => 'اسم الطالب',
        'id' => 'الرقم',
        'attendance_rate' => 'نسبة الحضور',
        'student_attendance_summary' => 'ملخص حضور الطلاب',
        'total_classes' => 'إجمالي الحصص',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'excused' => 'معذور',
        'status' => 'الحالة',
        'excellent' => 'ممتاز',
        'good' => 'جيد',
        'poor' => 'ضعيف',
        'mon' => 'اثنين',
        'tue' => 'ثلاثاء',
        'wed' => 'أربعاء',
        'thu' => 'خميس',
        'fri' => 'جمعة',
        'sat' => 'سبت',
        'sun' => 'أحد',
        'jan' => 'يناير',
        'feb' => 'فبراير',
        'mar' => 'مارس',
        'apr' => 'أبريل',
        'may' => 'مايو',
        'jun' => 'يونيو',
        'jul' => 'يوليو',
        'aug' => 'أغسطس',
        'sep' => 'سبتمبر',
        'oct' => 'أكتوبر',
        'nov' => 'نوفمبر',
        'dec' => 'ديسمبر',
        'no_record' => 'لا يوجد سجل',
        'class_not_found' => 'الصف غير موجود!'
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

// Check if class_id is provided
if (!isset($_GET['class_id']) || !is_numeric($_GET['class_id'])) {
    header('Location: classes.php');
    exit;
}

$class_id = intval($_GET['class_id']);

$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;

// 1 = light, 0 = dark
$is_dark = ($theme === 0);

// Get class details
$query = "SELECT c.*, s.name as section_name 
          FROM classes c 
          LEFT JOIN sections s ON c.section_id = s.id 
          WHERE c.id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$class_result = $stmt->get_result();
$class = $class_result->fetch_assoc();

if (!$class) {
    echo "<div class='alert alert-danger'>" . t('class_not_found') . "</div>";
    exit;
}

// Get all students in this class
$query = "SELECT s.* 
          FROM students s 
          INNER JOIN class_students cs ON s.id = cs.student_id 
          WHERE cs.class_id = ? 
          ORDER BY s.name";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$students_result = $stmt->get_result();
$students = [];
while ($row = $students_result->fetch_assoc()) {
    $students[$row['id']] = $row;
}

// Get class subjects with schedules
$query = "SELECT cs.*, sub.name as subject_name, sub.code as subject_code,
                 t.first_name, t.last_name,
                 css.day_of_week as schedule_day, css.start_time as schedule_start, 
                 css.end_time as schedule_end
          FROM class_subjects cs
          LEFT JOIN subjects sub ON cs.subject_id = sub.id
          LEFT JOIN teachers t ON cs.teacher_id = t.id
          LEFT JOIN class_subject_schedule css ON cs.id = css.class_subject_id
          WHERE cs.class_id = ?
          ORDER BY cs.day_of_week, cs.start_time";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $class_id);
$stmt->execute();
$subjects_result = $stmt->get_result();

// Organize subjects by day
$subjects_by_day = [
    'Monday' => [],
    'Tuesday' => [],
    'Wednesday' => [],
    'Thursday' => [],
    'Friday' => [],
    'Saturday' => [],
    'Sunday' => []
];

while ($subject = $subjects_result->fetch_assoc()) {
    $day = $subject['day_of_week'];
    $subjects_by_day[$day][] = $subject;
}

// Get attendance data for a specific date range (last 30 days by default)
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-d', strtotime('-30 days'));
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-d');

// Get attendance records
$query = "SELECT a.*
FROM attendance a
JOIN class_subjects cs 
  ON cs.subject_id = a.subject_id
 AND cs.class_id = a.class_id
WHERE a.class_id = ?
AND DATE(a.recorded_at) BETWEEN ? AND ?
ORDER BY a.recorded_at DESC, a.student_id
";
$stmt = $conn->prepare($query);
$stmt->bind_param("iss", $class_id, $start_date, $end_date);
$stmt->execute();
$attendance_result = $stmt->get_result();

// Organize attendance by student and date
$attendance_data = [];
while ($record = $attendance_result->fetch_assoc()) {
    $student_id = $record['student_id'];
    $date = date('Y-m-d', strtotime($record['recorded_at']));
    $subject_id = $record['subject_id'];
    
    if (!isset($attendance_data[$student_id])) {
        $attendance_data[$student_id] = [];
    }
    
    if (!isset($attendance_data[$student_id][$date])) {
        $attendance_data[$student_id][$date] = [];
    }
    
    $attendance_data[$student_id][$date][$subject_id] = $record['status'];
}

// Calculate attendance statistics
$attendance_stats = [];
foreach ($students as $student_id => $student) {
    $total_classes = 0;
    $present_count = 0;
    $absent_count = 0;
    $late_count = 0;
    $excused_count = 0;
    
    if (isset($attendance_data[$student_id])) {
        foreach ($attendance_data[$student_id] as $date => $subjects) {
            foreach ($subjects as $subject_id => $status) {
                $total_classes++;
                switch ($status) {
                    case 'present':
                        $present_count++;
                        break;
                    case 'absent':
                        $absent_count++;
                        break;
                    case 'late':
                        $late_count++;
                        break;
                    case 'excused':
                        $excused_count++;
                        break;
                }
            }
        }
    }
    
    $attendance_rate = $total_classes > 0 ? (($present_count + $excused_count) / $total_classes) * 100 : 0;
    
    $attendance_stats[$student_id] = [
        'total' => $total_classes,
        'present' => $present_count,
        'absent' => $absent_count,
        'late' => $late_count,
        'excused' => $excused_count,
        'rate' => round($attendance_rate, 2)
    ];
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> - <?php echo htmlspecialchars($class['name']); ?></title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- DataTables CSS -->
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.bootstrap5.min.css">
</head>
<body class="<?= $is_dark ? 'dark' : 'light' ?>">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-0">
                    <i class="bi bi-calendar-check me-2"></i>
                    <?= t('page_title') ?>
                </h1>
                <p class="text-muted mb-0">
                    <?php echo htmlspecialchars($class['name']); ?>
                    <?php if ($class['section_name']): ?>
                        - <?php echo htmlspecialchars($class['section_name']); ?>
                    <?php endif; ?>
                </p>
            </div>
            <div>
                <a href="classes.php" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left"></i> <?= t('back_to_classes') ?>
                </a>
                
            </div>
        </div>

        <!-- Date Filter -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0"><?= t('filter_attendance') ?></h5>
            </div>
            <div class="card-body">
                <form method="GET" class="row g-3">
                    <input type="hidden" name="class_id" value="<?php echo $class_id; ?>">
                    <div class="col-md-3">
                        <label for="start_date" class="form-label"><?= t('start_date') ?></label>
                        <input type="date" class="form-control" id="start_date" name="start_date" 
                               value="<?php echo $start_date; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="end_date" class="form-label"><?= t('end_date') ?></label>
                        <input type="date" class="form-control" id="end_date" name="end_date" 
                               value="<?php echo $end_date; ?>">
                    </div>
                    <div class="col-md-3 align-self-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-filter"></i> <?= t('apply_filter') ?>
                        </button>
                        <a href="?class_id=<?php echo $class_id; ?>" class="btn btn-outline-secondary">
                            <?= t('reset') ?>
                        </a>
                    </div>
                    <div class="col-md-3 text-end align-self-end">
                        <span class="text-muted">
                            <?= t('showing') ?>: <?php echo count($students); ?> <?= t('students') ?>
                        </span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Statistics Summary -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"><?= t('total_students') ?></h6>
                                <h3 class="mb-0"><?php echo count($students); ?></h3>
                            </div>
                            <div class="bg-primary text-white rounded-circle p-3">
                                <i class="bi bi-people fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"><?= t('average_attendance') ?></h6>
                                <h3 class="mb-0">
                                    <?php 
                                    $total_rate = 0;
                                    $count = 0;
                                    foreach ($attendance_stats as $stats) {
                                        if ($stats['total'] > 0) {
                                            $total_rate += $stats['rate'];
                                            $count++;
                                        }
                                    }
                                    echo $count > 0 ? round($total_rate / $count, 1) . '%' : '0%';
                                    ?>
                                </h3>
                            </div>
                            <div class="bg-success text-white rounded-circle p-3">
                                <i class="bi bi-check-circle fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"><?= t('date_range') ?></h6>
                                <h5 class="mb-0">
                                    <?php 
                                    $month_map = [
                                        'Jan' => t('jan'), 'Feb' => t('feb'), 'Mar' => t('mar'),
                                        'Apr' => t('apr'), 'May' => t('may'), 'Jun' => t('jun'),
                                        'Jul' => t('jul'), 'Aug' => t('aug'), 'Sep' => t('sep'),
                                        'Oct' => t('oct'), 'Nov' => t('nov'), 'Dec' => t('dec')
                                    ];
                                    echo $month_map[date('M', strtotime($start_date))] . ' ' . date('d', strtotime($start_date)); ?> - 
                                    <?php echo $month_map[date('M', strtotime($end_date))] . ' ' . date('d, Y', strtotime($end_date)); ?>
                                </h5>
                            </div>
                            <div class="bg-info text-white rounded-circle p-3">
                                <i class="bi bi-calendar-range fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-2"><?= t('class_sessions') ?></h6>
                                <h3 class="mb-0">
                                    <?php
                                    $total_sessions = 0;
                                    foreach ($subjects_by_day as $day => $subjects) {
                                        $total_sessions += count($subjects);
                                    }
                                    echo $total_sessions;
                                    ?>
                                </h3>
                            </div>
                            <div class="bg-warning text-white rounded-circle p-3">
                                <i class="bi bi-clock-history fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <ul class="nav nav-tabs mb-4" id="attendanceTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="detailed-tab" data-bs-toggle="tab" 
                        data-bs-target="#detailed" type="button" role="tab">
                    <i class="bi bi-table me-2"></i><?= t('detailed_view') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="students-tab" data-bs-toggle="tab" 
                        data-bs-target="#students" type="button" role="tab">
                    <i class="bi bi-person-lines-fill me-2"></i><?= t('student_summary') ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="timetable-tab" data-bs-toggle="tab" 
                        data-bs-target="#timetable" type="button" role="tab">
                    <i class="bi bi-calendar-week me-2"></i><?= t('class_timetable') ?>
                </button>
            </li>
        </ul>

        <div class="tab-content" id="attendanceTabContent">
            <!-- Detailed Attendance Tab -->
            <div class="tab-pane fade show active" id="detailed" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?= t('detailed_attendance_records') ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover" id="attendanceTable">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= t('student_name') ?></th>
                                        <th><?= t('id') ?></th>
                                        <?php
                                        // Generate date columns
                                        $current_date = $start_date;
                                        $day_map = [
                                            'Mon' => t('mon'), 'Tue' => t('tue'), 'Wed' => t('wed'),
                                            'Thu' => t('thu'), 'Fri' => t('fri'), 'Sat' => t('sat'),
                                            'Sun' => t('sun')
                                        ];
                                        while (strtotime($current_date) <= strtotime($end_date)) {
                                            echo "<th class='text-center'>" . 
                                                 $month_map[date('M', strtotime($current_date))] . " " . 
                                                 date('d', strtotime($current_date)) . 
                                                 "<br><small>" . 
                                                 $day_map[date('D', strtotime($current_date))] . 
                                                 "</small></th>";
                                            $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                                        }
                                        ?>
                                        <th><?= t('attendance_rate') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($students as $student_id => $student): ?>
                                        <tr>
                                            <td style="white-space: nowrap;">
                                                <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                                <?php if ($student['guardian_phone']): ?>
                                                    <br><small class="text-muted">
                                                        <i class="bi bi-telephone"></i> <?php echo htmlspecialchars($student['guardian_phone']); ?>
                                                    </small>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo $student_id; ?></td>
                                            <?php
                                            $current_date = $start_date;
                                            while (strtotime($current_date) <= strtotime($end_date)) {
                                                $status = '';
                                                if (isset($attendance_data[$student_id][$current_date])) {
                                                    // Get all statuses for this day
                                                    $day_statuses = array_values($attendance_data[$student_id][$current_date]);
                                                    // Count occurrences
                                                    $status_counts = array_count_values($day_statuses);
                                                    // Determine most frequent status
                                                    arsort($status_counts);
                                                    $status = key($status_counts);
                                                }
                                                
                                                $class = '';
                                                $text = '';
                                                $status_text = '';
                                                switch ($status) {
                                                    case 'present':
                                                        $class = 'present';
                                                        $text = 'P';
                                                        $status_text = t('present');
                                                        break;
                                                    case 'absent':
                                                        $class = 'absent';
                                                        $text = 'A';
                                                        $status_text = t('absent');
                                                        break;
                                                    case 'late':
                                                        $class = 'late';
                                                        $text = 'L';
                                                        $status_text = t('late');
                                                        break;
                                                    case 'excused':
                                                        $class = 'excused';
                                                        $text = 'E';
                                                        $status_text = t('excused');
                                                        break;
                                                    default:
                                                        $class = 'bg-light';
                                                        $text = '-';
                                                        $status_text = t('no_record');
                                                }
                                                
                                                echo "<td class='text-center attendance-cell $class' title='$current_date: $status_text'>
                                                        <span class='attendance-badge'>$text</span>
                                                      </td>";
                                                
                                                $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
                                            }
                                            ?>
                                            <td class="text-center">
                                                <?php
                                                $rate = $attendance_stats[$student_id]['rate'];
                                                $rate_class = '';
                                                if ($rate >= 90) $rate_class = 'high-attendance';
                                                elseif ($rate >= 75) $rate_class = 'medium-attendance';
                                                else $rate_class = 'low-attendance';
                                                ?>
                                                <span class="<?php echo $rate_class; ?>">
                                                    <?php echo $rate; ?>%
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Student Summary Tab -->
            <div class="tab-pane fade" id="students" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?= t('student_attendance_summary') ?></h5>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered" id="summaryTable">
                            <thead class="table-light">
                                <tr>
                                    <th><?= t('student_name') ?></th>
                                    <th><?= t('total_classes') ?></th>
                                    <th><?= t('present') ?></th>
                                    <th><?= t('absent') ?></th>
                                    <th><?= t('late') ?></th>
                                    <th><?= t('excused') ?></th>
                                    <th><?= t('attendance_rate') ?></th>
                                    <th><?= t('status') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($students as $student_id => $student): 
                                    $stats = $attendance_stats[$student_id];
                                ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($student['name']); ?></strong>
                                        </td>
                                        <td><?php echo $stats['total']; ?></td>
                                        <td class="text-success"><?php echo $stats['present']; ?></td>
                                        <td class="text-danger"><?php echo $stats['absent']; ?></td>
                                        <td class="text-warning"><?php echo $stats['late']; ?></td>
                                        <td class="text-info"><?php echo $stats['excused']; ?></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar <?php 
                                                    if ($stats['rate'] >= 90) echo 'bg-success';
                                                    elseif ($stats['rate'] >= 75) echo 'bg-warning';
                                                    else echo 'bg-danger';
                                                ?>" 
                                                role="progressbar" 
                                                style="width: <?php echo $stats['rate']; ?>%"
                                                aria-valuenow="<?php echo $stats['rate']; ?>" 
                                                aria-valuemin="0" 
                                                aria-valuemax="100">
                                                    <?php echo $stats['rate']; ?>%
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php
                                            if ($stats['rate'] >= 90) {
                                                echo '<span class="badge bg-success">' . t('excellent') . '</span>';
                                            } elseif ($stats['rate'] >= 75) {
                                                echo '<span class="badge bg-warning">' . t('good') . '</span>';
                                            } else {
                                                echo '<span class="badge bg-danger">' . t('poor') . '</span>';
                                            }
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Timetable Tab -->
            <div class="tab-pane fade" id="timetable" role="tabpanel">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0"><?= t('class_timetable') ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <?php foreach ($subjects_by_day as $day => $subjects): 
                                if (!empty($subjects)): ?>
                                    <div class="col-md-6 col-lg-4 mb-4">
                                        <div class="card subject-card h-100">
                                            <div class="card-header bg-light">
                                                <h6 class="mb-0">
                                                    <i class="bi bi-calendar-day me-2"></i>
                                                    <?php echo $day; ?>
                                                </h6>
                                            </div>
                                            <div class="card-body">
                                                <?php foreach ($subjects as $subject): ?>
                                                    <div class="mb-3 pb-3 border-bottom">
                                                        <h6 class="mb-1">
                                                            <?php echo htmlspecialchars($subject['subject_name']); ?>
                                                            <small class="text-muted">(<?php echo $subject['subject_code']; ?>)</small>
                                                        </h6>
                                                        <div class="d-flex justify-content-between text-muted small mb-2">
                                                            <span>
                                                                <i class="bi bi-clock"></i>
                                                                <?php echo date('h:i A', strtotime($subject['start_time'])); ?> - 
                                                                <?php echo date('h:i A', strtotime($subject['end_time'])); ?>
                                                            </span>
                                                        </div>
                                                        <?php if ($subject['first_name']): ?>
                                                            <div class="d-flex align-items-center">
                                                                <i class="bi bi-person-circle me-2"></i>
                                                                <span>
                                                                    <?php echo htmlspecialchars($subject['first_name'] . ' ' . $subject['last_name']); ?>
                                                                </span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                <?php endforeach; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif;
                            endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTables
            $('#attendanceTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                scrollX: true,
                ordering: true,
                <?php if($currentLangId == 2): ?>
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json'
                }
                <?php endif; ?>
            });

            $('#summaryTable').DataTable({
                dom: 'Bfrtip',
                buttons: [
                    'excel', 'pdf', 'print'
                ],
                pageLength: 25,
                ordering: true,
                <?php if($currentLangId == 2): ?>
                language: {
                    url: '//cdn.datatables.net/plug-ins/1.11.5/i18n/ar.json'
                }
                <?php endif; ?>
            });

            // Add click event to attendance cells
            $('.attendance-cell').click(function() {
                var status = $(this).find('.attendance-badge').text();
                var date = $(this).attr('title').split(':')[0].trim();
                var student = $(this).closest('tr').find('td:first').text().trim();
                
                var statusText = '';
                switch(status) {
                    case 'P': statusText = '<?= t("present") ?>'; break;
                    case 'A': statusText = '<?= t("absent") ?>'; break;
                    case 'L': statusText = '<?= t("late") ?>'; break;
                    case 'E': statusText = '<?= t("excused") ?>'; break;
                    default: statusText = '<?= t("no_record") ?>';
                }
                
                alert('<?= t("student") ?>: ' + student + '\n<?= t("date") ?>: ' + date + '\n<?= t("status") ?>: ' + statusText);
            });
        });

        function printReport() {
            window.print();
        }

        // Auto-refresh every 5 minutes if needed
        // setTimeout(function() {
        //     location.reload();
        // }, 300000);
    </script>
</body>
</html>

 <style>
        .attendance-badge {
            font-size: 0.75em;
            padding: 0.25em 0.5em;
        }
        .present { background-color: #d4edda; color: #155724; }
        .absent { background-color: #f8d7da; color: #721c24; }
        .late { background-color: #fff3cd; color: #856404; }
        .excused { background-color: #d1ecf1; color: #0c5460; }
        .attendance-cell {
            cursor: pointer;
            transition: all 0.2s;
        }
        .attendance-cell:hover {
            transform: scale(1.1);
        }
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #dee2e6;
        }
        .subject-card {
            border-left: 4px solid #007bff;
        }
        .stats-card {
            border-radius: 10px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .high-attendance { color: #28a745; font-weight: bold; }
        .medium-attendance { color: #ffc107; font-weight: bold; }
        .low-attendance { color: #dc3545; font-weight: bold; }
        /* Attendance letters */
        .attendance-cell.present .attendance-badge {
            color: #28a745;  /* green for Present */
            font-weight: bold;
        }

        .attendance-cell.absent .attendance-badge {
            color: #dc3545;  /* red for Absent */
            font-weight: bold;
        }

        .attendance-cell.late .attendance-badge {
            color: #ffc107;  /* yellow for Late */
            font-weight: bold;
        }

        .attendance-cell.excused .attendance-badge {
            color: #17a2b8;  /* blue for Excused */
            font-weight: bold;
        }

        .attendance-cell.bg-light .attendance-badge {
            color: #6c757d;  /* gray for Not Marked */
        }

                    /* ================= DARK MODE ================= */
            body.dark {
                background-color: #0f1115;
                color: #f1f1f1;
            }

            /* Cards */
            body.dark .card,
            body.dark .stats-card,
            body.dark .subject-card {
                background-color: #151922;
                color: #f1f1f1;
                box-shadow: 0 4px 12px rgba(0,0,0,0.6);
            }

            /* Card headers */
            body.dark .card-header {
                background-color: #1b2030;
                border-bottom: 2px solid #2a3042;
                color: #ffffff;
            }

            /* Tables */
            body.dark table {
                color: #f1f1f1;
            }

            body.dark th {
                background-color: #1b2030;
                color: #ffffff;
                border-bottom: 1px solid #2a3042;
            }

            body.dark td {
                background-color: #151922;
                border-bottom: 1px solid #2a3042;
            }

            /* Attendance cells hover */
            body.dark .attendance-cell:hover {
                background-color: #1f2535;
            }

            /* Badges base */
            body.dark .attendance-badge {
                background: transparent;
            }

            /* Attendance status colors (keep meaning) */
            body.dark .present {
                background-color: #1e3a2f;
                color: #4ade80;
            }

            body.dark .absent {
                background-color: #3a1e22;
                color: #f87171;
            }

            body.dark .late {
                background-color: #3a321e;
                color: #facc15;
            }

            body.dark .excused {
                background-color: #1e3340;
                color: #38bdf8;
            }

            /* Attendance letters */
            body.dark .attendance-cell.present .attendance-badge {
                color: #4ade80;
            }

            body.dark .attendance-cell.absent .attendance-badge {
                color: #f87171;
            }

            body.dark .attendance-cell.late .attendance-badge {
                color: #facc15;
            }

            body.dark .attendance-cell.excused .attendance-badge {
                color: #38bdf8;
            }

            body.dark .attendance-cell.bg-light .attendance-badge {
                color: #9ca3af;
            }

            /* Stats text */
            body.dark .high-attendance { color: #4ade80; }
            body.dark .medium-attendance { color: #facc15; }
            body.dark .low-attendance { color: #f87171; }

            /* Borders & separators */
            body.dark hr {
                border-color: #2a3042;
            }

            /* Links */
            body.dark a {
                color: #60a5fa;
            }

            body.dark a:hover {
                color: #93c5fd;
            }

    </style>