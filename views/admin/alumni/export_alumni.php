<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (empty($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

$format = $_GET['format'] ?? 'csv';
$section_id = isset($_GET['section']) ? intval($_GET['section']) : 0;
$class_id = isset($_GET['class']) ? intval($_GET['class']) : 0;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Build query
$query = "
    SELECT 
        s.name as student_name,
        sec.name as section_name,
        c.name as class_name,
        s.admission_date,
        a.graduation_date,
        a.graduation_reason,
        a.exam_score,
        a.attendance_percentage,
        a.created_at
    FROM alumni a
    INNER JOIN students s ON a.student_id = s.id
    INNER JOIN classes c ON a.class_id = c.id
    INNER JOIN sections sec ON a.section_id = sec.id
    WHERE 1=1
";

if ($section_id) {
    $query .= " AND a.section_id = $section_id";
}

if ($class_id) {
    $query .= " AND a.class_id = $class_id";
}

if ($search) {
    $query .= " AND s.name LIKE '%$search%'";
}

$query .= " ORDER BY sec.name, c.name, s.name";

$result = $conn->query($query);

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="alumni_' . date('Y-m-d') . '.csv"');
    
    $output = fopen('php://output', 'w');
    
    // CSV header
    fputcsv($output, [
        'Student Name',
        'Section',
        'Class',
        'Admission Date',
        'Graduation Date',
        'Graduation Reason',
        'Exam Score',
        'Attendance %',
        'Alumni Since'
    ]);
    
    // Data rows
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, [
            $row['student_name'],
            $row['section_name'],
            $row['class_name'],
            $row['admission_date'],
            $row['graduation_date'],
            ucfirst(str_replace('_', ' ', $row['graduation_reason'])),
            $row['exam_score'] ?? '',
            $row['attendance_percentage'] ?? '',
            $row['created_at']
        ]);
    }
    
    fclose($output);
    exit;
}

// For PDF/Excel, you would need libraries like TCPDF or PhpSpreadsheet
// Here's a basic HTML export as fallback
?>
<!DOCTYPE html>
<html>
<head>
    <title>Alumni Export</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h2>Alumni List</h2>
    <p>Exported on: <?= date('Y-m-d H:i:s') ?></p>
    <table>
        <tr>
            <th>Student Name</th>
            <th>Section</th>
            <th>Class</th>
            <th>Admission Date</th>
            <th>Graduation Date</th>
            <th>Graduation Reason</th>
            <th>Exam Score</th>
            <th>Attendance %</th>
            <th>Alumni Since</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['section_name']) ?></td>
            <td><?= htmlspecialchars($row['class_name']) ?></td>
            <td><?= $row['admission_date'] ?></td>
            <td><?= $row['graduation_date'] ?></td>
            <td><?= ucfirst(str_replace('_', ' ', $row['graduation_reason'])) ?></td>
            <td><?= $row['exam_score'] ?? '-' ?></td>
            <td><?= $row['attendance_percentage'] ? $row['attendance_percentage'] . '%' : '-' ?></td>
            <td><?= $row['created_at'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>