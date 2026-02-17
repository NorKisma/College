<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$exam_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($exam_id <= 0) die("Invalid exam ID");

/* ================= TRANSLATIONS ================= */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Edit Exam Marks | School Management System',
        'edit_exam_marks' => 'Edit Exam Marks',
        'manage_student_marks' => 'Manage student marks and attendance for',
        'students' => 'Students',
        'subjects' => 'Subjects',
        'class' => 'Class',
        'section' => 'Section',
        'search_students' => 'Search students by name...',
        'clear' => 'Clear',
        'all_attendance_status' => 'All Attendance Status',
        'present_only' => 'Present Only',
        'absent_only' => 'Absent Only',
        'quick_actions' => 'Quick Actions',
        'mark_all_present' => 'Mark All Present',
        'mark_all_absent' => 'Mark All Absent',
        'auto_pass_all' => 'Auto-Pass All',
        'clear_all_marks' => 'Clear All Marks',
        'student_marks_attendance' => 'Student Marks & Attendance',
        'visible_students' => 'students visible',
        'student_name' => 'Student Name',
        'roll_no' => 'Roll No.',
        'max' => 'Max',
        'pass' => 'Pass',
        'total' => 'Total',
        'marks' => 'Marks',
        'percentage' => 'Percentage',
        'status' => 'Status',
        'pass_fail' => 'Pass/Fail',
        'present' => 'Present',
        'absent' => 'Absent',
        'no_marks' => 'No marks',
        'save_all_changes' => 'Save All Changes',
        'back_to_exams' => 'Back to Exams',
        'auto_save_change' => 'Auto-save on change',
        'marks_validation_note' => 'Marks are automatically validated against maximum marks. Changes are saved immediately when "Auto-save" is enabled.',
        'invalid_exam_id' => 'Invalid exam ID',
        'exam_not_found' => 'Exam not found!',
        'saved_successfully' => '✅ Student marks and attendance saved successfully!',
        'fix_errors' => 'Please fix the following errors',
        'no_students_found' => 'No students found for this exam',
        'saving' => 'Saving...',
        'changes_auto_saved' => 'Changes auto-saved successfully',
        'attendance_updated' => 'Attendance status updated to',
        'marks_warning' => 'Marks must be between 0 and',
        'all_marked_present' => 'All students marked as present',
        'all_marked_absent' => 'All students marked as absent',
        'all_passing_grades' => 'All present students marked with passing grades',
        'marks_cleared' => 'All marks cleared',
        'clear_confirmation' => 'Are you sure you want to clear all marks? This cannot be undone.',
        'validation_error' => 'Some marks exceed maximum allowed values. Please fix them before saving.',
        'saving_changes' => 'Saving changes...'
    ],
    2 => [ // Arabic
        'page_title' => 'تعديل درجات الامتحان | نظام إدارة المدرسة',
        'edit_exam_marks' => 'تعديل درجات الامتحان',
        'manage_student_marks' => 'إدارة درجات وحضور الطلاب لـ',
        'students' => 'الطلاب',
        'subjects' => 'المواد',
        'class' => 'الفصل',
        'section' => 'القسم',
        'search_students' => 'ابحث عن الطلاب بالاسم...',
        'clear' => 'مسح',
        'all_attendance_status' => 'جميع حالات الحضور',
        'present_only' => 'الحاضرون فقط',
        'absent_only' => 'الغائبون فقط',
        'quick_actions' => 'إجراءات سريعة',
        'mark_all_present' => 'وضع جميع الحاضرين',
        'mark_all_absent' => 'وضع جميع الغائبين',
        'auto_pass_all' => 'نجاح الكل تلقائياً',
        'clear_all_marks' => 'مسح جميع الدرجات',
        'student_marks_attendance' => 'درجات وحضور الطلاب',
        'visible_students' => 'طالب مرئي',
        'student_name' => 'اسم الطالب',
        'roll_no' => 'الرقم التسلسلي',
        'max' => 'الأقصى',
        'pass' => 'النجاح',
        'total' => 'المجموع',
        'marks' => 'الدرجات',
        'percentage' => 'النسبة',
        'status' => 'الحالة',
        'pass_fail' => 'ناجح/راسب',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'no_marks' => 'لا توجد درجات',
        'save_all_changes' => 'حفظ جميع التغييرات',
        'back_to_exams' => 'العودة إلى الامتحانات',
        'auto_save_change' => 'الحفظ التلقائي عند التغيير',
        'marks_validation_note' => 'يتم التحقق من الدرجات تلقائياً مقابل الحد الأقصى للدرجات. يتم حفظ التغييرات فوراً عند تمكين "الحفظ التلقائي".',
        'invalid_exam_id' => 'رقم امتحان غير صالح',
        'exam_not_found' => 'الامتحان غير موجود!',
        'saved_successfully' => '✅ تم حفظ درجات وحضور الطلاب بنجاح!',
        'fix_errors' => 'يرجى إصلاح الأخطاء التالية',
        'no_students_found' => 'لم يتم العثور على طلاب لهذا الامتحان',
        'saving' => 'جاري الحفظ...',
        'changes_auto_saved' => 'تم حفظ التغييرات تلقائياً بنجاح',
        'attendance_updated' => 'تم تحديث حالة الحضور إلى',
        'marks_warning' => 'يجب أن تكون الدرجات بين 0 و',
        'all_marked_present' => 'تم وضع جميع الطلاب كحاضرين',
        'all_marked_absent' => 'تم وضع جميع الطلاب كغائبين',
        'all_passing_grades' => 'تم وضع جميع الطلاب الحاضرين بدرجات نجاح',
        'marks_cleared' => 'تم مسح جميع الدرجات',
        'clear_confirmation' => 'هل أنت متأكد أنك تريد مسح جميع الدرجات؟ لا يمكن التراجع عن هذا الإجراء.',
        'validation_error' => 'بعض الدرجات تتجاوز القيم المسموح بها القصوى. يرجى إصلاحها قبل الحفظ.',
        'saving_changes' => 'جاري حفظ التغييرات...'
    ],
    3 => [ // Somali
        'page_title' => 'Wax ka beddel Dhibcaha Imtixaanka | Nidaamka Maareynta Dugsiga',
        'edit_exam_marks' => 'Wax ka beddel Dhibcaha Imtixaanka',
        'manage_student_marks' => 'Maamul dhibcaha iyo xuduurka ardayda',
        'students' => 'Ardayda',
        'subjects' => 'Maadoyiinka',
        'class' => 'Fasalka',
        'section' => 'Qaybta',
        'search_students' => 'Raadi ardayda magaca...',
        'clear' => 'Tir tir',
        'all_attendance_status' => 'Dhammaan Xaaladaha Xuduurka',
        'present_only' => 'Joogta Kaliya',
        'absent_only' => 'Maqnaanta Kaliya',
        'quick_actions' => 'Ficillo Dhakhso leh',
        'mark_all_present' => 'Dhamaan jooga',
        'mark_all_absent' => 'Dhammaan Maqan',
        'auto_pass_all' => 'Dhammaan guuleystey',
        'clear_all_marks' => 'Ka tir Dhammaan Dhibcaha',
        'student_marks_attendance' => 'Dhibcaha iyo Xuduurka Ardayda',
        'visible_students' => 'arday la arki karo',
        'student_name' => 'Magaca Ardayga',
        'roll_no' => 'Lambarka',
        'max' => 'Ugu Badan',
        'pass' => 'Guuleystay',
        'total' => 'Wadarta',
        'marks' => 'Dhibcaha',
        'percentage' => 'Boqolkiiba',
        'status' => 'Xaalada',
        'pass_fail' => 'Guuleystal/Dhacay',
        'present' => 'Jooga',
        'absent' => 'Maqan',
        'no_marks' => 'Dhibco ma jiraan',
        'save_all_changes' => 'Save garee Dhammaan',
        'back_to_exams' => 'Dib u noqo',
        'auto_save_change' => 'Si auto ah u kaydi',
        'marks_validation_note' => 'Dhibcaha si otomaatig ah ayaa loo xaqiijiyaa dhibcaha ugu badan. Isbeddellada si degdeg ah ayaa la keydiyaa marka "Keydka Otomaatiga ah" la kiciyo.',
        'invalid_exam_id' => 'ID-ga imtixaanka aan sax ahayn',
        'exam_not_found' => 'Wax Imtixaan lama helin!',
        'saved_successfully' => '✅ Dhibcaha iyo xuduurka ardayda si guul leh ayaa loo Save gareeye!',
        'fix_errors' => 'Fadlan hagaaji khaladaha soo socda',
        'no_students_found' => 'Arday loogu talagalay imtixaankan lama helin',
        'saving' => 'Save gareynaya...',
        'changes_auto_saved' => 'Isbeddellada si guul leh ayaa loo keydiyay',
        'attendance_updated' => 'Xaalada xuduurka waa la cusboonaysiiyay',
        'marks_warning' => 'Dhibcaha waa in ay u dhaxeeyaan 0 iyo',
        'all_marked_present' => 'Dhammaan ardayda waa ay joogaan',
        'all_marked_absent' => 'Dhammaan ardayda waa ay maqanyihiin',
        'all_passing_grades' => 'Dhammaan ardayda joogtada ah waxaa lagu Siiyey dhibcaha guusha',
        'marks_cleared' => 'Dhammaan dhibcaha waa la nadiifiyay',
        'clear_confirmation' => 'Ma hubtaa inaad rabto inaad nadiifiso dhammaan dhibcaha?  ',
        'validation_error' => 'Qaar ka mid ah dhibcaha ayaa ka weyn qiimaha ugu badan ee la ogol yahay. Fadlan hagaaji ka hor inta aan la dirin.',
        'saving_changes' => 'Keydinta isbeddellada...'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

// ---------- FETCH EXAM ----------
$stmt = $conn->prepare("
    SELECT e.*, c.id AS class_id, c.name AS class_name, c.section_id, s.name AS section_name
    FROM exams e
    JOIN classes c ON e.class_id = c.id
    LEFT JOIN sections s ON c.section_id = s.id
    WHERE e.id = ? 
    LIMIT 1
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();
if (!$exam) die(t('exam_not_found'));

// ---------- FETCH SUBJECTS ----------
$stmt = $conn->prepare("
    SELECT es.*, s.name AS subject_name 
    FROM exam_subjects es 
    JOIN subjects s ON es.subject_id = s.id
    WHERE es.exam_id = ? 
    ORDER BY s.name
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$subjects_res = $stmt->get_result();
$subjects = [];
while ($row = $subjects_res->fetch_assoc()) {
    $subjects[$row['subject_id']] = $row;
}

// ---------- FETCH STUDENTS ----------
$stmt = $conn->prepare("
    SELECT s.id, s.name
    FROM class_students cs
    JOIN students s ON cs.student_id = s.id
    WHERE cs.class_id = ?
    ORDER BY s.name
");
$stmt->bind_param("i", $exam['class_id']);
$stmt->execute();
$students_res = $stmt->get_result();
$students = [];
while ($row = $students_res->fetch_assoc()) {
    $students[$row['id']] = $row;
}

// ---------- HANDLE SUBMIT ----------
$message = '';
$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($students as $student_id => $_s) {
    foreach ($subjects as $subject_id => $_sub) {

        $status_val = $_POST['status'][$student_id][$subject_id] ?? 'present';
        $mark       = $_POST['marks'][$student_id][$subject_id] ?? null;

        $mark_val = ($status_val === 'absent')
            ? null
            : (($mark === '' || $mark === null) ? null : (float)$mark);


// Check existing
$stmt_check = $conn->prepare("
    SELECT id FROM exam_results 
    WHERE exam_id = ? AND student_id = ? AND subject_id = ?
");
$stmt_check->bind_param("iii", $exam_id, $student_id, $subject_id);
$stmt_check->execute();
$res_check = $stmt_check->get_result();

if ($res_check->num_rows > 0) {
    $row_id = $res_check->fetch_assoc()['id'];
    $stmt_update = $conn->prepare("
        UPDATE exam_results 
        SET obtained_marks = ?, status = ?, updated_at = NOW()
        WHERE id = ?
    ");
    $stmt_update->bind_param("dsi", $mark_val, $status_val, $row_id);
    $stmt_update->execute();
} else {
    $stmt_insert = $conn->prepare("
        INSERT INTO exam_results 
            (exam_id, student_id, class_id, section_id, subject_id, obtained_marks, status)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt_insert->bind_param(
        "iiiiids", 
        $exam_id, $student_id, $exam['class_id'], $exam['section_id'], $subject_id, $mark_val, $status_val
    );
    $stmt_insert->execute();
}

        }
    }
    if (empty($errors)) {
        $message = t('saved_successfully');
    }
}

/* ================= ESCAPE HELPER ================= */
function e($str): string {
    return htmlspecialchars((string)$str, ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('edit_exam_marks') ?> - <?= e($exam['name']) ?> | <?= t('school_management_system') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables for enhanced table functionality -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 for better dropdowns -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    
    <!-- Toastr for notifications -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="container-fluid">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1 class="h3 mb-1 text-primary">
                            <i class="fas fa-edit me-2"></i><?= t('edit_exam_marks') ?>
                        </h1>
                        <p class="text-muted mb-0"><?= t('manage_student_marks') ?> <?= e($exam['name']) ?></p>
                    </div>
                    <div>
                        <span class="badge bg-primary fs-6 p-2">
                            <i class="fas fa-calendar-alt me-1"></i> 
                            <?= date('F j, Y', strtotime($exam['exam_date'] ?? date('Y-m-d'))) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Summary Cards -->
        <div class="row mb-4">
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card summary-card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text mb-1"><?= t('students') ?></p>
                                <h2 class="stat-number mb-0"><?= count($students) ?></h2>
                            </div>
                            <div class="fs-1">
                                <i class="fas fa-users"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card" style="background: linear-gradient(135deg, #2ecc71, #27ae60); color: white;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text mb-1"><?= t('subjects') ?></p>
                                <h2 class="stat-number mb-0"><?= count($subjects) ?></h2>
                            </div>
                            <div class="fs-1">
                                <i class="fas fa-book"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card" style="background: linear-gradient(135deg, #3498db, #2980b9); color: white;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text mb-1"><?= t('class') ?></p>
                                <h2 class="stat-number mb-0"><?= e($exam['class_name']) ?></h2>
                            </div>
                            <div class="fs-1">
                                <i class="fas fa-school"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3 col-sm-6 mb-3">
                <div class="card" style="background: linear-gradient(135deg, #9b59b6, #8e44ad); color: white;">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <p class="card-text mb-1"><?= t('section') ?></p>
                                <h2 class="stat-number mb-0"><?= e($exam['section_name']) ?></h2>
                            </div>
                            <div class="fs-1">
                                <i class="fas fa-layer-group"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-check-circle me-2 fs-4"></i>
                <div><?= e($message) ?></div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($errors): ?>
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="fas fa-exclamation-triangle me-2 fs-4"></i>
                <div>
                    <strong><?= t('fix_errors') ?>:</strong>
                    <ul class="mb-0 mt-1">
                        <?php foreach ($errors as $e): ?>
                            <li><?= e($e) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <!-- Filter & Quick Actions -->
        <div class="filter-container">
            <div class="row">
                <div class="col-md-8">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0">
                            <i class="fas fa-search text-muted"></i>
                        </span>
                        <input type="text" id="studentFilter" class="form-control border-start-0" 
                               placeholder="<?= t('search_students') ?>">
                        <button class="btn btn-outline-secondary" type="button" id="clearFilter">
                            <?= t('clear') ?>
                        </button>
                    </div>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <select id="attendanceFilter" class="form-select">
                        <option value=""><?= t('all_attendance_status') ?></option>
                        <option value="present"><?= t('present_only') ?></option>
                        <option value="absent"><?= t('absent_only') ?></option>
                    </select>
                </div>
            </div>
            
            <div class="quick-actions mt-3">
                <small class="text-muted d-block mb-2"><?= t('quick_actions') ?>:</small>
                <button type="button" class="btn btn-sm btn-outline-primary quick-action-btn" id="markAllPresent">
                    <i class="fas fa-user-check me-1"></i><?= t('mark_all_present') ?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-danger quick-action-btn" id="markAllAbsent">
                    <i class="fas fa-user-times me-1"></i><?= t('mark_all_absent') ?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-success quick-action-btn" id="autoPassAll">
                    <i class="fas fa-magic me-1"></i><?= t('auto_pass_all') ?>
                </button>
                <button type="button" class="btn btn-sm btn-outline-warning quick-action-btn" id="clearAllMarks">
                    <i class="fas fa-eraser me-1"></i><?= t('clear_all_marks') ?>
                </button>
            </div>
        </div>
        
        <!-- Main Form -->
        <form method="post" id="marksForm">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <i class="fas fa-table me-2"></i><?= t('student_marks_attendance') ?>
                    </div>
                    <div class="text-light">
                        <span id="visibleStudents"><?= count($students) ?></span> <?= t('of') ?> <?= count($students) ?> <?= t('visible_students') ?>
                    </div>
                </div>
                
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="marksTable">
                            <thead class="table-light">
                                <tr>
                                    <th width="200" class="align-middle">
                                        <div><?= t('student_name') ?></div>
                                        <div class="small text-muted"><?= t('roll_no') ?></div>
                                    </th>
                                    <?php foreach($subjects as $sub_id => $sub): ?>
                                    <th width="180" class="subject-header align-middle text-center">
                                        <div class="fw-bold"><?= e($sub['subject_name']) ?></div>
                                        <div class="small">
                                            <?= t('max') ?>: <span class="badge bg-secondary"><?= $sub['max_marks'] ?></span> | 
                                            <?= t('pass') ?>: <span class="badge bg-success"><?= $sub['pass_marks'] ?></span>
                                        </div>
                                    </th>
                                    <?php endforeach; ?>
                                    <th width="100" class="align-middle text-center">
                                        <div><?= t('total') ?></div>
                                        <div class="small text-muted"><?= t('marks') ?></div>
                                    </th>
                                    <th width="100" class="align-middle text-center">
                                        <div><?= t('percentage') ?></div>
                                        <div class="small text-muted">%</div>
                                    </th>
                                    <th width="120" class="align-middle text-center">
                                        <div><?= t('status') ?></div>
                                        <div class="small text-muted"><?= t('pass_fail') ?></div>
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($students as $student): ?>
                                <?php 
                                    $total = 0; 
                                    $subject_count = count($subjects);
                                    $passed_subjects = 0;
                                    
                                    // Store subject data for JS
                                    $subject_data = [];
                                    foreach($subjects as $sub_id => $sub) {
                                        $res = $conn->query("SELECT obtained_marks, status FROM exam_results WHERE exam_id = $exam_id AND student_id = {$student['id']} AND subject_id = $sub_id");
                                        $row = $res->fetch_assoc();
                                        $mark = $row['obtained_marks'] ?? '';
                                        $status = $row['status'] ?? (($mark === '' || $mark === null) ? 'absent' : 'present');
                                        
                                        $subject_data[] = [
                                            'id' => $sub_id,
                                            'mark' => $mark,
                                            'status' => $status,
                                            'max' => $sub['max_marks'],
                                            'pass' => $sub['pass_marks']
                                        ];
                                        
                                        $total += is_numeric($mark) ? $mark : 0;
                                        if (is_numeric($mark) && $mark >= $sub['pass_marks']) {
                                            $passed_subjects++;
                                        }
                                    }
                                    
                                    $percentage = $subject_count ? round($total / array_sum(array_column($subjects, 'max_marks')) * 100, 2) : 0;
                                    $overall_status = ($percentage >= 50 && $passed_subjects == $subject_count) ? 'Pass' : 'Fail';
                                    $overall_class = ($overall_status == 'Pass') ? 'passed' : 'failed';
                                ?>
                                <tr class="student-row" data-student-id="<?= $student['id'] ?>">
                                    <td class="align-middle fw-medium">
                                        <div><?= e($student['name']) ?></div>
                                        <div class="small text-muted">ID: <?= $student['id'] ?></div>
                                    </td>
                                    
                                    <?php foreach($subject_data as $index => $subject): 
                                        $class = ($subject['status'] === 'absent') ? 'absent' : (($subject['mark'] >= $subject['pass']) ? 'passed' : 'failed');
                                    ?>
                                    <td class="align-middle <?= $class ?>" data-subject-id="<?= $subject['id'] ?>">
                                        <!-- Attendance Toggle -->
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="me-2">
                                                <span class="status-indicator status-<?= $subject['status'] ?>"></span>
                                                <small class="text-muted">
                                                    <?= ucfirst(t($subject['status'])) ?>
                                                </small>
                                            </div>
                                            <div class="ms-auto attendance-toggle" 
                                                 data-student-id="<?= $student['id'] ?>" 
                                                 data-subject-id="<?= $subject['id'] ?>"
                                                 title="<?= t('toggle_attendance') ?>">
                                                <?php if($subject['status'] === 'present'): ?>
                                                    <i class="fas fa-toggle-on text-success fs-5"></i>
                                                <?php else: ?>
                                                    <i class="fas fa-toggle-off text-secondary fs-5"></i>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        
                                        <!-- Hidden Status Field -->
                                        <input type="hidden" 
                                               name="status[<?= $student['id'] ?>][<?= $subject['id'] ?>]" 
                                               value="<?= $subject['status'] ?>" 
                                               class="status-field">
                                        
                                        <!-- Marks Input -->
                                        <div class="input-group input-group-sm">
                                            <input type="number" 
                                                   name="marks[<?= $student['id'] ?>][<?= $subject['id'] ?>]" 
                                                   value="<?= e($subject['mark']) ?>" 
                                                   min="0" 
                                                   max="<?= $subject['max'] ?>" 
                                                   class="form-control marks-input" 
                                                   data-student-id="<?= $student['id'] ?>"
                                                   data-subject-id="<?= $subject['id'] ?>"
                                                   data-max="<?= $subject['max'] ?>"
                                                   data-pass="<?= $subject['pass'] ?>"
                                                   <?= $subject['status'] === 'absent' ? 'disabled' : '' ?>>
                                            <span class="input-group-text bg-transparent border-start-0">/<?= $subject['max'] ?></span>
                                        </div>
                                        
                                        <!-- Pass/Fail Indicator -->
                                        <div class="small mt-1 text-center">
                                            <?php if($subject['status'] === 'present'): ?>
                                                <?php if(is_numeric($subject['mark'])): ?>
                                                    <?php if($subject['mark'] >= $subject['pass']): ?>
                                                        <span class="badge bg-success"><?= t('pass') ?></span>
                                                    <?php else: ?>
                                                        <span class="badge bg-danger"><?= t('fail') ?></span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-secondary"><?= t('no_marks') ?></span>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= t('absent') ?></span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <?php endforeach; ?>
                                    
                                    <!-- Totals -->
                                    <td class="align-middle text-center fw-bold total-cell">
                                        <?= $total ?>
                                    </td>
                                    
                                    <td class="align-middle text-center percentage-cell">
                                        <div class="fw-bold"><?= $percentage ?>%</div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar" role="progressbar" 
                                                 style="width: <?= min($percentage, 100) ?>%;"></div>
                                        </div>
                                    </td>
                                    
                                    <td class="align-middle text-center">
                                        <span class="badge rounded-pill <?= $overall_class === 'passed' ? 'bg-success' : 'bg-danger' ?> p-2">
                                            <?= $overall_status ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <!-- Action Buttons -->
            <div class="action-buttons mt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <button type="submit" class="btn btn-primary btn-lg px-4" id="saveButton">
                            <i class="fas fa-save me-2"></i><?= t('save_all_changes') ?>
                        </button>
                        <a href="exams.php" class="btn btn-outline-secondary btn-lg px-4 ms-2">
                            <i class="fas fa-arrow-left me-2"></i><?= t('back_to_exams') ?>
                        </a>
                    </div>
                    <div class="text-end">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="autoSave" checked>
                            <label class="form-check-label" for="autoSave">
                                <?= t('auto_save_change') ?>
                            </label>
                        </div>
                        <div class="progress" id="saveProgress" style="display: none;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        
        <!-- Footer Note -->
        <div class="text-center text-muted mt-4 small">
            <p>
                <i class="fas fa-info-circle me-1"></i>
                <?= t('marks_validation_note') ?>
            </p>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Initialize DataTable
        $(document).ready(function() {
            // Initialize DataTable with custom options
            const table = $('#marksTable').DataTable({
                paging: false,
                searching: false,
                info: false,
                ordering: true,
                responsive: true,
                autoWidth: false,
                order: [[0, 'asc']],
                columnDefs: [
                    { orderable: false, targets: [1, 2, 3, 4, 5] } // Make subject columns not sortable
                ],
                language: {
                    emptyTable: "<?= t('no_students_found') ?>"
                }
            });
            
            // Update visible students count
            updateVisibleStudents();
            
            // Initialize toastr
            toastr.options = {
                "positionClass": "toast-top-right",
                "closeButton": true,
                "progressBar": true,
                "timeOut": "5000"
            };
            
            // Filter students by name
            $('#studentFilter').on('keyup', function() {
                table.search(this.value).draw();
                updateVisibleStudents();
                highlightSearchTerm(this.value);
            });
            
            // Clear filter
            $('#clearFilter').on('click', function() {
                $('#studentFilter').val('').keyup();
            });
            
            // Filter by attendance status
            $('#attendanceFilter').on('change', function() {
                const status = $(this).val();
                
                if (status === '') {
                    table.columns().search('').draw();
                } else {
                    // This is a simplified filter - in a real implementation, 
                    // you'd need a more complex approach to filter by cell content
                    table.rows().nodes().each(function(row) {
                        const hasStatus = $(row).find('.status-field[value="' + status + '"]').length > 0;
                        $(row).toggle(hasStatus);
                    });
                    
                    updateVisibleStudents();
                }
            });
            
            // Toggle attendance on icon click
            $(document).on('click', '.attendance-toggle', function() {
                const studentId = $(this).data('student-id');
                const subjectId = $(this).data('subject-id');
                const statusField = $(this).closest('td').find('.status-field');
                const marksInput = $(this).closest('td').find('.marks-input');
                const toggleIcon = $(this).find('i');
                const statusIndicator = $(this).closest('td').find('.status-indicator');
                
                // Toggle status
                const newStatus = statusField.val() === 'present' ? 'absent' : 'present';
                statusField.val(newStatus);
                
                // Update UI
                if (newStatus === 'present') {
                    toggleIcon.removeClass('fa-toggle-off text-secondary')
                             .addClass('fa-toggle-on text-success');
                    statusIndicator.removeClass('status-absent')
                                   .addClass('status-present');
                    marksInput.prop('disabled', false);
                    $(this).closest('td').find('.badge.bg-secondary').text('<?= t('no_marks') ?>').removeClass('bg-secondary').addClass('bg-warning');
                } else {
                    toggleIcon.removeClass('fa-toggle-on text-success')
                             .addClass('fa-toggle-off text-secondary');
                    statusIndicator.removeClass('status-present')
                                   .addClass('status-absent');
                    marksInput.prop('disabled', true);
                    $(this).closest('td').find('.badge').text('<?= t('absent') ?>').removeClass('bg-warning bg-success bg-danger').addClass('bg-secondary');
                }
                
                // Update cell class
                updateCellClass($(this).closest('td'), marksInput.val(), newStatus);
                
                // Recalculate totals
                recalculateStudentTotals(studentId);
                
                // Auto-save if enabled
                if ($('#autoSave').is(':checked')) {
                    autoSaveChanges(studentId, subjectId, marksInput.val(), newStatus);
                }
                
                // Show notification
                toastr.info('<?= t('attendance_updated') ?> ' + newStatus);
            });
            
            // Handle marks input changes
            $(document).on('input', '.marks-input', function() {
                const studentId = $(this).data('student-id');
                const subjectId = $(this).data('subject-id');
                const value = $(this).val();
                const max = $(this).data('max');
                const pass = $(this).data('pass');
                
                // Validate input
                if (value !== '' && (parseFloat(value) > max || parseFloat(value) < 0)) {
                    $(this).addClass('is-invalid');
                    toastr.warning('<?= t('marks_warning') ?> ' + max);
                    return;
                }
                
                $(this).removeClass('is-invalid');
                
                // Update pass/fail badge
                const badge = $(this).closest('td').find('.badge');
                if (value === '') {
                    badge.text('<?= t('no_marks') ?>').removeClass('bg-success bg-danger').addClass('bg-warning');
                } else if (parseFloat(value) >= pass) {
                    badge.text('<?= t('pass') ?>').removeClass('bg-warning bg-danger').addClass('bg-success');
                } else {
                    badge.text('<?= t('fail') ?>').removeClass('bg-warning bg-success').addClass('bg-danger');
                }
                
                // Update cell class
                const status = $(this).closest('td').find('.status-field').val();
                updateCellClass($(this).closest('td'), value, status);
                
                // Recalculate totals
                recalculateStudentTotals(studentId);
                
                // Auto-save if enabled
                if ($('#autoSave').is(':checked') && status === 'present') {
                    autoSaveChanges(studentId, subjectId, value, status);
                }
            });
            
            // Mark all present
            $('#markAllPresent').on('click', function() {
                $('.attendance-toggle').each(function() {
                    if ($(this).find('i').hasClass('fa-toggle-off')) {
                        $(this).click();
                    }
                });
                toastr.success('<?= t('all_marked_present') ?>');
            });
            
            // Mark all absent
            $('#markAllAbsent').on('click', function() {
                $('.attendance-toggle').each(function() {
                    if ($(this).find('i').hasClass('fa-toggle-on')) {
                        $(this).click();
                    }
                });
                toastr.success('<?= t('all_marked_absent') ?>');
            });
            
            // Auto-pass all (set marks to pass marks)
            $('#autoPassAll').on('click', function() {
                $('.marks-input').each(function() {
                    if (!$(this).prop('disabled')) {
                        const passMarks = $(this).data('pass');
                        $(this).val(passMarks).trigger('input');
                    }
                });
                toastr.success('<?= t('all_passing_grades') ?>');
            });
            
            // Clear all marks
            $('#clearAllMarks').on('click', function() {
                if (confirm('<?= t('clear_confirmation') ?>')) {
                    $('.marks-input').each(function() {
                        if (!$(this).prop('disabled')) {
                            $(this).val('').trigger('input');
                        }
                    });
                    toastr.success('<?= t('marks_cleared') ?>');
                }
            });
            
            // Form submission with validation
            $('#marksForm').on('submit', function(e) {
                // Basic validation - check if any marks exceed max
                let hasErrors = false;
                
                $('.marks-input').each(function() {
                    if (!$(this).prop('disabled')) {
                        const value = $(this).val();
                        const max = $(this).data('max');
                        
                        if (value !== '' && parseFloat(value) > max) {
                            $(this).addClass('is-invalid');
                            hasErrors = true;
                        }
                    }
                });
                
                if (hasErrors) {
                    e.preventDefault();
                    toastr.error('<?= t('validation_error') ?>');
                    $('html, body').animate({
                        scrollTop: $('.is-invalid').first().offset().top - 100
                    }, 500);
                } else {
                    // Show saving progress
                    $('#saveProgress').show();
                    $('#saveButton').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i><?= t('saving') ?>');
                }
            });
            
            // Initialize Select2 for dropdowns
            $('select').select2({
                minimumResultsForSearch: Infinity,
                width: '100%'
            });
            
            // Highlight search term in student names
            function highlightSearchTerm(term) {
                if (term === '') {
                    $('.student-row td:first-child').each(function() {
                        $(this).html($(this).text());
                    });
                    return;
                }
                
                const regex = new RegExp('(' + term + ')', 'gi');
                
                $('.student-row td:first-child').each(function() {
                    const text = $(this).text();
                    const highlighted = text.replace(regex, '<span class="highlight">$1</span>');
                    $(this).html(highlighted);
                });
            }
            
            // Update visible students count
            function updateVisibleStudents() {
                const visibleRows = $('.student-row:visible').length;
                $('#visibleStudents').text(visibleRows);
            }
            
            // Update cell class based on marks and attendance
            function updateCellClass(cell, marks, status) {
                const passMarks = cell.find('.marks-input').data('pass');
                
                cell.removeClass('passed failed absent');
                
                if (status === 'absent') {
                    cell.addClass('absent');
                } else if (marks === '' || isNaN(parseFloat(marks))) {
                    cell.addClass('absent');
                } else if (parseFloat(marks) >= passMarks) {
                    cell.addClass('passed');
                } else {
                    cell.addClass('failed');
                }
            }
            
            // Recalculate totals for a student
            function recalculateStudentTotals(studentId) {
                const row = $(`[data-student-id="${studentId}"]`);
                let total = 0;
                let maxTotal = 0;
                let passedSubjects = 0;
                let subjectCount = 0;
                
                row.find('.marks-input').each(function() {
                    const value = $(this).val();
                    const max = $(this).data('max');
                    const pass = $(this).data('pass');
                    const status = $(this).closest('td').find('.status-field').val();
                    
                    maxTotal += max;
                    subjectCount++;
                    
                    if (status === 'present' && value !== '' && !isNaN(parseFloat(value))) {
                        total += parseFloat(value);
                        
                        if (parseFloat(value) >= pass) {
                            passedSubjects++;
                        }
                    }
                });
                
                // Update total cell
                row.find('.total-cell').text(total.toFixed(1));
                
                // Calculate percentage
                const percentage = maxTotal > 0 ? (total / maxTotal * 100) : 0;
                row.find('.percentage-cell .fw-bold').text(percentage.toFixed(2) + '%');
                row.find('.percentage-cell .progress-bar').css('width', Math.min(percentage, 100) + '%');
                
                // Update overall status
                const overallStatus = (percentage >= 50 && passedSubjects === subjectCount) ? 'Pass' : 'Fail';
                const overallClass = overallStatus === 'Pass' ? 'bg-success' : 'bg-danger';
                
                const statusBadge = row.find('.percentage-cell').next().find('.badge');
                statusBadge.removeClass('bg-success bg-danger').addClass(overallClass).text(overallStatus);
            }
            
            // Auto-save changes via AJAX
            function autoSaveChanges(studentId, subjectId, marks, status) {
                // In a real implementation, this would send an AJAX request to a PHP endpoint
                // For this example, we'll simulate it with a timeout
                
                // Show saving indicator
                const saveButton = $('#saveButton');
                const originalText = saveButton.html();
                saveButton.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i><?= t('saving') ?>');
                
                // Simulate AJAX call
                setTimeout(() => {
                    saveButton.prop('disabled', false).html(originalText);
                    
                    // Show success notification
                    toastr.success('<?= t('changes_auto_saved') ?>', '', {timeOut: 2000});
                }, 800);
            }
            
            // Keyboard shortcuts
            $(document).on('keydown', function(e) {
                // Ctrl + S to save
                if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                    e.preventDefault();
                    $('#saveButton').click();
                }
                
                // Escape to clear filter
                if (e.key === 'Escape') {
                    $('#studentFilter').val('').keyup();
                }
            });
            
            // Initialize tooltips
            $('[title]').tooltip();
        });
    </script>
</body>
</html>

<style>
        :root {
            --primary-color: #4361ee;
            --success-color: #2ecc71;
            --danger-color: #e74c3c;
            --warning-color: #f39c12;
            --info-color: #3498db;
            --light-gray: #f8f9fa;
            --border-radius: 8px;
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding-top: 20px;
            padding-bottom: 40px;
        }
        
        .card {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 25px;
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            border-radius: var(--border-radius) var(--border-radius) 0 0 !important;
            padding: 15px 20px;
        }
        
        .status-indicator {
            display: inline-block;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            margin-right: 5px;
        }
        
        .status-present { background-color: var(--success-color); }
        .status-absent { background-color: var(--danger-color); }
        
        .marks-input {
            width: 90px;
            transition: all 0.2s;
        }
        
        .marks-input:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
            border-color: var(--primary-color);
        }
        
        .passed { 
            background-color: rgba(46, 204, 113, 0.1); 
            border-left: 4px solid var(--success-color);
        }
        
        .failed { 
            background-color: rgba(231, 76, 60, 0.1); 
            border-left: 4px solid var(--danger-color);
        }
        
        .absent { 
            background-color: rgba(189, 195, 199, 0.1); 
            border-left: 4px solid #bdc3c7;
        }
        
        .student-row:hover {
            background-color: rgba(67, 97, 238, 0.03);
        }
        
        .summary-card {
            background: linear-gradient(135deg, #4361ee, #3a56d4);
            color: white;
        }
        
        .progress-bar {
            background-color: var(--primary-color);
        }
        
        .subject-header {
            background-color: var(--light-gray);
            font-weight: 600;
            border-bottom: 2px solid #dee2e6;
        }
        
        .action-buttons {
            position: sticky;
            bottom: 0;
            background: white;
            padding: 15px;
            border-top: 1px solid #dee2e6;
            z-index: 100;
            box-shadow: 0 -3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .filter-container {
            background-color: white;
            border-radius: var(--border-radius);
            padding: 15px;
            margin-bottom: 20px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }
        
        .highlight {
            background-color: #fff9c4 !important;
        }
        
        .quick-actions {
            margin-bottom: 15px;
        }
        
        .quick-action-btn {
            margin-right: 8px;
            margin-bottom: 8px;
        }
        
        .attendance-toggle {
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .attendance-toggle:hover {
            transform: scale(1.1);
        }
        
        #saveProgress {
            height: 5px;
            margin-top: 10px;
        }
        
        .stat-number {
            font-size: 1.8rem;
            font-weight: 700;
        }
        
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .marks-input {
                width: 70px;
            }
        }
    </style>