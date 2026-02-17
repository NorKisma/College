<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Paid Students Report | Finance System',
        'page_subtitle' => 'Financial overview of student fee payments',
        'view_unpaid' => 'View Unpaid',
        'back' => 'Back',
        'total_collected' => 'Total Collected',
        'total_fees_collected' => 'Total fees collected',
        'students' => 'Students',
        'matching_filters' => 'Matching filters',
        'fully_paid' => 'Fully Paid',
        'partially_paid' => 'Partially Paid',
        'of_students' => 'of students',
        'filter_options' => 'Filter Options',
        'export_report' => 'Export Report',
        'student_name' => 'Student Name',
        'search_by_name' => 'Search by name...',
        'class' => 'Class',
        'all_classes' => 'All Classes',
        'month' => 'Month',
        'all_months' => 'All Months',
        'year' => 'Year',
        'from_date' => 'From Date',
        'to_date' => 'To Date',
        'apply_filters' => 'Apply Filters',
        'clear_all' => 'Clear All',
        'student_payment_records' => 'Student Payment Records',
        'per_page' => 'per page',
        'enrollment_date' => 'Enrollment Date',
        'status' => 'Status',
        'discount' => 'Discount',
        'amount_paid' => 'Amount Paid',
        'total_owed' => 'Total Owed',
        'balance' => 'Balance',
        'last_payment' => 'Last Payment',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'no_payments' => 'No payments',
        'no_payment_records' => 'No payment records found',
        'try_adjusting_search' => 'Try adjusting your search criteria or filters',
        'first_page' => 'First',
        'previous_page' => 'Previous',
        'next_page' => 'Next',
        'last_page' => 'Last',
        'page_of' => 'Page',
        'of' => 'of',
        'january' => 'January',
        'february' => 'February',
        'march' => 'March',
        'april' => 'April',
        'may' => 'May',
        'june' => 'June',
        'july' => 'July',
        'august' => 'August',
        'september' => 'September',
        'october' => 'October',
        'november' => 'November',
        'december' => 'December'
    ],
    2 => [ // Arabic
        'page_title' => 'تقرير الطلاب المدفوعين | نظام المالية',
        'page_subtitle' => 'نظرة عامة مالية لمدفوعات رسوم الطلاب',
        'view_unpaid' => 'عرض غير المدفوعين',
        'back' => 'رجوع',
        'total_collected' => 'إجمالي المحصل',
        'total_fees_collected' => 'إجمالي الرسوم المحصلة',
        'students' => 'الطلاب',
        'matching_filters' => 'يطابق الفلاتر',
        'fully_paid' => 'مدفوع بالكامل',
        'partially_paid' => 'مدفوع جزئياً',
        'of_students' => 'من الطلاب',
        'filter_options' => 'خيارات التصفية',
        'export_report' => 'تصدير التقرير',
        'student_name' => 'اسم الطالب',
        'search_by_name' => 'بحث بالاسم...',
        'class' => 'الصف',
        'all_classes' => 'جميع الصفوف',
        'month' => 'الشهر',
        'all_months' => 'جميع الأشهر',
        'year' => 'السنة',
        'from_date' => 'من تاريخ',
        'to_date' => 'إلى تاريخ',
        'apply_filters' => 'تطبيق الفلاتر',
        'clear_all' => 'مسح الكل',
        'student_payment_records' => 'سجلات مدفوعات الطلاب',
        'per_page' => 'لكل صفحة',
        'enrollment_date' => 'تاريخ التسجيل',
        'status' => 'الحالة',
        'discount' => 'الخصم',
        'amount_paid' => 'المبلغ المدفوع',
        'total_owed' => 'إجمالي المستحق',
        'balance' => 'الرصيد',
        'last_payment' => 'آخر دفعة',
        'paid' => 'مدفوع',
        'partial' => 'جزئي',
        'no_payments' => 'لا توجد مدفوعات',
        'no_payment_records' => 'لم يتم العثور على سجلات مدفوعات',
        'try_adjusting_search' => 'حاول تعديل معايير البحث أو الفلاتر',
        'first_page' => 'الأول',
        'previous_page' => 'السابق',
        'next_page' => 'التالي',
        'last_page' => 'الأخير',
        'page_of' => 'صفحة',
        'of' => 'من',
        'january' => 'يناير',
        'february' => 'فبراير',
        'march' => 'مارس',
        'april' => 'أبريل',
        'may' => 'مايو',
        'june' => 'يونيو',
        'july' => 'يوليو',
        'august' => 'أغسطس',
        'september' => 'سبتمبر',
        'october' => 'أكتوبر',
        'november' => 'نوفمبر',
        'december' => 'ديسمبر'
    ]
];

function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

function getMonthName($monthNumber) {
    $monthNames = [
        1 => t('january'),
        2 => t('february'),
        3 => t('march'),
        4 => t('april'),
        5 => t('may'),
        6 => t('june'),
        7 => t('july'),
        8 => t('august'),
        9 => t('september'),
        10 => t('october'),
        11 => t('november'),
        12 => t('december')
    ];
    
    return $monthNames[$monthNumber] ?? date('F', mktime(0, 0, 0, $monthNumber, 1));
}

// =========================
// THEME READER
// =========================
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1");
$themeMode = ($themeRes && $themeRes->num_rows > 0)
    ? (int)$themeRes->fetch_assoc()['mode']
    : 1; // default light

    $startDate = isset($_GET['start_date']) && $_GET['start_date'] !== '' ? $_GET['start_date'] : '';
$endDate   = isset($_GET['end_date']) && $_GET['end_date'] !== '' ? $_GET['end_date'] : '';


$bgColor   = ($themeMode === 0) ? '#0f172a' : '#f8fafc';
$cardBg    = ($themeMode === 0) ? '#1e293b' : '#ffffff';
$textColor = ($themeMode === 0) ? '#f1f5f9' : '#1e293b';
$tableHead = ($themeMode === 0) ? '#334155' : '#e2e8f0';
$borderClr = ($themeMode === 0) ? '#334155' : '#cbd5e1';

// Professional color palette
$primaryColor = ($themeMode === 0) ? '#3b82f6' : '#2563eb';
$primaryLight = ($themeMode === 0) ? '#60a5fa' : '#3b82f6';
$dangerColor = ($themeMode === 0) ? '#ef4444' : '#dc2626';
$warningColor = ($themeMode === 0) ? '#f59e0b' : '#d97706';
$successColor = ($themeMode === 0) ? '#10b981' : '#059669';
$infoColor = ($themeMode === 0) ? '#0ea5e9' : '#0284c7';
$grayColor = ($themeMode === 0) ? '#64748b' : '#475569';
$lightGray = ($themeMode === 0) ? '#334155' : '#f1f5f9';

/* =========================
   PAGINATION (STUDENTS)
   ========================= */
$allowedLimits = [20, 25, 30, 50, 70, 100];

$perPage = isset($_GET['limit']) && in_array((int)$_GET['limit'], $allowedLimits)
    ? (int)$_GET['limit']
    : 25;

$page = isset($_GET['page']) && (int)$_GET['page'] > 0
    ? (int)$_GET['page']
    : 1;

$offset = ($page - 1) * $perPage;

/* Total students count */
$totalStudentsRes = $conn->query("SELECT COUNT(*) total FROM class_students");
$totalStudents = (int)$totalStudentsRes->fetch_assoc()['total'];

$totalPages = (int)ceil($totalStudents / $perPage);

/* =========================
   FILTER INPUTS
   ========================= */
$filterClass = isset($_GET['class']) ? (int)$_GET['class'] : '';
$filterMonth = isset($_GET['month']) ? (int)$_GET['month'] : '';
$filterYear  = isset($_GET['year'])  ? (int)$_GET['year']  : '';
$searchName  = trim($_GET['search'] ?? '');

/* =========================
   ACADEMIC CALENDAR (PAYABLE MONTHS)
   ========================= */
$calendarRes = $conn->query("
    SELECT year, month, is_payable
    FROM academic_calendar
");

$payableMonths = [];
while ($cal = $calendarRes->fetch_assoc()) {
    $payableMonths[$cal['year']][$cal['month']] = (int)$cal['is_payable'];
}

/* =========================
   DEFAULT FEES PER CLASS
   ========================= */
$defaultClassFees = [
    5 => 10,
    'default' => 15
];

/* =========================
   STUDENTS + CLASSES - ONE ROW PER STUDENT
   ========================= */
$where = [];

if ($filterClass) {
    $where[] = "c.id = $filterClass";
}

if ($searchName !== '') {
    $safeSearch = $conn->real_escape_string($searchName);
    $where[] = "s.name LIKE '%$safeSearch%'";
}

// Add date range filter
if ($startDate && $endDate) {
    $startSafe = $conn->real_escape_string($startDate);
    $endSafe   = $conn->real_escape_string($endDate);
    $where[] = "(cs.created_at BETWEEN '$startSafe' AND '$endSafe')";
} elseif ($startDate) {
    $startSafe = $conn->real_escape_string($startDate);
    $where[] = "cs.created_at >= '$startSafe'";
} elseif ($endDate) {
    $endSafe = $conn->real_escape_string($endDate);
    $where[] = "cs.created_at <= '$endSafe'";
}

// Build the final WHERE SQL **after all filters are added**
$whereSql = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Modified query to get distinct students
$students = $conn->query("
    SELECT DISTINCT s.id student_id, s.name student_name,
           c.id class_id, c.name class_name,
           cs.created_at enrolled_at
    FROM students s
    JOIN class_students cs ON cs.student_id = s.id
    JOIN classes c ON c.id = cs.class_id
    $whereSql
    ORDER BY s.name ASC
    LIMIT $perPage OFFSET $offset
");

// Fetch all student payments - grouped by student
$studentFeesRes = $conn->query("
    SELECT student_id, SUM(paid_amount) as total_paid, MAX(created_at) as last_payment_date
    FROM student_fees 
    GROUP BY student_id
    ORDER BY last_payment_date DESC
");

$studentTotalPayments = [];
$lastPaymentDates = [];
while ($sf = $studentFeesRes->fetch_assoc()) {
    $studentTotalPayments[$sf['student_id']] = (float)$sf['total_paid'];
    $lastPaymentDates[$sf['student_id']] = $sf['last_payment_date'];
}

// Fetch student discounts
$discountsRes = $conn->query("
    SELECT student_id, discount_percentage 
    FROM student_discounts 
    WHERE created_at IN (
        SELECT MAX(created_at) 
        FROM student_discounts 
        GROUP BY student_id
    )
");

$studentDiscounts = [];
while ($d = $discountsRes->fetch_assoc()) {
    $studentDiscounts[$d['student_id']] = (float)$d['discount_percentage'];
}

// Store students data
$studentsData = [];
if ($students && $students->num_rows > 0) {
    while ($s = $students->fetch_assoc()) {
        $studentsData[] = $s;
    }
}

// Calculate statistics - ONE CALCULATION PER STUDENT
$totalPaidMoney = 0;
$totalFullyPaid = 0;
$totalPartiallyPaid = 0;
$studentCalculations = [];

if (!empty($studentsData)) {
    foreach ($studentsData as $s) {
        $studentId = $s['student_id'];
        $totalPaid = $studentTotalPayments[$studentId] ?? 0;
        
        if ($totalPaid > 0) {
            // Calculate total owed from enrollment to today
            $start = new DateTime(date('Y-m-01', strtotime($s['enrolled_at'])));
            $today = new DateTime(date('Y-m-01'));
            $cursor = clone $start;
            
            $totalOwed = 0;
            $anyFullyPaidMonth = false;
            
            while ($cursor <= $today) {
                $month = (int)$cursor->format('m');
                $year = (int)$cursor->format('Y');
                
                // Skip unpayable months
                if (isset($payableMonths[$year][$month]) && $payableMonths[$year][$month] === 0) {
                    $cursor->modify('+1 month');
                    continue;
                }
                
                // Apply month filter if set
                if ($filterMonth && $month !== $filterMonth) {
                    $cursor->modify('+1 month');
                    continue;
                }
                
                // Apply year filter if set
                if ($filterYear && $year !== $filterYear) {
                    $cursor->modify('+1 month');
                    continue;
                }
                
                // Get fee for this month
                $fee = $conn->query("
                    SELECT id, amount FROM class_fees
                    WHERE class_id = {$s['class_id']}
                    AND month = $month AND year = $year
                    LIMIT 1
                ")->fetch_assoc();
                
                $feeAmount = $fee['amount'] ?? ($defaultClassFees[$s['class_id']] ?? $defaultClassFees['default']);
                $discountPercentage = $studentDiscounts[$studentId] ?? 0;
                $finalAmount = $feeAmount - ($feeAmount * $discountPercentage / 100);
                
                $totalOwed += $finalAmount;
                
                // Check if this specific month is fully paid
                if ($totalPaid >= $finalAmount && $finalAmount > 0) {
                    $anyFullyPaidMonth = true;
                }
                
                $cursor->modify('+1 month');
            }
            
            // Store student calculations
            $studentCalculations[$studentId] = [
                'total_paid' => $totalPaid,
                'total_owed' => $totalOwed,
                'discount' => $studentDiscounts[$studentId] ?? 0,
                'last_payment' => $lastPaymentDates[$studentId] ?? null,
                'status' => $totalPaid >= $totalOwed ? 'paid' : 'partial'
            ];
            
            // Update totals
            $totalPaidMoney += min($totalPaid, $totalOwed);
            
            if ($studentCalculations[$studentId]['status'] === 'paid') {
                $totalFullyPaid++;
            } else {
                $totalPartiallyPaid++;
            }
        }
    }
}

// Filtered student count
$filteredTotalQuery = "
    SELECT COUNT(DISTINCT s.id) as filtered_total 
    FROM students s
    JOIN class_students cs ON cs.student_id = s.id
    JOIN classes c ON c.id = cs.class_id
    $whereSql
";

$filteredTotalRes = $conn->query($filteredTotalQuery);
$filteredTotalStudents = $filteredTotalRes ? (int)$filteredTotalRes->fetch_assoc()['filtered_total'] : 0;

// Calculate payment completion rate
$paymentRate = $filteredTotalStudents > 0 ? ($totalFullyPaid / $filteredTotalStudents * 100) : 0;
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
   
</head>
<body>
    <!-- Header -->
    <header class="app-header">
        <div class="container">
            <div class="header-content">
                <div class="page-title">
                    <h1><i class="fas fa-file-invoice-dollar"></i> <?= t('page_title') ?></h1>
                    <p><?= t('page_subtitle') ?></p>
                </div>
                <div class="header-actions">
                    <a href="not_paid.php" class="btn btn-secondary">
                        <i class="fas fa-exchange-alt"></i> <?= t('view_unpaid') ?>
                    </a>
                    <a href="fees.php" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> <?= t('back') ?>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        <!-- Statistics -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title"><?= t('total_collected') ?></div>
                    <div class="stat-icon green">
                        <i class="fas fa-hand-holding-usd"></i>
                    </div>
                </div>
                <div class="stat-value">$<?= number_format($totalPaidMoney, 2) ?></div>
                <div class="stat-subtext">
                    <i class="fas fa-chart-line"></i> <?= t('total_fees_collected') ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title"><?= t('students') ?></div>
                    <div class="stat-icon blue">
                        <i class="fas fa-users"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $filteredTotalStudents ?></div>
                <div class="stat-subtext">
                    <i class="fas fa-filter"></i> <?= t('matching_filters') ?>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title"><?= t('fully_paid') ?></div>
                    <div class="stat-icon green">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $totalFullyPaid ?></div>
                <div class="stat-subtext">
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: <?= min(100, $paymentRate) ?>%"></div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-header">
                    <div class="stat-title"><?= t('partially_paid') ?></div>
                    <div class="stat-icon orange">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
                <div class="stat-value"><?= $totalPartiallyPaid ?></div>
                <div class="stat-subtext">
                    <?= $filteredTotalStudents > 0 ? number_format(($totalPartiallyPaid / $filteredTotalStudents * 100), 1) : '0' ?>% <?= t('of_students') ?>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <section class="filters-panel">
            <div class="filters-header">
                <h2><i class="fas fa-filter"></i> <?= t('filter_options') ?></h2>
              <a href="paid_fees_report.php?<?= http_build_query($_GET) ?>" 
   class="btn btn-success"
   target="_blank">
    <i class="fas fa-file-export"></i> <?= t('export_report') ?>
</a>


            </div>

            <form method="GET" class="filters-form">
                <div class="filters-grid">
                    <div class="filter-group">
                        <label class="filter-label"><?= t('student_name') ?></label>
                        <input type="text" name="search" placeholder="<?= t('search_by_name') ?>" 
                               value="<?= htmlspecialchars($searchName) ?>" class="filter-input">
                    </div>

                    <div class="filter-group">
                        <label class="filter-label"><?= t('class') ?></label>
                        <select name="class" class="filter-input">
                            <option value=""><?= t('all_classes') ?></option>
                            <?php
                            $cls = $conn->query("SELECT id, name FROM classes ORDER BY name");
                            while ($c = $cls->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>" <?= $filterClass==$c['id']?'selected':'' ?>>
                                    <?= $c['name'] ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label"><?= t('month') ?></label>
                        <select name="month" class="filter-input">
                            <option value=""><?= t('all_months') ?></option>
                            <?php for ($m=1; $m<=12; $m++): ?>
                    <option value="<?= $m ?>" <?= $filterMonth==$m?'selected':'' ?>>
                        <?= getMonthName($m) ?>
                    </option>
                <?php endfor; ?>

                        </select>
                    </div>

                    <div class="filter-group">
                        <label class="filter-label"><?= t('year') ?></label>
                        <input type="number" name="year" placeholder="<?= t('year') ?>" 
                               value="<?= $filterYear ?: '' ?>" class="filter-input" min="2000" max="2030">
                    </div>
                    <div class="filter-group">
                            <label class="filter-label"><?= t('from_date') ?></label>
                            <input type="date" name="start_date" 
                                value="<?= htmlspecialchars($_GET['start_date'] ?? '') ?>" 
                                class="filter-input">
                        </div>

                        <div class="filter-group">
                            <label class="filter-label"><?= t('to_date') ?></label>
                            <input type="date" name="end_date" 
                                value="<?= htmlspecialchars($_GET['end_date'] ?? '') ?>" 
                                class="filter-input">
                        </div>

                </div>

                <div class="filters-actions">
                    <input type="hidden" name="limit" value="<?= $perPage ?>">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> <?= t('apply_filters') ?>
                    </button>
                    <a href="paid_fees.php" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> <?= t('clear_all') ?>
                    </a>
                </div>
            </form>
        </section>

        <!-- Data Table -->
        <section class="data-table-container">
            <div class="table-header">
                <h2><i class="fas fa-table"></i> <?= t('student_payment_records') ?></h2>
                <div class="table-controls">
                    <form method="GET" class="rows-form">
                        <select name="limit" onchange="this.form.submit()">
                            <?php foreach ([25, 50, 100] as $l): ?>
                                <option value="<?= $l ?>" <?= $l==$perPage?'selected':'' ?>>
                                    <?= $l ?> <?= t('per_page') ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="page" value="1">
                    </form>
                </div>
            </div>

            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th><?= t('student_name') ?></th>
                            <th><?= t('class') ?></th>
                            <th><?= t('enrollment_date') ?></th>
                            <th><?= t('status') ?></th>
                            <th><?= t('discount') ?></th>
                            <th><?= t('amount_paid') ?></th>
                            <th><?= t('total_owed') ?></th>
                            <th><?= t('balance') ?></th>
                            <th><?= t('last_payment') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($studentsData)): ?>
                            <?php foreach ($studentsData as $s): ?>
                                <?php 
                                $studentId = $s['student_id'];
                                if (isset($studentCalculations[$studentId])): 
                                    $calc = $studentCalculations[$studentId];
                                    $balance = max(0, $calc['total_owed'] - $calc['total_paid']);
                                ?>
                                    <tr>
                                        <td><strong><?= htmlspecialchars($s['student_name']) ?></strong></td>
                                        <td><?= htmlspecialchars($s['class_name']) ?></td>
                                        <td><?= date('M d, Y', strtotime($s['enrolled_at'])) ?></td>
                                        <td>
                                            <span class="status-badge status-<?= $calc['status'] ?>">
                                                <i class="fas fa-<?= $calc['status'] === 'paid' ? 'check' : 'clock' ?>"></i>
                                                <?= $calc['status'] === 'paid' ? t('paid') : t('partial') ?>
                                            </span>
                                        </td>
                                        <td><?= number_format($calc['discount'], 1) ?>%</td>
                                        <td class="amount-cell amount-paid">$<?= number_format($calc['total_paid'], 2) ?></td>
                                        <td class="amount-cell amount-total">$<?= number_format($calc['total_owed'], 2) ?></td>
                                        <td class="amount-cell" style="color: <?= $balance > 0 ? 'var(--warning)' : 'var(--success)' ?>">
                                            $<?= number_format($balance, 2) ?>
                                        </td>
                                        <td class="payment-date">
                                            <?= $calc['last_payment'] ? date('M d, Y', strtotime($calc['last_payment'])) : t('no_payments') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9">
                                    <div class="empty-state">
                                        <i class="fas fa-search"></i>
                                        <h3><?= t('no_payment_records') ?></h3>
                                        <p><?= t('try_adjusting_search') ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($totalPages > 1): ?>
            <div class="pagination">
                <button class="page-btn" <?= $page <= 1 ? 'disabled' : '' ?>
                        onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['page'=>1])) ?>'">
                    <i class="fas fa-angle-double-left"></i> <?= t('first_page') ?>
                </button>
                
                <button class="page-btn" <?= $page <= 1 ? 'disabled' : '' ?>
                        onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['page'=>$page-1])) ?>'">
                    <i class="fas fa-chevron-left"></i> <?= t('previous_page') ?>
                </button>
                
                <span class="page-current"><?= t('page_of') ?> <?= $page ?> <?= t('of') ?> <?= $totalPages ?></span>
                
                <button class="page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?>
                        onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['page'=>$page+1])) ?>'">
                    <?= t('next_page') ?> <i class="fas fa-chevron-right"></i>
                </button>
                
                <button class="page-btn" <?= $page >= $totalPages ? 'disabled' : '' ?>
                        onclick="window.location.href='?<?= http_build_query(array_merge($_GET, ['page'=>$totalPages])) ?>'">
                    <?= t('last_page') ?> <i class="fas fa-angle-double-right"></i>
                </button>
            </div>
            <?php endif; ?>
        </section>
    </main>

    <script>
        // Professional interactions
        document.addEventListener('DOMContentLoaded', function() {
            // Table row interaction
            const tableRows = document.querySelectorAll('.data-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('click', function(e) {
                    // Toggle selection
                    tableRows.forEach(r => r.classList.remove('selected'));
                    this.classList.add('selected');
                });
            });

            // Export button loading
            const exportBtn = document.querySelector('a[href*="export=paid"]');
            if (exportBtn) {
                exportBtn.addEventListener('click', function(e) {
                    this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= addslashes(t('export_report')) ?>...';
                    this.style.pointerEvents = 'none';
                    
                    setTimeout(() => {
                        this.innerHTML = '<i class="fas fa-file-export"></i> <?= addslashes(t('export_report')) ?>';
                        this.style.pointerEvents = 'auto';
                    }, 2000);
                });
            }

            // Form validation
            const yearInput = document.querySelector('input[name="year"]');
            if (yearInput) {
                yearInput.addEventListener('change', function() {
                    const year = parseInt(this.value);
                    if (this.value && (year < 2000 || year > 2030)) {
                        alert('Please enter a valid year between 2000 and 2030');
                        this.value = '';
                        this.focus();
                    }
                });
            }
        });
    </script>
</body>
</html>


 <style>
        /* Professional Corporate Design System */
        :root {
            --primary: <?= $primaryColor ?>;
            --primary-hover: <?= $themeMode === 0 ? '#2563eb' : '#1d4ed8' ?>;
            --success: <?= $successColor ?>;
            --warning: <?= $warningColor ?>;
            --danger: <?= $dangerColor ?>;
            --info: <?= $infoColor ?>;
            --gray-50: <?= $themeMode === 0 ? '#0f172a' : '#f8fafc' ?>;
            --gray-100: <?= $themeMode === 0 ? '#1e293b' : '#f1f5f9' ?>;
            --gray-200: <?= $themeMode === 0 ? '#334155' : '#e2e8f0' ?>;
            --gray-300: <?= $themeMode === 0 ? '#475569' : '#cbd5e1' ?>;
            --gray-400: <?= $themeMode === 0 ? '#64748b' : '#94a3b8' ?>;
            --gray-500: <?= $grayColor ?>;
            --gray-600: <?= $themeMode === 0 ? '#475569' : '#475569' ?>;
            --gray-700: <?= $themeMode === 0 ? '#334155' : '#334155' ?>;
            --gray-800: <?= $themeMode === 0 ? '#1e293b' : '#1e293b' ?>;
            --gray-900: <?= $themeMode === 0 ? '#0f172a' : '#0f172a' ?>;
            --bg-primary: var(--gray-50);
            --bg-card: var(--gray-100);
            --text-primary: <?= $textColor ?>;
            --text-secondary: var(--gray-600);
            --border-color: var(--gray-300);
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --font-sans: -apple-system, BlinkMacSystemFont, 'Segoe UI', 'Roboto', 'Oxygen', 'Ubuntu', 'Cantarell', 'Fira Sans', 'Droid Sans', 'Helvetica Neue', sans-serif;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: var(--font-sans);
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
            line-height: 1.5;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Header */
        .app-header {
            background: linear-gradient(135deg, var(--primary), var(--primary-hover));
            color: white;
            padding: 24px 0;
            margin-bottom: 32px;
            box-shadow: var(--shadow-md);
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .page-title h1 {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .page-title p {
            opacity: 0.9;
            font-size: 14px;
        }

        .header-actions {
            display: flex;
            gap: 12px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 32px;
        }

        .stat-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 24px;
            transition: all 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary);
        }

        .stat-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 16px;
        }

        .stat-title {
            font-size: 14px;
            font-weight: 500;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-icon {
            width: 48px;
            height: 48px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 20px;
        }

        .stat-icon.blue { background: rgba(59, 130, 246, 0.1); color: var(--primary); }
        .stat-icon.green { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .stat-icon.orange { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .stat-icon.purple { background: rgba(139, 92, 246, 0.1); color: #8b5cf6; }

        .stat-value {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-primary);
            margin: 8px 0;
        }

        .stat-subtext {
            font-size: 13px;
            color: var(--gray-500);
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .progress-bar {
            height: 4px;
            background: var(--gray-200);
            border-radius: 2px;
            margin-top: 8px;
            overflow: hidden;
        }

        .progress-fill {
            height: 100%;
            background: var(--success);
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        /* Filters Panel */
        .filters-panel {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            padding: 24px;
            margin-bottom: 24px;
        }

        .filters-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .filters-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .filters-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 16px;
            margin-bottom: 20px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 500;
            color: var(--text-secondary);
        }

        .filter-input {
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: 6px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
            transition: all 0.2s ease;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .filters-actions {
            display: flex;
            gap: 12px;
            padding-top: 20px;
            border-top: 1px solid var(--border-color);
        }

        /* Buttons */
        .btn {
            padding: 10px 20px;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            border: none;
            cursor: pointer;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn:focus {
            outline: none;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--primary-hover);
        }

        .btn-secondary {
            background: transparent;
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background: var(--gray-100);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            background: #059669;
        }

        /* Data Table */
        .data-table-container {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 32px;
        }

        .table-header {
            padding: 20px 24px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .table-header h2 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-primary);
        }

        .table-controls {
            display: flex;
            gap: 16px;
            align-items: center;
        }

        .table-controls select {
            padding: 8px 12px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--bg-primary);
            color: var(--text-primary);
            font-size: 14px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .data-table thead {
            background: var(--gray-100);
        }

        .data-table th {
            padding: 16px 20px;
            text-align: left;
            font-size: 12px;
            font-weight: 600;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid var(--border-color);
        }

        .data-table td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
            font-size: 14px;
        }

        .data-table tbody tr {
            transition: background-color 0.2s ease;
        }

        .data-table tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        .data-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-paid {
            background: rgba(16, 185, 129, 0.1);
            color: var(--success);
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .status-partial {
            background: rgba(245, 158, 11, 0.1);
            color: var(--warning);
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        /* Amount Cells */
        .amount-cell {
            font-family: 'SF Mono', 'Roboto Mono', monospace;
            font-weight: 600;
        }

        .amount-paid {
            color: var(--success);
        }

        .amount-total {
            color: var(--text-primary);
        }

        /* Payment Date */
        .payment-date {
            font-size: 13px;
            color: var(--gray-500);
        }

        /* Empty State */
        .empty-state {
            padding: 64px 24px;
            text-align: center;
        }

        .empty-state i {
            font-size: 48px;
            color: var(--gray-400);
            margin-bottom: 16px;
        }

        .empty-state h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 8px;
        }

        .empty-state p {
            color: var(--gray-500);
            font-size: 14px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 8px;
            padding: 24px;
        }

        .page-btn {
            padding: 8px 16px;
            border: 1px solid var(--border-color);
            border-radius: 4px;
            background: var(--bg-card);
            color: var(--text-primary);
            font-size: 14px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 6px;
        }

        .page-btn:hover:not(:disabled) {
            background: var(--gray-100);
        }

        .page-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .page-current {
            padding: 8px 16px;
            background: var(--primary);
            color: white;
            border-radius: 4px;
            font-size: 14px;
            font-weight: 500;
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filters-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .header-content {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .stats-container {
                grid-template-columns: 1fr;
            }
            
            .filters-grid {
                grid-template-columns: 1fr;
            }
            
            .filters-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 16px;
            }
            
            .table-controls {
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 480px) {
            .header-actions {
                flex-direction: column;
                width: 100%;
            }
            
            .btn {
                width: 100%;
                justify-content: center;
            }
            
            .filters-actions {
                flex-direction: column;
            }
            
            .pagination {
                flex-wrap: wrap;
            }
        }
    </style>