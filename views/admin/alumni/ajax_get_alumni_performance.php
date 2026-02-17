<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (empty($_SESSION['user_id'])) {
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = intval($_POST['class_id']);
    $section_id = intval($_POST['section_id']);
    $performance_type = $_POST['performance_type'];

    // Get students in this class
    $students_query = $conn->prepare("
        SELECT s.id, s.name, s.admission_date 
        FROM students s
        INNER JOIN class_students cs ON s.id = cs.student_id
        WHERE cs.class_id = ?
        ORDER BY s.name
    ");
    $students_query->bind_param("i", $class_id);
    $students_query->execute();
    $students_result = $students_query->get_result();

    // Get alumni criteria
    $criteria_query = $conn->prepare("SELECT * FROM alumni_criteria WHERE class_id = ? AND section_id = ?");
    $criteria_query->bind_param("ii", $class_id, $section_id);
    $criteria_query->execute();
    $criteria_result = $criteria_query->get_result();
    $criteria = $criteria_result->fetch_assoc();
    $criteria_result->close();

    // Get students already marked as alumni
    $alumni_student_ids = [];
    $alumni_query = $conn->prepare("SELECT student_id FROM alumni WHERE class_id = ?");
    $alumni_query->bind_param("i", $class_id);
    $alumni_query->execute();
    $alumni_result = $alumni_query->get_result();
    while ($row = $alumni_result->fetch_assoc()) {
        $alumni_student_ids[] = $row['student_id'];
    }

    $output = '<div class="table-container">';
    $output .= '<h3>' . ucfirst($performance_type) . ' Performance</h3>';
    $output .= '<table class="table table-striped table-hover">';
    $output .= '<thead><tr><th>Student Name</th><th>Admission Date</th>';

    if ($performance_type === 'exam' || $performance_type === 'both') {
        $output .= '<th>Total Exam Score</th><th>Percentage</th>';
        if ($criteria && $criteria['min_exam_score'] > 0) {
            $output .= '<th>Required Score</th><th>Status</th>';
        }
    }

    if ($performance_type === 'attendance' || $performance_type === 'both') {
        $output .= '<th>Attendance %</th>';
        if ($criteria && $criteria['min_attendance_percentage'] > 0) {
            $output .= '<th>Required %</th><th>Status</th>';
        }
    }

    $output .= '<th>Actions</th></tr></thead><tbody>';

    while ($student = $students_result->fetch_assoc()) {
        if (in_array($student['id'], $alumni_student_ids)) continue;

        $exam_passed = false;
        $attendance_passed = false;

        $output .= '<tr>';
        $output .= '<td>' . htmlspecialchars($student['name']) . '</td>';
        $output .= '<td>' . $student['admission_date'] . '</td>';

        // -------- Exam performance --------
        if ($performance_type === 'exam' || $performance_type === 'both') {
            $exam_query = $conn->prepare("
                SELECT 
                    SUM(er.obtained_marks) AS total_obtained,
                    SUM(es.max_marks) AS total_max
                FROM exam_results er
                INNER JOIN exams e ON er.exam_id = e.id
                INNER JOIN exam_subjects es 
                    ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
                WHERE er.student_id = ?
                  AND er.class_id = ?
                  AND e.academic_year = (SELECT MAX(academic_year) FROM exams WHERE class_id = ?)
                  AND er.status = 'present'
            ");
            $exam_query->bind_param("iii", $student['id'], $class_id, $class_id);
            $exam_query->execute();
            $exam_result = $exam_query->get_result()->fetch_assoc();

            $total_obtained = $exam_result['total_obtained'] ?: 0;
            $total_max = $exam_result['total_max'] ?: 1;
            $percentage = ($total_obtained / $total_max) * 100;

            $output .= '<td>' . number_format($total_obtained, 2) . '</td>';
            $output .= '<td>' . number_format($percentage, 2) . '%</td>';

            if ($criteria && $criteria['min_exam_score'] > 0) {
                $exam_passed = $total_obtained >= $criteria['min_exam_score'];
                $exam_status = $exam_passed ? 'Pass' : 'Fail';
                $output .= '<td>' . $criteria['min_exam_score'] . '</td>';
                $output .= '<td class="' . ($exam_passed ? 'eligible' : 'not-eligible') . '">' . $exam_status . '</td>';
            }
        }
 
        // -------- Attendance performance --------
        if ($performance_type === 'attendance' || $performance_type === 'both') {
            $attendance_query = $conn->prepare("
                SELECT 
                    COUNT(DISTINCT a.date) AS total_days,
                    COUNT(DISTINCT CASE WHEN a.status IN ('present','late','excused') THEN a.date END) AS present_days
                FROM attendance a
                INNER JOIN class_students cs 
                    ON cs.student_id = a.student_id
                    AND cs.class_id = a.class_id
                WHERE a.student_id = ?
                  AND cs.class_id = ?
            ");
            $attendance_query->bind_param("ii", $student['id'], $class_id);
            $attendance_query->execute();
            $attendance_result = $attendance_query->get_result()->fetch_assoc();

            $present_days = (int)($attendance_result['present_days'] ?? 0);
            $total_days = (int)($attendance_result['total_days'] ?? 0);
            $attendance_percentage = $total_days > 0 ? ($present_days / $total_days) * 100 : 0;

            $output .= '<td>' . number_format($attendance_percentage, 2) . '%</td>';

            if ($criteria && $criteria['min_attendance_percentage'] > 0) {
                $attendance_passed = $attendance_percentage >= $criteria['min_attendance_percentage'];
                $attendance_status = $attendance_passed ? 'Pass' : 'Fail';
                $output .= '<td>' . $criteria['min_attendance_percentage'] . '%</td>';
                $output .= '<td class="' . ($attendance_passed ? 'eligible' : 'not-eligible') . '">' . $attendance_status . '</td>';
            }
        }

        // -------- Eligibility check --------
        if ($performance_type === 'both') {
            // "Both" button: must pass both exam AND attendance
            $is_eligible = ($exam_passed ?? false) && ($attendance_passed ?? false);
        } elseif ($performance_type === 'exam') {
            $is_eligible = $exam_passed ?? false;
        } elseif ($performance_type === 'attendance') {
            $is_eligible = $attendance_passed ?? false;
        } else {
            $is_eligible = ($criteria['require_both'] ?? false) 
                ? ($exam_passed ?? false) && ($attendance_passed ?? false)
                : ($exam_passed ?? false) || ($attendance_passed ?? false);
        }

        $output .= '<td>';
        if ($is_eligible) {
            $output .= '<button class="btn btn-success btn-sm" onclick="makeAlumni(' . $student['id'] . ', \'' . $performance_type . '\')">Make Alumni</button>';
        } else {
            $output .= '<span class="text-muted">Not Eligible</span>';
        }
        $output .= '</td>';

        $output .= '</tr>';
    }

    $output .= '</tbody></table></div>';
    echo $output;
}
?>
