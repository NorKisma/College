<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

$child_id = $_POST['child_id'] ?? 0;

// 1. Get the first enrollment date
$first_class_query = $conn->prepare("
    SELECT MIN(created_at) AS first_day
    FROM class_students
    WHERE student_id = ?
");
$first_class_query->bind_param("i", $child_id);
$first_class_query->execute();
$first_class_result = $first_class_query->get_result()->fetch_assoc();
$first_day = $first_class_result['first_day'] ?? date('Y-m-01');

// 2. Get all student_fees for this student
$fees_query = $conn->prepare("
    SELECT id, paid_amount, original_amount, status, payment_date, created_at
    FROM student_fees
    WHERE student_id = ?
    ORDER BY created_at ASC
");
$fees_query->bind_param("i", $child_id);
$fees_query->execute();
$fees_result = $fees_query->get_result();

// 3. Prepare month tracking from first_day to now
$all_months = [];
$start = new DateTime($first_day);
$end = new DateTime(date('Y-m-01')); // current month
$interval = new DateInterval('P1M');
$period = new DatePeriod($start, $interval, $end->add($interval));

foreach ($period as $dt) {
    $all_months[$dt->format('Y-m')] = [
        'paid' => false,
        'paid_amount' => 0,
        'status' => 'unpaid'
    ];
}

// 4. Mark paid or partial months from student_fees
while ($fee = $fees_result->fetch_assoc()) {
    // Use created_at month/year as the fee's month
    $key = (new DateTime($fee['created_at']))->format('Y-m');
    if (isset($all_months[$key])) {
        $all_months[$key]['paid_amount'] += $fee['paid_amount'];
        $all_months[$key]['status'] = $fee['status'];
        if ($fee['status'] == 'paid') {
            $all_months[$key]['paid'] = true;
        } elseif ($fee['status'] == 'partial') {
            $all_months[$key]['paid'] = false;
        }
    }
}

// 5. Totals
$total_paid = array_sum(array_column($all_months, 'paid_amount'));
$total_pending = count(array_filter($all_months, fn($m) => $m['status'] != 'paid'));
$total_months = count($all_months);

?>
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card stat-card bg-success text-white">
            <div class="stat-icon"><i class="bi bi-currency-dollar"></i></div>
            <h3>$<?php echo number_format($total_paid, 2); ?></h3>
            <p>Inta la bixiyey</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card bg-danger text-white">
            <div class="stat-icon"><i class="bi bi-clock"></i></div>
            <h3><?php echo $total_pending; ?></h3>
            <p>Bilaha Aan la bixin</p>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card stat-card bg-info text-white">
            <div class="stat-icon"><i class="bi bi-calendar"></i></div>
            <h3><?php echo $total_months; ?></h3>
            <p>Wadarta Bilaha</p>
        </div>
    </div>
</div>

<div class="table-responsive">
    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Bisha/Sanadka</th>
                <th>Xaalada</th>
                <th>Inta la bixiyey</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($all_months as $m => $info): 
                $dt = DateTime::createFromFormat('Y-m', $m);
            ?>
                <tr>
                    <td><?php echo $dt->format('F Y'); ?></td>
                    <td>
                        <span class="badge bg-<?php 
                            echo $info['status'] == 'paid' ? 'success' : 
                                 ($info['status'] == 'partial' ? 'warning' : 'danger'); 
                        ?>">
                            <?php echo ucfirst($info['status']); ?>
                        </span>
                    </td>
                    <td>$<?php echo number_format($info['paid_amount'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
