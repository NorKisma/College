<?php
require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT COALESCE(category, 'Other') as category, 
                               SUM(amount) as total 
                        FROM expenses 
                        WHERE recorded_by = ? 
                        GROUP BY COALESCE(category, 'Other') 
                        ORDER BY total DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$data = [];
$colors = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['category'];
    $data[] = (float)$row['total'];
}

echo json_encode([
    'success' => true,
    'chartData' => [
        'labels' => $labels,
        'data' => $data
    ]
]);
?>