<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Student Finance Dashboard',
        'header_title' => 'Student Finance Dashboard',
        'back_button' => 'Back',
        'print_button' => 'Print Statement',
        
        // Student Info
        'student_class' => 'Class',
        'student_section' => 'Section',
        'academic_year' => 'Academic Year',
        'gender' => 'Gender',
        'guardian_phone' => 'Guardian Phone',
        'alert_info' => 'If there are months not set for any record, the system may decline to read them...',
        
        // Summary Cards
        'summary_total_original' => 'Total Original',
        'summary_tooltip_original' => 'Full amount without discounts',
        'summary_total_discount' => 'Total Discount',
        'summary_tooltip_discount' => '{percentage}% of total',
        'summary_total_paid' => 'Total Paid',
        'summary_tooltip_paid' => '{percentage}% collected',
        'summary_total_pending' => 'Total Pending',
        'summary_tooltip_pending_attention' => 'Needs attention',
        'summary_tooltip_pending_clear' => 'All clear',
        
        // Chart Section
        'chart_title' => 'Fee Collection Overview',
        'chart_bar_option' => 'Bar Chart',
        'chart_line_option' => 'Line Chart',
        'chart_period_all' => 'All Time',
        'chart_period_year' => 'This Year',
        'chart_period_month' => 'Last 6 Months',
        
        // Table Section
        'table_title' => 'Fee Details',
        'table_showing_records' => 'Showing {count} fee records',
        'table_header_month' => 'Month',
        'table_header_year' => 'Year',
        'table_header_category' => 'Fee Category',
        'table_header_original' => 'Original',
        'table_header_discount' => 'Discount',
        'table_header_paid' => 'Paid',
        'table_header_pending' => 'Pending',
        'table_header_status' => 'Status',
        'table_header_reason' => 'Discount Reason',
        
        // Status Labels
        'status_paid' => 'Paid',
        'status_partial' => 'Partial',
        'status_unpaid' => 'Unpaid',
        
        // Footer
        'footer_copyright' => 'Student Finance Dashboard',
        'footer_generated' => 'Generated on',
        
        // Error Messages
        'no_student' => 'No student selected.',
        'student_not_found' => 'Student not found.',
        
        // Month Names (for table)
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mar',
        'month_apr' => 'Apr',
        'month_may' => 'May',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Aug',
        'month_sep' => 'Sep',
        'month_oct' => 'Oct',
        'month_nov' => 'Nov',
        'month_dec' => 'Dec',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'لوحة تحكم مالية الطالب',
        'header_title' => 'لوحة تحكم مالية الطالب',
        'back_button' => 'رجوع',
        'print_button' => 'طباعة البيان',
        
        // Student Info
        'student_class' => 'الصف',
        'student_section' => 'القسم',
        'academic_year' => 'السنة الأكاديمية',
        'gender' => 'الجنس',
        'guardian_phone' => 'هاتف ولي الأمر',
        'alert_info' => 'إذا كانت هناك أشهر غير مضبوطة لأي سجل، قد يرفض النظام قراءتها...',
        
        // Summary Cards
        'summary_total_original' => 'إجمالي المبلغ الأصلي',
        'summary_tooltip_original' => 'المبلغ الكامل بدون خصومات',
        'summary_total_discount' => 'إجمالي الخصم',
        'summary_tooltip_discount' => '{percentage}% من الإجمالي',
        'summary_total_paid' => 'إجمالي المدفوع',
        'summary_tooltip_paid' => '{percentage}% تم تحصيله',
        'summary_total_pending' => 'إجمالي المعلق',
        'summary_tooltip_pending_attention' => 'تحتاج إلى اهتمام',
        'summary_tooltip_pending_clear' => 'كل شيء واضح',
        
        // Chart Section
        'chart_title' => 'نظرة عامة على تحصيل الرسوم',
        'chart_bar_option' => 'مخطط شريطي',
        'chart_line_option' => 'مخطط خطي',
        'chart_period_all' => 'كل الوقت',
        'chart_period_year' => 'هذه السنة',
        'chart_period_month' => 'آخر 6 أشهر',
        
        // Table Section
        'table_title' => 'تفاصيل الرسوم',
        'table_showing_records' => 'عرض {count} سجل رسوم',
        'table_header_month' => 'الشهر',
        'table_header_year' => 'السنة',
        'table_header_category' => 'فئة الرسوم',
        'table_header_original' => 'الأصلي',
        'table_header_discount' => 'الخصم',
        'table_header_paid' => 'المدفوع',
        'table_header_pending' => 'المعلق',
        'table_header_status' => 'الحالة',
        'table_header_reason' => 'سبب الخصم',
        
        // Status Labels
        'status_paid' => 'مدفوع',
        'status_partial' => 'جزئي',
        'status_unpaid' => 'غير مدفوع',
        
        // Footer
        'footer_copyright' => 'لوحة تحكم مالية الطالب',
        'footer_generated' => 'تم إنشاؤه في',
        
        // Error Messages
        'no_student' => 'لم يتم اختيار طالب.',
        'student_not_found' => 'الطالب غير موجود.',
        
        // Month Names (for table)
        'month_jan' => 'يناير',
        'month_feb' => 'فبراير',
        'month_mar' => 'مارس',
        'month_apr' => 'أبريل',
        'month_may' => 'مايو',
        'month_jun' => 'يونيو',
        'month_jul' => 'يوليو',
        'month_aug' => 'أغسطس',
        'month_sep' => 'سبتمبر',
        'month_oct' => 'أكتوبر',
        'month_nov' => 'نوفمبر',
        'month_dec' => 'ديسمبر',
    ]
];

// Translation helper function with string replacement
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

// Theme detection
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
if ($theme_res && $theme_row = $theme_res->fetch_assoc()) {
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

$student_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($student_id <= 0) {
    die(t('no_student'));
}

$current_year = date('Y');

/* ================= STUDENT INFO ================= */
$stmt = $conn->prepare("
    SELECT 
        s.id,
        s.name,
        s.gender,
        s.guardian_phone,
        cs.academic_year,
        cs.class_id AS class_id,
        cs.created_at AS enrolled_at,
        c.name AS class_name,
        sec.name AS section_name
    FROM students s
    LEFT JOIN class_students cs
        ON cs.student_id = s.id
       AND cs.academic_year = (
            SELECT MAX(academic_year)
            FROM class_students
            WHERE student_id = s.id
       )
    LEFT JOIN classes c
        ON cs.class_id = c.id
    LEFT JOIN sections sec
        ON c.section_id = sec.id
    WHERE s.id = ?
    LIMIT 1
");

$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die(t('student_not_found'));
}

/* ================= HELPERS ================= */
function safe_pending($original, $discount, $paid) {
    $pending = $original - $discount - $paid;
    return $pending > 0 ? $pending : 0;
}

/* ================= ALL FEES (ALL MONTHS) ================= */
// We'll build a month-by-month view from enrollment to today and
// use find_previous_class_fee() when an exact month/year class fee is missing.
$class_id = (int)($student['class_id'] ?? 0);
$enrolled_at = $student['enrolled_at'] ?? null;

$fees = [];
$total_original = 0;
$total_discount = 0;
$total_paid     = 0;
$total_pending  = 0;

// Load all class_fees for this class into an associative map
$classFees = [];
if ($class_id) {
    $cfRes = $conn->prepare("SELECT * FROM class_fees WHERE class_id = ? ORDER BY year, month");
    $cfRes->bind_param('i', $class_id);
    $cfRes->execute();
    $cfResRes = $cfRes->get_result();
    while ($r = $cfResRes->fetch_assoc()) {
        $classFees[$r['class_id']][(int)$r['year']][(int)$r['month']] = $r;
    }
    $cfRes->close();
}

// Load student_fees snapshot for quick lookup
$studentFees = [];
$sfRes = $conn->prepare("SELECT * FROM student_fees WHERE student_id = ?");
$sfRes->bind_param('i', $student_id);
$sfRes->execute();
$sfResRes = $sfRes->get_result();
while ($sfr = $sfResRes->fetch_assoc()) {
    $studentFees[$sfr['class_fee_id']] = $sfr;
}
$sfRes->close();

// Determine iteration start date
if ($enrolled_at) {
    $start = new DateTime(date('Y-m-01', strtotime($enrolled_at)));
} else {
    $start = new DateTime(date('Y-01-01')); // fallback to start of year
}
$today = new DateTime(date('Y-m-01'));
$cursor = clone $start;

// Helper function to get Arabic month name
function get_month_name($month, $langId) {
    $monthNames = [
        1 => 'month_jan', 2 => 'month_feb', 3 => 'month_mar',
        4 => 'month_apr', 5 => 'month_may', 6 => 'month_jun',
        7 => 'month_jul', 8 => 'month_aug', 9 => 'month_sep',
        10 => 'month_oct', 11 => 'month_nov', 12 => 'month_dec'
    ];
    return t($monthNames[$month]);
}

while ($cursor <= $today) {
    $year = (int)$cursor->format('Y');
    $month = (int)$cursor->format('n');

    $feeRec = find_previous_class_fee($classFees, $class_id, $year, $month, 24);
    if (!$feeRec) {
        $cursor->modify('+1 month');
        continue;
    }

    $class_fee_id = (int)$feeRec['id'];
    $original_amount = (float)$feeRec['amount'];

    $isFallback = (
    $feeRec['source_month'] != $month ||
    $feeRec['source_year'] != $year
);

if ($isFallback) {
    // NO PAYMENT CAN APPLY
    $sf = null;
} else {
    $sf = $studentFees[$class_fee_id] ?? null;
}

    $discount = (float)($sf['discount_amount'] ?? 0);
    $paid = (float)($sf['paid_amount'] ?? 0);
    $pending = safe_pending($original_amount, $discount, $paid);

    $total_original += $original_amount;
    $total_discount += $discount;
    $total_paid     += $paid;
    $total_pending  += $pending;

    $fees[] = [
        'class_fee_id' => $class_fee_id,
        'month' => $month,
        'year' => $year,
        'original_amount' => $original_amount,
        'fee_category' => $feeRec['fee_category_id'] ?? null,
        'discount' => $discount,
        'discount_reason' => $sf['discount_reason'] ?? null,
        'paid_amount' => $paid,
        'pending' => $pending,
        'status' => $sf['status'] ?? 'unpaid',
        'source_month' => $feeRec['source_month'] ?? $month,
        'source_year' => $feeRec['source_year'] ?? $year
    ];

    $cursor->modify('+1 month');
}

/* ================= CHART DATA ================= */
$chart_labels = [];
$chart_collected = [];
$chart_pending = [];

$tmp = [];
foreach ($fees as $f) {
    $yr = $f['year'];
    $mo = $f['month'];
    $key = $yr . '-' . $mo;
    if (!isset($tmp[$key])) {
        $tmp[$key] = [
            'collected' => 0,
            'pending' => 0,
            'label' => get_month_name($mo, $currentLangId) . ' ' . $yr
        ];
    }

    $paid = (float)($f['paid_amount'] ?? 0);
    $discount = (float)($f['discount'] ?? 0);
    $amount = (float)($f['original_amount'] ?? 0);
    $pending = safe_pending($amount, $discount, $paid);

    $tmp[$key]['collected'] += $paid;
    $tmp[$key]['pending'] += $pending;
}

foreach ($tmp as $t) {
    $chart_labels[] = $t['label'];
    $chart_collected[] = $t['collected'];
    $chart_pending[] = $t['pending'];
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?>">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><i class="fas fa-chart-line"></i> <?= t('header_title') ?></h1>
            <div class="header-actions">
                <button class="btn btn-secondary" onclick="window.history.back()">
                    <i class="fas fa-arrow-left"></i> <?= t('back_button') ?>
                </button>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> <?= t('print_button') ?>
                </button>
            </div>
        </div>

        <!-- Student Profile Card -->
        <div class="profile-card">
            <div class="profile-header">
                <div class="student-info">
                    <h2><?= htmlspecialchars($student['name']) ?></h2>
                    <p><i class="fas fa-user-graduate"></i> <?= t('student_class') ?>: <?= htmlspecialchars($student['class_name']) ?> | <?= t('student_section') ?>: <?= htmlspecialchars($student['section_name']) ?></p>
                    <p><i class="fas fa-calendar-alt"></i> <?= t('academic_year') ?>: <?= htmlspecialchars($student['academic_year']) ?></p>
                    <p><i class="fas fa-venus-mars"></i> <?= t('gender') ?>: <?= htmlspecialchars($student['gender']) ?></p>
                    <p><i class="fas fa-phone"></i> <?= t('guardian_phone') ?>: <?= htmlspecialchars($student['guardian_phone']) ?></p>
                </div>
                <div class="alert">
                    <i class="fas fa-info-circle"></i>
                    <span><?= t('alert_info') ?></span>
                </div>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-cards">
            <div class="summary-card card-1">
                <div class="card-icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                </div>
                <div class="card-content">
                    <h3><?= t('summary_total_original') ?></h3>
                    <div class="amount"><?= number_format($total_original, 2) ?></div>
                    <div class="trend up">
                        <i class="fas fa-chart-line"></i>
                        <?= t('summary_tooltip_original') ?>
                    </div>
                </div>
            </div>
            
            <div class="summary-card card-2">
                <div class="card-icon">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="card-content">
                    <h3><?= t('summary_total_discount') ?></h3>
                    <div class="amount"><?= number_format($total_discount, 2) ?></div>
                    <div class="trend down">
                        <i class="fas fa-arrow-down"></i>
                        <?= t('summary_tooltip_discount', ['percentage' => number_format(($total_discount/$total_original)*100, 1)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="summary-card card-3">
                <div class="card-icon">
                    <i class="fas fa-money-check-alt"></i>
                </div>
                <div class="card-content">
                    <h3><?= t('summary_total_paid') ?></h3>
                    <div class="amount"><?= number_format($total_paid, 2) ?></div>
                    <div class="trend up">
                        <i class="fas fa-arrow-up"></i>
                        <?= t('summary_tooltip_paid', ['percentage' => number_format(($total_paid/($total_original - $total_discount))*100, 1)]) ?>
                    </div>
                </div>
            </div>
            
            <div class="summary-card card-4">
                <div class="card-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="card-content">
                    <h3><?= t('summary_total_pending') ?></h3>
                    <div class="amount"><?= number_format($total_pending, 2) ?></div>
                    <div class="trend <?= $total_pending > 0 ? 'down' : 'up' ?>">
                        <?php if($total_pending > 0): ?>
                        <i class="fas fa-exclamation-triangle"></i>
                        <?= t('summary_tooltip_pending_attention') ?>
                        <?php else: ?>
                        <i class="fas fa-check-circle"></i>
                        <?= t('summary_tooltip_pending_clear') ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-container">
            <div class="chart-header">
                <h3><i class="fas fa-chart-bar"></i> <?= t('chart_title') ?></h3>
                <div class="chart-controls">
                    <select id="chartType">
                        <option value="bar"><?= t('chart_bar_option') ?></option>
                        <option value="line"><?= t('chart_line_option') ?></option>
                    </select>
                    <select id="chartPeriod">
                        <option value="all"><?= t('chart_period_all') ?></option>
                        <option value="year"><?= t('chart_period_year') ?></option>
                        <option value="month"><?= t('chart_period_month') ?></option>
                    </select>
                </div>
            </div>
            <canvas id="financeChart" height="100"></canvas>
        </div>

        <!-- Fees Table -->
        <div class="table-container">
            <div class="table-header">
                <h3><i class="fas fa-table"></i> <?= t('table_title') ?></h3>
                <div class="table-info">
                    <span><?= t('table_showing_records', ['count' => count($fees)]) ?></span>
                </div>
            </div>
            <table>
                <thead>
                    <tr>
                        <th><?= t('table_header_month') ?></th>
                        <th><?= t('table_header_year') ?></th>
                        <th><?= t('table_header_category') ?></th>
                        <th><?= t('table_header_original') ?></th>
                        <th><?= t('table_header_discount') ?></th>
                        <th><?= t('table_header_paid') ?></th>
                        <th><?= t('table_header_pending') ?></th>
                        <th><?= t('table_header_status') ?></th>
                        <th><?= t('table_header_reason') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($fees as $f): ?>
                    <tr>
                        <td><strong><?= get_month_name($f['month'], $currentLangId) ?></strong></td>
                        <td><?= $f['year'] ?></td>
                        <td><?= htmlspecialchars($f['fee_category']) ?></td>
                        <td><?= number_format($f['original_amount'], 2) ?></td>
                        <td>
                            <?php if($f['discount'] > 0): ?>
                            <span style="color:var(--warning-color);">
                                <i class="fas fa-tag"></i> <?= number_format($f['discount'], 2) ?>
                            </span>
                            <?php else: ?>
                            <?= number_format($f['discount'], 2) ?>
                            <?php endif; ?>
                        </td>
                        <td><?= number_format($f['paid_amount'] ?? 0, 2) ?></td>
                        <td>
                            <?php if($f['pending'] > 0): ?>
                            <span style="color:var(--danger-color);">
                                <i class="fas fa-exclamation-circle"></i> <?= number_format($f['pending'], 2) ?>
                            </span>
                            <?php else: ?>
                            <span style="color:var(--success-color);">
                                <i class="fas fa-check-circle"></i> <?= number_format($f['pending'], 2) ?>
                            </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="status <?= $f['status'] ?>">
                                <?= t('status_' . $f['status']) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($f['discount_reason'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p><?= t('footer_copyright') ?> &copy; <?= date('Y') ?> | <?= t('footer_generated') ?> <?= date('F j, Y, g:i a') ?></p>
        </div>
    </div>

    <script>
        // Initialize Chart
        const ctx = document.getElementById('financeChart').getContext('2d');
        
        // Chart colors based on theme
        const isDark = document.body.classList.contains('dark');
        const chartColors = {
            collected: isDark ? '#10b981' : '#059669',
            pending: isDark ? '#ef4444' : '#dc2626',
            grid: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
            text: isDark ? '#e2e8f0' : '#334155'
        };
        
        let financeChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($chart_labels) ?>,
                datasets: [
                    {
                        label: '<?= $currentLangId == 2 ? 'محصل' : 'Collected' ?>',
                        data: <?= json_encode($chart_collected) ?>,
                        backgroundColor: chartColors.collected,
                        borderColor: chartColors.collected,
                        borderWidth: 1,
                        borderRadius: 6
                    },
                    {
                        label: '<?= $currentLangId == 2 ? 'معلق' : 'Pending' ?>',
                        data: <?= json_encode($chart_pending) ?>,
                        backgroundColor: chartColors.pending,
                        borderColor: chartColors.pending,
                        borderWidth: 1,
                        borderRadius: 6
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            color: chartColors.text,
                            font: {
                                size: 14
                            }
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: isDark ? '#1e293b' : '#ffffff',
                        titleColor: chartColors.text,
                        bodyColor: chartColors.text,
                        borderColor: chartColors.grid,
                        borderWidth: 1
                    }
                },
                scales: {
                    x: {
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: chartColors.grid
                        },
                        ticks: {
                            color: chartColors.text,
                            callback: function(value) {
                                return '₹' + value;
                            }
                        }
                    }
                }
            }
        });
        
        // Chart controls
        document.getElementById('chartType').addEventListener('change', function() {
            financeChart.config.type = this.value;
            financeChart.update();
        });
        
        // Print optimization
        window.addEventListener('beforeprint', () => {
            financeChart.resize(800, 400);
        });
        
        // Add some interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Highlight rows on hover
            const tableRows = document.querySelectorAll('tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateX(5px)';
                });
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateX(0)';
                });
            });
            
            // Add animation to summary cards on load
            const cards = document.querySelectorAll('.summary-card');
            cards.forEach((card, index) => {
                card.style.animationDelay = `${index * 0.1}s`;
                card.style.animation = 'fadeInUp 0.5s ease forwards';
                card.style.opacity = '0';
            });
            
            // Add CSS animation
            const style = document.createElement('style');
            style.textContent = `
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
            `;
            document.head.appendChild(style);
        });
    </script>
</body>
</html>

    <style>
        :root {
            --primary-color: #4361ee;
            --primary-light: #eef2ff;
            --secondary-color: #3a0ca3;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --info-color: #3b82f6;
            --light-bg: #f8fafc;
            --dark-bg: #0f172a;
            --card-light: #ffffff;
            --card-dark: #1e293b;
            --text-light: #334155;
            --text-dark: #e2e8f0;
            --border-light: #e2e8f0;
            --border-dark: #334155;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s ease;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body.light {
            background: var(--light-bg);
            color: var(--text-light);
        }

        body.dark {
            background: var(--dark-bg);
            color: var(--text-dark);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .header h1 i {
            font-size: 32px;
        }

        .header-actions {
            display: flex;
            gap: 15px;
        }

        .btn {
            padding: 12px 24px;
            border-radius: var(--radius-sm);
            border: none;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
            font-size: 15px;
        }

        .btn-primary {
            background: var(--primary-color);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .btn-secondary {
            background: var(--light-bg);
            color: var(--text-light);
            border: 1px solid var(--border-light);
        }

        .dark .btn-secondary {
            background: var(--card-dark);
            color: var(--text-dark);
            border-color: var(--border-dark);
        }

        .btn-secondary:hover {
            background: var(--border-light);
            transform: translateY(-2px);
        }

        .dark .btn-secondary:hover {
            background: var(--border-dark);
        }

        /* Student Profile Card */
        .profile-card {
            background: var(--card-light);
            border-radius: var(--radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
            border-left: 5px solid var(--primary-color);
        }

        .dark .profile-card {
            background: var(--card-dark);
        }

        .profile-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .student-info h2 {
            font-size: 22px;
            margin-bottom: 8px;
            color: var(--primary-color);
        }

        .student-info p {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
            font-size: 15px;
        }

        .student-info i {
            width: 20px;
            color: var(--primary-color);
        }

        .alert {
            background: #fef3c7;
            color: #92400e;
            padding: 12px 16px;
            border-radius: var(--radius-sm);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            max-width: 500px;
        }

        .dark .alert {
            background: #451a03;
            color: #fbbf24;
        }

        /* Summary Cards */
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: var(--card-light);
            border-radius: var(--radius);
            padding: 25px;
            box-shadow: var(--shadow);
            display: flex;
            align-items: center;
            gap: 20px;
            transition: var(--transition);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .dark .summary-card {
            background: var(--card-dark);
        }

        .card-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .card-1 .card-icon {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .card-2 .card-icon {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .card-3 .card-icon {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .card-4 .card-icon {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        .card-content h3 {
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 8px;
            opacity: 0.8;
        }

        .card-content .amount {
            font-size: 26px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .card-content .trend {
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .card-content .trend.up {
            color: var(--success-color);
        }

        .card-content .trend.down {
            color: var(--danger-color);
        }

        /* Chart Container */
        .chart-container {
            background: var(--card-light);
            border-radius: var(--radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--shadow);
        }

        .dark .chart-container {
            background: var(--card-dark);
        }

        .chart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .chart-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }

        .chart-controls {
            display: flex;
            gap: 10px;
        }

        .chart-controls select {
            padding: 8px 15px;
            border-radius: var(--radius-sm);
            border: 1px solid var(--border-light);
            background: var(--card-light);
            color: var(--text-light);
            font-size: 14px;
            cursor: pointer;
        }

        .dark .chart-controls select {
            background: var(--card-dark);
            border-color: var(--border-dark);
            color: var(--text-dark);
        }

        /* Fees Table */
        .table-container {
            background: var(--card-light);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            margin-bottom: 30px;
            overflow-x: auto;
        }

        .dark .table-container {
            background: var(--card-dark);
        }

        .table-header {
            padding: 20px 25px;
            border-bottom: 1px solid var(--border-light);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dark .table-header {
            border-bottom-color: var(--border-dark);
        }

        .table-header h3 {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 900px;
        }

        thead {
            background: var(--primary-light);
        }

        .dark thead {
            background: rgba(59, 130, 246, 0.1);
        }

        th {
            padding: 16px 20px;
            text-align: left;
            font-weight: 600;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary-color);
            border-bottom: 1px solid var(--border-light);
        }

        .dark th {
            border-bottom-color: var(--border-dark);
        }

        td {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            font-size: 14px;
        }

        .dark td {
            border-bottom-color: var(--border-dark);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: var(--primary-light);
        }

        .dark tbody tr:hover {
            background: rgba(59, 130, 246, 0.05);
        }

        /* Status badges */
        .status {
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
        }

        .status.paid {
            background: #d1fae5;
            color: #065f46;
        }

        .dark .status.paid {
            background: #064e3b;
            color: #a7f3d0;
        }

        .status.partial {
            background: #fef3c7;
            color: #92400e;
        }

        .dark .status.partial {
            background: #78350f;
            color: #fde68a;
        }

        .status.unpaid {
            background: #fee2e2;
            color: #991b1b;
        }

        .dark .status.unpaid {
            background: #7f1d1d;
            color: #fca5a5;
        }

        /* Footer */
        .footer {
            text-align: center;
            padding: 20px;
            margin-top: 30px;
            font-size: 14px;
            opacity: 0.7;
            border-top: 1px solid var(--border-light);
        }

        .dark .footer {
            border-top-color: var(--border-dark);
        }

        /* Print Styles */
        @media print {
            .header-actions,
            .alert,
            .chart-container,
            .footer {
                display: none;
            }
            
            .container {
                padding: 0;
            }
            
            .profile-card,
            .summary-card,
            .table-container {
                box-shadow: none;
                border: 1px solid #ddd;
                margin-bottom: 20px;
                page-break-inside: avoid;
            }
            
            table {
                min-width: auto;
            }
            
            body {
                font-size: 12px;
            }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }
            
            .btn {
                padding: 10px 18px;
                font-size: 14px;
            }
            
            .summary-cards {
                grid-template-columns: 1fr;
            }
            
            .profile-header {
                flex-direction: column;
            }
            
            th, td {
                padding: 12px 15px;
            }
            
            .table-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }

        @media (max-width: 480px) {
            .container {
                padding: 15px;
            }
            
            .header h1 {
                font-size: 24px;
            }
            
            .btn {
                padding: 8px 15px;
                font-size: 13px;
            }
            
            .card-content .amount {
                font-size: 22px;
            }
        }
    </style>