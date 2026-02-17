<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

// =========================
// App Info
// =========================
$appRes = $conn->query("SELECT app_name FROM app_settings ORDER BY id DESC LIMIT 1");
$app_name  = $appRes->fetch_assoc()['app_name'] ?? 'My Center';

// =========================
// FILTER INPUTS
// =========================
$filterClass = isset($_GET['class']) ? (int)$_GET['class'] : '';
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';
$filterYear  = isset($_GET['year']) ? (int)$_GET['year'] : '';
$searchName  = trim($_GET['search'] ?? '');
$startDate   = $_GET['start_date'] ?? '';
$endDate     = $_GET['end_date'] ?? '';

// =========================
// WHERE CONDITIONS
// =========================
$where = [];
if ($filterClass) $where[] = "c.id = $filterClass";
if ($searchName !== '') $where[] = "s.name LIKE '%".$conn->real_escape_string($searchName)."%'";
if ($startDate && $endDate) $where[] = "cs.created_at BETWEEN '{$conn->real_escape_string($startDate)}' AND '{$conn->real_escape_string($endDate)}'";
elseif ($startDate) $where[] = "cs.created_at >= '{$conn->real_escape_string($startDate)}'";
elseif ($endDate) $where[] = "cs.created_at <= '{$conn->real_escape_string($endDate)}'";

$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// =========================
// FETCH STUDENTS
// =========================
$students = $conn->query("
    SELECT DISTINCT s.id student_id, s.name student_name,
           c.id class_id, c.name class_name,
           cs.created_at enrolled_at
    FROM students s
    JOIN class_students cs ON cs.student_id = s.id
    JOIN classes c ON c.id = cs.class_id
    $whereSql
    ORDER BY s.name ASC
");

// =========================
// FETCH PAYMENTS & DISCOUNTS
// =========================
$studentTotalPayments = [];
$lastPaymentDates = [];
$studentFeesRes = $conn->query("SELECT student_id, SUM(paid_amount) total_paid, MAX(created_at) last_payment FROM student_fees GROUP BY student_id");
while($sf = $studentFeesRes->fetch_assoc()) {
    $studentTotalPayments[$sf['student_id']] = (float)$sf['total_paid'];
    $lastPaymentDates[$sf['student_id']] = $sf['last_payment'];
}

$studentDiscounts = [];
$discountsRes = $conn->query("
    SELECT student_id, discount_percentage
    FROM student_discounts
    WHERE created_at IN (
        SELECT MAX(created_at) FROM student_discounts GROUP BY student_id
    )
");
while($d = $discountsRes->fetch_assoc()){
    $studentDiscounts[$d['student_id']] = (float)$d['discount_percentage'];
}

// =========================
// DEFAULT CLASS FEES
// =========================
$defaultClassFees = [5 => 10, 'default' => 15];

// =========================
// STUDENT CALCULATIONS
// =========================
$studentsData = [];
$studentCalculations = [];
$totalPendingMoney = 0;

while($s = $students->fetch_assoc()) {
    $studentsData[] = $s;
    $studentId = $s['student_id'];
    $totalPaid = $studentTotalPayments[$studentId] ?? 0;
    $feeAmount = $defaultClassFees[$s['class_id']] ?? $defaultClassFees['default'];
    $discount = $studentDiscounts[$studentId] ?? 0;
    $totalOwed = $feeAmount - ($feeAmount * $discount / 100);
    $balance = max(0, $totalOwed - $totalPaid);

    // Only show unpaid/partial
    if($totalPaid >= $totalOwed) continue;

    $status = $totalPaid > 0 ? 'partial' : 'unpaid';
    $totalPendingMoney += $balance;

    $studentCalculations[$studentId] = [
        'total_paid' => $totalPaid,
        'total_owed' => $totalOwed,
        'discount' => $discount,
        'status' => $status,
        'balance' => $balance,
        'last_payment' => $lastPaymentDates[$studentId] ?? null
    ];
}

// =========================
// EXPORT CSV if requested
// =========================
if(isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=unpaid_students_report_'.date('Y-m-d').'.csv');
    $output = fopen('php://output', 'w');

    fputcsv($output, [htmlspecialchars($app_name)]);
    if($startDate || $endDate) fputcsv($output, ["Date Range:", $startDate, "to", $endDate]);
    fputcsv($output, []);
    fputcsv($output, ['Student Name','Class','Enrollment Date','Status','Discount','Amount Paid','Total Owed','Balance','Last Payment']);

    foreach($studentsData as $s){
    $id = $s['student_id'];

    // Skip students not in calculations (fully paid)
    if(!isset($studentCalculations[$id])) continue;

    $calc = $studentCalculations[$id];

    fputcsv($output, [
        $s['student_name'],
        $s['class_name'],
        date('Y-m-d', strtotime($s['enrolled_at'])),
        ucfirst($calc['status']),
        number_format($calc['discount'],1),
        number_format($calc['total_paid'],2),
        number_format($calc['total_owed'],2),
        number_format($calc['balance'],2),
        $calc['last_payment'] ? date('Y-m-d', strtotime($calc['last_payment'])) : 'No payments'
    ]);
}


    fputcsv($output, []);
    fputcsv($output, ["Contact: school@gmail.com | +252-612-345-678"]);

    fclose($output);
    exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Unpaid Students Report</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
body { font-family: Arial, sans-serif; margin:20px; }
.summary-cards { display:flex; gap:20px; margin-bottom:20px; }
.card { flex:1; background:#f4f4f4; padding:15px; border-radius:8px; text-align:center; }
.card .value { font-size:1.5em; font-weight:bold; margin-top:5px; }
.data-table { width:100%; border-collapse:collapse; margin-top:20px; }
.data-table th, .data-table td { border:1px solid #ccc; padding:8px; text-align:center; }
.data-table th { background:#eee; }
.buttons { margin-bottom:20px; }
.buttons button, .buttons a { margin-right:10px; padding:8px 12px; border:none; border-radius:5px; text-decoration:none; color:#fff; }
.btn-excel { background:#28a745; }
.btn-pdf { background:#dc3545; }
.btn-print { background:#17a2b8; }
</style>
</head>
<body>

<h1><?= $app_name ?> Unpaid Students Report</h1>
 
<div class="buttons">
    <a href="?export=excel" class="btn-excel"><i class="fas fa-file-excel"></i> Export Excel</a>
    <button id="printPage" class="btn-print"><i class="fas fa-print"></i> Print</button>
</div>

<div id="reportArea">
    <div class="summary-cards">
        <div class="card">
            Total Pending
            <div class="value">$<?= number_format($totalPendingMoney,2) ?></div>
        </div>
        <div class="card">
            Students
            <div class="value"><?= count($studentsData) ?></div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Class</th>
                <th>Enrollment Date</th>
                <th>Status</th>
                <th>Discount</th>
                <th>Amount Paid</th>
                <th>Total Owed</th>
                <th>Balance</th>
                <th>Last Payment</th>
            </tr>
        </thead>
        <tbody>
<?php foreach($studentsData as $s): 
    $id = $s['student_id'];
    if(!isset($studentCalculations[$id])) continue; // skip fully paid students
    $calc = $studentCalculations[$id];
?>
<tr>
    <td><?= htmlspecialchars($s['student_name']) ?></td>
    <td><?= htmlspecialchars($s['class_name']) ?></td>
    <td><?= date('Y-m-d', strtotime($s['enrolled_at'])) ?></td>
    <td><?= ucfirst($calc['status']) ?></td>
    <td><?= number_format($calc['discount'],1) ?>%</td>
    <td><?= number_format($calc['total_paid'],2) ?></td>
    <td><?= number_format($calc['total_owed'],2) ?></td>
    <td><?= number_format($calc['balance'],2) ?></td>
    <td><?= $calc['last_payment'] ? date('Y-m-d', strtotime($calc['last_payment'])) : 'No payments' ?></td>
</tr>
<?php endforeach; ?>
</tbody>

    </table>
</div>

<script>
document.getElementById('printPage').addEventListener('click', () => {
    window.print();
});
</script>

</body>
</html>
