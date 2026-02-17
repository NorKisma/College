<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// ================= THEME =================
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
if ($theme_res && $theme_row = $theme_res->fetch_assoc()) {
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

function is_member() {
    return ($_SESSION['role'] ?? '') === 'member';
}
// Get current month and year
$current_month = date('n');
$current_year  = date('Y');
// ================= FILTERS =================
$filter_month   = isset($_GET['month']) ? (int)$_GET['month'] : $current_month;
$filter_year    = isset($_GET['year']) ? (int)$_GET['year'] : $current_year;
$filter_class   = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$filter_paid    = isset($_GET['paid_only']) ? 1 : 0;

$filter_from    = $_GET['from_date'] ?? null;
$filter_to      = $_GET['to_date'] ?? null;


$monthNames = [];
for ($m = 1; $m <= 12; $m++) {
    $monthNames[$m] = date('F', mktime(0, 0, 0, $m, 1));
}


/* ================= FEE CATEGORY ================= */
$fee_res = $conn->query("SELECT id FROM fee_categories WHERE name='Fee Salary' LIMIT 1");
$fee_salary_category_id = ($fee_res && $r = $fee_res->fetch_assoc()) ? (int)$r['id'] : 0;

// ================= COMMON FILTER WHERE =================
$where = [];
$params = [];
$types  = "";

$where[] = "cf.fee_category_id = ?";
$params[] = $fee_salary_category_id;
$types   .= "i";

$where[] = "cf.month = ?";
$where[] = "cf.year = ?";
$params[] = $filter_month;
$params[] = $filter_year;
$types   .= "ii";

if ($filter_from && $filter_to) {
    $where[] = "sf.payment_date BETWEEN ? AND ?";
    $params[] = $filter_from;
    $params[] = $filter_to;
    $types   .= "ss";
}

if ($filter_paid) {
    $where[] = "sf.paid_amount > 0";
}

if ($filter_class) {
    $where[] = "cf.class_id = ?";
    $params[] = $filter_class;
    $types   .= "i";
}

$where_sql = implode(" AND ", $where);

// ================= HELPER FUNCTIONS =================
function sf_discount(array $row): float {
    if (!empty($row['discount_amount']) && $row['discount_amount'] > 0) {
        return (float)$row['discount_amount'];
    }
    if (!empty($row['discount_percentage']) && $row['discount_percentage'] > 0) {
        return ($row['original_amount'] * $row['discount_percentage']) / 100;
    }
    return 0.0;
}

function sf_final(array $row): float {
    return max(0, $row['original_amount'] - sf_discount($row));
}

function sf_pending(array $row): float {
    return max(0, sf_final($row) - $row['paid_amount']);
}


// ================= SUMMARY STATISTICS =================
$summary = [];
$total_collected = 0;
$total_pending   = 0;
$students_paid   = [];

// Get all students
$students_res = $conn->query("SELECT id FROM students");
while ($student = $students_res->fetch_assoc()) {
    $student_id = $student['id'];

    // Get student's class
    $stmt_class = $conn->prepare("
        SELECT class_id 
        FROM class_students 
        WHERE student_id=? 
        ORDER BY academic_year DESC 
        LIMIT 1
    ");
    $stmt_class->bind_param("i", $student_id);
    $stmt_class->execute();
    $stmt_class->bind_result($class_id);

    if ($stmt_class->fetch()) {
        $stmt_class->close();

        // Get section of class
        $stmt_sec = $conn->prepare("SELECT section_id FROM classes WHERE id=? LIMIT 1");
        $stmt_sec->bind_param("i", $class_id);
        $stmt_sec->execute();
        $stmt_sec->bind_result($section_id);
        $stmt_sec->fetch();
        $stmt_sec->close();

        // Get section fee
        $stmt_fee = $conn->prepare("
            SELECT amount 
            FROM section_fees 
            WHERE section_id=? AND fee_category_id=? 
            ORDER BY created_at DESC 
            LIMIT 1
        ");
        $stmt_fee->bind_param("ii", $section_id, $fee_salary_category_id);
        $stmt_fee->execute();
        $stmt_fee->bind_result($section_fee_amount);
        $stmt_fee->fetch();
        $stmt_fee->close();

        // Check if student has paid this month
        $stmt_pay = $conn->prepare("
            SELECT 
                sf.original_amount,
                sf.paid_amount,
                sf.discount_amount,
                sf.discount_percentage
            FROM student_fees sf
            JOIN class_fees cf ON sf.class_fee_id = cf.id
            WHERE sf.student_id=? 
              AND cf.class_id=? 
              AND cf.fee_category_id=? 
              AND cf.month=? 
              AND cf.year=?
            LIMIT 1
        ");
        $stmt_pay->bind_param(
            "iiiii",
            $student_id,
            $class_id,
            $fee_salary_category_id,
            $current_month,
            $current_year
        );
        $stmt_pay->execute();
        $res_pay = $stmt_pay->get_result();

        if ($row_pay = $res_pay->fetch_assoc()) {
            $pending = sf_pending($row_pay);

            if ($row_pay['paid_amount'] > 0) {
                $students_paid[$student_id] = true;
            }
        } else {
            // No student_fees row → full section fee pending
            $pending = $section_fee_amount;
        }

        $total_pending += $pending;
        $stmt_pay->close();
    } else {
        $stmt_class->close();
    }
}

    
$summary['pending'] = $total_pending; 

$stmt = $conn->prepare("
    SELECT 
        COALESCE(SUM(sf.paid_amount), 0) AS collected,
        COUNT(DISTINCT sf.student_id) AS students_paid
    FROM student_fees sf
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    WHERE $where_sql
");
$stmt->bind_param($types, ...$params);
$stmt->execute();
$stmt->bind_result($summary['collected'], $summary['students_paid']);
$stmt->fetch();
$stmt->close();

 

// Total classes with Fee Salary fees this month
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT cf.class_id)
    FROM class_fees cf
    WHERE cf.fee_category_id = ?
      AND cf.month = ?
      AND cf.year = ?
" . ($filter_class ? " AND cf.class_id = ?" : "")
);

if ($filter_class) {
    $stmt->bind_param("iiii", $fee_salary_category_id, $filter_month, $filter_year, $filter_class);
} else {
    $stmt->bind_param("iii", $fee_salary_category_id, $filter_month, $filter_year);
}

$stmt->execute();
$stmt->bind_result($summary['classes']);
$stmt->fetch();
$stmt->close();


// ================= RECENT PAYMENTS (LAST 10) =================
$where = [];
$params = [];
$types  = "";

// Base filters
$where[] = "cf.fee_category_id = ?";
$params[] = $fee_salary_category_id;
$types   .= "i";

// Month / year
$where[] = "cf.month = ?";
$where[] = "cf.year = ?";
$params[] = $filter_month;
$params[] = $filter_year;
$types   .= "ii";

// Date range
if ($filter_from && $filter_to) {
    $where[] = "sf.payment_date BETWEEN ? AND ?";
    $params[] = $filter_from;
    $params[] = $filter_to;
    $types   .= "ss";
}

// Paid only
if ($filter_paid) {
    $where[] = "sf.paid_amount > 0";
}

// Class filter
if ($filter_class) {
    $where[] = "cf.class_id = ?";
    $params[] = $filter_class;
    $types   .= "i";
}

$sql = "
    SELECT 
        sf.id as payment_id,
        s.id as student_id,
        s.name as student_name,
        s.gender,
        c.name as class_name,
        sec.name as section_name,
        sf.original_amount,
        sf.paid_amount,
        sf.discount_amount,
        sf.discount_percentage,
        sf.status,
        sf.payment_date,
        sf.created_at
    FROM student_fees sf
    JOIN students s ON sf.student_id = s.id
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    JOIN classes c ON cf.class_id = c.id
    LEFT JOIN sections sec ON c.section_id = sec.id
    WHERE " . implode(" AND ", $where) . "
    ORDER BY sf.payment_date DESC, sf.created_at DESC
    LIMIT 100
";

$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();

$recent_payments_result = $stmt->get_result();
$recent_payments = [];
while ($row = $recent_payments_result->fetch_assoc()) {
    $row['discount']         = sf_discount($row);
    $row['final_amount']     = sf_final($row);
    $row['remaining_amount'] = sf_pending($row);
    $recent_payments[] = $row;
}
$stmt->close();

// ================= CLASS-WISE ANALYSIS =================
$class_analysis_query = "
    SELECT 
        c.id AS class_id,
        c.name AS class_name,
        c.section_id,
        sec.name AS section_name,
        COUNT(DISTINCT cs.student_id) AS total_students
    FROM classes c
    LEFT JOIN sections sec ON c.section_id = sec.id
    INNER JOIN class_students cs 
        ON cs.class_id = c.id 
       AND cs.is_active = 1
    GROUP BY c.id, c.name, c.section_id, sec.name
    ORDER BY c.name
";

$stmt = $conn->prepare($class_analysis_query);
$stmt->execute();
$class_analysis_result = $stmt->get_result();
$class_analysis = [];

while ($class = $class_analysis_result->fetch_assoc()) {
    $stmt_students = $conn->prepare("SELECT student_id FROM class_students WHERE class_id=?");
    $stmt_students->bind_param("i", $class['class_id']);
    $stmt_students->execute();
    $students_res2 = $stmt_students->get_result();

    $total_collected = 0;
    $total_pending = 0;
    $paid_students = [];

    while ($stu = $students_res2->fetch_assoc()) {
        $student_id = $stu['student_id'];
        $section_id = $class['section_id'] ?? null;
        if (!$section_id) continue;

        // Get latest section fee
        $stmt_fee2 = $conn->prepare("
            SELECT amount FROM section_fees WHERE section_id=? AND fee_category_id=? ORDER BY created_at DESC LIMIT 1
        ");
        $stmt_fee2->bind_param("ii", $section_id, $fee_salary_category_id);
        $stmt_fee2->execute();
        $stmt_fee2->bind_result($section_fee_amount);
        $stmt_fee2->fetch();
        $stmt_fee2->close();

        // Get student fee for this month
        $stmt_fee_paid = $conn->prepare("
            SELECT original_amount, paid_amount, discount_amount, discount_percentage 
            FROM student_fees 
            WHERE student_id=? 
              AND class_fee_id IN (
                  SELECT id FROM class_fees 
                  WHERE class_id=? AND fee_category_id=? AND month=? AND year=?
              )
            LIMIT 1
        ");
        $stmt_fee_paid->bind_param("iiiii", $student_id, $class['class_id'], $fee_salary_category_id, $current_month, $current_year);
        $stmt_fee_paid->execute();
        $res_fee_paid = $stmt_fee_paid->get_result();

        if ($row_fee = $res_fee_paid->fetch_assoc()) {
            // Calculate pending using discount helper
            $pending = sf_pending($row_fee);
            if ($row_fee['paid_amount'] > 0) {
                $paid_students[$student_id] = true;
            }
            $total_collected += $row_fee['paid_amount'];
        } else {
            // Student has not paid → full section fee pending
            $pending = $section_fee_amount;
        }

        $total_pending += $pending;
        $stmt_fee_paid->close();
    }

    $stmt_students->close();

    // Add totals to class array
    $class['total_collected'] = $total_collected;
    $class['total_pending'] = $total_pending;
    $class['paid_students'] = count($paid_students);

    $class_analysis[] = $class;
}

$stmt->close();

// ================= TREND CALCULATION =================
$last_month = $current_month - 1;
$last_month_year = $current_year;
if ($last_month == 0) {
    $last_month = 12;
    $last_month_year--;
}
function get_monthly_fees($conn, $month, $year, $fee_category_id) {

    $stmt = $conn->prepare("
        SELECT 
            sf.original_amount,
            sf.paid_amount,
            sf.discount_amount,
            sf.discount_percentage
        FROM student_fees sf
        JOIN class_fees cf ON sf.class_fee_id = cf.id
        WHERE cf.fee_category_id = ?
          AND cf.month = ?
          AND cf.year = ?
    ");
    $stmt->bind_param("iii", $fee_category_id, $month, $year);
    $stmt->execute();
    $res = $stmt->get_result();

    $collected = 0;
    $pending   = 0;
    $students_paid = [];

    while ($row = $res->fetch_assoc()) {
        $collected += $row['paid_amount'];

        if ($row['paid_amount'] > 0) {
            $students_paid[$row['student_id'] ?? uniqid()] = true;
        }

        $pending += sf_pending($row);
    }

    $stmt->close();

    return [
        'collected'     => $collected,
        'pending'       => $pending,
        'students_paid' => count($students_paid)
    ];
}



    // Include students who haven't paid at all

$current_data = get_monthly_fees($conn, $filter_month, $filter_year, $fee_salary_category_id);

$prev_month = $filter_month - 1;
$prev_year  = $filter_year;
if ($prev_month == 0) {
    $prev_month = 12;
    $prev_year--;
}

$last_data = get_monthly_fees($conn, $prev_month, $prev_year, $fee_salary_category_id);

function calc_percent_change($current, $previous) {
    if ($previous == 0) return $current > 0 ? 100 : 0;
    return round((($current - $previous) / $previous) * 100, 1);
}

$summary['collected_trend']     = calc_percent_change($current_data['collected'], $last_data['collected']);
$summary['pending_trend']       = calc_percent_change($current_data['pending'], $last_data['pending']);
$summary['students_paid_trend'] = calc_percent_change($current_data['students_paid'], $last_data['students_paid']);

// ================= CHART DATA FOR 12 MONTHS =================
$chart_labels = [];
$chart_data   = [];
for ($m = 1; $m <= 12; $m++) {
    $month_data = get_monthly_fees($conn, $m, $current_year, $fee_salary_category_id);
    $chart_labels[] = date('M', mktime(0,0,0,$m,1));
    $chart_data[]   = $month_data['collected'];
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fees Management Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   
</head>
<?php
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Hardcoded translations for Fees Management page
$translations = [
    1 => [ // English
        // Header
        'dashboard_title' => 'Fees Management Dashboard',
        'dashboard_subtitle' => 'Real-time overview of fees collection and pending payments',
        
        // Quick Links (tooltips)
        'view_paid_fees' => 'View Paid Fees',
        'view_unpaid_fees' => 'View Unpaid Fees',
        'school_holidays' => 'School Holidays',
        'student_discounts' => 'Student Discounts',
        'fee_categories' => 'Fee Categories',
        
        // Filter Labels
        'filter_from' => 'From',
        'filter_to' => 'To',
        'filter_month' => 'Month',
        'filter_year' => 'Year',
        'filter_class' => 'Class',
        'filter_all_classes' => 'All Classes',
        'filter_apply' => 'Apply',
        
        // Summary Cards
        'summary_total_collected' => 'Total Collected This Month',
        'summary_total_pending' => 'Total Pending Fees',
        'summary_students_paid' => 'Students Paid',
        'summary_active_classes' => 'Active Classes',
        'summary_from_students' => 'From {count} students',
        'summary_needs_followup' => 'Needs follow-up',
        'summary_good_collection' => 'Good collection rate',
        'summary_with_fee_structure' => 'With fee structure',
        
        // Chart Section
        'chart_title' => 'Collection Trend',
        'chart_subtitle' => 'Monthly fees collection overview',
        
        // Recent Payments
        'recent_payments_title' => 'Recent Payments',
        'recent_payments_subtitle' => 'Last 10 fee payments recorded',
        'new_payment' => 'New Payment',
        'export' => 'Export',
        
        // Table Headers
        'th_student' => 'Student',
        'th_class' => 'Class',
        'th_amount_paid' => 'Amount Paid',
        'th_pending' => 'Pending',
        'th_status' => 'Status',
        'th_date' => 'Date',
        'th_actions' => 'Actions',
        
        // Status Labels
        'status_paid' => 'Paid',
        'status_partial' => 'Partial',
        'status_unpaid' => 'Unpaid',
        
        // Action Buttons (tooltips)
        'action_view' => 'View Details',
        'action_edit' => 'Edit Payment',
        'action_delete' => 'Delete Payment',
        
        // Empty States
        'no_payments_found' => 'No Payments Found',
        'no_payments_description' => 'No fee payments have been recorded for this month yet.',
        'no_class_data' => 'No Class Data',
        'no_class_data_description' => 'No class fee data available for this month.',
        
        // Class Analysis
        'class_analysis_title' => 'Class-wise Analysis',
        'class_analysis_subtitle' => 'Collection status per class',
        'stat_total_students' => 'Total Students',
        'stat_paid' => 'Paid',
        'stat_collected' => 'Collected',
        'stat_pending' => 'Pending',
        'stat_collection_progress' => 'Collection Progress',
        'stat_remaining' => 'remaining',
        'btn_view_class' => 'View Class Details',
        
        // Month Names (for filter)
        'month_january' => 'January',
        'month_february' => 'February',
        'month_march' => 'March',
        'month_april' => 'April',
        'month_may' => 'May',
        'month_june' => 'June',
        'month_july' => 'July',
        'month_august' => 'August',
        'month_september' => 'September',
        'month_october' => 'October',
        'month_november' => 'November',
        'month_december' => 'December',
    ],
    2 => [ // Arabic
        // Header
        'dashboard_title' => 'لوحة إدارة الرسوم',
        'dashboard_subtitle' => 'نظرة عامة فورية على تحصيل الرسوم والمدفوعات المعلقة',
        
        // Quick Links (tooltips)
        'view_paid_fees' => 'عرض الرسوم المدفوعة',
        'view_unpaid_fees' => 'عرض الرسوم غير المدفوعة',
        'school_holidays' => 'العطل المدرسية',
        'student_discounts' => 'خصومات الطلاب',
        'fee_categories' => 'فئات الرسوم',
        
        // Filter Labels
        'filter_from' => 'من',
        'filter_to' => 'إلى',
        'filter_month' => 'الشهر',
        'filter_year' => 'السنة',
        'filter_class' => 'الصف',
        'filter_all_classes' => 'جميع الصفوف',
        'filter_apply' => 'تطبيق',
        
        // Summary Cards
        'summary_total_collected' => 'إجمالي ما تم تحصيله هذا الشهر',
        'summary_total_pending' => 'إجمالي الرسوم المعلقة',
        'summary_students_paid' => 'الطلاب المدفوعين',
        'summary_active_classes' => 'الصفوف النشطة',
        'summary_from_students' => 'من {count} طالب',
        'summary_needs_followup' => 'تحتاج إلى متابعة',
        'summary_good_collection' => 'معدل تحصيل جيد',
        'summary_with_fee_structure' => 'مع هيكل الرسوم',
        
        // Chart Section
        'chart_title' => 'اتجاه التحصيل',
        'chart_subtitle' => 'نظرة عامة على تحصيل الرسوم الشهري',
        
        // Recent Payments
        'recent_payments_title' => 'المدفوعات الأخيرة',
        'recent_payments_subtitle' => 'آخر 10 مدفوعات رسوم مسجلة',
        'new_payment' => 'دفعة جديدة',
        'export' => 'تصدير',
        
        // Table Headers
        'th_student' => 'الطالب',
        'th_class' => 'الصف',
        'th_amount_paid' => 'المبلغ المدفوع',
        'th_pending' => 'المعلق',
        'th_status' => 'الحالة',
        'th_date' => 'التاريخ',
        'th_actions' => 'الإجراءات',
        
        // Status Labels
        'status_paid' => 'مدفوع',
        'status_partial' => 'جزئي',
        'status_unpaid' => 'غير مدفوع',
        
        // Action Buttons (tooltips)
        'action_view' => 'عرض التفاصيل',
        'action_edit' => 'تعديل الدفع',
        'action_delete' => 'حذف الدفع',
        
        // Empty States
        'no_payments_found' => 'لم يتم العثور على مدفوعات',
        'no_payments_description' => 'لم يتم تسجيل أي مدفوعات رسوم لهذا الشهر بعد.',
        'no_class_data' => 'لا توجد بيانات للصفوف',
        'no_class_data_description' => 'لا توجد بيانات رسوم متاحة للصفوف لهذا الشهر.',
        
        // Class Analysis
        'class_analysis_title' => 'تحليل حسب الصف',
        'class_analysis_subtitle' => 'حالة التحصيل لكل صف',
        'stat_total_students' => 'إجمالي الطلاب',
        'stat_paid' => 'مدفوع',
        'stat_collected' => 'محصل',
        'stat_pending' => 'معلق',
        'stat_collection_progress' => 'تقدم التحصيل',
        'stat_remaining' => 'متبقي',
        'btn_view_class' => 'عرض تفاصيل الصف',
        
        // Month Names (for filter)
        'month_january' => 'يناير',
        'month_february' => 'فبراير',
        'month_march' => 'مارس',
        'month_april' => 'أبريل',
        'month_may' => 'مايو',
        'month_june' => 'يونيو',
        'month_july' => 'يوليو',
        'month_august' => 'أغسطس',
        'month_september' => 'سبتمبر',
        'month_october' => 'أكتوبر',
        'month_november' => 'نوفمبر',
        'month_december' => 'ديسمبر',
    ]
];

// Helper function with string replacement support
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    // Replace placeholders like {count}
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('dashboard_title') ?> | School Management</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ============================================
           PROFESSIONAL FEE MANAGEMENT DASHBOARD STYLING
           With RTL/LTR support
           ============================================ */
        
        :root {
            /* Primary Colors */
            --primary-blue: #4f46e5;
            --primary-blue-light: #6366f1;
            --primary-blue-dark: #4338ca;
            --primary-blue-bg: rgba(79, 70, 229, 0.1);
            
            /* Light Mode */
            --bg-primary: #ffffff;
            --bg-secondary: #f8fafc;
            --bg-tertiary: #f1f5f9;
            --bg-card: #ffffff;
            
            /* Text Colors */
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            
            /* Border Colors */
            --border-color: #e2e8f0;
            --border-light: #f1f5f9;
            
            /* Status Colors */
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            
            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            
            /* Spacing */
            --space-xs: 0.25rem;
            --space-sm: 0.5rem;
            --space-md: 1rem;
            --space-lg: 1.5rem;
            --space-xl: 2rem;
            
            /* Border Radius */
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --radius-xl: 1rem;
        }

        /* Dark Mode */
        .dark {
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --bg-card: #1e293b;
            
            --text-primary: #f8fafc;
            --text-secondary: #cbd5e1;
            --text-tertiary: #94a3b8;
            
            --border-color: #334155;
            --border-light: #1e293b;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            transition: background-color 0.3s ease;
            min-height: 100vh;
        }

        body[dir="rtl"] {
            font-family: 'Inter', 'Segoe UI Arabic', sans-serif;
            text-align: right;
        }

        .container {
            max-width: 1440px;
            margin: 0 auto;
            padding: 2rem;
        }

        /* Header */
        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .header-left h1 {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.5rem;
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-light));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .subtitle {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-secondary);
            font-size: 1rem;
        }

        .subtitle i {
            color: var(--primary-blue);
        }

        .header-right {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .btn-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
            background: var(--primary-blue-bg);
            color: var(--primary-blue);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .btn-icon:hover {
            background: var(--primary-blue);
            color: white;
            transform: translateY(-2px);
            border-color: var(--primary-blue);
        }

        /* Filters */
        .filters-bar {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            background: var(--bg-card);
            padding: 1.5rem;
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-secondary);
        }

        .filter-group input,
        .filter-group select {
            padding: 0.75rem;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            background: var(--bg-secondary);
            color: var(--text-primary);
            font-family: inherit;
            transition: border-color 0.2s ease;
        }

        .filter-group input:focus,
        .filter-group select:focus {
            outline: none;
            border-color: var(--primary-blue);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: var(--radius-lg);
            font-family: inherit;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue), var(--primary-blue-dark));
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(79, 70, 229, 0.3);
        }

        .btn-secondary {
            background: var(--bg-secondary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-sm {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
        }

        /* Summary Grid */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .summary-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            padding: 1.5rem;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .summary-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
            border-color: var(--primary-blue);
        }

        .summary-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
        }

        .summary-card.collected::before { background: linear-gradient(90deg, var(--success), #34d399); }
        .summary-card.pending::before { background: linear-gradient(90deg, var(--warning), #fbbf24); }
        .summary-card.students::before { background: linear-gradient(90deg, var(--info), #60a5fa); }
        .summary-card.classes::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1rem;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            background: var(--primary-blue-bg);
            border-radius: var(--radius-lg);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-blue);
            font-size: 1.25rem;
        }

        .card-trend {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            background: var(--success);
            color: white;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .card-trend.negative {
            background: var(--danger);
        }

        .card-value {
            font-size: 2rem;
            font-weight: 800;
            margin-bottom: 0.25rem;
        }

        .card-label {
            color: var(--text-secondary);
            font-size: 0.875rem;
            margin-bottom: 1rem;
        }

        .card-details {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--text-tertiary);
            font-size: 0.75rem;
            padding-top: 1rem;
            border-top: 1px solid var(--border-light);
        }

        /* Chart Section */
        .chart-section {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem;
        }

        .section-title {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .section-subtitle {
            color: var(--text-secondary);
            font-size: 0.875rem;
        }

        .chart-container {
            height: 300px;
            position: relative;
        }

        /* Main Grid */
        .main-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
        }

        @media (min-width: 1024px) {
            .main-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        .section-card {
            background: var(--bg-card);
            border-radius: var(--radius-xl);
            border: 1px solid var(--border-color);
            padding: 1.5rem;
            box-shadow: var(--shadow-sm);
        }

        /* Table */
        .table-container {
            overflow-x: auto;
            margin-top: 1rem;
        }

        .data-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        .data-table th {
            padding: 1rem;
            text-align: left;
            color: var(--text-secondary);
            font-size: 0.875rem;
            font-weight: 600;
            border-bottom: 1px solid var(--border-color);
        }

        body[dir="rtl"] .data-table th {
            text-align: right;
        }

        .data-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-color);
            vertical-align: middle;
        }

        /* Student Info */
        .student-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        body[dir="rtl"] .student-info {
            flex-direction: row-reverse;
        }

        .avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            font-size: 1rem;
            flex-shrink: 0;
        }

        .avatar.male { background: #3b82f6; color: white; }
        .avatar.female { background: #ec4899; color: white; }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: var(--radius-full);
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-paid { background: rgba(16, 185, 129, 0.1); color: var(--success); }
        .status-partial { background: rgba(245, 158, 11, 0.1); color: var(--warning); }
        .status-unpaid { background: rgba(239, 68, 68, 0.1); color: var(--danger); }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-icon.view { color: var(--info); }
        .btn-icon.edit { color: var(--warning); }
        .btn-icon.delete { color: var(--danger); }

        /* Class Grid */
        .class-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .class-card {
            background: var(--bg-card);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            padding: 1rem;
            transition: all 0.3s ease;
        }

        .class-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .class-stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
            margin: 1rem 0;
        }

        .stat-item {
            text-align: center;
            padding: 0.75rem;
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
        }

        .stat-value {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-value.collected { color: var(--success); }
        .stat-value.pending { color: var(--warning); }

        /* Progress Bar */
        .progress-container {
            margin: 1rem 0;
        }

        .progress-bar {
            height: 8px;
            background: var(--bg-tertiary);
            border-radius: var(--radius-full);
            overflow: hidden;
            margin-top: 0.5rem;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-blue-light));
            border-radius: var(--radius-full);
        }

        /* Empty States */
        .empty-state {
            text-align: center;
            padding: 3rem 2rem;
            color: var(--text-tertiary);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .dashboard-header {
                flex-direction: column;
                text-align: center;
            }
            
            .header-right {
                justify-content: center;
            }
            
            .filters-bar {
                grid-template-columns: 1fr;
            }
            
            .summary-grid {
                grid-template-columns: 1fr;
            }
        }

        /* RTL Specific Adjustments */
        body[dir="rtl"] .btn i,
        body[dir="rtl"] .btn-icon i {
            margin-left: 0.5rem;
            margin-right: 0;
        }

        body[dir="rtl"] .student-details {
            margin-right: 1rem;
            margin-left: 0;
        }

        body[dir="rtl"] .action-buttons {
            flex-direction: row-reverse;
        }

        body[dir="rtl"] .class-footer {
            flex-direction: row-reverse;
        }

        /* Pulse Animation */
        .pulse {
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.7); }
            70% { box-shadow: 0 0 0 10px rgba(79, 70, 229, 0); }
            100% { box-shadow: 0 0 0 0 rgba(79, 70, 229, 0); }
        }
    </style>
</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?>">
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="header-left">
                <h1><?= t('dashboard_title') ?></h1>
                <div class="subtitle">
                    <i class="fas fa-chart-line"></i>
                    <?= t('dashboard_subtitle') ?>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="header-right">
                <a href="paid_fees.php" class="btn btn-icon btn-success" title="<?= t('view_paid_fees') ?>">
                    <i class="fas fa-check-circle"></i>
                </a>
                <a href="not_paid.php" class="btn btn-icon" title="<?= t('view_unpaid_fees') ?>">
                    <i class="fas fa-exclamation-circle"></i>
                </a>
                <a href="academic_year.php" class="btn btn-icon" title="<?= t('school_holidays') ?>">
                    <i class="fas fa-calendar-alt"></i>
                </a>
                <a href="student_discount.php" class="btn btn-icon" title="<?= t('student_discounts') ?>">
                    <i class="fas fa-percent"></i>
                </a>
                <a href="fee_category.php" class="btn btn-icon" title="<?= t('fee_categories') ?>">
                    <i class="fas fa-tags"></i>
                </a>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="filters-bar">
            <div class="filter-group">
                <label><?= t('filter_from') ?></label>
                <input type="date" name="from_date" value="<?= htmlspecialchars($filter_from) ?>">
            </div>

            <div class="filter-group">
                <label><?= t('filter_to') ?></label>
                <input type="date" name="to_date" value="<?= htmlspecialchars($filter_to) ?>">
            </div>

            <div class="filter-group">
                <label><?= t('filter_month') ?></label>
                <select name="month">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= $m == $filter_month ? 'selected' : '' ?>>
                            <?= $monthNames[$m] ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="filter-group">
                <label><?= t('filter_year') ?></label>
                <select name="year">
                    <?php for ($y = date('Y') - 5; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= $y == $filter_year ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>

            <div class="filter-group">
                <label><?= t('filter_class') ?></label>
                <select name="class_id">
                    <option value=""><?= t('filter_all_classes') ?></option>
                    <?php
                    $cls = $conn->query("SELECT id, name FROM classes ORDER BY name");
                    while ($c = $cls->fetch_assoc()):
                    ?>
                        <option value="<?= $c['id'] ?>" <?= $filter_class == $c['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($c['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="filter-group">
                <button class="btn btn-primary">
                    <i class="fas fa-filter"></i> <?= t('filter_apply') ?>
                </button>
            </div>
        </form>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card collected">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-money-check-alt"></i>
                    </div>
                    <div class="card-trend <?= $summary['collected_trend'] < 0 ? 'negative' : '' ?>">
                        <i class="fas fa-arrow-<?= $summary['collected_trend'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= abs($summary['collected_trend']) ?>%
                    </div>
                </div>
                <div class="card-value">$<?= number_format($summary['collected'], 2) ?></div>
                <div class="card-label"><?= t('summary_total_collected') ?></div>
                <div class="card-details">
                    <i class="fas fa-info-circle"></i>
                    <?= t('summary_from_students', ['count' => $summary['students_paid']]) ?>
                </div>
            </div>

            <div class="summary-card pending">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="card-trend <?= $summary['pending_trend'] < 0 ? 'negative' : '' ?>">
                        <i class="fas fa-arrow-<?= $summary['pending_trend'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= abs($summary['pending_trend']) ?>%
                    </div>
                </div>
                <div class="card-value">$<?= number_format($summary['pending'], 2) ?></div>
                <div class="card-label"><?= t('summary_total_pending') ?></div>
                <div class="card-details">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= t('summary_needs_followup') ?>
                </div>
            </div>

            <div class="summary-card students">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="card-trend <?= $summary['students_paid_trend'] < 0 ? 'negative' : '' ?>">
                        <i class="fas fa-arrow-<?= $summary['students_paid_trend'] >= 0 ? 'up' : 'down' ?>"></i>
                        <?= abs($summary['students_paid_trend']) ?>%
                    </div>
                </div>
                <div class="card-value"><?= $summary['students_paid'] ?></div>
                <div class="card-label"><?= t('summary_students_paid') ?></div>
                <div class="card-details">
                    <i class="fas fa-check-circle"></i>
                    <?= t('summary_good_collection') ?>
                </div>
            </div>

            <div class="summary-card classes">
                <div class="card-header">
                    <div class="card-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                </div>
                <div class="card-value"><?= $summary['classes'] ?></div>
                <div class="card-label"><?= t('summary_active_classes') ?></div>
                <div class="card-details">
                    <i class="fas fa-book-open"></i>
                    <?= t('summary_with_fee_structure') ?>
                </div>
            </div>
        </div>

        <!-- Chart Section -->
        <div class="chart-section">
            <div class="section-header">
                <div>
                    <div class="section-title"><?= t('chart_title') ?></div>
                    <div class="section-subtitle"><?= t('chart_subtitle') ?></div>
                </div>
            </div>
            <div class="chart-container">
                <canvas id="collectionChart"></canvas>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="main-grid">
            <!-- Recent Payments -->
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <div class="section-title"><?= t('recent_payments_title') ?></div>
                        <div class="section-subtitle"><?= t('recent_payments_subtitle') ?></div>
                    </div>
                    <div class="section-actions">
                        <?php if (!is_member()): ?>
                        <a href="add_fees.php" class="btn btn-primary pulse">
                            <i class="fas fa-plus"></i>
                            <?= t('new_payment') ?>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="table-container">
                    <?php if (empty($recent_payments)): ?>
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-receipt"></i>
                            </div>
                            <h3><?= t('no_payments_found') ?></h3>
                            <p><?= t('no_payments_description') ?></p>
                        </div>
                    <?php else: ?>
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th><?= t('th_student') ?></th>
                                    <th><?= t('th_class') ?></th>
                                    <th><?= t('th_amount_paid') ?></th>
                                    <th><?= t('th_pending') ?></th>
                                    <th><?= t('th_status') ?></th>
                                    <th><?= t('th_date') ?></th>
                                    <th><?= t('th_actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recent_payments as $payment): ?>
                                <tr>
                                    <td style="white-space: nowrap;">
                                        <div class="student-info">
                                            <div class="avatar <?= $payment['gender'] ?>">
                                                <?= strtoupper(substr($payment['student_name'], 0, 1)) ?>
                                            </div>
                                            <div class="student-details">
                                                <div class="name"><?= htmlspecialchars($payment['student_name']) ?></div>
                                                <div class="class"><?= $payment['gender'] ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="class"><?= htmlspecialchars($payment['class_name']) ?></div>
                                        <?php if (!empty($payment['section_name'])): ?>
                                            <strong class="section"><?= htmlspecialchars($payment['section_name']) ?></strong>
                                        <?php endif; ?>
                                    </td>
                                    <td class="amount-cell paid-amount">
                                        $<?= number_format($payment['paid_amount'], 2) ?>
                                    </td>
                                    <td class="amount-cell pending-amount">
                                        $<?= number_format($payment['remaining_amount'], 2) ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?= $payment['status'] ?>">
                                            <i class="fas fa-<?= $payment['status'] === 'paid' ? 'check-circle' : ($payment['status'] === 'partial' ? 'clock' : 'times-circle') ?>"></i>
                                            <?= t('status_' . $payment['status']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?= $payment['payment_date'] ? date('d M Y', strtotime($payment['payment_date'])) : 'N/A' ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <a href="student_finance.php?id=<?= $payment['student_id'] ?>" 
                                               class="btn-icon view" 
                                               title="<?= t('action_view') ?>">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <?php if (!is_member()): ?>
                                            <a href="edit_payment.php?id=<?= $payment['payment_id'] ?>" 
                                               class="btn-icon edit" 
                                               title="<?= t('action_edit') ?>">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <button class="btn-icon delete"
                                                    title="<?= t('action_delete') ?>"
                                                    onclick="deletePayment(this, <?= $payment['payment_id'] ?>, '<?= htmlspecialchars($payment['student_name']) ?>')">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Class Analysis -->
            <div class="section-card">
                <div class="section-header">
                    <div>
                        <div class="section-title"><?= t('class_analysis_title') ?></div>
                        <div class="section-subtitle"><?= t('class_analysis_subtitle') ?></div>
                    </div>
                </div>
                <div class="class-grid">
                    <?php if (empty($class_analysis)): ?>
                        <div class="empty-state" style="grid-column: 1 / -1;">
                            <div class="empty-icon">
                                <i class="fas fa-chalkboard"></i>
                            </div>
                            <h3><?= t('no_class_data') ?></h3>
                            <p><?= t('no_class_data_description') ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($class_analysis as $class): ?>
                            <?php if ($class['total_students'] > 0): ?>
                            <div class="class-card">
                                <div class="class-header">
                                    <div class="class-name"><?= htmlspecialchars($class['class_name']) ?></div>
                                    <?php if (!empty($class['section_name'])): ?>
                                        <span class="class-section"><?= htmlspecialchars($class['section_name']) ?></span>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="class-stats">
                                    <div class="stat-item">
                                        <div class="stat-value"><?= $class['total_students'] ?></div>
                                        <div class="stat-label"><?= t('stat_total_students') ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value"><?= $class['paid_students'] ?></div>
                                        <div class="stat-label"><?= t('stat_paid') ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value collected">
                                            $<?= number_format($class['total_collected'], 2) ?>
                                        </div>
                                        <div class="stat-label"><?= t('stat_collected') ?></div>
                                    </div>
                                    <div class="stat-item">
                                        <div class="stat-value pending">$<?= number_format($class['total_pending'], 0) ?></div>
                                        <div class="stat-label"><?= t('stat_pending') ?></div>
                                    </div>
                                </div>
                                
                                <div class="progress-container">
                                    <div class="progress-label">
                                        <span><?= t('stat_collection_progress') ?></span>
                                        <span><?= $class['total_students'] > 0 ? round(($class['paid_students'] / $class['total_students']) * 100) : 0 ?>%</span>
                                    </div>
                                    <div class="progress-bar">
                                        <div class="progress-fill" style="width: <?= $class['total_students'] > 0 ? ($class['paid_students'] / $class['total_students']) * 100 : 0 ?>%"></div>
                                    </div>
                                </div>
                                
                                <div class="class-footer">
                                    <div class="collection-rate <?= $class['total_students'] > 0 && ($class['paid_students'] / $class['total_students']) >= 0.8 ? 'high' : ($class['paid_students'] / $class['total_students'] >= 0.5 ? 'medium' : 'low') ?>">
                                        <i class="fas fa-<?= $class['total_students'] > 0 && ($class['paid_students'] / $class['total_students']) >= 0.8 ? 'trophy' : ($class['paid_students'] / $class['total_students'] >= 0.5 ? 'chart-line' : 'exclamation-triangle') ?>"></i>
                                        <?= $class['total_students'] > 0 ? round(($class['paid_students'] / $class['total_students']) * 100) : 0 ?>%
                                    </div>
                                    <div>
                                        <?= $class['total_students'] - $class['paid_students'] ?> <?= t('stat_remaining') ?>
                                    </div>
                                    <div style="margin-top: 8px;">
                                        <a href="class_details.php?class_id=<?= $class['class_id'] ?>" class="btn btn-primary btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

     
<script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize Chart
            const ctx = document.getElementById('collectionChart').getContext('2d');
            
            // Get theme colors
            const isDark = document.body.classList.contains('dark');
            const gridColor = isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)';
            const textColor = isDark ? '#cbd5e1' : '#64748b';
            
            // Sample data for the chart
            const collectionChart = new Chart(ctx, {
                type: 'line',
                data: {
            labels: <?= json_encode($chart_labels) ?>,
            datasets: [{
                label: 'Fees Collected',
                data: <?= json_encode($chart_data) ?>,
                borderColor: isDark ? '#34d399' : '#10b981',
                backgroundColor: isDark ? 'rgba(52, 211, 153, 0.1)' : 'rgba(16, 185, 129, 0.1)',
                borderWidth: 3,
                fill: true,
                tension: 0.4,
                pointBackgroundColor: isDark ? '#3aee3405' : '#10b981',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 8
            }]
        }
        ,
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: {
                                    labels: {
                                        color: textColor,
                                        font: {
                                            family: "'Inter', sans-serif"
                                        }
                                    }
                                },
                                tooltip: {
                                    backgroundColor: isDark ? '#1e293b' : '#ffffff',
                                    titleColor: textColor,
                                    bodyColor: textColor,
                                    borderColor: isDark ? '#475569' : '#e2e8f0',
                                    borderWidth: 1,
                                    callbacks: {
                                        label: function(context) {
                                            return `$${context.parsed.y.toLocaleString()}`;
                                        }
                                    }
                                }
                            },
                            scales: {
                                x: {
                                    grid: {
                                        color: gridColor
                                    },
                                    ticks: {
                                        color: textColor
                                    }
                                },
                                y: {
                                    grid: {
                                        color: gridColor
                                    },
                                    ticks: {
                                        color: textColor,
                                        callback: function(value) {
                                            return '$' + value.toLocaleString();
                                        }
                                    }
                                }
                            },
                            interaction: {
                                intersect: false,
                                mode: 'index'
                            }
                        }
                    });

                 

                    // Delete Payment
                window.deletePayment = function(btn, paymentId, studentName) {
    if (!confirm(`Delete payment for ${studentName}? This cannot be undone.`)) return;

    fetch('delete_payment.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: `payment_id=${paymentId}`
    })
    .then(res => {
        if (!res.ok) throw new Error('Delete failed');
        return res.text();
    })
    .then(() => {
        const row = btn.closest('tr');
        row.remove();
        showNotification(`Payment for ${studentName} deleted successfully.`, 'success');
    })
    .catch(err => {
        console.error(err);
        showNotification('Failed to delete payment.', 'error');
    });
    };

                    // Show notification
                    function showNotification(message, type = 'info') {
                        // Remove existing notifications
                        const existing = document.querySelector('.notification');
                        if (existing) existing.remove();
                        
                        const notification = document.createElement('div');
                        notification.className = `notification ${type}`;
                        notification.innerHTML = `
                            <i class="fas fa-${type === 'success' ? 'check-circle' : 'info-circle'}"></i>
                            <span>${message}</span>
                            <button onclick="this.parentElement.remove()">&times;</button>
                        `;
                        
                        // Add styles for notification
                        notification.style.cssText = `
                            position: fixed;
                            top: 20px;
                            right: 20px;
                            padding: 15px 20px;
                            background: ${isDark ? '#1e293b' : '#ffffff'};
                            color: ${isDark ? '#f1f5f9' : '#1e293b'};
                            border-radius: var(--radius-md);
                            box-shadow: var(--shadow-lg);
                            border-left: 4px solid ${type === 'success' ? (isDark ? '#34d399' : '#10b981') : (isDark ? '#3b82f6' : '#3b82f6')};
                            display: flex;
                            align-items: center;
                            gap: 10px;
                            z-index: 1000;
                            animation: slideIn 0.3s ease-out;
                            font-size: 0.875rem;
                        `;
                        
                        document.body.appendChild(notification);
                        
                        // Auto remove after 5 seconds
                        setTimeout(() => {
                            if (notification.parentNode) {
                                notification.style.animation = 'slideOut 0.3s ease-out';
                                setTimeout(() => notification.remove(), 300);
                            }
                        }, 5000);
                    }

                    // Add CSS for slideOut animation
                    const style = document.createElement('style');
                    style.textContent = `
                        @keyframes slideIn {
                            from { transform: translateX(100%); opacity: 0; }
                            to { transform: translateX(0); opacity: 1; }
                        }
                        @keyframes slideOut {
                            from { transform: translateX(0); opacity: 1; }
                            to { transform: translateX(100%); opacity: 0; }
                        }
                    `;
                    document.head.appendChild(style);

                    // Show empty state
                    function showEmptyState() {
                        const tableContainer = document.querySelector('.table-container');
                        tableContainer.innerHTML = `
                            <div class="empty-state">
                                <div class="empty-icon">
                                    <i class="fas fa-receipt"></i>
                                </div>
                                <h3>No Payments Found</h3>
                                <p>No fee payments have been recorded for this month yet.</p>
                            </div>
                        `;
                    }

                    // Add hover effects to cards
                    const cards = document.querySelectorAll('.summary-card, .class-card');
                    cards.forEach(card => {
                        card.addEventListener('mouseenter', function() {
                            this.style.transform = this.classList.contains('summary-card') ? 'translateY(-5px)' : 'translateY(-3px)';
                        });
                        
                        card.addEventListener('mouseleave', function() {
                            this.style.transform = 'translateY(0)';
                        });
                    });

                    // Update collection rate colors based on percentage
                    document.querySelectorAll('.collection-rate').forEach(rateEl => {
                        const text = rateEl.textContent;
                        const percentage = parseInt(text);
                        
                        if (percentage >= 80) {
                            rateEl.className = 'collection-rate high';
                            rateEl.innerHTML = `<i class="fas fa-trophy"></i> ${percentage}% Collect`;
                        } else if (percentage >= 50) {
                            rateEl.className = 'collection-rate medium';
                            rateEl.innerHTML = `<i class="fas fa-chart-line"></i> ${percentage}% Collect`;
                        } else {
                            rateEl.className = 'collection-rate low';
                            rateEl.innerHTML = `<i class="fas fa-exclamation-triangle"></i> ${percentage}% Collect`;
                        }
                    });

                    // Print functionality
                    document.addEventListener('keydown', function(e) {
                        if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                            e.preventDefault();
                            alert('Print functionality would open print dialog in real application.');
                        }
                    });
                });
</script>
</body>
</html>
 