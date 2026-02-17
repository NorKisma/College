<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$exam_id = (int)($_GET['id'] ?? 0);

if ($exam_id <= 0) {
    $_SESSION['error'] = "Invalid exam ID.";
    header("Location: exams.php");
    exit;
}

// Check exam exists
$stmt = $conn->prepare("SELECT name FROM exams WHERE id = ? LIMIT 1");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
    $_SESSION['error'] = "Exam not found.";
    header("Location: exams.php");
    exit;
}

$conn->begin_transaction();

try {

    // 1️⃣ Delete exam results
    $stmt = $conn->prepare("DELETE FROM exam_results WHERE exam_id = ?");
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();

    // 2️⃣ Delete exam subjects
    $stmt = $conn->prepare("DELETE FROM exam_subjects WHERE exam_id = ?");
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();

    // 3️⃣ Delete exam itself
    $stmt = $conn->prepare("DELETE FROM exams WHERE id = ?");
    $stmt->bind_param("i", $exam_id);
    $stmt->execute();

    $conn->commit();

    $_SESSION['success'] = "Exam '{$exam['name']}' deleted successfully.";

} catch (Exception $e) {

    $conn->rollback();
    $_SESSION['error'] = "Failed to delete exam. Please try again.";
}

header("Location: exams.php");
exit;
?>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title text-danger">
                    <i class="fas fa-trash me-2"></i>Delete Exam
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body">
                Are you sure you want to delete
                <strong id="examNameToDelete"></strong>?
                <br>
                <small class="text-muted">This action cannot be undone.</small>
            </div>

            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <a id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i> Delete
                </a>
            </div>

        </div>
    </div>
</div>
