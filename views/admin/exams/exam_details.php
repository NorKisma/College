<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Exam Results',
        'header_title' => 'Exam Results',
        'class_info' => 'Class',
        'section_info' => 'Section',
        'class_selector' => 'Select Class',
        
        // Stats Cards
        'stats_total_students' => 'Total Students',
        'stats_enrolled' => 'Enrolled in class',
        'stats_total_exams' => 'Total Exams',
        'stats_conducted' => 'Conducted',
        'stats_subjects' => 'Subjects',
        'stats_in_curriculum' => 'In curriculum',
        'stats_marks_entered' => 'Marks Entered',
        'stats_individual_marks' => 'Individual marks',
        
        // Exam Types
        'exam_type_midterm' => 'Midterm',
        'exam_type_final' => 'Final',
        
        // Exam Info
        'exam_subjects' => 'subjects',
        'exam_students' => 'students',
        'exam_marks' => 'marks',
        
        // Table Headers
        'table_student' => 'Student',
        'table_max' => 'Max',
        'table_pass' => 'Pass',
        'table_id' => 'ID',
        
        // Status Labels
        'status_absent' => 'Absent',
        'status_present' => 'Present',
        'status_not_entered' => 'Not entered',
        
        // Action Buttons
        'btn_save_changes' => 'Save All Changes',
        'btn_print_results' => 'Print Results',
        'btn_clear_marks' => 'Clear All Marks',
        
        // Export Functionality
        'btn_export' => 'Export Report',
        'export_title' => 'Export Exam Results',
        'export_description' => 'Choose your preferred export format. All exports include summary statistics and data table.',
        'export_pdf' => 'Export as PDF',
        'export_excel' => 'Export as Excel',
        'export_print' => 'Print Report',
        'export_info' => 'Export includes: Summary statistics and exam data table.',
        'export_generating' => 'Generating export...',
        
        // Messages
        'invalid_class_id' => 'Invalid class ID.',
        'class_not_found' => 'Class not found!',
        'no_exams_found' => 'No Exams Found',
        'no_exams_recorded' => 'There are no exams recorded for this class yet.',
        'no_subjects_added' => 'No subjects added to this exam. Please add subjects first.',
        
        // Success Messages
        'success_marks_saved' => 'Marks saved successfully',
        'success_marks_saved_count' => 'Saved {count} marks',
        'success_marks_cleared' => 'Cleared {count} marks.',
        
        // Error Messages
        'error_saving_marks' => 'Error saving marks',
        'error_saving_changes' => 'Error saving changes: ',
        'error_clearing_marks' => 'Error clearing marks: ',
        'error_network' => 'Network error: ',
        'error_no_changes' => 'No changes to save.',
        
        // Validation Messages
        'validation_marks_range' => 'Marks must be between 0 and {max}',
        
        // Confirmation Messages
        'confirm_clear_marks' => 'Are you sure you want to clear ALL marks for this exam? This action cannot be undone.',
        'confirm_unsaved_changes' => 'You have unsaved changes. Are you sure you want to leave?',
        
        // Pagination
        'pagination_prev' => 'Prev',
        'pagination_next' => 'Next',
        
        // Status Options
        'option_present' => 'Present',
        'option_absent' => 'Absent',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'نتائج الامتحانات',
        'header_title' => 'نتائج الامتحانات',
        'class_info' => 'الصف',
        'section_info' => 'القسم',
        'class_selector' => 'اختر الصف',
        
        // Stats Cards
        'stats_total_students' => 'إجمالي الطلاب',
        'stats_enrolled' => 'مسجلين في الصف',
        'stats_total_exams' => 'إجمالي الامتحانات',
        'stats_conducted' => 'تم إجراؤها',
        'stats_subjects' => 'المواد',
        'stats_in_curriculum' => 'في المنهج',
        'stats_marks_entered' => 'الدرجات المدخلة',
        'stats_individual_marks' => 'درجات فردية',
        
        // Exam Types
        'exam_type_midterm' => 'منتصف الفصل',
        'exam_type_final' => 'نهاية الفصل',
        
        // Exam Info
        'exam_subjects' => 'مواد',
        'exam_students' => 'طلاب',
        'exam_marks' => 'درجات',
        
        // Table Headers
        'table_student' => 'الطالب',
        'table_max' => 'الحد الأقصى',
        'table_pass' => 'النجاح',
        'table_id' => 'الرقم',
        
        // Status Labels
        'status_absent' => 'غائب',
        'status_present' => 'حاضر',
        'status_not_entered' => 'غير مدخل',
        
        // Action Buttons
        'btn_save_changes' => 'حفظ جميع التغييرات',
        'btn_print_results' => 'طباعة النتائج',
        'btn_clear_marks' => 'مسح جميع الدرجات',
        
        // Export Functionality
        'btn_export' => 'تصدير التقرير',
        'export_title' => 'تصدير نتائج الامتحان',
        'export_description' => 'اختر تنسيق التصدير المفضل لديك. تشمل جميع عمليات التصدير الإحصائيات وجدول البيانات.',
        'export_pdf' => 'تصدير كملف PDF',
        'export_excel' => 'تصدير كملف Excel',
        'export_print' => 'طباعة التقرير',
        'export_info' => 'يشمل التصدير: الإحصائيات وجدول بيانات الامتحان.',
        'export_generating' => 'جارٍ إنشاء التصدير...',
        
        // Messages
        'invalid_class_id' => 'معرف الصف غير صالح.',
        'class_not_found' => 'لم يتم العثور على الصف!',
        'no_exams_found' => 'لم يتم العثور على امتحانات',
        'no_exams_recorded' => 'لا توجد امتحانات مسجلة لهذا الصف بعد.',
        'no_subjects_added' => 'لم تتم إضافة مواد لهذا الامتحان. الرجاء إضافة المواد أولاً.',
        
        // Success Messages
        'success_marks_saved' => 'تم حفظ الدرجات بنجاح',
        'success_marks_saved_count' => 'تم حفظ {count} درجة',
        'success_marks_cleared' => 'تم مسح {count} درجة.',
        
        // Error Messages
        'error_saving_marks' => 'خطأ في حفظ الدرجات',
        'error_saving_changes' => 'خطأ في حفظ التغييرات: ',
        'error_clearing_marks' => 'خطأ في مسح الدرجات: ',
        'error_network' => 'خطأ في الشبكة: ',
        'error_no_changes' => 'لا توجد تغييرات للحفظ.',
        
        // Validation Messages
        'validation_marks_range' => 'يجب أن تكون الدرجات بين 0 و {max}',
        
        // Confirmation Messages
        'confirm_clear_marks' => 'هل أنت متأكد أنك تريد مسح جميع الدرجات لهذا الامتحان؟ لا يمكن التراجع عن هذا الإجراء.',
        'confirm_unsaved_changes' => 'لديك تغييرات غير محفوظة. هل أنت متأكد أنك تريد المغادرة؟',
        
        // Pagination
        'pagination_prev' => 'السابق',
        'pagination_next' => 'التالي',
        
        // Status Options
        'option_present' => 'حاضر',
        'option_absent' => 'غائب',
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

// Get theme from database
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1; // default light
$isDark = $themeMode == 0;

// Get class ID from URL
$class_id = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
if ($class_id <= 0) die(t('invalid_class_id'));

// ---------- Pagination ----------
$per_page = 25;
$page = isset($_GET['page']) && $_GET['page'] > 0 ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Get class details
$class = $conn->query("
    SELECT c.*, s.name as section_name 
    FROM classes c 
    LEFT JOIN sections s ON c.section_id = s.id 
    WHERE c.id = $class_id
")->fetch_assoc();

if (!$class) die(t('class_not_found'));

// Get all classes for dropdown
$classes = $conn->query("SELECT id, name FROM classes ORDER BY name");

// Handle AJAX requests for saving marks
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'save_mark') {
        $exam_id = (int)$_POST['exam_id'];
        $student_id = (int)$_POST['student_id'];
        $subject_id = (int)$_POST['subject_id'];
        $marks = $_POST['marks'] !== '' ? (float)$_POST['marks'] : null;
        $status = $_POST['status'] ?? 'present';
        $remarks = $_POST['remarks'] ?? '';
        
        // Check if record exists
        $check = $conn->query("
            SELECT id FROM exam_results 
            WHERE exam_id = $exam_id 
            AND student_id = $student_id 
            AND subject_id = $subject_id
        ");
        
        if ($check->num_rows > 0) {
            // Update existing
            $stmt = $conn->prepare("
                UPDATE exam_results 
                SET obtained_marks = ?, status = ?, remarks = ?, updated_at = NOW() 
                WHERE exam_id = ? AND student_id = ? AND subject_id = ?
            ");
            $stmt->bind_param("dssiii", $marks, $status, $remarks, $exam_id, $student_id, $subject_id);
        } else {
            // Insert new
            $stmt = $conn->prepare("
                INSERT INTO exam_results 
                (exam_id, student_id, class_id, section_id, subject_id, obtained_marks, status, remarks) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param("iiiiidss", 
                $exam_id, $student_id, $class_id, $class['section_id'], 
                $subject_id, $marks, $status, $remarks
            );
        }
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => t('success_marks_saved')]);
        } else {
            echo json_encode(['success' => false, 'message' => t('error_saving_marks')]);
        }
        $stmt->close();
        exit;
    }
    
    if ($_POST['action'] === 'save_bulk') {
        $updates = json_decode($_POST['updates'], true);
        $success_count = 0;
        
        foreach ($updates as $update) {
            $exam_id = (int)$update['exam_id'];
            $student_id = (int)$update['student_id'];
            $subject_id = (int)$update['subject_id'];
            $marks = $update['marks'] !== '' ? (float)$update['marks'] : null;
            $status = $update['status'] ?? 'present';
            
            $check = $conn->query("
                SELECT id FROM exam_results 
                WHERE exam_id = $exam_id 
                AND student_id = $student_id 
                AND subject_id = $subject_id
            ");
            
            if ($check->num_rows > 0) {
                $stmt = $conn->prepare("
                    UPDATE exam_results 
                    SET obtained_marks = ?, status = ?, updated_at = NOW() 
                    WHERE exam_id = ? AND student_id = ? AND subject_id = ?
                ");
                $stmt->bind_param("dsiii", $marks, $status, $exam_id, $student_id, $subject_id);
            } else {
                $stmt = $conn->prepare("
                    INSERT INTO exam_results 
                    (exam_id, student_id, class_id, section_id, subject_id, obtained_marks, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iiiiids", 
                    $exam_id, $student_id, $class_id, $class['section_id'], 
                    $subject_id, $marks, $status
                );
            }
            
            if ($stmt->execute()) $success_count++;
            $stmt->close();
        }
        
        echo json_encode(['success' => true, 'message' => t('success_marks_saved_count', ['count' => $success_count])]);
        exit;
    }
}

$total_students_res = $conn->query("
    SELECT COUNT(*) as total
    FROM class_students
    WHERE class_id = $class_id
");
$total_students = $total_students_res->fetch_assoc()['total'];
$total_pages = ceil($total_students / $per_page);

// Get all students in this class
$students = $conn->query("
    SELECT s.*, cs.academic_year 
    FROM students s 
    JOIN class_students cs ON s.id = cs.student_id 
    WHERE cs.class_id = $class_id 
    ORDER BY s.name
    LIMIT $per_page OFFSET $offset
");

// Get all exams for this class
$exams = $conn->query("
    SELECT e.*, 
           COUNT(DISTINCT es.id) as subject_count,
           COUNT(DISTINCT er.id) as results_count
    FROM exams e
    LEFT JOIN exam_subjects es ON es.exam_id = e.id
    LEFT JOIN exam_results er ON er.exam_id = e.id AND er.class_id = $class_id
    WHERE e.class_id = $class_id 
    GROUP BY e.id
    ORDER BY e.start_date DESC, e.exam_type, e.name
");

// Get all subjects for this class
$subjects = $conn->query("
    SELECT cs.*, s.name as subject_name, s.code 
    FROM class_subjects cs 
    JOIN subjects s ON cs.subject_id = s.id 
    WHERE cs.class_id = $class_id 
    ORDER BY s.name
");

// Prepare exam data with subjects
$exam_data = [];
while ($exam = $exams->fetch_assoc()) {
    $exam_data[$exam['id']] = $exam;
    
    // Get subjects for this exam
    $exam_subjects = $conn->query("
        SELECT es.*, s.name as subject_name, s.code 
        FROM exam_subjects es 
        JOIN subjects s ON es.subject_id = s.id 
        WHERE es.exam_id = {$exam['id']} 
        ORDER BY s.name
    ");
    
    $exam_data[$exam['id']]['subjects'] = [];
    while ($subject = $exam_subjects->fetch_assoc()) {
        $exam_data[$exam['id']]['subjects'][$subject['subject_id']] = $subject;
    }
}

// Get all marks for this class
$all_marks = $conn->query("
    SELECT er.* 
    FROM exam_results er
    JOIN exams e ON e.id = er.exam_id
    WHERE er.class_id = $class_id 
    AND e.class_id = $class_id
");

$marks_by_student = [];
while ($mark = $all_marks->fetch_assoc()) {
    $key = $mark['student_id'] . '_' . $mark['exam_id'] . '_' . $mark['subject_id'];
    $marks_by_student[$key] = $mark;
}

// Calculate statistics
$stats = [
    'total_students' => $total_students,
    'total_exams' => count($exam_data),
    'total_subjects' => $subjects->num_rows,
    'total_marks' => count($marks_by_student)
];

// Reset result pointers
$students->data_seek(0);
$exams->data_seek(0);
$subjects->data_seek(0);
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> - <?= htmlspecialchars($class['name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Include SheetJS for Excel export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
    <!-- Include jsPDF for PDF export -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
 
</head>
<body>
    <!-- Export Modal -->
    <div id="exportModal" class="export-modal">
        <div class="export-modal-content">
            <button class="export-close" onclick="closeExportModal()">&times;</button>
            <h2><i class="fas fa-file-export"></i> <?= t('export_title') ?></h2>
            <p class="export-description">
                <?= t('export_description') ?>
            </p>
            
            <div class="export-options">
                <button onclick="exportToPDF()" class="export-btn export-pdf">
                    <i class="fas fa-file-pdf"></i> <?= t('export_pdf') ?>
                </button>
                <button onclick="exportToExcel()" class="export-btn export-excel">
                    <i class="fas fa-file-excel"></i> <?= t('export_excel') ?>
                </button>
                <button onclick="exportPrint()" class="export-btn export-print">
                    <i class="fas fa-print"></i> <?= t('export_print') ?>
                </button>
            </div>
            
            <p class="export-info">
                <i class="fas fa-info-circle"></i> <?= t('export_info') ?>
            </p>
        </div>
    </div>

    <!-- Export Loading Overlay -->
    <div id="exportLoading" class="export-loading">
        <div class="export-spinner"></div>
        <div class="export-loading-text"><?= t('export_generating') ?></div>
    </div>

    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-top">
                <div class="header-title">
                    <h1><i class="fas fa-chart-line"></i> <?= t('header_title') ?></h1>
                    <div class="class-info">
                        <i class="fas fa-graduation-cap"></i>
                        <?= t('class_info') ?>: <?= htmlspecialchars($class['name']) ?> 
                        <?php if($class['section_name']): ?>
                            • <?= t('section_info') ?>: <?= htmlspecialchars($class['section_name']) ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="header-actions">
                    <div class="class-selector">
                        <i class="fas fa-exchange-alt"></i>
                        <select onchange="window.location = '?class_id=' + this.value">
                            <option value=""><?= t('class_selector') ?></option>
                            <?php while($c = $classes->fetch_assoc()): ?>
                                <option value="<?= $c['id'] ?>" <?= $c['id'] == $class_id ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <button onclick="openExportModal()" class="btn btn-export">
                        <i class="fas fa-file-export"></i> <?= t('btn_export') ?>
                    </button>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="stats-grid">
                <div class="stat-card">
                    <h3><?= t('stats_total_students') ?></h3>
                    <div class="value"><?= $stats['total_students'] ?></div>
                    <div class="label"><?= t('stats_enrolled') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= t('stats_total_exams') ?></h3>
                    <div class="value"><?= $stats['total_exams'] ?></div>
                    <div class="label"><?= t('stats_conducted') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= t('stats_subjects') ?></h3>
                    <div class="value"><?= $stats['total_subjects'] ?></div>
                    <div class="label"><?= t('stats_in_curriculum') ?></div>
                </div>
                <div class="stat-card">
                    <h3><?= t('stats_marks_entered') ?></h3>
                    <div class="value"><?= $stats['total_marks'] ?></div>
                    <div class="label"><?= t('stats_individual_marks') ?></div>
                </div>
            </div>
        </div>

        <!-- Exams Container -->
        <div class="exams-container">
            <?php if(count($exam_data) == 0): ?>
                <div class="no-data">
                    <i class="fas fa-clipboard-list fa-3x" style="margin-bottom: 1rem; opacity: 0.5;"></i>
                    <h3><?= t('no_exams_found') ?></h3>
                    <p><?= t('no_exams_recorded') ?></p>
                </div>
            <?php endif; ?>
            
            <?php foreach($exam_data as $exam_id => $exam): ?>
            <div class="exam-accordion" data-exam-id="<?= $exam_id ?>">
                <div class="exam-header" onclick="toggleExam(this)">
                    <div class="exam-title">
                        <h3>
                            <?= htmlspecialchars($exam['name']) ?>
                            <span class="badge <?= $exam['exam_type'] === 'midterm' ? 'badge-midterm' : 'badge-final' ?>">
                                <i class="fas fa-<?= $exam['exam_type'] === 'midterm' ? 'vial' : 'flag-checkered' ?>"></i>
                                <?= $exam['exam_type'] === 'midterm' ? t('exam_type_midterm') : t('exam_type_final') ?>
                            </span>
                        </h3>
                        <div class="exam-info">
                            <?php if($exam['start_date']): ?>
                                <span><i class="fas fa-calendar-alt"></i> <?= date('M d, Y', strtotime($exam['start_date'])) ?></span>
                            <?php endif; ?>
                            <?php if($exam['end_date'] && $exam['end_date'] != $exam['start_date']): ?>
                                <span>to <?= date('M d, Y', strtotime($exam['end_date'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="exam-stats">
                        <div class="exam-stat">
                            <i class="fas fa-book"></i>
                            <span><?= count($exam['subjects']) ?> <?= t('exam_subjects') ?></span>
                        </div>
                        <div class="exam-stat">
                            <i class="fas fa-users"></i>
                            <span><?= $stats['total_students'] ?> <?= t('exam_students') ?></span>
                        </div>
                        <div class="exam-stat">
                            <i class="fas fa-check-circle"></i>
                            <span><?= $exam['results_count'] ?> <?= t('exam_marks') ?></span>
                        </div>
                    </div>
                    
                    <i class="fas fa-chevron-down"></i>
                </div>
                
                <div class="exam-content">
                    <?php if(count($exam['subjects']) == 0): ?>
                        <div class="no-data" style="padding: 2rem;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <p><?= t('no_subjects_added') ?></p>
                        </div>
                    <?php else: ?>
                        <!-- Marks Table -->
                        <div class="marks-table-container">
                            <table class="marks-table" id="examTable-<?= $exam_id ?>">
                                <thead>
                                    <tr>
                                        <th class="student-col"><?= t('table_student') ?></th>
                                        <?php foreach($exam['subjects'] as $subject_id => $subject): ?>
                                        <th class="subject-col">
                                            <div style="text-align: center;">
                                                <div style="font-weight: 600;"><?= htmlspecialchars($subject['subject_name']) ?></div>
                                                <div style="font-size: 0.75rem; color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;">
                                                    <?= t('table_max') ?>: <?= $subject['max_marks'] ?> | <?= t('table_pass') ?>: <?= $subject['pass_marks'] ?>
                                                </div>
                                            </div>
                                        </th>
                                        <?php endforeach; ?>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while($student = $students->fetch_assoc()): ?>
                                    <tr data-student-id="<?= $student['id'] ?>">
                                        <td class="student-col">
                                            <div style="display: flex; align-items: center; gap: 0.5rem;">
                                                <div style="width: 32px; height: 32px; border-radius: 50%; background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                                                    <?= strtoupper(substr($student['name'], 0, 1)) ?>
                                                </div>
                                                <div>
                                                    <div style="font-weight: 600;"><?= htmlspecialchars($student['name']) ?></div>
                                                    <div style="font-size: 0.75rem; color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;">
                                                        <?= t('table_id') ?>: <?= $student['id'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        
                                        <?php foreach($exam['subjects'] as $subject_id => $subject): 
                                            $mark_key = $student['id'] . '_' . $exam_id . '_' . $subject_id;
                                            $mark = $marks_by_student[$mark_key] ?? null;
                                            $marks_value = $mark['obtained_marks'] ?? '';
                                            $status = $mark['status'] ?? null;
                                            $is_absent = ($status === 'absent');
                                        ?>
                                        <td class="subject-col">
                                            <div class="editable-cell" 
                                                 data-exam-id="<?= $exam_id ?>"
                                                 data-student-id="<?= $student['id'] ?>"
                                                 data-subject-id="<?= $subject_id ?>"
                                                 data-max-marks="<?= $subject['max_marks'] ?>"
                                                 ondblclick="startEdit(this)">
                                                <div class="cell-content" style="display: <?= $is_absent ? 'none' : 'block' ?>;">
                                                    <?php if($is_absent): ?>
                                                        <span style="color:#dc3545; font-weight:600;"><?= t('status_absent') ?></span>
                                                    <?php elseif($marks_value !== ''): ?>
                                                        <span style="font-weight: 600;"><?= $marks_value ?></span>
                                                    <?php else: ?>
                                                        <span style="color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>; font-style: italic;">
                                                            <?= t('status_not_entered') ?>
                                                        </span>
                                                    <?php endif; ?>
                                                </div>
                                                
                                                <div class="cell-status status-<?= $status ?>" 
                                                     onclick="toggleStatusMenu(event, this)"
                                                     title="<?= $is_absent ? t('status_absent') . ' - ' . t('status_present') : t('status_present') . ' - ' . t('status_absent') ?>">
                                                    <i class="fas fa-<?= $is_absent ? 'times' : 'check' ?>"></i>
                                                </div>
                                                
                                                <div class="status-toggle">
                                                    <div class="status-option" onclick="setStatus(this, 'present')">
                                                        <i class="fas fa-check"></i> <?= t('option_present') ?>
                                                    </div>
                                                    <div class="status-option" onclick="setStatus(this, 'absent')">
                                                        <i class="fas fa-times"></i> <?= t('option_absent') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <?php endforeach; ?>
                                    </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="?class_id=<?= $class_id ?>&page=<?= $page - 1 ?>">&laquo; <?= t('pagination_prev') ?></a>
                                <?php endif; ?>

                                <?php for ($p = 1; $p <= $total_pages; $p++): ?>
                                    <a 
                                        href="?class_id=<?= $class_id ?>&page=<?= $p ?>"
                                        class="<?= $p == $page ? 'active' : '' ?>"
                                    >
                                        <?= $p ?>
                                    </a>
                                <?php endfor; ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="?class_id=<?= $class_id ?>&page=<?= $page + 1 ?>"><?= t('pagination_next') ?> &raquo;</a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Action Buttons -->
                        <div class="action-buttons">
                            <button class="btn btn-primary" onclick="saveAllChanges(<?= $exam_id ?>)">
                                <i class="fas fa-save"></i> <?= t('btn_save_changes') ?>
                            </button>
                            <button class="btn btn-secondary" onclick="printExam(<?= $exam_id ?>)">
                                <i class="fas fa-print"></i> <?= t('btn_print_results') ?>
                            </button>
                            <button class="btn btn-danger" onclick="clearAllMarks(<?= $exam_id ?>)">
                                <i class="fas fa-trash"></i> <?= t('btn_clear_marks') ?>
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <?php 
            // Reset students pointer for next exam
            $students->data_seek(0);
            endforeach; 
            ?>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
    </div>

    <script>
        // Export Modal Functions
        function openExportModal() {
            document.getElementById('exportModal').style.display = 'flex';
        }

        function closeExportModal() {
            document.getElementById('exportModal').style.display = 'none';
        }

        function showExportLoading(show) {
            document.getElementById('exportLoading').style.display = show ? 'flex' : 'none';
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modal = document.getElementById('exportModal');
            if (event.target === modal) {
                closeExportModal();
            }
        };

        // Export to PDF
        function exportToPDF() {
            showExportLoading(true);
            
            setTimeout(() => {
                const { jsPDF } = window.jspdf;
                const doc = new jsPDF('landscape', 'mm', 'a4');
                
                // Add header
                doc.setFontSize(24);
                doc.setTextColor(0, 0, 0);
                doc.text('Exam Results Report', 105, 20, { align: 'center' });
                
                doc.setFontSize(14);
                doc.text(`Class: ${document.querySelector('.class-info').textContent}`, 20, 40);
                doc.text(`Generated: ${new Date().toLocaleDateString()}`, doc.internal.pageSize.width - 50, 40);
                
                // Add summary stats
                const stats = [
                    `Total Students: ${<?= $stats['total_students'] ?>}`,
                    `Total Exams: ${<?= $stats['total_exams'] ?>}`,
                    `Total Subjects: ${<?= $stats['total_subjects'] ?>}`,
                    `Marks Entered: ${<?= $stats['total_marks'] ?>}`
                ];
                
                doc.setFontSize(12);
                stats.forEach((stat, index) => {
                    doc.text(stat, 20 + (index * 60), 55);
                });
                
                // Add table for each exam
                document.querySelectorAll('.exam-accordion').forEach((examAccordion, examIndex) => {
                    const examHeader = examAccordion.querySelector('.exam-title h3').textContent;
                    const table = examAccordion.querySelector('.marks-table');
                    
                    if (!table) return;
                    
                    // Start new page for each exam after the first
                    if (examIndex > 0) {
                        doc.addPage();
                    }
                    
                    // Add exam title
                    doc.setFontSize(16);
                    doc.text(examHeader, 20, 70);
                    
                    // Prepare table data
                    const data = [];
                    const headers = [];
                    
                    // Add headers
                    const headerRow = table.querySelector('thead tr');
                    headerRow.querySelectorAll('th').forEach(th => {
                        headers.push(th.textContent.trim());
                    });
                    data.push(headers);
                    
                    // Add rows
                    table.querySelectorAll('tbody tr').forEach(row => {
                        const rowData = [];
                        row.querySelectorAll('td').forEach(td => {
                            rowData.push(td.textContent.trim());
                        });
                        data.push(rowData);
                    });
                    
                    // Add table to PDF
                    doc.autoTable({
                        head: [data[0]],
                        body: data.slice(1),
                        startY: 80,
                        theme: 'grid',
                        styles: {
                            fontSize: 8,
                            cellPadding: 2,
                            overflow: 'linebreak'
                        },
                        headStyles: {
                            fillColor: [59, 130, 246],
                            textColor: [255, 255, 255],
                            fontStyle: 'bold'
                        },
                        alternateRowStyles: {
                            fillColor: [240, 240, 240]
                        }
                    });
                });
                
                // Add footer
                const pageCount = doc.internal.getNumberOfPages();
                for (let i = 1; i <= pageCount; i++) {
                    doc.setPage(i);
                    doc.setFontSize(10);
                    doc.setTextColor(128, 128, 128);
                    doc.text(`Page ${i} of ${pageCount}`, doc.internal.pageSize.width - 30, doc.internal.pageSize.height - 10);
                }
                
                // Save PDF
                doc.save(`Exam_Results_<?= date('Y-m-d_H-i-s') ?>.pdf`);
                
                showExportLoading(false);
                closeExportModal();
            }, 500);
        }

        // Export to Excel
        function exportToExcel() {
            showExportLoading(true);
            
            setTimeout(() => {
                const wb = XLSX.utils.book_new();
                
                // Create worksheet for each exam
                document.querySelectorAll('.exam-accordion').forEach((examAccordion, index) => {
                    const examHeader = examAccordion.querySelector('.exam-title h3').textContent;
                    const table = examAccordion.querySelector('.marks-table');
                    
                    if (!table) return;
                    
                    // Create worksheet from table
                    const ws = XLSX.utils.table_to_sheet(table);
                    
                    // Add summary info above table
                    const summaryData = [
                        ['Exam Results Report'],
                        ['Class:', document.querySelector('.class-info').textContent],
                        ['Exam:', examHeader],
                        ['Generated:', new Date().toLocaleDateString()],
                        [''],
                        ['Summary Statistics'],
                        [`Total Students: ${<?= $stats['total_students'] ?>}`],
                        [`Total Exams: ${<?= $stats['total_exams'] ?>}`],
                        [`Total Subjects: ${<?= $stats['total_subjects'] ?>}`],
                        [`Marks Entered: ${<?= $stats['total_marks'] ?>}`],
                        ['']
                    ];
                    
                    XLSX.utils.sheet_add_aoa(ws, summaryData, { origin: -1 });
                    
                    // Adjust column widths
                    const wscols = [
                        { wch: 25 }, // Student name
                    ];
                    
                    // Add column widths for subjects
                    const subjectCount = table.querySelectorAll('thead th').length - 1;
                    for (let i = 0; i < subjectCount; i++) {
                        wscols.push({ wch: 15 });
                    }
                    ws['!cols'] = wscols;
                    
                    // Add worksheet to workbook
                    const sheetName = `Exam ${index + 1}`.substring(0, 31); // Excel sheet name limit
                    XLSX.utils.book_append_sheet(wb, ws, sheetName);
                });
                
                // Save file
                XLSX.writeFile(wb, `Exam_Results_<?= date('Y-m-d_H-i-s') ?>.xlsx`);
                
                showExportLoading(false);
                closeExportModal();
            }, 500);
        }

        // Print function
        function exportPrint() {
            // Create print window content
            const printWindow = window.open('', '_blank');
            printWindow.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Exam Results Report - <?= date('Y-m-d') ?></title>
                    <style>
                        @page {
                            size: A4 landscape;
                            margin: 20mm;
                        }
                        body {
                            font-family: Arial, sans-serif;
                            color: #000;
                            padding: 20px;
                        }
                        .print-header {
                            text-align: center;
                            margin-bottom: 30px;
                            border-bottom: 2px solid #333;
                            padding-bottom: 20px;
                        }
                        .print-header h1 {
                            font-size: 24px;
                            margin-bottom: 10px;
                        }
                        .print-stats {
                            display: grid;
                            grid-template-columns: repeat(4, 1fr);
                            gap: 20px;
                            margin: 30px 0;
                        }
                        .print-stat {
                            border: 1px solid #333;
                            padding: 15px;
                            text-align: center;
                            border-radius: 8px;
                        }
                        .print-stat-value {
                            font-size: 24px;
                            font-weight: bold;
                            margin-bottom: 5px;
                        }
                        .print-stat-label {
                            font-size: 12px;
                            color: #666;
                        }
                        .print-table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                            font-size: 11px;
                        }
                        .print-table th, .print-table td {
                            border: 1px solid #333;
                            padding: 8px;
                            text-align: left;
                        }
                        .print-table th {
                            background: #f5f5f5;
                            font-weight: bold;
                        }
                        .print-exam-title {
                            margin-top: 40px;
                            padding-bottom: 10px;
                            border-bottom: 1px solid #333;
                            font-size: 18px;
                            font-weight: bold;
                        }
                        .print-footer {
                            margin-top: 40px;
                            text-align: center;
                            font-size: 11px;
                            color: #666;
                            border-top: 1px solid #333;
                            padding-top: 20px;
                        }
                        .page-break {
                            page-break-after: always;
                        }
                    </style>
                </head>
                <body>
                    <div class="print-header">
                        <h1>Exam Results Report</h1>
                        <p>Class: ${document.querySelector('.class-info').textContent}</p>
                        <p>Generated on: ${new Date().toLocaleDateString()}</p>
                    </div>
                    
                    <div class="print-stats">
                        <div class="print-stat">
                            <div class="print-stat-value"><?= $stats['total_students'] ?></div>
                            <div class="print-stat-label">Total Students</div>
                        </div>
                        <div class="print-stat">
                            <div class="print-stat-value"><?= $stats['total_exams'] ?></div>
                            <div class="print-stat-label">Total Exams</div>
                        </div>
                        <div class="print-stat">
                            <div class="print-stat-value"><?= $stats['total_subjects'] ?></div>
                            <div class="print-stat-label">Total Subjects</div>
                        </div>
                        <div class="print-stat">
                            <div class="print-stat-value"><?= $stats['total_marks'] ?></div>
                            <div class="print-stat-label">Marks Entered</div>
                        </div>
                    </div>
                    
                    ${Array.from(document.querySelectorAll('.exam-accordion')).map((examAccordion, index) => {
                        const examTitle = examAccordion.querySelector('.exam-title h3').textContent;
                        const table = examAccordion.querySelector('.marks-table');
                        
                        if (!table) return '';
                        
                        return `
                            <div class="print-exam-title">
                                ${index + 1}. ${examTitle}
                            </div>
                            ${table.outerHTML}
                            ${index < document.querySelectorAll('.exam-accordion').length - 1 ? '<div class="page-break"></div>' : ''}
                        `;
                    }).join('')}
                    
                    <div class="print-footer">
                        <p>Report generated by Exam Management System | ${new Date().toLocaleString()}</p>
                    </div>
                    
                    <script>
                        window.onload = function() {
                            window.print();
                            setTimeout(function() {
                                window.close();
                            }, 1000);
                        };
                    <\/script>
                </body>
                </html>
            `);
            
            printWindow.document.close();
            closeExportModal();
        }

        // Global variables
        let editedCells = new Set();
        let currentEditCell = null;
        let bulkUpdates = {};
        
        // JavaScript translations
        const jsTranslations = {
            success_marks_saved: "<?= t('success_marks_saved') ?>",
            success_marks_saved_count: "<?= t('success_marks_saved_count', ['count' => '{count}']) ?>",
            success_marks_cleared: "<?= t('success_marks_cleared', ['count' => '{count}']) ?>",
            
            error_no_changes: "<?= t('error_no_changes') ?>",
            error_saving_changes: "<?= t('error_saving_changes') ?>",
            error_clearing_marks: "<?= t('error_clearing_marks') ?>",
            error_network: "<?= t('error_network') ?>",
            error_saving_marks: "<?= t('error_saving_marks') ?>",
            
            validation_marks_range: "<?= t('validation_marks_range', ['max' => '{max}']) ?>",
            
            confirm_clear_marks: "<?= t('confirm_clear_marks') ?>",
            confirm_unsaved_changes: "<?= t('confirm_unsaved_changes') ?>",
            
            status_not_entered: "<?= t('status_not_entered') ?>",
            status_absent: "<?= t('status_absent') ?>",
            status_present: "<?= t('status_present') ?>",
            
            placeholder_enter_marks: "<?= t('placeholder_enter_marks') ?>"
        };
        
        // Toggle exam accordion
        function toggleExam(header) {
            const accordion = header.closest('.exam-accordion');
            const content = accordion.querySelector('.exam-content');
            const icon = header.querySelector('.fa-chevron-down');
            
            if (content.classList.contains('expanded')) {
                content.classList.remove('expanded');
                header.classList.remove('active');
                icon.style.transform = 'rotate(0deg)';
            } else {
                // Close all other exams
                document.querySelectorAll('.exam-content.expanded').forEach(el => {
                    el.classList.remove('expanded');
                    el.previousElementSibling.classList.remove('active');
                    el.previousElementSibling.querySelector('.fa-chevron-down').style.transform = 'rotate(0deg)';
                });
                
                content.classList.add('expanded');
                header.classList.add('active');
                icon.style.transform = 'rotate(180deg)';
            }
        }
        
        // Start editing a cell
        function startEdit(cell) {
            if (currentEditCell) {
                finishEdit(currentEditCell);
            }
            
            const content = cell.querySelector('.cell-content');
            const marks = content.querySelector('span')?.textContent.trim();
            const isAbsent = cell.querySelector('.cell-status').classList.contains('status-absent');
            
            if (isAbsent) return; // Don't edit if absent
            
            cell.classList.add('editing');
            const maxMarks = cell.dataset.maxMarks;
            
            const input = document.createElement('input');
            input.type = 'number';
            input.className = 'cell-input';
            input.value = marks === jsTranslations.status_not_entered ? '' : marks;
            input.min = 0;
            input.max = maxMarks;
            input.step = 0.01;
            input.placeholder = jsTranslations.placeholder_enter_marks || 'Enter marks';
            
            input.onblur = () => finishEdit(cell);
            input.onkeydown = (e) => {
                if (e.key === 'Enter') finishEdit(cell);
                if (e.key === 'Escape') cancelEdit(cell);
            };
            
            content.style.display = 'none';
            cell.appendChild(input);
            input.focus();
            input.select();
            
            currentEditCell = cell;
        }
        
        // Finish editing a cell
        function finishEdit(cell) {
            if (!cell.classList.contains('editing')) return;
            
            const input = cell.querySelector('.cell-input');
            const content = cell.querySelector('.cell-content');
            const marks = input.value.trim();
            const maxMarks = parseFloat(cell.dataset.maxMarks);
            
            // Validate marks
            if (marks !== '' && (parseFloat(marks) < 0 || parseFloat(marks) > maxMarks)) {
                alert(jsTranslations.validation_marks_range.replace('{max}', maxMarks));
                input.focus();
                return;
            }
            
            // Update display
            content.innerHTML = '';
            if (marks !== '') {
                const marksSpan = document.createElement('span');
                marksSpan.style.fontWeight = '600';
                marksSpan.textContent = marks;
                content.appendChild(marksSpan);
            } else {
                const span = document.createElement('span');
                span.style.color = '<?= $isDark ? "var(--dark-secondary)" : "var(--light-secondary)" ?>';
                span.style.fontStyle = 'italic';
                span.textContent = jsTranslations.status_not_entered;
                content.appendChild(span);
            }
            
            // Clean up
            input.remove();
            content.style.display = 'block';
            cell.classList.remove('editing');
            
            // Track changes
            const key = `${cell.dataset.examId}_${cell.dataset.studentId}_${cell.dataset.subjectId}`;
            editedCells.add(key);
            
            if (!bulkUpdates[cell.dataset.examId]) {
                bulkUpdates[cell.dataset.examId] = [];
            }
            
            bulkUpdates[cell.dataset.examId].push({
                exam_id: cell.dataset.examId,
                student_id: cell.dataset.studentId,
                subject_id: cell.dataset.subjectId,
                marks: marks,
                status: cell.querySelector('.cell-status').classList.contains('status-absent') ? 'absent' : 'present'
            });
            
            currentEditCell = null;
        }
        
        // Cancel editing
        function cancelEdit(cell) {
            const input = cell.querySelector('.cell-input');
            const content = cell.querySelector('.cell-content');
            
            input.remove();
            content.style.display = 'block';
            cell.classList.remove('editing');
            currentEditCell = null;
        }
        
        // Toggle status menu
        function toggleStatusMenu(event, statusElement) {
            event.stopPropagation();
            
            const cell = statusElement.closest('.editable-cell');
            const menu = cell.querySelector('.status-toggle');
            
            // Close all other menus
            document.querySelectorAll('.status-toggle.show').forEach(m => {
                if (m !== menu) m.classList.remove('show');
            });
            
            menu.classList.toggle('show');
            
            // Position menu
            const rect = statusElement.getBoundingClientRect();
            menu.style.position = 'fixed';
            menu.style.top = `${rect.bottom + 5}px`;
            menu.style.left = `${rect.left}px`;
        }
        
        // Set status
        function setStatus(option, status) {
            const menu = option.closest('.status-toggle');
            const cell = menu.closest('.editable-cell');
            const statusElement = cell.querySelector('.cell-status');
            const content = cell.querySelector('.cell-content');
            
            // Update status element
            statusElement.className = `cell-status status-${status}`;
            statusElement.innerHTML = `<i class="fas fa-${status === 'absent' ? 'times' : 'check'}"></i>`;
            
            // Update content visibility
            if (status === 'absent') {
                content.style.display = 'none';
            } else {
                content.style.display = 'block';
                if (content.textContent.trim() === jsTranslations.status_not_entered) {
                    // Auto-start editing for present students without marks
                    setTimeout(() => startEdit(cell), 100);
                }
            }
            
            // Track changes
            const key = `${cell.dataset.examId}_${cell.dataset.studentId}_${cell.dataset.subjectId}`;
            editedCells.add(key);
            
            if (!bulkUpdates[cell.dataset.examId]) {
                bulkUpdates[cell.dataset.examId] = [];
            }
            
            bulkUpdates[cell.dataset.examId].push({
                exam_id: cell.dataset.examId,
                student_id: cell.dataset.studentId,
                subject_id: cell.dataset.subjectId,
                marks: status === 'absent' ? null : (
                    cell.querySelector('.cell-content span')?.textContent.trim() || ''
                ),
                status: status
            });
            
            menu.classList.remove('show');
        }
        
        // Save all changes for an exam
        async function saveAllChanges(examId) {
            if (!bulkUpdates[examId] || bulkUpdates[examId].length === 0) {
                alert(jsTranslations.error_no_changes);
                return;
            }
            
            showLoading(true);
            
            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=save_bulk&updates=${encodeURIComponent(JSON.stringify(bulkUpdates[examId]))}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(result.message);
                    // Clear tracked changes
                    bulkUpdates[examId] = [];
                    editedCells.clear();
                    
                    // Refresh page to show updated data
                    setTimeout(() => location.reload(), 1000);
                } else {
                    alert(jsTranslations.error_saving_changes + result.message);
                }
            } catch (error) {
                alert(jsTranslations.error_network + error.message);
            } finally {
                showLoading(false);
            }
        }
        
        // Clear all marks for an exam
        async function clearAllMarks(examId) {
            if (!confirm(jsTranslations.confirm_clear_marks)) {
                return;
            }
            
            showLoading(true);
            
            try {
                // Get all cells for this exam
                const cells = document.querySelectorAll(`.editable-cell[data-exam-id="${examId}"]`);
                const updates = [];
                
                cells.forEach(cell => {
                    updates.push({
                        exam_id: examId,
                        student_id: cell.dataset.studentId,
                        subject_id: cell.dataset.subjectId,
                        marks: '',
                        status: 'present'
                    });
                });
                
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `action=save_bulk&updates=${encodeURIComponent(JSON.stringify(updates))}`
                });
                
                const result = await response.json();
                
                if (result.success) {
                    alert(jsTranslations.success_marks_cleared.replace('{count}', updates.length));
                    location.reload();
                } else {
                    alert(jsTranslations.error_clearing_marks + result.message);
                }
            } catch (error) {
                alert(jsTranslations.error_network + error.message);
            } finally {
                showLoading(false);
            }
        }
        
        // Print exam results
        function printExam(examId) {
            // Remove previous print areas
            document.querySelectorAll('.print-area').forEach(el => {
                el.classList.remove('print-area');
            });

            const exam = document.querySelector(
                `.exam-accordion[data-exam-id="${examId}"]`
            );

            if (!exam) {
                alert('Exam not found');
                return;
            }

            // Expand exam if collapsed
            const content = exam.querySelector('.exam-content');
            if (!content.classList.contains('expanded')) {
                content.classList.add('expanded');
                exam.querySelector('.exam-header').classList.add('active');
            }

            // Mark this exam as printable
            exam.classList.add('print-area');

            // Print
            window.print();

            // Cleanup after print
            window.onafterprint = () => {
                exam.classList.remove('print-area');
            };
        }
        
        // Show/hide loading overlay
        function showLoading(show) {
            document.getElementById('loadingOverlay').classList.toggle('show', show);
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && currentEditCell) {
                cancelEdit(currentEditCell);
            }
            
            // Ctrl+S to save all changes in current exam
            if (e.ctrlKey && e.key === 's') {
                e.preventDefault();
                const expandedExam = document.querySelector('.exam-content.expanded');
                if (expandedExam) {
                    const examId = expandedExam.closest('.exam-accordion').dataset.examId;
                    saveAllChanges(examId);
                }
            }
            
            // Ctrl+E to open export modal
            if (e.ctrlKey && e.key === 'e') {
                e.preventDefault();
                openExportModal();
            }
        });
        
        // Close status menus when clicking outside
        document.addEventListener('click', () => {
            document.querySelectorAll('.status-toggle.show').forEach(menu => {
                menu.classList.remove('show');
            });
        });
        
        // Initialize - expand first exam
        document.addEventListener('DOMContentLoaded', () => {
            const firstExam = document.querySelector('.exam-accordion');
            if (firstExam) {
                const header = firstExam.querySelector('.exam-header');
                toggleExam(header);
            }
        });
        
        // Auto-save on page unload if there are unsaved changes
        window.addEventListener('beforeunload', (e) => {
            if (editedCells.size > 0) {
                e.preventDefault();
                e.returnValue = jsTranslations.confirm_unsaved_changes;
            }
        });
    </script>
</body>
</html>

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

        /* Export Modal */
        .export-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .export-modal-content {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            padding: 40px;
            border-radius: 16px;
            max-width: 500px;
            width: 90%;
            text-align: center;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            position: relative;
        }

        .export-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            font-size: 24px;
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .export-close:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        .export-modal h2 {
            font-size: 24px;
            margin-bottom: 15px;
            color: <?= $isDark ? 'var(--dark-text)' : 'var(--light-text)' ?>;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .export-description {
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            margin-bottom: 30px;
            line-height: 1.6;
        }

        .export-options {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin: 30px 0;
        }

        .export-btn {
            padding: 18px;
            border: none;
            border-radius: 10px;
            font-weight: 600;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 15px;
            width: 100%;
        }

        .export-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        .export-pdf {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .export-excel {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
        }

        .export-print {
            background: linear-gradient(135deg, #3b82f6, #1d4ed8);
            color: white;
        }

        .export-info {
            font-size: 14px;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            margin-top: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        /* Export Loading */
        .export-loading {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.9);
            z-index: 3000;
            justify-content: center;
            align-items: center;
            flex-direction: column;
        }

        .export-spinner {
            width: 60px;
            height: 60px;
            border: 4px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            border-top-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-bottom: 20px;
        }

        .export-loading-text {
            color: white;
            font-size: 18px;
            font-weight: 500;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Header */
        .header {
            margin-bottom: 2rem;
            padding-bottom: 1.5rem;
            border-bottom: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title {
            flex: 1;
        }

        .header-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: <?= $isDark ? 'linear-gradient(135deg, #60a5fa, #3b82f6)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' ?>;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            margin-bottom: 0.5rem;
        }

        .header-title .class-info {
            background: <?= $isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)' ?>;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            border: 1px solid <?= $isDark ? 'rgba(96, 165, 250, 0.2)' : 'rgba(59, 130, 246, 0.2)' ?>;
            font-size: 0.875rem;
            display: inline-block;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .class-selector {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .class-selector select {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            border: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            background: <?= $isDark ? 'var(--dark-input)' : 'var(--light-input)' ?>;
            color: inherit;
            font-weight: 500;
            min-width: 200px;
        }

        .btn-export {
            background: linear-gradient(135deg, #8b5cf6, #7c3aed);
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            border: none;
            transition: all 0.2s ease;
        }

        .btn-export:hover {
            background: linear-gradient(135deg, #7c3aed, #6d28d9);
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        /* Stats Grid */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            padding: 1.25rem;
            border-radius: 12px;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            text-align: center;
            transition: transform 0.2s ease;
        }

        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
        }

        .stat-card:nth-child(1) .value { color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>; }
        .stat-card:nth-child(2) .value { color: <?= $isDark ? 'var(--dark-warning)' : 'var(--light-warning)' ?>; }
        .stat-card:nth-child(3) .value { color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>; }
        .stat-card:nth-child(4) .value { color: <?= $isDark ? 'var(--dark-info)' : 'var(--light-info)' ?>; }

        .stat-card .label {
            font-size: 0.75rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
        }

        /* Exams Accordion */
        .exams-container {
            margin-top: 2rem;
        }

        .exam-accordion {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 12px;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            margin-bottom: 1rem;
            overflow: hidden;
        }

        .exam-header {
            padding: 1.25rem 1.5rem;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.2s ease;
        }

        .exam-header:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        .exam-header.active {
            background: <?= $isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)' ?>;
            border-bottom: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .exam-title {
            display: flex;
            align-items: center;
            gap: 1rem;
            flex: 1;
        }

        .exam-title h3 {
            font-size: 1.125rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .exam-info {
            display: flex;
            gap: 1rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            font-size: 0.875rem;
        }

        .exam-stats {
            display: flex;
            gap: 1rem;
            margin-left: 2rem;
        }

        .exam-stat {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            border-radius: 6px;
            background: <?= $isDark ? 'rgba(30, 41, 59, 0.5)' : 'rgba(241, 245, 249, 0.5)' ?>;
        }

        .exam-content {
            padding: 0;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease, padding 0.3s ease;
        }

        .exam-content.expanded {
            padding: 1.5rem;
            max-height: 5000px;
        }

        /* Badges */
        .badge {
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
        }

        .badge-midterm {
            background: rgba(245, 158, 11, 0.1);
            color: <?= $isDark ? 'var(--dark-warning)' : 'var(--light-warning)' ?>;
            border: 1px solid rgba(245, 158, 11, 0.2);
        }

        .badge-final {
            background: rgba(59, 130, 246, 0.1);
            color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .badge-success {
            background: rgba(16, 185, 129, 0.1);
            color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
            border: 1px solid rgba(16, 185, 129, 0.2);
        }

        .badge-danger {
            background: rgba(239, 68, 68, 0.1);
            color: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
            border: 1px solid rgba(239, 68, 68, 0.2);
        }

        /* Marks Table */
        .marks-table-container {
            overflow-x: auto;
            margin-top: 1.5rem;
            border-radius: 8px;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .marks-table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        .marks-table th {
            background: <?= $isDark ? '#0f172a' : '#f1f5f9' ?>;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            border-bottom: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .marks-table td {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            vertical-align: middle;
        }

        .marks-table tbody tr:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        .student-col {
            /* position: sticky; */
            left: 0;
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            z-index: 5;
            min-width: 200px;
            border-right: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .subject-col {
            min-width: 120px;
        }

        /* Editable Cells */
        .editable-cell {
            cursor: pointer;
            transition: background-color 0.2s ease;
            position: relative;
            min-height: 40px;
            display: flex;
            align-items: center;
        }

        .editable-cell:hover {
            background: <?= $isDark ? 'rgba(96, 165, 250, 0.1)' : 'rgba(59, 130, 246, 0.1)' ?>;
        }

        .editable-cell.editing {
            padding: 0;
        }

        .cell-input {
            width: 100%;
            height: 100%;
            padding: 0.5rem;
            border: 2px solid <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            border-radius: 4px;
            background: <?= $isDark ? 'var(--dark-input)' : 'var(--light-input)' ?>;
            color: inherit;
            font-size: 0.875rem;
            outline: none;
        }

        .cell-status {
            position: absolute;
            top: -8px;
            right: -8px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.6rem;
            cursor: pointer;
        }

        .status-present {
            background: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
            color: white;
        }

        .status-absent {
            background: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
            color: white;
        }

        .status-toggle {
            position: absolute;
            top: 100%;
            left: 0;
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            border-radius: 4px;
            padding: 0.5rem;
            display: none;
            z-index: 100;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .status-toggle.show {
            display: block;
        }

        .status-option {
            padding: 0.25rem 0.5rem;
            cursor: pointer;
            border-radius: 4px;
            margin-bottom: 0.25rem;
        }

        .status-option:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
            padding-top: 1rem;
            border-top: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.875rem;
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
            transform: translateY(-1px);
        }

        .btn-success {
            background: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
            color: white;
        }

        .btn-danger {
            background: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
            color: white;
        }

        .btn-secondary {
            background: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            color: white;
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            display: none;
        }

        .loading-overlay.show {
            display: flex;
        }

        .spinner {
            width: 50px;
            height: 50px;
            border: 3px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            border-top-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        /* No Data */
        .no-data {
            text-align: center;
            padding: 3rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .exam-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1rem;
            }
            
            .exam-stats {
                margin-left: 0;
                width: 100%;
                justify-content: space-between;
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .header-top {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header-actions {
                width: 100%;
                flex-direction: column;
                align-items: stretch;
            }
            
            .class-selector select {
                width: 100%;
            }
            
            .btn-export {
                width: 100%;
                justify-content: center;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .marks-table-container {
                margin: 0 -1rem;
                border-radius: 0;
                border-left: none;
                border-right: none;
            }
            
            .export-modal-content {
                padding: 30px 20px;
            }
            
            .export-btn {
                padding: 15px;
                font-size: 14px;
            }
        }

        @media (max-width: 480px) {
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .exam-info {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .exam-stats {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        /* Print Styles */
        @media print {
            .action-buttons,
            .class-selector,
            .exam-header .fa-chevron-down,
            .btn-export,
            .export-modal {
                display: none !important;
            }
            
            .exam-content {
                max-height: none !important;
                padding: 1.5rem !important;
                display: block !important;
            }
            
            .marks-table th {
                position: static !important;
            }
            
            .student-col {
                position: static !important;
            }
        }
        /* ===== PRINT MODE ===== */
        @media print {
            body * {
                visibility: hidden !important;
            }

            .exam-accordion.print-area,
            .exam-accordion.print-area * {
                visibility: visible !important;
            }

            .exam-accordion.print-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            /* Hide buttons & controls */
            .btn,
            .cell-status,
            .status-toggle {
                display: none !important;
            }

            /* Better table look */
            table {
                border-collapse: collapse !important;
                width: 100%;
            }

            th, td {
                border: 1px solid #000 !important;
                padding: 6px !important;
                font-size: 12px;
            }

            .badge-success { color: green !important; }
            .badge-danger  { color: red !important; }
        }

        @media print {
            @page {
                size: A4 landscape;
                margin: 10mm;
            }

            table {
                width: 100%;
                table-layout: fixed;
            }

            th, td {
                font-size: 10px !important;
                word-wrap: break-word;
            }
        }
        
        .pagination {
            margin: 20px 0;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 6px 12px;
            margin: 2px;
            border: 1px solid #ccc;
            text-decoration: none;
            color: #333;
        }

        .pagination a.active {
            background: #007bff;
            color: #fff;
            border-color: #007bff;
        }
    </style>