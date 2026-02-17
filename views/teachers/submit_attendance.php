<?php
require_once __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $class_id = $_POST['class_id'];
    $subject_id = $_POST['subject_id'];
    $teacher_id = $_POST['teacher_id'];
    $date = $_POST['date'];
    $statuses = $_POST['status'];
    $remarks = $_POST['remarks'] ?? [];
    
    $conn->begin_transaction();
    
    try {
        foreach ($statuses as $student_id => $status) {
            $remark = $remarks[$student_id] ?? '';
            
            // Check if attendance already exists for today
            $check_query = "
                SELECT id FROM attendance 
                WHERE student_id = ? 
                AND class_id = ? 
                AND date = ?
                AND subject_id = ?
            ";
            $check_stmt = $conn->prepare($check_query);
            $check_stmt->bind_param("iiss", $student_id, $class_id, $date, $subject_id);
            $check_stmt->execute();
            $existing = $check_stmt->get_result();
            
            if ($existing->num_rows > 0) {
                // Update existing
                $update_query = "
                    UPDATE attendance 
                    SET status = ?, remarks = ?, recorded_by = ?, recorded_at = NOW()
                    WHERE student_id = ? AND class_id = ? AND date = ? AND subject_id = ?
                ";
                $update_stmt = $conn->prepare($update_query);
                $update_stmt->bind_param("sssiiss", $status, $remark, $teacher_id, $student_id, $class_id, $date, $subject_id);
                $update_stmt->execute();
            } else {
                // Insert new
                $insert_query = "
                    INSERT INTO attendance 
                    (student_id, class_id, section_id, subject_id, date, status, remarks, recorded_by, recorded_at)
                    SELECT ?, ?, c.section_id, ?, ?, ?, ?, ?, NOW()
                    FROM classes c WHERE c.id = ?
                ";
                $insert_stmt = $conn->prepare($insert_query);
                $insert_stmt->bind_param("iiissssi", $student_id, $class_id, $subject_id, $date, $status, $remark, $teacher_id, $class_id);
                $insert_stmt->execute();
            }
        }
        
        $conn->commit();
        echo json_encode(['success' => true, 'message' => 'Attendance recorded successfully']);
    } catch (Exception $e) {
        $conn->rollback();
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }
}
?>