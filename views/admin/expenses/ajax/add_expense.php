<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
 
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not authenticated']);
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];

// Get form data
$title = trim($_POST['title']);
$category = trim($_POST['category']);
$new_category = trim($_POST['new_category']);
$amount = floatval($_POST['amount']);
$expense_date = $_POST['expense_date'];
$notes = trim($_POST['notes'] ?? '');

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

// Insert expense
$stmt = $conn->prepare("
    INSERT INTO expenses (title, category, amount, expense_date, recorded_by, created_at)
    VALUES (?, ?, ?, ?, ?, NOW())
");
$stmt->bind_param("ssdsi", $title, $category, $amount, $expense_date, $logged_in_user_id);

if ($stmt->execute()) {
    // If notes were provided, you might want to store them in a separate table
    // For now, we'll just log it
    $expense_id = $stmt->insert_id;
    
    // Log activity (optional)
    $log_stmt = $conn->prepare("
        INSERT INTO audit_logs (user_id, action, description, created_at)
        VALUES (?, 'EXPENSE_CREATE', ?, NOW())
    ");
    $description = "Created expense: $title (Amount: $$amount, Category: $category)";
    $log_stmt->bind_param("is", $logged_in_user_id, $description);
    $log_stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Expense added successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to add expense: ' . $conn->error]);
}
?>