<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Add Alumni - Student Management System',
        'add_alumni' => 'Add Alumni',
        'select_section' => 'Select Section:',
        'select_class' => 'Select Class:',
        'load_students' => 'Load Students',
        'alumni_criteria' => 'Alumni Criteria for Selected Class',
        'minimum_exam_score' => 'Minimum Exam Score:',
        'minimum_attendance' => 'Minimum Attendance (%):',
        'require_both' => 'Require Both Conditions',
        'not_set' => 'Not set',
        'yes' => 'Yes',
        'no' => 'No',
        'edit_criteria' => 'Edit Criteria',
        'save_criteria' => 'Save Criteria',
        'cancel' => 'Cancel',
        'exam_performance' => 'Exam Performance',
        'attendance_performance' => 'Attendance Performance',
        'both' => 'Both',
        'loading' => 'Loading...',
        'error_loading' => 'Error loading data',
        'mark_alumni' => 'Mark this student as alumni?',
        'student_marked' => 'Student successfully marked as alumni!',
        'already_alumni' => 'Student is already an alumni!',
        'criteria_updated' => 'Alumni criteria updated successfully!',
        'error_criteria' => 'Error updating criteria',
        'error_alumni' => 'Error marking as alumni'
    ],
    2 => [ // Arabic
        'page_title' => 'إضافة خريج - نظام إدارة الطلاب',
        'add_alumni' => 'إضافة خريج',
        'select_section' => 'اختر القسم:',
        'select_class' => 'اختر الصف:',
        'load_students' => 'تحميل الطلاب',
        'alumni_criteria' => 'معايير الخريجين للصف المحدد',
        'minimum_exam_score' => 'الحد الأدنى لنتيجة الامتحان:',
        'minimum_attendance' => 'الحد الأدنى للحضور (%):',
        'require_both' => 'يتطلب كلا الشرطين',
        'not_set' => 'غير محدد',
        'yes' => 'نعم',
        'no' => 'لا',
        'edit_criteria' => 'تعديل المعايير',
        'save_criteria' => 'حفظ المعايير',
        'cancel' => 'إلغاء',
        'exam_performance' => 'أداء الامتحان',
        'attendance_performance' => 'أداء الحضور',
        'both' => 'كلاهما',
        'loading' => 'جاري التحميل...',
        'error_loading' => 'خطأ في تحميل البيانات',
        'mark_alumni' => 'تحديد هذا الطالب كخريج؟',
        'student_marked' => 'تم تحديد الطالب كخريج بنجاح!',
        'already_alumni' => 'الطالب خريج بالفعل!',
        'criteria_updated' => 'تم تحديث معايير الخريجين بنجاح!',
        'error_criteria' => 'خطأ في تحديث المعايير',
        'error_alumni' => 'خطأ في تحديد الخريج'
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

if (empty($_SESSION['user_id'])) {
    header('Location: ../../login.php');
    exit;
}

// theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0);

// Initialize message variables
$message = '';
$message_type = '';

// Handle alumni creation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['make_alumni'])) {
        $student_id = intval($_POST['student_id']);
        $class_id = intval($_POST['class_id']);
        $section_id = intval($_POST['section_id']);
        $graduation_reason = $conn->real_escape_string($_POST['graduation_reason']);

        // Check if already alumni in this class
        $check = $conn->prepare("SELECT id FROM alumni WHERE student_id = ? AND class_id = ?");
        $check->bind_param("ii", $student_id, $class_id);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows == 0) {
            // Always calculate exam_score
            $exam_score = 0;
            $exam_q = $conn->prepare("
                SELECT SUM(er.obtained_marks) AS total_obtained,
                       SUM(es.max_marks) AS total_max
                FROM exam_results er
                INNER JOIN exams e ON er.exam_id = e.id
                INNER JOIN exam_subjects es ON er.exam_id = es.exam_id AND er.subject_id = es.subject_id
                WHERE er.student_id = ? AND er.class_id = ? AND er.status = 'present'
            ");
            $exam_q->bind_param("ii", $student_id, $class_id);
            $exam_q->execute();
            $res = $exam_q->get_result()->fetch_assoc();
            if ($res && $res['total_max'] > 0) {
                $exam_score = ($res['total_obtained'] / $res['total_max']) * 100;
            }

            // Always calculate attendance_percentage
            $attendance_percentage = 0;
            $att_q = $conn->prepare("
                SELECT COUNT(DISTINCT a.date) AS total_days,
                       COUNT(DISTINCT CASE WHEN a.status IN ('present','late','excused') THEN a.date END) AS present_days
                FROM attendance a
                INNER JOIN class_students cs ON cs.student_id = a.student_id AND cs.class_id = a.class_id
                WHERE a.student_id = ? AND cs.class_id = ?
            ");
            $att_q->bind_param("ii", $student_id, $class_id);
            $att_q->execute();
            $res = $att_q->get_result()->fetch_assoc();
            $total_days = (int)($res['total_days'] ?? 0);
            $present_days = (int)($res['present_days'] ?? 0);
            $attendance_percentage = $total_days > 0 ? ($present_days / $total_days) * 100 : 0;

            // Insert into alumni
            $stmt = $conn->prepare("
                INSERT INTO alumni 
                (student_id, class_id, section_id, graduation_date, graduation_reason, exam_score, attendance_percentage)
                VALUES (?, ?, ?, CURDATE(), ?, ?, ?)
            ");
            $stmt->bind_param("iiisdd", $student_id, $class_id, $section_id, $graduation_reason, $exam_score, $attendance_percentage);
            
            if ($stmt->execute()) {
                $message = t('student_marked');
                $message_type = "success";
            } else {
                $message = t('error_alumni') . ": " . $stmt->error;
                $message_type = "error";
            }
        } else {
            $message = t('already_alumni');
            $message_type = "warning";
        }

        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $message_type;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    // Update criteria
    if (isset($_POST['update_criteria'])) {
        $class_id = intval($_POST['class_id']);
        $section_id = intval($_POST['section_id']);
        $min_exam_score = floatval($_POST['min_exam_score']);
        $min_attendance = floatval($_POST['min_attendance']);
        $require_both = isset($_POST['require_both']) ? 1 : 0;

        $check = $conn->prepare("SELECT id FROM alumni_criteria WHERE class_id = ? AND section_id = ?");
        $check->bind_param("ii", $class_id, $section_id);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            $stmt = $conn->prepare("UPDATE alumni_criteria SET min_exam_score = ?, min_attendance_percentage = ?, require_both = ? WHERE class_id = ? AND section_id = ?");
            $stmt->bind_param("ddiii", $min_exam_score, $min_attendance, $require_both, $class_id, $section_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO alumni_criteria (class_id, section_id, min_exam_score, min_attendance_percentage, require_both) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("iiddi", $class_id, $section_id, $min_exam_score, $min_attendance, $require_both);
        }

        if ($stmt->execute()) {
            $message = t('criteria_updated');
            $message_type = "success";
        } else {
            $message = t('error_criteria') . ": " . $stmt->error;
            $message_type = "error";
        }

        // Redirect to avoid resubmission
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $message_type;
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}

// Display flash message if any
if (isset($_SESSION['flash_message'])) {
    $message = $_SESSION['flash_message'];
    $message_type = $_SESSION['flash_type'];
    unset($_SESSION['flash_message'], $_SESSION['flash_type']);
}

// Get sections for dropdown
$sections = $conn->query("SELECT id, name FROM sections ORDER BY name");
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Theme Tweaks */
        .ts-control {
            border-radius: 5px;
            padding: 10px;
            border-color: #ddd;
        }
        .ts-wrapper.single .ts-control {
            background-image: none;
        }
        body.dark .ts-control {
            background-color: #3d3d3d;
            border-color: #555;
            color: #fff;
        }
        body.dark .ts-dropdown {
            background-color: #3d3d3d;
            border-color: #555;
            color: #fff;
        }
        body.dark .ts-dropdown .option {
            color: #fff;
        }
        body.dark .ts-dropdown .active {
            background-color: #555;
            color: #fff;
        }
        body.dark .ts-control input {
            color: #fff;
        }
    </style></head>
<body class="<?= $is_dark ? 'dark' : 'light' ?>">
    <div class="container">
        <h2><?= t('add_alumni') ?></h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Selection Form -->
        <form id="selectionForm" method="GET">
            <div class="form-group">
                <label for="section"><?= t('select_section') ?></label>
                <select id="section" name="section" class="form-control" required onchange="loadClasses()">
                    <option value="">-- <?= t('select_section') ?> --</option>
                    <?php while ($section = $sections->fetch_assoc()): ?>
                        <option value="<?= $section['id'] ?>" <?= isset($_GET['section']) && $_GET['section'] == $section['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($section['name']) ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="class"><?= t('select_class') ?></label>
                <select id="class" name="class" class="form-control" required <?= !isset($_GET['section']) ? 'disabled' : '' ?> onchange="loadStudents()">
                    <option value="">-- <?= t('select_class') ?> --</option>
                    <?php if (isset($_GET['section'])): ?>
                        <?php 
                        $section_id = intval($_GET['section']);
                        $classes = $conn->query("SELECT c.id, c.name FROM classes c WHERE c.section_id = $section_id ORDER BY c.name");
                        while ($class = $classes->fetch_assoc()):
                        ?>
                            <option value="<?= $class['id'] ?>" <?= isset($_GET['class']) && $_GET['class'] == $class['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($class['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </select>
            </div>

            <?php if (isset($_GET['class'])): ?>
                <button type="submit" class="btn btn-primary"><?= t('load_students') ?></button>
            <?php endif; ?>
        </form>

        <?php if (isset($_GET['class']) && isset($_GET['section'])): ?>
            <?php
            $class_id = intval($_GET['class']);
            $section_id = intval($_GET['section']);
            
            // Get alumni criteria for this class
            $criteria_result = $conn->prepare("SELECT * FROM alumni_criteria WHERE class_id = ? AND section_id = ?");
            $criteria_result->bind_param("ii", $class_id, $section_id);
            $criteria_result->execute();
            $criteria = $criteria_result->get_result()->fetch_assoc();
            $criteria_result->close();
            ?>

            <!-- Alumni Criteria Section -->
            <div class="criteria-box">
                <h3><?= t('alumni_criteria') ?></h3>
                <div id="criteriaDisplay" class="<?= $criteria ? '' : 'hidden' ?>">
                    <div class="criteria-row">
                        <div class="criteria-item">
                            <label><?= t('minimum_exam_score') ?></label>
                            <span id="displayExamScore"><?= $criteria ? $criteria['min_exam_score'] : t('not_set') ?></span>
                        </div>
                        <div class="criteria-item">
                            <label><?= t('minimum_attendance') ?></label>
                            <span id="displayAttendance"><?= $criteria ? $criteria['min_attendance_percentage'] : t('not_set') ?></span>
                        </div>
                        <div class="criteria-item">
                            <label><?= t('require_both') ?>:</label>
                            <span id="displayRequireBoth"><?= $criteria && $criteria['require_both'] ? t('yes') : t('no') ?></span>
                        </div>
                    </div>
                    <button type="button" class="btn btn-warning" onclick="toggleCriteriaEdit()"><?= t('edit_criteria') ?></button>
                </div>

                <form id="criteriaForm" method="POST" class="<?= $criteria ? 'hidden' : '' ?>">
                    <input type="hidden" name="class_id" value="<?= $class_id ?>">
                    <input type="hidden" name="section_id" value="<?= $section_id ?>">
                    <div class="criteria-row">
                        <div class="criteria-item">
                            <label for="min_exam_score"><?= t('minimum_exam_score') ?></label>
                            <input type="number" id="min_exam_score" name="min_exam_score" class="form-control" step="0.01" min="0" value="<?= $criteria ? $criteria['min_exam_score'] : '0' ?>">
                        </div>
                        <div class="criteria-item">
                            <label for="min_attendance"><?= t('minimum_attendance') ?></label>
                            <input type="number" id="min_attendance" name="min_attendance" class="form-control" step="0.01" min="0" max="100" value="<?= $criteria ? $criteria['min_attendance_percentage'] : '0' ?>">
                        </div>
                        <div class="criteria-item">
                            <label>&nbsp;</label>
                            <div>
                                <input type="checkbox" id="require_both" name="require_both" value="1" <?= $criteria && $criteria['require_both'] ? 'checked' : '' ?>>
                                <label for="require_both"><?= t('require_both') ?></label>
                            </div>
                        </div>
                    </div>
                    <div class="criteria-row">
                        <button type="submit" name="update_criteria" class="btn btn-success"><?= t('save_criteria') ?></button>
                        <button type="button" class="btn btn-outline-secondary" onclick="toggleCriteriaEdit()"><?= t('cancel') ?></button>
                    </div>
                </form>
            </div>

            <!-- Performance Buttons -->
            <div class="btn-group">
                <button class="btn btn-primary" onclick="loadPerformance('exam')"><?= t('exam_performance') ?></button>
                <button class="btn btn-warning" onclick="loadPerformance('attendance')"><?= t('attendance_performance') ?></button>
                <button class="btn btn-success" onclick="loadPerformance('both')"><?= t('both') ?></button>
            </div>

            <!-- Loading Indicator -->
            <div id="loading" class="loading hidden">
                <?= t('loading') ?>
            </div>

            <!-- Results Table -->
            <div id="resultsContainer"></div>
        <?php endif; ?>
    </div>

    <script>
        function loadClasses() {
            const section = document.getElementById('section').value;
            const classSelect = document.getElementById('class');
            
            if (section) {
                classSelect.disabled = false;
                // In a real implementation, you would fetch classes via AJAX
                // For now, just submit the form
                document.getElementById('selectionForm').submit();
            } else {
                classSelect.disabled = true;
                classSelect.innerHTML = '<option value="">-- <?= t('select_class') ?> --</option>';
            }
        }

        function loadStudents() {
            document.getElementById('selectionForm').submit();
        }

        function toggleCriteriaEdit() {
            const display = document.getElementById('criteriaDisplay');
            const form = document.getElementById('criteriaForm');
            
            if (display.classList.contains('hidden')) {
                display.classList.remove('hidden');
                form.classList.add('hidden');
            } else {
                display.classList.add('hidden');
                form.classList.remove('hidden');
            }
        }

        function loadPerformance(type) {
            const classId = <?= isset($_GET['class']) ? $_GET['class'] : 'null' ?>;
            const sectionId = <?= isset($_GET['section']) ? $_GET['section'] : 'null' ?>;
            
            if (!classId || !sectionId) return;
            
            document.getElementById('loading').classList.remove('hidden');
            document.getElementById('resultsContainer').innerHTML = '';
            
            const formData = new FormData();
            formData.append('class_id', classId);
            formData.append('section_id', sectionId);
            formData.append('performance_type', type);
            
            fetch('ajax_get_alumni_performance.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(html => {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('resultsContainer').innerHTML = html;
            })
            .catch(error => {
                document.getElementById('loading').classList.add('hidden');
                document.getElementById('resultsContainer').innerHTML = '<div class="alert alert-error"><?= t('error_loading') ?></div>';
            });
        }

        function makeAlumni(studentId, graduationReason) {
            if (!confirm('<?= t('mark_alumni') ?>')) return;
            
            const formData = new FormData();
            formData.append('student_id', studentId);
            formData.append('class_id', <?= isset($_GET['class']) ? $_GET['class'] : '0' ?>);
            formData.append('section_id', <?= isset($_GET['section']) ? $_GET['section'] : '0' ?>);
            formData.append('graduation_reason', graduationReason);
            formData.append('make_alumni', '1');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (response.ok) {
                    window.location.reload();
                }
            });
        }

        // Auto-load exam performance if class is selected
        });

        // Initialize Tom Select
        document.addEventListener('DOMContentLoaded', function() {
            // Section Select
            new TomSelect('#section', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['remove_button']
            });

            // Class Select
            new TomSelect('#class', {
                create: false,
                sortField: { field: "text", direction: "asc" },
                plugins: ['remove_button']
            });

            <?php if (isset($_GET['class']) && isset($_GET['section'])): ?>
                loadPerformance('exam');
            <?php endif; ?>
        });
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

        body.dark .container {
            background-color: #2d2d2d;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        body.dark .form-control,
        body.dark .table,
        body.dark .btn-outline-secondary {
            background-color: #3d3d3d;
            color: #fff;
            border-color: #555;
        }

        body.dark .table {
            background-color: #2d2d2d;
        }

        body.dark .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        body.dark .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        body.dark .criteria-box {
            background-color: #3d3d3d;
            border-color: #555;
        }

        .container {
            max-width: 1400px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .form-control:disabled {
            background-color: #f8f9fa;
            cursor: not-allowed;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            margin-right: 10px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #1e7e34;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        .btn-group {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .btn-group .btn {
            flex: 1;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        body.dark .alert-success {
            background-color: #155724;
            color: #d4edda;
            border-color: #0c3d1a;
        }

        body.dark .alert-error {
            background-color: #721c24;
            color: #f8d7da;
            border-color: #5a151b;
        }

        body.dark .alert-warning {
            background-color: #856404;
            color: #fff3cd;
            border-color: #6b5203;
        }

        .eligible {
            background-color: #d4edda !important;
            color: #155724;
        }

        body.dark .eligible {
            background-color: #155724 !important;
            color: #d4edda;
        }

        .not-eligible {
            background-color: #f8d7da !important;
            color: #721c24;
        }

        body.dark .not-eligible {
            background-color: #721c24 !important;
            color: #f8d7da;
        }

        .criteria-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 20px;
        }

        .criteria-row {
            display: flex;
            gap: 20px;
            margin-bottom: 10px;
        }

        .criteria-item {
            flex: 1;
        }

        .criteria-item label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .edit-btn {
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
            padding: 5px 10px;
        }

        .edit-btn:hover {
            text-decoration: underline;
        }

        .hidden {
            display: none;
        }

        .student-selection {
            margin-bottom: 20px;
        }

        .student-checkbox {
            margin-right: 10px;
        }

        .bulk-actions {
            margin-top: 20px;
            padding-top: 15px;
            border-top: 1px solid #dee2e6;
        }

        .loading {
            text-align: center;
            padding: 20px;
            color: #6c757d;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            
            .criteria-row {
                flex-direction: column;
                gap: 10px;
            }
            
            .btn-group {
                flex-direction: column;
            }
        }
    </style>