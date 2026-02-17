<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ===================== TRANSLATIONS ===================== */
$teacher_id = $_SESSION['user_id'];

// Get teacher's language
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Add Subjects to Exam',
        'add_subjects' => 'Add Subjects to Exam',
        'exam' => 'Exam',
        'class' => 'Class',
        'all_subjects_added' => 'All subjects are already added to this exam.',
        'subjects_added' => 'Subjects added successfully!',
        'add' => 'Add',
        'subject' => 'Subject',
        'max_marks' => 'Max Marks',
        'pass_marks' => 'Pass Marks',
        'add_subjects_button' => 'Add Subjects',
        'back' => 'Back',
        'invalid_exam' => 'Invalid exam ID',
        'exam_not_found' => 'Exam not found'
    ],
    2 => [ // Arabic
        'page_title' => 'إضافة مواد إلى الامتحان',
        'add_subjects' => 'إضافة مواد إلى الامتحان',
        'exam' => 'الامتحان',
        'class' => 'الفصل',
        'all_subjects_added' => 'جميع المواد تمت إضافتها بالفعل إلى هذا الامتحان.',
        'subjects_added' => 'تمت إضافة المواد بنجاح!',
        'add' => 'إضافة',
        'subject' => 'المادة',
        'max_marks' => 'الحد الأقصى للعلامات',
        'pass_marks' => 'علامات النجاح',
        'add_subjects_button' => 'إضافة المواد',
        'back' => 'عودة',
        'invalid_exam' => 'معرف الامتحان غير صالح',
        'exam_not_found' => 'الامتحان غير موجود'
    ],
    3 => [ // Somali
        'page_title' => 'Ku Dar Maaddooyinka Imtixaanka',
        'add_subjects' => 'Ku Dar Maaddooyinka Imtixaanka',
        'exam' => 'Imtixaan',
        'class' => 'Fasalka',
        'all_subjects_added' => 'Dhammaan maaddooyinka horay ayaa lagu daray imtixaan kan.',
        'subjects_added' => 'Maaddooyinka si guul leh loogu daray!',
        'add' => 'Ku Dar',
        'subject' => 'Maadda',
        'max_marks' => 'Dhibcaha Ugu Badan',
        'pass_marks' => 'Dhibcaha Baaska',
        'add_subjects_button' => 'Ku Dar Maaddooyinka',
        'back' => 'Dib',
        'invalid_exam' => 'Aqoonsiga imtixaanka ma ahan mid sax ah',
        'exam_not_found' => 'Imtixaan lama helin'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

/* -------------------- GET EXAM -------------------- */
$exam_id = (int)($_GET['exam_id'] ?? 0);
if ($exam_id <= 0) {
    die(t('invalid_exam'));
}

$stmt = $conn->prepare("
    SELECT e.*, c.name AS class_name
    FROM exams e
    JOIN classes c ON c.id = e.class_id
    WHERE e.id = ?
");
$stmt->bind_param("i", $exam_id);
$stmt->execute();
$exam = $stmt->get_result()->fetch_assoc();

if (!$exam) {
    die(t('exam_not_found'));
}

/* -------------------- FETCH AVAILABLE SUBJECTS -------------------- */
$stmt = $conn->prepare("
    SELECT s.id, s.name
    FROM subjects s
    JOIN class_subjects cs ON cs.subject_id = s.id
    WHERE cs.class_id = ?
    AND s.id NOT IN (
        SELECT subject_id FROM exam_subjects WHERE exam_id = ?
    )
    ORDER BY s.name
");
$stmt->bind_param("ii", $exam['class_id'], $exam_id);
$stmt->execute();
$subjects = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

/* -------------------- HANDLE SUBMIT -------------------- */
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['subjects'])) {

    $stmt = $conn->prepare("
        INSERT INTO exam_subjects (exam_id, subject_id, max_marks, pass_marks)
        VALUES (?, ?, ?, ?)
    ");

    foreach ($_POST['subjects'] as $sub_id) {
        $max  = (float)$_POST['max_marks'][$sub_id];
        $pass = (float)$_POST['pass_marks'][$sub_id];

        if ($pass > $max) continue; // safety

        $stmt->bind_param("iidd", $exam_id, $sub_id, $max, $pass);
        $stmt->execute();
    }

    $message = t('subjects_added');
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('page_title') ?> | <?= e($exam['name']) ?></title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
    :root {
        --primary: #2c3e50;
        --primary-light: #34495e;
        --secondary: #7f8c8d;
        --accent: #3498db;
        --success: #27ae60;
        --light: #f8f9fa;
        --border: #e0e0e0;
        --shadow: 0 2px 10px rgba(0,0,0,0.05);
        --radius: 8px;
        --radius-sm: 6px;
    }
    
    * {
        font-family: 'Inter', sans-serif;
    }
    
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #e4e8ec 100%);
        min-height: 100vh;
        color: #333;
    }
    
    .container {
        max-width: 1200px;
    }
    
    /* Header */
    .page-header {
        background: var(--primary);
        color: white;
        padding: 2rem 0;
        margin-bottom: 2rem;
        border-radius: 0 0 var(--radius) var(--radius);
        box-shadow: var(--shadow);
    }
    
    .page-title {
        font-weight: 600;
        color: white;
        margin-bottom: 0.5rem;
    }
    
    .page-subtitle {
        color: rgba(255,255,255,0.85);
        font-size: 0.95rem;
    }
    
    /* Exam Info Card */
    .exam-info-card {
        background: white;
        border-radius: var(--radius);
        padding: 1.5rem;
        box-shadow: var(--shadow);
        margin-bottom: 2rem;
        border-left: 4px solid var(--accent);
    }
    
    .exam-info-item {
        display: flex;
        align-items: center;
        margin-bottom: 0.75rem;
    }
    
    .exam-info-item:last-child {
        margin-bottom: 0;
    }
    
    .info-label {
        font-weight: 600;
        color: var(--primary);
        min-width: 80px;
        margin-right: 1rem;
    }
    
    .info-value {
        color: #555;
    }
    
    /* Alerts */
    .alert-custom {
        border-radius: var(--radius-sm);
        border: none;
        padding: 1rem 1.25rem;
        box-shadow: var(--shadow);
        margin-bottom: 1.5rem;
    }
    
    .alert-success {
        background-color: #d4edda;
        color: #155724;
        border-left: 4px solid var(--success);
    }
    
    .alert-info {
        background-color: #d1ecf1;
        color: #0c5460;
        border-left: 4px solid var(--accent);
    }
    
    /* Table */
    .table-container {
        background: white;
        border-radius: var(--radius);
        box-shadow: var(--shadow);
        overflow: hidden;
        margin-bottom: 2rem;
    }
    
    .table-header {
        background: var(--primary-light);
        color: white;
        padding: 1rem 1.5rem;
        font-weight: 600;
    }
    
    .table-responsive {
        overflow-x: auto;
    }
    
    .table {
        margin-bottom: 0;
        min-width: 800px;
    }
    
    .table thead {
        background-color: #f8f9fa;
        color: var(--primary);
    }
    
    .table th {
        font-weight: 600;
        padding: 1rem;
        border-bottom: 2px solid var(--border);
    }
    
    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--border);
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    /* Form Elements */
    .form-control {
        border: 1px solid var(--border);
        border-radius: var(--radius-sm);
        padding: 0.6rem 0.75rem;
        font-size: 0.9rem;
        transition: all 0.3s;
    }
    
    .form-control:focus {
        border-color: var(--accent);
        box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
    }
    
    .form-check-input:checked {
        background-color: var(--accent);
        border-color: var(--accent);
    }
    
    /* Buttons */
    .btn-primary {
        background: var(--accent);
        border: none;
        padding: 0.75rem 2rem;
        font-weight: 500;
        border-radius: var(--radius-sm);
        transition: all 0.3s;
    }
    
    .btn-primary:hover {
        background: #2980b9;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(52, 152, 219, 0.2);
    }
    
    .btn-secondary {
        background: var(--secondary);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: var(--radius-sm);
        transition: all 0.3s;
    }
    
    .btn-secondary:hover {
        background: #6c757d;
        transform: translateY(-1px);
    }
    
    .btn-group {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state-icon {
        font-size: 3rem;
        color: #bdc3c7;
        margin-bottom: 1.5rem;
    }
    
    .empty-state h4 {
        font-weight: 600;
        color: var(--primary);
        margin-bottom: 0.5rem;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem 0;
        }
        
        .exam-info-card {
            padding: 1.25rem;
        }
        
        .exam-info-item {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .info-label {
            margin-bottom: 0.25rem;
        }
        
        .btn-group {
            flex-direction: column;
            width: 100%;
        }
        
        .btn {
            width: 100%;
        }
        
        .table {
            font-size: 0.9rem;
        }
        
        .table th,
        .table td {
            padding: 0.75rem 0.5rem;
        }
    }
    
    @media (max-width: 576px) {
        .container {
            padding-left: 1rem;
            padding-right: 1rem;
        }
        
        .page-header {
            border-radius: 0 0 var(--radius) var(--radius);
        }
    }
    
    /* RTL Support */
    [dir="rtl"] .exam-info-item {
        flex-direction: row-reverse;
    }
    
    [dir="rtl"] .info-label {
        margin-right: 0;
        margin-left: 1rem;
    }
    
    [dir="rtl"] .table-header,
    [dir="rtl"] .table th {
        text-align: right;
    }
</style>
</head>

<body>
    <!-- Header -->
    <div class="page-header">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <h1 class="page-title mb-3">
                        <i class="fas fa-book-medical me-2"></i><?= t('add_subjects') ?>
                    </h1>
                    <p class="page-subtitle">
                        <?= t('add_subjects') ?> - <?= e($exam['name']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container">
        <!-- Exam Information -->
        <div class="exam-info-card">
            <div class="row">
                <div class="col-md-6">
                    <div class="exam-info-item">
                        <span class="info-label"><?= t('exam') ?>:</span>
                        <span class="info-value">
                            <strong><?= e($exam['name']) ?></strong>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="exam-info-item">
                        <span class="info-label"><?= t('class') ?>:</span>
                        <span class="info-value">
                            <strong><?= e($exam['class_name']) ?></strong>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success Message -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-3"></i>
                    <div><?= e($message) ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!$subjects): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-clipboard-check"></i>
                </div>
                <h4><?= t('all_subjects_added') ?></h4>
                <p class="text-muted mb-4"><?= t('all_subjects_added') ?></p>
                <a href="view_exam.php?id=<?= $exam_id ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-2"></i><?= t('back') ?>
                </a>
            </div>
        <?php else: ?>

            <!-- Subjects Form -->
            <form method="post" id="subjectsForm">
                <div class="table-container">
                    <div class="table-header">
                        <i class="fas fa-list-check me-2"></i><?= t('add_subjects') ?>
                    </div>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th width="60"><?= t('add') ?></th>
                                    <th><?= t('subject') ?></th>
                                    <th width="150"><?= t('max_marks') ?></th>
                                    <th width="150"><?= t('pass_marks') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($subjects as $s): ?>
                                <tr>
                                    <td>
                                        <div class="form-check">
                                            <input type="checkbox" 
                                                   class="form-check-input subject-checkbox" 
                                                   name="subjects[]" 
                                                   value="<?= e($s['id']) ?>" 
                                                   checked
                                                   id="subject_<?= e($s['id']) ?>">
                                        </div>
                                    </td>
                                    <td>
                                        <label for="subject_<?= e($s['id']) ?>" class="form-check-label" style="cursor: pointer;">
                                            <?= e($s['name']) ?>
                                        </label>
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="max_marks[<?= e($s['id']) ?>]"
                                               class="form-control" 
                                               min="1" 
                                               max="1000"
                                               step="0.5"
                                               required
                                               oninput="validateMarks(this, <?= e($s['id']) ?>)">
                                    </td>
                                    <td>
                                        <input type="number" 
                                               name="pass_marks[<?= e($s['id']) ?>]"
                                               class="form-control" 
                                               min="0" 
                                               max="1000"
                                               step="0.5"
                                               required
                                               oninput="validatePassMarks(this, <?= e($s['id']) ?>)">
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="btn-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-plus-circle me-2"></i><?= t('add_subjects_button') ?>
                    </button>
                    <a href="view_exam.php?id=<?= $exam_id ?>" class="btn btn-secondary">
                        <i class="fas fa-arrow-left me-2"></i><?= t('back') ?>
                    </a>
                </div>
            </form>
        <?php endif; ?>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Validate max marks
            function validateMarks(input, subjectId) {
                const value = parseFloat(input.value);
                const passInput = document.querySelector(`input[name="pass_marks[${subjectId}]"]`);
                
                if (value < 1) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    return false;
                }
                
                if (passInput && passInput.value) {
                    const passValue = parseFloat(passInput.value);
                    if (passValue > value) {
                        passInput.classList.add('is-invalid');
                        passInput.classList.remove('is-valid');
                    } else {
                        passInput.classList.remove('is-invalid');
                        passInput.classList.add('is-valid');
                    }
                }
                
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }
            
            // Validate pass marks
            function validatePassMarks(input, subjectId) {
                const value = parseFloat(input.value);
                const maxInput = document.querySelector(`input[name="max_marks[${subjectId}]"]`);
                
                if (value < 0) {
                    input.classList.add('is-invalid');
                    input.classList.remove('is-valid');
                    return false;
                }
                
                if (maxInput && maxInput.value) {
                    const maxValue = parseFloat(maxInput.value);
                    if (value > maxValue) {
                        input.classList.add('is-invalid');
                        input.classList.remove('is-valid');
                        return false;
                    }
                }
                
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                return true;
            }
            
            // Form validation
            document.getElementById('subjectsForm').addEventListener('submit', function(e) {
                let isValid = true;
                let errorMessage = '';
                
                // Check at least one subject is selected
                const checkedSubjects = document.querySelectorAll('.subject-checkbox:checked');
                if (checkedSubjects.length === 0) {
                    isValid = false;
                    errorMessage = 'Please select at least one subject to add.';
                }
                
                // Validate marks for checked subjects
                checkedSubjects.forEach(checkbox => {
                    const subjectId = checkbox.value;
                    const maxInput = document.querySelector(`input[name="max_marks[${subjectId}]"]`);
                    const passInput = document.querySelector(`input[name="pass_marks[${subjectId}]"]`);
                    
                    if (!maxInput.value || !passInput.value) {
                        isValid = false;
                        errorMessage = 'Please fill all marks fields for selected subjects.';
                    }
                    
                    if (maxInput.value && passInput.value) {
                        const max = parseFloat(maxInput.value);
                        const pass = parseFloat(passInput.value);
                        
                        if (pass > max) {
                            isValid = false;
                            errorMessage = 'Pass marks cannot be greater than maximum marks.';
                        }
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert(errorMessage);
                    return false;
                }
                
                // Show loading state
                const submitBtn = e.submitter;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Adding...';
                submitBtn.disabled = true;
                
                return true;
            });
            
            // Select/Deselect all checkbox
            const selectAllBtn = document.createElement('button');
            selectAllBtn.type = 'button';
            selectAllBtn.className = 'btn btn-sm btn-outline-secondary mb-3';
            selectAllBtn.innerHTML = '<i class="fas fa-check-double me-1"></i>Select All';
            
            let allSelected = true;
            
            selectAllBtn.addEventListener('click', function() {
                const checkboxes = document.querySelectorAll('.subject-checkbox');
                allSelected = !allSelected;
                
                checkboxes.forEach(cb => {
                    cb.checked = allSelected;
                });
                
                this.innerHTML = allSelected 
                    ? '<i class="fas fa-check-double me-1"></i>Deselect All' 
                    : '<i class="fas fa-check-double me-1"></i>Select All';
            });
            
            // Add select all button if subjects exist
            const tableHeader = document.querySelector('.table-header');
            if (tableHeader && document.querySelector('.subject-checkbox')) {
                const headerDiv = document.createElement('div');
                headerDiv.className = 'mt-2';
                headerDiv.appendChild(selectAllBtn);
                tableHeader.appendChild(headerDiv);
            }
        });
    </script>
</body>
</html>