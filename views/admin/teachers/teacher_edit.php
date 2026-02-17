<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Edit Teacher | Academic System',
        'invalid_teacher_id' => 'Invalid teacher ID.',
        'teacher_not_found' => 'Teacher not found.',
        'pin_error' => 'Login PIN must be exactly 6 digits (numbers only).',
        'update_failed' => 'Update failed: ',
        'success_message' => 'Teacher updated successfully!',
        
        // Form elements
        'form_header' => 'Edit Teacher',
        'form_subtitle' => 'Update the professional information for',
        'alert_success' => 'Success!',
        'alert_error' => 'Error:',
        
        // Sections
        'section_basic_info' => 'Basic Information',
        'section_personal_details' => 'Personal Details',
        'section_contact_info' => 'Contact Information',
        'section_security' => 'Security',
        'section_professional_info' => 'Professional Information',
        
        // Labels
        'label_employee_code' => 'Employee Code',
        'label_status' => 'Status',
        'label_first_name' => 'First Name',
        'label_last_name' => 'Last Name',
        'label_gender' => 'Gender',
        'label_hire_date' => 'Hire Date',
        'label_salary' => 'Salary',
        'label_phone_number' => 'Phone Number',
        'label_email_address' => 'Email Address',
        'label_login_pin' => 'Login PIN',
        'label_qualification' => 'Qualification',
        
        // Placeholders
        'placeholder_emp_code' => 'EMP-001',
        'placeholder_first_name' => 'John',
        'placeholder_last_name' => 'Doe',
        'placeholder_phone' => '+1 (555) 123-4567',
        'placeholder_email' => 'teacher@example.com',
        'placeholder_pin' => 'Enter new 6-digit PIN (leave blank to keep current)',
        'placeholder_qualification' => 'M.Ed, B.Sc, Ph.D, etc.',
        
        // Status options
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'status_suspended' => 'Suspended',
        
        // Gender options
        'gender_male' => 'Male',
        'gender_female' => 'Female',
        'gender_other' => 'Other',
        
        // Validation messages
        'validation_required' => 'is required.',
        'validation_phone' => 'Please enter a valid phone number.',
        'validation_email' => 'Please enter a valid email address.',
        'validation_pin' => 'PIN must be exactly 6 digits (numbers only).',
        
        // Buttons
        'btn_back_teachers' => 'Back to Teachers',
        'btn_update_teacher' => 'Update Teacher',
        
        // Stats
        'stat_current_status' => 'Current Status',
        'stat_employee_since' => 'Employee Since',
        'stat_gender' => 'Gender',
        'stat_last_updated' => 'Last Updated',
        
        // JavaScript messages
        'js_unsaved_changes' => 'You have unsaved changes. Are you sure you want to leave?',
        'js_confirm_cancel' => 'You have unsaved changes. Are you sure you want to cancel?'
    ],
    2 => [ // Arabic - Simple language
        'page_title' => 'تعديل المعلم | النظام الأكاديمي',
        'invalid_teacher_id' => 'معرف المعلم غير صالح.',
        'teacher_not_found' => 'المعلم غير موجود.',
        'pin_error' => 'يجب أن يكون رمز الدخول مكون من 6 أرقام فقط.',
        'update_failed' => 'فشل التحديث: ',
        'success_message' => 'تم تحديث بيانات المعلم بنجاح!',
        
        // Form elements
        'form_header' => 'تعديل المعلم',
        'form_subtitle' => 'تحديث المعلومات المهنية لـ',
        'alert_success' => 'نجاح!',
        'alert_error' => 'خطأ:',
        
        // Sections
        'section_basic_info' => 'المعلومات الأساسية',
        'section_personal_details' => 'البيانات الشخصية',
        'section_contact_info' => 'معلومات الاتصال',
        'section_security' => 'الأمان',
        'section_professional_info' => 'المعلومات المهنية',
        
        // Labels
        'label_employee_code' => 'رمز الموظف',
        'label_status' => 'الحالة',
        'label_first_name' => 'الاسم الأول',
        'label_last_name' => 'الاسم الأخير',
        'label_gender' => 'الجنس',
        'label_hire_date' => 'تاريخ التعيين',
        'label_salary' => 'الراتب',
        'label_phone_number' => 'رقم الهاتف',
        'label_email_address' => 'البريد الإلكتروني',
        'label_login_pin' => 'رمز الدخول',
        'label_qualification' => 'المؤهل',
        
        // Placeholders
        'placeholder_emp_code' => 'EMP-001',
        'placeholder_first_name' => 'محمد',
        'placeholder_last_name' => 'أحمد',
        'placeholder_phone' => '+966 555 123456',
        'placeholder_email' => 'teacher@example.com',
        'placeholder_pin' => 'أدخل رمز مكون من 6 أرقام (اترك فارغًا للحفاظ على الحالي)',
        'placeholder_qualification' => 'ماجستير، بكالوريوس، دكتوراه، إلخ.',
        
        // Status options
        'status_active' => 'نشط',
        'status_inactive' => 'غير نشط',
        'status_suspended' => 'معلق',
        
        // Gender options
        'gender_male' => 'ذكر',
        'gender_female' => 'أنثى',
        'gender_other' => 'أخرى',
        
        // Validation messages
        'validation_required' => 'مطلوب.',
        'validation_phone' => 'الرجاء إدخال رقم هاتف صالح.',
        'validation_email' => 'الرجاء إدخال بريد إلكتروني صالح.',
        'validation_pin' => 'يجب أن يتكون الرمز من 6 أرقام فقط.',
        
        // Buttons
        'btn_back_teachers' => 'العودة للمعلمين',
        'btn_update_teacher' => 'تحديث المعلم',
        
        // Stats
        'stat_current_status' => 'الحالة الحالية',
        'stat_employee_since' => 'موظف منذ',
        'stat_gender' => 'الجنس',
        'stat_last_updated' => 'آخر تحديث',
        
        // JavaScript messages
        'js_unsaved_changes' => 'لديك تغييرات غير محفوظة. هل تريد المغادرة؟',
        'js_confirm_cancel' => 'لديك تغييرات غير محفوظة. هل تريد الإلغاء؟'
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

/* =======================
   READ THEME MODE
   ======================= */
$theme_mode = 1; // default light
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1");
if($themeRes && $themeRes->num_rows > 0){
    $themeRow = $themeRes->fetch_assoc();
    $theme_mode = (int)$themeRow['mode'];
}
$isDark = ($theme_mode === 0);

/* =======================
   GET TEACHER
   ======================= */
$id = $_GET['id'] ?? null;
if(!$id){
    die(t('invalid_teacher_id'));
}

$stmt = $conn->prepare("SELECT * FROM teachers WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();
$teacher = $res->fetch_assoc();
if(!$teacher){
    die(t('teacher_not_found'));
}

/* =======================
   HANDLE UPDATE
   ======================= */
$success = '';
$error   = '';

if($_SERVER['REQUEST_METHOD'] === 'POST'){
    $first_name     = $_POST['first_name'] ?? '';
    $last_name      = $_POST['last_name'] ?? '';
    $gender         = $_POST['gender'] ?? '';
    $phone          = $_POST['phone'] ?? '';
    $email          = $_POST['email'] ?? '';
    $qualification  = $_POST['qualification'] ?? '';
    $hire_date      = $_POST['hire_date'] ?? '';
    $status         = $_POST['status'] ?? 'active';
    $employee_code  = $_POST['employee_code'] ?? '';
    $login_pin      = $_POST['login_pin'] ?? '';
    $salary = (float)($_POST['salary'] ?? 0);


    // PIN validation (only if provided)
    if($login_pin !== '' && !preg_match('/^\d{6}$/', $login_pin)){
        $error = t('pin_error');
    } else {

        // Build query dynamically (PIN only updates if entered)
        if($login_pin !== ''){
    $update = $conn->prepare("
        UPDATE teachers SET
        first_name=?, last_name=?, gender=?, phone=?, email=?,
        qualification=?, hire_date=?, status=?, salary=?, employee_code=?, login_pin=?
        WHERE id=?
    ");

    $update->bind_param(
        "sssssssssdsi",
        $first_name,
        $last_name,
        $gender,
        $phone,
        $email,
        $qualification,
        $hire_date,
        $status,
        $salary,         // d
        $employee_code, // s
        $login_pin,      // s
        $id              // i
    );
}
else {
    $update = $conn->prepare("
        UPDATE teachers SET
        first_name=?, last_name=?, gender=?, phone=?, email=?,
        qualification=?, hire_date=?, status=?, salary=?, employee_code=?
        WHERE id=?
    ");

    $update->bind_param(
        "ssssssssdsi",
        $first_name,
        $last_name,
        $gender,
        $phone,
        $email,
        $qualification,
        $hire_date,
        $status,
        $salary,         // d
        $employee_code, // s
        $id              // i
    );
}


        if($update->execute()){
            $success = t('success_message');
            $teacher = array_merge($teacher, $_POST);
        } else {
            $error = t('update_failed') . $conn->error;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Dark Mode Tweaks */
        body.dark .ts-control {
            background-color: var(--dark-bg);
            border-color: #4b5563;
            color: #fff;
        }
        body.dark .ts-dropdown {
            background-color: var(--dark-card);
            border-color: #4b5563;
            color: #fff;
        }
        body.dark .ts-dropdown .option {
            color: #e2e8f0;
        }
        body.dark .ts-dropdown .active {
            background-color: var(--primary-color);
            color: #fff;
        }
        body.dark .ts-control input {
            color: #fff;
        }
    </style>
</head>
<body class="<?php echo $isDark ? 'dark' : 'light'; ?>">
    <div class="form-container fade-in">
        <div class="glass-card">
            <div class="form-header">
                <h2 class="mb-2 d-flex align-items-center gap-3">
                    <i class="bi bi-person-badge-fill fs-1"></i>
                    <div>
                        <?= t('form_header') ?>
                        <div class="teacher-id-badge">
                            <i class="bi bi-tag"></i>
                            ID: <?php echo htmlspecialchars($id); ?>
                        </div>
                    </div>
                </h2>
                <p class="mb-0 opacity-75">
                    <?= t('form_subtitle') ?> <?php echo htmlspecialchars($teacher['first_name'] . ' ' . $teacher['last_name']); ?>
                </p>
            </div>
            
            <?php if($success): ?>
                <div class="alert alert-success m-4 d-flex align-items-center fade-in">
                    <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                    <div>
                        <strong><?= t('alert_success') ?></strong> <?php echo htmlspecialchars($success); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <?php if($error): ?>
                <div class="alert alert-danger m-4 d-flex align-items-center fade-in">
                    <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
                    <div>
                        <strong><?= t('alert_error') ?></strong> <?php echo htmlspecialchars($error); ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <form method="post" class="form-body" id="editTeacherForm">
                <h3 class="form-section-title fade-in-delay-1">
                    <i class="bi bi-person-lines-fill"></i>
                    <?= t('section_basic_info') ?>
                </h3>
                
                <div class="form-grid fade-in-delay-1">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-badge"></i>
                            <?= t('label_employee_code') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-upc-scan input-group-icon"></i>
                            <input type="text" name="employee_code" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['employee_code']); ?>"
                                   placeholder="<?= t('placeholder_emp_code') ?>" required>
                        </div>
                        <div class="invalid-feedback"><?= t('label_employee_code') ?> <?= t('validation_required') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-info-circle"></i>
                            <?= t('label_status') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-circle-fill input-group-icon"></i>
                            <select name="status" class="form-select">
                                <option value="active" <?php echo $teacher['status'] == 'active' ? 'selected' : ''; ?>><?= t('status_active') ?></option>
                                <option value="inactive" <?php echo $teacher['status'] == 'inactive' ? 'selected' : ''; ?>><?= t('status_inactive') ?></option>
                                <option value="suspended" <?php echo $teacher['status'] == 'suspended' ? 'selected' : ''; ?>><?= t('status_suspended') ?></option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <h3 class="form-section-title fade-in-delay-2">
                    <i class="bi bi-person-vcard"></i>
                    <?= t('section_personal_details') ?>
                </h3>
                
                <div class="form-grid fade-in-delay-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-circle"></i>
                            <?= t('label_first_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-person input-group-icon"></i>
                            <input type="text" name="first_name" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['first_name']); ?>"
                                   placeholder="<?= t('placeholder_first_name') ?>" required>
                        </div>
                        <div class="invalid-feedback"><?= t('label_first_name') ?> <?= t('validation_required') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-square"></i>
                            <?= t('label_last_name') ?>
                            <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-person input-group-icon"></i>
                            <input type="text" name="last_name" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['last_name']); ?>"
                                   placeholder="<?= t('placeholder_last_name') ?>" required>
                        </div>
                        <div class="invalid-feedback"><?= t('label_last_name') ?> <?= t('validation_required') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-gender-ambiguous"></i>
                            <?= t('label_gender') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-gender-male input-group-icon"></i>
                            <select name="gender" class="form-select">
                                <option value="male" <?php echo $teacher['gender'] == 'male' ? 'selected' : ''; ?>><?= t('gender_male') ?></option>
                                <option value="female" <?php echo $teacher['gender'] == 'female' ? 'selected' : ''; ?>><?= t('gender_female') ?></option>
                                <option value="other" <?php echo $teacher['gender'] == 'other' ? 'selected' : ''; ?>><?= t('gender_other') ?></option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-calendar"></i>
                            <?= t('label_hire_date') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-calendar-date input-group-icon"></i>
                            <input type="date" name="hire_date" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['hire_date']); ?>">
                        </div>
                    </div>
                    <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-cash"></i>
                        <?= t('label_salary') ?>
                        <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <i class="bi bi-currency-dollar input-group-icon"></i>
                        <input type="number" step="0.01" min="0" name="salary" class="form-control"
                            value="<?php echo htmlspecialchars($teacher['salary']); ?>"
                            required>
                    </div>
                </div>

                </div>
                
                <h3 class="form-section-title fade-in-delay-2">
                    <i class="bi bi-telephone"></i>
                    <?= t('section_contact_info') ?>
                </h3>
                
                <div class="form-grid fade-in-delay-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-phone"></i>
                            <?= t('label_phone_number') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-telephone-fill input-group-icon"></i>
                            <input type="tel" name="phone" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['phone']); ?>"
                                   placeholder="<?= t('placeholder_phone') ?>">
                        </div>
                        <div class="invalid-feedback"><?= t('validation_phone') ?></div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-envelope"></i>
                            <?= t('label_email_address') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-envelope-fill input-group-icon"></i>
                            <input type="email" name="email" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['email']); ?>"
                                   placeholder="<?= t('placeholder_email') ?>">
                        </div>
                        <div class="invalid-feedback"><?= t('validation_email') ?></div>
                    </div>
                </div>
                <h3 class="form-section-title fade-in-delay-2">
                    <i class="bi bi-shield-lock"></i>
                    <?= t('section_security') ?>
                </h3>

                <div class="form-grid fade-in-delay-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-lock-fill"></i>
                            <?= t('label_login_pin') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-key-fill input-group-icon"></i>
                            <input type="text"
                                name="login_pin"
                                class="form-control"
                                placeholder="<?= t('placeholder_pin') ?>"
                                maxlength="6"
                                pattern="\d{6}">
                        </div>
                        <div class="invalid-feedback">
                            <?= t('validation_pin') ?>
                        </div>
                    </div>
                </div>

                <h3 class="form-section-title fade-in-delay-2">
                    <i class="bi bi-mortarboard"></i>
                    <?= t('section_professional_info') ?>
                </h3>
                
                <div class="form-grid fade-in-delay-2">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-award"></i>
                            <?= t('label_qualification') ?>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-mortarboard-fill input-group-icon"></i>
                            <input type="text" name="qualification" class="form-control"
                                   value="<?php echo htmlspecialchars($teacher['qualification']); ?>"
                                   placeholder="<?= t('placeholder_qualification') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="form-actions fade-in-delay-2">
                    <a href="teachers.php" class="btn-outline-secondary">
                        <i class="bi bi-arrow-left"></i>
                        <?= t('btn_back_teachers') ?>
                    </a>
                    <button type="submit" class="btn-primary" id="submitBtn">
                        <i class="bi bi-check-circle-fill"></i>
                        <?= t('btn_update_teacher') ?>
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Quick Stats -->
        <div class="glass-card mt-4 p-4 fade-in" style="animation-delay: 0.3s;">
            <div class="row text-center">
                <div class="col-md-3 col-6 mb-3">
                    <div class="text-light small mb-1"><?= t('stat_current_status') ?></div>
                    <?php 
                    $statusClass = 'status-' . $teacher['status'];
                    $statusIcons = [
                        'active' => 'bi-check-circle-fill text-success',
                        'inactive' => 'bi-dash-circle-fill text-secondary',
                        'suspended' => 'bi-x-circle-fill text-danger'
                    ];
                    ?>
                    <div class="<?php echo $statusClass; ?> status-badge d-inline-flex align-items-center gap-2">
                        <i class="bi <?php echo $statusIcons[$teacher['status']] ?? 'bi-question-circle'; ?>"></i>
                        <?php echo ucfirst($teacher['status']); ?>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
    <div class="text-light small mb-1"><?= t('stat_employee_since') ?></div>
    <div class="fw-bold">
        <?php
        if (!empty($teacher['hire_date']) && $teacher['hire_date'] !== '0000-00-00') {
            echo date('j-M-Y', strtotime($teacher['hire_date']));
        } else {
            echo 'N/A';
        }
        ?>
    </div>
</div>

                <div class="col-md-3 col-6 mb-3">
                    <div class="text-light small mb-1"><?= t('stat_gender') ?></div>
                    <div class="fw-bold text-capitalize">
                        <?php echo htmlspecialchars($teacher['gender']); ?>
                    </div>
                </div>
                <div class="col-md-3 col-6 mb-3">
                    <div class="text-light small mb-1"><?= t('stat_last_updated') ?></div>
                    <div class="fw-bold">
                        <?php 
                        $updated = isset($teacher['updated_at']) ? date('M d, Y', strtotime($teacher['updated_at'])) : 'Today';
                        echo $updated;
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editTeacherForm');
            const submitBtn = document.getElementById('submitBtn');
            
            // Auto-format phone number
            const phoneInput = document.querySelector('input[name="phone"]');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 0) {
                    value = '+1 (' + value.substring(0, 3) + ') ' + value.substring(3, 6) + '-' + value.substring(6, 10);
                    e.target.value = value.substring(0, 17);
                }
            });
            
            // Form validation
            form.addEventListener('submit', function(e) {
                let isValid = true;
                
                // Clear previous validation
                form.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Validate required fields
                const requiredFields = ['employee_code', 'first_name', 'last_name', 'salary'];
                requiredFields.forEach(fieldName => {
                    const field = document.querySelector(`[name="${fieldName}"]`);
                    if (!field.value.trim()) {
                        field.classList.add('is-invalid');
                        isValid = false;
                    }
                });
                
                // Validate email format if provided
                const emailField = document.querySelector('[name="email"]');
                if (emailField.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailField.value)) {
                    emailField.classList.add('is-invalid');
                    isValid = false;
                }
                // Validate login PIN if provided
                const pinField = document.querySelector('[name="login_pin"]');
                if (pinField.value && !/^\d{6}$/.test(pinField.value)) {
                    pinField.classList.add('is-invalid');
                    isValid = false;
                }

                
                if (!isValid) {
                    e.preventDefault();
                    // Focus on first invalid field
                    const firstInvalid = form.querySelector('.is-invalid');
                    if (firstInvalid) {
                        firstInvalid.focus();
                    }
                    return false;
                }
                
                // Show loading state
                submitBtn.classList.add('btn-loading');
                submitBtn.disabled = true;
                submitBtn.innerHTML = '';
                
                return true;
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input[required], select[required]');
            inputs.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim()) {
                        this.classList.remove('is-invalid');
                    }
                });
                
                input.addEventListener('blur', function() {
                    if (this.hasAttribute('required') && !this.value.trim()) {
                        this.classList.add('is-invalid');
                    }
                });
            });
            
            // Focus on first input
            setTimeout(() => {
                document.querySelector('[name="employee_code"]').focus();
            }, 300);
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl + S to save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    submitBtn.click();
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    window.location.href = 'teachers.php';
                }
            });
            
            // Add unsaved changes warning
            let formChanged = false;
            const originalValues = {};
            const formInputs = form.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                originalValues[input.name] = input.value;
                input.addEventListener('input', function() {
                    formChanged = (input.value !== originalValues[input.name]);
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = '<?= t('js_unsaved_changes') ?>';
                }
            });
            
            // Clear unsaved changes flag on submit
            form.addEventListener('submit', function() {
                formChanged = false;
            });
            
            // Cancel button also clears flag
            document.querySelector('.btn-outline-secondary').addEventListener('click', function(e) {
                if (formChanged && !confirm('<?= t('js_confirm_cancel') ?>')) {
                    e.preventDefault();
                } else {
                    formChanged = false;
                }
            });
            
            // Update status badge on status change
            const statusSelect = document.querySelector('[name="status"]');
            const statusBadge = document.querySelector('.status-badge');
            const statusIcon = statusBadge.querySelector('i');
            
            statusSelect.addEventListener('change', function() {
                const newStatus = this.value;
                const statusClasses = ['status-active', 'status-inactive', 'status-suspended'];
                
                // Remove all status classes
                statusClasses.forEach(cls => statusBadge.classList.remove(cls));
                
                // Add new status class
                statusBadge.classList.add('status-' + newStatus);
                
                // Update icon and text
                const iconMap = {
                    'active': 'bi-check-circle-fill text-success',
                    'inactive': 'bi-dash-circle-fill text-secondary',
                    'suspended': 'bi-x-circle-fill text-danger'
                };
                
                statusIcon.className = 'bi ' + (iconMap[newStatus] || 'bi-question-circle');
                statusBadge.querySelector('span').textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                statusIcon.className = 'bi ' + (iconMap[newStatus] || 'bi-question-circle');
                statusBadge.querySelector('span').textContent = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
            });

            // Initialize Tom Select
            document.querySelectorAll('select').forEach((el) => {
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    plugins: ['remove_button']
                });
            });
        });
    </script>
</body>
</html>

<style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --light-bg: #f8fafc;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body.light {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            color: #334155;
            min-height: 100vh;
        }
        
        body.dark {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            color: #e2e8f0;
            min-height: 100vh;
        }
        
        .form-container {
            max-width: 800px;
            width: 100%;
            margin: 0 auto;
            padding: 20px;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.3);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            overflow: hidden;
        }
        
        body.dark .glass-card {
            background: rgba(30, 41, 59, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-header {
            background: linear-gradient(135deg, #4361ee, #7209b7);
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
        }
        
        .form-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        .teacher-id-badge {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 6px 14px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
        
        .form-body {
            padding: 40px;
        }
        
        body.dark .form-body {
            background: var(--dark-card);
        }
        
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--primary-color);
            margin: 30px 0 20px 0;
            padding-bottom: 12px;
            border-bottom: 2px solid rgba(67, 97, 238, 0.1);
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        body.dark .form-section-title {
            color: #e2e8f0;
            border-bottom-color: rgba(255, 255, 255, 0.1);
        }
        
        .form-section-title:first-child {
            margin-top: 0;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
        }
        
        .form-group {
            margin-bottom: 0;
        }
        
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        body.dark .form-label {
            color: #cbd5e1;
        }
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control, .input-group .form-select {
            padding-left: 45px;
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: var(--transition);
            background: white;
        }
        
        body.dark .input-group .form-control,
        body.dark .input-group .form-select {
            background: #1e293b;
            border-color: #334155;
            color: #e2e8f0;
        }
        
        .input-group .form-control:focus,
        .input-group .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
        }
        
        body.dark .input-group .form-control:focus,
        body.dark .input-group .form-select:focus {
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.2);
        }
        
        .input-group-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 4;
        }
        
        body.dark .input-group-icon {
            color: #64748b;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(67, 97, 238, 0.4);
            background: linear-gradient(135deg, var(--primary-dark), #2f44b5);
        }
        
        .btn-outline-secondary {
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            color: #64748b;
            border-radius: 8px;
            padding: 15px 30px;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        body.dark .btn-outline-secondary {
            background: #1e293b;
            border-color: #334155;
            color: #cbd5e1;
        }
        
        .btn-outline-secondary:hover {
            background: #e2e8f0;
            color: #475569;
        }
        
        body.dark .btn-outline-secondary:hover {
            background: #334155;
            color: #e2e8f0;
        }
        
        .form-actions {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-top: 40px;
            padding-top: 25px;
            border-top: 1px solid rgba(0, 0, 0, 0.05);
        }
        
        body.dark .form-actions {
            border-top-color: rgba(255, 255, 255, 0.1);
        }
        
        .alert-success {
            background: linear-gradient(135deg, rgba(6, 214, 160, 0.1), rgba(6, 214, 160, 0.05));
            border: 1px solid rgba(6, 214, 160, 0.3);
            border-left: 4px solid var(--success-color);
            color: #047857;
            border-radius: 8px;
        }
        
        body.dark .alert-success {
            background: rgba(6, 214, 160, 0.1);
            border-color: rgba(6, 214, 160, 0.3);
            color: #a7f3d0;
        }
        
        .alert-danger {
            background: linear-gradient(135deg, rgba(239, 71, 111, 0.1), rgba(239, 71, 111, 0.05));
            border: 1px solid rgba(239, 71, 111, 0.3);
            border-left: 4px solid var(--danger-color);
            color: #be123c;
            border-radius: 8px;
        }
        
        body.dark .alert-danger {
            background: rgba(239, 71, 111, 0.1);
            border-color: rgba(239, 71, 111, 0.3);
            color: #fda4af;
        }
        
        .status-badge {
            padding: 6px 12px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.8rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .status-active {
            background: rgba(6, 214, 160, 0.1);
            color: var(--success-color);
            border: 1px solid rgba(6, 214, 160, 0.3);
        }
        
        .status-inactive {
            background: rgba(100, 116, 139, 0.1);
            color: #64748b;
            border: 1px solid rgba(100, 116, 139, 0.3);
        }
        
        .status-suspended {
            background: rgba(239, 71, 111, 0.1);
            color: var(--danger-color);
            border: 1px solid rgba(239, 71, 111, 0.3);
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        
        .fade-in-delay-1 {
            animation: fadeIn 0.5s ease-out 0.1s both;
        }
        
        .fade-in-delay-2 {
            animation: fadeIn 0.5s ease-out 0.2s both;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .form-body {
                padding: 25px;
            }
            
            .form-header {
                padding: 25px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn-primary, .btn-outline-secondary {
                width: 100%;
            }
        }
        
        /* Loading state */
        .btn-loading {
            position: relative;
            color: transparent !important;
        }
        
        .btn-loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            top: 50%;
            left: 50%;
            margin-top: -10px;
            margin-left: -10px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-top-color: white;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        
        /* Form validation */
        .is-invalid {
            border-color: var(--danger-color) !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef476f'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef476f' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: var(--danger-color);
        }
        
        .is-invalid ~ .invalid-feedback {
            display: block;
        }
    </style>