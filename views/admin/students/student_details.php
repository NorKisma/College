<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Student Details',
        'header_back' => 'Back to Dashboard',
        'btn_print' => 'Print Profile',
        'invalid_id' => 'Invalid Student ID.',
        'student_not_found' => 'Student not found.',
        
        // Cards
        'card_personal_info' => 'Personal Information',
        'card_academic_info' => 'Academic Information',
        'card_guardian_info' => 'Guardian / Parent',
        'card_subjects_teachers' => 'Subjects & Teachers',
        'card_fee_summary' => 'Fee Summary',
        'card_exam_performance' => 'Exam Performance Overview',
        'card_attendance' => 'Attendance Overview',
        'card_communication' => 'Communication Log',
        
        // Labels
        'label_date_of_birth' => 'Date of Birth',
        'label_gender' => 'Gender',
        'label_admission_date' => 'Admission Date',
        'label_address' => 'Address',
        'label_current_class' => 'Current Class',
        'label_section' => 'Section',
        'label_academic_year' => 'Academic Year',
        'label_enrollment_date' => 'Enrollment Date',
        'label_not_enrolled' => 'Not enrolled in any class.',
        'label_parent_name' => 'Name',
        'label_parent_phone' => 'Phone',
        'label_parent_email' => 'Email',
        'label_occupation' => 'Occupation',
        'label_guardian_name' => 'Guardian Name',
        'label_guardian_phone' => 'Guardian Phone',
        'label_not_linked' => '* Not linked to Parents table account',
        
        // Table Headers
        'table_subject' => 'Subject',
        'table_teacher' => 'Teacher',
        'table_day' => 'Day',
        'table_time' => 'Time',
        'table_no_subjects' => 'No subjects assigned.',
        'table_type' => 'Type',
        'table_due' => 'Due',
        'table_amount' => 'Amount',
        'table_no_fees' => 'No fees assigned.',
        
        // Statistics
        'stat_total_fees' => 'Total Fees',
        'stat_discount' => 'Discount',
        'stat_paid' => 'Paid',
        'stat_fee_breakdown' => 'Fee Breakdown',
        'stat_exam_passed' => 'Passed',
        'stat_exam_failed' => 'Failed',
        'stat_exam_average' => 'Average',
        'stat_total_exams' => 'Total Exams',
        'stat_present' => 'Present',
        'stat_absent' => 'Absent',
        'stat_late' => 'Late',
        'stat_excused' => 'Excused',
        'stat_total_records' => 'Total Records',
        
        // Messages
        'no_messages' => 'No messages found.',
        
        // Status
        'gender_male' => 'Male',
        'gender_female' => 'Female'
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'تفاصيل الطالب',
        'header_back' => 'العودة للوحة التحكم',
        'btn_print' => 'طباعة الملف',
        'invalid_id' => 'معرف الطالب غير صالح.',
        'student_not_found' => 'الطالب غير موجود.',
        
        // Cards
        'card_personal_info' => 'المعلومات الشخصية',
        'card_academic_info' => 'المعلومات الأكاديمية',
        'card_guardian_info' => 'ولي الأمر / الوالد',
        'card_subjects_teachers' => 'المواد والمعلمين',
        'card_fee_summary' => 'ملخص الرسوم',
        'card_exam_performance' => 'نظرة عامة على أداء الامتحانات',
        'card_attendance' => 'نظرة عامة على الحضور',
        'card_communication' => 'سجل التواصل',
        
        // Labels
        'label_date_of_birth' => 'تاريخ الميلاد',
        'label_gender' => 'الجنس',
        'label_admission_date' => 'تاريخ التسجيل',
        'label_address' => 'العنوان',
        'label_current_class' => 'الصف الحالي',
        'label_section' => 'القسم',
        'label_academic_year' => 'السنة الدراسية',
        'label_enrollment_date' => 'تاريخ الالتحاق',
        'label_not_enrolled' => 'غير مسجل في أي صف.',
        'label_parent_name' => 'الاسم',
        'label_parent_phone' => 'الهاتف',
        'label_parent_email' => 'البريد الإلكتروني',
        'label_occupation' => 'المهنة',
        'label_guardian_name' => 'اسم ولي الأمر',
        'label_guardian_phone' => 'هاتف ولي الأمر',
        'label_not_linked' => '* غير مرتبط بحساب جدول أولياء الأمور',
        
        // Table Headers
        'table_subject' => 'المادة',
        'table_teacher' => 'المعلم',
        'table_day' => 'اليوم',
        'table_time' => 'الوقت',
        'table_no_subjects' => 'لا توجد مواد معينة.',
        'table_type' => 'النوع',
        'table_due' => 'موعد الاستحقاق',
        'table_amount' => 'المبلغ',
        'table_no_fees' => 'لا توجد رسوم معينة.',
        
        // Statistics
        'stat_total_fees' => 'إجمالي الرسوم',
        'stat_discount' => 'الخصم',
        'stat_paid' => 'المدفوع',
        'stat_fee_breakdown' => 'تفصيل الرسوم',
        'stat_exam_passed' => 'ناجح',
        'stat_exam_failed' => 'راسب',
        'stat_exam_average' => 'المتوسط',
        'stat_total_exams' => 'إجمالي الامتحانات',
        'stat_present' => 'حاضر',
        'stat_absent' => 'غائب',
        'stat_late' => 'متأخر',
        'stat_excused' => 'معذور',
        'stat_total_records' => 'إجمالي السجلات',
        
        // Messages
        'no_messages' => 'لا توجد رسائل.',
        
        // Status
        'gender_male' => 'ذكر',
        'gender_female' => 'أنثى'
    ]
];

// Translation helper function
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

// theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;

// 1 = light, 0 = dark
$is_dark = ($theme === 0);

$student_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($student_id === 0) {
    die(t('invalid_id'));
}

/* ================= THEME ================= */
 

/* ================= 1. BASIC STUDENT INFO ================= */
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$student = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$student) {
    die(t('student_not_found'));
}

/* ================= 2. PARENT INFO ================= */
// Linked via phone number as per schema
$parent = null;
if (!empty($student['guardian_phone'])) {
    $stmt = $conn->prepare("SELECT * FROM parents WHERE phone = ? LIMIT 1");
    $stmt->bind_param("s", $student['guardian_phone']);
    $stmt->execute();
    $parent = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

/* ================= 3. CLASS & SECTION INFO ================= */
// Get latest academic year class assignment
$class_info = null;
$stmt = $conn->prepare("
    SELECT cs.*, c.name as class_name, s.name as section_name, c.section_id 
    FROM class_students cs 
    JOIN classes c ON cs.class_id = c.id 
    LEFT JOIN sections s ON c.section_id = s.id 
    WHERE cs.student_id = ? 
    ORDER BY cs.id DESC LIMIT 1
");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$class_info = $stmt->get_result()->fetch_assoc();
$stmt->close();

$current_class_id = $class_info['class_id'] ?? 0;
$current_section_id = $class_info['section_id'] ?? 0;

// Always define joined_date
if ($class_info && !empty($class_info['created_at'])) {
    // From class assignment
    $joined_date = new DateTime($class_info['created_at']);
} else {
    // Fallback: use student admission date
    $joined_date = new DateTime($student['admission_date'] ?? date('Y-m-d'));
}

$joined_date_str = $joined_date->format('Y-m-d');
$today = new DateTime();

// Calculate months since joining
$diff = $joined_date->diff($today);
$months = ($diff->y * 12) + $diff->m;

// Count current month if joined mid-month
if ($diff->d > 0) {
    $months++;
}

// Minimum 1 month
$months = max(1, $months);

/* ================= 4. SCHEDULE & SUBJECTS ================= */
$subjects = [];
if ($current_class_id) {
    // Determine subject names, teachers, and basic timing
    $sql = "
        SELECT 
            sub.name as subject_name, 
            t.first_name, t.last_name, t.phone as teacher_phone,
            cs.day_of_week, cs.start_time, cs.end_time,
            cs.id as class_subject_id
        FROM class_subjects cs
        LEFT JOIN subjects sub ON cs.subject_id = sub.id
        LEFT JOIN teachers t ON cs.teacher_id = t.id
        WHERE cs.class_id = ?
        ORDER BY cs.day_of_week, cs.start_time
    ";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_class_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $subjects[] = $row;
    }
    $stmt->close();
}

/* ================= 5. EXAMS & RESULTS ================= */
$exam_results = [];
$sql = "
    SELECT 
        e.name as exam_name, e.academic_year, e.exam_type, e.start_date,
        sub.name as subject_name,
        er.obtained_marks, er.remarks, er.status as attendance_status,
        es.max_marks, es.pass_marks
    FROM exam_results er
    JOIN exams e ON er.exam_id = e.id
    JOIN subjects sub ON er.subject_id = sub.id
    LEFT JOIN exam_subjects es ON (es.exam_id = e.id AND es.subject_id = er.subject_id)
    WHERE er.student_id = ?
    ORDER BY e.start_date DESC, sub.name ASC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $exam_results[] = $row;
}
$stmt->close();

/* ================= 6. ATTENDANCE ================= */
$attendance_records = [];
$attendance_stats = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];

// Fetch last 50 records for list
$sql = "
    SELECT a.*, sub.name as subject_name 
    FROM attendance a 
    LEFT JOIN subjects sub ON a.subject_id = sub.id
    WHERE a.student_id = ? 
    ORDER BY a.date DESC LIMIT 50
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $attendance_records[] = $row;
    // Simple stats counting (only for these 50? Better to count all)
}
$stmt->close();

// Full stats count
$sql_stats = "SELECT status, COUNT(*) as count FROM attendance WHERE student_id = ? GROUP BY status";
$stmt = $conn->prepare($sql_stats);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $st = strtolower($row['status']);
    if(isset($attendance_stats[$st])) {
        $attendance_stats[$st] = $row['count'];
    }
}
$stmt->close();

/* ================= 7. FEES & DISCOUNTS ================= */
$fees = [];
$total_payable = 0;
$total_discount = 0;

$monthly_fee = 0;

if ($current_section_id > 0) {
    $sql = "
        SELECT sf.amount
        FROM section_fees sf
        JOIN fee_categories fc ON fc.id = sf.fee_category_id
        WHERE sf.section_id = ?
        AND fc.name = 'fee salary'
        LIMIT 1
    ";

    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $current_section_id);
    $stmt->execute();

    $row = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($row) {
        $monthly_fee = (float)$row['amount'];
    }
}

// Calculate total payable
$total_payable = $months * $monthly_fee;

// Fee breakdown (for UI)
$temp_date = clone $joined_date;

for ($i = 0; $i < $months; $i++) {
    $temp_date->modify('+1 month');
    $fees[] = [
        'type' => 'Monthly Fee',
        'due_date' => $temp_date->format('Y-m-d'),
        'amount' => $monthly_fee
    ];
}

// Class Fees
if ($current_class_id) {
    $sql = "SELECT amount, 'Class Fee' as type, due_date FROM class_fees WHERE class_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $current_class_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $fees[] = $row;
        $total_payable += $row['amount'];
    }
    $stmt->close();
}

// Section Fees
if ($current_section_id) {
    $sql = "
    SELECT amount, 'Section Fee' as type, created_at as due_date
    FROM section_fees
    WHERE section_id = ?
    AND created_at >= ?
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $current_section_id, $joined_date_str);

    $stmt->execute();
    $res = $stmt->get_result();
    while($row = $res->fetch_assoc()) {
        $fees[] = $row;
        $total_payable += $row['amount'];
    }
    $stmt->close();
}

// Discounts
$sql = "SELECT * FROM student_discounts WHERE student_id = ? AND active = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $student_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $d_amt = 0;
    if ($row['discount_amount'] > 0) {
        $d_amt = $row['discount_amount'];
    } elseif ($row['discount_percentage'] > 0) {
        $d_amt = ($total_payable * ($row['discount_percentage'] / 100));
    }
    $total_discount += $d_amt;
}
$stmt->close();

/* ================= PAID AMOUNT ================= */
$total_paid = 0;
$stmt = $conn->prepare("SELECT COALESCE(SUM(paid_amount),0) AS total_paid FROM student_fees WHERE student_id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$paid_row = $stmt->get_result()->fetch_assoc();
$total_paid = (float)($paid_row['total_paid'] ?? 0);
$stmt->close();

/* ================= 8. MESSAGES ================= */
$messages = [];
// Find parent ID for message query
$parent_id = $parent['id'] ?? 0;
$sql = "
    SELECT * FROM messages 
    WHERE (recipient_type = 'parent' AND recipient_id = ?)
    OR (recipient_type = 'teacher' AND 1=0) -- Placeholder if we add student type later
    ORDER BY created_at DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $parent_id);
$stmt->execute();
$res = $stmt->get_result();
while($row = $res->fetch_assoc()) {
    $messages[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>"
      class="<?= $is_dark ? 'dark' : 'light' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> - <?php echo htmlspecialchars($student['name']); ?></title>
</head>
<body class="<?= $is_dark ? 'dark' : 'light' ?> min-h-screen">

<div class="container">
    <!-- Header -->
    <div class="page-header">
        <div class="page-title">
            <h1><?php echo htmlspecialchars($student['name']); ?> <span>(ID: <?php echo $student['id']; ?>)</span></h1>
        </div>
        <div class="no-print">
            <button onclick="window.print()" class="btn btn-print"><?= t('btn_print') ?></button>
            <a href="javascript:window.history.back()" class="btn btn-outline"><?= t('header_back') ?></a>
        </div>
    </div>

    <!-- Top Row: Basic Info -->
    <div class="grid-3">
        <!-- 1. Student Profile -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><?= t('card_personal_info') ?></h3></div>
            <div class="card-body">
                <div class="data-row"><span class="label"><?= t('label_date_of_birth') ?></span> <span class="value"><?php echo $student['dob']; ?></span></div>
                <div class="data-row"><span class="label"><?= t('label_gender') ?></span> <span class="value"><?php echo $student['gender'] == 'male' ? t('gender_male') : t('gender_female'); ?></span></div>
                <div class="data-row"><span class="label"><?= t('label_admission_date') ?></span> <span class="value"><?php echo $student['admission_date']; ?></span></div>
                <div class="data-row"><span class="label"><?= t('label_address') ?></span> <span class="value"><?php echo htmlspecialchars($student['address']); ?></span></div>
            </div>
        </div>

        <!-- 2. Academic Status -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><?= t('card_academic_info') ?></h3></div>
            <div class="card-body">
                <?php if($class_info): ?>
                    <div class="data-row"><span class="label"><?= t('label_current_class') ?></span> <span class="value"><?php echo htmlspecialchars($class_info['class_name']); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_section') ?></span> <span class="value"><?php echo htmlspecialchars($class_info['section_name'] ?? 'N/A'); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_academic_year') ?></span> <span class="value"><?php echo htmlspecialchars($class_info['academic_year']); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_enrollment_date') ?></span> <span class="value"><?php echo date('Y-m-d', strtotime($class_info['created_at'])); ?></span></div>
                <?php else: ?>
                    <div style="padding: 20px; text-align: center; color: var(--text-muted);"><?= t('label_not_enrolled') ?></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- 3. Guardian Info -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><?= t('card_guardian_info') ?></h3></div>
            <div class="card-body">
                <?php if($parent): ?>
                    <div class="data-row"><span class="label"><?= t('label_parent_name') ?></span> <span class="value"><?php echo htmlspecialchars($parent['name']); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_parent_phone') ?></span> <span class="value"><a href="tel:<?php echo $parent['phone']; ?>"><?php echo htmlspecialchars($parent['phone']); ?></a></span></div>
                    <div class="data-row"><span class="label"><?= t('label_parent_email') ?></span> <span class="value"><?php echo htmlspecialchars($parent['email']); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_occupation') ?></span> <span class="value"><?php echo htmlspecialchars($parent['occupation']); ?></span></div>
                <?php else: ?>
                    <div class="data-row"><span class="label"><?= t('label_guardian_name') ?></span> <span class="value"><?php echo htmlspecialchars($student['guardian_name']); ?></span></div>
                    <div class="data-row"><span class="label"><?= t('label_guardian_phone') ?></span> <span class="value"><?php echo htmlspecialchars($student['guardian_phone']); ?></span></div>
                    <div style="margin-top: 10px; font-size: 0.8rem; color: var(--warning);"><?= t('label_not_linked') ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Middle Row: Schedule & Fees -->
    <div class="grid-2">
        <!-- 4. Class Subjects & Schedule -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><?= t('card_subjects_teachers') ?></h3></div>
            <div class="card-body" style="padding:0;">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th><?= t('table_subject') ?></th>
                                <th><?= t('table_teacher') ?></th>
                                <th><?= t('table_day') ?></th>
                                <th><?= t('table_time') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($subjects) > 0): ?>
                                <?php foreach($subjects as $sub): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($sub['subject_name']); ?></td>
                                    <td>
                                        <?php echo htmlspecialchars($sub['first_name'] . ' ' . $sub['last_name']); ?>
                                        <br><small style="color:var(--text-muted)"><?php echo $sub['teacher_phone']; ?></small>
                                    </td>
                                    <td><?php echo $sub['day_of_week']; ?></td>
                                    <td><?php echo date('H:i', strtotime($sub['start_time'])) . ' - ' . date('H:i', strtotime($sub['end_time'])); ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" style="text-align:center;"><?= t('table_no_subjects') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- 5. Financial Summary -->
        <div class="card">
            <div class="card-header"><h3 class="card-title"><?= t('card_fee_summary') ?></h3></div>
            <div class="card-body">
                <div class="stats-container">
                    <div class="stat-box">
                        <span class="stat-val"><?php echo number_format($total_payable, 2); ?></span>
                        <span class="stat-lbl"><?= t('stat_total_fees') ?></span>
                    </div>
                    <div class="stat-box" style="border-color: var(--success); color: var(--success);">
                        <span class="stat-val">-<?php echo number_format($total_discount, 2); ?></span>
                        <span class="stat-lbl"><?= t('stat_discount') ?></span>
                    </div>
                    <div class="stat-box" style="border-color: #06b6d4; color: #06b6d4;">
                        <span class="stat-val"><?php echo number_format($total_paid, 2); ?></span>
                        <span class="stat-lbl"><?= t('stat_paid') ?></span>
                    </div>
                </div>
                
                <h4 style="font-size: 0.9rem; margin: 15px 0 10px; color: var(--text-muted); text-transform:uppercase;"><?= t('stat_fee_breakdown') ?></h4>
                <div class="table-responsive" style="max-height: 200px; overflow-y: auto;">
                    <table>
                        <thead><tr><th><?= t('table_type') ?></th><th><?= t('table_due') ?></th><th><?= t('table_amount') ?></th></tr></thead>
                        <tbody>
                            <?php foreach($fees as $fee): ?>
                            <tr>
                                <td><?php echo $fee['type']; ?></td>
                                <td><?php echo $fee['due_date']; ?></td>
                                <td><?php echo number_format($fee['amount'], 2); ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($fees)): ?>
                                <tr><td colspan="3" style="text-align:center"><?= t('table_no_fees') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Row: Exams, Attendance, Messages -->
    <div class="grid-2">
        <!-- 6. Exam Results -->
        <?php
        // Calculate exam overview
        $total_exams = count($exam_results);
        $total_obtained = 0;
        $total_max = 0;
        $pass_count = 0;

        foreach($exam_results as $exam){
            $total_obtained += $exam['obtained_marks'];
            $total_max += $exam['max_marks'];
            if($exam['obtained_marks'] >= $exam['pass_marks']){
                $pass_count++;
            }
        }

        $average_percent = $total_max ? round($total_obtained / $total_max * 100, 1) : 0;
        $pass_percent = $total_exams ? round($pass_count / $total_exams * 100, 1) : 0;
        $fail_count = $total_exams - $pass_count;
        $fail_percent = $total_exams ? round($fail_count / $total_exams * 100, 1) : 0;
        ?>

        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?= t('card_exam_performance') ?></h3>
            </div>
            <div class="card-body">
                <div style="display:flex; gap:10px; margin-bottom:10px;">
                    <div class="badge bg-green"><?= t('stat_exam_passed') ?>: <?php echo $pass_count; ?> (<?php echo $pass_percent; ?>%)</div>
                    <div class="badge bg-red"><?= t('stat_exam_failed') ?>: <?php echo $fail_count; ?> (<?php echo $fail_percent; ?>%)</div>
                    <div class="badge bg-blue"><?= t('stat_exam_average') ?>: <?php echo $average_percent; ?>%</div>
                </div>

                <!-- Optional visual bar -->
                <div style="display:flex; height:20px; border-radius:5px; overflow:hidden; background:#eee;">
                    <div style="flex:<?php echo $pass_count; ?>; background:#28a745;"></div>
                    <div style="flex:<?php echo $fail_count; ?>; background:#dc3545;"></div>
                </div>

                <div style="margin-top:5px; font-size:0.85rem; color:#555;">
                    <?= t('stat_total_exams') ?>: <?php echo $total_exams; ?>
                </div>
            </div>
        </div>

        <!-- 7. Attendance & Messages -->
        <div style="display: flex; flex-direction: column; gap: 20px;">
            <!-- Attendance -->
            <?php
            // Calculate overall attendance performance
            $total_attendance = array_sum($attendance_stats);
            $present_pct = $total_attendance ? round($attendance_stats['present'] / $total_attendance * 100, 1) : 0;
            $absent_pct = $total_attendance ? round($attendance_stats['absent'] / $total_attendance * 100, 1) : 0;
            $late_pct = $total_attendance ? round($attendance_stats['late'] / $total_attendance * 100, 1) : 0;
            $excused_pct = $total_attendance ? round($attendance_stats['excused'] / $total_attendance * 100, 1) : 0;
            ?>
            <div class="card" style="flex:1;">
                <div class="card-header">
                    <h3 class="card-title"><?= t('card_attendance') ?></h3>
                </div>
                
                <div class="card-body">
                    <div style="display:flex; gap:10px; margin-bottom:10px;">
                        <div style="margin-top:5px; font-size:0.85rem; color:#555;">
                            <?= t('stat_total_records') ?>: <?php echo $total_attendance; ?>
                        </div>:
                        <div class="badge bg-green"><?= t('stat_present') ?>: <?php echo $attendance_stats['present']; ?> (<?php echo $present_pct; ?>%)</div>
                        <div class="badge bg-red"><?= t('stat_absent') ?>: <?php echo $attendance_stats['absent']; ?> (<?php echo $absent_pct; ?>%)</div>
                        <div class="badge bg-yellow"><?= t('stat_late') ?>: <?php echo $attendance_stats['late']; ?> (<?php echo $late_pct; ?>%)</div>
                        <div class="badge bg-blue"><?= t('stat_excused') ?>: <?php echo $attendance_stats['excused']; ?> (<?php echo $excused_pct; ?>%)</div>
                    </div>

                    <!-- Optional visual bar -->
                    <div style="display:flex; height:20px; border-radius:5px; overflow:hidden; background:#eee;">
                        <div style="flex:<?php echo $attendance_stats['present']; ?>; background:#28a745;"></div>
                        <div style="flex:<?php echo $attendance_stats['absent']; ?>; background:#dc3545;"></div>
                        <div style="flex:<?php echo $attendance_stats['late']; ?>; background:#ffc107;"></div>
                        <div style="flex:<?php echo $attendance_stats['excused']; ?>; background:#007bff;"></div>
                    </div>
                </div>
            </div>
            <?php
            function summarize($text, $limit = 90) {
                $text = trim(strip_tags($text));
                return strlen($text) > $limit
                    ? mb_substr($text, 0, $limit) . '...'
                    : $text;
            }
            ?>

            <!-- Messages -->
            <div class="card" style="flex:1;">
                <div class="card-header">
                    <h3 class="card-title"><?= t('card_communication') ?></h3>
                </div>

                <div class="card-body" style="padding:0; max-height:250px; overflow-y:auto;">
                    <?php if (!empty($messages)): ?>
                        <?php foreach ($messages as $msg): ?>
                            <div style="
                                padding:12px 15px;
                                border-bottom:1px solid var(--border-color);
                                display:flex;
                                gap:10px;
                            ">
                                <!-- Unread indicator -->
                                <div style="margin-top:6px;">
                                    <?php if (!$msg['is_read']): ?>
                                        <span style="
                                            width:8px;
                                            height:8px;
                                            background:var(--warning);
                                            border-radius:50%;
                                            display:inline-block;
                                        "></span>
                                    <?php endif; ?>
                                </div>

                                <!-- Message summary -->
                                <div style="flex:1;">
                                    <div style="display:flex; justify-content:space-between; align-items:center;">
                                        <strong style="color:var(--primary); font-size:0.95rem;">
                                            <?= htmlspecialchars($msg['message_title']) ?>
                                        </strong>
                                        <small style="color:var(--text-muted);">
                                            <?= date('M d', strtotime($msg['created_at'])) ?>
                                        </small>
                                    </div>

                                    <div style="
                                        font-size:0.85rem;
                                        color:var(--text-muted);
                                        margin-top:4px;
                                        line-height:1.4;
                                    ">
                                        <?= htmlspecialchars(summarize($msg['message_body'])) ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="padding:20px; text-align:center; color:var(--text-muted);">
                            <?= t('no_messages') ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>

<style>
        :root {
            /* Backgrounds */
            --bg-body: <?= $is_dark ? '#1a1b1e' : '#f4f6f9' ?>;
            --bg-card: <?= $is_dark ? '#25262b' : '#ffffff' ?>;
            
            /* Text */
            --text-main: <?= $is_dark ? '#c1c2c5' : '#333333' ?>;
            --text-muted: <?= $is_dark ? '#909296' : '#666666' ?>;

            /* Borders */
            --border-color: <?= $is_dark ? '#373a40' : '#e5e7eb' ?>;

            /* Colors */
            --primary: #3b82f6;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
        }

        /* Body & Layout */
        body {
            font-family: 'Segoe UI', system-ui, sans-serif;
            background: var(--bg-body);
            color: var(--text-main);
            margin: 0;
            padding: 20px;
        }
        .container { max-width: 1400px; margin: 0 auto; }

        /* Header */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
            padding-bottom: 15px;
        }
        .page-title h1 { margin: 0; font-size: 1.8rem; }
        .page-title span { color: var(--text-muted); font-size: 1rem; font-weight: normal; margin-left: 10px; }

        /* Buttons */
        .btn {
            padding: 8px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 500;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
            border: none;
        }
        .btn-primary { background: var(--primary); color: white; }
        .btn-outline { border: 1px solid var(--border-color); color: var(--text-main); background: transparent; }
        .btn-print { background: var(--text-main); color: var(--bg-card); }

        /* Grid System */
        .grid-3 { display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 20px; margin-bottom: 20px; }
        .grid-2 { display: grid; grid-template-columns: repeat(auto-fit, minmax(500px, 1fr)); gap: 20px; margin-bottom: 20px; }

        /* Cards */
        .card {
            background: var(--bg-card);
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            overflow: hidden;
            border: 1px solid var(--border-color);
            height: 100%;
        }
        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid var(--border-color);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: rgba(0,0,0,0.02);
        }
        .card-title {
            margin: 0;
            font-size: 1rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--primary);
        }
        .card-body { padding: 20px; }

        /* Data Lists */
        .data-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dashed var(--border-color); }
        .data-row:last-child { border-bottom: none; }
        .label { color: var(--text-muted); font-weight: 500; }
        .value { font-weight: 600; text-align: right; }

        /* Tables */
        .table-responsive { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; font-size: 0.9rem; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid var(--border-color); }
        th { color: var(--text-muted); font-weight: 600; font-size: 0.85rem; text-transform: uppercase; }
        tr:last-child td { border-bottom: none; }

        /* Badges */
        .badge { padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 600; }
        .bg-green { background: rgba(16, 185, 129, 0.15); color: var(--success); }
        .bg-red { background: rgba(239, 68, 68, 0.15); color: var(--danger); }
        .bg-yellow { background: rgba(245, 158, 11, 0.15); color: var(--warning); }
        .bg-blue { background: rgba(59, 130, 246, 0.15); color: var(--primary); }

        /* Stats Blocks */
        .stats-container { display: flex; gap: 10px; margin-bottom: 15px; }
        .stat-box {
            flex: 1;
            background: var(--bg-body);
            padding: 10px;
            border-radius: 6px;
            text-align: center;
            border: 1px solid var(--border-color);
        }
        .stat-val { display: block; font-size: 1.2rem; font-weight: 700; color: var(--text-main); }
        .stat-lbl { font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; }

        /* Print Styles */
        @media print {
            .no-print { display: none !important; }
            body { background: white; color: black; padding: 0; }
            .card { box-shadow: none; border: 1px solid #ddd; break-inside: avoid; }
            .grid-3, .grid-2 { display: block; }
            .card { margin-bottom: 20px; }
        }
</style>