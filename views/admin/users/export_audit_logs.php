<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

// Authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: login.php');
    exit;
}

// Optional: Only admins can export
$me_stmt = $conn->prepare("SELECT u.role_id, r.name AS role_name FROM users u JOIN roles r ON r.id = u.role_id WHERE u.id = ?");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
if ($me['role_name'] !== 'admin') {
    die('Access denied');
}

// Filters
$filter_action = $_GET['action'] ?? '';
$filter_user   = $_GET['user_id'] ?? '';
$start_date    = $_GET['start_date'] ?? '';
$end_date      = $_GET['end_date'] ?? '';

// Get logs
$params = [];
$sql = "SELECT al.*, u.username, r.name AS role FROM audit_logs al
        LEFT JOIN users u ON al.user_id = u.id
        LEFT JOIN roles r ON u.role_id = r.id
        WHERE 1=1";

if ($filter_action) { $sql .= " AND al.action LIKE ?"; $params[] = "%$filter_action%"; }
if ($filter_user) { $sql .= " AND al.user_id = ?"; $params[] = $filter_user; }
if ($start_date) { $sql .= " AND DATE(al.created_at) >= ?"; $params[] = $start_date; }
if ($end_date) { $sql .= " AND DATE(al.created_at) <= ?"; $params[] = $end_date; }

$sql .= " ORDER BY al.created_at DESC";

$stmt = $conn->prepare($sql);
if ($params) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$logs = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Detect format
$format = $_GET['format'] ?? 'csv'; // default CSV

if ($format === 'csv') {
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="audit_logs.csv"');

    $output = fopen('php://output', 'w');
    fputcsv($output, ['ID','User','Role','Action','Description','Created At']);

    foreach ($logs as $log) {
        fputcsv($output, [
            $log['id'],
            $log['username'],
            $log['role'],
            $log['action'],
            $log['description'],
            $log['created_at']
        ]);
    }
    fclose($output);
    exit;
}

if ($format === 'word') {
    header("Content-Type: application/vnd.ms-word");
    header("Content-Disposition: attachment; filename=audit_logs.doc");

    echo "<h2>Audit Logs</h2>";
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr><th>ID</th><th>User</th><th>Role</th><th>Action</th><th>Description</th><th>Created At</th></tr>";

    foreach ($logs as $log) {
        echo "<tr>";
        echo "<td>{$log['id']}</td>";
        echo "<td>".htmlspecialchars($log['username'])."</td>";
        echo "<td>".htmlspecialchars($log['role'])."</td>";
        echo "<td>".htmlspecialchars($log['action'])."</td>";
        echo "<td>".htmlspecialchars($log['description'])."</td>";
        echo "<td>{$log['created_at']}</td>";
        echo "</tr>";
    }

    echo "</table>";
    exit;
}

// Optional: PDF export using TCPDF or FPDF library
if ($format === 'pdf') {
    require_once __DIR__ . '/../../vendor/autoload.php'; // If using composer TCPDF
    $pdf = new \TCPDF();
    $pdf->AddPage();
    $html = '<h2>Audit Logs</h2><table border="1" cellpadding="4"><tr><th>ID</th><th>User</th><th>Role</th><th>Action</th><th>Description</th><th>Created At</th></tr>';
    foreach ($logs as $log) {
        $html .= "<tr>
                    <td>{$log['id']}</td>
                    <td>".htmlspecialchars($log['username'])."</td>
                    <td>".htmlspecialchars($log['role'])."</td>
                    <td>".htmlspecialchars($log['action'])."</td>
                    <td>".htmlspecialchars($log['description'])."</td>
                    <td>{$log['created_at']}</td>
                  </tr>";
    }
    $html .= '</table>';
    $pdf->writeHTML($html);
    $pdf->Output('audit_logs.pdf', 'D');
    exit;
}
?>
<div class="container mt-4">
    <h3>Export Audit Logs</h3>
    <form method="get" action="export_logs.php" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="action" class="form-control" placeholder="Action keyword">
        </div>
        <div class="col-md-3">
            <select name="user_id" class="form-select">
                <option value="">All Users</option>
                <?php foreach($users as $u): ?>
                    <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2"><input type="date" name="start_date" class="form-control"></div>
        <div class="col-md-2"><input type="date" name="end_date" class="form-control"></div>
        <div class="col-md-2">
            <select name="format" class="form-select">
                <option value="csv">Excel (CSV)</option>
                <option value="word">Word (DOC)</option>
                <option value="pdf">PDF</option>
            </select>
        </div>
        <div class="col-md-12 mt-2">
            <button class="btn btn-primary">Export Logs</button>
        </div>
    </form>
</div>
