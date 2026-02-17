<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Alumni Profile - {student_name} - Student Management System',
        'no_alumni_selected' => 'No alumni selected. Please go back to the alumni list.',
        'alumni_record_not_found' => 'Alumni record not found. Please go back to the alumni list.',
        'back_to_alumni_list' => 'Back to Alumni List',
        'alumni_id' => 'Alumni ID',
        'graduated' => 'Graduated',
        'age' => 'Age',
        'as_alumni' => 'As Alumni',
        'admitted' => 'Admitted',
        'graduation_type' => 'Graduation Type',
        'personal_info' => 'Personal Information',
        'full_name' => 'Full Name',
        'date_of_birth' => 'Date of Birth',
        'sex' => 'SEX',
        'student_id' => 'Student ID',
        'admission_date' => 'Admission Date',
        'graduation_date' => 'Graduation Date',
        'batch' => 'Batch',
        'address' => 'Address',
        'contact_information' => 'Contact Information',
        'guardian_name' => 'Guardian Name',
        'guardian_phone' => 'Guardian Phone',
        'guardian_email' => 'Guardian Email',
        'performance_summary' => 'Performance Summary',
        'final_exam_score' => 'Final Exam Score',
        'attendance_percentage' => 'Attendance %',
        'alumni_duration' => 'Alumni Duration',
        'academic_history' => 'Academic History',
        'class' => 'Class',
        'academic_year' => 'Academic Year',
        'exam_name' => 'Exam Name',
        'subject' => 'Subject',
        'marks_obtained' => 'Marks Obtained',
        'max_marks' => 'Max Marks',
        'percentage' => 'Percentage',
        'status' => 'Status',
        'passed' => 'Passed',
        'failed' => 'Failed',
        'overall_total' => 'Overall Total',
        'no_academic_records' => 'No academic records found for this alumni.',
        'attendance_records' => 'Attendance Records',
        'month' => 'Month',
        'total_days' => 'Total Days',
        'present_days' => 'Present Days',
        'absent_days' => 'Absent Days',
        'excellent' => 'Excellent',
        'good' => 'Good',
        'fair' => 'Fair',
        'poor' => 'Poor',
        'overall_last_6_months' => 'Overall (Last 6 Months)',
        'no_attendance_records' => 'No attendance records found for this alumni.',
        'financial_records' => 'Financial Records',
        'fee_category' => 'Fee Category',
        'total_amount' => 'Total Amount',
        'paid_amount' => 'Paid Amount',
        'balance' => 'Balance',
        'fully_paid' => 'Fully Paid',
        'partially_paid' => 'Partially Paid',
        'not_paid' => 'Not Paid',
        'no_financial_records' => 'No financial records found for this alumni.',
        'timeline' => 'Timeline',
        'student_admission' => 'Student Admission',
        'graduation_day' => 'Graduation Day',
        'added_to_alumni_list' => 'Added to Alumni List',
        'years_old' => 'years old',
        'exam_performance' => 'Exam Performance',
        'attendance_performance' => 'Attendance Performance',
        'both' => 'Both',
        'was_admitted' => 'was admitted to',
        'successfully_graduated' => 'Successfully graduated based on',
        'final_score' => 'Final Score',
        'attendance' => 'Attendance',
        'officially_registered' => 'Officially registered as an alumni of',
        'paid' => 'Paid',
        'partial' => 'Partial',
        'unpaid' => 'Unpaid'
    ],
    2 => [ // Arabic
        'page_title' => 'ملف الخريج - {student_name} - نظام إدارة الطلاب',
        'no_alumni_selected' => 'لم يتم تحديد خريج. يرجى العودة إلى قائمة الخريجين.',
        'alumni_record_not_found' => 'لم يتم العثور على سجل الخريج. يرجى العودة إلى قائمة الخريجين.',
        'back_to_alumni_list' => 'العودة إلى قائمة الخريجين',
        'alumni_id' => 'رقم الخريج',
        'graduated' => 'تخرج',
        'age' => 'العمر',
        'as_alumni' => 'كخريج',
        'admitted' => 'تم القبول',
        'graduation_type' => 'نوع التخرج',
        'personal_info' => 'المعلومات الشخصية',
        'full_name' => 'الاسم الكامل',
        'date_of_birth' => 'تاريخ الميلاد',
        'sex' => 'الجنس',
        'student_id' => 'رقم الطالب',
        'admission_date' => 'تاريخ القبول',
        'graduation_date' => 'تاريخ التخرج',
        'batch' => 'الدفعة',
        'address' => 'العنوان',
        'contact_information' => 'معلومات الاتصال',
        'guardian_name' => 'اسم ولي الأمر',
        'guardian_phone' => 'هاتف ولي الأمر',
        'guardian_email' => 'بريد ولي الأمر',
        'performance_summary' => 'ملخص الأداء',
        'final_exam_score' => 'نتيجة الامتحان النهائي',
        'attendance_percentage' => 'نسبة الحضور',
        'alumni_duration' => 'مدة الخريج',
        'academic_history' => 'السجل الأكاديمي',
        'class' => 'الصف',
        'academic_year' => 'السنة الأكاديمية',
        'exam_name' => 'اسم الامتحان',
        'subject' => 'المادة',
        'marks_obtained' => 'العلامات المتحصل عليها',
        'max_marks' => 'الحد الأقصى للعلامات',
        'percentage' => 'النسبة المئوية',
        'status' => 'الحالة',
        'passed' => 'ناجح',
        'failed' => 'راسب',
        'overall_total' => 'المجموع الكلي',
        'no_academic_records' => 'لم يتم العثور على سجلات أكاديمية لهذا الخريج.',
        'attendance_records' => 'سجلات الحضور',
        'month' => 'الشهر',
        'total_days' => 'إجمالي الأيام',
        'present_days' => 'أيام الحضور',
        'absent_days' => 'أيام الغياب',
        'excellent' => 'ممتاز',
        'good' => 'جيد',
        'fair' => 'مقبول',
        'poor' => 'ضعيف',
        'overall_last_6_months' => 'المجموع (آخر 6 أشهر)',
        'no_attendance_records' => 'لم يتم العثور على سجلات حضور لهذا الخريج.',
        'financial_records' => 'السجلات المالية',
        'fee_category' => 'فئة الرسوم',
        'total_amount' => 'المبلغ الإجمالي',
        'paid_amount' => 'المبلغ المدفوع',
        'balance' => 'الرصيد',
        'fully_paid' => 'مدفوع بالكامل',
        'partially_paid' => 'مدفوع جزئياً',
        'not_paid' => 'غير مدفوع',
        'no_financial_records' => 'لم يتم العثور على سجلات مالية لهذا الخريج.',
        'timeline' => 'الجدول الزمني',
        'student_admission' => 'قبول الطالب',
        'graduation_day' => 'يوم التخرج',
        'added_to_alumni_list' => 'إضافة إلى قائمة الخريجين',
        'years_old' => 'سنة',
        'exam_performance' => 'أداء الامتحان',
        'attendance_performance' => 'أداء الحضور',
        'both' => 'كلاهما',
        'was_admitted' => 'تم قبوله في',
        'successfully_graduated' => 'تم التخرج بنجاح بناءً على',
        'final_score' => 'النتيجة النهائية',
        'attendance' => 'الحضور',
        'officially_registered' => 'تم تسجيله رسمياً كخريج في',
        'paid' => 'مدفوع',
        'partial' => 'جزئي',
        'unpaid' => 'غير مدفوع'
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

// Helper function to translate graduation reason
function translateReason($reason) {
    switch($reason) {
        case 'exam_performance': return t('exam_performance');
        case 'attendance_performance': return t('attendance_performance');
        case 'both': return t('both');
        default: return ucfirst(str_replace('_', ' ', $reason));
    }
}

// Helper function to translate fee status
function translateFeeStatus($status) {
    switch($status) {
        case 'paid': return t('paid');
        case 'partial': return t('partial');
        case 'unpaid': return t('unpaid');
        default: return ucfirst($status);
    }
}

if (empty($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

// Theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0);

// Get alumni ID from URL
$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if (!$student_id) {
    echo "<h2>" . t('no_alumni_selected') . "</h2>";
    exit;
}

$query = $conn->prepare("
    SELECT 
        a.*,
        s.id as student_id,
        s.name as student_name,
        s.dob,
        s.gender,
        s.address,
        s.guardian_name,
        s.guardian_phone,
        s.guardian_email,
        s.admission_date,
        c.name as class_name,
        c.id as class_id,
        sec.name as section_name,
        sec.id as section_id
    FROM alumni a
    INNER JOIN students s ON a.student_id = s.id
    INNER JOIN classes c ON a.class_id = c.id
    INNER JOIN sections sec ON a.section_id = sec.id
    WHERE a.student_id = ?
");
$query->bind_param("i", $student_id);
$query->execute();
$result = $query->get_result();

if ($result->num_rows === 0) {
    echo "<h2>" . t('alumni_record_not_found') . "</h2>";
    echo "<a href='alumni.php'>" . t('back_to_alumni_list') . "</a>";
    exit;
}

$alumni = $result->fetch_assoc();

// Academic performance history
$academic_query = $conn->prepare("
    SELECT 
        e.name as exam_name,
        e.exam_type,
        e.academic_year,
        er.obtained_marks,
        es.max_marks,
        subj.name as subject_name,
        ROUND((er.obtained_marks / es.max_marks) * 100, 2) as percentage
    FROM exam_results er
    INNER JOIN exams e ON er.exam_id = e.id
    INNER JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
    INNER JOIN subjects subj ON er.subject_id = subj.id
    WHERE er.student_id = ?
      AND er.class_id = ?
    ORDER BY e.academic_year DESC, e.start_date DESC
");
if (!$academic_query) die("SQL Error: " . $conn->error);
$academic_query->bind_param("ii", $alumni['student_id'], $alumni['class_id']);
$academic_query->execute();
$academic_result = $academic_query->get_result();

// Attendance summary
$attendance_query = $conn->prepare("
    SELECT 
        DATE_FORMAT(date, '%Y-%m') as month,
        COUNT(*) as total_days,
        SUM(CASE WHEN status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) as present_days,
        ROUND((SUM(CASE WHEN status IN ('present', 'late', 'excused') THEN 1 ELSE 0 END) / COUNT(*)) * 100, 2) as percentage
    FROM attendance 
    WHERE student_id = ? 
      AND class_id = ?
    GROUP BY DATE_FORMAT(date, '%Y-%m')
    ORDER BY month DESC
    LIMIT 6
");
if (!$attendance_query) die("SQL Error: " . $conn->error);
$attendance_query->bind_param("ii", $alumni['student_id'], $alumni['class_id']);
$attendance_query->execute();
$attendance_result = $attendance_query->get_result();

// Fees info
$total_paid_query = $conn->prepare("
    SELECT 
        IFNULL(SUM(paid_amount), 0) AS total_paid
    FROM student_fees
    WHERE student_id = ?
");

if (!$total_paid_query) {
    die("SQL Error: " . $conn->error);
}

$total_paid_query->bind_param("i", $alumni['student_id']);
$total_paid_query->execute();
$total_paid_result = $total_paid_query->get_result();
$total_paid_row = $total_paid_result->fetch_assoc();

$total_paid = $total_paid_row['total_paid'];


// Calculate age
$age = '';
if ($alumni['dob']) {
    $birth_date = new DateTime($alumni['dob']);
    $today = new DateTime();
    $age = $today->diff($birth_date)->y;
}

// Alumni duration
$alumni_since = new DateTime($alumni['created_at']);
$today = new DateTime();
$alumni_years = $today->diff($alumni_since)->y;
$alumni_months = $today->diff($alumni_since)->m;

// =========================
// FEES DETAILS (ALUMNI VIEW)
// =========================
$fees_query = $conn->prepare("
    SELECT 
        fc.name AS fee_category,
        cf.amount AS original_amount,
        sf.paid_amount,
        sf.discount_amount,
        sf.status,
        cf.month,
        cf.year,
        cf.due_date
    FROM student_fees sf
    INNER JOIN class_fees cf ON sf.class_fee_id = cf.id
    INNER JOIN fee_categories fc ON cf.fee_category_id = fc.id
    WHERE sf.student_id = ?
    ORDER BY cf.year DESC, cf.month DESC
");

if (!$fees_query) {
    die('SQL Error: ' . $conn->error);
}

$fees_query->bind_param("i", $alumni['student_id']);
$fees_query->execute();
$fees_result = $fees_query->get_result();


// =========================
// APP SETTINGS
// =========================
$app_query = $conn->query("SELECT app_name FROM app_settings LIMIT 1");
$app_name = ($app_query && $row = $app_query->fetch_assoc()) ? $row['app_name'] : "Institute";

?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title', ['student_name' => htmlspecialchars($alumni['student_name'])]) ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        body.light {
            background-color: #f5f5f5;
            color: #333;
        }

        body.dark {
            background-color: #1a1a1a;
            color: #fff;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px 15px 0 0;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .profile-info h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .profile-info .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .profile-stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.8;
            letter-spacing: 1px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-outline-light {
            background-color: transparent;
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: white;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .btn-light {
            background-color: white;
            color: #667eea;
            font-weight: 600;
        }

        .btn-light:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-content {
            background-color: #fff;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        body.dark .profile-content {
            background-color: #2d2d2d;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .profile-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
        }

        body.dark .profile-card {
            background: linear-gradient(135deg, #3d3d3d 0%, #555555 100%);
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
            border: 5px solid white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        body.dark .avatar {
            border-color: #2d2d2d;
        }
        body.dark .team{
            color: Black;
        }

        .student-id {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        body.dark .student-id {
            color: #aaa;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0;
        }

        .sidebar-nav {
            background-color: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
        }

        body.dark .sidebar-nav {
            background-color: #3d3d3d;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #495057;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        body.dark .nav-item {
            color: #ddd;
        }

        .nav-item:hover {
            background-color: #e9ecef;
            border-left-color: #667eea;
            color: #667eea;
        }

        body.dark .nav-item:hover {
            background-color: #4d4d4d;
        }

        .nav-item.active {
            background-color: #e9ecef;
            border-left-color: #667eea;
            color: #667eea;
            font-weight: 600;
        }

        body.dark .nav-item.active {
            background-color: #4d4d4d;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .section {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        body.dark .section {
            background-color: #3d3d3d;
        }

        .section-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        body.dark .section-header {
            background-color: #4d4d4d;
            border-bottom-color: #555;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        body.dark .section-title {
            color: #ddd;
        }

        .section-body {
            padding: 25px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        body.dark .info-item {
            background-color: #4d4d4d;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        body.dark .info-label {
            color: #aaa;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #495057;
        }

        body.dark .info-value {
            color: #ddd;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        body.dark .table th {
            background-color: #4d4d4d;
            color: #ddd;
            border-bottom-color: #555;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        body.dark .table td {
            border-bottom-color: #555;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        body.dark .table tbody tr:hover {
            background-color: #4d4d4d;
        }

        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        body.dark .progress-bar {
            background-color: #555;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43e97b, #38f9d7);
            border-radius: 4px;
        }

        .chart-container {
            height: 200px;
            position: relative;
            margin: 20px 0;
        }

        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }

        body.dark .timeline::before {
            background-color: #555;
        }

        .timeline-item {
            position: relative;
            padding: 15px 0;
            padding-left: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 20px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #667eea;
            border: 3px solid white;
        }

        body.dark .timeline-item::before {
            border-color: #2d2d2d;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        body.dark .timeline-date {
            color: #aaa;
        }

        .timeline-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        body.dark .timeline-content {
            background-color: #4d4d4d;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        body.dark .contact-item {
            background-color: #4d4d4d;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .contact-details h4 {
            font-size: 14px;
            margin-bottom: 2px;
            color: #495057;
        }

        body.dark .contact-details h4 {
            color: #ddd;
        }

        .contact-details p {
            font-size: 14px;
            color: #6c757d;
        }

        body.dark .contact-details p {
            color: #aaa;
        }

        @media (max-width: 1024px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .profile-stats {
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .header-actions {
                flex-wrap: wrap;
            }
            
            .profile-info h1 {
                font-size: 28px;
            }
        }

        .print-only {
            display: none;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?= $is_dark ? 'dark' : 'light' ?>">
    <div class="container">
        <!-- Profile Header -->
        <div class="profile-header">
            <div class="header-content">
                <div class="profile-info">
                    <h1><?= htmlspecialchars($alumni['student_name']) ?></h1>
                    <div class="subtitle">
                        <?= t('alumni_id') ?>: #<?= $alumni['student_id'] ?> | 
                        <?= htmlspecialchars($alumni['section_name']) ?> / <?= htmlspecialchars($alumni['class_name']) ?> |
                        <?= t('graduated') ?>: <?= date('F j, Y', strtotime($alumni['graduation_date'])) ?>
                    </div>
                    <div class="profile-stats">
                        <div class="stat-item">
                            <div class="stat-value"><?= $age ?: '--' ?></div>
                            <div class="stat-label"><?= t('age') ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= $alumni_years ?>y <?= $alumni_months ?>m</div>
                            <div class="stat-label"><?= t('as_alumni') ?></div>
                        </div>
                        <div class="stat-item">
                            <div class="stat-value"><?= date('M Y', strtotime($alumni['admission_date'])) ?></div>
                            <div class="stat-label"><?= t('admitted') ?></div>
                        </div>
                        <div class="stat-item">
                            <?php
                            $reason_badge = '';
                            switch($alumni['graduation_reason']) {
                                case 'exam_performance': $reason_badge = 'badge-success'; break;
                                case 'attendance_performance': $reason_badge = 'badge-warning'; break;
                                case 'both': $reason_badge = 'badge-info'; break;
                            }
                            ?>
                            <div class="stat-value"><span class="badge <?= $reason_badge ?>" style="font-size: 14px;">
                                <?= translateReason($alumni['graduation_reason']) ?>
                            </span></div>
                            <div class="stat-label"><?= t('graduation_type') ?></div>
                        </div>
                    </div>
                </div>
               
            </div>
        </div>

        <!-- Main Content -->
        <div class="profile-content">
            <!-- Sidebar -->
            <div class="sidebar">
                <div class="profile-card">
                    <div class="avatar">
                        <?= strtoupper(substr(explode(' ', $alumni['student_name'])[0], 0, 1)) ?>
                    </div>
                    <div class="student-id">ID: #<?= $alumni['student_id'] ?></div>
                    <div class="status-badge"><?= t('alumni_id') ?></div>
                    <h3><?= htmlspecialchars($alumni['student_name']) ?></h3>
                    <p><?= htmlspecialchars($alumni['section_name']) ?> / <?= htmlspecialchars($alumni['class_name']) ?></p>
                </div>

                <div class="sidebar-nav">
                    <a href="#personal" class="nav-item active" onclick="showSection('personal')">
                        <i class="fas fa-user"></i> <?= t('personal_info') ?>
                    </a>
                    <a href="#academic" class="nav-item" onclick="showSection('academic')">
                        <i class="fas fa-graduation-cap"></i> <?= t('academic_history') ?>
                    </a>
                    <a href="#attendance" class="nav-item" onclick="showSection('attendance')">
                        <i class="fas fa-calendar-check"></i> <?= t('attendance_records') ?>
                    </a>
                    <a href="#financial" class="nav-item" onclick="showSection('financial')">
                        <i class="fas fa-money-bill-wave"></i> <?= t('financial_records') ?>
                    </a>
                    <a href="#timeline" class="nav-item" onclick="showSection('timeline')">
                        <i class="fas fa-history"></i> <?= t('timeline') ?>
                    </a>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="main-content">
                <!-- Personal Information Section -->
                <div id="personal" class="section">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-user"></i> <?= t('personal_info') ?></h3>
                    </div>
                    <div class="section-body">
                        <div class="info-grid">
                            <div class="info-item">
                                <div class="info-label"><?= t('full_name') ?></div>
                                <div class="info-value"><?= htmlspecialchars($alumni['student_name']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= t('date_of_birth') ?></div>
                                <div class="info-value"><?= $alumni['dob'] ?> (<?= $age ?> <?= t('years_old') ?>)</div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= t('sex') ?></div>
                                <div class="info-value"><?= ucfirst($alumni['gender']) ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= t('student_id') ?></div>
                                <div class="info-value">#<?= $alumni['student_id'] ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= t('admission_date') ?></div>
                                <div class="info-value"><?= $alumni['admission_date'] ?></div>
                            </div>
                            <div class="info-item">
                                <div class="info-label"><?= t('graduation_date') ?></div>
                                <div class="info-value">
                                    <?= date('F j, Y', strtotime($alumni['graduation_date'])) ?>
                                    <span class="badge badge-success"><?= date('Y', strtotime($alumni['graduation_date'])) ?> <?= t('batch') ?></span>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                            <h4 style="margin-bottom: 15px;"><i class="fas fa-map-marker-alt"></i> <?= t('address') ?></h4>
                            <div class="info-item">
                                <div class="info-value"><?= htmlspecialchars($alumni['address']) ?></div>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                            <h4 style="margin-bottom: 15px;"><i class="fas fa-users"></i> <?= t('contact_information') ?></h4>
                            <div class="contact-info">
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="contact-details">
                                        <h4><?= t('guardian_name') ?></h4>
                                        <p><?= htmlspecialchars($alumni['guardian_name']) ?></p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <div class="contact-details">
                                        <h4><?= t('guardian_phone') ?></h4>
                                        <p><?= htmlspecialchars($alumni['guardian_phone']) ?></p>
                                    </div>
                                </div>
                                <div class="contact-item">
                                    <div class="contact-icon">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <div class="contact-details">
                                        <h4><?= t('guardian_email') ?></h4>
                                        <p><?= htmlspecialchars($alumni['guardian_email']) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div style="margin-top: 30px;">
                            <h4 style="margin-bottom: 15px;"><i class="fas fa-chart-line"></i> <?= t('performance_summary') ?></h4>
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                                <div class="metric-card">
                                    <div class="metric-value"><?= $alumni['exam_score'] ? number_format($alumni['exam_score'], 2) : '--' ?></div>
                                    <div class="metric-label"><?= t('final_exam_score') ?></div>
                                </div>
                                <div class="metric-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                                    <div class="metric-value"><?= $alumni['attendance_percentage'] ? number_format($alumni['attendance_percentage'], 2) . '%' : '--' ?></div>
                                    <div class="metric-label"><?= t('attendance_percentage') ?></div>
                                </div>
                                <div class="metric-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                                    <div class="metric-value"><?= $alumni_years ?>y <?= $alumni_months ?>m</div>
                                    <div class="metric-label"><?= t('alumni_duration') ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Academic History Section -->
                <div id="academic" class="section" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-graduation-cap"></i> <?= t('academic_history') ?></h3>
                        <div>
                            <span class="badge badge-primary"><?= t('class') ?>: <?= htmlspecialchars($alumni['class_name']) ?></span>
                        </div>
                    </div>
                    <div class="section-body">
                        <?php if ($academic_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= t('academic_year') ?></th>
                                            <th><?= t('exam_name') ?></th>
                                            <th><?= t('subject') ?></th>
                                            <th><?= t('marks_obtained') ?></th>
                                            <th><?= t('max_marks') ?></th>
                                            <th><?= t('percentage') ?></th>
                                            <th><?= t('status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_obtained = 0;
                                        $total_max = 0;
                                        while ($row = $academic_result->fetch_assoc()): 
                                            $total_obtained += $row['obtained_marks'];
                                            $total_max += $row['max_marks'];
                                            $passed = $row['percentage'] >= 50;
                                        ?>
                                            <tr>
                                                <td><?= $row['academic_year'] ?></td>
                                                <td><?= htmlspecialchars($row['exam_name']) ?> (<?= $row['exam_type'] ?>)</td>
                                                <td><?= htmlspecialchars($row['subject_name']) ?></td>
                                                <td><?= number_format($row['obtained_marks'], 2) ?></td>
                                                <td><?= number_format($row['max_marks'], 2) ?></td>
                                                <td><?= number_format($row['percentage'], 2) ?>%</td>
                                                <td>
                                                    <span class="badge <?= $passed ? 'badge-success' : 'badge-danger' ?>">
                                                        <?= $passed ? t('passed') : t('failed') ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                                            <td class="team" colspan="3"><?= t('overall_total') ?></td>
                                            <td class="team"><?= number_format($total_obtained, 2) ?></td>
                                            <td class="team"><?= number_format($total_max, 2) ?></td>
                                            <td class="team"><?= number_format(($total_obtained / $total_max) * 100, 2) ?>%</td>
                                            <td>
                                                <?php $overall_percentage = ($total_obtained / $total_max) * 100; ?>
                                                <span class="badge <?= $overall_percentage >= 50 ? 'badge-success' : 'badge-danger' ?>">
                                                    <?= $overall_percentage >= 50 ? t('passed') : t('failed') ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #6c757d;">
                                <i class="fas fa-book fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                                <p><?= t('no_academic_records') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Attendance Section -->
                <div id="attendance" class="section" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-calendar-check"></i> <?= t('attendance_records') ?></h3>
                    </div>
                    <div class="section-body">
                        <?php if ($attendance_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= t('month') ?></th>
                                            <th><?= t('total_days') ?></th>
                                            <th><?= t('present_days') ?></th>
                                            <th><?= t('absent_days') ?></th>
                                            <th><?= t('percentage') ?></th>
                                            <th><?= t('status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $overall_total = 0;
                                        $overall_present = 0;
                                        while ($row = $attendance_result->fetch_assoc()): 
                                            $absent = $row['total_days'] - $row['present_days'];
                                            $overall_total += $row['total_days'];
                                            $overall_present += $row['present_days'];
                                        ?>
                                            <tr>
                                                <td><?= date('F Y', strtotime($row['month'] . '-01')) ?></td>
                                                <td><?= $row['total_days'] ?></td>
                                                <td><?= $row['present_days'] ?></td>
                                                <td><?= $absent ?></td>
                                                <td>
                                                    <div style="display: flex; align-items: center; gap: 10px;">
                                                        <span><?= number_format($row['percentage'], 2) ?>%</span>
                                                        <div class="progress-bar" style="flex: 1;">
                                                            <div class="progress-fill" style="width: <?= $row['percentage'] ?>%;"></div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php
                                                    $attendance_badge = '';
                                                    if ($row['percentage'] >= 90) $attendance_badge = 'badge-success';
                                                    elseif ($row['percentage'] >= 75) $attendance_badge = 'badge-warning';
                                                    else $attendance_badge = 'badge-danger';
                                                    ?>
                                                    <span class="badge <?= $attendance_badge ?>">
                                                        <?php
                                                        if ($row['percentage'] >= 90) echo t('excellent');
                                                        elseif ($row['percentage'] >= 75) echo t('good');
                                                        elseif ($row['percentage'] >= 60) echo t('fair');
                                                        else echo t('poor');
                                                        ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                                            <td class="team"><?= t('overall_last_6_months') ?></td>
                                            <td class="team"><?= $overall_total ?></td>
                                            <td class="team"><?= $overall_present ?></td>
                                            <td class="team"><?= $overall_total - $overall_present ?></td>
                                            <td class="team"><?= $overall_total > 0 ? number_format(($overall_present / $overall_total) * 100, 2) : 0 ?>%</td>
                                            <td>
                                                <?php
                                                $overall_percentage = $overall_total > 0 ? ($overall_present / $overall_total) * 100 : 0;
                                                $overall_badge = '';
                                                if ($overall_percentage >= 90) $overall_badge = 'badge-success';
                                                elseif ($overall_percentage >= 75) $overall_badge = 'badge-warning';
                                                else $overall_badge = 'badge-danger';
                                                ?>
                                                <span class="badge <?= $overall_badge ?>">
                                                    <?php
                                                    if ($overall_percentage >= 90) echo t('excellent');
                                                    elseif ($overall_percentage >= 75) echo t('good');
                                                    elseif ($overall_percentage >= 60) echo t('fair');
                                                    else echo t('poor');
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #6c757d;">
                                <i class="fas fa-calendar fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                                <p><?= t('no_attendance_records') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Financial Records Section -->
                <div id="financial" class="section" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-money-bill-wave"></i> <?= t('financial_records') ?></h3>
                    </div>
                    <div class="section-body">
                        <?php if ($fees_result->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th><?= t('fee_category') ?></th>
                                            <th><?= t('total_amount') ?></th>
                                            <th><?= t('paid_amount') ?></th>
                                            <th><?= t('balance') ?></th>
                                            <th><?= t('status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $total_fees = 0;
                                        $total_paid = 0;

                                        while ($row = $fees_result->fetch_assoc()):
                                            $amount = $row['original_amount'] - $row['discount_amount'];
                                            $paid = $row['paid_amount'];
                                            $balance = $amount - $paid;

                                            $total_fees += $amount;
                                            $total_paid += $paid;

                                            $paid_percentage = $amount > 0 ? ($paid / $amount) * 100 : 100;
                                        ?>
                                            <tr>
                                                <td><?= htmlspecialchars($row['fee_category']) ?></td>
                                                <td><?= number_format($amount, 2) ?></td>
                                                <td><?= number_format($paid, 2) ?></td>
                                                <td><?= number_format($balance, 2) ?></td>
                                                <td>
                                                    <span class="badge 
                                                        <?= $row['status'] === 'paid' ? 'badge-success' :
                                                            ($row['status'] === 'partial' ? 'badge-warning' : 'badge-danger') ?>">
                                                        <?= translateFeeStatus($row['status']) ?>
                                                    </span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                    <tfoot>
                                        <tr style="background-color: #f8f9fa; font-weight: bold;">
                                            <td class="team"><?= t('overall_total') ?></td>
                                            <td class="team"><?= number_format($total_fees, 2) ?></td>
                                            <td class="team"><?= number_format($total_paid, 2) ?></td>
                                            <td class="team"><?= number_format($total_fees - $total_paid, 2) ?></td>
                                            <td>
                                                <?php 
                                                $total_percentage = $total_fees > 0 ? ($total_paid / $total_fees) * 100 : 100;
                                                $status_color = $total_percentage == 100 ? 'badge-success' : 
                                                              ($total_percentage >= 50 ? 'badge-warning' : 'badge-danger');
                                                ?>
                                                <span class="badge <?= $status_color ?>">
                                                    <?php
                                                    if ($total_percentage == 100) echo t('fully_paid');
                                                    elseif ($total_percentage == 0) echo t('not_paid');
                                                    else echo t('partially_paid');
                                                    ?>
                                                </span>
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        <?php else: ?>
                            <div style="text-align: center; padding: 40px; color: #6c757d;">
                                <i class="fas fa-money-bill fa-3x" style="margin-bottom: 15px; opacity: 0.5;"></i>
                                <p><?= t('no_financial_records') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Timeline Section -->
                <div id="timeline" class="section" style="display: none;">
                    <div class="section-header">
                        <h3 class="section-title"><i class="fas fa-history"></i> <?= t('timeline') ?></h3>
                    </div>
                    <div class="section-body">
                        <div class="timeline">
                            <div class="timeline-item">
                                <div class="timeline-date"><?= date('F j, Y', strtotime($alumni['admission_date'])) ?></div>
                                <div class="timeline-content">
                                    <h5><i class="fas fa-user-plus text-primary"></i> <?= t('student_admission') ?></h5>
                                    <p><?= htmlspecialchars($alumni['student_name']) ?> <?= t('was_admitted') ?> <?= htmlspecialchars($alumni['section_name']) ?> / <?= htmlspecialchars($alumni['class_name']) ?>.</p>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-date"><?= date('F j, Y', strtotime($alumni['graduation_date'])) ?></div>
                                <div class="timeline-content">
                                    <h5><i class="fas fa-graduation-cap text-success"></i> <?= t('graduation_day') ?></h5>
                                    <p><?= t('successfully_graduated') ?> <?= str_replace('_', ' ', $alumni['graduation_reason']) ?>.</p>
                                    <?php if ($alumni['exam_score']): ?>
                                        <p><strong><?= t('final_score') ?>:</strong> <?= number_format($alumni['exam_score'], 2) ?></p>
                                    <?php endif; ?>
                                    <?php if ($alumni['attendance_percentage']): ?>
                                        <p><strong><?= t('attendance') ?>:</strong> <?= number_format($alumni['attendance_percentage'], 2) ?>%</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                            
                            <div class="timeline-item">
                                <div class="timeline-date"><?= date('F j, Y, g:i a', strtotime($alumni['created_at'])) ?></div>
                                <div class="timeline-content">
                                    <h5><i class="fas fa-user-graduate text-info"></i> <?= t('added_to_alumni_list') ?></h5>
                                    <p><?= t('officially_registered') ?> <?= htmlspecialchars($app_name) ?>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show selected section
            document.getElementById(sectionId).style.display = 'block';
            
            // Update active nav item
            document.querySelectorAll('.nav-item').forEach(item => {
                item.classList.remove('active');
            });
            event.target.classList.add('active');
        }

        // Initialize with personal section
        document.addEventListener('DOMContentLoaded', function() {
            showSection('personal');
        });

        // Print functionality
        window.onbeforeprint = function() {
            // Show all sections for printing
            document.querySelectorAll('.section').forEach(section => {
                section.style.display = 'block';
            });
        };

        window.onafterprint = function() {
            // Restore to personal section after printing
            showSection('personal');
        };
    </script>
</body>
</html>

<style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        body.light {
            background-color: #f5f5f5;
            color: #333;
        }

        body.dark {
            background-color: #1a1a1a;
            color: #fff;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .profile-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px 30px;
            border-radius: 15px 15px 0 0;
            position: relative;
            overflow: hidden;
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 1px, transparent 1px);
            background-size: 20px 20px;
            opacity: 0.3;
        }

        .header-content {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            position: relative;
            z-index: 1;
        }

        .profile-info h1 {
            font-size: 36px;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .profile-info .subtitle {
            font-size: 18px;
            opacity: 0.9;
            margin-bottom: 20px;
        }

        .profile-stats {
            display: flex;
            gap: 30px;
            margin-top: 20px;
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 12px;
            text-transform: uppercase;
            opacity: 0.8;
            letter-spacing: 1px;
        }

        .header-actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-outline-light {
            background-color: transparent;
            border: 2px solid rgba(255, 255, 255, 0.8);
            color: white;
        }

        .btn-outline-light:hover {
            background-color: rgba(255, 255, 255, 0.1);
            transform: translateY(-2px);
        }

        .btn-light {
            background-color: white;
            color: #667eea;
            font-weight: 600;
        }

        .btn-light:hover {
            background-color: #f8f9fa;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }

        .profile-content {
            background-color: #fff;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 0 30px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: grid;
            grid-template-columns: 300px 1fr;
            gap: 30px;
        }

        body.dark .profile-content {
            background-color: #2d2d2d;
        }

        .sidebar {
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .profile-card {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin-bottom: 20px;
        }

        body.dark .profile-card {
            background: linear-gradient(135deg, #3d3d3d 0%, #555555 100%);
        }

        .avatar {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            margin: 0 auto 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
            color: white;
            font-weight: bold;
            border: 5px solid white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        body.dark .avatar {
            border-color: #2d2d2d;
        }
        body.dark .team{
            color: Black;
        }

        .student-id {
            font-size: 14px;
            color: #666;
            margin-bottom: 5px;
        }

        body.dark .student-id {
            color: #aaa;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 16px;
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            color: white;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin: 15px 0;
        }

        .sidebar-nav {
            background-color: #f8f9fa;
            border-radius: 12px;
            overflow: hidden;
        }

        body.dark .sidebar-nav {
            background-color: #3d3d3d;
        }

        .nav-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 20px;
            color: #495057;
            text-decoration: none;
            border-left: 4px solid transparent;
            transition: all 0.3s;
        }

        body.dark .nav-item {
            color: #ddd;
        }

        .nav-item:hover {
            background-color: #e9ecef;
            border-left-color: #667eea;
            color: #667eea;
        }

        body.dark .nav-item:hover {
            background-color: #4d4d4d;
        }

        .nav-item.active {
            background-color: #e9ecef;
            border-left-color: #667eea;
            color: #667eea;
            font-weight: 600;
        }

        body.dark .nav-item.active {
            background-color: #4d4d4d;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        .section {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            overflow: hidden;
        }

        body.dark .section {
            background-color: #3d3d3d;
        }

        .section-header {
            background-color: #f8f9fa;
            padding: 20px;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        body.dark .section-header {
            background-color: #4d4d4d;
            border-bottom-color: #555;
        }

        .section-title {
            font-size: 18px;
            font-weight: 600;
            color: #495057;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        body.dark .section-title {
            color: #ddd;
        }

        .section-body {
            padding: 25px;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .info-item {
            padding: 15px;
            background-color: #f8f9fa;
            border-radius: 8px;
            border-left: 4px solid #667eea;
        }

        body.dark .info-item {
            background-color: #4d4d4d;
        }

        .info-label {
            font-size: 12px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 5px;
            letter-spacing: 0.5px;
        }

        body.dark .info-label {
            color: #aaa;
        }

        .info-value {
            font-size: 16px;
            font-weight: 500;
            color: #495057;
        }

        body.dark .info-value {
            color: #ddd;
        }

        .table-responsive {
            overflow-x: auto;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th {
            background-color: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: 600;
            color: #495057;
            border-bottom: 2px solid #dee2e6;
        }

        body.dark .table th {
            background-color: #4d4d4d;
            color: #ddd;
            border-bottom-color: #555;
        }

        .table td {
            padding: 12px;
            border-bottom: 1px solid #dee2e6;
        }

        body.dark .table td {
            border-bottom-color: #555;
        }

        .table tbody tr:hover {
            background-color: #f8f9fa;
        }

        body.dark .table tbody tr:hover {
            background-color: #4d4d4d;
        }

        .progress-bar {
            height: 8px;
            background-color: #e9ecef;
            border-radius: 4px;
            overflow: hidden;
        }

        body.dark .progress-bar {
            background-color: #555;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #43e97b, #38f9d7);
            border-radius: 4px;
        }

        .chart-container {
            height: 200px;
            position: relative;
            margin: 20px 0;
        }

        .metric-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
        }

        .metric-value {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .metric-label {
            font-size: 14px;
            opacity: 0.9;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .badge {
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-success { background-color: #28a745; color: white; }
        .badge-warning { background-color: #ffc107; color: #212529; }
        .badge-info { background-color: #17a2b8; color: white; }
        .badge-primary { background-color: #007bff; color: white; }
        .badge-danger { background-color: #dc3545; color: white; }

        .timeline {
            position: relative;
            padding-left: 30px;
        }

        .timeline::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 2px;
            background-color: #dee2e6;
        }

        body.dark .timeline::before {
            background-color: #555;
        }

        .timeline-item {
            position: relative;
            padding: 15px 0;
            padding-left: 20px;
        }

        .timeline-item::before {
            content: '';
            position: absolute;
            left: -26px;
            top: 20px;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            background-color: #667eea;
            border: 3px solid white;
        }

        body.dark .timeline-item::before {
            border-color: #2d2d2d;
        }

        .timeline-date {
            font-size: 12px;
            color: #6c757d;
            margin-bottom: 5px;
        }

        body.dark .timeline-date {
            color: #aaa;
        }

        .timeline-content {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
        }

        body.dark .timeline-content {
            background-color: #4d4d4d;
        }

        .contact-info {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px;
            background-color: #f8f9fa;
            border-radius: 8px;
        }

        body.dark .contact-item {
            background-color: #4d4d4d;
        }

        .contact-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        .contact-details h4 {
            font-size: 14px;
            margin-bottom: 2px;
            color: #495057;
        }

        body.dark .contact-details h4 {
            color: #ddd;
        }

        .contact-details p {
            font-size: 14px;
            color: #6c757d;
        }

        body.dark .contact-details p {
            color: #aaa;
        }

        @media (max-width: 1024px) {
            .profile-content {
                grid-template-columns: 1fr;
            }
            
            .sidebar {
                position: static;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 20px;
            }
            
            .profile-stats {
                flex-wrap: wrap;
                gap: 15px;
            }
            
            .header-actions {
                flex-wrap: wrap;
            }
            
            .profile-info h1 {
                font-size: 28px;
            }
        }

        .print-only {
            display: none;
        }
    </style>