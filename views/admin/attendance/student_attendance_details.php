<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php'; 

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

// Get theme setting
$theme_mode = 1; // default light
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1"); 
if($themeRes && $themeRes->num_rows > 0){
    $themeRow = $themeRes->fetch_assoc();
    $theme_mode = (int)$themeRow['mode'];
}
$isDark = ($theme_mode === 0);

// Get student ID from URL
$student_id = isset($_GET['student_id']) ? (int)$_GET['student_id'] : 0;

if ($student_id <= 0) {
    header("Location: students.php");
    exit();
}

// Get student basic information
$studentStmt = $conn->prepare("
    SELECT s.*, 
           c.name as class_name, 
           sec.name as section_name,
           cs.academic_year,
           CONCAT(t.first_name, ' ', t.last_name) as class_teacher
    FROM students s
    LEFT JOIN class_students cs ON s.id = cs.student_id
    LEFT JOIN classes c ON cs.class_id = c.id
    LEFT JOIN sections sec ON c.section_id = sec.id
    LEFT JOIN teachers t ON c.id = t.id
    WHERE s.id = ?
    LIMIT 1
");
$studentStmt->bind_param("i", $student_id);
$studentStmt->execute();
$studentResult = $studentStmt->get_result();

if ($studentResult->num_rows === 0) {
    header("Location: students.php");
    exit();
}

$student = $studentResult->fetch_assoc();
$studentStmt->close();

// Get attendance statistics
function getAttendanceStats($conn, $student_id) {
    $stats = [];
    
    // Overall statistics
    $stmt = $conn->prepare("
        SELECT 
            status,
            COUNT(*) as count,
            MONTH(date) as month,
            YEAR(date) as year
        FROM attendance 
        WHERE student_id = ?
        GROUP BY status, MONTH(date), YEAR(date)
        ORDER BY year DESC, month DESC
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $monthly_stats = [];
    $overall = [
        'present' => 0,
        'absent' => 0,
        'late' => 0,
        'excused' => 0,
        'total' => 0
    ];
    
    while ($row = $result->fetch_assoc()) {
        $month_key = $row['year'] . '-' . str_pad($row['month'], 2, '0', STR_PAD_LEFT);
        if (!isset($monthly_stats[$month_key])) {
            $monthly_stats[$month_key] = [
                'present' => 0,
                'absent' => 0,
                'late' => 0,
                'excused' => 0,
                'total' => 0
            ];
        }
        
        $monthly_stats[$month_key][$row['status']] = (int)$row['count'];
        $monthly_stats[$month_key]['total'] += (int)$row['count'];
        
        $overall[$row['status']] += (int)$row['count'];
        $overall['total'] += (int)$row['count'];
    }
    
    $overall['percentage'] = $overall['total'] > 0 
        ? round((($overall['present'] + $overall['excused']) / $overall['total']) * 100, 2)
        : 0;
    
    $stmt->close();
    
    // Get current month stats
    $current_month = date('Y-m');
    $current_stats = isset($monthly_stats[$current_month]) 
        ? $monthly_stats[$current_month] 
        : ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0, 'total' => 0];
    
    $current_stats['percentage'] = $current_stats['total'] > 0 
        ? round((($current_stats['present'] + $current_stats['excused']) / $current_stats['total']) * 100, 2)
        : 0;
    
    // Get attendance by subject
    $stmt = $conn->prepare("
        SELECT 
            sub.name as subject_name,
            COUNT(*) as total_classes,
            SUM(CASE WHEN a.status = 'present' THEN 1 ELSE 0 END) as present,
            SUM(CASE WHEN a.status = 'absent' THEN 1 ELSE 0 END) as absent,
            SUM(CASE WHEN a.status = 'late' THEN 1 ELSE 0 END) as late,
            SUM(CASE WHEN a.status = 'excused' THEN 1 ELSE 0 END) as excused
        FROM attendance a
        LEFT JOIN subjects sub ON a.subject_id = sub.id
        WHERE a.student_id = ? AND a.subject_id IS NOT NULL
        GROUP BY a.subject_id, sub.name
        ORDER BY total_classes DESC
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $subject_stats = [];
    while ($row = $result->fetch_assoc()) {
        $row['percentage'] = $row['total_classes'] > 0 
            ? round((($row['present'] + $row['excused']) / $row['total_classes']) * 100, 2)
            : 0;
        $subject_stats[] = $row;
    }
    $stmt->close();
    
    // Get streak information
    $stmt = $conn->prepare("
        SELECT 
            date,
            status,
            CASE 
                WHEN status IN ('present', 'excused') THEN 1
                ELSE 0
            END as attended
        FROM attendance 
        WHERE student_id = ?
        ORDER BY date DESC
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $streak = 0;
    $longest_streak = 0;
    $current_streak = 0;
    $last_date = null;
    $dates = [];
    
    while ($row = $result->fetch_assoc()) {
        $dates[] = $row;
        if ($row['attended'] == 1) {
            $current_streak++;
            $longest_streak = max($longest_streak, $current_streak);
        } else {
            if ($streak === 0) {
                $streak = $current_streak;
            }
            $current_streak = 0;
        }
    }
    
    if ($streak === 0 && $current_streak > 0) {
        $streak = $current_streak;
    }
    
    $stmt->close();
    
    // Get recent attendance pattern
    $recent_pattern = array_slice($dates, 0, min(30, count($dates)));
    
    return [
        'overall' => $overall,
        'current_month' => $current_stats,
        'monthly' => $monthly_stats,
        'subjects' => $subject_stats,
        'streak' => $streak,
        'longest_streak' => $longest_streak,
        'pattern' => $recent_pattern
    ];
}

// Get detailed attendance records
function getAttendanceDetails($conn, $student_id, $limit = 100) {
    $stmt = $conn->prepare("
        SELECT 
            a.*,
            sub.name as subject_name,
            c.name as class_name,
            sec.name as section_name,
            DATE_FORMAT(a.date, '%W') as day_name,
            cs.day_of_week as scheduled_day,
            cs.start_time as scheduled_start,
            cs.end_time as scheduled_end
        FROM attendance a
        LEFT JOIN subjects sub ON a.subject_id = sub.id
        LEFT JOIN classes c ON a.class_id = c.id
        LEFT JOIN sections sec ON a.section_id = sec.id
        LEFT JOIN class_subjects cs ON a.subject_id = cs.subject_id 
            AND a.class_id = cs.class_id
            AND a.date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
        WHERE a.student_id = ?
        ORDER BY a.date DESC, a.recorded_at DESC
        LIMIT ?
    ");
    $stmt->bind_param("ii", $student_id, $limit);
    $stmt->execute();
    $result = $stmt->get_result();
    $records = [];
    while ($row = $result->fetch_assoc()) {
        $records[] = $row;
    }
    $stmt->close();
    return $records;
}

// Get attendance history (changes)
function getAttendanceHistory($conn, $student_id) {
    $stmt = $conn->prepare("
        SELECT 
            ah.*,
            a.date,
            s.name as student_name,
            sub.name as subject_name
        FROM attendance_history ah
        JOIN attendance a ON ah.attendance_id = a.id
        JOIN students s ON ah.student_id = s.id
        LEFT JOIN subjects sub ON a.subject_id = sub.id
        WHERE ah.student_id = ?
        ORDER BY ah.changed_at DESC
        LIMIT 50
    ");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $history = [];
    while ($row = $result->fetch_assoc()) {
        $history[] = $row;
    }
    $stmt->close();
    return $history;
}

// Get all data
$attendance_stats = getAttendanceStats($conn, $student_id);
$attendance_details = getAttendanceDetails($conn, $student_id, 200);
$attendance_history = getAttendanceHistory($conn, $student_id);

// Get academic year start and end dates
$current_year = date('Y');
$academic_start = $current_year . '-09-01'; // Assuming September start
$academic_end = ($current_year + 1) . '-06-30'; // Assuming June end

// Calculate attendance trend
$trend_data = [];
foreach ($attendance_stats['monthly'] as $month => $data) {
    if ($data['total'] > 0) {
        $percentage = round((($data['present'] + $data['excused']) / $data['total']) * 100, 1);
        $trend_data[] = [
            'month' => date('M Y', strtotime($month . '-01')),
            'percentage' => $percentage,
            'present' => $data['present'],
            'total' => $data['total']
        ];
    }
}
usort($trend_data, function($a, $b) {
    return strtotime($a['month']) - strtotime($b['month']);
});

// Get recent absence reasons
$absence_reasons = [];
foreach ($attendance_details as $record) {
    if (in_array($record['status'], ['absent', 'late']) && !empty($record['remarks'])) {
        $absence_reasons[] = [
            'date' => $record['date'],
            'status' => $record['status'],
            'reason' => $record['remarks']
        ];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($student['name']); ?> - Attendance Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        :root {
            --bg-color: #f8fafc;
            --text-color: #1e293b;
            --card-bg: #ffffff;
            --border-color: #e2e8f0;
            --primary-color: #3b82f6;
            --primary-light: #eff6ff;
            --secondary-color: #64748b;
            --success-color: #10b981;
            --success-light: #d1fae5;
            --danger-color: #ef4444;
            --danger-light: #fee2e2;
            --warning-color: #f59e0b;
            --warning-light: #fef3c7;
            --info-color: #06b6d4;
            --info-light: #cffafe;
            --purple: #8b5cf6;
            --pink: #ec4899;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-700: #334155;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        .dark {
            --bg-color: #0f172a;
            --text-color: #f1f5f9;
            --card-bg: #1e293b;
            --border-color: #334155;
            --primary-color: #60a5fa;
            --primary-light: rgba(96, 165, 250, 0.1);
            --secondary-color: #94a3b8;
            --success-color: #34d399;
            --success-light: rgba(52, 211, 153, 0.1);
            --danger-color: #f87171;
            --danger-light: rgba(248, 113, 113, 0.1);
            --warning-color: #fbbf24;
            --warning-light: rgba(251, 191, 36, 0.1);
            --info-color: #22d3ee;
            --info-light: rgba(34, 211, 238, 0.1);
            --gray-100: #1e293b;
            --gray-200: #334155;
            --gray-300: #475569;
            --gray-700: #cbd5e1;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            line-height: 1.6;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background-color: var(--card-bg);
            color: var(--primary-color);
            text-decoration: none;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            font-weight: 500;
            transition: all 0.3s;
        }

        .back-btn:hover {
            background-color: var(--primary-light);
            transform: translateX(-3px);
            box-shadow: var(--shadow);
        }

        .student-header {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .student-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--purple));
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2em;
            font-weight: bold;
            box-shadow: var(--shadow-lg);
        }

        .student-info h1 {
            font-size: 1.8em;
            color: var(--text-color);
            margin-bottom: 5px;
        }

        .student-meta {
            display: flex;
            gap: 15px;
            color: var(--secondary-color);
            font-size: 0.95em;
        }

        .student-meta span {
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background-color: var(--card-bg);
            border-radius: 12px;
            padding: 25px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border-color);
            transition: transform 0.3s, box-shadow 0.3s;
            position: relative;
            overflow: hidden;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--purple));
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .stat-title {
            font-size: 0.9em;
            color: var(--secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        .stat-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2em;
        }

        .stat-icon.present { background-color: var(--success-light); color: var(--success-color); }
        .stat-icon.absent { background-color: var(--danger-light); color: var(--danger-color); }
        .stat-icon.late { background-color: var(--warning-light); color: var(--warning-color); }
        .stat-icon.excused { background-color: var(--info-light); color: var(--info-color); }
        .stat-icon.overall { background-color: var(--primary-light); color: var(--primary-color); }

        .stat-value {
            font-size: 2.5em;
            font-weight: bold;
            line-height: 1;
            margin-bottom: 10px;
        }

        .stat-detail {
            font-size: 0.9em;
            color: var(--secondary-color);
        }

        .percentage-circle {
            position: relative;
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
        }

        .percentage-svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .percentage-circle-bg {
            fill: none;
            stroke: var(--gray-200);
            stroke-width: 8;
        }

        .percentage-circle-fill {
            fill: none;
            stroke-width: 8;
            stroke-linecap: round;
            transition: stroke-dasharray 1s ease;
        }

        .percentage-circle-fill.high { stroke: var(--success-color); }
        .percentage-circle-fill.medium { stroke: var(--warning-color); }
        .percentage-circle-fill.low { stroke: var(--danger-color); }

        .percentage-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .percentage-value {
            font-size: 2em;
            font-weight: bold;
            line-height: 1;
        }

        .percentage-label {
            font-size: 0.85em;
            color: var(--secondary-color);
            margin-top: 5px;
        }

        /* Navigation Tabs */
        .nav-tabs {
            display: flex;
            border-bottom: 2px solid var(--border-color);
            margin-bottom: 30px;
            overflow-x: auto;
            padding-bottom: 2px;
        }

        .tab-btn {
            padding: 15px 25px;
            background: none;
            border: none;
            color: var(--secondary-color);
            cursor: pointer;
            font-size: 15px;
            font-weight: 500;
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
        }

        .tab-btn.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
            background-color: var(--primary-light);
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

        /* Section Cards */
        .section-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--shadow);
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
            display: flex;
            align-items: center;
            gap: 10px;
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            border: 1px solid var(--border-color);
            background-color: var(--card-bg);
        }

        .attendance-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .attendance-table th {
            background-color: var(--primary-light);
            color: var(--primary-color);
            font-weight: 600;
            padding: 15px;
            text-align: left;
            position: sticky;
            top: 0;
            border-bottom: 2px solid var(--border-color);
        }

        .attendance-table td {
            padding: 15px;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        .attendance-table tr:hover {
            background-color: var(--primary-light);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 0.85em;
            font-weight: 600;
            border: 1px solid transparent;
        }

        .status-present {
            background-color: var(--success-light);
            color: var(--success-color);
            border-color: var(--success-color);
        }

        .status-absent {
            background-color: var(--danger-light);
            color: var(--danger-color);
            border-color: var(--danger-color);
        }

        .status-late {
            background-color: var(--warning-light);
            color: var(--warning-color);
            border-color: var(--warning-color);
        }

        .status-excused {
            background-color: var(--info-light);
            color: var(--info-color);
            border-color: var(--info-color);
        }

        .status-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            display: inline-block;
        }

        .dot-present { background-color: var(--success-color); }
        .dot-absent { background-color: var(--danger-color); }
        .dot-late { background-color: var(--warning-color); }
        .dot-excused { background-color: var(--info-color); }

        /* Calendar View */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }

        .month-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
        }

        .month-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .month-title {
            font-weight: 600;
            color: var(--primary-color);
        }

        .days-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 5px;
            margin-top: 10px;
        }

        .day-header {
            text-align: center;
            font-size: 0.8em;
            color: var(--secondary-color);
            padding: 5px;
            font-weight: 600;
        }

        .day-cell {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            font-size: 0.85em;
            position: relative;
            cursor: default;
        }

        .day-cell.empty {
            background: transparent;
        }

        .day-cell.present {
            background-color: var(--success-light);
            color: var(--success-color);
            font-weight: 600;
        }

        .day-cell.absent {
            background-color: var(--danger-light);
            color: var(--danger-color);
            font-weight: 600;
        }

        .day-cell.late {
            background-color: var(--warning-light);
            color: var(--warning-color);
            font-weight: 600;
        }

        .day-cell.excused {
            background-color: var(--info-light);
            color: var(--info-color);
            font-weight: 600;
        }

        /* Subject Performance */
        .subject-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
        }

        .subject-card {
            background-color: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 10px;
            padding: 20px;
            transition: transform 0.3s;
        }

        .subject-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow);
        }

        .subject-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .subject-name {
            font-weight: 600;
            color: var(--primary-color);
        }

        .subject-stats {
            margin-top: 15px;
        }

        .stat-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
            font-size: 0.9em;
        }

        .stat-label {
            color: var(--secondary-color);
        }

        .stat-value {
            font-weight: 600;
            color: var(--text-color);
        }

        /* Progress Bars */
        .progress-container {
            margin: 10px 0;
        }

        .progress-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 5px;
            font-size: 0.9em;
        }

        .progress-bar {
            height: 8px;
            background-color: var(--gray-200);
            border-radius: 4px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            transition: width 0.5s ease;
        }

        .progress-present { background-color: var(--success-color); }
        .progress-absent { background-color: var(--danger-color); }
        .progress-late { background-color: var(--warning-color); }
        .progress-excused { background-color: var(--info-color); }

        /* Trend Chart */
        .trend-container {
            height: 200px;
            position: relative;
            margin-top: 20px;
        }

        .trend-line {
            fill: none;
            stroke: var(--primary-color);
            stroke-width: 3;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        .trend-point {
            fill: var(--primary-color);
            stroke: white;
            stroke-width: 2;
            cursor: pointer;
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--secondary-color);
        }

        .empty-icon {
            font-size: 3em;
            margin-bottom: 20px;
            opacity: 0.3;
        }

        .empty-text {
            font-size: 1.1em;
            margin-bottom: 10px;
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            
            .section-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ddd;
            }
            
            body {
                background: white;
                color: black;
            }
        }

        /* Responsive Styles */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .student-header {
                flex-direction: column;
                text-align: center;
            }
            
            .student-meta {
                justify-content: center;
                flex-wrap: wrap;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .nav-tabs {
                flex-wrap: wrap;
            }
            
            .tab-btn {
                flex: 1;
                min-width: 120px;
                justify-content: center;
                padding: 12px;
                font-size: 14px;
            }
            
            .attendance-table th,
            .attendance-table td {
                padding: 10px;
                font-size: 0.9em;
            }
            
            .subject-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Dark mode adjustments */
        .dark .percentage-circle-bg {
            stroke: var(--gray-700);
        }
    </style>
</head>
<body class="<?php echo $isDark ? 'dark' : 'light'; ?>">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <a href="javascript:history.back()" class="back-btn">
                <i class="fas fa-arrow-left"></i> Back
            </a>
            
            <div class="student-header">
                <div class="student-avatar">
                    <?php echo strtoupper(substr($student['name'], 0, 2)); ?>
                </div>
                <div class="student-info">
                    <h1><?php echo htmlspecialchars($student['name']); ?></h1>
                    <div class="student-meta">
                        <span><i class="fas fa-id-card"></i> ID: <?php echo $student['id']; ?></span>
                        <span><i class="fas fa-<?php echo $student['gender'] == 'male' ? 'mars' : 'venus'; ?>"></i> <?php echo ucfirst($student['gender']); ?></span>
                        <span><i class="fas fa-graduation-cap"></i> <?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?></span>
                        <span><i class="fas fa-calendar-alt"></i> <?php echo htmlspecialchars($student['academic_year'] ?? 'N/A'); ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Overall Attendance</div>
                    <div class="stat-icon overall"><i class="fas fa-chart-line"></i></div>
                </div>
                <div class="percentage-circle">
                    <svg class="percentage-svg" viewBox="0 0 36 36">
                        <path class="percentage-circle-bg"
                              d="M18 2.0845
                                 a 15.9155 15.9155 0 0 1 0 31.831
                                 a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                        <path class="percentage-circle-fill <?php 
                            echo $attendance_stats['overall']['percentage'] >= 90 ? 'high' : 
                                ($attendance_stats['overall']['percentage'] >= 75 ? 'medium' : 'low'); 
                        ?>"
                              stroke-dasharray="<?php echo $attendance_stats['overall']['percentage']; ?>, 100"
                              d="M18 2.0845
                                 a 15.9155 15.9155 0 0 1 0 31.831
                                 a 15.9155 15.9155 0 0 1 0 -31.831"
                        />
                    </svg>
                    <div class="percentage-text">
                        <div class="percentage-value"><?php echo $attendance_stats['overall']['percentage']; ?>%</div>
                        <div class="percentage-label"><?php echo $attendance_stats['overall']['total']; ?> days</div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Current Month</div>
                    <div class="stat-icon overall"><i class="fas fa-calendar"></i></div>
                </div>
                <div class="stat-value"><?php echo $attendance_stats['current_month']['percentage']; ?>%</div>
                <div class="stat-detail">
                    <div class="progress-container">
                        <div class="progress-label">
                            <span>Present: <?php echo $attendance_stats['current_month']['present']; ?></span>
                            <span>Total: <?php echo $attendance_stats['current_month']['total']; ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill progress-present" 
                                 style="width: <?php echo $attendance_stats['current_month']['total'] > 0 ? 
                                    ($attendance_stats['current_month']['present'] / $attendance_stats['current_month']['total']) * 100 : 0; ?>%">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Current Streak</div>
                    <div class="stat-icon present"><i class="fas fa-fire"></i></div>
                </div>
                <div class="stat-value"><?php echo $attendance_stats['streak']; ?> days</div>
                <div class="stat-detail">
                    Longest: <?php echo $attendance_stats['longest_streak']; ?> days
                </div>
            </div>
            
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title">Breakdown</div>
                    <div class="stat-icon overall"><i class="fas fa-chart-pie"></i></div>
                </div>
                <div class="stat-detail">
                    <div class="stat-row">
                        <span class="status-badge status-present">
                            <span class="status-dot dot-present"></span> Present
                        </span>
                        <span><?php echo $attendance_stats['overall']['present']; ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="status-badge status-absent">
                            <span class="status-dot dot-absent"></span> Absent
                        </span>
                        <span><?php echo $attendance_stats['overall']['absent']; ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="status-badge status-late">
                            <span class="status-dot dot-late"></span> Late
                        </span>
                        <span><?php echo $attendance_stats['overall']['late']; ?></span>
                    </div>
                    <div class="stat-row">
                        <span class="status-badge status-excused">
                            <span class="status-dot dot-excused"></span> Excused
                        </span>
                        <span><?php echo $attendance_stats['overall']['excused']; ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation Tabs -->
        <div class="nav-tabs">
            <button class="tab-btn active" onclick="showTab('detailed')">
                <i class="fas fa-list-alt"></i> Detailed Records
            </button>
            <button class="tab-btn" onclick="showTab('calendar')">
                <i class="fas fa-calendar"></i> Calendar View
            </button>
            <button class="tab-btn" onclick="showTab('subjects')">
                <i class="fas fa-book"></i> By Subjects
            </button>
            <button class="tab-btn" onclick="showTab('trends')">
                <i class="fas fa-chart-line"></i> Trends
            </button>
            <button class="tab-btn" onclick="showTab('history')">
                <i class="fas fa-history"></i> History
            </button>
            <button class="tab-btn no-print" onclick="window.print()">
                <i class="fas fa-print"></i> Print
            </button>
        </div>

        <!-- Detailed Records Tab -->
        <div id="detailed-tab" class="tab-content active">
            <div class="section-card">
                <div class="section-title">
                    <h3><i class="fas fa-list-alt"></i> Attendance Records</h3>
                    <span class="badge"><?php echo count($attendance_details); ?> records</span>
                </div>
                
                <?php if (!empty($attendance_details)): ?>
                    <div class="table-container">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Day</th>
                                    <th>Status</th>
                                    <th>Subject</th>
                                    <th>Schedule</th>
                                    <th>Remarks</th>
                                    <th>Recorded By</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance_details as $record): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo date('M d, Y', strtotime($record['date'])); ?></strong>
                                        </td>
                                        <td><?php echo $record['day_name']; ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo $record['status']; ?>">
                                                <span class="status-dot dot-<?php echo $record['status']; ?>"></span>
                                                <?php echo ucfirst($record['status']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($record['subject_name']): ?>
                                                <?php echo htmlspecialchars($record['subject_name']); ?>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">General</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($record['scheduled_start']): ?>
                                                <small><?php echo date('h:i A', strtotime($record['scheduled_start'])); ?> - 
                                                       <?php echo date('h:i A', strtotime($record['scheduled_end'])); ?></small>
                                            <?php else: ?>
                                                <span style="color: var(--secondary-color);">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($record['remarks'] ?? '-'); ?></td>
                                        <td><?php echo htmlspecialchars($record['recorded_by'] ?? '-'); ?></td>
                                        <td>
                                            <small><?php echo date('h:i A', strtotime($record['recorded_at'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="empty-text">No attendance records found</div>
                        <p>Attendance records will appear here once recorded.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Calendar View Tab -->
        <div id="calendar-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><i class="fas fa-calendar"></i> Monthly Calendar View</h3>
                    <div>
                        <button class="btn" onclick="previousMonth()"><i class="fas fa-chevron-left"></i></button>
                        <span id="current-month" style="margin: 0 10px;"><?php echo date('F Y'); ?></span>
                        <button class="btn" onclick="nextMonth()"><i class="fas fa-chevron-right"></i></button>
                    </div>
                </div>
                
                <?php 
                // Group attendance by date for easier calendar display
                $attendance_by_date = [];
                foreach ($attendance_details as $record) {
                    $date = $record['date'];
                    if (!isset($attendance_by_date[$date])) {
                        $attendance_by_date[$date] = $record;
                    }
                }
                ?>
                
                <div id="calendar-container">
                    <!-- Calendar will be generated by JavaScript -->
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-calendar-alt"></i></div>
                        <div class="empty-text">Calendar view will be generated here</div>
                        <p>JavaScript is required for interactive calendar.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- By Subjects Tab -->
        <div id="subjects-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><i class="fas fa-book"></i> Attendance by Subject</h3>
                    <span class="badge"><?php echo count($attendance_stats['subjects']); ?> subjects</span>
                </div>
                
                <?php if (!empty($attendance_stats['subjects'])): ?>
                    <div class="subject-grid">
                        <?php foreach ($attendance_stats['subjects'] as $subject): ?>
                            <div class="subject-card">
                                <div class="subject-header">
                                    <div class="subject-name"><?php echo htmlspecialchars($subject['subject_name']); ?></div>
                                    <div class="percentage-value"><?php echo $subject['percentage']; ?>%</div>
                                </div>
                                
                                <div class="subject-stats">
                                    <div class="stat-row">
                                        <span class="stat-label">Total Classes:</span>
                                        <span class="stat-value"><?php echo $subject['total_classes']; ?></span>
                                    </div>
                                    
                                    <div class="progress-container">
                                        <div class="progress-label">
                                            <span>Present</span>
                                            <span><?php echo $subject['present']; ?></span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill progress-present" 
                                                 style="width: <?php echo $subject['total_classes'] > 0 ? 
                                                    ($subject['present'] / $subject['total_classes']) * 100 : 0; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-container">
                                        <div class="progress-label">
                                            <span>Absent</span>
                                            <span><?php echo $subject['absent']; ?></span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill progress-absent" 
                                                 style="width: <?php echo $subject['total_classes'] > 0 ? 
                                                    ($subject['absent'] / $subject['total_classes']) * 100 : 0; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="progress-container">
                                        <div class="progress-label">
                                            <span>Late</span>
                                            <span><?php echo $subject['late']; ?></span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill progress-late" 
                                                 style="width: <?php echo $subject['total_classes'] > 0 ? 
                                                    ($subject['late'] / $subject['total_classes']) * 100 : 0; ?>%">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-book"></i></div>
                        <div class="empty-text">No subject-specific attendance data</div>
                        <p>Attendance by subject will appear here when available.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Trends Tab -->
        <div id="trends-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><i class="fas fa-chart-line"></i> Attendance Trends</h3>
                    <span class="badge">Monthly Analysis</span>
                </div>
                
                <?php if (!empty($trend_data)): ?>
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px; margin-top: 20px;">
                        <div>
                            <h4 style="color: var(--primary-color); margin-bottom: 15px;">Monthly Performance</h4>
                            <div style="height: 250px; position: relative;">
                                <canvas id="trendChart"></canvas>
                            </div>
                        </div>
                        
                        <div>
                            <h4 style="color: var(--primary-color); margin-bottom: 15px;">Monthly Breakdown</h4>
                            <div style="max-height: 300px; overflow-y: auto;">
                                <?php foreach (array_reverse($trend_data) as $month_data): ?>
                                    <div style="margin-bottom: 15px; padding-bottom: 15px; border-bottom: 1px solid var(--border-color);">
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span style="font-weight: 600;"><?php echo $month_data['month']; ?></span>
                                            <span style="font-weight: 600; color: 
                                                <?php 
                                                if ($month_data['percentage'] >= 90) echo 'var(--success-color)';
                                                elseif ($month_data['percentage'] >= 75) echo 'var(--warning-color)';
                                                else echo 'var(--danger-color)';
                                                ?>">
                                                <?php echo $month_data['percentage']; ?>%
                                            </span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill progress-present" 
                                                 style="width: <?php echo $month_data['percentage']; ?>%"></div>
                                        </div>
                                        <div style="font-size: 0.85em; color: var(--secondary-color); margin-top: 5px;">
                                            <?php echo $month_data['present']; ?> present of <?php echo $month_data['total']; ?> days
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pattern Analysis -->
                    <div style="margin-top: 30px;">
                        <h4 style="color: var(--primary-color); margin-bottom: 15px;">Pattern Analysis</h4>
                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                            <?php
                            // Calculate patterns
                            $day_patterns = ['Monday' => 0, 'Tuesday' => 0, 'Wednesday' => 0, 
                                           'Thursday' => 0, 'Friday' => 0, 'Saturday' => 0, 'Sunday' => 0];
                            $day_totals = array_fill_keys(array_keys($day_patterns), 0);
                            
                            foreach ($attendance_details as $record) {
                                $day = date('l', strtotime($record['date']));
                                if (isset($day_patterns[$day])) {
                                    $day_totals[$day]++;
                                    if ($record['status'] == 'present') {
                                        $day_patterns[$day]++;
                                    }
                                }
                            }
                            ?>
                            
                            <?php foreach ($day_patterns as $day => $present_count): ?>
                                <?php if ($day_totals[$day] > 0): ?>
                                    <?php $day_percentage = ($present_count / $day_totals[$day]) * 100; ?>
                                    <div>
                                        <div style="display: flex; justify-content: space-between; margin-bottom: 5px;">
                                            <span><?php echo substr($day, 0, 3); ?></span>
                                            <span style="font-weight: 600;"><?php echo round($day_percentage, 1); ?>%</span>
                                        </div>
                                        <div class="progress-bar">
                                            <div class="progress-fill 
                                                <?php 
                                                if ($day_percentage >= 90) echo 'progress-present';
                                                elseif ($day_percentage >= 75) echo 'progress-late';
                                                else echo 'progress-absent';
                                                ?>" 
                                                style="width: <?php echo $day_percentage; ?>%">
                                            </div>
                                        </div>
                                        <div style="font-size: 0.85em; color: var(--secondary-color); margin-top: 5px;">
                                            <?php echo $present_count; ?>/<?php echo $day_totals[$day]; ?>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-chart-line"></i></div>
                        <div class="empty-text">No trend data available</div>
                        <p>Trend analysis requires multiple months of attendance data.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- History Tab -->
        <div id="history-tab" class="tab-content">
            <div class="section-card">
                <div class="section-title">
                    <h3><i class="fas fa-history"></i> Attendance History (Changes)</h3>
                    <span class="badge"><?php echo count($attendance_history); ?> changes</span>
                </div>
                
                <?php if (!empty($attendance_history)): ?>
                    <div class="table-container">
                        <table class="attendance-table">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Subject</th>
                                    <th>Change</th>
                                    <th>Changed By</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($attendance_history as $history): ?>
                                    <tr>
                                        <td><?php echo date('M d, Y', strtotime($history['date'])); ?></td>
                                        <td><?php echo htmlspecialchars($history['subject_name'] ?? 'General'); ?></td>
                                        <td>
                                            <div style="display: flex; align-items: center; gap: 10px;">
                                                <span class="status-badge" style="background-color: var(--danger-light); color: var(--danger-color);">
                                                    <?php echo ucfirst($history['old_status']); ?>
                                                </span>
                                                <i class="fas fa-arrow-right" style="color: var(--secondary-color);"></i>
                                                <span class="status-badge" style="background-color: var(--success-light); color: var(--success-color);">
                                                    <?php echo ucfirst($history['new_status']); ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td><?php echo htmlspecialchars($history['recorded_by']); ?></td>
                                        <td>
                                            <small><?php echo date('M d, Y h:i A', strtotime($history['changed_at'])); ?></small>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon"><i class="fas fa-history"></i></div>
                        <div class="empty-text">No attendance changes recorded</div>
                        <p>Attendance history tracks any modifications made to records.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Chart.js for graphs -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
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
            
            // Initialize charts if needed
            if (tabName === 'trends') {
                initializeTrendChart();
            } else if (tabName === 'calendar') {
                generateCalendar();
            }
        }
        
        // Check for tab parameter in URL on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const tabParam = urlParams.get('tab');
            if (tabParam) {
                showTabByName(tabParam);
            } else {
                // Initialize charts for active tab
                initializeTrendChart();
                generateCalendar();
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
                
                // Initialize charts if needed
                if (tabName === 'trends') {
                    initializeTrendChart();
                } else if (tabName === 'calendar') {
                    generateCalendar();
                }
            }
        }
        
        // Calendar functionality
        let currentMonth = new Date().getMonth();
        let currentYear = new Date().getFullYear();
        
        function generateCalendar() {
            const container = document.getElementById('calendar-container');
            if (!container) return;
            
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June',
                              'July', 'August', 'September', 'October', 'November', 'December'];
            
            // Update month display
            document.getElementById('current-month').textContent = 
                `${monthNames[currentMonth]} ${currentYear}`;
            
            // Create calendar
            const firstDay = new Date(currentYear, currentMonth, 1);
            const lastDay = new Date(currentYear, currentMonth + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startingDay = firstDay.getDay();
            
            // Prepare attendance data
            const attendanceData = <?php echo json_encode($attendance_by_date); ?>;
            
            // Generate calendar HTML
            let calendarHTML = `
                <div class="days-grid">
                    <div class="day-header">Sun</div>
                    <div class="day-header">Mon</div>
                    <div class="day-header">Tue</div>
                    <div class="day-header">Wed</div>
                    <div class="day-header">Thu</div>
                    <div class="day-header">Fri</div>
                    <div class="day-header">Sat</div>
            `;
            
            // Empty cells for days before the first day of the month
            for (let i = 0; i < startingDay; i++) {
                calendarHTML += '<div class="day-cell empty"></div>';
            }
            
            // Days of the month
            for (let day = 1; day <= daysInMonth; day++) {
                const dateString = `${currentYear}-${String(currentMonth + 1).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
                const attendance = attendanceData[dateString];
                
                let cellClass = 'day-cell';
                let dayContent = day;
                
                if (attendance) {
                    cellClass += ` ${attendance.status}`;
                    dayContent = `
                        <div style="position: relative;">
                            ${day}
                            <div style="position: absolute; top: -5px; right: -5px; width: 8px; height: 8px; 
                                      background-color: ${getStatusColor(attendance.status)}; 
                                      border-radius: 50%;"></div>
                        </div>
                    `;
                }
                
                calendarHTML += `<div class="${cellClass}">${dayContent}</div>`;
                
                // Break line after Saturday
                if ((day + startingDay) % 7 === 0 && day !== daysInMonth) {
                    calendarHTML += '</div><div class="days-grid">';
                }
            }
            
            // Fill remaining cells
            const totalCells = 42; // 6 weeks * 7 days
            const filledCells = startingDay + daysInMonth;
            for (let i = filledCells; i < totalCells; i++) {
                calendarHTML += '<div class="day-cell empty"></div>';
            }
            
            calendarHTML += '</div>';
            
            // Add legend
            calendarHTML += `
                <div style="margin-top: 20px; display: flex; gap: 15px; flex-wrap: wrap;">
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="day-cell present" style="width: 20px; height: 20px;"></div>
                        <span>Present</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="day-cell absent" style="width: 20px; height: 20px;"></div>
                        <span>Absent</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="day-cell late" style="width: 20px; height: 20px;"></div>
                        <span>Late</span>
                    </div>
                    <div style="display: flex; align-items: center; gap: 5px;">
                        <div class="day-cell excused" style="width: 20px; height: 20px;"></div>
                        <span>Excused</span>
                    </div>
                </div>
            `;
            
            container.innerHTML = calendarHTML;
        }
        
        function getStatusColor(status) {
            const colors = {
                'present': '#10b981',
                'absent': '#ef4444',
                'late': '#f59e0b',
                'excused': '#06b6d4'
            };
            return colors[status] || '#94a3b8';
        }
        
        function previousMonth() {
            currentMonth--;
            if (currentMonth < 0) {
                currentMonth = 11;
                currentYear--;
            }
            generateCalendar();
        }
        
        function nextMonth() {
            currentMonth++;
            if (currentMonth > 11) {
                currentMonth = 0;
                currentYear++;
            }
            generateCalendar();
        }
        
        // Trend Chart
        let trendChart = null;
        
        function initializeTrendChart() {
            const ctx = document.getElementById('trendChart');
            if (!ctx) return;
            
            // Destroy existing chart
            if (trendChart) {
                trendChart.destroy();
            }
            
            const trendData = <?php echo json_encode($trend_data); ?>;
            
            const labels = trendData.map(item => item.month);
            const percentages = trendData.map(item => item.percentage);
            const presentCounts = trendData.map(item => item.present);
            const totalCounts = trendData.map(item => item.total);
            
            // Create gradient
            const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 250);
            gradient.addColorStop(0, 'rgba(59, 130, 246, 0.8)');
            gradient.addColorStop(1, 'rgba(59, 130, 246, 0.1)');
            
            trendChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Attendance Percentage',
                        data: percentages,
                        borderColor: 'rgb(59, 130, 246)',
                        backgroundColor: gradient,
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: 'rgb(59, 130, 246)',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const index = context.dataIndex;
                                    return [
                                        `Attendance: ${percentages[index]}%`,
                                        `Present: ${presentCounts[index]}/${totalCounts[index]} days`
                                    ];
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
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        },
                        x: {
                            grid: {
                                color: 'rgba(0, 0, 0, 0.1)'
                            }
                        }
                    }
                }
            });
        }
        
        // Export functionality
        function exportToPDF() {
            // This would require a PDF generation library
            alert('PDF export functionality would be implemented here.');
        }
        
        function exportToExcel() {
            // This would require an Excel generation library
            alert('Excel export functionality would be implemented here.');
        }
        
        // Print optimization
        window.addEventListener('beforeprint', function() {
            document.querySelectorAll('.no-print').forEach(el => {
                el.style.display = 'none';
            });
        });
        
        window.addEventListener('afterprint', function() {
            document.querySelectorAll('.no-print').forEach(el => {
                el.style.display = '';
            });
        });
        
        // Initialize on page load
        generateCalendar();
        initializeTrendChart();
    </script>
</body>
</html>