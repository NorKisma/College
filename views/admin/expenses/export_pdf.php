<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
if (!isset($_SESSION['user_id'])) exit('Unauthorized');

// App info
$appRes = $conn->query("SELECT app_name, logo_path FROM app_settings ORDER BY id DESC LIMIT 1");
$app = $appRes->fetch_assoc();
$app_name  = $app['app_name'] ?? 'My Center';
$logo_path = $app['logo_path'] ? '/../college/' . $app['logo_path'] : '';

// Filters
$month = $_GET['month'] ?? date('Y-m');
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';
$searchTerm = "%$search%";

// Fetch expenses
$query = "SELECT e.*, u.first_name, u.last_name
          FROM expenses e
          LEFT JOIN users u ON e.recorded_by = u.id
          WHERE 1=1";
$params=[]; $types="";

if($month){ $query.=" AND DATE_FORMAT(e.expense_date,'%Y-%m')=?"; $params[]=$month; $types.="s"; }
if($category){ $query.=" AND e.category=?"; $params[]=$category; $types.="s"; }
if($search){ $query.=" AND (e.title LIKE ? OR e.category LIKE ?)"; $params[]=$searchTerm; $params[]=$searchTerm; $types.="ss"; }

$query.=" ORDER BY e.expense_date DESC";
$stmt=$conn->prepare($query);
if($params) $stmt->bind_param($types,...$params);
$stmt->execute();
$expenses=$stmt->get_result();

// Stats
$statsQuery = "SELECT SUM(amount) total, COUNT(*) count, AVG(amount) avg, MAX(amount) max FROM expenses WHERE 1=1";
$params=[]; $types="";
if($month){ $statsQuery.=" AND DATE_FORMAT(expense_date,'%Y-%m')=?"; $params[]=$month; $types.="s";}
if($category){ $statsQuery.=" AND category=?"; $params[]=$category; $types.="s";}
if($search){ $statsQuery.=" AND (title LIKE ? OR category LIKE ?)"; $params[]=$searchTerm; $params[]=$searchTerm; $types.="ss"; }
$stmt2=$conn->prepare($statsQuery);
if($params) $stmt2->bind_param($types,...$params);
$stmt2->execute();
$stats=$stmt2->get_result()->fetch_assoc();

// Dompdf
require_once __DIR__.'/../../../vendor/dompdf/autoload.inc.php';
use Dompdf\Dompdf;
use Dompdf\Options;
$options = new Options(); $options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// HTML
ob_start();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Expense Report</title>
<style>
body { font-family: Arial,sans-serif; color:#000; }
.header { text-align:center; margin-bottom:20px; }
.header img { max-height:80px; display:block; margin:0 auto 5px; }
table { width:100%; border-collapse: collapse; font-size:12px; margin-top:15px; }
th, td { border:1px solid #000; padding:6px; }
th { background:#f1f1f1; }
.stats { display:flex; gap:10px; margin-bottom:15px;}
.stat { flex:1; border:1px solid #000; text-align:center; padding:8px; }
</style>
</head>
<body>
<div class="header">
    
    <h2><?= htmlspecialchars($app_name) ?></h2>
    <h3>Expense Report — <?= date('F Y', strtotime($month.'-01')) ?></h3>
</div>


<div class="stats">
    <div class="stat"><b>Total</b><br>$<?= number_format($stats['total'],2) ?></div>
    <div class="stat"><b>Records</b><br><?= $stats['count'] ?></div>
    <div class="stat"><b>Average</b><br>$<?= number_format($stats['avg'],2) ?></div>
    <div class="stat"><b>Maximum</b><br>$<?= number_format($stats['max'],2) ?></div>
</div>

<table>
<thead>
<tr>
<th>#</th><th>Title</th><th>Category</th><th>Amount</th><th>Date</th><th>Recorded By</th>
</tr>
</thead>
<tbody>
<?php while($row=$expenses->fetch_assoc()): ?>
<tr>
<td><?= $row['id'] ?></td>
<td><?= htmlspecialchars($row['title']) ?></td>
<td><?= htmlspecialchars($row['category']) ?></td>
<td>$<?= number_format($row['amount'],2) ?></td>
<td><?= date('d M Y', strtotime($row['expense_date'])) ?></td>
<td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
</tr>
<?php endwhile; ?>
</tbody>
</table>
</body>
</html>
<?php
$html = ob_get_clean();
$dompdf->loadHtml($html);
$dompdf->setPaper('A4','portrait');
$dompdf->render();
$dompdf->stream("Expense_Report_".date('Y_m').".pdf", ["Attachment"=>true]);
exit;
