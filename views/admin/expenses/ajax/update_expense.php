<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];
$expense_id = intval($_POST['id']);

// Check if expense exists and user has permission
$check_stmt = $conn->prepare("SELECT recorded_by FROM expenses WHERE id = ?");
$check_stmt->bind_param("i", $expense_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Expense not found']);
    exit;
}

$expense = $check_result->fetch_assoc();

// Only allow update if user created the expense or is admin
// You might want to check user role here
if ($expense['recorded_by'] != $logged_in_user_id) {
    // Check if user is admin
    $role_stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
    $role_stmt->bind_param("i", $logged_in_user_id);
    $role_stmt->execute();
    $role_result = $role_stmt->get_result();
    $user = $role_result->fetch_assoc();
    
    if ($user['role_id'] != 1) { // Assuming 1 is admin role
        echo json_encode(['success' => false, 'message' => 'Permission denied']);
        exit;
    }
}

// Get form data
$title = trim($_POST['title']);
$category = trim($_POST['category']);
$new_category = trim($_POST['new_category'] ?? '');
$amount = floatval($_POST['amount']);
$expense_date = $_POST['expense_date'];

// Validate
if (empty($title) || $amount <= 0 || empty($expense_date)) {
    echo json_encode(['success' => false, 'message' => 'Please fill all required fields']);
    exit;
}

// Use new category if provided
if (!empty($new_category)) {
    $category = $new_category;
} elseif (empty($category)) {
    $category = 'Other';
}

// Update expense
$stmt = $conn->prepare("
    UPDATE expenses 
    SET title = ?, category = ?, amount = ?, expense_date = ?
    WHERE id = ?
");
$stmt->bind_param("ssdsi", $title, $category, $amount, $expense_date, $expense_id);

if ($stmt->execute()) {
    // Log activity
    $log_stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action, description, created_at)
        VALUES (?, 'EXPENSE_UPDATE', ?, NOW())
    ");
    $description = "Updated expense ID: $expense_id - $title (Amount: $$amount, Category: $category)";
    $log_stmt->bind_param("is", $logged_in_user_id, $description);
    $log_stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Expense updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update expense: ' . $conn->error]);
}
?>