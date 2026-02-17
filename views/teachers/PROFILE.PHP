<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ===================== TRANSLATIONS ===================== */
$teacher_id = (int)$_SESSION['user_id'];

// Get teacher's current language
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'My Profile | Teacher Portal',
        'employee_code' => 'Employee Code',
        'joined' => 'Joined',
        'personal_information' => 'Personal Information',
        'full_name' => 'Full Name',
        'gender' => 'Gender',
        'qualification' => 'Qualification',
        'hire_date' => 'Hire Date',
        'member_since' => 'Member Since',
        'update_profile' => 'Update Profile Information',
        'contact_information' => 'Contact Information',
        'phone_number' => 'Phone Number',
        'email_address' => 'Email Address',
        'security_settings' => 'Security Settings',
        'pin_note' => 'Your PIN must be exactly 6 digits. It\'s used for quick login to the system.',
        'previous_pin' => 'Previous PIN',
        'new_pin' => 'New PIN',
        'required_only' => 'Required only if changing PIN',
        'leave_blank' => 'Leave blank to keep current PIN',
        'save_changes' => 'Save Changes',
        'profile_updated' => 'Profile updated successfully.',
        'previous_pin_required' => 'Previous PIN is required to change PIN.',
        'previous_pin_incorrect' => 'Previous PIN is incorrect.',
        'new_pin_format' => 'New PIN must be exactly 6 digits.',
        'update_failed' => 'Failed to update profile.',
        'teacher_not_found' => 'Teacher not found.',
        'active' => 'Active',
        'language_settings' => 'Language Settings',
        'interface_language' => 'Interface Language',
        'select_language' => 'Select Language',
        'save_language' => 'Save Language',
        'language_updated' => 'Language updated successfully.',
        'language_error' => 'Please select a language.',
        'choose_language' => 'Choose Your Language',
        'save_continue' => 'Save & Continue'
    ],
    2 => [ // Arabic
        'page_title' => 'ملفي الشخصي | بوابة المعلم',
        'employee_code' => 'رمز الموظف',
        'joined' => 'انضم في',
        'personal_information' => 'المعلومات الشخصية',
        'full_name' => 'الاسم الكامل',
        'gender' => 'الجنس',
        'qualification' => 'المؤهل',
        'hire_date' => 'تاريخ التعيين',
        'member_since' => 'عضو منذ',
        'update_profile' => 'تحديث معلومات الملف الشخصي',
        'contact_information' => 'معلومات الاتصال',
        'phone_number' => 'رقم الهاتف',
        'email_address' => 'البريد الإلكتروني',
        'security_settings' => 'إعدادات الأمان',
        'pin_note' => 'يجب أن يتكون الرقم السري من 6 أرقام بالضبط. يتم استخدامه لتسجيل الدخول السريع إلى النظام.',
        'previous_pin' => 'الرقم السري السابق',
        'new_pin' => 'الرقم السري الجديد',
        'required_only' => 'مطلوب فقط إذا كنت تقوم بتغيير الرقم السري',
        'leave_blank' => 'اتركه فارغًا للحفاظ على الرقم السري الحالي',
        'save_changes' => 'حفظ التغييرات',
        'profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
        'previous_pin_required' => 'مطلوب الرقم السري السابق لتغيير الرقم السري.',
        'previous_pin_incorrect' => 'الرقم السري السابق غير صحيح.',
        'new_pin_format' => 'يجب أن يتكون الرقم السري الجديد من 6 أرقام بالضبط.',
        'update_failed' => 'فشل في تحديث الملف الشخصي.',
        'teacher_not_found' => 'المعلم غير موجود.',
        'active' => 'نشط',
        'language_settings' => 'إعدادات اللغة',
        'interface_language' => 'لغة الواجهة',
        'select_language' => 'اختر اللغة',
        'save_language' => 'حفظ اللغة',
        'language_updated' => 'تم تحديث اللغة بنجاح.',
        'language_error' => 'الرجاء اختيار لغة.',
        'choose_language' => 'اختر لغتك',
        'save_continue' => 'حفظ والمتابعة'
    ],
    3 => [ // Somali
        'page_title' => 'My Profile | Albaabka Macallinka',
        'employee_code' => 'Koodhka Shaqaalaha',
        'joined' => 'Ku biiray',
        'personal_information' => 'Macluumaadka Shaqsiyeed',
        'full_name' => 'Magaca: ',
        'gender' => 'Jinsiga: ',
        'qualification' => 'Xirfada/Shahadda: ',
        'hire_date' => 'La shaqaaleysiye:  ',
        'member_since' => 'La diwaan-geliyey: ',
        'update_profile' => 'Cusbooneysii Macluumaadka Profile',
        'contact_information' => 'Macluumaadka Xiriirka',
        'phone_number' => 'Lambarka Taleefanka',
        'email_address' => 'Cinwaanka Email',
        'security_settings' => 'Sixidda Amniga',
        'pin_note' => 'PIN-kagu waa inuu noqdaa 6 lambar oo sax ah. Waxaa loo isticmaalaa si degdeg ah loo galo nidaamka.',
        'previous_pin' => 'PIN-kii Hore',
        'new_pin' => 'PIN Cusub',
        'required_only' => 'Geli PIN-kii hore',
        'leave_blank' => 'ka tag si aad ugu hayso PIN-kaga hadda',
        'save_changes' => 'Save garee',
        'profile_updated' => 'Profile-ka si guul leh loo cusbooneysiiyay.',
        'previous_pin_required' => 'PIN-kii hore waa loo baahan yahay si loo beddelo PIN.',
        'previous_pin_incorrect' => 'PIN-kii hore waa khalad.',
        'new_pin_format' => 'PIN cusub waa inuu noqdaa 6 lambar oo sax ah.',
        'update_failed' => 'Khalad ayaa dhacay markii la cusbooneysiinayay profile.',
        'teacher_not_found' => 'Macallin lama helin.',
        'active' => 'Firfircoon',
        'language_settings' => 'Dejinta Luqadda',
        'interface_language' => 'Luqadda Interface',
        'select_language' => 'Dooro Luqadda',
        'save_language' => 'Keydi Luqadda',
        'language_updated' => 'Luqaddii si guul leh loo cusbooneysiiyay.',
        'language_error' => 'Fadlan dooro luqad.',
        'choose_language' => 'Dooro Luqaddaada',
        'save_continue' => 'Save garee oo Sii Wadi'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

function e($v) {
    return htmlspecialchars($v ?? '', ENT_QUOTES, 'UTF-8');
}

$message = '';
$error = '';
$lang_message = '';
$lang_error = '';

// Fetch teacher data
$stmt = $conn->prepare("
    SELECT 
        employee_code,
        first_name,
        last_name,
        gender,
        phone,
        email,
        qualification,
        hire_date,
        status,
        login_pin,
        created_at,
        language_id
    FROM teachers
    WHERE id = ?
    LIMIT 1
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$teacher = $stmt->get_result()->fetch_assoc();

if (!$teacher) {
    die(t('teacher_not_found'));
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $phone = trim($_POST['phone'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $old_pin = trim($_POST['old_pin'] ?? '');
        $new_pin = trim($_POST['new_pin'] ?? '');

        // PIN logic
        if ($new_pin !== '') {
            if ($old_pin === '') {
                $error = t('previous_pin_required');
            } elseif ($old_pin !== $teacher['login_pin']) {
                $error = t('previous_pin_incorrect');
            } elseif (!preg_match('/^[0-9]{6}$/', $new_pin)) {
                $error = t('new_pin_format');
            }
        }

        if (!$error) {
            if ($new_pin !== '') {
                $stmt = $conn->prepare("
                    UPDATE teachers
                    SET phone = ?, email = ?, login_pin = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("sssi", $phone, $email, $new_pin, $teacher_id);
            } else {
                $stmt = $conn->prepare("
                    UPDATE teachers
                    SET phone = ?, email = ?
                    WHERE id = ?
                ");
                $stmt->bind_param("ssi", $phone, $email, $teacher_id);
            }

            if ($stmt->execute()) {
                $message = t('profile_updated');
                // Refresh teacher data
                $teacher['phone'] = $phone;
                $teacher['email'] = $email;
                if ($new_pin !== '') {
                    $teacher['login_pin'] = $new_pin;
                }
            } else {
                $error = t('update_failed');
            }
        }
    }
    
    // Handle language update
    if (isset($_POST['update_language'])) {
        $language_id = (int)($_POST['language_id'] ?? 0);

        if ($language_id > 0) {
            $stmt = $conn->prepare("UPDATE teachers SET language_id = ? WHERE id = ?");
            $stmt->bind_param("ii", $language_id, $teacher_id);
            
            if ($stmt->execute()) {
                $lang_message = t('language_updated');
                // Update current language and refresh page
                $currentLangId = $language_id;
                $teacher['language_id'] = $language_id;
            }
        } else {
            $lang_error = t('language_error');
        }
    }
}

// Fetch available languages
$languages = [];
$langs_res = $conn->query("SELECT id, name FROM languages ORDER BY name");
while ($lang = $langs_res->fetch_assoc()) {
    $languages[] = $lang;
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
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #1abc9c;
            --warning-color: #f39c12;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            --avatar-size: 120px;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            color: #333;
        }
        
        .container-fluid {
            max-width: 1200px;
        }
        
        .profile-header {
            background: linear-gradient(135deg, var(--primary-color), #1a2530);
            color: white;
            padding: 2.5rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 15px 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            transform: translate(30%, -30%);
        }
        
        .page-title {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }
        
        .page-subtitle {
            color: rgba(255, 255, 255, 0.85);
            font-size: 1rem;
        }
        
        .profile-avatar {
            width: var(--avatar-size);
            height: var(--avatar-size);
            background: linear-gradient(135deg, var(--secondary-color), var(--accent-color));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 3rem;
            color: white;
            border: 5px solid white;
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
            margin-right: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background: linear-gradient(to right, var(--primary-color), #3a5068);
            color: white;
            font-weight: 600;
            padding: 1.25rem 1.5rem;
            border-bottom: none;
        }
        
        .card-header i {
            width: 22px;
            margin-right: 10px;
        }
        
        .card-body {
            padding: 1.75rem;
        }
        
        .info-item {
            padding: 0.85rem 0;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: var(--primary-color);
            min-width: 150px;
            display: flex;
            align-items: center;
        }
        
        .info-label i {
            width: 20px;
            margin-right: 8px;
            color: var(--secondary-color);
        }
        
        .info-value {
            color: #555;
            flex: 1;
        }
        
        .badge-status {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .badge-active {
            background-color: #d4edda;
            color: #155724;
        }
        
        .form-label {
            font-weight: 600;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .form-control {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            transition: all 0.3s;
        }
        
        .form-control:focus {
            border-color: var(--secondary-color);
            box-shadow: 0 0 0 0.25rem rgba(52, 152, 219, 0.15);
        }
        
        .input-group-text {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            color: var(--primary-color);
        }
        
        .btn-primary {
            background: linear-gradient(to right, var(--secondary-color), #2980b9);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(52, 152, 219, 0.3);
        }
        
        .btn-secondary {
            background-color: #6c757d;
            border: none;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            transition: all 0.3s ease;
        }
        
        .btn-secondary:hover {
            background-color: #5a6268;
            transform: translateY(-1px);
        }
        
        .alert {
            border-radius: 10px;
            border: none;
            padding: 1rem 1.25rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border-left: 4px solid #28a745;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border-left: 4px solid #dc3545;
        }
        
        .section-title {
            color: var(--primary-color);
            font-weight: 600;
            margin-bottom: 1.5rem;
            padding-bottom: 0.75rem;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 10px;
            color: var(--secondary-color);
        }
        
        .password-toggle {
            cursor: pointer;
            color: var(--secondary-color);
        }
        
        .stats-card {
            background: white;
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s ease;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 0.5rem;
        }
        
        .stats-label {
            color: #666;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                padding: 1.5rem 0;
                text-align: center;
            }
            
            .profile-avatar {
                margin: 0 auto 1.5rem;
            }
            
            .info-label {
                min-width: 120px;
            }
            
            .info-item {
                flex-direction: column;
                align-items: flex-start;
            }
        }
        
        .pin-note {
            font-size: 0.85rem;
            color: #666;
            margin-top: 0.5rem;
            padding: 0.75rem;
            background-color: #f8f9fa;
            border-radius: 6px;
            border-left: 3px solid var(--warning-color);
        }
        
        .pin-note i {
            color: var(--warning-color);
            margin-right: 5px;
        }
        
        .language-selector {
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 2px solid #f0f0f0;
        }
        
        [dir="rtl"] .profile-header::before {
            right: auto;
            left: 0;
            transform: translate(-30%, -30%);
        }
        
        [dir="rtl"] .profile-avatar {
            margin-right: 0;
            margin-left: 2rem;
        }
        
        [dir="rtl"] .card-header i,
        [dir="rtl"] .info-label i,
        [dir="rtl"] .section-title i,
        [dir="rtl"] .pin-note i {
            margin-right: 0;
            margin-left: 10px;
        }
        
        [dir="rtl"] .input-group-text {
            order: 2;
        }
        
        [dir="rtl"] .form-control {
            order: 1;
        }
    </style>
</head>
<body>
    <!-- Profile Header -->
    <div class="profile-header">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row align-items-center">
                <div class="profile-avatar mb-3 mb-md-0">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="text-center text-md-start">
                    <h1 class="page-title"><?= e($teacher['first_name'] . ' ' . $teacher['last_name']) ?></h1>
                    <p class="page-subtitle mb-2"><?= t('employee_code') ?>: <?= e($teacher['employee_code']) ?></p>
                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2 mt-2">
                        <span class="badge badge-status badge-active"><?= t('active') ?></span>
                        <span class="badge bg-light text-dark">
                            <i class="fas fa-calendar-alt me-1"></i> <?= t('joined') ?> <?= date('F Y', strtotime($teacher['hire_date'])) ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid">
        <!-- Messages -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?= e($message) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
        
        <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i>
                <?= e($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="row">
            <!-- Left Column: Profile Details -->
            <div class="col-lg-5">
                <!-- Account Information -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-user-circle"></i><?= t('personal_information') ?>
                    </div>
                    <div class="card-body">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-id-card"></i><?= t('employee_code') ?>
                            </div>
                            <div class="info-value">
                                <strong><?= e($teacher['employee_code']) ?></strong>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-user"></i><?= t('full_name') ?>
                            </div>
                            <div class="info-value">
                                <?= e($teacher['first_name'] . ' ' . $teacher['last_name']) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-venus-mars"></i><?= t('gender') ?>
                            </div>
                            <div class="info-value">
                                <?= ucfirst(e($teacher['gender'])) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-graduation-cap"></i><?= t('qualification') ?>
                            </div>
                            <div class="info-value">
                                <?= e($teacher['qualification']) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-calendar-check"></i><?= t('hire_date') ?>
                            </div>
                            <div class="info-value">
                                <?= date('F j, Y', strtotime(e($teacher['hire_date']))) ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fas fa-clock"></i><?= t('member_since') ?>
                            </div>
                            <div class="info-value">
                                <?= date('F j, Y', strtotime(e($teacher['created_at']))) ?>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Language Settings -->
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-language"></i><?= t('language_settings') ?>
                    </div>
                    <div class="card-body">
                        <?php if ($lang_message): ?>
                            <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
                                <i class="fas fa-check-circle me-2"></i>
                                <?= e($lang_message) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($lang_error): ?>
                            <div class="alert alert-danger alert-dismissible fade show mb-3" role="alert">
                                <i class="fas fa-exclamation-triangle me-2"></i>
                                <?= e($lang_error) ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        <?php endif; ?>
                        
                        <form method="post">
                            <div class="mb-3">
                                <label class="form-label">
                                    <i class="fas fa-globe me-1"></i><?= t('interface_language') ?>
                                </label>
                                <select name="language_id" class="form-select" required>
                                    <option value="">-- <?= t('select_language') ?> --</option>
                                    <?php foreach ($languages as $lang): ?>
                                        <option value="<?= e($lang['id']) ?>" 
                                                <?= $teacher['language_id'] == $lang['id'] ? 'selected' : '' ?>>
                                            <?= e($lang['name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text"><?= t('choose_language') ?></div>
                            </div>
                            
                            <button type="submit" name="update_language" class="btn btn-primary w-100">
                                <i class="fas fa-save me-2"></i><?= t('save_language') ?>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Right Column: Edit Form -->
            <div class="col-lg-7">
                <form method="post" id="profileForm">
                    <input type="hidden" name="update_profile" value="1">
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-edit"></i><?= t('update_profile') ?>
                        </div>
                        
                        <div class="card-body">
                            <!-- Contact Information Section -->
                            <h5 class="section-title">
                                <i class="fas fa-address-book"></i><?= t('contact_information') ?>
                            </h5>
                            
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-phone me-1"></i><?= t('phone_number') ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-mobile-alt"></i>
                                        </span>
                                        <input type="text" 
                                               name="phone"
                                               value="<?= e($teacher['phone']) ?>"
                                               class="form-control"
                                               placeholder="<?= t('phone_number') ?>">
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-envelope me-1"></i><?= t('email_address') ?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fas fa-at"></i>
                                        </span>
                                        <input type="email" 
                                               name="email"
                                               value="<?= e($teacher['email']) ?>"
                                               class="form-control"
                                               placeholder="<?= t('email_address') ?>">
                                    </div>
                                </div>
                            </div>

                            <!-- Security Section -->
                            <h5 class="section-title">
                                <i class="fas fa-shield-alt"></i><?= t('security_settings') ?>
                            </h5>
                            
                            <div class="pin-note">
                                <i class="fas fa-info-circle"></i>
                                <?= t('pin_note') ?>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-lock me-1"></i><?= t('previous_pin') ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="old_pin"
                                               id="old_pin"
                                               maxlength="6"
                                               pattern="[0-9]{6}"
                                               inputmode="numeric"
                                               class="form-control"
                                               placeholder="<?= t('previous_pin') ?>">
                                        <span class="input-group-text password-toggle" id="toggleOldPin">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted"><?= t('required_only') ?></small>
                                </div>
                                
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        <i class="fas fa-key me-1"></i><?= t('new_pin') ?>
                                    </label>
                                    <div class="input-group">
                                        <input type="password"
                                               name="new_pin"
                                               id="new_pin"
                                               maxlength="6"
                                               pattern="[0-9]{6}"
                                               inputmode="numeric"
                                               class="form-control"
                                               placeholder="<?= t('new_pin') ?>">
                                        <span class="input-group-text password-toggle" id="toggleNewPin">
                                            <i class="fas fa-eye"></i>
                                        </span>
                                    </div>
                                    <small class="text-muted"><?= t('leave_blank') ?></small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="card-footer d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i><?= t('save_changes') ?>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Password visibility toggle
            const toggleOldPin = document.getElementById('toggleOldPin');
            const toggleNewPin = document.getElementById('toggleNewPin');
            const oldPinInput = document.getElementById('old_pin');
            const newPinInput = document.getElementById('new_pin');
            
            toggleOldPin.addEventListener('click', function() {
                const type = oldPinInput.getAttribute('type') === 'password' ? 'text' : 'password';
                oldPinInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            
            toggleNewPin.addEventListener('click', function() {
                const type = newPinInput.getAttribute('type') === 'password' ? 'text' : 'password';
                newPinInput.setAttribute('type', type);
                this.querySelector('i').classList.toggle('fa-eye');
                this.querySelector('i').classList.toggle('fa-eye-slash');
            });
            
            // Form validation
            document.getElementById('profileForm').addEventListener('submit', function(e) {
                const newPin = document.getElementById('new_pin').value.trim();
                const oldPin = document.getElementById('old_pin').value.trim();
                
                if (newPin !== '') {
                    if (oldPin === '') {
                        e.preventDefault();
                        alert('<?= t('previous_pin_required') ?>');
                        document.getElementById('old_pin').focus();
                        return false;
                    }
                    
                    if (!/^\d{6}$/.test(newPin)) {
                        e.preventDefault();
                        alert('<?= t('new_pin_format') ?>');
                        document.getElementById('new_pin').focus();
                        return false;
                    }
                }
                
                // Show loading state
                const submitBtn = e.submitter;
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i><?= t('saving') ?>';
                submitBtn.disabled = true;
                
                return true;
            });
            
            // Auto-format phone number
            const phoneInput = document.querySelector('input[name="phone"]');
            phoneInput.addEventListener('input', function(e) {
                let value = e.target.value.replace(/\D/g, '');
                if (value.length > 10) value = value.substring(0, 10);
                e.target.value = value;
            });
            
            // PIN input formatting
            [oldPinInput, newPinInput].forEach(input => {
                input.addEventListener('input', function(e) {
                    let value = e.target.value.replace(/\D/g, '');
                    if (value.length > 6) value = value.substring(0, 6);
                    e.target.value = value;
                });
            });
        });
    </script>
</body>
</html>