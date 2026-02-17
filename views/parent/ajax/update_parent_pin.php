<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

session_start();

$parent_id = $_POST['parent_id'];
$current_pin = $_POST['current_pin'];
$new_pin = $_POST['new_pin'];

// Verify current PIN
$query = $conn->prepare("SELECT login_pin FROM parents WHERE id = ?");
$query->bind_param("i", $parent_id);
$query->execute();
$result = $query->get_result();
$parent = $result->fetch_assoc();

if ($parent && $parent['login_pin'] == $current_pin) {
    // Update PIN
    $update_query = $conn->prepare("UPDATE parents SET login_pin = ? WHERE id = ?");
    $update_query->bind_param("si", $new_pin, $parent_id);
    
    if ($update_query->execute()) {
        echo json_encode([
            'success' => true,
            'message' => 'PIN updated successfully!'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update PIN. Please try again.'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Current PIN is incorrect.'
    ]);
}
?>