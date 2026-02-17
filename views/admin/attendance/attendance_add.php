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
        'page_title' => 'Attendance Management | Add Record',
        'page_header' => 'Add Attendance',
        'back_button' => 'Back to Attendance',
        
        // Filter Card
        'filter_title' => 'Filter Attendance Data',
        'filter_section' => 'Section',
        'filter_section_select' => 'Select Section',
        'filter_class' => 'Class',
        'filter_class_select' => 'Select Class',
        'filter_current_section' => 'Current Section',
        'filter_date' => 'Date',
        'filter_subject' => 'Subject',
        'filter_subject_select' => 'Select Subject',
        
        // Action Buttons
        'action_mark_all_present' => 'Mark All Present',
        'action_mark_all_absent' => 'Mark All Absent',
        'action_reset_all' => 'Reset All',
        'action_save' => 'Save Attendance Record',
        
        // Students Table
        'students_title' => 'Student Attendance',
        'table_student' => 'Student',
        'table_gender' => 'Gender',
        'table_status' => 'Status',
        'table_remarks' => 'Remarks',
        'remarks_placeholder' => 'Add remarks...',
        
        // Empty States
        'empty_no_students' => 'No students to display',
        'empty_no_students_message' => 'Select a class, subject, and date to load student attendance data',
        'empty_select_subject' => 'Select a subject',
        'empty_select_subject_message' => 'Choose a subject to view and manage student attendance',
        'empty_no_students_found' => 'No students found',
        'empty_no_students_found_message' => 'No students are enrolled in this class or no data is available for the selected criteria',
        'empty_loading' => 'Loading student data...',
        'empty_loading_message' => 'Please wait while we fetch the attendance records',
        'empty_error' => 'Error loading data',
        'empty_error_message' => 'Unable to load student data. Please try again.',
        
        // Status Options
        'status_present' => '✅ Present',
        'status_absent' => '❌ Absent',
        'status_late' => '⏰ Late',
        'status_excused' => '📋 Excused',
        
        // Gender Labels
        'gender_male' => 'Male',
        'gender_female' => 'Female',
        
        // Toast Notifications
        'toast_title' => 'Attendance Manager',
        'toast_all_present' => 'All students marked as present',
        'toast_all_absent' => 'All students marked as absent',
        'toast_all_reset' => 'All statuses reset',
        
        // Success Messages
        'success_saved' => 'Attendance saved successfully',
        
        // Access Denied
        'access_denied' => 'Access denied',
        
        // Database Labels
        'unknown_class' => 'Unknown Class',
        'unknown_subject' => 'Unknown Subject',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة الحضور | إضافة سجل',
        'page_header' => 'إضافة حضور',
        'back_button' => 'رجوع إلى الحضور',
        
        // Filter Card
        'filter_title' => 'تصفية بيانات الحضور',
        'filter_section' => 'القسم',
        'filter_section_select' => 'اختر القسم',
        'filter_class' => 'الفصل',
        'filter_class_select' => 'اختر الفصل',
        'filter_current_section' => 'القسم الحالي',
        'filter_date' => 'التاريخ',
        'filter_subject' => 'المادة',
        'filter_subject_select' => 'اختر المادة',
        
        // Action Buttons
        'action_mark_all_present' => 'تحديد الكل حاضر',
        'action_mark_all_absent' => 'تحديد الكل غائب',
        'action_reset_all' => 'إعادة تعيين الكل',
        'action_save' => 'حفظ سجل الحضور',
        
        // Students Table
        'students_title' => 'حضور الطلاب',
        'table_student' => 'الطالب',
        'table_gender' => 'الجنس',
        'table_status' => 'الحالة',
        'table_remarks' => 'ملاحظات',
        'remarks_placeholder' => 'أضف ملاحظات...',
        
        // Empty States
        'empty_no_students' => 'لا توجد طلاب للعرض',
        'empty_no_students_message' => 'حدد الفصل والمادة والتاريخ لتحميل بيانات حضور الطلاب',
        'empty_select_subject' => 'اختر مادة',
        'empty_select_subject_message' => 'اختر مادة لعرض وإدارة حضور الطلاب',
        'empty_no_students_found' => 'لم يتم العثور على طلاب',
        'empty_no_students_found_message' => 'لا يوجد طلاب مسجلين في هذا الفصل أو لا تتوفر بيانات للمعايير المحددة',
        'empty_loading' => 'جاري تحميل بيانات الطلاب...',
        'empty_loading_message' => 'يرجى الانتظار أثناء جلب سجلات الحضور',
        'empty_error' => 'خطأ في تحميل البيانات',
        'empty_error_message' => 'تعذر تحميل بيانات الطلاب. يرجى المحاولة مرة أخرى.',
        
        // Status Options
        'status_present' => '✅ حاضر',
        'status_absent' => '❌ غائب',
        'status_late' => '⏰ متأخر',
        'status_excused' => '📋 معذور',
        
        // Gender Labels
        'gender_male' => 'ذكر',
        'gender_female' => 'أنثى',
        
        // Toast Notifications
        'toast_title' => 'مدير الحضور',
        'toast_all_present' => 'تم تحديد جميع الطلاب كحاضرين',
        'toast_all_absent' => 'تم تحديد جميع الطلاب كغائبين',
        'toast_all_reset' => 'تم إعادة تعيين جميع الحالات',
        
        // Success Messages
        'success_saved' => 'تم حفظ الحضور بنجاح',
        
        // Access Denied
        'access_denied' => 'تم رفض الوصول',
        
        // Database Labels
        'unknown_class' => 'فصل غير معروف',
        'unknown_subject' => 'مادة غير معروفة',
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

/* ---------- AUTH ---------- */
$logged_in_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$logged_in_id || $role !== 'admin') die(t('access_denied'));

/* ---------- ADMIN NAME ---------- */
$admin = $conn->query("SELECT first_name, last_name FROM users WHERE id=$logged_in_id")->fetch_assoc();
$recorded_by = "Admin " . trim($admin['first_name'].' '.$admin['last_name']);

/* ---------- LOAD CLASSES & SUBJECTS ---------- */
$classes = $conn->query("SELECT id, name FROM classes ORDER BY name")->fetch_all(MYSQLI_ASSOC);
$subjects = $conn->query("SELECT id, name FROM subjects ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = ($theme_row['mode'] ?? 0);
$theme_class = ($theme === 0) ? 'dark-theme' : 'light-theme';

/* ---------- SAVE ATTENDANCE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!validate_csrf_token($_POST['csrf_token'])) die('Invalid CSRF token');

    $class_id   = (int)$_POST['class_id'];
    $section_id = (int)$_POST['section_id'];
    $subject_id = (int)$_POST['subject_id'];
    $date       = $_POST['date'];

    // Get human-readable class and subject names
    $class_name = $conn->query("SELECT name FROM classes WHERE id=$class_id")->fetch_assoc()['name'] ?? t('unknown_class');
    $subject_name = $conn->query("SELECT name FROM subjects WHERE id=$subject_id")->fetch_assoc()['name'] ?? t('unknown_subject');

    $stmt = $conn->prepare("
        INSERT INTO attendance
        (student_id, class_id, section_id, subject_id, `date`, status, remarks, recorded_by)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            status=VALUES(status),
            remarks=VALUES(remarks),
            recorded_by=VALUES(recorded_by)
    ");

    foreach ($_POST['status'] as $student_id => $status) {
        $remarks = $_POST['remarks'][$student_id] ?? '';
        $stmt->bind_param('iiiissss', $student_id, $class_id, $section_id, $subject_id, $date, $status, $remarks, $recorded_by);
        $stmt->execute();
    }

    /* ---------- AUDIT LOG ---------- */
    $description = sprintf(
        "Saved attendance for class %s, subject %s by %s on %s",
        $class_name,
        $subject_name,
        $recorded_by,
        $date
    );

    log_audit(
        $conn,
        $logged_in_id,
        'ATTENDANCE_SAVE',
        $description
    );

    flash_message('success', t('success_saved'));
    header('Location: attendance.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('page_title') ?></title>
<link href="../../assets/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<!-- Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<style>
    /* Tom Select Theme Tweaks */
    .ts-control {
        border-radius: 0.5rem;
        padding: 0.5rem 1rem;
        border-color: #dee2e6;
    }
    .dark-theme .ts-control {
        background-color: #020617;
        border-color: #334155;
        color: #e5e7eb;
    }
    .dark-theme .ts-dropdown {
        background-color: #020617;
        border-color: #334155;
        color: #e5e7eb;
    }
    .dark-theme .ts-dropdown .option {
        color: #e5e7eb;
    }
    .dark-theme .ts-dropdown .active {
        background-color: #1e293b;
        color: #fff;
    }
    .dark-theme .ts-control input {
        color: #e5e7eb;
    }
</style>
</head>


<body class="<?= $theme_class ?>">


<!-- ANIMATED BACKGROUND ELEMENTS -->
<div class="bg-element bg-1"></div>
<div class="bg-element bg-2"></div>
<div class="bg-element bg-3"></div>

<div class="container-fluid">
    <div class="page-header">
        <h3><i class="fas fa-calendar-check me-2"></i><?= t('page_header') ?></h3>
        <a href="attendance.php" class="back-btn">
            <i class="fas fa-arrow-left"></i> <?= t('back_button') ?>
        </a>
    </div>

    <form method="post">
        <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

        <!-- FILTER CARD -->
        <div class="card-box">
            <div class="filter-section-title">
                <i class="fas fa-filter"></i> <?= t('filter_title') ?>
            </div>
            
            <div class="filter-flex">
                <!-- SECTION FIRST -->
                <div class="filter-item">
                    <label><i class="fas fa-layer-group"></i> <?= t('filter_section') ?></label>
                    <select id="section_select" class="form-select">
                        <option value=""><?= t('filter_section_select') ?></option>
                    </select>
                    <input type="hidden" id="section_id" name="section_id">
                </div>

                <!-- CLASS DEPENDS ON SECTION -->
                <div class="filter-item">
                    <label><i class="fas fa-school"></i> <?= t('filter_class') ?></label>
                    <select id="class_id" name="class_id" class="form-select" required>
                        <option value=""><?= t('filter_class_select') ?></option>
                    </select>
                </div>

                <!-- READ ONLY CONFIRMATION -->
                <div class="filter-item">
                    <label><i class="fas fa-check-circle"></i> <?= t('filter_current_section') ?></label>
                    <input type="text" id="section_name" class="form-control" readonly>
                </div>

                <div class="filter-item">
                    <label><i class="fas fa-calendar-day"></i> <?= t('filter_date') ?></label>
                    <input type="date" name="date" id="date" class="form-control" value="<?= date('Y-m-d') ?>" required>
                </div>

                <div class="filter-item">
                    <label><i class="fas fa-book-open"></i> <?= t('filter_subject') ?></label>
                    <select id="subject_id" name="subject_id" class="form-select" required disabled>
                        <option value=""><?= t('filter_subject_select') ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- ACTION BUTTONS -->
        <div class="action-bar">
            <button type="button" class="action-btn btn-success" onclick="markAll('present')">
                <i class="fas fa-check-circle"></i> <?= t('action_mark_all_present') ?>
            </button>
            <button type="button" class="action-btn btn-danger" onclick="markAll('absent')">
                <i class="fas fa-times-circle"></i> <?= t('action_mark_all_absent') ?>
            </button>
            <button type="button" class="action-btn btn-secondary" onclick="markAll('')">
                <i class="fas fa-undo"></i> <?= t('action_reset_all') ?>
            </button>
        </div>

        <!-- STUDENTS TABLE -->
        <div class="card-box">
            <div class="filter-section-title">
                <i class="fas fa-users"></i> <?= t('students_title') ?>
            </div>
            
            <div class="table-container">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th><i class="fas fa-user-graduate me-1"></i> <?= t('table_student') ?></th>
                            <th><i class="fas fa-venus-mars me-1"></i> <?= t('table_gender') ?></th>
                            <th><i class="fas fa-clipboard-check me-1"></i> <?= t('table_status') ?></th>
                            <th><i class="fas fa-comment-dots me-1"></i> <?= t('table_remarks') ?></th>
                        </tr>
                    </thead>
                    <tbody id="students_body">
                        <tr>
                            <td colspan="4" class="text-center">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h4><?= t('empty_no_students') ?></h4>
                                    <p><?= t('empty_no_students_message') ?></p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="sticky-save text-end">
                <button class="save-btn">
                    <i class="fas fa-save"></i> <?= t('action_save') ?>
                </button>
            </div>
        </div>
    </form>
</div>

<script src="../../assets/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', init);

// Get translations for JavaScript
const translations = {
    loading: "<?= t('empty_loading') ?>",
    loading_message: "<?= t('empty_loading_message') ?>",
    no_students_found: "<?= t('empty_no_students_found') ?>",
    no_students_found_message: "<?= t('empty_no_students_found_message') ?>",
    error_loading: "<?= t('empty_error') ?>",
    error_loading_message: "<?= t('empty_error_message') ?>",
    select_subject: "<?= t('empty_select_subject') ?>",
    select_subject_message: "<?= t('empty_select_subject_message') ?>",
    toast_title: "<?= t('toast_title') ?>",
    toast_all_present: "<?= t('toast_all_present') ?>",
    toast_all_absent: "<?= t('toast_all_absent') ?>",
    toast_all_reset: "<?= t('toast_all_reset') ?>",
    status_present: "<?= t('status_present') ?>",
    status_absent: "<?= t('status_absent') ?>",
    status_late: "<?= t('status_late') ?>",
    status_excused: "<?= t('status_excused') ?>",
    remarks_placeholder: "<?= t('remarks_placeholder') ?>",
    gender_male: "<?= t('gender_male') ?>",
    gender_female: "<?= t('gender_female') ?>"
};

/* =====================
   INIT
   ===================== */
async function init() {
    await loadSections();
    await loadClasses(); // load ALL classes initially
}

/* =====================
   LOAD SECTIONS
   ===================== */
const sectionSelect = document.getElementById('section_select');
const classSelect = document.getElementById('class_id');
const sectionNameInput = document.getElementById('section_name');
const sectionIdInput = document.getElementById('section_id');

async function loadSections() {
    const res = await fetch('get_attendance_data.php?mode=sections');
    const data = await res.json();
    sectionSelect.innerHTML = `<option value=""><?= t('filter_section_select') ?></option>`;
    data.sections.forEach(sec => {
        sectionSelect.innerHTML += `<option value="${sec.id}">${sec.name}</option>`;
    });
    refreshTomSelect('section_select');
}

async function loadClasses(sectionId = '') {
    let url = 'get_attendance_data.php?mode=classes';
    if (sectionId) url += `&section_id=${sectionId}`;

    const res = await fetch(url);
    const data = await res.json();

    classSelect.innerHTML = `<option value=""><?= t('filter_class_select') ?></option>`;
    data.classes.forEach(c => {
        classSelect.innerHTML += `<option value="${c.id}" data-section="${c.section_id}">${c.name}</option>`;
    });
    refreshTomSelect('class_id');
}

/* ================== SECTION CHANGE ================== */
sectionSelect.addEventListener('change', async () => {
    const secId = sectionSelect.value;
    sectionIdInput.value = secId;
    sectionNameInput.value = sectionSelect.options[sectionSelect.selectedIndex].text;
    await loadClasses(secId);
    resetSubjects();
    resetStudents();
});

/* ================== CLASS CHANGE ================== */
classSelect.addEventListener('change', async () => {
    const clsId = classSelect.value;
    if (!clsId) return;

    // Get section of this class from data-section attribute
    const selectedOption = classSelect.options[classSelect.selectedIndex];
    const secId = selectedOption.dataset.section;

    sectionIdInput.value = secId;
    sectionNameInput.value = sectionSelect.querySelector(`option[value="${secId}"]`)?.text || '';

    // Load subjects for this class
    const res = await fetch(`get_attendance_data.php?mode=subjects&class_id=${clsId}`);
    const data = await res.json();

    const subjectSelect = document.getElementById('subject_id');
    subjectSelect.disabled = false;
    subjectSelect.innerHTML = `<option value=""><?= t('filter_subject_select') ?></option>`;
    data.subjects.forEach(s => {
        subjectSelect.innerHTML += `<option value="${s.id}">${s.name}</option>`;
    });
    refreshTomSelect('subject_id');

    resetStudents();
});

/* ================== INIT ================== */
document.addEventListener('DOMContentLoaded', async () => {
    await loadSections();
    await loadClasses(); // load all classes as default
});

/* =====================
   LOAD STUDENTS
   ===================== */
document.getElementById('subject_id').addEventListener('change', loadStudents);
document.getElementById('date').addEventListener('change', loadStudents);

async function loadStudents() {
    const classId   = document.getElementById('class_id').value;
    const sectionId = document.getElementById('section_id').value;
    const subjectId = document.getElementById('subject_id').value;
    const date      = document.getElementById('date').value;

    if (!classId || !sectionId || !subjectId || !date) return;

    // Show loading state
    const tbody = document.getElementById('students_body');
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="text-center py-5">
                <div class="empty-state">
                    <div class="loading"></div>
                    <h4>${translations.loading}</h4>
                    <p>${translations.loading_message}</p>
                </div>
            </td>
        </tr>`;
    
    try {
        const res = await fetch(
            `get_attendance_data.php?mode=students&class_id=${classId}&section_id=${sectionId}&subject_id=${subjectId}&date=${date}`
        );
        const data = await res.json();

        tbody.innerHTML = '';

        if (data.students && data.students.length > 0) {
            data.students.forEach(st => {
                const a = data.attendance[st.id] || {};
                const gender = st.gender === 'Male' ? translations.gender_male : translations.gender_female;
                const genderClass = st.gender === 'Male' ? 'bg-primary' : 'bg-pink';
                const genderGradient = st.gender === 'Male' ? 
                    'linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%)' : 
                    'linear-gradient(135deg, #f472b6 0%, #db2777 100%)';
                
                tbody.innerHTML += `
                <tr>
                    <td>
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="width: 36px; height: 36px; background: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                ${st.name.charAt(0).toUpperCase()}
                            </div>
                            <div>
                                <div class="fw-semibold">${st.name}</div>
                                <div class="text-muted small">ID: ${st.id}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span class="badge ${genderClass} p-2" style="background: ${genderGradient}!important; color: white;">
                            ${gender}
                        </span>
                    </td>
                    <td>
                        <select name="status[${st.id}]" class="status-select" style="border-color: ${getStatusColor(a.status)}; background-color: ${getStatusColor(a.status)}20;">
                            <option value="present" ${a.status=='present'?'selected':''}>${translations.status_present}</option>
                            <option value="absent" ${a.status=='absent'?'selected':''}>${translations.status_absent}</option>
                            <option value="late" ${a.status=='late'?'selected':''}>${translations.status_late}</option>
                            <option value="excused" ${a.status=='excused'?'selected':''}>${translations.status_excused}</option>
                        </select>
                    </td>
                    <td>
                        <input type="text" name="remarks[${st.id}]" value="${a.remarks||''}" class="remarks-input" placeholder="${translations.remarks_placeholder}">
                    </td>
                </tr>`;
            });
        } else {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">
                        <div class="empty-state">
                            <i class="fas fa-user-slash"></i>
                            <h4>${translations.no_students_found}</h4>
                            <p>${translations.no_students_found_message}</p>
                        </div>
                    </td>
                </tr>`;
        }
    } catch (error) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" class="text-center">
                    <div class="empty-state">
                        <i class="fas fa-exclamation-triangle text-danger"></i>
                        <h4>${translations.error_loading}</h4>
                        <p>${translations.error_loading_message}</p>
                    </div>
                </td>
            </tr>`;
        console.error('Error loading students:', error);
    }
}

/* =====================
   HELPERS
   ===================== */
function getStatusColor(status) {
    switch(status) {
        case 'present': return '#22c55e';
        case 'absent': return '#ef4444';
        case 'late': return '#f59e0b';
        case 'excused': return '#8b5cf6';
        default: return '#e2e8f0';
    }
}

function resetSubjects() {
    const s = document.getElementById('subject_id');
    s.innerHTML = `<option value=""><?= t('filter_subject_select') ?></option>`;
    s.disabled = true;
}

function resetStudents() {
    document.getElementById('students_body').innerHTML = `
        <tr>
            <td colspan="4" class="text-center">
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h4>${translations.select_subject}</h4>
                    <p>${translations.select_subject_message}</p>
                </div>
            </td>
        </tr>`;
}

/* =====================
   MARK ALL
   ===================== */
function markAll(value) {
    document.querySelectorAll('select[name^="status"]').forEach(s => {
        s.value = value || '';
        // Update visual styling
        s.style.borderColor = getStatusColor(value);
        s.style.backgroundColor = getStatusColor(value) + '20';
    });
    
    // Show feedback
    let message = translations.toast_all_reset;
    if (value === 'present') message = translations.toast_all_present;
    if (value === 'absent') message = translations.toast_all_absent;
    
    showToast(message);
}

/* =====================
   TOAST NOTIFICATION
   ===================== */
function showToast(message) {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = 'position-fixed bottom-0 end-0 p-3';
    toast.style.zIndex = '1050';
    
    toast.innerHTML = `
        <div class="toast show" role="alert">
            <div class="toast-header" style="background: var(--primary-gradient); color: white;">
                <i class="fas fa-check-circle me-2"></i>
                <strong class="me-auto">${translations.toast_title}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Auto remove after 3 seconds
    setTimeout(() => {
        toast.remove();
    }, 3000);
}
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

function refreshTomSelect(id) {
    const el = document.getElementById(id);
    if (el.tomselect) {
        el.tomselect.destroy();
    }
    new TomSelect(el, {
        create: false,
        sortField: {
            field: "text",
            direction: "asc"
        },
        plugins: ['remove_button']
    });
}
</script>



<style>
        :root {
            --primary-gradient: linear-gradient(135deg, #4361ee 0%, #3a0ca3 100%);
            --secondary-gradient: linear-gradient(135deg, #4cc9f0 0%, #4361ee 100%);
            --success-gradient: linear-gradient(135deg, #4ade80 0%, #22c55e 100%);
            --danger-gradient: linear-gradient(135deg, #f87171 0%, #ef4444 100%);
            --card-gradient: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-sm: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
            --shadow-md: 0 10px 25px -5px rgba(0, 0, 0, 0.07), 0 5px 10px -5px rgba(0, 0, 0, 0.04);
            --shadow-lg: 0 20px 40px -10px rgba(0, 0, 0, 0.1), 0 10px 20px -5px rgba(0, 0, 0, 0.04);
            --radius-xl: 20px;
            --radius-lg: 16px;
            --radius-md: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* =========================
            DARK THEME
            ========================= */
            .dark-theme {
                --primary-gradient: linear-gradient(135deg, #020617 0%, #0f172a 100%);
                --secondary-gradient: linear-gradient(135deg, #1e293b 0%, #020617 100%);
                --card-gradient: linear-gradient(135deg, #020617 0%, #020617 100%);

                --glass-bg: rgba(15, 23, 42, 0.75);
                --glass-border: rgba(148, 163, 184, 0.12);

                color: #e5e7eb;
            }


        * {
            box-sizing: border-box;
        }

        body {
            background: linear-gradient(135deg, #f0f4ff 0%, #f8fafc 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', system-ui, sans-serif;
            color: #334155;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            overflow-x: hidden;
        }
        body.dark-theme {
            background: linear-gradient(135deg, #020617 0%, #0f172a 100%);
            color: #e5e7eb;
        }


        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 10% 20%, rgba(100, 116, 255, 0.08) 0%, transparent 20%),
                radial-gradient(circle at 90% 80%, rgba(74, 222, 128, 0.05) 0%, transparent 20%),
                radial-gradient(circle at 50% 50%, rgba(168, 85, 247, 0.03) 0%, transparent 30%);
            z-index: -1;
        }
                body.dark-theme .card-box,
        body.dark-theme .action-bar,
        body.dark-theme .sticky-save {
            background: rgba(15, 23, 42, 0.85);
            border-color: rgba(148, 163, 184, 0.15);
        }

        body.dark-theme .form-control,
        body.dark-theme .form-select,
        body.dark-theme .remarks-input,
        body.dark-theme .status-select {
            background-color: #020617;
            color: #e5e7eb;
            border-color: #334155;
        }

        body.dark-theme .table tbody tr {
            background: #020617;
        }

        body.dark-theme .table tbody tr:nth-child(even) {
            background: #020617;
        }

        body.dark-theme .filter-section-title,
        body.dark-theme label,
        body.dark-theme .page-header h3 {
            color: #e5e7eb;
        }

        /* ANIMATED BACKGROUND ELEMENTS */
        .bg-element {
            position: fixed;
            border-radius: 50%;
            filter: blur(60px);
            opacity: 0.4;
            z-index: -1;
        }

        .bg-1 {
            width: 300px;
            height: 300px;
            background: #4361ee;
            top: -100px;
            right: -100px;
        }

        .bg-2 {
            width: 200px;
            height: 200px;
            background: #4cc9f0;
            bottom: -50px;
            left: -50px;
        }

        .bg-3 {
            width: 150px;
            height: 150px;
            background: #f472b6;
            top: 50%;
            left: 10%;
        }

        /* CONTAINER */
        .container-fluid {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* HEADER */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2.5rem;
            padding: 1.5rem 0;
            position: relative;
        }

        .page-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 1px;
            background: linear-gradient(to right, transparent, rgba(99, 102, 241, 0.2), transparent);
        }

        .page-header h3 {
            font-weight: 800;
            font-size: 2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
            display: inline-block;
            position: relative;
            padding-left: 1.8rem;
        }

        .page-header h3::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 8px;
            height: 80%;
            background: var(--primary-gradient);
            border-radius: 4px;
        }

        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            background: rgba(255, 255, 255, 0.9);
            border: 1px solid rgba(226, 232, 240, 0.8);
            border-radius: var(--radius-lg);
            color: #64748b;
            font-weight: 600;
            text-decoration: none;
            transition: var(--transition);
            box-shadow: var(--shadow-sm);
            backdrop-filter: blur(10px);
        }

        .back-btn:hover {
            background: white;
            border-color: #94a3b8;
            color: #475569;
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        /* CARD DESIGN */
        .card-box {
            background: var(--glass-bg);
            border-radius: var(--radius-xl);
            box-shadow: var(--shadow-lg);
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
        }

        .card-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 4px;
            background: var(--primary-gradient);
        }

        .card-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        /* FILTER SECTION */
        .filter-section-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: #475569;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .filter-section-title i {
            color: #4361ee;
            font-size: 1.2rem;
        }

        .filter-flex {
            display: flex;
            flex-wrap: wrap;
            gap: 1.5rem;
        }

        .filter-item {
            display: flex;
            flex-direction: column;
            min-width: 220px;
            flex: 1;
        }

        .filter-item label {
            font-size: 0.875rem;
            font-weight: 600;
            color: #64748b;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-item label i {
            color: #94a3b8;
            font-size: 0.875rem;
        }

        /* FORM CONTROLS */
        .form-select, .form-control {
            padding: 0.875rem 1rem;
            border: 1.5px solid #e2e8f0;
            border-radius: var(--radius-md);
            font-size: 1rem;
            transition: var(--transition);
            background-color: white;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
        }

        .form-select:focus, .form-control:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.15);
            outline: none;
        }

        .form-select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234761ee' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 16px;
            padding-right: 2.5rem;
        }

        /* ACTION BUTTONS */
        .action-bar {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            margin-bottom: 2rem;
            padding: 1.5rem;
            background: var(--glass-bg);
            border-radius: var(--radius-lg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .action-btn {
            padding: 0.875rem 1.75rem;
            border-radius: var(--radius-md);
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            transition: var(--transition);
            border: none;
            cursor: pointer;
            flex: 1;
            min-width: 180px;
            box-shadow: var(--shadow-sm);
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-md);
        }

        .action-btn:active {
            transform: translateY(-1px);
        }

        .btn-success {
            background: var(--success-gradient);
            color: white;
        }

        .btn-danger {
            background: var(--danger-gradient);
            color: white;
        }

        .btn-secondary {
            background: linear-gradient(135deg, #94a3b8 0%, #64748b 100%);
            color: white;
        }

        /* TABLE DESIGN */
        .table-container {
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-md);
            margin-top: 1rem;
        }

        .table {
            margin-bottom: 0;
            border-collapse: separate;
            border-spacing: 0;
            width: 100%;
        }

        .table thead {
            background: var(--primary-gradient);
        }

        .table th {
            font-weight: 700;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: white;
            padding: 1.25rem 1.5rem;
            border: none;
            position: relative;
        }

        .table th:not(:last-child)::after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: rgba(255, 255, 255, 0.2);
        }

        .table tbody tr {
            transition: var(--transition);
            background: white;
        }

        .table tbody tr:nth-child(even) {
            background: #f8fafc;
        }

        .table tbody tr:hover {
            background: rgba(67, 97, 238, 0.05);
            transform: translateX(4px);
        }

        .table td {
            padding: 1.25rem 1.5rem;
            border-top: 1px solid #f1f5f9;
            vertical-align: middle;
        }

        /* STATUS SELECT STYLING */
        .status-select {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            border: 1.5px solid #e2e8f0;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='%234155b5' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 12px;
            padding-right: 2rem;
        }

        .status-select:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* REMARKS INPUT */
        .remarks-input {
            padding: 0.75rem 1rem;
            border-radius: var(--radius-sm);
            border: 1.5px solid #e2e8f0;
            transition: var(--transition);
            width: 100%;
        }

        .remarks-input:focus {
            border-color: #4361ee;
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
            outline: none;
        }

        /* STICKY SAVE */
        .sticky-save {
            position: sticky;
            bottom: 0;
            background: var(--glass-bg);
            padding: 1.5rem 2rem;
            box-shadow: 0 -10px 30px rgba(0, 0, 0, 0.08);
            border-radius: 0 0 var(--radius-xl) var(--radius-xl);
            border-top: 1px solid var(--glass-border);
            margin-top: 1rem;
            backdrop-filter: blur(10px);
            z-index: 10;
        }

        .save-btn {
            padding: 1rem 2.5rem;
            background: var(--primary-gradient);
            color: white;
            border: none;
            border-radius: var(--radius-lg);
            font-weight: 700;
            font-size: 1.1rem;
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            transition: var(--transition);
            cursor: pointer;
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
        }

        .save-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(67, 97, 238, 0.4);
        }

        .save-btn:active {
            transform: translateY(-1px);
        }

        /* EMPTY STATE */
        .empty-state {
            padding: 4rem 2rem;
            text-align: center;
            color: #94a3b8;
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .empty-state h4 {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #64748b;
        }

        .empty-state p {
            font-size: 0.95rem;
            max-width: 400px;
            margin: 0 auto;
        }

        /* LOADING INDICATOR */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
            margin-right: 8px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* RESPONSIVE DESIGN */
        @media (max-width: 1200px) {
            .filter-item {
                min-width: calc(50% - 0.75rem);
            }
        }

        @media (max-width: 768px) {
            body {
                padding: 15px;
            }
            
            .page-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 1.5rem;
            }
            
            .page-header h3 {
                font-size: 1.75rem;
            }
            
            .filter-item {
                min-width: 100%;
            }
            
            .action-btn {
                min-width: 100%;
            }
            
            .table {
                display: block;
                overflow-x: auto;
            }
            
            .table th, .table td {
                white-space: nowrap;
            }
            
            .sticky-save {
                text-align: center;
            }
            
            .save-btn {
                width: 100%;
                justify-content: center;
            }
        }

        @media (max-width: 576px) {
            .card-box {
                padding: 1.5rem;
            }
            
            .action-bar {
                padding: 1rem;
            }
            
            .table th, .table td {
                padding: 1rem;
            }
        }

        /* ANIMATIONS */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card-box, .action-bar, .table-container {
            animation: fadeIn 0.5s ease-out forwards;
        }

        /* PULSE ANIMATION FOR EMPTY STATE */
        @keyframes pulse {
            0% { opacity: 0.5; }
            50% { opacity: 1; }
            100% { opacity: 0.5; }
        }

        .empty-state i {
            animation: pulse 2s infinite;
        }

        /* CUSTOM SCROLLBAR */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
</style>
</body>
</html>
