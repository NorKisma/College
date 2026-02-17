<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

$appRes = $conn->query("SELECT app_name, logo_path FROM app_settings ORDER BY id DESC LIMIT 1");
$app = $appRes->fetch_assoc();
$app_name  = $app['app_name'] ?? 'My Center';

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

if ($filterClass) {
    $where[] = "c.id = $filterClass";
}
if ($searchName !== '') {
    $safeSearch = $conn->real_escape_string($searchName);
    $where[] = "s.name LIKE '%$safeSearch%'";
}
if ($startDate && $endDate) {
    $where[] = "(cs.created_at BETWEEN '{$conn->real_escape_string($startDate)}' AND '{$conn->real_escape_string($endDate)}')";
} elseif ($startDate) {
    $where[] = "cs.created_at >= '{$conn->real_escape_string($startDate)}'";
} elseif ($endDate) {
    $where[] = "cs.created_at <= '{$conn->real_escape_string($endDate)}'";
}

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
$studentFeesRes = $conn->query("
    SELECT student_id, SUM(paid_amount) as total_paid, MAX(created_at) as last_payment_date
    FROM student_fees
    GROUP BY student_id
");
while ($sf = $studentFeesRes->fetch_assoc()) {
    $studentTotalPayments[$sf['student_id']] = (float)$sf['total_paid'];
    $lastPaymentDates[$sf['student_id']] = $sf['last_payment_date'];
}

$studentDiscounts = [];
$discountsRes = $conn->query("
    SELECT student_id, discount_percentage
    FROM student_discounts
    WHERE created_at IN (
        SELECT MAX(created_at)
        FROM student_discounts
        GROUP BY student_id
    )
");
while ($d = $discountsRes->fetch_assoc()) {
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
$totalPaidMoney = 0;
$totalFullyPaid = 0;
$totalPartiallyPaid = 0;

while ($s = $students->fetch_assoc()) {
    $studentsData[] = $s;
    $studentId = $s['student_id'];
    $totalPaid = $studentTotalPayments[$studentId] ?? 0;

    $feeAmount = $defaultClassFees[$s['class_id']] ?? $defaultClassFees['default'];
    $discount = $studentDiscounts[$studentId] ?? 0;
    $totalOwed = $feeAmount - ($feeAmount * $discount / 100);

    $status = $totalPaid >= $totalOwed ? 'paid' : 'partial';

    $studentCalculations[$studentId] = [
        'total_paid' => $totalPaid,
        'total_owed' => $totalOwed,
        'discount' => $discount,
        'status' => $status,
        'last_payment' => $lastPaymentDates[$studentId] ?? null
    ];

    $totalPaidMoney += min($totalPaid, $totalOwed);
    if ($status === 'paid') $totalFullyPaid++;
    else $totalPartiallyPaid++;
}

$filteredTotalStudents = count($studentsData);
$paymentRate = $filteredTotalStudents > 0 ? ($totalFullyPaid / $filteredTotalStudents * 100) : 0;

// =========================
// EXPORT EXCEL
// =========================
if (isset($_GET['export']) && $_GET['export'] === 'excel') {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename=paid_students_report_' . date('Y-m-d') . '.csv');
    $output = fopen('php://output', 'w');
    fputcsv($output, ['Student Name','Class','Enrollment Date','Status','Discount','Amount Paid','Total Owed','Balance','Last Payment']);
    foreach ($studentsData as $s) {
        $studentId = $s['student_id'];
        $calc = $studentCalculations[$studentId];
        $balance = max(0, $calc['total_owed'] - $calc['total_paid']);
        fputcsv($output, [
            $s['student_name'],
            $s['class_name'],
            date('Y-m-d', strtotime($s['enrolled_at'])),
            ucfirst($calc['status']),
            number_format($calc['discount'],1),
            number_format($calc['total_paid'],2),
            number_format($calc['total_owed'],2),
            number_format($balance,2),
            $calc['last_payment'] ? date('Y-m-d', strtotime($calc['last_payment'])) : 'No payments'
        ]);
    }
    fclose($output);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Paid Students Report</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<style>
:root {
    --primary: #2c3e50;
    --secondary: #3498db;
    --success: #27ae60;
    --warning: #f39c12;
    --danger: #e74c3c;
    --light: #ecf0f1;
    --dark: #2c3e50;
    --gray: #95a5a6;
    --light-gray: #f8f9fa;
    --border: #dee2e6;
}

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: #f5f7fa;
    color: #333;
    line-height: 1.6;
    padding: 20px;
    min-height: 100vh;
}

.container {
    max-width: 1400px;
    margin: 0 auto;
}

/* Header Styles */
.header {
    background: linear-gradient(135deg, var(--primary) 0%, #1a2530 100%);
    color: white;
    padding: 2rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.logo-section h1 {
    font-size: 2.2rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
    letter-spacing: 0.5px;
}

.logo-section h3 {
    font-size: 1.4rem;
    font-weight: 400;
    opacity: 0.9;
}

.date-range {
    background: rgba(255,255,255,0.1);
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    font-size: 0.95rem;
    backdrop-filter: blur(10px);
}

/* Action Buttons */
.actions-bar {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    margin-bottom: 2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
}

.export-buttons {
    display: flex;
    gap: 1rem;
}

.btn {
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: 8px;
    font-size: 0.95rem;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
}

.btn-excel {
    background: linear-gradient(to right, var(--success), #2ecc71);
    color: white;
}

.btn-pdf {
    background: linear-gradient(to right, var(--danger), #c0392b);
    color: white;
}

.btn-print {
    background: linear-gradient(to right, var(--secondary), #2980b9);
    color: white;
}

.btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

/* Summary Cards */
.summary-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2rem;
}

.summary-card {
    background: white;
    padding: 1.5rem;
    border-radius: 12px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    text-align: center;
    transition: transform 0.3s ease;
    border-top: 4px solid var(--secondary);
}

.summary-card:hover {
    transform: translateY(-5px);
}

.summary-card:nth-child(2) {
    border-top-color: var(--success);
}

.summary-card:nth-child(3) {
    border-top-color: var(--warning);
}

.summary-card:nth-child(4) {
    border-top-color: var(--danger);
}

.card-label {
    font-size: 0.9rem;
    color: var(--gray);
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 0.5rem;
}

.card-value {
    font-size: 2.2rem;
    font-weight: 700;
    color: var(--primary);
}

.card-value.currency::before {
    content: '$';
    font-size: 1.5rem;
    vertical-align: super;
}

/* Table Styles */
.report-container {
    background: white;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    margin-bottom: 2rem;
    overflow-x: auto;
}

.report-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 1000px;
}

.report-table thead {
    background: linear-gradient(to right, var(--primary), #34495e);
}

.report-table th {
    padding: 1.2rem 1rem;
    text-align: left;
    color: white;
    font-weight: 600;
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border-right: 1px solid rgba(255,255,255,0.1);
}

.report-table th:last-child {
    border-right: none;
}

.report-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background-color 0.2s ease;
}

.report-table tbody tr:hover {
    background-color: var(--light-gray);
}

.report-table td {
    padding: 1rem;
    font-size: 0.95rem;
}

.status-badge {
    padding: 0.4rem 1rem;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.3px;
}

.status-paid {
    background-color: rgba(39, 174, 96, 0.15);
    color: var(--success);
}

.status-partial {
    background-color: rgba(243, 156, 18, 0.15);
    color: var(--warning);
}

/* Footer */
.report-footer {
    text-align: center;
    padding: 2rem;
    color: var(--gray);
    font-size: 0.9rem;
    border-top: 1px solid var(--border);
    background: white;
    border-radius: 12px;
}

.contact-info {
    display: flex;
    justify-content: center;
    gap: 2rem;
    margin-top: 0.5rem;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .actions-bar {
        flex-direction: column;
        align-items: stretch;
    }
    
    .export-buttons {
        justify-content: center;
    }
    
    .summary-grid {
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    }
    
    .card-value {
        font-size: 1.8rem;
    }
    
    body {
        padding: 10px;
    }
}

@media (max-width: 480px) {
    .summary-grid {
        grid-template-columns: 1fr;
    }
    
    .btn {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
    }
    
    .header {
        padding: 1.5rem;
    }
    
    .logo-section h1 {
        font-size: 1.8rem;
    }
}

/* Print Styles */
@media print {
    .no-print {
        display: none !important;
    }
    
    body {
        background: white;
        padding: 0;
        margin: 0;
    }
    
    .container {
        max-width: 100%;
        margin: 0;
    }
    
    .report-container {
        box-shadow: none;
        border: none;
    }
    
    .report-table th {
        background: #f0f0f0 !important;
        color: black !important;
        -webkit-print-color-adjust: exact;
    }
    
    .summary-card {
        break-inside: avoid;
    }
}

/* Loading Animation */
.loading {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(255,255,255,0.9);
    z-index: 9999;
    justify-content: center;
    align-items: center;
    flex-direction: column;
}

.loading-spinner {
    width: 50px;
    height: 50px;
    border: 5px solid var(--light);
    border-top-color: var(--secondary);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}
</style>
</head>
<body>
<div class="loading" id="loading">
    <div class="loading-spinner"></div>
    <p style="margin-top: 20px;">Generating PDF...</p>
</div>

<div class="container">
    <header class="header">
        <div class="header-content">
            <div class="logo-section">
                <h1><?= htmlspecialchars($app_name) ?></h1>
                <h3>Paid Students Report</h3>
            </div>
            <?php if($startDate || $endDate): ?>
            <div class="date-range">
                <i class="fas fa-calendar-alt"></i>
                Date Range: <?= htmlspecialchars($startDate) ?> — <?= htmlspecialchars($endDate) ?>
            </div>
            <?php endif; ?>
        </div>
    </header>

    <div class="actions-bar">
        <div class="report-info">
            <strong><?= $filteredTotalStudents ?></strong> students found
        </div>
        <div class="export-buttons">
            <a href="?export=excel" class="btn btn-excel no-print">
                <i class="fas fa-file-excel"></i> Export Excel
            </a>
            <button id="exportPDF" class="btn btn-pdf no-print">
                <i class="fas fa-file-pdf"></i> Export PDF
            </button>
            <button id="printPage" class="btn btn-print no-print">
                <i class="fas fa-print"></i> Print
            </button>
        </div>
    </div>

    <div id="printArea">
        <div class="summary-grid">
            <div class="summary-card">
                <div class="card-label">Total Collected</div>
                <div class="card-value currency"><?= number_format($totalPaidMoney,2) ?></div>
            </div>
            <div class="summary-card">
                <div class="card-label">Total Students</div>
                <div class="card-value"><?= $filteredTotalStudents ?></div>
            </div>
            <div class="summary-card">
                <div class="card-label">Fully Paid</div>
                <div class="card-value"><?= $totalFullyPaid ?></div>
            </div>
            <div class="summary-card">
                <div class="card-label">Partially Paid</div>
                <div class="card-value"><?= $totalPartiallyPaid ?></div>
            </div>
        </div>

        <div class="report-container">
            <table class="report-table" id="reportTable">
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
                        $studentId = $s['student_id'];
                        $calc = $studentCalculations[$studentId];
                        $balance = max(0, $calc['total_owed'] - $calc['total_paid']);
                    ?>
                    <tr>
                        <td><?= htmlspecialchars($s['student_name']) ?></td>
                        <td><?= htmlspecialchars($s['class_name']) ?></td>
                        <td><?= date('Y-m-d', strtotime($s['enrolled_at'])) ?></td>
                        <td>
                            <span class="status-badge status-<?= $calc['status'] ?>">
                                <?= ucfirst($calc['status']) ?>
                            </span>
                        </td>
                        <td><?= number_format($calc['discount'],1) ?>%</td>
                        <td><strong>$<?= number_format($calc['total_paid'],2) ?></strong></td>
                        <td>$<?= number_format($calc['total_owed'],2) ?></td>
                        <td>
                            <?php if($balance > 0): ?>
                                <span style="color: var(--danger);">$<?= number_format($balance,2) ?></span>
                            <?php else: ?>
                                <span style="color: var(--success);">$0.00</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($calc['last_payment']): ?>
                                <i class="far fa-calendar-check"></i>
                                <?= date('Y-m-d', strtotime($calc['last_payment'])) ?>
                            <?php else: ?>
                                <span style="color: var(--gray);">No payments</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="report-footer">
            <p>Report generated on <?= date('F j, Y \a\t g:i A') ?></p>
            <div class="contact-info">
                <span><i class="fas fa-envelope"></i> school@gmail.com</span>
                <span><i class="fas fa-phone"></i> +252-612-345-678</span>
            </div>
        </div>
    </div>
</div>

<script>
// Print functionality
document.getElementById('printPage').addEventListener('click', () => {
    window.print();
});

// PDF export with loading indicator
document.getElementById('exportPDF').addEventListener('click', function() {
    const loading = document.getElementById('loading');
    const area = document.getElementById('printArea');
    
    loading.style.display = 'flex';
    
    setTimeout(() => {
        html2canvas(area, { 
            scale: 2,
            useCORS: true,
            backgroundColor: '#ffffff'
        }).then(canvas => {
            const imgData = canvas.toDataURL('image/png');
            const pdf = new jspdf.jsPDF('p', 'pt', 'a4');
            
            const pdfWidth = pdf.internal.pageSize.getWidth();
            const imgHeight = canvas.height * pdfWidth / canvas.width;
            
            pdf.addImage(imgData, 'PNG', 0, 0, pdfWidth, imgHeight);
            
            // Add footer
            pdf.setFontSize(10);
            const pageHeight = pdf.internal.pageSize.getHeight();
            pdf.text('Generated on ' + new Date().toLocaleDateString(), 
                    pdfWidth - 100, pageHeight - 20);
            
            pdf.save('paid_students_report_' + new Date().toISOString().slice(0,10) + '.pdf');
            
            loading.style.display = 'none';
        }).catch(error => {
            console.error('PDF generation error:', error);
            loading.style.display = 'none';
            alert('Error generating PDF. Please try again.');
        });
    }, 500);
});

// Add subtle row highlighting
document.addEventListener('DOMContentLoaded', function() {
    const tableRows = document.querySelectorAll('.report-table tbody tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.backgroundColor = '#f8f9fa';
        });
        row.addEventListener('mouseleave', function() {
            this.style.backgroundColor = '';
        });
    });
});
</script>

</body>
</html>