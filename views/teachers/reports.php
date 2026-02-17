<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

/* ---------- TRANSLATION SYSTEM ---------- */
// Get user's language preference from teachers table
$user_id = $_SESSION['user_id'] ?? null;
$currentLangId = 1; // Default to English (id: 1)

if ($user_id) {
    $langQuery = $conn->prepare("SELECT language_id FROM teachers WHERE id = ?");
    $langQuery->bind_param("i", $user_id);
    $langQuery->execute();
    $result = $langQuery->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $currentLangId = $row['language_id'] ?? 1;
    }
}

// Function to get translation from database
function translate($key, $langId = null) {
    global $conn, $currentLangId;
    
    if ($langId === null) {
        $langId = $currentLangId;
    }
    
    // Prepare statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = ?");
    $stmt->bind_param("si", $key, $langId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        return $row['translation_value'];
    }
    
    // Fallback to English if translation not found
    if ($langId != 1) {
        $stmt = $conn->prepare("SELECT translation_value FROM translations WHERE translation_key = ? AND language_id = 1");
        $stmt->bind_param("s", $key);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return $row['translation_value'];
        }
    }
    
    // Return the key itself if no translation found
    return $key;
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// ------------------- GET THEME -------------------
 
// ------------------- GET TEACHER INFO -------------------
$teacher_id = $_SESSION['user_id'];
$teacherRes = $conn->query("SELECT * FROM teachers WHERE id = $teacher_id");
$teacher = $teacherRes->fetch_assoc();

// ------------------- SUMMARY DATA -------------------
// Total Subjects
$totalSubjects = $conn->query("SELECT COUNT(*) c FROM class_subjects WHERE teacher_id = $teacher_id")->fetch_assoc()['c'];

// Total Classes
$totalClasses = $conn->query("SELECT COUNT(DISTINCT class_id) c FROM class_subjects WHERE teacher_id = $teacher_id")->fetch_assoc()['c'];

// Total Students
$classIds = [];
$res = $conn->query("SELECT DISTINCT class_id FROM class_subjects WHERE teacher_id = $teacher_id");
while($row = $res->fetch_assoc()) $classIds[] = $row['class_id'];
$totalStudents = 0;
if(count($classIds) > 0){
    $ids = implode(',', $classIds);
    $totalStudents = $conn->query("SELECT COUNT(DISTINCT student_id) c FROM class_students WHERE class_id IN ($ids)")->fetch_assoc()['c'];
}

// Total Attendances
$totalAttendances = $conn->query("SELECT COUNT(*) c FROM attendance WHERE recorded_by = '{$teacher['first_name']} {$teacher['last_name']}'")->fetch_assoc()['c'];

// Day with most subjects
$dayRes = $conn->query("SELECT day_of_week, COUNT(*) c FROM class_subjects WHERE teacher_id = $teacher_id GROUP BY day_of_week ORDER BY c DESC LIMIT 1");
$mostBusyDay = $dayRes->num_rows > 0 ? $dayRes->fetch_assoc()['day_of_week'] : '-';

// ------------------- DATE RANGE FOR CHARTS -------------------
$startDate = isset($_GET['start_date']) ? $_GET['start_date'] : date('Y-m-01');
$endDate   = isset($_GET['end_date']) ? $_GET['end_date'] : date('Y-m-t');

// ------------------- TODAY'S SUBJECTS -------------------
$todayDay = date('l'); // Sunday, Monday, etc
$now = date('H:i:s');

$sql = "
SELECT 
    css.start_time,
    css.end_time,
    sub.name AS subject_name,
    c.name   AS class_name
FROM class_subject_schedule css
INNER JOIN class_subjects cs 
    ON cs.id = css.class_subject_id
INNER JOIN subjects sub 
    ON sub.id = cs.subject_id
INNER JOIN classes c 
    ON c.id = cs.class_id
WHERE cs.teacher_id = ?
  AND css.day_of_week = ?
  AND css.start_time != '00:00:00'
ORDER BY css.start_time ASC
";

$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $teacher_id, $todayDay);
$stmt->execute();
$result = $stmt->get_result();

$todaysSchedule = [];
$passedSubjects = [];

while ($row = $result->fetch_assoc()) {
    $todaysSchedule[] = $row;
    if ($row['end_time'] < $now) {
        $passedSubjects[] = $row;
    }
}

// ------------------- ATTENDANCE CHART DATA -------------------
$chartDataRes = $conn->query("
    SELECT s.name as subject_name, 
        SUM(a.status='present') as present,
        SUM(a.status='absent') as absent,
        SUM(a.status='late') as late,
        SUM(a.status='excused') as excused
    FROM attendance a
    JOIN subjects s ON a.subject_id = s.id
    JOIN class_subjects cs ON cs.subject_id = s.id
    WHERE cs.teacher_id = $teacher_id
    GROUP BY s.name
");

$chartLabels = [];
$chartPresent = [];
$chartAbsent = [];
$chartLate = [];
$chartExcused = [];
while($row = $chartDataRes->fetch_assoc()){
    $chartLabels[] = $row['subject_name'];
    $chartPresent[] = $row['present'];
    $chartAbsent[] = $row['absent'];
    $chartLate[] = $row['late'];
    $chartExcused[] = $row['excused'];
}

// ------------------- CLASS-WISE ATTENDANCE -------------------
$classAttendanceRes = $conn->query("
    SELECT cs.class_id, c.name AS class_name,
           SUM(CASE WHEN a.status='present' THEN 1 ELSE 0 END) AS present,
           SUM(CASE WHEN a.status='absent' THEN 1 ELSE 0 END) AS absent,
           SUM(CASE WHEN a.status='late' THEN 1 ELSE 0 END) AS late,
           SUM(CASE WHEN a.status='excused' THEN 1 ELSE 0 END) AS excused
    FROM class_subjects cs
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN attendance a 
        ON a.class_id = cs.class_id
        AND a.subject_id = cs.subject_id
    WHERE cs.teacher_id = $teacher_id
    GROUP BY cs.class_id, c.name
");

$classChartData = [];
while($row = $classAttendanceRes->fetch_assoc()){
    $classChartData[] = $row;
}

// Prepare JS arrays
$classLabels = array_map(fn($c)=>$c['class_name'], $classChartData);
$classPresent = array_map(fn($c)=>intval($c['present']), $classChartData);
$classAbsent  = array_map(fn($c)=>intval($c['absent']), $classChartData);
$classLate    = array_map(fn($c)=>intval($c['late']), $classChartData);
$classExcused = array_map(fn($c)=>intval($c['excused']), $classChartData);

// ------------------- LATE/EARLY ANALYSIS -------------------
$lateAnalysisRes = $conn->query("
    SELECT c.name AS class_name,
           ROUND(SUM(a.status='late')/COUNT(*)*100,2) AS late_percent
    FROM attendance a
    JOIN classes c ON a.class_id=c.id
    JOIN class_subjects cs ON cs.class_id=c.id
    WHERE cs.teacher_id=$teacher_id
    GROUP BY c.name
");

$today = date('Y-m-d');
$todayDay = date('l');


// ------------------- CLASSES WITHOUT ATTENDANCE TODAY -------------------
$noAttendanceRes = $conn->query("
    SELECT 
        c.name AS class_name,
        s.name AS subject_name,
        css.start_time,
        css.end_time
    FROM class_subject_schedule css
    JOIN class_subjects cs ON cs.id = css.class_subject_id
    JOIN classes c ON c.id = cs.class_id
    JOIN subjects s ON s.id = cs.subject_id
    LEFT JOIN attendance a 
        ON a.class_id = cs.class_id
       AND a.subject_id = cs.subject_id
       AND a.date = '$today'
    WHERE cs.teacher_id = $teacher_id
      AND css.day_of_week = '$todayDay'
      AND a.id IS NULL
    ORDER BY css.start_time
");

$noAttendanceClasses = [];
while ($row = $noAttendanceRes->fetch_assoc()) {
    $noAttendanceClasses[] = $row;
}


// ------------------- TODAY'S ATTENDANCE RECORDS -------------------
$todayDate = date('Y-m-d');

$classSummaryRes = $conn->query("
    SELECT 
        c.id AS class_id,
        c.name AS class_name,
        SUM(a.status='present') AS present,
        SUM(a.status='absent') AS absent,
        SUM(a.status='late') AS late,
        SUM(a.status='excused') AS excused,
        SUM(a.recorded_by = '{$teacher['first_name']} {$teacher['last_name']}') AS marked_by_me
    FROM class_subjects cs
    JOIN classes c ON c.id = cs.class_id
    LEFT JOIN attendance a 
        ON a.class_id = cs.class_id 
        AND a.subject_id = cs.subject_id
        AND a.date = '$todayDate'
    WHERE cs.teacher_id = $teacher_id
    GROUP BY cs.class_id, c.name
    ORDER BY c.name
");

if(!$classSummaryRes){
    die("Class-wise attendance query failed: " . $conn->error);
}

$classSummary = [];
while($row = $classSummaryRes->fetch_assoc()) {
    $classSummary[] = $row;
}

// ------------------- TOP/BOTTOM STUDENTS -------------------
$topStudents = $conn->query("
SELECT s.name, COUNT(a.id) AS present_count
FROM class_students cs
JOIN students s ON s.id = cs.student_id
LEFT JOIN attendance a 
    ON a.student_id = cs.student_id 
    AND a.class_id = cs.class_id 
    AND a.status = 'present'
JOIN class_subjects csub 
    ON csub.class_id = cs.class_id
WHERE csub.teacher_id = $teacher_id
GROUP BY s.id
ORDER BY present_count DESC
LIMIT 5

");
$bottomStudents = $conn->query("
    SELECT st.name, COUNT(*) as absent_count
    FROM attendance a
    JOIN students st ON st.id=a.student_id
    JOIN class_subjects cs ON cs.class_id=a.class_id
    WHERE cs.teacher_id=$teacher_id
    GROUP BY a.student_id
    ORDER BY absent_count DESC
    LIMIT 5
");

// ------------------- ONGOING & NEXT SUBJECT -------------------
$ongoingRes = $conn->query("
    SELECT sub.name AS subject_name, c.name AS class_name, css.start_time, css.end_time
    FROM class_subject_schedule css
    JOIN class_subjects cs ON cs.id=css.class_subject_id
    JOIN subjects sub ON sub.id=cs.subject_id
    JOIN classes c ON c.id=cs.class_id
    WHERE cs.teacher_id=$teacher_id
      AND css.day_of_week='$todayDay'
      AND css.start_time <= '$now' AND css.end_time >= '$now'
    LIMIT 1
");
$nextRes = $conn->query("
    SELECT sub.name AS subject_name, c.name AS class_name, css.start_time
    FROM class_subject_schedule css
    JOIN class_subjects cs ON cs.id=css.class_subject_id
    JOIN subjects sub ON sub.id=cs.subject_id
    JOIN classes c ON c.id=cs.class_id
    WHERE cs.teacher_id=$teacher_id
      AND css.day_of_week='$todayDay'
      AND css.start_time > '$now'
    ORDER BY css.start_time ASC
    LIMIT 1
");
$ongoing = $ongoingRes->fetch_assoc();
$next = $nextRes->fetch_assoc();

// ------------------- CLASS SUMMARY -------------------
$classesSummary = [];
if(count($classIds) > 0){
    $classesRes = $conn->query("SELECT c.id as class_id, c.name as class_name, COUNT(cs.student_id) as student_count 
        FROM classes c
        JOIN class_students cs ON c.id = cs.class_id
        WHERE c.id IN ($ids)
        GROUP BY c.id");
    while($row = $classesRes->fetch_assoc()) $classesSummary[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= translate('page_title') ?> | <?= translate('system_name') ?></title>
    
    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2.0.0"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    
    <style>
        :root {
    --primary: #4361ee;
    --primary-light: #4895ef;
    --secondary: #3f37c9;
    --success: #4cc9f0;
    --danger: #f72585;
    --warning: #f8961e;
    --info: #7209b7;
    --light: #f8f9fa;
    --dark: #212529;
    --border-radius: 12px;
    --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
    --transition: all 0.3s ease;
    --gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
}

.dark-mode {
    --primary: #5a7dff;
    --primary-light: #6d8eff;
    --secondary: #4d44d6;
    --light: #1a1d28;
    --dark: #f8f9fa;
    --box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    --gradient: linear-gradient(135deg, #5a7dff 0%, #3a0ca3 100%);
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', sans-serif;
    background: var(--light);
    color: var(--dark);
    transition: var(--transition);
    min-height: 100vh;
    overflow-x: hidden;
    zoom: 85%; /* Zoom out the entire page */
    transform-origin: top center;
}

body.dark {
    background: #0f1419;
    color: #f0f0f0;
}

/* Container with responsive width */
.container {
    max-width: 1920px; /* Increased max-width */
    width: 95%;
    margin: 0 auto;
    padding: 15px 25px;
}

/* Header Styles - Responsive */
.dashboard-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    padding: 18px 25px;
    background: var(--gradient);
    border-radius: var(--border-radius);
    color: white;
    box-shadow: var(--box-shadow);
    flex-wrap: wrap;
    gap: 15px;
}

.header-left h1 {
    font-size: 1.6rem; /* Reduced slightly */
    font-weight: 700;
    margin-bottom: 5px;
    line-height: 1.3;
}

.header-left p {
    opacity: 0.9;
    font-size: 0.9rem; /* Reduced */
    line-height: 1.4;
}

.header-right {
    display: flex;
    align-items: center;
    gap: 15px;
    flex-wrap: wrap;
}

.date-time-display {
    background: rgba(255, 255, 255, 0.15);
    padding: 10px 18px;
    border-radius: 50px;
    font-weight: 600;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    font-size: 0.9rem; /* Reduced */
    white-space: nowrap;
}

.teacher-avatar {
    width: 45px; /* Reduced */
    height: 45px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.2);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem; /* Reduced */
    border: 2px solid white;
    flex-shrink: 0;
}

/* Stats Grid - More responsive */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 18px;
    margin-bottom: 25px;
}

.stat-card {
    background: white;
    border-radius: var(--border-radius);
    padding: 20px;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    border-left: 5px solid var(--primary);
    position: relative;
    overflow: hidden;
}

body.dark .stat-card {
    background: #1e2530;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    position: absolute;
    top: 15px;
    right: 15px;
    font-size: 1.8rem; /* Reduced */
    opacity: 0.2;
}

.stat-value {
    font-size: 2rem; /* Reduced */
    font-weight: 700;
    margin-bottom: 5px;
    color: var(--primary);
}

.stat-label {
    font-size: 0.9rem; /* Reduced */
    color: #6c757d;
    font-weight: 500;
}

body.dark .stat-label {
    color: #aaa;
}

/* Main Layout - Responsive grid */
.dashboard-main {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 22px;
    margin-bottom: 25px;
}

/* Card Styles */
.card {
    background: white;
    border-radius: var(--border-radius);
    padding: 22px;
    box-shadow: var(--box-shadow);
    margin-bottom: 22px;
    transition: var(--transition);
}

body.dark .card {
    background: #1e2530;
}

.card-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 18px;
    padding-bottom: 12px;
    border-bottom: 1px solid #eee;
    flex-wrap: wrap;
    gap: 10px;
}

body.dark .card-header {
    border-bottom: 1px solid #333;
}

body.dark strong {
    color: inherit;
}

body.dark .time {
    color: inherit;
}

.card-title {
    font-size: 1.2rem; /* Reduced */
    font-weight: 700;
    color: var(--primary);
    display: flex;
    align-items: center;
    gap: 8px;
    line-height: 1.3;
}

.card-title i {
    font-size: 1.1rem; /* Reduced */
}

/* Charts - Responsive */
.chart-container {
    position: relative;
    width: 100%;
    min-height: 200px; /* Reduced */
    height: auto;
}

/* Schedule List */
.schedule-list {
    list-style: none;
}

.schedule-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    margin-bottom: 8px;
    background: #f8f9fa;
    border-radius: 10px;
    transition: var(--transition);
    border-left: 4px solid var(--primary);
    flex-wrap: wrap;
    gap: 10px;
}

body.dark .schedule-item {
    background: #252e3a;
}

.schedule-item:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

body.dark .schedule-item:hover {
    background: #2d3847;
}

.schedule-item.ongoing {
    border-left-color: var(--success);
    background: rgba(76, 201, 240, 0.1);
}

.schedule-item.passed {
    border-left-color: #aaa;
    opacity: 0.7;
}

.schedule-time {
    font-weight: 600;
    color: var(--primary);
    min-width: 120px; /* Reduced */
    font-size: 0.9rem; /* Reduced */
}

/* Attendance Summary */
.attendance-summary {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); /* Reduced */
    gap: 12px;
    margin-top: 18px;
}

.attendance-item {
    text-align: center;
    padding: 12px;
    border-radius: 10px;
    background: #f8f9fa;
    font-size: 0.9rem; /* Reduced */
}

body.dark .attendance-item {
    background: #252e3a;
}

.attendance-item.present {
    border-top: 4px solid #2ecc71;
}

.attendance-item.absent {
    border-top: 4px solid #e74c3c;
}

.attendance-item.late {
    border-top: 4px solid #f1c40f;
}

.attendance-item.excused {
    border-top: 4px solid #3498db;
}

/* Progress Bars */
.progress-container {
    margin: 12px 0;
}

.progress-label {
    display: flex;
    justify-content: space-between;
    margin-bottom: 5px;
    font-size: 0.85rem; /* Reduced */
}

.progress-bar {
    height: 6px; /* Reduced */
    background: #e9ecef;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    border-radius: 4px;
    background: var(--primary);
    transition: width 1s ease;
}

/* Top/Bottom Students */
.student-list {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}

.student-card {
    flex: 1;
    min-width: 180px; /* Reduced */
    background: #f8f9fa;
    border-radius: 10px;
    padding: 12px;
    text-align: center;
    font-size: 0.9rem; /* Reduced */
}

body.dark .student-card {
    background: #252e3a;
}

.student-card.top {
    border-top: 4px solid #2ecc71;
}

.student-card.bottom {
    border-top: 4px solid #e74c3c;
}

.student-avatar {
    width: 45px; /* Reduced */
    height: 45px;
    border-radius: 50%;
    background: var(--primary-light);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    margin: 0 auto 8px;
    font-size: 1.1rem; /* Reduced */
}

/* Filter Bar */
.filter-bar {
    background: white;
    border-radius: var(--border-radius);
    padding: 18px;
    margin-bottom: 25px;
    box-shadow: var(--box-shadow);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 12px;
}

body.dark .filter-bar {
    background: #1e2530;
}

.date-filter {
    display: flex;
    gap: 12px;
    align-items: center;
    flex-wrap: wrap;
}

.date-input {
    padding: 8px 12px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    background: white;
    font-size: 0.9rem; /* Reduced */
}

body.dark .date-input {
    background: #252e3a;
    border-color: #444;
    color: white;
}

/* Buttons */
.btn {
    padding: 8px 16px;
    border: none;
    border-radius: 8px;
    font-family: 'Inter', sans-serif;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.9rem; /* Reduced */
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--secondary);
    transform: translateY(-2px);
}

.btn-outline {
    background: transparent;
    border: 2px solid var(--primary);
    color: var(--primary);
}

.btn-outline:hover {
    background: var(--primary);
    color: white;
}

.btn-sm {
    padding: 5px 10px;
    font-size: 0.8rem; /* Reduced */
}

/* Footer */
.dashboard-footer {
    text-align: center;
    padding: 18px;
    margin-top: 35px;
    color: #6c757d;
    font-size: 0.85rem; /* Reduced */
    border-top: 1px solid #eee;
}

body.dark .dashboard-footer {
    border-top: 1px solid #333;
}

/* No Attendance Classes */
.no-attendance {
    color: red;
    font-weight: 600;
    white-space: normal; /* Changed from nowrap to normal */
    word-break: break-word;
}

.list-group-item {
    padding: 10px 0;
    border-bottom: 1px solid #eee;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 8px;
}

body.dark .list-group-item {
    border-bottom: 1px solid #333;
}

.badge {
    padding: 4px 8px;
    border-radius: 4px;
    font-size: 0.8rem; /* Reduced */
}

/* Ongoing & Next Subject Container */
.ongoing-next-container {
    margin-top: 18px;
    padding: 12px;
    background: #f8f9fa;
    border-radius: 10px;
    display: flex;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 15px;
}

/* Responsive Breakpoints */
@media (max-width: 1440px) {
    body {
        zoom: 80%;
    }
    
    .container {
        width: 98%;
        padding: 12px 20px;
    }
    
    .dashboard-main {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
}

@media (max-width: 1200px) {
    body {
        zoom: 75%;
    }
    
    .dashboard-header {
        flex-direction: column;
        align-items: stretch;
        gap: 15px;
    }
    
    .header-right {
        justify-content: space-between;
    }
    
    .attendance-summary {
        grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    }
}

@media (max-width: 992px) {
    .dashboard-main {
        grid-template-columns: 1fr;
    }
    
    .student-card {
        min-width: 160px;
    }
    
    .schedule-time {
        min-width: 100px;
    }
}

@media (max-width: 768px) {
    body {
        zoom: 70%;
    }
    
    .container {
        padding: 10px 15px;
    }
    
    .dashboard-header {
        padding: 15px;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
    
    .header-right {
        flex-direction: column;
        align-items: stretch;
        gap: 10px;
    }
    
    .date-time-display {
        text-align: center;
        padding: 8px;
        font-size: 0.85rem;
    }
    
    .date-filter {
        flex-direction: column;
        width: 100%;
    }
    
    .date-input {
        width: 100%;
    }
    
    .schedule-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .schedule-time {
        min-width: auto;
        align-self: flex-end;
    }
    
    .attendance-summary {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 576px) {
    body {
        zoom: 65%;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .student-list {
        flex-direction: column;
    }
    
    .student-card {
        width: 100%;
        min-width: auto;
    }
    
    .card {
        padding: 18px;
    }
    
    .card-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .card-title {
        font-size: 1.1rem;
    }
    
    .btn {
        width: 100%;
        justify-content: center;
    }
    
    .list-group-item {
        flex-direction: column;
        align-items: flex-start;
        gap: 5px;
    }
    
    .badge {
        align-self: flex-start;
    }
}

@media (max-width: 480px) {
    body {
        zoom: 60%;
    }
    
    .container {
        padding: 8px 12px;
    }
    
    .dashboard-header {
        padding: 12px;
    }
    
    .header-left h1 {
        font-size: 1.4rem;
    }
    
    .stat-value {
        font-size: 1.8rem;
    }
    
    .schedule-item {
        padding: 10px;
    }
}

/* Print styles */
@media print {
    body {
        zoom: 100% !important;
    }
    
    .container {
        width: 100%;
        max-width: none;
        padding: 0;
    }
    
    .card {
        break-inside: avoid;
        box-shadow: none;
        border: 1px solid #ddd;
    }
    
    .btn, .filter-bar, .dashboard-footer {
        display: none !important;
    }
}

/* Animation keyframes */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(20px); }
    to { opacity: 1; transform: translateY(0); }
}

.fade-in {
    animation: fadeIn 0.5s ease forwards;
}

.delay-1 { animation-delay: 0.1s; }
.delay-2 { animation-delay: 0.2s; }
.delay-3 { animation-delay: 0.3s; }
.delay-4 { animation-delay: 0.4s; }

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

body.dark ::-webkit-scrollbar-track {
    background: #252e3a;
}

::-webkit-scrollbar-thumb {
    background: var(--primary);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: var(--secondary);
}

/* Live Indicator */
.live-indicator {
    display: inline-block;
    width: 8px;
    height: 8px;
    background: #ff0000;
    border-radius: 50%;
    margin-right: 6px;
    animation: pulse 1.5s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}
    </style>
</head>
<body  >
    <div class="container">
        <!-- Header -->
        <header class="dashboard-header fade-in">
            <div class="header-left">
                <h1><?= translate('teacher_dashboard') ?></h1>
                <p><?= translate('welcome_back') ?>, <?= $teacher['first_name'].' '.$teacher['last_name']; ?> | <?= $teacher['employee_code']; ?></p>
                <p style="margin-top: 5px; font-size: 0.85rem; opacity: 0.8;">
                    <i class="fas fa-envelope"></i> <?= $teacher['email']; ?> | 
                    <i class="fas fa-phone"></i> <?= $teacher['phone']; ?> |
                    <i class="fas fa-graduation-cap"></i> <?= $teacher['qualification']; ?>
                </p>
            </div>
            <div class="header-right">
                <div class="date-time-display">
                    <i class="far fa-calendar-alt"></i> <?= date('l, F j, Y'); ?>
                    <span style="margin: 0 10px">|</span>
                    <i class="far fa-clock"></i> <span id="realTimeClock"></span>
                </div>
                <div class="teacher-avatar">
                    <?= strtoupper(substr($teacher['first_name'], 0, 1) . substr($teacher['last_name'], 0, 1)); ?>
                </div>
            </div>
        </header>

        <!-- Stats Overview -->
        <div class="stats-grid fade-in">
            <div class="stat-card fade-in delay-1">
                <div class="stat-icon">
                    <i class="fas fa-book-open"></i>
                </div>
                <div class="stat-value"><?= $totalSubjects ?></div>
                <div class="stat-label"><?= translate('total_subjects') ?></div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card fade-in delay-2">
                <div class="stat-icon">
                    <i class="fas fa-school"></i>
                </div>
                <div class="stat-value"><?= $totalClasses ?></div>
                <div class="stat-label"><?= translate('total_classes') ?></div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card fade-in delay-3">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-value"><?= $totalStudents ?></div>
                <div class="stat-label"><?= translate('total_students') ?></div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card fade-in delay-4">
                <div class="stat-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <div class="stat-value"><?= $totalAttendances ?></div>
                <div class="stat-label"><?= translate('total_attendances') ?></div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
            
            <div class="stat-card fade-in delay-1">
                <div class="stat-icon">
                    <i class="fas fa-calendar-day"></i>
                </div>
                <div class="stat-value"><?= $mostBusyDay ?></div>
                <div class="stat-label"><?= translate('busiest_day') ?></div>
                <div class="progress-container">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 100%"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Date Filter -->
        <!-- <div class="filter-bar fade-in delay-2">
            <h3 style="color: var(--primary);"><i class="fas fa-chart-line"></i> <?= translate('attendance_analytics') ?></h3>
            <form method="GET" class="date-filter">
                <div>
                    <label for="start_date" style="font-weight: 500; margin-right: 10px;"><?= translate('start_date') ?>:</label>
                    <input type="date" id="start_date" name="start_date" value="<?= htmlspecialchars($startDate) ?>" class="date-input">
                </div>
                <div>
                    <label for="end_date" style="font-weight: 500; margin-right: 10px;"><?= translate('end_date') ?>:</label>
                    <input type="date" id="end_date" name="end_date" value="<?= htmlspecialchars($endDate) ?>" class="date-input">
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> <?= translate('apply_filter') ?>
                </button>
            </form>
        </div> -->

        <!-- Main Dashboard Content -->
        <div class="dashboard-main">
            <!-- Left Column -->
            <div class="dashboard-left">
                <!-- Today's Schedule -->
                <div class="card fade-in delay-1">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-calendar-day"></i> <?= translate('todays_schedule') ?> (<?= $todayDay ?>)</h3>
                        <small style="color: #ff0000;"><span class="live-indicator"></span><?= translate('live') ?></small>
                    </div>
                    
                    <?php if(empty($todaysSchedule)): ?>
                        <p style="text-align: center; padding: 30px; color: #6c757d;">
                            <i class="far fa-calendar-times" style="font-size: 2rem; margin-bottom: 15px; display: block;"></i>
                            <?= translate('no_subjects_scheduled') ?>
                        </p>
                    <?php else: ?>
                        <ul class="schedule-list">
                            <?php foreach($todaysSchedule as $s): 
                                $isOngoing = ($s['start_time'] <= $now && $s['end_time'] >= $now);
                                $isPassed = ($s['end_time'] < $now);
                            ?>
                                <li class="schedule-item <?= $isOngoing ? 'ongoing' : ($isPassed ? 'passed' : '') ?>">
                                    <div style="flex: 1;">
                                        <strong style="font-size: 1.1rem;"><?= htmlspecialchars($s['subject_name']) ?></strong>
                                        <div style="font-size: 0.9rem; color: #6c757d; margin-top: 5px;">
                                            <i class="fas fa-chalkboard-teacher"></i> <?= htmlspecialchars($s['class_name']) ?>
                                        </div>
                                    </div>
                                    <div class="schedule-time">
                                        <i class="far fa-clock"></i> <?= $s['start_time'] ?> - <?= $s['end_time'] ?>
                                    </div>
                                    <?php if($isOngoing): ?>
                                        <span style="color: #2ecc71; font-weight: 600; margin-left: 15px;">
                                            <i class="fas fa-play-circle"></i> <?= translate('ongoing') ?>
                                        </span>
                                    <?php elseif($isPassed): ?>
                                        <span style="color: #aaa; font-weight: 600; margin-left: 15px;">
                                            <i class="fas fa-check-circle"></i> <?= translate('completed') ?>
                                        </span>
                                    <?php endif; ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>

                    <div class="card fade-in delay-2">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-chart-bar"></i> <?= translate('attendance_overview') ?></h3>
                        <div style="font-size: 0.85rem; color: #6c757d;">
                            <span style="margin-right: 15px;"><span style="display: inline-block; width: 10px; height: 10px; background: #2ecc71; border-radius: 50%; margin-right: 5px;"></span> <?= translate('present') ?></span>
                            <span style="margin-right: 15px;"><span style="display: inline-block; width: 10px; height: 10px; background: #e74c3c; border-radius: 50%; margin-right: 5px;"></span> <?= translate('absent') ?></span>
                            <span><span style="display: inline-block; width: 10px; height: 10px; background: #f1c40f; border-radius: 50%; margin-right: 5px;"></span> <?= translate('late') ?></span>
                        </div>
                    </div>
                    <div class="chart-container">
                        <canvas id="attendanceChart"></canvas>
                    </div>
                </div>
                 <div class="card fade-in delay-2">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-layer-group"></i> <?= translate('class_wise_attendance') ?></h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="classAttendanceChart"></canvas>
                    </div>
                </div>
                
                    <div class="card">
    <div class="card-header">
        <h3 class="card-title">
            <i class="fas fa-exclamation-circle"></i>
            <?= translate('classes_no_attendance') ?>
        </h3>
    </div>

    <div class="card-body no-attendance">
        <?php if (count($noAttendanceClasses) > 0): ?>
            <ul class="list-group">
                <?php foreach ($noAttendanceClasses as $row): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <strong><?= htmlspecialchars($row['class_name']) ?></strong>
                            — <?= htmlspecialchars($row['subject_name']) ?>
                        </div>
                        <span class="badge bg-warning text-dark">
                            <?= substr($row['start_time'],0,5) ?> - <?= substr($row['end_time'],0,5) ?>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <div class="text-success">
                ✅ <?= translate('all_attendance_recorded') ?>
            </div>
        <?php endif; ?>
    </div>
</div>

                    
                    <!-- Ongoing & Next Subject -->
                    <div style="margin-top: 20px; padding: 15px; background: #f8f9fa; border-radius: 10px;">
                        <div style="display: flex; justify-content: space-between; flex-wrap: wrap; gap: 15px;">
                            <?php if($ongoing): ?>
                                <div style="flex: 1;">
                                    <h4 style="color: var(--primary); margin-bottom: 10px;">
                                        <i class="fas fa-play-circle"></i> <?= translate('ongoing_now') ?>
                                    </h4>
                                    <p class="time"><strong><?= $ongoing['subject_name'] ?></strong> (<?= $ongoing['class_name'] ?>)</p>
                                    <p class="time"><?= translate('ends_at') ?>: <?= $ongoing['end_time'] ?></p>
                                    <div style="margin-top: 10px;">
                                        <strong><?= translate('time_remaining') ?>:</strong> <span id="countdown" style="font-weight: 700; color: var(--danger);"></span>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <?php if($next): ?>
                                <div style="flex: 1;">
                                    <h4 style="color: var(--info); margin-bottom: 10px;">
                                        <i class="fas fa-arrow-circle-right"></i> <?= translate('up_next') ?>
                                    </h4>
                                    <p><strong><?= $next['subject_name'] ?></strong> (<?= $next['class_name'] ?>)</p>
                                    <p><?= translate('starts_at') ?>: <?= $next['start_time'] ?></p>
                                    <!-- <div style="margin-top: 10px;">
                                        <button class="btn btn-sm btn-outline">
                                            <i class="fas fa-bell"></i> <?= translate('set_reminder') ?>
                                        </button>
                                    </div> -->
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Attendance Chart -->
                 

                <!-- Top & Bottom Students -->
                <div class="card fade-in delay-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-trophy"></i> <?= translate('student_performance') ?></h3>
                    </div>
                    <div class="student-list">
                        <div style="flex: 1;">
                            <h4 style="color: #2ecc71; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-crown"></i> <?= translate('top_students') ?>
                            </h4>
                            <div class="student-list">
                                <?php while($row=$topStudents->fetch_assoc()): ?>
                                    <div class="student-card top">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($row['name'], 0, 2)); ?>
                                        </div>
                                        <h4><?= $row['name'] ?></h4>
                                        <p><?= translate('present') ?>: <?= $row['present_count'] ?> <?= translate('times') ?></p>
                                        <div class="progress-container" style="margin-top: 10px;">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?= min(100, ($row['present_count']/max(1, $totalAttendances))*100) ?>%; background: #2ecc71;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                        
                        <div style="flex: 1;">
                            <h4 style="color: #e74c3c; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
                                <i class="fas fa-exclamation-triangle"></i> <?= translate('need_attention') ?>
                            </h4>
                            <div class="student-list">
                                <?php while($row=$bottomStudents->fetch_assoc()): ?>
                                    <div class="student-card bottom">
                                        <div class="student-avatar">
                                            <?= strtoupper(substr($row['name'], 0, 2)); ?>
                                        </div>
                                        <h4><?= $row['name'] ?></h4>
                                        <p><?= translate('absent') ?>: <?= $row['absent_count'] ?> <?= translate('times') ?></p>
                                        <div class="progress-container" style="margin-top: 10px;">
                                            <div class="progress-bar">
                                                <div class="progress-fill" style="width: <?= min(100, ($row['absent_count']/max(1, $totalAttendances))*100) ?>%; background: #e74c3c;"></div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="dashboard-right">
                <!-- Class-wise Attendance -->
                <!-- <div class="card fade-in delay-2">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-layer-group"></i> <?= translate('class_wise_attendance') ?></h3>
                    </div>
                    <div class="chart-container">
                        <canvas id="classAttendanceChart"></canvas>
                    </div>
                </div> -->

                <!-- Today's Attendance Summary -->
                <div class="card fade-in delay-3">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-clipboard-list"></i> <?= translate('todays_attendance_summary') ?></h3>
                        <span style="background: var(--primary); color: white; padding: 5px 10px; border-radius: 20px; font-size: 0.85rem;">
                            <?= date('F j, Y'); ?>
                        </span>
                    </div>
                    
                    <?php if(empty($classSummary)): ?>
                        <p style="text-align: center; padding: 20px; color: #6c757d;"><?= translate('no_attendance_recorded') ?></p>
                    <?php else: ?>
                        <div class="attendance-summary">
                            <?php foreach($classSummary as $cls): ?>
                                <div class="attendance-item present">
                                    <h4><?= htmlspecialchars($cls['class_name']) ?></h4>
                                    <div style="display: flex; justify-content: space-between; margin-top: 10px; font-size: 0.9rem;">
                                        <div>
                                            <div style="color: #2ecc71;"><strong><?= $cls['present'] ?? 0 ?></strong> <?= translate('present') ?></div>
                                            <div style="color: #e74c3c;"><strong><?= $cls['absent'] ?? 0 ?></strong> <?= translate('absent') ?></div>
                                        </div>
                                        <div>
                                            <div style="color: #f1c40f;"><strong><?= $cls['late'] ?? 0 ?></strong> <?= translate('late') ?></div>
                                            <div style="color: #3498db;"><strong><?= $cls['excused'] ?? 0 ?></strong> <?= translate('excused') ?></div>
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px; font-size: 0.8rem; color: #6c757d;">
                                        <?= translate('marked_by_you') ?>: <?= $cls['marked_by_me'] ?? 0 ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Late/Early Analysis -->
                <div class="card fade-in delay-1">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-history"></i> <?= translate('most_late_classes') ?></h3>
                    </div>
                    
                    <?php while($row=$lateAnalysisRes->fetch_assoc()): ?>
                        <div style="margin-bottom: 20px;">
                            <div class="progress-label">
                                <span><?= $row['class_name'] ?></span>
                                <span><?= $row['late_percent'] ?>% <?= translate('late') ?></span>
                            </div>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $row['late_percent'] ?>%; background: #f1c40f;"></div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>

                <!-- Quick Actions -->
                <div class="card fade-in delay-4">
                    <div class="card-header">
                        <h3 class="card-title"><i class="fas fa-bolt"></i> <?= translate('quick_actions') ?></h3>
                    </div>
                    <div style="display: flex; flex-wrap: wrap; gap: 10px;">
                        <?php foreach($classesSummary as $cls): ?>
                            <a href="take_attendance.php?class_id=<?= $cls['class_id'] ?>" class="btn btn-outline" style="flex: 1; min-width: 150px; justify-content: center;">
                                <i class="fas fa-user-friends"></i> <?= $cls['class_name'] ?>
                                <span style="margin-left: 5px; background: var(--primary); color: white; width: 20px; height: 20px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 0.7rem;">
                                    <?= $cls['student_count'] ?>
                                </span>
                            </a>
                        <?php endforeach; ?>
                        
                        <!-- <button class="btn btn-primary" style="flex: 1; min-width: 150px; justify-content: center;">
                            <i class="fas fa-plus-circle"></i> <?= translate('add_attendance') ?>
                        </button> -->
                        
                        <!-- <button class="btn btn-outline" style="flex: 1; min-width: 150px; justify-content: center;">
                            <i class="fas fa-file-export"></i> <?= translate('export_data') ?>
                        </button>
                        
                        <button class="btn btn-outline" style="flex: 1; min-width: 150px; justify-content: center;">
                            <i class="fas fa-chart-pie"></i> <?= translate('view_reports') ?>
                        </button> -->
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <footer class="dashboard-footer fade-in delay-4">
            <p>
                <strong><?= htmlspecialchars($teacher['first_name'].' '.$teacher['last_name']); ?></strong> | 
                <?= translate('teacher_dashboard') ?> | <?= translate('system') ?> © <?= date('Y'); ?>
            </p>
            <p style="margin-top: 10px; font-size: 0.8rem; color: #6c757d;">
                <i class="fas fa-user-shield"></i> <?= translate('last_refresh') ?>: <?= translate('today_at') ?> <?= date('H:i') ?> 
            </p>
        </footer>
    </div>

    <script>
        // Real-time Clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2,'0');
            const minutes = String(now.getMinutes()).padStart(2,'0');
            const seconds = String(now.getSeconds()).padStart(2,'0');
            document.getElementById('realTimeClock').innerText = `${hours}:${minutes}:${seconds}`;
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Countdown for ongoing class
        <?php if($ongoing): ?>
        let endTime = "<?= $ongoing['end_time'] ?>";
        function updateCountdown(){
            let now = new Date();
            let [h,m,s] = endTime.split(':');
            let end = new Date(); end.setHours(h,m,s,0);
            let diff = Math.max(0,end-now);
            let hrs=Math.floor(diff/3600000);
            let mins=Math.floor((diff%3600000)/60000);
            let secs=Math.floor((diff%60000)/1000);
            document.getElementById('countdown').innerText = `${hrs}h ${mins}m ${secs}s`;
        }
        setInterval(updateCountdown,1000);
        updateCountdown();
        <?php endif; ?>

        // Chart Configuration
        const chartColors = {
            primary: '#4361ee',
            success: '#2ecc71',
            danger: '#e74c3c',
            warning: '#f1c40f',
            info: '#3498db',
            light: '#f8f9fa',
            dark: '#212529'
        };

        // Attendance Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
       new Chart(ctx,{
    type:'line',
    data:{
        labels: <?= json_encode($chartLabels) ?>,
        datasets:[
            {
                label:'<?= translate('present') ?>',
                borderColor: chartColors.success,
                data:<?= json_encode($chartPresent) ?>,
                fill:false,
                tension:0.4
            },
            {
                label:'<?= translate('absent') ?>',
                borderColor: chartColors.danger,
                data:<?= json_encode($chartAbsent) ?>,
                fill:false,
                tension:0.4
            },
            {
                label:'<?= translate('late') ?>',
                borderColor: chartColors.warning,
                data:<?= json_encode($chartLate) ?>,
                fill:false,
                tension:0.4
            },
            {
                label:'<?= translate('excused') ?>',
                borderColor: chartColors.info,
                data:<?= json_encode($chartExcused) ?>,
                fill:false,
                tension:0.4
            }
        ]
    },
    options:{
        responsive:true,
        maintainAspectRatio:false,
        plugins:{
            legend:{display:true, position:'top'},
            tooltip:{mode:'index', intersect:false}
        },
        interaction:{intersect:false, mode:'nearest'},
        scales:{
            x:{grid:{display:false}},
            y:{beginAtZero:true}
        }
    }
});


        // Class-wise Attendance Chart
        new Chart(document.getElementById('classAttendanceChart'),{
            type:'bar',
            data:{
                labels: <?= json_encode($classLabels) ?>,
                datasets:[
                    {label:'<?= translate('present') ?>', backgroundColor: chartColors.success, data:<?= json_encode($classPresent) ?>,
                     borderRadius: 6},
                    {label:'<?= translate('absent') ?>', backgroundColor: chartColors.danger, data:<?= json_encode($classAbsent) ?>,
                     borderRadius: 6},
                    {label:'<?= translate('late') ?>', backgroundColor: chartColors.warning, data:<?= json_encode($classLate) ?>,
                     borderRadius: 6},
                    {label:'<?= translate('excused') ?>', backgroundColor: chartColors.info, data:<?= json_encode($classExcused) ?>,
                     borderRadius: 6}
                ]
            },
            options:{
                responsive:true,
                maintainAspectRatio: false,
                plugins:{
                    legend:{display:true, position: 'top'},
                    tooltip: {mode: 'index', intersect: false}
                },
                scales:{
                    x: {stacked: true, grid: {display: false}},
                    y: {stacked: true, beginAtZero: true, grid: {borderDash: [5, 5]}}
                }
            }
        });

        // Add fade-in animations on scroll
        document.addEventListener('DOMContentLoaded', function() {
            const fadeElements = document.querySelectorAll('.fade-in');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = 1;
                        entry.target.style.transform = 'translateY(0)';
                    }
                });
            }, { threshold: 0.1 });

            fadeElements.forEach(el => {
                el.style.opacity = 0;
                el.style.transform = 'translateY(20px)';
                el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                observer.observe(el);
            });
        });
    </script>
</body>
</html>