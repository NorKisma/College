<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'];
$month = $_POST['month'] ?? date('Y-m');
$year_month = explode('-', $month);
$year = $year_month[0];
$month_num = $year_month[1];

// --- Monthly Attendance Stats ---
$query = $conn->prepare("
    SELECT 
        a.date,
        a.status,
        a.remarks,
        s.name as subject_name,
        c.name as class_name,
        t.first_name as teacher_name
    FROM attendance a
    LEFT JOIN subjects s ON a.subject_id = s.id
    LEFT JOIN classes c ON a.class_id = c.id
    LEFT JOIN teachers t ON s.teacher_id = t.id
    WHERE a.student_id = ?
    AND YEAR(a.date) = ?
    AND MONTH(a.date) = ?
    ORDER BY a.date DESC
");
$query->bind_param("iii", $child_id, $year, $month_num);
$query->execute();
$result = $query->get_result();

$attendance_stats = [
    'present' => 0,
    'absent' => 0,
    'late' => 0,
    'excused' => 0,
    'total' => 0
];
$attendance_data = [];
while ($row = $result->fetch_assoc()) {
    $attendance_data[] = $row;
    $attendance_stats[$row['status']]++;
    $attendance_stats['total']++;
}
$attendance_percentage = $attendance_stats['total'] > 0 
    ? round((($attendance_stats['present'] + $attendance_stats['excused']) / $attendance_stats['total']) * 100, 2)
    : 0;

// --- Today's Attendance ---
$today = date('Y-m-d');

// Get student's current class and section
$class_info = $conn->prepare("
    SELECT cs.class_id, c.section_id
    FROM class_students cs
    LEFT JOIN classes c ON cs.class_id = c.id
    WHERE cs.student_id = ? AND cs.is_active = 1
    LIMIT 1
");
$class_info->bind_param("i", $child_id);
$class_info->execute();
$class_result = $class_info->get_result()->fetch_assoc();
$class_id = $class_result['class_id'] ?? 0;
$section_id = $class_result['section_id'] ?? 0;

// Get subjects scheduled for today for this class
$$today = date('l'); // e.g., "Monday"
$today_date = date('Y-m-d'); // today's date

$subjects_today_stmt = $conn->prepare("
    SELECT 
        s.id AS subject_id,
        s.name AS subject_name,
        t.first_name AS teacher_name,
        a.status AS attendance_status,
        a.remarks
    FROM class_subjects cs
    INNER JOIN subjects s ON cs.subject_id = s.id
    LEFT JOIN teachers t ON cs.teacher_id = t.id
    INNER JOIN class_subject_schedule css ON css.class_subject_id = cs.id
    LEFT JOIN classes c ON cs.class_id = c.id
    LEFT JOIN attendance a 
        ON a.student_id = ? 
        AND a.subject_id = s.id 
        AND a.date = ?
    WHERE cs.class_id = ? 
      AND c.section_id = ? 
      AND css.day_of_week = ?
    ORDER BY css.start_time ASC
");

if (!$subjects_today_stmt) {
    die("Prepare failed: " . $conn->error);
}

// Bind parameters
$subjects_today_stmt->bind_param("isiss", $child_id, $today_date, $class_id, $section_id, $today);

// Execute the statement
if (!$subjects_today_stmt->execute()) {
    die("Execute failed: " . $subjects_today_stmt->error);
}

// Fetch result
$subjects_today_result = $subjects_today_stmt->get_result();
if (!$subjects_today_result) {
    die("Get result failed: " . $subjects_today_stmt->error);
}

// Build the array
$today_subjects = [];
while ($sub = $subjects_today_result->fetch_assoc()) {
    $today_subjects[$sub['subject_id']] = $sub;
}


// Get attendance records for today
$attendance_today_query = $conn->prepare("
    SELECT * FROM attendance 
    WHERE student_id = ? AND date = ?
");
$attendance_today_query->bind_param("is", $child_id, $today);
$attendance_today_query->execute();
$attendance_today_result = $attendance_today_query->get_result();

$today_attendance = [];
while ($att = $attendance_today_result->fetch_assoc()) {
    $today_attendance[$att['subject_id']] = $att;
}

// Merge subjects with attendance
foreach ($today_subjects as $sub_id => &$sub) {
    if (isset($today_attendance[$sub_id])) {
        $sub['status'] = $today_attendance[$sub_id]['status'];
        $sub['remarks'] = $today_attendance[$sub_id]['remarks'];
    } else {
        $sub['status'] = 'Not marked';
        $sub['remarks'] = '';
    }
}
?>

<!-- Attendance Stats -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card bg-primary text-white">
            <div class="stat-icon"><i class="bi bi-check-circle"></i></div>
            <h3><?php echo $attendance_stats['present']; ?></h3>
            <p>Maalmaha Xaadirka</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-danger text-white">
            <div class="stat-icon"><i class="bi bi-x-circle"></i></div>
            <h3><?php echo $attendance_stats['absent']; ?></h3>
            <p>Maalmaha maqan yahay</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-warning text-white">
            <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
            <h3><?php echo $attendance_stats['late']; ?></h3>
            <p>Maalmaha lasoo daahey</p>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card bg-info text-white">
            <div class="stat-icon"><i class="bi bi-percent"></i></div>
            <h3><?php echo $attendance_percentage; ?>%</h3>
            <p>Rateka Imaanshaha</p>
        </div>
    </div>
</div>

<!-- Today's Attendance -->
<h4>Maanta ma imaadey? (<?php echo date('M d, Y'); ?>)</h4>
<div class="table-responsive mb-4">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Maadada</th>
                <th>Macalinka</th>
                <th>Imaanshaha</th>
                <th>Ogeysiis</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($today_subjects)): ?>
                <?php foreach ($today_subjects as $sub): ?>
                    <tr class="attendance-<?php echo strtolower(str_replace(' ', '-', $sub['status'])); ?>">
                        <td><?php echo htmlspecialchars($sub['subject_name']); ?></td>
                        <td><?php echo htmlspecialchars($sub['teacher_name']); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $sub['status'] == 'present' ? 'success' : 
                                     ($sub['status'] == 'absent' ? 'danger' : 
                                     ($sub['status'] == 'late' ? 'warning' : 'secondary')); 
                            ?>">
                                <?php echo ucfirst($sub['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($sub['remarks'] ?: ''); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr><td colspan="4" class="text-center">Maanta wax maada ma lahan.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- Monthly Attendance Table -->
<div class="table-responsive">
    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>Taariikhda</th>
                <th>Maalinta</th>
                <th>Imaanshaha</th>
                <th>Maadada</th>
                <th>Macalinka</th>
                <th>Ogeysiis</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($attendance_data)): ?>
                <?php foreach ($attendance_data as $record): ?>
                    <tr class="attendance-<?php echo $record['status']; ?>">
                        <td><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                        <td><?php echo date('l', strtotime($record['date'])); ?></td>
                        <td>
                            <span class="badge bg-<?php 
                                echo $record['status'] == 'present' ? 'success' : 
                                     ($record['status'] == 'absent' ? 'danger' : 
                                     ($record['status'] == 'late' ? 'warning' : 'info')); 
                            ?>">
                                <?php echo ucfirst($record['status']); ?>
                            </span>
                        </td>
                        <td><?php echo htmlspecialchars($record['subject_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($record['teacher_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($record['remarks'] ?: 'No remarks'); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">Wax imaansha ah bishan ma lahan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
