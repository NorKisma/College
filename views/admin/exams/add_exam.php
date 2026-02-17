<?php
declare(strict_types=1);

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
        'page_title' => 'Launch New Exam | Academic Portal',
        'header_title' => 'Launch New Exam',
        'header_subtitle' => 'Create and configure a new examination for your students',
        
        // Form Labels
        'label_academic_year' => 'Academic Year',
        'label_exam_type' => 'Exam Type',
        'label_section' => 'Section',
        'label_class' => 'Class',
        'placeholder_select_year' => 'Select Academic Year',
        'placeholder_select_type' => 'Select Exam Type',
        'placeholder_select_section' => 'Select Section',
        'placeholder_select_class' => 'Select Section First',
        'option_midterm' => 'Midterm Examination',
        'option_final' => 'Final Examination',
        
        // Bulk Configuration
        'bulk_title' => 'Bulk Configuration',
        'bulk_max_marks' => 'Apply Same Max Marks',
        'bulk_pass_marks' => 'Apply Same Pass Marks',
        'placeholder_max_marks' => 'Enter max marks',
        'placeholder_pass_marks' => 'Enter pass marks',
        'bulk_apply_button' => 'Apply to All Subjects',
        
        // Subjects Section
        'subjects_title' => 'Subjects Configuration',
        'subjects_loading' => 'Loading subjects...',
        'no_subjects_message' => 'No subjects found for the selected class. Please select a different class.',
        'error_loading_subjects' => 'Error loading subjects. Please try again.',
        
        // Table Headers
        'table_subject_name' => 'Subject Name',
        'table_max_marks' => 'Max Marks',
        'table_pass_marks' => 'Pass Marks',
        'placeholder_subject_max' => 'Max marks',
        'placeholder_subject_pass' => 'Pass marks',
        
        // Success Messages
        'success_exam_launched' => 'Exam launched successfully!',
        'success_toast_exam' => 'Exam launched successfully!',
        'success_bulk_applied' => 'Marks applied to all subjects',
        
        // Error Messages
        'error_invalid_year' => 'Invalid academic year format.',
        'error_no_subjects' => 'No subjects selected for this exam.',
        'error_duplicate_exam' => 'An exam of type {exam_type} already exists for this class and section in the last 6 months.',
        'error_loading_classes' => 'Error loading classes',
        'error_no_subjects_available' => 'No subjects available',
        'error_validation_class' => 'Please select a class with subjects',
        'error_validation_marks' => 'Please fill all marks fields',
        'error_at_least_one_value' => 'Please enter at least one value to apply',
        
        // Form Actions
        'btn_back_to_exams' => 'Back to Exams',
        'btn_launch_exam' => 'Launch Exam',
        
        // Alert Titles
        'alert_success' => 'Success!',
        'alert_error' => 'Error!',
        
        // Toast Messages
        'toast_type_success' => 'success',
        'toast_type_error' => 'error',
        'toast_type_warning' => 'warning',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إطلاق امتحان جديد | البوابة الأكاديمية',
        'header_title' => 'إطلاق امتحان جديد',
        'header_subtitle' => 'إنشاء وتكوين امتحان جديد لطلابك',
        
        // Form Labels
        'label_academic_year' => 'السنة الأكاديمية',
        'label_exam_type' => 'نوع الامتحان',
        'label_section' => 'القسم',
        'label_class' => 'الصف',
        'placeholder_select_year' => 'اختر السنة الأكاديمية',
        'placeholder_select_type' => 'اختر نوع الامتحان',
        'placeholder_select_section' => 'اختر القسم',
        'placeholder_select_class' => 'اختر القسم أولاً',
        'option_midterm' => 'امتحان منتصف الفصل',
        'option_final' => 'امتحان نهاية الفصل',
        
        // Bulk Configuration
        'bulk_title' => 'الإعدادات الجماعية',
        'bulk_max_marks' => 'تطبيق نفس الدرجة القصوى',
        'bulk_pass_marks' => 'تطبيق نفس درجة النجاح',
        'placeholder_max_marks' => 'أدخل الدرجة القصوى',
        'placeholder_pass_marks' => 'أدخل درجة النجاح',
        'bulk_apply_button' => 'تطبيق على جميع المواد',
        
        // Subjects Section
        'subjects_title' => 'تكوين المواد',
        'subjects_loading' => 'جاري تحميل المواد...',
        'no_subjects_message' => 'لم يتم العثور على مواد للصف المحدد. الرجاء اختيار صف مختلف.',
        'error_loading_subjects' => 'خطأ في تحميل المواد. الرجاء المحاولة مرة أخرى.',
        
        // Table Headers
        'table_subject_name' => 'اسم المادة',
        'table_max_marks' => 'الدرجة القصوى',
        'table_pass_marks' => 'درجة النجاح',
        'placeholder_subject_max' => 'الدرجة القصوى',
        'placeholder_subject_pass' => 'درجة النجاح',
        
        // Success Messages
        'success_exam_launched' => 'تم إطلاق الامتحان بنجاح!',
        'success_toast_exam' => 'تم إطلاق الامتحان بنجاح!',
        'success_bulk_applied' => 'تم تطبيق الدرجات على جميع المواد',
        
        // Error Messages
        'error_invalid_year' => 'تنسيق السنة الأكاديمية غير صالح.',
        'error_no_subjects' => 'لم يتم اختيار أي مواد لهذا الامتحان.',
        'error_duplicate_exam' => 'امتحان من نوع {exam_type} موجود بالفعل لهذا الصف والقسم في آخر 6 أشهر.',
        'error_loading_classes' => 'خطأ في تحميل الصفوف',
        'error_no_subjects_available' => 'لا توجد مواد متاحة',
        'error_validation_class' => 'الرجاء اختيار صف به مواد',
        'error_validation_marks' => 'الرجاء ملء جميع حقول الدرجات',
        'error_at_least_one_value' => 'الرجاء إدخال قيمة واحدة على الأقل للتطبيق',
        
        // Form Actions
        'btn_back_to_exams' => 'العودة إلى الامتحانات',
        'btn_launch_exam' => 'إطلاق الامتحان',
        
        // Alert Titles
        'alert_success' => 'نجاح!',
        'alert_error' => 'خطأ!',
        
        // Toast Messages
        'toast_type_success' => 'success',
        'toast_type_error' => 'error',
        'toast_type_warning' => 'warning',
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

$msg = "";
$error = "";

// ================= THEME =================
// Get the latest theme (0 = dark, 1 = light) - LOGIC UNCHANGED
$theme_res = $conn->query("SELECT mode FROM theme ORDER BY updated_at DESC LIMIT 1");
if ($theme_res && $theme_row = $theme_res->fetch_assoc()) {
    $theme_mode = (int)$theme_row['mode'];
} else {
    $theme_mode = 1; // default light mode
}

// Load sections
$sections = $conn->query("SELECT id, name FROM sections");

// ================= EXAM SUBMISSION =================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $academic_year = $_POST['academic_year'] ?? '';

    if (!preg_match('/^\d{4}-\d{4}$/', $academic_year)) {
        $error = "❌ " . t('error_invalid_year');
    }

    $exam_type  = $_POST['exam_type']; // midterm / final
    $section_id = (int)$_POST['section_id'];
    $class_id   = (int)$_POST['class_id'];

    // Check subjects
    if (!isset($_POST['subjects']) || !is_array($_POST['subjects']) || count($_POST['subjects']) === 0) {
        $error = "❌ " . t('error_no_subjects');
    }

    if (!$error) {
        // Check for duplicate exam in last 6 months
        $sixMonthsAgo = date('Y-m-d', strtotime('-6 months'));
        $check = $conn->prepare("
            SELECT id
            FROM exams
            WHERE exam_type = ?
              AND section_id = ?
              AND class_id = ?
              AND academic_year = ?
            LIMIT 1
        ");
        $check->bind_param("siis", $exam_type, $section_id, $class_id, $academic_year);

        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "❌ " . t('error_duplicate_exam', ['exam_type' => $exam_type]);
        }
    }

    if (!$error) {
        // ✅ Insert exam now that no duplicate exists
        $stmt = $conn->prepare("
            INSERT INTO exams 
            (name, exam_type, section_id, class_id, academic_year, start_date)
            VALUES (?, ?, ?, ?, ?, ?)
        ");
        $exam_name = ucfirst($exam_type) . " Exam";
        $start_date = date('Y-m-d');

        $stmt->bind_param(
            "ssiiss",
            $exam_name,
            $exam_type,
            $section_id,
            $class_id,
            $academic_year,
            $start_date
        );

        $stmt->execute();
        $exam_id = $stmt->insert_id;

        // Attach subjects
        foreach ($_POST['subjects'] as $subject_id => $row) {
            $stmt2 = $conn->prepare("
                INSERT INTO exam_subjects (exam_id, subject_id, max_marks, pass_marks)
                VALUES (?, ?, ?, ?)
            ");
            $stmt2->bind_param(
                "iidd",
                $exam_id,
                $subject_id,
                $row['max'],
                $row['pass']
            );
            $stmt2->execute();
        }

        $msg = "🔥 " . t('success_exam_launched');
        // Prevent resubmission on refresh
        header("Location: add_exam.php?success=1");
        exit;
    }
}

// Helper function for escaping output
function e($string): string {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>" 
      data-theme="<?php echo $theme_mode === 0 ? 'dark' : 'light'; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        .ts-control {
            border-radius: var(--border-radius);
            padding: 0.75rem 1rem;
            background-color: var(--light-bg-card);
            border-color: var(--light-border);
            color: var(--light-text-primary);
        }
        [data-theme="dark"] .ts-control {
            background-color: var(--dark-bg-card);
            border-color: var(--dark-border);
            color: var(--dark-text-primary);
        }
        [data-theme="dark"] .ts-dropdown {
            background-color: var(--dark-bg-card);
            border-color: var(--dark-border);
            color: var(--dark-text-primary);
        }
        [data-theme="dark"] .ts-dropdown .option {
            color: var(--dark-text-primary);
        }
        [data-theme="dark"] .ts-dropdown .option:hover,
        [data-theme="dark"] .ts-dropdown .option.active {
            background-color: var(--dark-bg-secondary);
        }
        [data-theme="dark"] .ts-control input {
             color: var(--dark-text-primary);
        }
    </style>
<body id="body" data-theme="<?php echo $theme_mode === 0 ? 'dark' : 'light'; ?>">
    <!-- Pass PHP variables to JS -->
    <script>
        const themeMode = <?= $theme_mode ?>;
        const currentLangId = <?= $currentLangId ?>;
        
        // Translation strings for JavaScript
        const jsTranslations = {
            // Toast messages
            success_exam_launched: "<?= t('success_toast_exam') ?>",
            success_bulk_applied: "<?= t('success_bulk_applied') ?>",
            error_no_subjects: "<?= t('error_no_subjects_available') ?>",
            error_at_least_one_value: "<?= t('error_at_least_one_value') ?>",
            error_validation_class: "<?= t('error_validation_class') ?>",
            error_validation_marks: "<?= t('error_validation_marks') ?>",
            
            // Loading messages
            loading_subjects: "<?= t('subjects_loading') ?>",
            error_loading_classes: "<?= t('error_loading_classes') ?>",
            error_loading_subjects: "<?= t('error_loading_subjects') ?>",
            no_subjects_message: "<?= t('no_subjects_message') ?>",
            
            // Toast types
            toast_type_success: "<?= t('toast_type_success') ?>",
            toast_type_error: "<?= t('toast_type_error') ?>",
            toast_type_warning: "<?= t('toast_type_warning') ?>"
        };
    </script>

    <div class="main-container">
        <!-- Page Header -->
        <div class="page-header">
            <h1>
                <i class="fas fa-rocket me-2"></i><?= t('header_title') ?>
            </h1>
            <p><?= t('header_subtitle') ?></p>
        </div>

        <!-- Alert Messages -->
        <?php if ($msg): ?>
            <div class="alert-container">
                <div class="alert-custom success">
                    <i class="fas fa-check-circle alert-icon" style="color: var(--light-success);"></i>
                    <div class="flex-grow-1">
                        <strong><?= t('alert_success') ?></strong> <?= e($msg) ?>
                    </div>
                </div>
            </div>
        <?php elseif ($error): ?>
            <div class="alert-container">
                <div class="alert-custom error">
                    <i class="fas fa-exclamation-triangle alert-icon" style="color: var(--light-danger);"></i>
                    <div class="flex-grow-1">
                        <strong><?= t('alert_error') ?></strong> <?= e($error) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Main Form -->
        <div class="form-container">
            <form method="POST" id="examForm" novalidate>
                <!-- Basic Information Section -->
                <div class="row g-4">
                    <!-- Academic Year -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-alt me-2"></i><?= t('label_academic_year') ?>
                            </label>
                            <select name="academic_year" id="academic_year" class="form-select-custom tom-select" required>
                                <option value=""><?= t('placeholder_select_year') ?></option>
                                <?php
                                $currentYear = date('Y');
                                for ($y = $currentYear - 1; $y <= $currentYear + 1; $y++):
                                    $label = $y . '-' . ($y + 1);
                                ?>
                                    <option value="<?= e($label) ?>"><?= e($label) ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Exam Type -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-clipboard-list me-2"></i><?= t('label_exam_type') ?>
                            </label>
                            <select name="exam_type" id="exam_type" class="form-select-custom tom-select" required>
                                <option value=""><?= t('placeholder_select_type') ?></option>
                                <option value="midterm"><?= t('option_midterm') ?></option>
                                <option value="final"><?= t('option_final') ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Section -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-layer-group me-2"></i><?= t('label_section') ?>
                            </label>
                            <select name="section_id" id="section" class="form-select-custom tom-select" required>
                                <option value=""><?= t('placeholder_select_section') ?></option>
                                <?php while ($s = $sections->fetch_assoc()): ?>
                                    <option value="<?= e($s['id']) ?>"><?= e($s['name']) ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Class -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-users-class me-2"></i><?= t('label_class') ?>
                            </label>
                            <select name="class_id" id="class" class="form-select-custom tom-select" required disabled>
                                <option value=""><?= t('placeholder_select_class') ?></option>
                            </select>
                            <div class="loading-spinner" id="classLoading">
                                <div class="spinner"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bulk Apply Section -->
                <div class="bulk-apply-section">
                    <h4 class="mb-3">
                        <i class="fas fa-copy me-2"></i><?= t('bulk_title') ?>
                    </h4>
                    <div class="bulk-apply-grid">
                        <div>
                            <label class="form-label">
                                <i class="fas fa-chart-line me-2"></i><?= t('bulk_max_marks') ?>
                            </label>
                            <input type="number" 
                                   id="apply_max" 
                                   class="form-control-custom" 
                                   placeholder="<?= t('placeholder_max_marks') ?>"
                                   min="0"
                                   step="0.5">
                        </div>
                        <div>
                            <label class="form-label">
                                <i class="fas fa-check-circle me-2"></i><?= t('bulk_pass_marks') ?>
                            </label>
                            <input type="number" 
                                   id="apply_pass" 
                                   class="form-control-custom" 
                                   placeholder="<?= t('placeholder_pass_marks') ?>"
                                   min="0"
                                   step="0.5">
                        </div>
                    </div>
                    <button type="button" id="apply_all_btn" class="apply-btn">
                        <i class="fas fa-check-double me-2"></i><?= t('bulk_apply_button') ?>
                    </button>
                </div>

                <!-- Subjects Section -->
                <div class="subjects-section">
                    <div class="section-header">
                        <i class="fas fa-book section-icon"></i>
                        <h3><?= t('subjects_title') ?></h3>
                    </div>
                    
                    <div id="subjectsLoading" class="loading-spinner">
                        <div class="spinner"></div>
                        <p class="mt-2"><?= t('subjects_loading') ?></p>
                    </div>
                    
                    <div id="noSubjectsMessage" class="alert-custom error" style="display: none;">
                        <i class="fas fa-exclamation-circle alert-icon"></i>
                        <div><?= t('no_subjects_message') ?></div>
                    </div>
                    
                    <div id="subjectsTableContainer" class="table-container" style="display: none;">
                        <table class="subjects-table">
                            <thead>
                                <tr>
                                    <th><?= t('table_subject_name') ?></th>
                                    <th><?= t('table_max_marks') ?></th>
                                    <th><?= t('table_pass_marks') ?></th>
                                </tr>
                            </thead>
                            <tbody id="subjects"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="form-actions">
                    <a href="exams.php" class="btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i><?= t('btn_back_to_exams') ?>
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-rocket me-2"></i><?= t('btn_launch_exam') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- JavaScript -->
    <script>
        // DOM Elements
        const bodyEl = document.getElementById('body');
        const sectionSelect = document.getElementById('section');
        const classSelect = document.getElementById('class');
        const classLoading = document.getElementById('classLoading');
        const subjectsBody = document.getElementById('subjects');
        const subjectsLoading = document.getElementById('subjectsLoading');
        const subjectsTableContainer = document.getElementById('subjectsTableContainer');
        const noSubjectsMessage = document.getElementById('noSubjectsMessage');
        const applyMax = document.getElementById('apply_max');
        const applyPass = document.getElementById('apply_pass');
        const applyBtn = document.getElementById('apply_all_btn');
        const examForm = document.getElementById('examForm');

        // Apply theme dynamically
        function applyTheme(mode) {
            if (mode === 0) { // dark
                bodyEl.setAttribute('data-theme', 'dark');
            } else { // light
                bodyEl.setAttribute('data-theme', 'light');
            }
        }
        applyTheme(themeMode);

        // Initialize Tom Selects
        let sectionTom, classTom;
        
        document.addEventListener('DOMContentLoaded', () => {
            document.querySelectorAll('.tom-select').forEach(el => {
                const ts = new TomSelect(el, {
                    create: false,
                    sortField: {field: "text", direction: "asc"}
                });
                if(el.id === 'section') sectionTom = ts;
                if(el.id === 'class') classTom = ts;
            });

            // Check if there's a success parameter in URL
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.has('success')) {
                showToast(jsTranslations.success_exam_launched, jsTranslations.toast_type_success);
                window.history.replaceState({}, document.title, window.location.pathname);
            }
        });

        // Load classes when section changes
        sectionSelect.addEventListener('change', function () {
            const sectionId = this.value;
            
            // Handle clearing/resetting
            if (classTom) {
                classTom.clear();
                classTom.clearOptions();
                classTom.disable();
            }
            
            if (!sectionId) {
                // Already disabled above
                return;
            }

            classLoading.style.display = 'block';

            fetch('get_classes.php?section_id=' + sectionId)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    classLoading.style.display = 'none';
                    
                    if (classTom) {
                        classTom.enable();
                        data.forEach(c => {
                            classTom.addOption({value: c.id, text: c.name});
                        });
                        // Select placeholder is handled by Tom Select via 'placeholder' option or empty option
                    } else {
                        // Fallback if Tom Select didn't init for some reason
                         classSelect.disabled = false;
                         let options = `<option value="">${jsTranslations.placeholder_select_class || 'Select Class'}</option>`;
                         data.forEach(c => {
                            options += `<option value="${c.id}">${c.name}</option>`;
                         });
                         classSelect.innerHTML = options;
                    }

                    // Reset subjects
                    resetSubjects();
                })
                .catch(error => {
                    console.error('Error loading classes:', error);
                    classLoading.style.display = 'none';
                    if(!classTom) classSelect.innerHTML = `<option value="">${jsTranslations.error_loading_classes}</option>`;
                });
        });

        // Load subjects when class changes
        classSelect.addEventListener('change', function () {
            const classId = this.value;
            if (!classId) {
                resetSubjects();
                return;
            }

            showSubjectsLoading();
            
            fetch('get_subjects.php?class_id=' + classId)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    hideSubjectsLoading();
                    
                    if (data.length === 0) {
                        noSubjectsMessage.style.display = 'flex';
                        subjectsTableContainer.style.display = 'none';
                        return;
                    }
                    
                    noSubjectsMessage.style.display = 'none';
                    subjectsTableContainer.style.display = 'block';
                    
                    let rows = '';
                    data.forEach(s => {
                        rows += `
                            <tr>
                                <td class="subject-name">${s.name}</td>
                                <td>
                                    <input type="number" 
                                           name="subjects[${s.id}][max]" 
                                           class="form-control-custom" 
                                           required 
                                           min="0"
                                           step="0.5"
                                           placeholder="${jsTranslations.placeholder_subject_max || 'Max marks'}">
                                </td>
                                <td>
                                    <input type="number" 
                                           name="subjects[${s.id}][pass]" 
                                           class="form-control-custom" 
                                           required 
                                           min="0"
                                           step="0.5"
                                           placeholder="${jsTranslations.placeholder_subject_pass || 'Pass marks'}">
                                </td>
                            </tr>
                        `;
                    });
                    subjectsBody.innerHTML = rows;
                })
                .catch(error => {
                    console.error('Error loading subjects:', error);
                    hideSubjectsLoading();
                    noSubjectsMessage.innerHTML = `
                        <i class="fas fa-exclamation-triangle alert-icon"></i>
                        <div>${jsTranslations.error_loading_subjects}</div>
                    `;
                    noSubjectsMessage.style.display = 'flex';
                });
        });

        // Apply same marks to all subjects
        applyBtn.addEventListener('click', () => {
            const maxVal = parseFloat(applyMax.value);
            const passVal = parseFloat(applyPass.value);
            
            if (isNaN(maxVal) && isNaN(passVal)) {
                showToast(jsTranslations.error_at_least_one_value, jsTranslations.toast_type_warning);
                return;
            }

            const rows = subjectsBody.querySelectorAll('tr');
            if (rows.length === 0) {
                showToast(jsTranslations.error_no_subjects, jsTranslations.toast_type_warning);
                return;
            }

            rows.forEach(tr => {
                const maxInput = tr.querySelector('input[name$="[max]"]');
                const passInput = tr.querySelector('input[name$="[pass]"]');
                
                if (maxInput && !isNaN(maxVal)) maxInput.value = maxVal;
                if (passInput && !isNaN(passVal)) passInput.value = passVal;
            });

            showToast(jsTranslations.success_bulk_applied, jsTranslations.toast_type_success);
        });

        // Helper functions
        function showSubjectsLoading() {
            subjectsLoading.style.display = 'block';
            subjectsTableContainer.style.display = 'none';
            noSubjectsMessage.style.display = 'none';
        }

        function hideSubjectsLoading() {
            subjectsLoading.style.display = 'none';
        }

        function resetSubjects() {
            subjectsBody.innerHTML = '';
            subjectsTableContainer.style.display = 'none';
            noSubjectsMessage.style.display = 'none';
        }

        function showToast(message, type) {
            const toast = document.createElement('div');
            toast.className = `alert-custom ${type === 'success' ? 'success' : (type === 'warning' ? 'warning' : 'error')}`;
            toast.style.position = 'fixed';
            toast.style.top = '20px';
            toast.style.right = '20px';
            toast.style.zIndex = '9999';
            toast.style.minWidth = '300px';
            toast.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : (type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle')} alert-icon"></i>
                <div>${message}</div>
            `;
            
            document.body.appendChild(toast);
            
            setTimeout(() => {
                toast.style.opacity = '0';
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        }

        // Form validation
        examForm.addEventListener('submit', function(event) {
            const classId = classSelect.value;
            const subjectRows = subjectsBody.querySelectorAll('tr');
            
            if (!classId || subjectRows.length === 0) {
                event.preventDefault();
                showToast(jsTranslations.error_validation_class, jsTranslations.toast_type_error);
                return;
            }

            // Validate all marks
            let isValid = true;
            subjectRows.forEach(tr => {
                const maxInput = tr.querySelector('input[name$="[max]"]');
                const passInput = tr.querySelector('input[name$="[pass]"]');
                
                if (!maxInput.value || !passInput.value) {
                    isValid = false;
                    maxInput.style.borderColor = 'var(--light-danger)';
                    passInput.style.borderColor = 'var(--light-danger)';
                } else {
                    maxInput.style.borderColor = '';
                    passInput.style.borderColor = '';
                }
            });

            if (!isValid) {
                event.preventDefault();
                showToast(jsTranslations.error_validation_marks, jsTranslations.toast_type_error);
            }
        });

        // Clear validation on input
        document.addEventListener('input', function(e) {
            if (e.target.matches('input[name$="[max]"], input[name$="[pass]"]')) {
                e.target.style.borderColor = '';
            }
        });

        // Initialize moved to top of script
    </script>
</body>
</html>

 <style>
        /* CSS Variables for Themes */
        :root {
            /* Light Theme */
            --light-bg-primary: #f8f9fa;
            --light-bg-secondary: #ffffff;
            --light-bg-card: #ffffff;
            --light-text-primary: #212529;
            --light-text-secondary: #6c757d;
            --light-border: #dee2e6;
            --light-border-focus: #86b7fe;
            --light-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --light-shadow-hover: 0 6px 12px rgba(0, 0, 0, 0.15);
            --light-success: #28a745;
            --light-danger: #dc3545;
            --light-warning: #ffc107;
            --light-info: #17a2b8;
            --light-primary: #0d6efd;
            
            /* Dark Theme */
            --dark-bg-primary: #121212;
            --dark-bg-secondary: #1e1e1e;
            --dark-bg-card: #2d2d2d;
            --dark-text-primary: #e9ecef;
            --dark-text-secondary: #adb5bd;
            --dark-border: #495057;
            --dark-border-focus: #0d6efd;
            --dark-shadow: 0 4px 6px rgba(0, 0, 0, 0.3);
            --dark-shadow-hover: 0 6px 12px rgba(0, 0, 0, 0.4);
            --dark-success: #20c997;
            --dark-danger: #e74c3c;
            --dark-warning: #f39c12;
            --dark-info: #17a2b8;
            --dark-primary: #3498db;
            
            /* Common */
            --transition-speed: 0.3s;
            --border-radius: 12px;
            --border-radius-sm: 8px;
            --sidebar-width: 260px;
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            transition: background-color var(--transition-speed), 
                       color var(--transition-speed),
                       border-color var(--transition-speed),
                       box-shadow var(--transition-speed);
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Theme Application */
        body[data-theme="light"] {
            background-color: var(--light-bg-primary);
            color: var(--light-text-primary);
        }

        body[data-theme="dark"] {
            background-color: var(--dark-bg-primary);
            color: var(--dark-text-primary);
        }

        /* Main Container */
        .main-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem 1rem;
        }

        /* Header */
        .page-header {
            margin-bottom: 2.5rem;
            position: relative;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 60px;
            height: 4px;
            background: linear-gradient(90deg, var(--light-primary), var(--light-info));
            border-radius: 2px;
        }

        body[data-theme="dark"] .page-header::after {
            background: linear-gradient(90deg, var(--dark-primary), var(--dark-info));
        }

        .page-header h1 {
            font-size: 2.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            background: linear-gradient(90deg, var(--light-primary), var(--light-info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        body[data-theme="dark"] .page-header h1 {
            background: linear-gradient(90deg, var(--dark-primary), var(--dark-info));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .page-header p {
            color: var(--light-text-secondary);
            font-size: 1.1rem;
            max-width: 600px;
        }

        body[data-theme="dark"] .page-header p {
            color: var(--dark-text-secondary);
        }

        /* Alert Messages */
        .alert-container {
            margin-bottom: 2rem;
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert-custom {
            border-radius: var(--border-radius);
            border: none;
            padding: 1.25rem 1.5rem;
            box-shadow: var(--light-shadow);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        body[data-theme="dark"] .alert-custom {
            box-shadow: var(--dark-shadow);
        }

        .alert-custom.success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            border-left: 4px solid var(--light-success);
        }

        body[data-theme="dark"] .alert-custom.success {
            background: linear-gradient(135deg, rgba(32, 201, 151, 0.2), rgba(32, 201, 151, 0.1));
            border-left: 4px solid var(--dark-success);
        }

        .alert-custom.error {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            border-left: 4px solid var(--light-danger);
        }

        body[data-theme="dark"] .alert-custom.error {
            background: linear-gradient(135deg, rgba(231, 76, 60, 0.2), rgba(231, 76, 60, 0.1));
            border-left: 4px solid var(--dark-danger);
        }

        .alert-icon {
            font-size: 1.5rem;
        }

        /* Form Container */
        .form-container {
            background: var(--light-bg-card);
            border-radius: var(--border-radius);
            box-shadow: var(--light-shadow);
            padding: 2.5rem;
            margin-bottom: 2rem;
        }

        body[data-theme="dark"] .form-container {
            background: var(--dark-bg-card);
            box-shadow: var(--dark-shadow);
        }

        /* Form Groups */
        .form-group {
            margin-bottom: 1.75rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--light-text-primary);
            font-size: 0.95rem;
        }

        body[data-theme="dark"] .form-label {
            color: var(--dark-text-primary);
        }

        .form-control-custom,
        .form-select-custom {
            width: 100%;
            padding: 0.875rem 1rem;
            border: 2px solid var(--light-border);
            border-radius: var(--border-radius-sm);
            background: var(--light-bg-secondary);
            color: var(--light-text-primary);
            font-size: 1rem;
            transition: all var(--transition-speed);
        }

        body[data-theme="dark"] .form-control-custom,
        body[data-theme="dark"] .form-select-custom {
            background: var(--dark-bg-secondary);
            border-color: var(--dark-border);
            color: var(--dark-text-primary);
        }

        .form-control-custom:focus,
        .form-select-custom:focus {
            outline: none;
            border-color: var(--light-border-focus);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        body[data-theme="dark"] .form-control-custom:focus,
        body[data-theme="dark"] .form-select-custom:focus {
            border-color: var(--dark-border-focus);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.25);
        }

        /* Bulk Apply Section */
        .bulk-apply-section {
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-radius: var(--border-radius);
            padding: 1.5rem;
            margin: 2rem 0;
            border-left: 4px solid var(--light-warning);
        }

        body[data-theme="dark"] .bulk-apply-section {
            background: linear-gradient(135deg, rgba(243, 156, 18, 0.1), rgba(243, 156, 18, 0.05));
            border-left: 4px solid var(--dark-warning);
        }

        .bulk-apply-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .apply-btn {
            background: linear-gradient(135deg, var(--light-warning), #e0a800);
            color: #212529;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }

        .apply-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--light-shadow-hover);
        }

        body[data-theme="dark"] .apply-btn {
            background: linear-gradient(135deg, var(--dark-warning), #d68910);
            color: #ffffff;
        }

        /* Subjects Table */
        .subjects-section {
            margin: 2.5rem 0;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid var(--light-border);
        }

        body[data-theme="dark"] .section-header {
            border-bottom-color: var(--dark-border);
        }

        .section-header h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--light-text-primary);
        }

        body[data-theme="dark"] .section-header h3 {
            color: var(--dark-text-primary);
        }

        .section-icon {
            font-size: 1.5rem;
            color: var(--light-primary);
        }

        body[data-theme="dark"] .section-icon {
            color: var(--dark-primary);
        }

        .table-container {
            overflow-x: auto;
            border-radius: var(--border-radius);
            box-shadow: var(--light-shadow);
        }

        body[data-theme="dark"] .table-container {
            box-shadow: var(--dark-shadow);
        }

        .subjects-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--light-bg-card);
        }

        body[data-theme="dark"] .subjects-table {
            background: var(--dark-bg-card);
        }

        .subjects-table thead {
            background: linear-gradient(135deg, var(--light-primary), #0b5ed7);
        }

        body[data-theme="dark"] .subjects-table thead {
            background: linear-gradient(135deg, var(--dark-primary), #2980b9);
        }

        .subjects-table th {
            padding: 1rem;
            text-align: left;
            color: white;
            font-weight: 600;
            border: none;
        }

        .subjects-table tbody tr {
            border-bottom: 1px solid var(--light-border);
            transition: background-color var(--transition-speed);
        }

        body[data-theme="dark"] .subjects-table tbody tr {
            border-bottom: 1px solid var(--dark-border);
        }

        .subjects-table tbody tr:hover {
            background: rgba(13, 110, 253, 0.05);
        }

        body[data-theme="dark"] .subjects-table tbody tr:hover {
            background: rgba(52, 152, 219, 0.1);
        }

        .subjects-table td {
            padding: 1rem;
            vertical-align: middle;
        }

        .subject-name {
            font-weight: 500;
            color: var(--light-text-primary);
        }

        body[data-theme="dark"] .subject-name {
            color: var(--dark-text-primary);
        }

        /* Form Actions */
        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 2.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid var(--light-border);
        }

        body[data-theme="dark"] .form-actions {
            border-top: 1px solid var(--dark-border);
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--light-primary), #0b5ed7);
            color: white;
            border: none;
            padding: 0.875rem 2rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: var(--light-shadow-hover);
            background: linear-gradient(135deg, #0b5ed7, var(--light-primary));
        }

        body[data-theme="dark"] .btn-primary {
            background: linear-gradient(135deg, var(--dark-primary), #2980b9);
        }

        body[data-theme="dark"] .btn-primary:hover {
            background: linear-gradient(135deg, #2980b9, var(--dark-primary));
        }

        .btn-secondary {
            background: var(--light-bg-secondary);
            color: var(--light-text-primary);
            border: 2px solid var(--light-border);
            padding: 0.875rem 2rem;
            border-radius: var(--border-radius-sm);
            font-weight: 600;
            cursor: pointer;
            transition: all var(--transition-speed);
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-secondary:hover {
            background: var(--light-border);
            transform: translateY(-2px);
            box-shadow: var(--light-shadow);
            text-decoration: none;
            color: var(--light-text-primary);
        }

        body[data-theme="dark"] .btn-secondary {
            background: var(--dark-bg-secondary);
            color: var(--dark-text-primary);
            border-color: var(--dark-border);
        }

        body[data-theme="dark"] .btn-secondary:hover {
            background: var(--dark-border);
            color: var(--dark-text-primary);
        }

        /* Loading States */
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 3px solid rgba(13, 110, 253, 0.3);
            border-top-color: var(--light-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto;
        }

        body[data-theme="dark"] .spinner {
            border-color: rgba(52, 152, 219, 0.3);
            border-top-color: var(--dark-primary);
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                padding: 1rem;
            }
            
            .page-header h1 {
                font-size: 2rem;
            }
            
            .form-container {
                padding: 1.5rem;
            }
            
            .bulk-apply-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-primary,
            .btn-secondary {
                width: 100%;
                justify-content: center;
            }
            
            .subjects-table th,
            .subjects-table td {
                padding: 0.75rem;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 576px) {
            .page-header h1 {
                font-size: 1.75rem;
            }
            
            .form-container {
                padding: 1rem;
            }
            
            .bulk-apply-section {
                padding: 1rem;
            }
        }

        /* Tablet Landscape */
        @media (min-width: 769px) and (max-width: 1024px) {
            .main-container {
                max-width: 95%;
            }
        }

        /* Print Styles */
        @media print {
            .form-actions,
            .bulk-apply-section {
                display: none;
            }
            
            .form-container {
                box-shadow: none;
                padding: 0;
            }
        }

        /* Accessibility */
        .form-control-custom:focus,
        .form-select-custom:focus,
        .btn-primary:focus,
        .btn-secondary:focus {
            outline: 2px solid var(--light-primary);
            outline-offset: 2px;
        }

        body[data-theme="dark"] .form-control-custom:focus,
        body[data-theme="dark"] .form-select-custom:focus,
        body[data-theme="dark"] .btn-primary:focus,
        body[data-theme="dark"] .btn-secondary:focus {
            outline-color: var(--dark-primary);
        }
    </style>