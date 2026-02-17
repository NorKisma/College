<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$summary = [];
$categories = [];

// Total expenses
$total_stmt = $conn->prepare("SELECT SUM(amount) as total FROM expenses WHERE recorded_by = ?");
$total_stmt->bind_param("i", $user_id);
$total_stmt->execute();
$total_result = $total_stmt->get_result();
$summary['total'] = $total_result->fetch_assoc()['total'] ?? 0;

// This month expenses
$month_start = date('Y-m-01');
$month_stmt = $conn->prepare("SELECT SUM(amount) as month_total FROM expenses 
                              WHERE recorded_by = ? AND expense_date >= ?");
$month_stmt->bind_param("is", $user_id, $month_start);
$month_stmt->execute();
$month_result = $month_stmt->get_result();
$summary['this_month'] = $month_result->fetch_assoc()['month_total'] ?? 0;
$summary['month_name'] = date('F Y');

// Unique categories
$cat_stmt = $conn->prepare("SELECT COUNT(DISTINCT COALESCE(category, 'Other')) as cat_count FROM expenses 
                            WHERE recorded_by = ?");
$cat_stmt->bind_param("i", $user_id);
$cat_stmt->execute();
$cat_result = $cat_stmt->get_result();
$summary['category_count'] = $cat_result->fetch_assoc()['cat_count'] ?? 0;

// Average monthly (last 3 months)
$three_months_ago = date('Y-m-01', strtotime('-3 months'));
$avg_stmt = $conn->prepare("SELECT AVG(monthly.total) as avg_monthly 
                            FROM (
                                SELECT DATE_FORMAT(expense_date, '%Y-%m') as month, 
                                       SUM(amount) as total 
                                FROM expenses 
                                WHERE recorded_by = ? AND expense_date >= ? 
                                GROUP BY DATE_FORMAT(expense_date, '%Y-%m')
                            ) monthly");
$avg_stmt->bind_param("is", $user_id, $three_months_ago);
$avg_stmt->execute();
$avg_result = $avg_stmt->get_result();
$summary['avg_monthly'] = $avg_result->fetch_assoc()['avg_monthly'] ?? 0;

// Get all categories for filter
$all_cat_stmt = $conn->prepare("SELECT DISTINCT COALESCE(category, 'Other') as category 
                                FROM expenses 
                                WHERE recorded_by = ? 
                                ORDER BY category");
$all_cat_stmt->bind_param("i", $user_id);
$all_cat_stmt->execute();
$all_cat_result = $all_cat_stmt->get_result();

while ($row = $all_cat_result->fetch_assoc()) {
    $categories[] = ['category' => $row['category']];
}

echo json_encode([
    'success' => true,
    'summary' => $summary,
    'categories' => $categories
]);
?>