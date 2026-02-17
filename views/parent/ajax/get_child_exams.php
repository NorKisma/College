<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'] ?? 0;
$show_all = isset($_POST['show_all_exams']) ? true : false;

// Get student's current active class and section
$class_query = $conn->prepare("
    SELECT cs.class_id, c.section_id
    FROM class_students cs
    LEFT JOIN classes c ON cs.class_id = c.id
    WHERE cs.student_id = ? AND cs.is_active = 1
    LIMIT 1
");
$class_query->bind_param("i", $child_id);
$class_query->execute();
$class_result = $class_query->get_result();
$class = $class_result->fetch_assoc();
$class_id = $class['class_id'] ?? 0;
$section_id = $class['section_id'] ?? 0;

// Fetch exams
if ($show_all) {
    // Show all exams the student has results for or are scheduled for
    $exams_query = $conn->prepare("
        SELECT 
            e.*,
            GROUP_CONCAT(DISTINCT es.subject_id) as subject_ids
        FROM exams e
        LEFT JOIN exam_subjects es ON e.id = es.exam_id
        LEFT JOIN exam_results er ON e.id = er.exam_id AND er.student_id = ?
        WHERE e.is_deleted = 0
        GROUP BY e.id
        ORDER BY e.start_date DESC
    ");
    $exams_query->bind_param("i", $child_id);
} else {
    // Show only exams for the student's current active class
    $exams_query = $conn->prepare("
        SELECT 
            e.*,
            GROUP_CONCAT(DISTINCT es.subject_id) as subject_ids
        FROM exams e
        LEFT JOIN exam_subjects es ON e.id = es.exam_id
        WHERE e.class_id = ? AND e.status = 'active' AND e.is_deleted = 0
        GROUP BY e.id
        ORDER BY e.start_date DESC
    ");
    $exams_query->bind_param("i", $class_id);
}

$exams_query->execute();
$exams_result = $exams_query->get_result();

$exams = [];
while ($exam = $exams_result->fetch_assoc()) {
    // Get results for this exam
    $results_query = $conn->prepare("
        SELECT 
            er.*,
            s.name as subject_name,
            es.max_marks,
            es.pass_marks,
            ROUND((er.obtained_marks / es.max_marks) * 100, 2) as percentage,
            CASE 
                WHEN (er.obtained_marks / es.max_marks) * 100 >= 85 THEN 'A+'
                WHEN (er.obtained_marks / es.max_marks) * 100 >= 75 THEN 'A'
                WHEN (er.obtained_marks / es.max_marks) * 100 >= 65 THEN 'B+'
                WHEN (er.obtained_marks / es.max_marks) * 100 >= 55 THEN 'B'
                WHEN (er.obtained_marks / es.max_marks) * 100 >= 40 THEN 'C'
                ELSE 'F'
            END as grade
        FROM exam_results er
        LEFT JOIN subjects s ON er.subject_id = s.id
        LEFT JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
        WHERE er.exam_id = ? AND er.student_id = ?
    ");
    $results_query->bind_param("ii", $exam['id'], $child_id);
    $results_query->execute();
    $results = $results_query->get_result()->fetch_all(MYSQLI_ASSOC);

    $exam['results'] = $results;
    $exams[] = $exam;
}

// Determine the latest exam (based on start_date)
$latest_exam_id = null;
if (!empty($exams)) {
    usort($exams, function($a, $b) {
        return strtotime($b['start_date']) <=> strtotime($a['start_date']);
    });
    $latest_exam_id = $exams[0]['id'];
}

// Render exams
if (!empty($exams)) {
    foreach ($exams as $exam) {
        $is_latest = ($exam['id'] == $latest_exam_id);
        ?>
        <div class="card mb-4 <?php echo $is_latest ? 'latest-exam border-primary' : ''; ?>">
            <div class="card-header <?php echo $is_latest ? 'bg-primary text-white' : 'bg-light'; ?>">
                <h5 class="mb-0">
                    <?php echo htmlspecialchars($exam['name']); ?>
                    <small class="text-muted">
                        (<?php echo date('M d, Y', strtotime($exam['start_date'])); ?>
                        <?php if ($exam['end_date']): ?>
                            - <?php echo date('M d, Y', strtotime($exam['end_date'])); ?>
                        <?php endif; ?>)
                    </small>
                    <?php if ($is_latest): ?>
                        <span class="badge bg-warning text-dark">Latest Exam</span>
                    <?php endif; ?>
                </h5>
            </div>
            <div class="card-body">
                <?php if (!empty($exam['results'])): ?>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Maadada</th>
                                    <th>Dhibcaha la heley</th>
                                    <th>Ugu badnaan</th>
                                    <th>Dhibcaha baasitaanka</th>
                                    <th>Boqolkiiba</th>
                                    <th>Darajada</th>
                                    <th>Xaalada</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $total_obtained = 0;
                                $total_max = 0;
                                foreach ($exam['results'] as $result):
                                    $total_obtained += $result['obtained_marks'] ?? 0;
                                    $total_max += $result['max_marks'];
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($result['subject_name']); ?></td>
                                        <td><?php echo $result['obtained_marks'] ?? 'Absent'; ?></td>
                                        <td><?php echo $result['max_marks']; ?></td>
                                        <td><?php echo $result['pass_marks']; ?></td>
                                        <td><?php echo $result['percentage'] ?? '0.00'; ?>%</td>
                                        <td><?php echo $result['grade'] ?? 'N/A'; ?></td>
                                        <td><?php echo ucfirst($result['status'] ?? 'present'); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr class="table-secondary">
                                    <td><strong>Total</strong></td>
                                    <td><strong><?php echo $total_obtained; ?></strong></td>
                                    <td><strong><?php echo $total_max; ?></strong></td>
                                    <td colspan="2">
                                        <strong>
                                            Si guud boqolleyda: 
                                            <?php echo $total_max > 0 ? round(($total_obtained / $total_max) * 100, 2) : 0; ?>%
                                        </strong>
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="alert alert-info">
                        Wax natiija ah ma lahan maadadan...
                    </div>
                <?php endif; ?>
            </div>
        </div>
        <?php
    }
} else {
    echo '<div class="alert alert-info">No exams found for this student.</div>';
}
?>
