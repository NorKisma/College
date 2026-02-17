<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (empty($_SESSION['user_id'])) {
    exit(json_encode(['success' => false, 'message' => 'Unauthorized']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['transfer_alumni'])) {
    $alumni_id = intval($_POST['alumni_id']);
    $new_class_id = intval($_POST['new_class_id']);
    $new_section_id = intval($_POST['new_section_id']);
    
    // Verify alumni exists
    $check_query = $conn->prepare("SELECT id FROM alumni WHERE id = ?");
    $check_query->bind_param("i", $alumni_id);
    $check_query->execute();
    
    if ($check_query->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Alumni not found']);
        exit;
    }
    
    // Verify new class and section exist
    $verify_query = $conn->prepare("SELECT id FROM classes WHERE id = ? AND section_id = ?");
    $verify_query->bind_param("ii", $new_class_id, $new_section_id);
    $verify_query->execute();
    
    if ($verify_query->get_result()->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Invalid class or section']);
        exit;
    }
    
    // Update alumni record
    $update_query = $conn->prepare("UPDATE alumni SET class_id = ?, section_id = ?, updated_at = NOW() WHERE id = ?");
    $update_query->bind_param("iii", $new_class_id, $new_section_id, $alumni_id);
    
    if ($update_query->execute()) {
        // Add to history
        $history_query = $conn->prepare("
            INSERT INTO alumni_history (alumni_id, changed_by, changes_description, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $description = "Transferred to new class/section";
        $history_query->bind_param("iis", $alumni_id, $_SESSION['user_id'], $description);
        $history_query->execute();
        
        echo json_encode(['success' => true, 'message' => 'Alumni transferred successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Transfer failed: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}
?>