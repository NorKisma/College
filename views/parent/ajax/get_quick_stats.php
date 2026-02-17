<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'];

// Get attendance percentage for current month
$attendance_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_days,
        SUM(CASE WHEN status IN ('present', 'excused') THEN 1 ELSE 0 END) as attended_days
    FROM attendance 
    WHERE student_id = ? 
    AND MONTH(date) = MONTH(CURDATE()) 
    AND YEAR(date) = YEAR(CURDATE())
");
$attendance_query->bind_param("i", $child_id);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result();
$attendance = $attendance_result->fetch_assoc();
$attendance_percentage = $attendance['total_days'] > 0 
    ? round(($attendance['attended_days'] / $attendance['total_days']) * 100, 2)
    : 0;

// Get average exam score
$exam_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_exams,
        AVG(er.obtained_marks) as avg_score
    FROM exam_results er
    WHERE er.student_id = ?
    AND er.obtained_marks IS NOT NULL
");
$exam_query->bind_param("i", $child_id);
$exam_query->execute();
$exam_result = $exam_query->get_result();
$exam_stats = $exam_result->fetch_assoc();

// Get fee status
$fee_query = $conn->prepare("
    SELECT 
        COUNT(*) as total_fees,
        SUM(CASE WHEN status = 'paid' THEN 1 ELSE 0 END) as paid_fees
    FROM student_fees 
    WHERE student_id = ?
");
$fee_query->bind_param("i", $child_id);
$fee_query->execute();
$fee_result = $fee_query->get_result();
$fee_stats = $fee_result->fetch_assoc();
$fee_percentage = $fee_stats['total_fees'] > 0 
    ? round(($fee_stats['paid_fees'] / $fee_stats['total_fees']) * 100, 2)
    : 100;
?>

<div class="row">
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-calendar-check text-primary" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h6 class="mb-0">Imaanshaha Bishan</h6>
                <div class="progress mt-1" style="height: 10px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $attendance_percentage; ?>%"></div>
                </div>
                <small><?php echo $attendance_percentage; ?>% (<?php echo $attendance['attended_days']; ?>/<?php echo $attendance['total_days']; ?>)</small>
            </div>
        </div>
    </div>
    
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-graph-up text-warning" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h6 class="mb-0">Celceliska dhibcaha imtixaanka</h6>
                <h4 class="mb-0"><?php echo number_format($exam_stats['avg_score'] ?? 0, 2); ?></h4>
                <small><?php echo $exam_stats['total_exams']; ?> exams</small>
            </div>
        </div>
    </div>
    
    <div class="col-12 mb-3">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="bi bi-cash text-success" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h6 class="mb-0">Lacag bixinta</h6>
                <div class="progress mt-1" style="height: 10px;">
                    <div class="progress-bar bg-info" style="width: <?php echo $fee_percentage; ?>%"></div>
                </div>
                <small><?php echo $fee_percentage; ?>% (<?php echo $fee_stats['paid_fees']; ?>/<?php echo $fee_stats['total_fees']; ?>)</small>
            </div>
        </div>
    </div>
</div>