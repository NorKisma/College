<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

// Get theme from database
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1; // default light
$isDark = $themeMode == 0;

// Get class ID from URL
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;

// Get all classes for dropdown
$classes = $conn->query("SELECT id, name FROM classes ORDER BY name");

// If no class selected, show the first one
if ($class_id == 0 && $classes->num_rows > 0) {
    $first_class = $classes->fetch_assoc();
    $class_id = $first_class['id'];
    $classes->data_seek(0); // Reset pointer
}

// Get class details
$class = $conn->query("
    SELECT c.*, s.name as section_name, s.payable 
    FROM classes c 
    LEFT JOIN sections s ON c.section_id = s.id 
    WHERE c.id = $class_id
")->fetch_assoc();

if (!$class) {
    die("Class not found!");
}

// Get date filters
$start_date = $_GET['start_date'] ?? date('Y-m-01'); // First day of current month
$end_date = $_GET['end_date'] ?? date('Y-m-t'); // Last day of current month

// Get search filter
$search = $_GET['search'] ?? '';

// Get students in this class
$students_query = "
    SELECT s.*, cs.academic_year 
    FROM students s 
    JOIN class_students cs ON s.id = cs.student_id 
    WHERE cs.class_id = $class_id
";

if ($search) {
    $students_query .= " AND (s.name LIKE '%" . $conn->real_escape_string($search) . "%' 
                          OR s.guardian_name LIKE '%" . $conn->real_escape_string($search) . "%'
                          OR s.guardian_phone LIKE '%" . $conn->real_escape_string($search) . "%')";
}

$students = $conn->query($students_query);
$total_students = $students->num_rows;

// Get fee categories for this class's section
$fee_categories = $conn->query("
    SELECT fc.*, sf.amount as section_fee
    FROM fee_categories fc
    LEFT JOIN section_fees sf ON fc.id = sf.fee_category_id AND sf.section_id = {$class['section_id']}
    ORDER BY fc.name
");

// Get all fee records for this class within date range
$fee_records = $conn->query("
    SELECT 
        sf.*,
        s.name as student_name,
        cf.amount as class_fee_amount,
        fc.name as fee_category_name,
        cf.month,
        cf.year
    FROM student_fees sf
    JOIN students s ON sf.student_id = s.id
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    JOIN fee_categories fc ON cf.fee_category_id = fc.id
    JOIN class_students cs ON s.id = cs.student_id
    WHERE cs.class_id = $class_id
    AND sf.payment_date BETWEEN '$start_date' AND '$end_date'
    ORDER BY sf.payment_date DESC
");

// Calculate statistics
$total_collected = 0;
$total_due = 0;
$total_discounts = 0;
$paid_count = 0;
$partial_count = 0;
$unpaid_count = 0;

while ($record = $fee_records->fetch_assoc()) {
    $total_collected += $record['paid_amount'];
    $total_discounts += $record['discount_amount'] ?? 0;
    
    if ($record['status'] == 'paid') $paid_count++;
    elseif ($record['status'] == 'partial') $partial_count++;
    else $unpaid_count++;
    
    $total_due += $record['original_amount'] - $record['discount_amount'] - $record['paid_amount'];
}

// Reset pointer for later use
$fee_records->data_seek(0);

// Get monthly trend data
$monthly_trend = $conn->query("
    SELECT 
        DATE_FORMAT(sf.payment_date, '%Y-%m') as month,
        SUM(sf.paid_amount) as total_collected,
        COUNT(DISTINCT sf.student_id) as students_paid
    FROM student_fees sf
    JOIN students s ON sf.student_id = s.id
    JOIN class_students cs ON s.id = cs.student_id
    WHERE cs.class_id = $class_id
    AND sf.payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
    AND sf.payment_date IS NOT NULL
    GROUP BY DATE_FORMAT(sf.payment_date, '%Y-%m')
    ORDER BY month
");

// Get fee category breakdown
$category_breakdown = $conn->query("
    SELECT 
        fc.name as category_name,
        SUM(sf.original_amount) as total_expected,
        SUM(sf.paid_amount) as total_collected,
        SUM(sf.discount_amount) as total_discounts,
        COUNT(*) as total_invoices
    FROM student_fees sf
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    JOIN fee_categories fc ON cf.fee_category_id = fc.id
    JOIN students s ON sf.student_id = s.id
    JOIN class_students cs ON s.id = cs.student_id
    WHERE cs.class_id = $class_id
    AND sf.payment_date BETWEEN '$start_date' AND '$end_date'
    GROUP BY fc.name
    ORDER BY total_collected DESC
");

// Get top defaulters
$top_defaulters = $conn->query("
    SELECT 
        s.name as student_name,
        SUM(sf.original_amount - sf.discount_amount - sf.paid_amount) as total_due,
        COUNT(*) as overdue_invoices
    FROM student_fees sf
    JOIN students s ON sf.student_id = s.id
    JOIN class_students cs ON s.id = cs.student_id
    WHERE cs.class_id = $class_id
    AND sf.status != 'paid'
    AND sf.payment_date < CURDATE()
    GROUP BY s.id
    ORDER BY total_due DESC
    LIMIT 5
");

// Calculate collection rate
$collection_rate = ($total_collected / max(1, ($total_collected + $total_due))) * 100;

// Get recent transactions
$recent_transactions = $conn->query("
    SELECT 
        sf.*,
        s.name as student_name,
        fc.name as fee_category_name
    FROM student_fees sf
    JOIN students s ON sf.student_id = s.id
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    JOIN fee_categories fc ON cf.fee_category_id = fc.id
    JOIN class_students cs ON s.id = cs.student_id
    WHERE cs.class_id = $class_id
    ORDER BY sf.created_at DESC
    LIMIT 10
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Class Finance Dashboard - <?= htmlspecialchars($class['name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            /* Light Theme */
            --light-bg: #f8fafc;
            --light-card: #ffffff;
            --light-text: #1e293b;
            --light-border: #e2e8f0;
            --light-primary: #3b82f6;
            --light-primary-hover: #2563eb;
            --light-secondary: #64748b;
            --light-success: #10b981;
            --light-danger: #ef4444;
            --light-warning: #f59e0b;
            --light-info: #06b6d4;
            --light-hover: #f1f5f9;
            --light-input: #ffffff;
            
            /* Dark Theme */
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-text: #f1f5f9;
            --dark-border: #334155;
            --dark-primary: #60a5fa;
            --dark-primary-hover: #3b82f6;
            --dark-secondary: #94a3b8;
            --dark-success: #34d399;
            --dark-danger: #f87171;
            --dark-warning: #fbbf24;
            --dark-info: #22d3ee;
            --dark-hover: #334155;
            --dark-input: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        body {
            min-height: 100vh;
            background: <?= $isDark ? 'var(--dark-bg)' : 'var(--light-bg)' ?>;
            color: <?= $isDark ? 'var(--dark-text)' : 'var(--light-text)' ?>;
            padding: 1.5rem;
        }

        .container {
            max-width: 1800px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            background: <?= $isDark ? 'linear-gradient(135deg, #1e293b, #0f172a)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' ?>;
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            position: relative;
            overflow: hidden;
        }

        .header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.1;
            transform: rotate(30deg);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            position: relative;
            z-index: 1;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 2.25rem;
            font-weight: 800;
            letter-spacing: -0.5px;
        }

        .header-title i {
            font-size: 2.5rem;
            background: rgba(255,255,255,0.1);
            padding: 1rem;
            border-radius: 16px;
            backdrop-filter: blur(10px);
        }

        .class-selector {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: rgba(255,255,255,0.1);
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .class-selector select {
            background: transparent;
            border: 2px solid rgba(255,255,255,0.2);
            color: white;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            min-width: 200px;
        }

        .class-selector select option {
            color: #1e293b;
        }

        .header-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            position: relative;
            z-index: 1;
        }

        .stat-card {
            background: rgba(255,255,255,0.1);
            padding: 1.5rem;
            border-radius: 16px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.2);
        }

        .stat-content h3 {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-bottom: 0.5rem;
        }

        .stat-content .value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
        }

        .stat-content .trend {
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .trend.up {
            color: #34d399;
        }

        .trend.down {
            color: #f87171;
        }

        /* Filters */
        .filter-section {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 16px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-control {
            padding: 0.875rem 1rem;
            background: <?= $isDark ? 'var(--dark-input)' : 'var(--light-input)' ?>;
            border: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            border-radius: 10px;
            font-size: 1rem;
            color: inherit;
            outline: none;
            width: 100%;
        }

        .form-control:focus {
            border-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            color: white;
        }

        .btn-primary:hover {
            background: <?= $isDark ? 'var(--dark-primary-hover)' : 'var(--light-primary-hover)' ?>;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
        }

        .btn-secondary {
            background: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            color: white;
        }

        /* Dashboard Grid */
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .dashboard-card {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 20px;
            padding: 1.5rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Status Indicators */
        .status-indicator {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
        }

        .status-paid { background: var(--light-success); }
        .status-partial { background: var(--light-warning); }
        .status-unpaid { background: var(--light-danger); }

        /* Table Styles */
        .table-container {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background: <?= $isDark ? '#0f172a' : '#f1f5f9' ?>;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            vertical-align: middle;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        /* Badges */
        .badge {
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
        }

        .badge-warning {
            background: rgba(245, 158, 11, 0.1);
            color: <?= $isDark ? 'var(--dark-warning)' : 'var(--light-warning)' ?>;
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
        }

        .badge-info {
            background: rgba(6, 182, 212, 0.1);
            color: <?= $isDark ? 'var(--dark-info)' : 'var(--light-info)' ?>;
        }

        /* Progress Bar */
        .progress-bar {
            height: 8px;
            background: <?= $isDark ? '#334155' : '#e2e8f0' ?>;
            border-radius: 4px;
            overflow: hidden;
            margin: 0.5rem 0;
        }

        .progress-fill {
            height: 100%;
            border-radius: 4px;
            background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        /* Animation */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-in {
            animation: fadeInUp 0.5s ease forwards;
        }

        .delay-1 { animation-delay: 0.1s; opacity: 0; }
        .delay-2 { animation-delay: 0.2s; opacity: 0; }
        .delay-3 { animation-delay: 0.3s; opacity: 0; }
        .delay-4 { animation-delay: 0.4s; opacity: 0; }

        /* Responsive */
        @media (max-width: 1200px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .header {
                padding: 1.5rem;
            }
            
            .header-top {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .class-selector {
                width: 100%;
            }
            
            .header-stats {
                grid-template-columns: 1fr;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .btn {
                width: 100%;
            }
            
            .card-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
        }

        @media (max-width: 480px) {
            body {
                padding: 1rem;
            }
            
            .header-title h1 {
                font-size: 1.75rem;
            }
            
            .dashboard-card,
            .table-container {
                padding: 1rem;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: <?= $isDark ? '#1e293b' : '#f1f5f9' ?>;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: <?= $isDark ? '#475569' : '#cbd5e1' ?>;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: <?= $isDark ? '#64748b' : '#94a3b8' ?>;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header Section -->
        <div class="header animate-in">
            <div class="header-top">
                <div class="header-title">
                    <i class="fas fa-chart-line"></i>
                    <h1>Class Finance Dashboard</h1>
                </div>
                
                <div class="class-selector">
                    <i class="fas fa-graduation-cap"></i>
                    <form method="GET" style="display: inline;">
                        <select name="class_id" onchange="this.form.submit()" class="form-control">
                            <?php while($c = $classes->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $class_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                        <?php if($start_date): ?><input type="hidden" name="start_date" value="<?= $start_date ?>"><?php endif; ?>
                        <?php if($end_date): ?><input type="hidden" name="end_date" value="<?= $end_date ?>"><?php endif; ?>
                        <?php if($search): ?><input type="hidden" name="search" value="<?= $search ?>"><?php endif; ?>
                    </form>
                </div>
            </div>
            
            <div class="header-stats">
                <div class="stat-card animate-in delay-1">
                    <div class="stat-content">
                        <h3>TOTAL COLLECTED</h3>
                        <div class="value">$<?= number_format($total_collected, 2) ?></div>
                        <div class="trend up">
                            <i class="fas fa-arrow-up"></i>
                            <?= number_format($collection_rate, 1) ?>% collection rate
                        </div>
                    </div>
                </div>
                
                <div class="stat-card animate-in delay-2">
                    <div class="stat-content">
                        <h3>OUTSTANDING BALANCE</h3>
                        <div class="value">$<?= number_format($total_due, 2) ?></div>
                        <div class="trend down">
                            <i class="fas fa-exclamation-circle"></i>
                            From <?= $unpaid_count + $partial_count ?> students
                        </div>
                    </div>
                </div>
                
                <div class="stat-card animate-in delay-3">
                    <div class="stat-content">
                        <h3>TOTAL DISCOUNTS</h3>
                        <div class="value">$<?= number_format($total_discounts, 2) ?></div>
                        <div class="trend">
                            <i class="fas fa-percentage"></i>
                            Applied across all fees
                        </div>
                    </div>
                </div>
                
                <div class="stat-card animate-in delay-4">
                    <div class="stat-content">
                        <h3>STUDENTS</h3>
                        <div class="value"><?= $total_students ?></div>
                        <div class="status-indicator">
                            <span class="status-dot status-paid"></span>
                            <span><?= $paid_count ?> Paid</span>
                            <span class="status-dot status-partial" style="margin-left: 1rem;"></span>
                            <span><?= $partial_count ?> Partial</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section animate-in delay-1">
            <form method="GET" class="filter-grid">
                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                
                <!-- <div class="form-group">
                    <label for="search"><i class="fas fa-search"></i> Search Student</label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="Search by student or guardian name..." value="<?= htmlspecialchars($search) ?>">
                </div> -->
                
                <div class="form-group">
                    <label for="start_date"><i class="fas fa-calendar-alt"></i> From Date</label>
                    <input type="date" name="start_date" id="start_date" class="form-control" value="<?= $start_date ?>">
                </div>
                
                <div class="form-group">
                    <label for="end_date"><i class="fas fa-calendar-alt"></i> To Date</label>
                    <input type="date" name="end_date" id="end_date" class="form-control" value="<?= $end_date ?>">
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Apply Filters
                    </button>
                    <a href="?class_id=<?= $class_id ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            </form>
        </div>

        <!-- Dashboard Charts -->
        <div class="dashboard-grid">
            <!-- Collection Trend Chart -->
            <div class="dashboard-card animate-in delay-1">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-chart-line"></i>
                        Collection Trend (Last 6 Months)
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="collectionTrendChart"></canvas>
                </div>
            </div>

            <!-- Fee Category Breakdown -->
            <div class="dashboard-card animate-in delay-2">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-chart-pie"></i>
                        Fee Category Breakdown
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>

            <!-- Payment Status -->
            <div class="dashboard-card animate-in delay-3">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Payment Status Distribution
                    </div>
                </div>
                <div class="chart-container">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>

            <!-- Top Defaulters -->
            <div class="dashboard-card animate-in delay-4">
                <div class="card-header">
                    <div class="card-title">
                        <i class="fas fa-exclamation-triangle"></i>
                        Top 5 Defaulters
                    </div>
                </div>
                <div class="space-y-3">
                    <?php while($defaulter = $top_defaulters->fetch_assoc()): ?>
                    <div class="defaulter-item">
                        <div class="flex justify-between items-center mb-1">
                            <span class="font-semibold"><?= htmlspecialchars($defaulter['student_name']) ?></span>
                            <span class="text-red-500 font-bold">$<?= number_format($defaulter['total_due'], 2) ?></span>
                        </div>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: <?= min(100, ($defaulter['total_due'] / max(1, $total_due)) * 100) ?>%"></div>
                        </div>
                        <div class="text-sm text-gray-500 mt-1"><?= $defaulter['overdue_invoices'] ?> overdue invoice(s)</div>
                    </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

        <!-- Fee Categories Table -->
        <div class="table-container animate-in delay-2">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-list-alt"></i>
                    Fee Categories & Amounts
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Amount</th>
                        <th>Expected Total</th>
                        <th>Collected</th>
                        <th>Discounts</th>
                        <th>Invoices</th>
                        <th>Collection Rate</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($category = $category_breakdown->fetch_assoc()): 
                        $category_rate = ($category['total_collected'] / max(1, $category['total_expected'])) * 100;
                    ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($category['category_name']) ?></strong></td>
                        <td>$<?= number_format($category['total_expected'] / max(1, $category['total_invoices']), 2) ?></td>
                        <td>$<?= number_format($category['total_expected'], 2) ?></td>
                        <td>$<?= number_format($category['total_collected'], 2) ?></td>
                        <td>
                            <span class="badge badge-info">
                                $<?= number_format($category['total_discounts'], 2) ?>
                            </span>
                        </td>
                        <td><?= $category['total_invoices'] ?></td>
                        <td>
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: <?= $category_rate ?>%"></div>
                            </div>
                            <div class="text-sm text-center mt-1"><?= number_format($category_rate, 1) ?>%</div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <!-- Recent Transactions -->
        <div class="table-container animate-in delay-3" style="margin-top: 2rem;">
            <div class="card-header">
                <div class="card-title">
                    <i class="fas fa-history"></i>
                    Recent Transactions
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Student</th>
                        <th>Category</th>
                        <th>Original</th>
                        <th>Discount</th>
                        <th>Paid</th>
                        <th>Balance</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($transaction = $recent_transactions->fetch_assoc()): 
                        $balance = $transaction['original_amount'] - $transaction['discount_amount'] - $transaction['paid_amount'];
                    ?>
                    <tr>
                        <td><?= date('M d, Y', strtotime($transaction['payment_date'])) ?></td>
                        <td><?= htmlspecialchars($transaction['student_name']) ?></td>
                        <td><?= htmlspecialchars($transaction['fee_category_name']) ?></td>
                        <td>$<?= number_format($transaction['original_amount'], 2) ?></td>
                        <td>
                            <?php if($transaction['discount_amount'] > 0): ?>
                                <span class="badge badge-info">
                                    -$<?= number_format($transaction['discount_amount'], 2) ?>
                                </span>
                            <?php else: ?>
                                $0.00
                            <?php endif; ?>
                        </td>
                        <td>$<?= number_format($transaction['paid_amount'], 2) ?></td>
                        <td>$<?= number_format($balance, 2) ?></td>
                        <td>
                            <?php if($transaction['status'] == 'paid'): ?>
                                <span class="badge badge-success">Paid</span>
                            <?php elseif($transaction['status'] == 'partial'): ?>
                                <span class="badge badge-warning">Partial</span>
                            <?php else: ?>
                                <span class="badge badge-danger">Unpaid</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        // Prepare data for charts
        const monthlyData = <?= json_encode($monthly_trend->fetch_all(MYSQLI_ASSOC)) ?>;
        const categoryData = <?= json_encode($category_breakdown->fetch_all(MYSQLI_ASSOC)) ?>;
        const statusData = {
            paid: <?= $paid_count ?>,
            partial: <?= $partial_count ?>,
            unpaid: <?= $unpaid_count ?>
        };

        // Chart colors based on theme
        const chartColors = {
            primary: '<?= $isDark ? '#60a5fa' : '#3b82f6' ?>',
            success: '<?= $isDark ? '#34d399' : '#10b981' ?>',
            warning: '<?= $isDark ? '#fbbf24' : '#f59e0b' ?>',
            danger: '<?= $isDark ? '#f87171' : '#ef4444' ?>',
            info: '<?= $isDark ? '#22d3ee' : '#06b6d4' ?>',
            grid: '<?= $isDark ? '#334155' : '#e2e8f0' ?>',
            text: '<?= $isDark ? '#94a3b8' : '#64748b' ?>'
        };

        // Collection Trend Chart
        const trendCtx = document.getElementById('collectionTrendChart').getContext('2d');
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: monthlyData.map(m => m.month),
                datasets: [{
                    label: 'Collection Amount',
                    data: monthlyData.map(m => m.total_collected),
                    borderColor: chartColors.primary,
                    backgroundColor: chartColors.primary + '20',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        labels: {
                            color: chartColors.text
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text,
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    },
                    x: {
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text
                        }
                    }
                }
            }
        });

        // Category Breakdown Chart
        const categoryCtx = document.getElementById('categoryChart').getContext('2d');
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryData.map(c => c.category_name),
                datasets: [{
                    data: categoryData.map(c => c.total_collected),
                    backgroundColor: [
                        chartColors.primary,
                        chartColors.success,
                        chartColors.warning,
                        chartColors.danger,
                        chartColors.info,
                        '#8b5cf6',
                        '#ec4899'
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            color: chartColors.text
                        }
                    }
                }
            }
        });

        // Status Distribution Chart
        const statusCtx = document.getElementById('statusChart').getContext('2d');
        new Chart(statusCtx, {
            type: 'bar',
            data: {
                labels: ['Paid', 'Partial', 'Unpaid'],
                datasets: [{
                    label: 'Number of Students',
                    data: [statusData.paid, statusData.partial, statusData.unpaid],
                    backgroundColor: [
                        chartColors.success,
                        chartColors.warning,
                        chartColors.danger
                    ]
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text
                        }
                    },
                    x: {
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text
                        }
                    }
                }
            }
        });

        // Animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.animate-in').forEach(el => {
            observer.observe(el);
        });

        // Print functionality
        function printReport() {
            window.print();
        }

        // Export functionality
        function exportToExcel() {
            alert('Export functionality would be implemented here. This could generate an Excel report.');
        }
    </script>
</body>
</html>