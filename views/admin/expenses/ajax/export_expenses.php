<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
 
if (!isset($_SESSION['user_id'])) {
    die('Not authenticated');
}

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="expenses_' . date('Y-m-d') . '.csv"');

$output = fopen('php://output', 'w');

// Header
fputcsv($output, ['ID', 'Title', 'Category', 'Amount', 'Date', 'Recorded By', 'Created At']);

// Data
$query = "SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as recorded_by_name 
          FROM expenses e 
          LEFT JOIN users u ON e.recorded_by = u.id 
          ORDER BY e.expense_date DESC";
$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    fputcsv($output, [
        $row['id'],
        $row['title'],
        $row['category'],
        $row['amount'],
        $row['expense_date'],
        $row['recorded_by_name'],
        $row['created_at']
    ]);
}

fclose($output);
?>