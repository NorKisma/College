<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/helpers.php';
require_once __DIR__ . '/../../../config/sessions.php';

$input = json_decode(file_get_contents('php://input'), true);

$cs_id = (int)($input['cs_id'] ?? 0);
$new_subject_id = (int)($input['new_subject_id'] ?? 0);
$csrf_token = $input['csrf_token'] ?? '';

if (!$cs_id || !$new_subject_id) {
    echo json_encode(['success' => false, 'error' => 'Invalid input']);
    exit;
}

if (!validate_csrf_token($csrf_token)) {
    echo json_encode(['success' => false, 'error' => 'Invalid CSRF token']);
    exit;
}

/* ---------------------------------------------------
   1. Get class_id for this class_subject
--------------------------------------------------- */
$stmt = $conn->prepare("
    SELECT class_id 
    FROM class_subjects 
    WHERE id=?
");
$stmt->bind_param('i', $cs_id);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    echo json_encode(['success' => false, 'error' => 'Invalid class_subject']);
    exit;
}

$class_id = (int)$row['class_id'];

/* ---------------------------------------------------
   2. Prevent duplicate subject in same class
--------------------------------------------------- */
$stmt = $conn->prepare("
    SELECT 1 
    FROM class_subjects 
    WHERE class_id=? AND subject_id=? AND id!=?
");
$stmt->bind_param('iii', $class_id, $new_subject_id, $cs_id);
$stmt->execute();

if ($stmt->get_result()->num_rows > 0) {
    echo json_encode([
        'success' => false,
        'error'   => 'This subject is already assigned to this class'
    ]);
    exit;
}

/* ---------------------------------------------------
   3. Update subject
--------------------------------------------------- */
$stmt = $conn->prepare("
    UPDATE class_subjects 
    SET subject_id=? 
    WHERE id=?
");
$stmt->bind_param('ii', $new_subject_id, $cs_id);

if (!$stmt->execute()) {
    echo json_encode(['success' => false, 'error' => 'Failed to update subject']);
    exit;
}

/* ---------------------------------------------------
   4. Fetch subject name (for UI update)
--------------------------------------------------- */
$stmt = $conn->prepare("
    SELECT name 
    FROM subjects 
    WHERE id=?
");
$stmt->bind_param('i', $new_subject_id);
$stmt->execute();
$subject = $stmt->get_result()->fetch_assoc();

echo json_encode([
    'success'      => true,
    'subject_name'=> $subject['name']
]);
