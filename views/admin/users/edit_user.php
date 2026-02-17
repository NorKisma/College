<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

// Check authentication
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: login.php');
    exit;
}

// Get user info
$res = $conn->query("
    SELECT u.id, u.username, u.role_id, r.name AS role_name
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.id = $logged_in_id
");
$me = $res->fetch_assoc();
$my_role_id = (int) $me['role_id'];
$my_role_name = $me['role_name'];

// Get user to edit
$edit_user_id = (int) ($_GET['id'] ?? 0);
if (!$edit_user_id) {
    header('Location: users.php?error=invalid_user');
    exit;
}

// Check permissions
if ($my_role_name == 'member' && $edit_user_id != $logged_in_id) {
    header('Location: users.php?error=no_permission');
    exit;
}

// Fetch user data
$stmt = $conn->prepare("
    SELECT u.*, r.name AS role_name 
    FROM users u 
    JOIN roles r ON r.id = u.role_id 
    WHERE u.id = ?
");
$stmt->bind_param("i", $edit_user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: users.php?error=user_not_found');
    exit;
}

$user = $result->fetch_assoc();

// Fetch roles for dropdown
$roles = [];
$res = $conn->query("SELECT id, name FROM roles ORDER BY id ASC");
while ($row = $res->fetch_assoc()) $roles[] = $row;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['csrf_token'])) {
    if (!validate_csrf_token($_POST['csrf_token'])) {
        flash_message('error', 'Invalid CSRF token.');
        header("Location: edit_user.php?id=$edit_user_id");
        exit;
    }

    $action = $_POST['action'] ?? 'update';
    
    if ($action == 'update') {
        // Collect data
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $first_name = trim($_POST['first_name'] ?? '');
        $last_name = trim($_POST['last_name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $status = $_POST['status'] ?? 'active';
        
        // Role can only be changed by admin
        if ($my_role_name == 'admin' && $edit_user_id != $logged_in_id) {
            $role_id = (int) ($_POST['role_id'] ?? $user['role_id']);
            // Prevent admin from removing own admin role
            if ($edit_user_id == $logged_in_id && $role_id != $my_role_id) {
                flash_message('error', 'You cannot remove your own admin role.');
                header("Location: edit_user.php?id=$edit_user_id");
                exit;
            }
        } else {
            $role_id = $user['role_id'];
        }
        
        // Password update (optional)
        $password_update = '';
      if (!empty($_POST['password'])) {
    if ($_POST['password'] !== $_POST['confirm_password']) {
        flash_message('error', 'Passwords do not match.');
        header("Location: edit_user.php?id=$edit_user_id");
        exit;
    }
    // Store plain text (not recommended)
    $plain_password = $_POST['password'];
    $password_update = ", password = ?";
}

        
        // Validate email
        if (!validate_email($email)) {
            flash_message('error', 'Invalid email format.');
            header("Location: edit_user.php?id=$edit_user_id");
            exit;
        }
        
        // Check duplicate email (excluding current user)
        if (is_email_taken($conn, $email, $edit_user_id)) {
            flash_message('error', 'Email already exists.');
            header("Location: edit_user.php?id=$edit_user_id");
            exit;
        }
        
        // Validate phone
        if (!empty($phone) && !validate_phone($phone)) {
            flash_message('error', 'Invalid phone number format.');
            header("Location: edit_user.php?id=$edit_user_id");
            exit;
        }
        
        // Build query
        if (!empty($password_update)) {
            $sql = "UPDATE users SET 
                    username = ?, email = ?, first_name = ?, last_name = ?, 
                    phone = ?, status = ?, role_id = ? $password_update 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
           $stmt->bind_param("ssssssisi", $username, $email, $first_name, $last_name, 
                 $phone, $status, $role_id, $plain_password, $edit_user_id);
  } else {
            $sql = "UPDATE users SET 
                    username = ?, email = ?, first_name = ?, last_name = ?, 
                    phone = ?, status = ?, role_id = ? 
                    WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssii", $username, $email, $first_name, $last_name, 
                             $phone, $status, $role_id, $edit_user_id);
        }
        
        if ($stmt->execute()) {
            // Log the action
            $action_type = ($edit_user_id == $logged_in_id) ? 'USER_UPDATE_SELF' : 'USER_UPDATE';
           
            
            flash_message('success', 'User updated successfully.');
            header("Location: users.php");
            exit;
        } else {
            flash_message('error', 'Failed to update user.');
            header("Location: edit_user.php?id=$edit_user_id");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit User | EduSys Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ==========================================================================
           ULTRA MODERN DESIGN SYSTEM
           ========================================================================== */
        :root {
            /* Primary Colors */
            --primary-hue: 220;
            --primary-color: hsl(var(--primary-hue), 85%, 60%);
            --primary-dark: hsl(var(--primary-hue), 85%, 45%);
            --primary-light: hsl(var(--primary-hue), 85%, 75%);
            --primary-glow: hsl(var(--primary-hue), 100%, 70%);
            
            /* Status Colors */
            --success-color: #00d9b8;
            --warning-color: #ffb347;
            --danger-color: #ff6b8b;
            --info-color: #2dd4bf;
            
            /* Theme Colors - Light */
            --light-bg: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
            --light-card-bg: rgba(255, 255, 255, 0.92);
            --light-text: #1e293b;
            --light-text-secondary: #64748b;
            
            /* Theme Colors - Dark */
            --dark-bg: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
            --dark-card-bg: rgba(30, 41, 59, 0.92);
            --dark-text: #e2e8f0;
            --dark-text-secondary: #94a3b8;
            
            /* Glass Effect */
            --glass-blur: blur(20px);
            --glass-border: 1px solid rgba(255, 255, 255, 0.18);
            
            /* Animations */
            --transition-fast: 0.2s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 0.5s cubic-bezier(0.4, 0, 0.2, 1);
            
            /* Border Radius */
            --radius-sm: 12px;
            --radius-md: 16px;
            --radius-lg: 20px;
            --radius-xl: 24px;
        }

        /* ==========================================================================
           BASE STYLES
           ========================================================================== */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: var(--light-bg);
            color: var(--light-text);
            min-height: 100vh;
            font-weight: 400;
            line-height: 1.6;
        }

        body.dark {
            background: var(--dark-bg);
            color: var(--dark-text);
        }

        .container {
            max-width: 1200px;
        }

        /* ==========================================================================
           CUSTOM SCROLLBAR
           ========================================================================== */
        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(0, 0, 0, 0.05);
            border-radius: 10px;
        }

        body.dark ::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, var(--primary-color), var(--primary-light));
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, var(--primary-dark), var(--primary-color));
        }

        /* ==========================================================================
           BACK BUTTON - FLOATING STYLE
           ========================================================================== */
        .back-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 24px;
            background: linear-gradient(135deg, rgba(100, 116, 139, 0.1), rgba(71, 85, 105, 0.1));
            backdrop-filter: var(--glass-blur);
            border: 1px solid rgba(100, 116, 139, 0.2);
            border-radius: var(--radius-md);
            color: var(--light-text);
            text-decoration: none;
            font-weight: 500;
            transition: all var(--transition-normal);
            margin-bottom: 2rem;
        }

        body.dark .back-btn {
            background: rgba(255, 255, 255, 0.05);
            border-color: rgba(255, 255, 255, 0.1);
            color: var(--dark-text);
        }

        .back-btn:hover {
            transform: translateX(-4px);
            background: linear-gradient(135deg, rgba(100, 116, 139, 0.2), rgba(71, 85, 105, 0.2));
            border-color: var(--primary-color);
            color: var(--primary-color);
        }

        /* ==========================================================================
           MAIN CARD - GLASS EFFECT
           ========================================================================== */
        .main-card {
            background: linear-gradient(
                135deg,
                rgba(255, 255, 255, 0.12),
                rgba(255, 255, 255, 0.08)
            );
            backdrop-filter: var(--glass-blur);
            -webkit-backdrop-filter: var(--glass-blur);
            border: var(--glass-border);
            border-radius: var(--radius-xl);
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.08),
                0 8px 32px rgba(31, 38, 135, 0.08),
                inset 0 1px 0 rgba(255, 255, 255, 0.2);
            overflow: hidden;
            position: relative;
            animation: cardFloat 6s ease-in-out infinite;
        }

        body.dark .main-card {
            box-shadow: 
                0 20px 60px rgba(0, 0, 0, 0.2),
                0 8px 32px rgba(31, 38, 135, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        @keyframes cardFloat {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-10px); }
        }

        /* ==========================================================================
           PROFILE HEADER - GRADIENT OVERLAY
           ========================================================================== */
        .profile-header {
            background: linear-gradient(135deg, 
                var(--primary-color) 0%, 
                #7209b7 50%,
                var(--danger-color) 100%);
            background-size: 200% 200%;
            animation: gradientShift 8s ease infinite;
            padding: 3rem 2rem;
            position: relative;
            overflow: hidden;
        }

        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .profile-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='100' height='100' viewBox='0 0 100 100' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M11 18c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm48 25c3.866 0 7-3.134 7-7s-3.134-7-7-7-7 3.134-7 7 3.134 7 7 7zm-43-7c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm63 31c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM34 90c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zm56-76c1.657 0 3-1.343 3-3s-1.343-3-3-3-3 1.343-3 3 1.343 3 3 3zM12 86c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm28-65c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm23-11c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-6 60c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm29 22c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zM32 63c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm57-13c2.76 0 5-2.24 5-5s-2.24-5-5-5-5 2.24-5 5 2.24 5 5 5zm-9-21c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM60 91c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM35 41c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2zM12 60c1.105 0 2-.895 2-2s-.895-2-2-2-2 .895-2 2 .895 2 2 2z' fill='%23ffffff' fill-opacity='0.05' fill-rule='evenodd'/%3E%3C/svg%3E");
            opacity: 0.3;
        }

        .profile-content {
            position: relative;
            z-index: 2;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        /* ==========================================================================
           AVATAR - HOLOGRAPHIC EFFECT
           ========================================================================== */
        .avatar-container {
            position: relative;
            width: 100px;
            height: 100px;
        }

        .avatar-placeholder {
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.3), rgba(255, 255, 255, 0.1));
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.5rem;
            font-weight: 700;
            color: white;
            border: 3px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
            transition: all var(--transition-normal);
        }

        .avatar-placeholder::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(
                45deg,
                transparent,
                rgba(255, 255, 255, 0.3),
                transparent
            );
            transform: rotate(45deg);
            animation: avatarShine 3s ease-in-out infinite;
        }

        @keyframes avatarShine {
            0% { transform: rotate(45deg) translateX(-100%); }
            100% { transform: rotate(45deg) translateX(100%); }
        }

        .avatar-placeholder:hover {
            transform: scale(1.05);
            box-shadow: 0 0 40px rgba(255, 255, 255, 0.3);
        }

        /* ==========================================================================
           USER INFO DISPLAY
           ========================================================================== */
        .user-info {
            color: white;
            flex: 1;
        }

        .user-info h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .user-details {
            display: flex;
            gap: 1.5rem;
            flex-wrap: wrap;
            opacity: 0.9;
        }

        .user-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
        }

        .role-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ==========================================================================
           CARD BODY - FORM SECTION
           ========================================================================== */
        .card-body {
            padding: 2.5rem;
        }

        body.dark .card-body {
            background: var(--dark-card-bg);
        }

        /* ==========================================================================
           ALERT MESSAGES - GLASS EFFECT
           ========================================================================== */
        .alert {
            border: none;
            border-radius: var(--radius-md);
            padding: 1.25rem 1.5rem;
            backdrop-filter: var(--glass-blur);
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .alert::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            bottom: 0;
            width: 4px;
            border-radius: var(--radius-md) 0 0 var(--radius-md);
        }

        .alert-success {
            background: rgba(0, 217, 184, 0.1);
            border: 1px solid rgba(0, 217, 184, 0.2);
        }

        .alert-success::before {
            background: var(--success-color);
        }

        .alert-danger {
            background: rgba(255, 107, 139, 0.1);
            border: 1px solid rgba(255, 107, 139, 0.2);
        }

        .alert-danger::before {
            background: var(--danger-color);
        }

        .alert .btn-close {
            filter: invert(1);
            opacity: 0.8;
        }

        /* ==========================================================================
           FORM STYLES - MODERN INPUTS
           ========================================================================== */
        .form-label {
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: var(--light-text);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        body.dark .form-label {
            color: var(--dark-text);
        }

        .form-label i {
            color: var(--primary-color);
            width: 20px;
        }

        .form-control, .form-select {
            border: 2px solid transparent;
            border-radius: var(--radius-sm);
            padding: 0.875rem 1rem;
            font-size: 0.95rem;
            transition: all var(--transition-normal);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: var(--glass-blur);
        }

        body.dark .form-control,
        body.dark .form-select {
            background: rgba(30, 41, 59, 0.9);
            color: var(--dark-text);
            border-color: rgba(255, 255, 255, 0.1);
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(var(--primary-hue), 85%, 60%, 0.1);
            outline: none;
            transform: translateY(-2px);
        }

        .form-control[readonly] {
            background: rgba(0, 0, 0, 0.05);
            opacity: 0.8;
        }

        body.dark .form-control[readonly] {
            background: rgba(255, 255, 255, 0.05);
        }

        .form-text {
            font-size: 0.85rem;
            margin-top: 0.5rem;
            opacity: 0.7;
        }

        .form-text.text-warning {
            color: var(--warning-color) !important;
        }

        /* ==========================================================================
           FORM ROWS & GRID
           ========================================================================== */
        .form-row {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            position: relative;
        }

        /* ==========================================================================
           PASSWORD SECTION - SPECIAL STYLING
           ========================================================================== */
        .password-section {
            background: linear-gradient(135deg, rgba(67, 97, 238, 0.05), rgba(114, 9, 183, 0.05));
            border: 1px solid rgba(67, 97, 238, 0.1);
            border-radius: var(--radius-md);
            padding: 1.5rem;
            margin: 2rem 0;
        }

        body.dark .password-section {
            background: rgba(67, 97, 238, 0.05);
            border-color: rgba(67, 97, 238, 0.2);
        }

        .password-section .form-label {
            color: var(--primary-color);
        }

        /* ==========================================================================
           BUTTONS - FLOATING DESIGN
           ========================================================================== */
        .btn {
    border: none;
    border-radius: var(--radius-md);
    padding: 0.875rem 2rem;
    font-weight: 600;
    font-size: 0.95rem;
    letter-spacing: 0.5px;
    transition: all var(--transition-normal);
    position: relative;
    overflow: hidden;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.75rem;
    min-width: 140px;
}

.btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.2),
        transparent
    );
    transition: left 0.6s ease;
}

.btn:hover::before {
    left: 100%;
}

.btn-primary {
    background: linear-gradient(
        135deg,
        var(--primary-color),
        var(--primary-dark)
    );
    color: white;
    box-shadow: 
        0 4px 20px rgba(var(--primary-hue), 85%, 60%, 0.3),
        0 2px 6px rgba(var(--primary-hue), 85%, 60%, 0.2);
}

.btn-primary:hover {
    transform: translateY(-3px);
    box-shadow: 
        0 8px 30px rgba(var(--primary-hue), 85%, 60%, 0.4),
        0 4px 10px rgba(var(--primary-hue), 85%, 60%, 0.3);
    color: white;
}

.btn-outline-secondary {
    background: transparent;
    border: 2px solid rgba(100, 116, 139, 0.3);
    color: var(--light-text-secondary);
    min-width: 120px;
}

body.dark .btn-outline-secondary {
    border-color: rgba(255, 255, 255, 0.2);
    color: var(--dark-text-secondary);
}

.btn-outline-secondary:hover {
    background: rgba(100, 116, 139, 0.1);
    border-color: var(--primary-color);
    color: var(--primary-color);
    transform: translateY(-3px);
}

/* ==========================================================================
   FORM ACTIONS - FLOATING BAR
   ========================================================================== */
.form-actions {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 2rem;
    margin-top: 2rem;
    border-top: 1px solid rgba(0, 0, 0, 0.1);
}

body.dark .form-actions {
    border-top-color: rgba(255, 255, 255, 0.1);
}

/* ==========================================================================
   SPINNER FOR LOADING STATE
   ========================================================================== */
.spinner {
    display: inline-block;
    width: 1rem;
    height: 1rem;
    border: 2px solid rgba(255, 255, 255, 0.3);
    border-top-color: white;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to { transform: rotate(360deg); }
}

/* ==========================================================================
   INVALID FEEDBACK
   ========================================================================== */
.invalid-feedback {
    display: block;
    width: 100%;
    margin-top: 0.5rem;
    font-size: 0.875rem;
    color: var(--danger-color);
    animation: shake 0.5s ease-in-out;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-5px); }
    75% { transform: translateX(5px); }
}

/* ==========================================================================
   RESPONSIVE DESIGN
   ========================================================================== */
@media (max-width: 768px) {
    .container {
        padding: 1rem;
    }
    
    .profile-header {
        padding: 2rem 1rem;
    }
    
    .profile-content {
        flex-direction: column;
        text-align: center;
        gap: 1rem;
    }
    
    .user-details {
        justify-content: center;
    }
    
    .card-body {
        padding: 1.5rem;
    }
    
    .form-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .form-actions {
        flex-direction: column;
        gap: 1rem;
    }
    
    .btn {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .profile-header {
        padding: 1.5rem 1rem;
    }
    
    .avatar-container {
        width: 80px;
        height: 80px;
    }
    
    .avatar-placeholder {
        font-size: 2rem;
    }
    
    .user-info h1 {
        font-size: 1.5rem;
    }
}

/* ==========================================================================
   SPECIAL EFFECTS
   ========================================================================== */

/* Particle Background */
body::before {
    content: '';
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    pointer-events: none;
    z-index: -1;
    background: 
        radial-gradient(circle at 20% 80%, rgba(var(--primary-hue), 85%, 60%, 0.05) 0%, transparent 50%),
        radial-gradient(circle at 80% 20%, rgba(var(--primary-hue), 85%, 60%, 0.05) 0%, transparent 50%);
}

/* Glow Effect on Hover */
.form-control:hover, .form-select:hover {
    box-shadow: 0 0 20px rgba(var(--primary-hue), 85%, 60%, 0.1);
}

/* Floating Animation for Cards */
.main-card {
    animation: float 6s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* ==========================================================================
   LOADING OVERLAY
   ========================================================================== */
.loading-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.8);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.loading-overlay.active {
    opacity: 1;
    visibility: visible;
}

.loading-spinner {
    width: 80px;
    height: 80px;
    border: 4px solid rgba(255, 255, 255, 0.1);
    border-top-color: var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

/* ==========================================================================
   CUSTOM SELECT STYLING
   ========================================================================== */
.form-select {
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%234361ee' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 1rem center;
    background-size: 16px 12px;
    padding-right: 2.5rem;
}

/* ==========================================================================
   THEME TRANSITIONS
   ========================================================================== */
.main-card, .alert, .form-control, .btn {
    transition: background-color 0.3s ease, border-color 0.3s ease, color 0.3s ease;
}

/* ==========================================================================
   FOCUS VISIBLE FOR ACCESSIBILITY
   ========================================================================== */
*:focus-visible {
    outline: 2px solid var(--primary-color);
    outline-offset: 2px;
    border-radius: var(--radius-sm);
}

/* ==========================================================================
   SELECTION COLOR
   ========================================================================== */
::selection {
    background-color: rgba(var(--primary-hue), 85%, 60%, 0.3);
    color: inherit;
}
    </style>
</head>
<body class="<?php echo isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'dark' ? 'dark' : 'light'; ?>">
    <div class="container py-5">
        <!-- Back Button -->
        <a href="users.php" class="back-btn">
            <i class="fas fa-arrow-left"></i>
            <span>Back to Users</span>
        </a>
        
        <!-- Main Card -->
        <div class="main-card">
            <!-- Profile Header -->
            <div class="profile-header">
                <div class="profile-content">
                    <div class="avatar-container">
                        <div class="avatar-placeholder">
                            <?php echo strtoupper(substr($user['first_name'] ?? $user['username'], 0, 1)); ?>
                        </div>
                    </div>
                    
                    <div class="user-info">
                        <h1>Edit User Profile</h1>
                        <div class="user-details">
                            <div class="user-detail">
                                <i class="fas fa-user"></i>
                                <span><?php echo htmlspecialchars($user['username']); ?></span>
                            </div>
                            <div class="user-detail">
                                <i class="fas fa-envelope"></i>
                                <span><?php echo htmlspecialchars($user['email']); ?></span>
                            </div>
                            <div class="user-detail">
                                <span class="role-badge"><?php echo htmlspecialchars($user['role_name']); ?></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Card Body -->
            <div class="card-body">
                <!-- Flash Messages -->
                <?php $flash_messages = get_flash_messages(); ?>
                <?php if (!empty($flash_messages)): ?>
                    <?php foreach ($flash_messages as $msg): ?>
                        <div class="alert alert-<?php echo $msg['type'] == 'error' ? 'danger' : $msg['type']; ?> 
                                    alert-dismissible fade show" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-<?php echo $msg['type'] == 'error' ? 'exclamation-triangle' : 'check-circle'; ?> me-3 fs-4"></i>
                                <div class="flex-grow-1">
                                    <?php echo htmlspecialchars($msg['message']); ?>
                                </div>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
                
                <!-- Edit Form -->
                <form method="POST" id="editUserForm" class="needs-validation" novalidate>
                    <input type="hidden" name="csrf_token" value="<?php echo csrf_token(); ?>">
                    <input type="hidden" name="action" value="update">
                    
                    <!-- Basic Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user-tag"></i>
                                Username *
                            </label>
                            <input type="text" class="form-control" name="username" 
                                   value="<?php echo htmlspecialchars($user['username']); ?>" required
                                   <?php echo ($my_role_name == 'member' && $edit_user_id != $logged_in_id) ? 'readonly' : ''; ?>
                                   pattern="[a-zA-Z][a-zA-Z0-9]*"
                                   title="Username must start with a letter and contain only letters and numbers">
                            <div class="invalid-feedback">
                                Please enter a valid username (letters and numbers only, starting with a letter).
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email *
                            </label>
                            <input type="email" class="form-control" name="email" 
                                   value="<?php echo htmlspecialchars($user['email']); ?>" required
                                   <?php echo ($my_role_name == 'member' && $edit_user_id != $logged_in_id) ? 'readonly' : ''; ?>>
                            <div class="invalid-feedback">
                                Please enter a valid email address.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Personal Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                First Name
                            </label>
                            <input type="text" class="form-control" name="first_name" 
                                   value="<?php echo htmlspecialchars($user['first_name'] ?? ''); ?>">
                        </div>
                        
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-user"></i>
                                Last Name
                            </label>
                            <input type="text" class="form-control" name="last_name" 
                                   value="<?php echo htmlspecialchars($user['last_name'] ?? ''); ?>">
                        </div>
                    </div>
                    
                    <!-- Contact Information -->
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-phone"></i>
                                Phone
                            </label>
                            <input type="tel" class="form-control" name="phone" 
                                   value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>"
                                   pattern="[\d\s\-\+\(\)]{10,20}"
                                   title="Enter a valid phone number (10-20 digits)">
                            <div class="invalid-feedback">
                                Please enter a valid phone number.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Admin Only Fields -->
                    <?php if ($my_role_name == 'admin'): ?>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-user-shield"></i>
                                    Role
                                </label>
                                <select class="form-select" name="role_id" 
                                        <?php echo ($edit_user_id == $logged_in_id) ? 'disabled' : ''; ?>>
                                    <?php foreach ($roles as $role): ?>
                                        <option value="<?php echo $role['id']; ?>" 
                                            <?php echo ($role['id'] == $user['role_id']) ? 'selected' : ''; ?>
                                            <?php echo ($role['name'] == 'admin' && $edit_user_id == $logged_in_id) ? 'disabled' : ''; ?>>
                                            <?php echo htmlspecialchars($role['name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if ($edit_user_id == $logged_in_id): ?>
                                    <div class="form-text text-warning">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        You cannot change your own role
                                    </div>
                                <?php endif; ?>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-circle"></i>
                                    Status
                                </label>
                                <select class="form-select" name="status">
                                    <option value="active" <?php echo ($user['status'] == 'active') ? 'selected' : ''; ?>>Active</option>
                                    <option value="inactive" <?php echo ($user['status'] == 'inactive') ? 'selected' : ''; ?>>Inactive</option>
                                    <option value="suspended" <?php echo ($user['status'] == 'suspended') ? 'selected' : ''; ?>>Suspended</option>
                                </select>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Password Section -->
                    <div class="password-section">
                        <h5 class="mb-3"><i class="fas fa-key me-2"></i>Password Update</h5>
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i>
                                    New Password
                                </label>
                                <input type="password" class="form-control" name="password" id="newPassword"
                                       minlength="8" placeholder="Leave blank to keep current password">
                                <div class="form-text">Minimum 8 characters</div>
                            </div>
                            
                            <div class="form-group">
                                <label class="form-label">
                                    <i class="fas fa-lock"></i>
                                    Confirm Password
                                </label>
                                <input type="password" class="form-control" name="confirm_password" 
                                       id="confirmNewPassword" placeholder="Confirm new password">
                                <div class="invalid-feedback" id="passwordMatchFeedback">
                                    Passwords do not match
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Form Actions -->
                    <div class="form-actions">
                        <a href="users.php" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary" id="submitBtn">
                            <i class="fas fa-save"></i>
                            <span id="submitText">Update User</span>
                            <div class="spinner" id="submitSpinner" style="display: none;"></div>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Loading Overlay -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="loading-spinner"></div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password Match Validation
        const newPassword = document.getElementById('newPassword');
        const confirmNewPassword = document.getElementById('confirmNewPassword');
        const passwordMatchFeedback = document.getElementById('passwordMatchFeedback');
        
        function checkPasswordMatch() {
            if (newPassword.value && confirmNewPassword.value && newPassword.value !== confirmNewPassword.value) {
                confirmNewPassword.classList.add('is-invalid');
                passwordMatchFeedback.style.display = 'block';
                return false;
            } else {
                confirmNewPassword.classList.remove('is-invalid');
                passwordMatchFeedback.style.display = 'none';
                return true;
            }
        }
        
        if (newPassword && confirmNewPassword) {
            newPassword.addEventListener('input', checkPasswordMatch);
            confirmNewPassword.addEventListener('input', checkPasswordMatch);
        }
        
        // Form Validation and Submission
        const form = document.getElementById('editUserForm');
        const submitBtn = document.getElementById('submitBtn');
        const submitText = document.getElementById('submitText');
        const submitSpinner = document.getElementById('submitSpinner');
        const loadingOverlay = document.getElementById('loadingOverlay');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // Check password match
                if (!checkPasswordMatch()) {
                    confirmNewPassword.focus();
                    return;
                }
                
                // Validate form
                if (!form.checkValidity()) {
                    e.stopPropagation();
                    form.classList.add('was-validated');
                    return;
                }
                
                // Show loading state
                submitBtn.disabled = true;
                submitText.textContent = 'Updating...';
                submitSpinner.style.display = 'inline-block';
                loadingOverlay.classList.add('active');
                
                // Submit form after short delay to show loading animation
                setTimeout(() => {
                    form.submit();
                }, 500);
            });
        }
        
        // Real-time validation
        const inputs = form.querySelectorAll('input[required], select[required]');
        inputs.forEach(input => {
            input.addEventListener('input', function() {
                if (this.checkValidity()) {
                    this.classList.remove('is-invalid');
                    this.classList.add('is-valid');
                } else {
                    this.classList.remove('is-valid');
                    this.classList.add('is-invalid');
                }
            });
            
            input.addEventListener('blur', function() {
                if (this.hasAttribute('required') && !this.value.trim()) {
                    this.classList.add('is-invalid');
                }
            });
        });
        
        // Theme detection from cookies
        function detectTheme() {
            const theme = document.cookie.split('; ').find(row => row.startsWith('theme='));
            if (theme) {
                const themeValue = theme.split('=')[1];
                document.body.className = themeValue;
            }
        }
        
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            detectTheme();
            
            // Add floating animation to form groups on hover
            const formGroups = document.querySelectorAll('.form-group');
            formGroups.forEach(group => {
                group.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                group.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
            
            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl + S to save
                if (e.ctrlKey && e.key === 's') {
                    e.preventDefault();
                    submitBtn.click();
                }
                
                // Escape to cancel
                if (e.key === 'Escape') {
                    window.location.href = 'users.php';
                }
            });
        });
    </script>
</body>
</html>