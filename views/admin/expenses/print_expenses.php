<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    exit('Unauthorized');
}

// Get app settings
$appRes = $conn->query("SELECT app_name, logo_path, show_type FROM app_settings ORDER BY id DESC LIMIT 1");
$app = $appRes->fetch_assoc();

$app_name  = $app['app_name'] ?? 'My Center';
$logo_path = $app['logo_path'] ? '/../college/' . $app['logo_path'] : '';
$show_type = $app['show_type'] ?? 'text';

// Filters
$month    = $_GET['month'] ?? date('Y-m');
$category = $_GET['category'] ?? '';
$search   = $_GET['search'] ?? '';

// Build query
$query = "SELECT e.*, u.first_name, u.last_name
          FROM expenses e
          LEFT JOIN users u ON e.recorded_by = u.id
          WHERE 1=1";

$params = [];
$types  = "";

if ($month) {
    $query .= " AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?";
    $params[] = $month;
    $types .= 's';
}

if ($category) {
    $query .= " AND e.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $query .= " AND (e.title LIKE ? OR e.category LIKE ?)";
    $searchTerm = "%$search%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

$query .= " ORDER BY e.expense_date DESC";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$expenses = $stmt->get_result();

// Stats query
$statsQuery = "SELECT 
                SUM(amount) total,
                COUNT(*) count,
                AVG(amount) avg,
                MAX(amount) max
               FROM expenses WHERE 1=1";

$params = [];
$types  = "";

if ($month) {
    $statsQuery .= " AND DATE_FORMAT(expense_date,'%Y-%m') = ?";
    $params[] = $month;
    $types .= 's';
}

if ($category) {
    $statsQuery .= " AND category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $statsQuery .= " AND (title LIKE ? OR category LIKE ?)";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $types .= 'ss';
}

$stmt2 = $conn->prepare($statsQuery);
if ($params) {
    $stmt2->bind_param($types, ...$params);
}
$stmt2->execute();
$stats = $stmt2->get_result()->fetch_assoc();

// Get categories for filter dropdown
$categoriesQuery = $conn->query("SELECT DISTINCT category FROM expenses ORDER BY category");
$categories = [];
while ($cat = $categoriesQuery->fetch_assoc()) {
    $categories[] = $cat['category'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Expense Report | <?= htmlspecialchars($app_name) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Reset & Base */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.5;
            padding: 20px;
            min-height: 100vh;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 16px;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.06);
            overflow: hidden;
            position: relative;
        }

        /* Header */
        .report-header {
            background: linear-gradient(135deg, #1e3a8a 0%, #3b82f6 100%);
            color: white;
            padding: 30px 40px;
            position: relative;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 25px;
        }

        .logo-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logo-img {
            height: 70px;
            width: auto;
            max-width: 180px;
            object-fit: contain;
            filter: brightness(0) invert(1);
        }

        .center-info h1 {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
            letter-spacing: -0.5px;
        }

        .center-info .subtitle {
            font-size: 16px;
            font-weight: 400;
            opacity: 0.9;
        }

        .report-meta {
            text-align: right;
        }

        .report-meta .period {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 5px;
        }

        .report-meta .date {
            font-size: 15px;
            opacity: 0.9;
        }

        /* Action Bar */
        .action-bar {
            padding: 20px 40px;
            background: #f1f5f9;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .action-buttons {
            display: flex;
            gap: 12px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: #3b82f6;
            color: white;
        }

        .btn-primary:hover {
            background: #2563eb;
            transform: translateY(-1px);
        }

        .btn-danger {
            background: #ef4444;
            color: white;
        }

        .btn-danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .btn-success {
            background: #10b981;
            color: white;
        }

        .btn-success:hover {
            background: #059669;
            transform: translateY(-1px);
        }

        .btn-outline {
            background: transparent;
            border: 1px solid #cbd5e1;
            color: #64748b;
        }

        .btn-outline:hover {
            background: #f8fafc;
            border-color: #94a3b8;
        }

        /* Filter Section */
        .filter-section {
            padding: 25px 40px;
            background: white;
            border-bottom: 1px solid #e2e8f0;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 14px;
            font-weight: 600;
            color: #475569;
        }

        .filter-input, .filter-select {
            padding: 12px 16px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font-size: 14px;
            transition: border 0.2s ease;
            background: white;
        }

        .filter-input:focus, .filter-select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .apply-filter-btn {
            align-self: flex-end;
            padding: 12px 24px;
            background: #3b82f6;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .apply-filter-btn:hover {
            background: #2563eb;
        }

        /* Stats Cards */
        .stats-section {
            padding: 30px 40px;
            background: #f8fafc;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
        }

        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-label {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
            font-weight: 500;
        }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: #1e293b;
            margin-bottom: 4px;
        }

        .stat-change {
            font-size: 13px;
            color: #10b981;
            font-weight: 500;
        }

        .stat-card:nth-child(1) .stat-value { color: #3b82f6; }
        .stat-card:nth-child(2) .stat-value { color: #10b981; }
        .stat-card:nth-child(3) .stat-value { color: #f59e0b; }
        .stat-card:nth-child(4) .stat-value { color: #ef4444; }

        /* Table Section */
        .table-section {
            padding: 0 40px 30px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 12px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.03);
        }

        .data-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        .data-table thead {
            background: #f1f5f9;
        }

        .data-table th {
            padding: 18px 16px;
            text-align: left;
            font-weight: 600;
            color: #475569;
            border-bottom: 2px solid #e2e8f0;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .data-table tbody tr {
            border-bottom: 1px solid #f1f5f9;
            transition: background 0.15s ease;
        }

        .data-table tbody tr:hover {
            background: #f8fafc;
        }

        .data-table td {
            padding: 16px;
            font-size: 14px;
            color: #334155;
        }

        .amount-cell {
            font-weight: 600;
            color: #059669;
        }

        .category-badge {
            display: inline-block;
            padding: 4px 12px;
            background: #e0f2fe;
            color: #0369a1;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
        }

        /* Footer */
        .report-footer {
            padding: 25px 40px;
            background: #1e293b;
            color: #cbd5e1;
            text-align: center;
            border-top: 1px solid #334155;
        }

        .footer-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .footer-info {
            font-size: 14px;
        }

        .footer-info a {
            color: #60a5fa;
            text-decoration: none;
        }

        .footer-info a:hover {
            text-decoration: underline;
        }

        .generated-info {
            font-size: 13px;
            opacity: 0.8;
        }

        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            body { padding: 0; background: white; }
            .container { box-shadow: none; border-radius: 0; }
            .action-bar, .filter-section { display: none; }
            .report-header { background: #1e3a8a !important; -webkit-print-color-adjust: exact; }
            .data-table { min-width: 100%; }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .header-content { flex-direction: column; text-align: center; }
            .report-meta { text-align: center; }
        }

        @media (max-width: 992px) {
            .stats-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (max-width: 768px) {
            .report-header, .action-bar, .filter-section, 
            .stats-section, .table-section, .report-footer {
                padding: 20px;
            }
            
            .header-content { gap: 15px; }
            .logo-img { height: 60px; }
            .center-info h1 { font-size: 24px; }
            .action-bar { flex-direction: column; align-items: flex-start; }
            .action-buttons { width: 100%; justify-content: center; flex-wrap: wrap; }
            .filter-grid { grid-template-columns: 1fr; }
            .stats-grid { grid-template-columns: 1fr; }
            .footer-content { flex-direction: column; text-align: center; }
        }

        @media (max-width: 576px) {
            body { padding: 10px; }
            .btn { padding: 10px 16px; font-size: 13px; }
            .stat-value { font-size: 28px; }
            .data-table th, .data-table td { padding: 12px 10px; }
        }

        /* Loading State */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #64748b;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 20px;
            color: #cbd5e1;
        }

        .empty-state h3 {
            font-size: 18px;
            margin-bottom: 10px;
            color: #475569;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="report-header">
            <div class="header-content">
                <div class="logo-section">
                    <?php if ($logo_path): ?>
                        <img src="<?= $logo_path ?>" alt="Center Logo" class="logo-img">
                    <?php endif; ?>
                    <div class="center-info">
                        <h1><?= htmlspecialchars($app_name) ?></h1>
                        <div class="subtitle">Financial Management System</div>
                    </div>
                </div>
                <div class="report-meta">
                    <div class="period">Expense Report</div>
                    <div class="date"><?= date('F Y', strtotime($month.'-01')) ?></div>
                </div>
            </div>
        </div>

        <!-- Action Bar -->
        <div class="action-bar no-print">
            <div>
                <h3 style="color: #475569; font-size: 16px;">Report Actions</h3>
            </div>
            <div class="action-buttons">
                <button onclick="window.print()" class="btn btn-outline">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <a href="export_pdf.php?month=<?= urlencode($month) ?>&category=<?= urlencode($category) ?>&search=<?= urlencode($search) ?>" 
                   target="_blank" class="btn btn-danger">
                    <i class="fas fa-file-pdf"></i> Export PDF
                </a>
                <a href="export_excel.php?month=<?= urlencode($month) ?>&category=<?= urlencode($category) ?>&search=<?= urlencode($search) ?>" 
                   class="btn btn-success">
                    <i class="fas fa-file-excel"></i> Export Excel
                </a>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section no-print">
            <form method="GET" action="" class="filter-grid">
                <div class="filter-group">
                    <label class="filter-label">Month</label>
                    <input type="month" name="month" value="<?= htmlspecialchars($month) ?>" class="filter-input">
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Category</label>
                    <select name="category" class="filter-select">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat) ?>" <?= $category == $cat ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="filter-group">
                    <label class="filter-label">Search</label>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Search title or category..." class="filter-input">
                </div>
                
                <div class="filter-group">
                    <button type="submit" class="apply-filter-btn">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                </div>
            </form>
        </div>

        <!-- Stats Cards -->
        <div class="stats-section">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-label">Total Expenses</div>
                    <div class="stat-value">$<?= number_format($stats['total'], 2) ?></div>
                    <div class="stat-change">Current Period</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Records Count</div>
                    <div class="stat-value"><?= $stats['count'] ?></div>
                    <div class="stat-change">Transactions</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Average Amount</div>
                    <div class="stat-value">$<?= number_format($stats['avg'], 2) ?></div>
                    <div class="stat-change">Per Transaction</div>
                </div>
                
                <div class="stat-card">
                    <div class="stat-label">Maximum Expense</div>
                    <div class="stat-value">$<?= number_format($stats['max'], 2) ?></div>
                    <div class="stat-change">Highest Single Expense</div>
                </div>
            </div>
        </div>

        <!-- Data Table -->
        <div class="table-section">
            <div class="table-container">
                <?php if ($expenses->num_rows > 0): ?>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Amount</th>
                                <th>Date</th>
                                <th>Recorded By</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while($row = $expenses->fetch_assoc()): ?>
                            <tr>
                                <td>#<?= $row['id'] ?></td>
                                <td><?= htmlspecialchars($row['title']) ?></td>
                                <td>
                                    <span class="category-badge"><?= htmlspecialchars($row['category']) ?></span>
                                </td>
                                <td class="amount-cell">$<?= number_format($row['amount'], 2) ?></td>
                                <td><?= date('d M Y', strtotime($row['expense_date'])) ?></td>
                                <td><?= htmlspecialchars($row['first_name'].' '.$row['last_name']) ?></td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <div class="empty-state">
                        <i class="fas fa-chart-line"></i>
                        <h3>No expenses found</h3>
                        <p>Try adjusting your filters or add new expense records.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Footer -->
        <div class="report-footer">
            <div class="footer-content">
                <div class="footer-info">
                    For inquiries: <a href="mailto:info@yourcenter.com">info@yourcenter.com</a> | 
                    <a href="tel:+252610000000">+252 61 0000000</a>
                </div>
                <div class="generated-info">
                    Generated on <?= date('F j, Y \a\t g:i A') ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Add loading state to buttons
        document.addEventListener('DOMContentLoaded', function() {
            const buttons = document.querySelectorAll('.btn, .apply-filter-btn');
            buttons.forEach(button => {
                button.addEventListener('click', function(e) {
                    if (this.getAttribute('href') || this.type === 'submit') {
                        this.classList.add('loading');
                        setTimeout(() => this.classList.remove('loading'), 2000);
                    }
                });
            });
            
            // Highlight active filters
            const params = new URLSearchParams(window.location.search);
            if (params.toString()) {
                document.querySelector('.report-title').innerHTML += 
                    ' <span style="font-size:14px;opacity:0.8">(Filtered)</span>';
            }
        });
    </script>
</body>
</html>