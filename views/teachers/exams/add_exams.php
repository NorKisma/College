<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// =========================
// Teacher authentication
// =========================
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ===================== TRANSLATIONS ===================== */
$teacher_id = (int)$_SESSION['user_id'];

// Get teacher's language
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Create Exam | Teacher Panel',
        'create_exam' => 'Create New Exam',
        'academic_year' => 'Academic Year',
        'back_to_exams' => 'Back to Exams',
        'step1' => 'Exam Details',
        'step2' => 'Class Selection',
        'step3' => 'Subject Setup',
        'step4' => 'Review & Create',
        'exam_information' => 'Exam Information',
        'enter_details' => 'Enter basic details about the exam. All fields are required.',
        'exam_name' => 'Exam Name',
        'exam_name_placeholder' => 'e.g., First Term Examination',
        'exam_name_hint' => 'Give a clear, descriptive name for the exam',
        'exam_type' => 'Exam Type',
        'select_exam_type' => 'Select Exam Type',
        'midterm' => 'Midterm Exam',
        'final' => 'Final Exam',
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
        'practical' => 'Practical Exam',
        'test' => 'Unit Test',
        'start_date' => 'Start Date',
        'start_date_hint' => 'When the exam will begin',
        'end_date' => 'End Date',
        'end_date_hint' => 'When the exam will conclude',
        'class_selection' => 'Class Selection',
        'select_class_hint' => 'Select the class for which you want to create the exam.',
        'select_class' => 'Select Class',
        'choose_class' => 'Choose a Class',
        'loading_subjects' => 'Loading subjects...',
        'quick_marks_config' => 'Quick Marks Configuration',
        'global_marks_hint' => 'Set the same maximum and passing marks for all subjects at once:',
        'max_marks_for_all' => 'Max Marks (for all)',
        'max_marks_placeholder' => 'e.g., 100',
        'pass_marks_for_all' => 'Pass Marks (for all)',
        'pass_marks_placeholder' => 'e.g., 40',
        'apply_to_all' => 'Apply to All Subjects',
        'subjects_marks' => 'Subjects & Marks Configuration',
        'subjects_hint' => 'Configure maximum and passing marks for each subject. At least one subject must have marks specified.',
        'cancel' => 'Cancel',
        'create_exam_button' => 'Create Exam',
        'creating_exam' => 'Creating Exam...',
        'validation_error' => 'Please fill all required fields and add subjects with marks.',
        'exam_exists' => 'This exam already exists for the selected class and academic year.',
        'exam_created' => 'Exam created successfully for academic year',
        'failed_load' => 'Failed to load subjects. Please try again.',
        'marks_applied' => 'Marks applied to all subjects successfully.'
    ],
    2 => [ // Arabic
        'page_title' => 'إنشاء امتحان | لوحة المعلم',
        'create_exam' => 'إنشاء امتحان جديد',
        'academic_year' => 'السنة الأكاديمية',
        'back_to_exams' => 'العودة إلى الامتحانات',
        'step1' => 'تفاصيل الامتحان',
        'step2' => 'اختيار الفصل',
        'step3' => 'إعداد المواد',
        'step4' => 'المراجعة والإنشاء',
        'exam_information' => 'معلومات الامتحان',
        'enter_details' => 'أدخل التفاصيل الأساسية عن الامتحان. جميع الحقول مطلوبة.',
        'exam_name' => 'اسم الامتحان',
        'exam_name_placeholder' => 'مثلاً: امتحان الفصل الدراسي الأول',
        'exam_name_hint' => 'أعط اسمًا واضحًا ووصفيًا للامتحان',
        'exam_type' => 'نوع الامتحان',
        'select_exam_type' => 'اختر نوع الامتحان',
        'midterm' => 'امتحان منتصف الفصل',
        'final' => 'الامتحان النهائي',
        'quiz' => 'اختبار قصير',
        'assignment' => 'واجب',
        'practical' => 'امتحان عملي',
        'test' => 'اختبار الوحدة',
        'start_date' => 'تاريخ البدء',
        'start_date_hint' => 'متى يبدأ الامتحان',
        'end_date' => 'تاريخ الانتهاء',
        'end_date_hint' => 'متى ينتهي الامتحان',
        'class_selection' => 'اختيار الفصل',
        'select_class_hint' => 'اختر الفصل الذي تريد إنشاء الامتحان له.',
        'select_class' => 'اختر الفصل',
        'choose_class' => 'اختر فصلًا',
        'loading_subjects' => 'جاري تحميل المواد...',
        'quick_marks_config' => 'إعداد العلامات السريع',
        'global_marks_hint' => 'قم بتعيين نفس الحد الأقصى وعلامات النجاح لجميع المواد دفعة واحدة:',
        'max_marks_for_all' => 'الحد الأقصى للعلامات (للكل)',
        'max_marks_placeholder' => 'مثلاً: 100',
        'pass_marks_for_all' => 'علامات النجاح (للكل)',
        'pass_marks_placeholder' => 'مثلاً: 40',
        'apply_to_all' => 'تطبيق على جميع المواد',
        'subjects_marks' => 'إعداد المواد والعلامات',
        'subjects_hint' => 'قم بتكوين الحد الأقصى وعلامات النجاح لكل مادة. يجب أن يكون لموضوع واحد على الأقل علامات محددة.',
        'cancel' => 'إلغاء',
        'create_exam_button' => 'إنشاء الامتحان',
        'creating_exam' => 'جاري إنشاء الامتحان...',
        'validation_error' => 'الرجاء ملء جميع الحقول المطلوبة وإضافة مواد بالعلامات.',
        'exam_exists' => 'هذا الامتحان موجود بالفعل للفصل والسنة الأكاديمية المحددة.',
        'exam_created' => 'تم إنشاء الامتحان بنجاح للسنة الأكاديمية',
        'failed_load' => 'فشل تحميل المواد. يرجى المحاولة مرة أخرى.',
        'marks_applied' => 'تم تطبيق العلامات على جميع المواد بنجاح.'
    ],
    3 => [ // Somali
        'page_title' => 'Abuur Imtixaan | Guddiga Macallinka',
        'create_exam' => 'Abuur Imtixaan Cusub',
        'academic_year' => 'Sannadka Tacliinta',
        'back_to_exams' => 'Ku Noqo Imtixaannada',
        'step1' => 'Faahfaahinta Imtixaanka',
        'step2' => 'Doorashada Fasalka',
        'step3' => 'Dejinta Maaddooyinka',
        'step4' => 'Dib-u-eegista & Abuurista',
        'exam_information' => 'Macluumaadka Imtixaanka',
        'enter_details' => 'Geli faahfaahinta aasaasiga ah ee imtixaanka. Dhammaan goobaha waa loo baahan yahay.',
        'exam_name' => 'Magaca Imtixaanka',
        'exam_name_placeholder' => 'tusaale: Imtixaanka Qeybta Koowaad',
        'exam_name_hint' => 'Siin magac cad oo sharrax leh imtixaanka',
        'exam_type' => 'Nooca Imtixaanka',
        'select_exam_type' => 'Dooro Nooca Imtixaanka',
        'midterm' => 'Imtixaan Dhexe',
        'final' => 'Imtixaan Dhamaadka',
        'quiz' => 'Su\'aalo',
        'assignment' => 'Layli',
        'practical' => 'Imtixaan Firfircoon',
        'test' => 'Imtixaan Unug',
        'start_date' => 'Taariikhda Bilowga',
        'start_date_hint' => 'Goorma ay bilowdo imtixaanka',
        'end_date' => 'Taariikhda Dhamaadka',
        'end_date_hint' => 'Goorma ay dhammaato imtixaanka',
        'class_selection' => 'Doorashada Fasalka',
        'select_class_hint' => 'Dooro fasalka aad rabto inaad u abuurto imtixaanka.',
        'select_class' => 'Dooro Fasalka',
        'choose_class' => 'Dooro Fasalka',
        'loading_subjects' => 'Maaddooyinka la soo dejiyayo...',
        'quick_marks_config' => 'Dejinta Dhibcaha Degdega ah',
        'global_marks_hint' => 'Deji isla dhibcaha ugu badan iyo kuwa guusha ee dhammaan maaddooyinka isla markiiba:',
        'max_marks_for_all' => 'Dhibcaha Ugu Badan (dhamaan)',
        'max_marks_placeholder' => 'tusaale: 100',
        'pass_marks_for_all' => 'Dhibcaha Guusha (dhamaan)',
        'pass_marks_placeholder' => 'tusaale: 40',
        'apply_to_all' => 'Ku Dhabbo Dhammaan Maaddooyinka',
        'subjects_marks' => 'Dejinta Maaddooyinka & Dhibcaha',
        'subjects_hint' => 'Habee dhibcaha ugu badan iyo kuwa guusha ee maadda kasta. Ugu yaraan hal maadda waa inay leedahay dhibco la qeexay.',
        'cancel' => 'Jooji',
        'create_exam_button' => 'Abuur Imtixaan',
        'creating_exam' => 'Imtixaan la abuuraayo...',
        'validation_error' => 'Fadlan buuxi dhammaan goobaha loo baahan yahay oo ku dar maaddooyin leh dhibco.',
        'exam_exists' => 'Imtixaan kan horay ayaa u jiray fasalka iyo sannadka tacliinta la dooray.',
        'exam_created' => 'Imtixaanka si guul leh loo abuuray sannadka tacliinta',
        'failed_load' => 'Khalad ayaa dhacay markii la soo dejinayay maaddooyinka. Fadlan isku day mar kale.',
        'marks_applied' => 'Dhibcaha si guul leh loogu dhabbay dhammaan maaddooyinka.'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$message = "";

// =========================
// Current academic year
// =========================
$academic_year = date('Y') . '-' . (date('Y') + 1);

// =========================
// Handle Exam Submission
// =========================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $exam_name  = trim($_POST['exam_name'] ?? '');
    $exam_type  = trim($_POST['exam_type'] ?? '');
    $class_id   = (int)($_POST['class_id'] ?? 0);
    $section_id = (int)($_POST['section_id'] ?? 0);
    $start_date = $_POST['start_date'] ?: null;
    $end_date   = $_POST['end_date'] ?: null;

    $max_marks  = $_POST['max_marks'] ?? [];
    $pass_marks = $_POST['pass_marks'] ?? [];

    // =========================
    // Basic validation
    // =========================
    if (!$exam_type || !$class_id || !$section_id || empty($max_marks)) {
        $message = "⚠ " . t('validation_error');
    } else {

        // =========================
        // Auto-generate exam name if empty
        // =========================
        if ($exam_name === '') {
            $exam_name = ucfirst($exam_type) . " ({$academic_year})";
        }

        // =========================
        // Check for duplicate exam
        // =========================
        $check = $conn->prepare("
            SELECT id 
            FROM exams
            WHERE name = ?
              AND class_id = ?
              AND section_id = ?
              AND academic_year = ?
            LIMIT 1
        ");
        $check->bind_param("siis", $exam_name, $class_id, $section_id, $academic_year);
        $check->execute();
        $exists = $check->get_result()->fetch_assoc();

        if ($exists) {

            $message = "⚠ " . t('exam_exists');

        } else {

            // =========================
            // Insert exam
            // =========================
            $stmt = $conn->prepare("
                INSERT INTO exams
                    (name, exam_type, class_id, section_id, start_date, end_date, academic_year)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");
            $stmt->bind_param(
                "ssiisss",
                $exam_name,
                $exam_type,
                $class_id,
                $section_id,
                $start_date,
                $end_date,
                $academic_year
            );
            $stmt->execute();
            $exam_id = $stmt->insert_id;

            // =========================
            // Insert exam subjects
            // =========================
            $stmt_sub = $conn->prepare("
                INSERT INTO exam_subjects
                    (exam_id, subject_id, max_marks, pass_marks)
                VALUES (?, ?, ?, ?)
            ");

            foreach ($max_marks as $subject_id => $max) {
                $max  = (float)$max;
                $pass = isset($pass_marks[$subject_id]) ? (float)$pass_marks[$subject_id] : null;

                if ($max <= 0 || $pass === null || $pass < 0) {
                    continue;
                }

                $stmt_sub->bind_param("iidd", $exam_id, $subject_id, $max, $pass);
                $stmt_sub->execute();
            }

            $message = "✅ " . t('exam_created') . " {$academic_year}!";
        }
    }
}

// =========================
// Fetch teacher classes
// =========================
$classes = [];

$stmt = $conn->prepare("
    SELECT DISTINCT c.id, c.name, c.section_id
    FROM classes c
    INNER JOIN class_subjects cs ON cs.class_id = c.id
    WHERE cs.teacher_id = ?
    ORDER BY c.name
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();

$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $classes[] = $row;
}
?>


<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
  
</head>
<body>
    <!-- Header Section -->
    <div class="page-header">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="page-title"><i class="fas fa-file-alt me-2"></i><?= t('create_exam') ?></h1>
                    <p class="page-subtitle"><?= t('academic_year') ?>: <?= e($academic_year) ?></p>
                </div>
                <div>
                    <a href="exams.php" class="btn btn-outline-light">
                        <i class="fas fa-arrow-left me-2"></i><?= t('back_to_exams') ?>
                    </a>
                </div>
            </div>
            
            <!-- Step Indicator -->
            <div class="step-indicator mt-4">
                <div class="step active">
                    <div class="step-circle">1</div>
                    <span class="step-label"><?= t('step1') ?></span>
                </div>
                <div class="step">
                    <div class="step-circle">2</div>
                    <span class="step-label"><?= t('step2') ?></span>
                </div>
                <div class="step">
                    <div class="step-circle">3</div>
                    <span class="step-label"><?= t('step3') ?></span>
                </div>
                <div class="step">
                    <div class="step-circle">4</div>
                    <span class="step-label"><?= t('step4') ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Success/Error Messages -->
        <?php if($message): ?>
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="fas fa-info-circle me-2"></i>
                <?= e($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form method="post" id="examForm">
            <!-- Exam Information -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i><?= t('exam_information') ?>
                </div>
                <div class="card-body">
                    <div class="info-badge">
                        <i class="fas fa-lightbulb"></i>
                        <?= t('enter_details') ?>
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label required-field"><?= t('exam_name') ?></label>
                            <input type="text" name="exam_name" class="form-control" placeholder="<?= t('exam_name_placeholder') ?>">
                            <small class="text-muted"><?= t('exam_name_hint') ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label required-field"><?= t('exam_type') ?></label>
                            <select name="exam_type" class="form-select" required>
                                <option value="">-- <?= t('select_exam_type') ?> --</option>
                                <option value="Midterm"><?= t('midterm') ?></option>
                                <option value="Final"><?= t('final') ?></option>
                                <option value="Quiz"><?= t('quiz') ?></option>
                                <option value="Assignment"><?= t('assignment') ?></option>
                                <option value="Practical"><?= t('practical') ?></option>
                                <option value="Test"><?= t('test') ?></option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= t('start_date') ?></label>
                            <input type="date" name="start_date" class="form-control date-picker">
                            <small class="text-muted"><?= t('start_date_hint') ?></small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label"><?= t('end_date') ?></label>
                            <input type="date" name="end_date" class="form-control date-picker">
                            <small class="text-muted"><?= t('end_date_hint') ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Class Selection -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users-class"></i><?= t('class_selection') ?>
                </div>
                <div class="card-body">
                    <div class="info-badge">
                        <i class="fas fa-lightbulb"></i>
                        <?= t('select_class_hint') ?>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label required-field"><?= t('select_class') ?></label>
                        <select name="class_id" id="class_id" class="form-select" required>
                            <option value="">-- <?= t('choose_class') ?> --</option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= e($c['id']) ?>" data-section="<?= e($c['section_id']) ?>">
                                    <?= e($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <input type="hidden" name="section_id" id="section_id">
                    </div>
                    
                    <div class="loading-spinner" id="classLoading">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p class="mt-2"><?= t('loading_subjects') ?></p>
                    </div>
                </div>
            </div>

            <!-- Global Marks Configuration -->
            <div class="card d-none" id="globalMarksCard">
                <div class="card-header">
                    <i class="fas fa-sliders-h"></i><?= t('quick_marks_config') ?>
                </div>
                <div class="card-body">
                    <div class="global-marks-section">
                        <p class="text-muted mb-3"><?= t('global_marks_hint') ?></p>
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4">
                                <label class="form-label"><?= t('max_marks_for_all') ?></label>
                                <input type="number" id="global_max" class="form-control" placeholder="<?= t('max_marks_placeholder') ?>" min="0" step="0.5">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label"><?= t('pass_marks_for_all') ?></label>
                                <input type="number" id="global_pass" class="form-control" placeholder="<?= t('pass_marks_placeholder') ?>" min="0" step="0.5">
                            </div>
                            <div class="col-md-4">
                                <button type="button" id="applyGlobalMarks" class="btn btn-secondary w-100">
                                    <i class="fas fa-check me-2"></i><?= t('apply_to_all') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects & Marks -->
            <div class="card d-none" id="subjectsCard">
                <div class="card-header">
                    <i class="fas fa-book"></i><?= t('subjects_marks') ?>
                </div>
                <div class="card-body">
                    <div class="info-badge">
                        <i class="fas fa-lightbulb"></i>
                        <?= t('subjects_hint') ?>
                    </div>
                    
                    <div id="subjectsContainer"></div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="d-flex justify-content-between mt-4 mb-5">
                <a href="exams.php" class="btn btn-outline-secondary">
                    <i class="fas fa-times me-2"></i><?= t('cancel') ?>
                </a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="fas fa-save me-2"></i><?= t('create_exam_button') ?>
                </button>
            </div>
        </form>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize date pickers
            flatpickr(".date-picker", {
                dateFormat: "Y-m-d",
                minDate: "today",
                allowInput: true
            });
            
            const classSelect = document.getElementById('class_id');
            const sectionInput = document.getElementById('section_id');
            const subjectsCard = document.getElementById('subjectsCard');
            const subjectsContainer = document.getElementById('subjectsContainer');
            const globalMarksCard = document.getElementById('globalMarksCard');
            const applyGlobalBtn = document.getElementById('applyGlobalMarks');
            const classLoading = document.getElementById('classLoading');
            const steps = document.querySelectorAll('.step');
            
            // Step indicator update function
            function updateStepIndicator(stepNumber) {
                steps.forEach((step, index) => {
                    if (index < stepNumber) {
                        step.classList.add('active');
                    } else {
                        step.classList.remove('active');
                    }
                });
            }
            
            // Initially show step 1 as active
            updateStepIndicator(1);
            
            classSelect.addEventListener('change', function() {
                const classId = this.value;
                const selectedOption = this.selectedOptions[0];
                const sectionId = selectedOption ? selectedOption.dataset.section : '';
                sectionInput.value = sectionId;
                
                if (!classId) {
                    subjectsCard.classList.add('d-none');
                    subjectsContainer.innerHTML = '';
                    globalMarksCard.classList.add('d-none');
                    updateStepIndicator(1);
                    return;
                }
                
                // Show loading spinner
                classLoading.style.display = 'block';
                subjectsCard.classList.add('d-none');
                globalMarksCard.classList.add('d-none');
                
                // Update step indicator
                updateStepIndicator(2);
                
                fetch('ajax/get_class_subjects.php?class_id=' + classId)
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.text();
                    })
                    .then(html => {
                        subjectsContainer.innerHTML = html;
                        subjectsCard.classList.remove('d-none');
                        globalMarksCard.classList.remove('d-none');
                        classLoading.style.display = 'none';
                        
                        // Update step indicator
                        updateStepIndicator(3);
                    })
                    .catch(error => {
                        console.error('Error fetching subjects:', error);
                        subjectsContainer.innerHTML = `
                            <div class="alert alert-danger">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= t('failed_load') ?>
                            </div>
                        `;
                        subjectsCard.classList.remove('d-none');
                        globalMarksCard.classList.add('d-none');
                        classLoading.style.display = 'none';
                    });
            });
            
            applyGlobalBtn.addEventListener('click', function() {
                const maxVal = document.getElementById('global_max').value;
                const passVal = document.getElementById('global_pass').value;
                
                if (!maxVal && !passVal) {
                    alert('Please enter at least one value to apply to all subjects.');
                    return;
                }
                
                subjectsContainer.querySelectorAll('input[name^="max_marks"]').forEach(input => {
                    if (maxVal) {
                        input.value = maxVal;
                        // Trigger input event to update any listeners
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                
                subjectsContainer.querySelectorAll('input[name^="pass_marks"]').forEach(input => {
                    if (passVal) {
                        input.value = passVal;
                        // Trigger input event to update any listeners
                        input.dispatchEvent(new Event('input', { bubbles: true }));
                    }
                });
                
                // Show feedback
                const feedback = document.createElement('div');
                feedback.className = 'alert alert-success mt-3';
                feedback.innerHTML = '<i class="fas fa-check-circle me-2"></i><?= t('marks_applied') ?>';
                subjectsContainer.prepend(feedback);
                
                // Remove feedback after 3 seconds
                setTimeout(() => feedback.remove(), 3000);
            });
            
            // Form validation
            document.getElementById('examForm').addEventListener('submit', function(e) {
                const examName = document.querySelector('input[name="exam_name"]').value.trim();
                const examType = document.querySelector('select[name="exam_type"]').value;
                const classId = document.querySelector('select[name="class_id"]').value;
                const maxMarksInputs = document.querySelectorAll('input[name^="max_marks"]');
                let hasMarks = false;
                
                // Check if at least one subject has max marks
                maxMarksInputs.forEach(input => {
                    if (input.value && input.value.trim() !== '') {
                        hasMarks = true;
                    }
                });
                
                if (!examType || !classId || !hasMarks) {
                    e.preventDefault();
                    
                    let errorMessage = 'Please fix the following issues:\n';
                    if (!examType) errorMessage += '- Exam Type is required\n';
                    if (!classId) errorMessage += '- Class selection is required\n';
                    if (!hasMarks) errorMessage += '- At least one subject must have Maximum Marks specified\n';
                    
                    alert(errorMessage);
                    return false;
                }
                
                // Update step indicator to show completion
                updateStepIndicator(4);
                
                // Add loading state to submit button
                const submitBtn = e.submitter;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?= t('creating_exam') ?>';
                submitBtn.disabled = true;
                
                // Allow form submission
                return true;
            });
        });
    </script>
</body>
</html>

  <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            --card-shadow-hover: 0 6px 12px rgba(0, 0, 0, 0.08);
        }
        
        body {
            background-color: #f5f7fb;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            color: #333;
            line-height: 1.6;
        }
        
        .container-fluid {
            max-width: 1200px;
        }
        
        .page-header {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2.5rem;
            border-radius: 0 0 10px 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        
        .page-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            margin-bottom: 1.5rem;
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: var(--card-shadow-hover);
        }
        
        .card-header {
            background-color: var(--primary-color);
            color: white;
            font-weight: 600;
            padding: 1rem 1.5rem;
            border-bottom: none;
            border-radius: 10px 10px 0 0 !important;
        }
        
        .card-header i {
            width: 22px;
            margin-right: 8px;
        }
        
        .card-body {
            padding: 1.5rem;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 0.5rem;
        }
        
        .form-control, .form-select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.6rem 0.85rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.15);
        }
        
        .btn-primary {
            background-color: var(--secondary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(41, 128, 185, 0.3);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 6px;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }
        
        .btn i {
            margin-right: 6px;
        }
        
        .alert {
            border-radius: 8px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .alert-info {
            background-color: #e3f2fd;
            color: #0c5460;
            border-left: 4px solid var(--secondary-color);
        }
        
        .subject-row {
            background-color: white;
            border: 1px solid #eaeaea;
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            transition: all 0.2s ease;
        }
        
        .subject-row:hover {
            background-color: #f9f9f9;
            border-color: #d0d0d0;
        }
        
        .subject-name {
            font-weight: 600;
            color: var(--primary-color);
            font-size: 1.05rem;
        }
        
        .required-field::after {
            content: " *";
            color: #e74c3c;
        }
        
        .global-marks-section {
            background-color: #f8fafc;
            border: 1px dashed #cbd5e0;
            border-radius: 8px;
            padding: 1.25rem;
        }
        
        .section-divider {
            border-top: 2px solid #eaeaea;
            margin: 2rem 0;
        }
        
        .info-badge {
            background-color: #e3f2fd;
            color: var(--secondary-color);
            padding: 0.5rem 0.85rem;
            border-radius: 6px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            margin-bottom: 1rem;
        }
        
        .info-badge i {
            margin-right: 6px;
        }
        
        @media (max-width: 768px) {
            .container-fluid {
                padding: 0 15px;
            }
            
            .page-header {
                padding: 1.5rem 0;
                margin-bottom: 2rem;
                border-radius: 0;
            }
            
            .card-body {
                padding: 1.25rem;
            }
        }
        
        .loading-spinner {
            display: none;
            text-align: center;
            padding: 2rem;
        }
        
        .loading-spinner i {
            font-size: 2rem;
            color: var(--secondary-color);
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .step-indicator::before {
            content: '';
            position: absolute;
            top: 15px;
            left: 0;
            width: 100%;
            height: 2px;
            background-color: #e0e0e0;
            z-index: 1;
        }
        
        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 2;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #e0e0e0;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 600;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background-color: var(--secondary-color);
            color: white;
        }
        
        .step-label {
            font-size: 0.85rem;
            color: #777;
            font-weight: 500;
        }
        
        .step.active .step-label {
            color: var(--secondary-color);
            font-weight: 600;
        }
    </style>