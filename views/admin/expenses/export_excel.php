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
if($month){ $query.=" AND DATE_FORMAT(e.expense_date,'%Y-%m')=?"; $params[]=$month; $types.="s";}
if($category){ $query.=" AND e.category=?"; $params[]=$category; $types.="s";}
if($search){ $query.=" AND (e.title LIKE ? OR e.category LIKE ?)"; $params[]=$searchTerm; $params[]=$searchTerm; $types.="ss";}
$query.=" ORDER BY e.expense_date DESC";
$stmt=$conn->prepare($query);
if($params) $stmt->bind_param($types,...$params);
$stmt->execute();
$expenses=$stmt->get_result();

// Output Excel headers
header("Content-Type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=Expense_Report_".date('Y_m').".xls");
header("Pragma: no-cache");
header("Expires: 0");

// App name and logo (logo will just show URL in Excel, images require embedding)
echo "Report for: $app_name\n";
echo "Month: ".date('F Y', strtotime($month.'-01'))."\n\n";

// Column headers
echo "ID\tTitle\tCategory\tAmount\tDate\tRecorded By\n";

// Data
while($row=$expenses->fetch_assoc()){
    echo $row['id']."\t".
         htmlspecialchars($row['title'])."\t".
         htmlspecialchars($row['category'])."\t".
         number_format($row['amount'],2)."\t".
         date('d M Y', strtotime($row['expense_date']))."\t".
         htmlspecialchars($row['first_name'].' '.$row['last_name'])."\n";
}
exit;
