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
        // Page Title & Navigation
        'page_title' => 'User Management | Admin Panel',
        'nav_add_user' => 'Add User',
        'nav_manage_users' => 'Manage Users',
        'nav_parents' => 'Parents',
        'nav_history' => 'History',
        
        // Header & Stats
        'header_title' => 'Manage Users',
        'total_users' => 'Total users',
        
        // Search & Filters
        'search_placeholder' => 'Search users...',
        'filter_all_status' => 'All Status',
        'filter_active' => 'Active',
        'filter_inactive' => 'Inactive',
        'filter_suspended' => 'Suspended',
        'filter_button' => 'Filter',
        
        // Table Headers
        'table_user' => 'User',
        'table_contact' => 'Contact',
        'table_role' => 'Role',
        'table_status' => 'Status',
        'table_created' => 'Created',
        'table_actions' => 'Actions',
        
        // Table Content
        'no_users_found' => 'No users found',
        'status_active' => 'Active',
        'status_inactive' => 'Inactive',
        'status_suspended' => 'Suspended',
        
        // Action Buttons
        'action_edit' => 'Edit',
        'action_delete' => 'Delete',
        'action_settings' => 'Settings',
        
        // Modal Titles
        'modal_create_user' => 'Create New User',
        'modal_edit_user' => 'Edit User',
        
        // Form Labels
        'form_username' => 'Username *',
        'form_email' => 'Email *',
        'form_password' => 'Password *',
        'form_confirm_password' => 'Confirm Password *',
        'form_first_name' => 'First Name',
        'form_last_name' => 'Last Name',
        'form_phone' => 'Phone',
        'form_role' => 'Role *',
        'form_status' => 'Status *',
        'form_select_role' => 'Select Role',
        
        // Form Helpers
        'password_hint' => 'Minimum 8 characters',
        'email_exists_error' => 'Email already exists',
        'password_mismatch' => 'Passwords do not match',
        
        // Modal Buttons
        'button_cancel' => 'Cancel',
        'button_create_user' => 'Create User',
        'button_update_user' => 'Update User',
        
        // Validation Messages
        'validation_username' => 'Username must start with letters, can include numbers, 3-50 characters, no spaces or underscores',
        'validation_phone' => 'Valid phone number required',
        
        // Confirmation Messages
        'confirm_delete' => 'Are you sure you want to delete "{username}"? This action cannot be undone.',
        
        // Success/Error Messages
        'success_user_created' => 'User created successfully.',
        'success_user_updated' => 'User updated successfully.',
        'success_user_deleted' => 'User deleted successfully.',
        'success_profile_updated' => 'Profile updated successfully.',
        'error_user_exists' => 'Email already exists.',
        'error_invalid_email' => 'Invalid email format.',
        'error_invalid_phone' => 'Invalid phone number format.',
        'error_password_format' => 'Password must be exactly 8 digits (numbers only).',
        'error_csrf' => 'Invalid CSRF token. Please try again.',
        'error_permission' => 'You do not have permission to perform this action.',
        'error_delete_self' => 'You cannot delete your own account.',
        'error_delete_admin' => 'Admins cannot delete other admins.',
        
        // Login & Access
        'login_required' => 'Login required',
        'user_suspended' => 'Your account has been suspended.',
        'user_not_found' => 'User not found.',
    ],
    2 => [ // Arabic
        // Page Title & Navigation
        'page_title' => 'إدارة المستخدمين | لوحة التحكم',
        'nav_add_user' => 'إضافة مستخدم',
        'nav_manage_users' => 'إدارة المستخدمين',
        'nav_parents' => 'ألوالدين',
        'nav_history' => 'السجل',
        
        // Header & Stats
        'header_title' => 'إدارة المستخدمين',
        'total_users' => 'إجمالي المستخدمين',
        
        // Search & Filters
        'search_placeholder' => 'بحث في المستخدمين...',
        'filter_all_status' => 'جميع الحالات',
        'filter_active' => 'نشط',
        'filter_inactive' => 'غير نشط',
        'filter_suspended' => 'موقوف',
        'filter_button' => 'تصفية',
        
        // Table Headers
        'table_user' => 'المستخدم',
        'table_contact' => 'الاتصال',
        'table_role' => 'الدور',
        'table_status' => 'الحالة',
        'table_created' => 'تاريخ الإنشاء',
        'table_actions' => 'الإجراءات',
        
        // Table Content
        'no_users_found' => 'لم يتم العثور على مستخدمين',
        'status_active' => 'نشط',
        'status_inactive' => 'غير نشط',
        'status_suspended' => 'موقوف',
        
        // Action Buttons
        'action_edit' => 'تعديل',
        'action_delete' => 'حذف',
        'action_settings' => 'الإعدادات',
        
        // Modal Titles
        'modal_create_user' => 'إنشاء مستخدم جديد',
        'modal_edit_user' => 'تعديل المستخدم',
        
        // Form Labels
        'form_username' => 'اسم المستخدم *',
        'form_email' => 'البريد الإلكتروني *',
        'form_password' => 'كلمة المرور *',
        'form_confirm_password' => 'تأكيد كلمة المرور *',
        'form_first_name' => 'الاسم الأول',
        'form_last_name' => 'اسم العائلة',
        'form_phone' => 'الهاتف',
        'form_role' => 'الدور *',
        'form_status' => 'الحالة *',
        'form_select_role' => 'اختر الدور',
        
        // Form Helpers
        'password_hint' => '8 أحرف على الأقل',
        'email_exists_error' => 'البريد الإلكتروني موجود بالفعل',
        'password_mismatch' => 'كلمات المرور غير متطابقة',
        
        // Modal Buttons
        'button_cancel' => 'إلغاء',
        'button_create_user' => 'إنشاء مستخدم',
        'button_update_user' => 'تحديث المستخدم',
        
        // Validation Messages
        'validation_username' => 'يجب أن يبدأ اسم المستخدم بحروف، يمكن أن يتضمن أرقاماً، من 3 إلى 50 حرفاً، بدون مسافات أو شرطات سفلية',
        'validation_phone' => 'رقم هاتف صحيح مطلوب',
        
        // Confirmation Messages
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف "{username}"؟ لا يمكن التراجع عن هذا الإجراء.',
        
        // Success/Error Messages
        'success_user_created' => 'تم إنشاء المستخدم بنجاح.',
        'success_user_updated' => 'تم تحديث المستخدم بنجاح.',
        'success_user_deleted' => 'تم حذف المستخدم بنجاح.',
        'success_profile_updated' => 'تم تحديث الملف الشخصي بنجاح.',
        'error_user_exists' => 'البريد الإلكتروني موجود بالفعل.',
        'error_invalid_email' => 'تنسيق بريد إلكتروني غير صالح.',
        'error_invalid_phone' => 'تنسيق رقم هاتف غير صالح.',
        'error_password_format' => 'يجب أن تتكون كلمة المرور من 8 أرقام فقط.',
        'error_csrf' => 'رمز CSRF غير صالح. يرجى المحاولة مرة أخرى.',
        'error_permission' => 'ليس لديك إذن لأداء هذا الإجراء.',
        'error_delete_self' => 'لا يمكنك حذف حسابك الخاص.',
        'error_delete_admin' => 'لا يمكن للمسؤولين حذف مسؤولين آخرين.',
        
        // Login & Access
        'login_required' => 'مطلوب تسجيل الدخول',
        'user_suspended' => 'تم تعليق حسابك.',
        'user_not_found' => 'المستخدم غير موجود.',
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

// Check authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: login.php');
    exit;
}

function is_member() {
    return ($_SESSION['role'] ?? '') === 'member';
}

function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}

// Fetch logged-in user info
$stmt = $conn->prepare("
    SELECT u.id, u.username, u.role_id, r.name AS role_name, u.email
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.id = ?
");
$stmt->bind_param("i", $logged_in_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$res || $res->num_rows === 0) {
    die(t('user_not_found'));
}

$me = $res->fetch_assoc();
$my_role_id = (int) $me['role_id'];
$my_role_name = $me['role_name'];

// Check if user is active (prevent suspended users from accessing)
$status_stmt = $conn->prepare("SELECT status FROM users WHERE id = ?");
$status_stmt->bind_param("i", $logged_in_id);
$status_stmt->execute();
$status_res = $status_stmt->get_result();
$status_row = $status_res->fetch_assoc();
if ($status_row['status'] !== 'active') {
    session_destroy();
    header('Location: login.php?error=suspended');
    exit;
}

// Fetch roles for dropdown
$roles = [];
$res = $conn->query("SELECT id, name FROM roles ORDER BY id ASC");
while ($row = $res->fetch_assoc()) $roles[] = $row;

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['csrf_token'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        flash_message('error', t('error_csrf'));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    $action = $_POST['action'] ?? '';
    
    // Check permissions based on action
    if ($my_role_name == 'member' && $action != 'update_self') {
        flash_message('error', t('error_permission'));
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }

    switch ($action) {
        case 'create':
            handle_create_user($conn, $my_role_name, $my_role_id);
            break;
        case 'delete':
            handle_delete_user($conn, $my_role_name, $my_role_id, $logged_in_id);
            break;
        case 'update_self':
            handle_update_self($conn, $logged_in_id);
            break;
    }
}

$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");

if($theme_res && $theme_row = $theme_res->fetch_assoc()){
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

// Build query for fetching users
$sql = "SELECT 
            u.id, u.username, u.email, u.first_name, u.last_name, 
            u.phone, u.role_id, r.name AS role_name, 
            u.status, u.created_at
        FROM users u
        JOIN roles r ON r.id = u.role_id
        WHERE 1=1";

$params = [];
$types = '';

// Apply role-based visibility (SUPER ADMIN = ID 1)
if ($my_role_name === 'admin') {
    // If NOT super admin → hide user ID 1
    if ($logged_in_id != 1) {
        $sql .= " AND u.id != 1";
    }
} elseif (in_array($my_role_name, ['member','Finance'])) {
    // Members & Finance see only themselves
    $sql .= " AND u.id = ?";
    $params[] = $logged_in_id;
    $types .= 'i';
}

$sql .= " ORDER BY u.role_id ASC, u.created_at DESC";

// Execute query to get all users (DataTables will handle pagination)
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Prepare Failed: " . $conn->error);
}

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$users = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
$total_users = count($users);

// Handle functions
function handle_create_user($conn, $my_role_name, $my_role_id) {
    // Validate required inputs
    $required = ['username', 'email', 'password', 'role_id', 'status'];
    foreach ($required as $field) {
        if (empty($_POST[$field])) {
            flash_message('error', "All required fields must be filled.");
            return;
        }
    }

    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_id = (int) $_POST['role_id'];
    $status = $_POST['status'];
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Admin cannot create another admin
    if ($my_role_name == 'admin' && $role_id == $my_role_id) {
        flash_message('error', 'Admins cannot create other admins.');
        return;
    }

    // Validate email format
    if (!validate_email($email)) {
        flash_message('error', t('error_invalid_email'));
        return;
    }

    // Check for duplicate email
    if (is_email_taken($conn, $email)) {
        flash_message('error', t('error_user_exists'));
        return;
    }

    // Validate phone if provided
    if (!empty($phone) && !validate_phone($phone)) {
        flash_message('error', t('error_invalid_phone'));
        return;
    }

    // Validate password: exactly 8 digits (numbers only)
    if (!preg_match('/^\d{8}$/', $password)) {
        flash_message('error', t('error_password_format'));
        return;
    }

    // Hash the password for security
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert user
    $stmt = $conn->prepare("INSERT INTO users 
        (role_id, username, email, password, first_name, last_name, phone, status) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $role_id, $username, $email, $hashed_password, 
                      $first_name, $last_name, $phone, $status);

    if ($stmt->execute()) {
        $new_user_id = $stmt->insert_id;
        log_audit($conn, $_SESSION['user_id'], 'USER_CREATE', 
                  "Created user: $username (ID: $new_user_id)");
        flash_message('success', t('success_user_created'));
    } else {
        flash_message('error', 'Failed to create user.');
    }
}

function handle_delete_user($conn, $my_role_name, $my_role_id, $logged_in_id) {
    $user_id = (int) ($_POST['user_id'] ?? 0);
    
    if ($user_id <= 0) {
        flash_message('error', 'Invalid user ID.');
        return;
    }

    // Prevent deleting self
    if ($user_id == $logged_in_id) {
        flash_message('error', t('error_delete_self'));
        return;
    }

    // For admins, prevent deleting other admins
    if ($my_role_name == 'admin') {
        $role_stmt = $conn->prepare("SELECT role_id FROM users WHERE id = ?");
        $role_stmt->bind_param("i", $user_id);
        $role_stmt->execute();
        $res = $role_stmt->get_result();
        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            if ($row['role_id'] == $my_role_id) {
                flash_message('error', t('error_delete_admin'));
                return;
            }
        }
    }

    // Get user info for audit log
    $audit_stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
    $audit_stmt->bind_param("i", $user_id);
    $audit_stmt->execute();
    $user = $audit_stmt->get_result()->fetch_assoc();

    // Delete user
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);

    if ($stmt->execute()) {
        log_audit($conn, $_SESSION['user_id'], 'USER_DELETE', 
                  "Deleted user: {$user['username']} (ID: $user_id)");
        flash_message('success', t('success_user_deleted'));
    } else {
        flash_message('error', 'Failed to delete user.');
    }
}

function handle_update_self($conn, $logged_in_id) {
    // Members can only update their own info (not role)
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');

    // Validate phone if provided
    if (!empty($phone) && !validate_phone($phone)) {
        flash_message('error', t('error_invalid_phone'));
        return;
    }

    $stmt = $conn->prepare("UPDATE users SET first_name = ?, last_name = ?, phone = ? WHERE id = ?");
    $stmt->bind_param("sssi", $first_name, $last_name, $phone, $logged_in_id);

    if ($stmt->execute()) {
        log_audit($conn, $logged_in_id, 'USER_UPDATE_SELF', "Updated personal information");
        flash_message('success', t('success_profile_updated'));
    } else {
        flash_message('error', 'Failed to update profile.');
    }
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">
    
    <style>
        /* Modern Dark/Light Theme */
        :root {
            /* Light Theme */
            --bg-color: #ffffff;
            --surface-color: #f8f9fa;
            --text-color: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --accent-color: #0d6efd;
            --accent-hover: #0b5ed7;
            --success-color: #198754;
            --danger-color: #dc3545;
            --warning-color: #ffc107;
            --table-header-bg: #f1f3f4;
            --table-hover-bg: rgba(0, 0, 0, 0.02);
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        /* Dark Theme */
        body.dark {
            --bg-color: #121212;
            --surface-color: #1e1e1e;
            --text-color: #e0e0e0;
            --text-secondary: #9e9e9e;
            --border-color: #2d2d2d;
            --accent-color: #3d8bfd;
            --accent-hover: #5da3ff;
            --success-color: #4caf50;
            --danger-color: #f44336;
            --warning-color: #ff9800;
            --table-header-bg: #2d2d2d;
            --table-hover-bg: rgba(255, 255, 255, 0.05);
            --shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.3);
            --shadow-md: 0 0.5rem 1rem rgba(0, 0, 0, 0.4);
        }

        /* Base Styles */
        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            transition: background-color 0.3s ease, color 0.3s ease;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Typography */
        h1, h2, h3, h4, h5, h6 {
            color: var(--text-color);
        }

        .text-muted {
            color: var(--text-color) !important;
        }

        /* Cards */
        .card {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: var(--shadow-md);
        }
        body.dark th{
            color: white;
        }
        body.dark td{
            color: black;
        }

        /* Tables */
        .table {
            color: var(--text-color);
            margin-bottom: 0;
        }

        .table th {
            background-color: var(--table-header-bg);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.85rem;
            letter-spacing: 0.5px;
            padding: 1rem;
        }

        .table td {
            border-color: var(--border-color);
            padding: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background-color: var(--table-hover-bg);
            transform: translateY(-1px);
            transition: all 0.2s ease;
        }

        /* Badges */
        .badge {
            font-weight: 500;
            letter-spacing: 0.3px;
            padding: 0.35em 0.65em;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 50rem;
            font-size: 0.75rem;
            font-weight: 500;
        }

        .status-active {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--success-color);
        }

        .status-inactive {
            background-color: rgba(158, 158, 158, 0.15);
            color: var(--text-secondary);
        }

        .status-suspended {
            background-color: rgba(244, 67, 54, 0.15);
            color: var(--danger-color);
        }

        /* Buttons */
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .btn-primary {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            border-color: var(--accent-hover);
            transform: translateY(-1px);
        }

        .btn-outline-primary {
            color: var(--accent-color);
            border-color: var(--accent-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-action {
            width: 36px;
            height: 36px;
            padding: 0;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
        }

        /* Forms */
        .form-control, .form-select {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-color);
            border-radius: var(--radius-sm);
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--surface-color);
            border-color: var(--accent-color);
            color: var(--text-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }

        .input-group-text {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-secondary);
        }

        /* Alerts */
        .alert {
            border: none;
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-sm);
        }

        .alert-success {
            background-color: rgba(76, 175, 80, 0.15);
            color: var(--success-color);
        }

        .alert-danger {
            background-color: rgba(244, 67, 54, 0.15);
            color: var(--danger-color);
        }

        /* Avatar */
        .avatar-placeholder {
            width: 40px;
            height: 40px;
            font-weight: 600;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
        }

        /* Pagination */
        .page-link {
            background-color: var(--surface-color);
            border-color: var(--border-color);
            color: var(--text-color);
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background-color: var(--table-hover-bg);
            border-color: var(--accent-color);
            color: var(--accent-color);
        }

        .page-item.active .page-link {
            background-color: var(--accent-color);
            border-color: var(--accent-color);
        }

        /* Modal */
        .modal-content {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-lg);
        }

        .modal-header {
            border-bottom-color: var(--border-color);
        }

        .modal-footer {
            border-top-color: var(--border-color);
        }

        /* Dropdown */
        .dropdown-menu {
            background-color: var(--surface-color);
            border: 1px solid var(--border-color);
            box-shadow: var(--shadow-md);
        }

        .dropdown-item {
            color: var(--text-color);
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background-color: var(--table-hover-bg);
        }
        body.dark .dropdown-menu-end:hover li a{
            color: white;
        }

        /* Loading */
        .loader {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid var(--text-secondary);
            border-radius: 50%;
            border-top-color: var(--accent-color);
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Responsive */
        @media (max-width: 768px) {
            .table-responsive {
                border-radius: var(--radius-md);
                border: 1px solid var(--border-color);
            }
            
            .btn-action {
                width: 32px;
                height: 32px;
                font-size: 0.875rem;
            }
            
            .avatar-placeholder {
                width: 32px;
                height: 32px;
                font-size: 0.875rem;
            }
        }

        /* Empty State */
        .empty-state {
            padding: 3rem 1rem;
            text-align: center;
        }

        .empty-state i {
            font-size: 3rem;
            color: var(--text-secondary);
            margin-bottom: 1rem;
        }

        /* Status Indicator */
        .status-indicator {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            margin-right: 0.5rem;
        }

        .status-active-indicator {
            background-color: var(--success-color);
            box-shadow: 0 0 0 2px rgba(76, 175, 80, 0.2);
        }

        .status-inactive-indicator {
            background-color: var(--text-secondary);
            box-shadow: 0 0 0 2px rgba(158, 158, 158, 0.2);
        }

        .status-suspended-indicator {
            background-color: var(--danger-color);
            box-shadow: 0 0 0 2px rgba(244, 67, 54, 0.2);
        }
    </style>
    
    <script>
        (function () {
            const theme = localStorage.getItem('theme') || 'dark';
            
            if (theme === 'light') {
                document.documentElement.classList.add('light');
                document.body.classList.add('light');
            } else {
                document.documentElement.classList.remove('light');
                document.body.classList.remove('light');
            }

            // Live sync with dashboard toggle
            window.addEventListener('message', function(e){
                if(e.data === 'light'){
                    document.body.classList.add('light');
                } else if(e.data === 'dark'){
                    document.body.classList.remove('light');
                }
            });
        })();
    </script>
</head>
<body class="<?php echo $theme === 1 ? 'light' : 'dark'; ?>">

<div class="container-fluid p-0">
    <div class="main-content">
        <!-- Top Bar -->
        <nav class="navbar navbar-light px-4 py-3 border-bottom">
            <div class="container-fluid">
                <div class="d-flex align-items-center">
                    <?php if(!is_finance() && !is_member()): ?>
                        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">
                            <i class="fas fa-user-plus me-2"></i> <?= t('nav_add_user') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </nav>
        
        <div class="p-4">
            <!-- Flash Messages -->
            <?php $flash_messages = get_flash_messages(); ?>
            <?php if (!empty($flash_messages)): ?>
                <?php foreach ($flash_messages as $msg): ?>
                    <div class="alert alert-<?php echo $msg['type'] == 'error' ? 'danger' : 'success'; ?> 
                                alert-dismissible fade show" role="alert">
                        <?php echo htmlspecialchars($msg['message']); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
            
            <!-- Header with Stats -->
            <div class="row mb-4 align-items-center">
                <div class="col-md-8">
                    <h1 class="h4 mb-2"><?= t('header_title') ?></h1>
                    <p class="text-primary mb-0"><?= t('total_users') ?>: <?php echo $total_users; ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <?php if ($my_role_name == 'admin'): ?>
                        <div class="dropdown">
                            <button class="btn btn-primary btn-action" id="settingsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-cog"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
                                <li>
                                    <a class="dropdown-item" href="../parents/parents.php">
                                        <i class="fas fa-people-group me-2"></i> <?= t('nav_parents') ?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Filters and Search removed (handled by DataTables) -->
            
            <!-- Users Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th><?= t('table_user') ?></th>
                                    <th><?= t('table_contact') ?></th>
                                    <th><?= t('table_role') ?></th>
                                    <th><?= t('table_status') ?></th>
                                    <th><?= t('table_created') ?></th>
                                    <th><?= t('table_actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($users)): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-users fa-2x mb-3"></i>
                                                <p><?= t('no_users_found') ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($users as $user): ?>
                                        <?php if (in_array($my_role_name, ['finance','member']) && $user['id'] !== $logged_in_id) continue; ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div class="avatar-placeholder bg-primary text-white rounded-circle me-3">
                                                        <?= strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)) ?>
                                                    </div>
                                                    <div>
                                                        <strong class="team"><?= htmlspecialchars($user['username']) ?></strong>
                                                        <div class="text-success small">
                                                            <?= htmlspecialchars(trim($user['first_name'].' '.$user['last_name'])) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <div><?= htmlspecialchars($user['email']) ?></div>
                                                <?php if (!empty($user['phone'])): ?>
                                                    <div class="text-success small"><?= htmlspecialchars($user['phone']) ?></div>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($user['role_name']) ?></span>
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <?php 
                                                        $statusClass = 'status-' . htmlspecialchars($user['status']);
                                                        $statusText = htmlspecialchars($user['status']);
                                                        switch($user['status']) {
                                                            case 'active':
                                                                $statusTranslation = t('status_active');
                                                                $indicatorClass = 'status-active-indicator';
                                                                break;
                                                            case 'inactive':
                                                                $statusTranslation = t('status_inactive');
                                                                $indicatorClass = 'status-inactive-indicator';
                                                                break;
                                                            case 'suspended':
                                                                $statusTranslation = t('status_suspended');
                                                                $indicatorClass = 'status-suspended-indicator';
                                                                break;
                                                            default:
                                                                $statusTranslation = $statusText;
                                                                $indicatorClass = 'status-inactive-indicator';
                                                        }
                                                    ?>
                                                    <span class="status-indicator <?= $indicatorClass ?>"></span>
                                                    <span class="status-badge <?= $statusClass ?>">
                                                        <?= $statusTranslation ?>
                                                    </span>
                                                </div>
                                            </td>
                                            <td>
                                                <?= date('M d, Y', strtotime($user['created_at'])) ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <?php if ($my_role_name === 'admin' || $user['id'] === $logged_in_id): ?>
                                                        <a href="edit_user.php?id=<?= $user['id'] ?>&editor=<?= $logged_in_id ?>" 
                                                           class="btn btn-action btn-outline-primary"
                                                           title="<?= t('action_edit') ?>">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                    <?php endif; ?>
                                                    <?php if (
                                                        $my_role_name === 'admin' &&
                                                        $user['role_name'] !== 'admin' &&
                                                        $user['id'] !== $logged_in_id
                                                    ): ?>
                                                        <form method="POST" class="d-inline"
                                                            onsubmit="return confirmDelete('<?= htmlspecialchars(addslashes($user['username'])) ?>')">
                                                            <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">
                                                            <input type="hidden" name="action" value="delete">
                                                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                                                            <button type="submit" class="btn btn-action btn-outline-danger" title="<?= t('action_delete') ?>">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create User Modal -->
<?php if ($my_role_name == 'admin'): ?>
    <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i> <?= t('modal_create_user') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST" id="createUserForm">
                    <div class="modal-body">
                        <input type="hidden" name="csrf_token" value="<?= csrf_token(); ?>">
                        <input type="hidden" name="action" value="create">
                        
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_username') ?></label>
                                <input type="text" class="form-control" name="username" required
                                    pattern="[a-zA-Z]+[a-zA-Z0-9]*"
                                    minlength="3" maxlength="50"
                                    title="<?= t('validation_username') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_email') ?></label>
                                <input type="email" class="form-control" name="email" required>
                                <div class="invalid-feedback email-feedback" style="display: none;">
                                    <?= t('email_exists_error') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_password') ?></label>
                                <input type="password" class="form-control" name="password" required 
                                       minlength="8" id="password">
                                <div class="form-text"><?= t('password_hint') ?></div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_confirm_password') ?></label>
                                <input type="password" class="form-control" name="confirm_password" required
                                       id="confirm_password">
                                <div class="invalid-feedback" id="password-match-feedback" style="display: none;">
                                    <?= t('password_mismatch') ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_first_name') ?></label>
                                <input type="text" class="form-control" name="first_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_last_name') ?></label>
                                <input type="text" class="form-control" name="last_name">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_phone') ?></label>
                                <input type="tel" class="form-control" name="phone" 
                                       pattern="[\d\s\-\+\(\)]{10,20}"
                                       title="<?= t('validation_phone') ?>">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_role') ?></label>
                                <select class="form-select" name="role_id" required>
                                    <option value=""><?= t('form_select_role') ?></option>
                                    <?php foreach ($roles as $role): ?>
                                        <?php if ($role['name'] == 'admin') continue; ?>
                                        <option value="<?php echo $role['id']; ?>">
                                            <?php echo htmlspecialchars($role['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label"><?= t('form_status') ?></label>
                                <select class="form-select" name="status" required>
                                    <option value="active"><?= t('filter_active') ?></option>
                                    <option value="inactive"><?= t('filter_inactive') ?></option>
                                    <option value="suspended"><?= t('filter_suspended') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('button_cancel') ?></button>
                        <button type="submit" class="btn btn-primary" id="createUserBtn">
                            <span id="createBtnText"><?= t('button_create_user') ?></span>
                            <span id="createBtnLoader" class="loader ms-2" style="display: none;"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Confirm delete
    function confirmDelete(username) {
        const message = "<?= t('confirm_delete', ['username' => '{username}']) ?>".replace('{username}', username);
        return confirm(message);
    }
    
    // Password match validation
    document.addEventListener('DOMContentLoaded', function() {
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        const passwordMatchFeedback = document.getElementById('password-match-feedback');
        
        function checkPasswordMatch() {
            if (password.value !== confirmPassword.value) {
                confirmPassword.classList.add('is-invalid');
                passwordMatchFeedback.style.display = 'block';
                return false;
            } else {
                confirmPassword.classList.remove('is-invalid');
                passwordMatchFeedback.style.display = 'none';
                return true;
            }
        }
        
        if (password && confirmPassword) {
            password.addEventListener('input', checkPasswordMatch);
            confirmPassword.addEventListener('input', checkPasswordMatch);
        }
    });
</script>

<!-- JQuery and DataTables JS -->
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>

<script>
        $(document).ready(function() {
            // Check if table exists
            const table = document.getElementById('usersTable');
            if (table) {
                $(table).DataTable({
                    responsive: true,
                    language: {
                        search: "_INPUT_",
                        searchPlaceholder: "<?= t('search_placeholder') ?>",
                        lengthMenu: "_MENU_",
                        info: "Showing _START_ to _END_ of _TOTAL_ users",
                        paginate: {
                            first: '<i class="fas fa-angle-double-left"></i>',
                            last: '<i class="fas fa-angle-double-right"></i>',
                            next: '<i class="fas fa-angle-right"></i>',
                            previous: '<i class="fas fa-angle-left"></i>'
                        }
                    },
                    columnDefs: [
                        { orderable: false, targets: -1 } // Disable sorting on Actions column
                    ],
                    order: [[3, 'asc']] // Sort by role initially
                });
            }
        });

        // Form submission loading indicator
        const createUserForm = document.getElementById('createUserForm');
        if (createUserForm) {
            createUserForm.addEventListener('submit', function(e) {
                if (!checkPasswordMatch()) {
                    e.preventDefault();
                    return;
                }
                
                const submitBtn = document.getElementById('createUserBtn');
                const btnText = document.getElementById('createBtnText');
                const btnLoader = document.getElementById('createBtnLoader');
                
                submitBtn.disabled = true;
                btnText.style.display = 'none';
                btnLoader.style.display = 'inline-block';
            });
        }
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Theme-aware styling
        function updateThemeStyles() {
            const isDark = !document.body.classList.contains('light');
            document.body.style.backgroundColor = isDark ? '#121212' : '#ffffff';
        }
        
        // Listen for theme changes
        window.addEventListener('message', function(e) {
            if (e.data === 'light' || e.data === 'dark') {
                setTimeout(updateThemeStyles, 100);
            }
        });
        
        // Initial theme update
        updateThemeStyles();
    });
</script>
</body>
</html>