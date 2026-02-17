<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// ================= THEME =================
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");

if($theme_res && $theme_row = $theme_res->fetch_assoc()){
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

// ================= TEACHER STATS =================
$totalTeachers = 0;
$activeTeachers = 0;

// Total teachers
$resTotal = $conn->query("SELECT COUNT(*) AS total FROM teachers");
if ($resTotal && $row = $resTotal->fetch_assoc()) {
    $totalTeachers = (int)$row['total'];
}

// Active teachers
$resActive = $conn->query("SELECT COUNT(*) AS active FROM teachers WHERE status = 'active'");
if ($resActive && $row = $resActive->fetch_assoc()) {
    $activeTeachers = (int)$row['active'];
}


// Check if employee_code already exists function
function employeeCodeExists($conn, $employee_code) {
    $stmt = $conn->prepare("SELECT id FROM teachers WHERE employee_code = ?");
    $stmt->bind_param("s", $employee_code);
    $stmt->execute();
    $stmt->store_result();
    $exists = $stmt->num_rows > 0;
    $stmt->close();
    return $exists;
}

// Process form submission
$message = "";
$message_type = ""; // success, error, warning

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    $employee_code = trim($_POST['employee_code'] ?? '');
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $qualification = trim($_POST['qualification'] ?? '');
    $hire_date = $_POST['hire_date'] ?? '';
    $status = $_POST['status'] ?? 'active';
    $login_pin = trim($_POST['login_pin'] ?? ''); // NEW PIN FIELD
    $salary = (float)($_POST['salary'] ?? 0);


    // Validate required fields
    $errors = [];

    if (empty($employee_code)) {
        $errors[] = "Employee code is required";
    } elseif (employeeCodeExists($conn, $employee_code)) {
        $errors[] = "Employee code already exists in the system";
    }

    if (empty($first_name)) $errors[] = "First name is required";
    if (empty($last_name)) $errors[] = "Last name is required";

    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format";
    }

    // Validate hire date
    if (!empty($hire_date) && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $hire_date)) {
        $errors[] = "Invalid date format (YYYY-MM-DD)";
    }

    // Validate login PIN: must be exactly 6 digits
    if (!preg_match('/^\d{6}$/', $login_pin)) {
        $errors[] = "Login PIN must be exactly 6 digits (numbers only)";
    }

    // Insert into database if no errors
    if (empty($errors)) {
       $stmt = $conn->prepare("INSERT INTO teachers (employee_code, first_name, last_name, gender, phone, email, qualification, hire_date, status, salary, login_pin) 
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->bind_param(
            "sssssssssds",
            $employee_code,
            $first_name,
            $last_name,
            $gender,
            $phone,
            $email,
            $qualification,
            $hire_date,
            $status,
            $salary,
            $login_pin
        );

        if ($stmt->execute()) {
            $message = "Teacher added successfully!";
            $message_type = "success";
            $_POST = []; // clear form
        } else {
            $message = "Error adding teacher: " . $stmt->error;
            $message_type = "error";
        }

        $stmt->close();
    } else {
        $message = implode("<br>", $errors);
        $message_type = "error";
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Teacher - Management System</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
        /* Tom Select Customization */
        .ts-control {
            border-radius: var(--radius-md);
            border-color: var(--border-color);
            padding: 10px 12px;
            background-color: var(--bg-primary);
            color: var(--text-primary);
        }
        .ts-wrapper.single .ts-control {
            background-image: none;
        }
        .ts-wrapper.single .ts-control::after {
            border-color: #888 transparent transparent transparent;
        }
        .dark .ts-control {
            background-color: var(--bg-primary);
            border-color: var(--border-color);
            color: #fff;
        }
        .dark .ts-dropdown {
            background-color: var(--bg-secondary);
            color: #fff;
            border-color: var(--border-color);
        }
        .dark .ts-dropdown .option {
            color: #fff;
        }
        .dark .ts-dropdown .active {
            background-color: var(--bg-tertiary);
            color: #fff;
        }
        .dark .ts-control input {
            color: #fff;
        }
    </style>    <style>
        :root {
            /* Light theme variables */
            --bg-primary: #f8fafc;
            --bg-secondary: #ffffff;
            --bg-tertiary: #f1f5f9;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-tertiary: #94a3b8;
            --border-color: #e2e8f0;
            --accent-color: #4f46e5;
            --accent-hover: #4338ca;
            --success-color: #10b981;
            --error-color: #ef4444;
            --warning-color: #f59e0b;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
        }

        body.dark {
            /* Dark theme variables */
            --bg-primary: #0f172a;
            --bg-secondary: #1e293b;
            --bg-tertiary: #334155;
            --text-primary: #f1f5f9;
            --text-secondary: #cbd5e1;
            --text-tertiary: #94a3b8;
            --border-color: #475569;
            --accent-color: #6366f1;
            --accent-hover: #818cf8;
            --success-color: #34d399;
            --error-color: #f87171;
            --warning-color: #fbbf24;
            --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.3);
            --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.3), 0 4px 6px -2px rgba(0, 0, 0, 0.2);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.5;
            transition: background-color 0.3s, color 0.3s;
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid var(--border-color);
        }

        .header-left h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .header-left p {
            color: var(--text-secondary);
            margin-top: 5px;
            font-size: 0.95rem;
        }

        .theme-indicator {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            background-color: var(--bg-tertiary);
            border-radius: var(--radius-md);
            font-size: 0.875rem;
            font-weight: 500;
        }

        .theme-icon {
            font-size: 1.125rem;
        }

        .main-content {
            display: grid;
            grid-template-columns: 1fr;
            gap: 30px;
        }

        @media (min-width: 992px) {
            .main-content {
                grid-template-columns: 1fr 350px;
            }
        }

        .form-container {
            background-color: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 30px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 1px solid var(--border-color);
            color: var(--text-primary);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(1, 1fr);
            gap: 20px;
        }

        @media (min-width: 768px) {
            .form-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .form-group {
            display: flex;
            flex-direction: column;
        }

        .form-group.full-width {
            grid-column: 1 / -1;
        }

        .form-label {
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 8px;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .required::after {
            content: "*";
            color: var(--error-color);
            margin-left: 4px;
        }

        .form-control {
            padding: 12px 16px;
            background-color: var(--bg-primary);
            border: 1px solid var(--border-color);
            border-radius: var(--radius-md);
            font-size: 1rem;
            color: var(--text-primary);
            transition: border-color 0.2s, box-shadow 0.2s;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.2);
        }

        .form-control.dark:focus {
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.3);
        }

        select.form-control {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%2364748b' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 0.75rem center;
            background-size: 1.5em 1.5em;
            padding-right: 2.5rem;
        }

        .form-actions {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 30px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 12px 24px;
            font-size: 1rem;
            font-weight: 500;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s;
            border: none;
            outline: none;
        }

        .btn-primary {
            background-color: var(--accent-color);
            color: white;
        }

        .btn-primary:hover {
            background-color: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: var(--shadow-md);
        }

        .btn-secondary {
            background-color: var(--bg-tertiary);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .btn-secondary:hover {
            background-color: var(--border-color);
        }

        .btn-reset {
            background-color: transparent;
            color: var(--text-secondary);
            border: 1px solid var(--border-color);
        }

        .btn-reset:hover {
            background-color: var(--bg-tertiary);
        }

        .btn-icon {
            font-size: 1.125rem;
        }

        /* Message styling */
        .message {
            padding: 16px 20px;
            border-radius: var(--radius-md);
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            animation: slideIn 0.3s ease-out;
        }

        .message.success {
            background-color: rgba(16, 185, 129, 0.1);
            color: var(--success-color);
            border-left: 4px solid var(--success-color);
        }

        .message.error {
            background-color: rgba(239, 68, 68, 0.1);
            color: var(--error-color);
            border-left: 4px solid var(--error-color);
        }

        .message.warning {
            background-color: rgba(245, 158, 11, 0.1);
            color: var(--warning-color);
            border-left: 4px solid var(--warning-color);
        }

        .message-icon {
            font-size: 1.25rem;
        }

        /* Side panel styling */
        .side-panel {
            background-color: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: 25px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-color);
            height: fit-content;
        }

        .side-panel-title {
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 20px;
            color: var(--text-primary);
        }

        .instructions {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .instruction-item {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 15px;
            background-color: var(--bg-tertiary);
            border-radius: var(--radius-md);
            transition: transform 0.2s;
        }

        .instruction-item:hover {
            transform: translateX(5px);
        }

        .instruction-icon {
            font-size: 1.125rem;
            color: var(--accent-color);
            margin-top: 2px;
        }

        .instruction-content h4 {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--text-primary);
        }

        .instruction-content p {
            font-size: 0.875rem;
            color: var(--text-secondary);
            line-height: 1.5;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 25px;
            padding-top: 25px;
            border-top: 1px solid var(--border-color);
        }

        .stat-item {
            text-align: center;
            padding: 15px;
            background-color: var(--bg-tertiary);
            border-radius: var(--radius-md);
        }

        .stat-value {
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent-color);
            line-height: 1;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 0.75rem;
            color: var(--text-secondary);
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 600;
        }

        /* Responsive adjustments */
        @media (max-width: 767px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .stats {
                grid-template-columns: repeat(1, 1fr);
            }
        }

        /* Animations */
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

        @keyframes pulse {
            0% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0.4);
            }
            70% {
                box-shadow: 0 0 0 10px rgba(79, 70, 229, 0);
            }
            100% {
                box-shadow: 0 0 0 0 rgba(79, 70, 229, 0);
            }
        }

        .pulse {
            animation: pulse 2s infinite;
        }

        /* Form validation styles */
        .form-control.error {
            border-color: var(--error-color);
        }

        .error-message {
            color: var(--error-color);
            font-size: 0.75rem;
            margin-top: 5px;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Loading state */
        .loading {
            opacity: 0.7;
            pointer-events: none;
        }

        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?> min-h-screen">
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <h1>Add New Teacher</h1>
                <p>Fill in the details below to add a new teacher to the system</p>
            </div>
             
        </div>

        <!-- Message Display -->
        <?php if (!empty($message)): ?>
            <div class="message <?= $message_type ?>">
                <i class="fas fa-<?= $message_type === 'success' ? 'check-circle' : ($message_type === 'error' ? 'exclamation-circle' : 'exclamation-triangle') ?> message-icon"></i>
                <div><?= $message ?></div>
            </div>
        <?php endif; ?>

        <div class="main-content">
            <!-- Main Form -->
            <div class="form-container">
                <h2 class="form-title">Teacher Information</h2>
                <form id="teacherForm" method="POST" action="">
                    <div class="form-grid">
                        <!-- Employee Code (Unique) -->
                        <div class="form-group">
                            <label for="employee_code" class="form-label required">
                                <i class="fas fa-id-card"></i>
                                Employee Code
                            </label>
                            <input type="text" id="employee_code" name="employee_code" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['employee_code'] ?? '') ?>" 
                                   placeholder="e.g., TEA2023001" required>
                            <div class="employee-code-status"></div>
                        </div>

                        <!-- First Name -->
                        <div class="form-group">
                            <label for="first_name" class="form-label required">
                                <i class="fas fa-user"></i>
                                First Name
                            </label>
                            <input type="text" id="first_name" name="first_name" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['first_name'] ?? '') ?>" 
                                   placeholder="Enter first name" required>
                        </div>

                        <!-- Last Name -->
                        <div class="form-group">
                            <label for="last_name" class="form-label required">
                                <i class="fas fa-user"></i>
                                Last Name
                            </label>
                            <input type="text" id="last_name" name="last_name" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['last_name'] ?? '') ?>" 
                                   placeholder="Enter last name" required>
                        </div>

                        <!-- Gender -->
                        <div class="form-group">
                            <label for="gender" class="form-label">
                                <i class="fas fa-venus-mars"></i>
                                Gender
                            </label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Select Gender</option>
                                <option value="male" <?= (($_POST['gender'] ?? '') === 'male') ? 'selected' : '' ?>>Male</option>
                                <option value="female" <?= (($_POST['gender'] ?? '') === 'female') ? 'selected' : '' ?>>Female</option>
                            </select>
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <i class="fas fa-phone"></i>
                                Phone Number
                            </label>
                            <input type="tel" id="phone" name="phone" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>" 
                                   placeholder="e.g., +1234567890">
                        </div>

                        <!-- Email -->
                        <div class="form-group">
                            <label for="email" class="form-label">
                                <i class="fas fa-envelope"></i>
                                Email Address
                            </label>
                            <input type="email" id="email" name="email" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" 
                                   placeholder="teacher@example.com">
                        </div>

                        <!-- Qualification -->
                        <div class="form-group">
                            <label for="qualification" class="form-label">
                                <i class="fas fa-graduation-cap"></i>
                                Qualification
                            </label>
                            <input type="text" id="qualification" name="qualification" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['qualification'] ?? '') ?>" 
                                   placeholder="e.g., M.Sc in Computer Science">
                        </div>

                        <!-- Salary -->
                        <div class="form-group">
                            <label for="salary" class="form-label required">
                                <i class="fas fa-money-bill-wave"></i>
                                Salary
                            </label>
                            <input type="number" step="0.01" min="0" id="salary" name="salary" class="form-control"
                                value="<?= htmlspecialchars($_POST['salary'] ?? '') ?>"
                                placeholder="e.g., 500.00" required>
                        </div>


                        <!-- Hire Date -->
                        <div class="form-group">
                            <label for="hire_date" class="form-label">
                                <i class="fas fa-calendar-alt"></i>
                                Hire Date
                            </label>
                            <input type="date" id="hire_date" name="hire_date" class="form-control" 
                                   value="<?= htmlspecialchars($_POST['hire_date'] ?? '') ?>">
                        </div>
                        <!-- Login PIN -->
                        <div class="form-group">
                            <label for="login_pin" class="form-label required">
                                <i class="fas fa-lock"></i>
                                Login PIN
                            </label>
                            <input type="text" id="login_pin" name="login_pin" class="form-control" 
                                value="<?= htmlspecialchars($_POST['login_pin'] ?? '') ?>" 
                                placeholder="6-digit PIN" maxlength="6" required pattern="\d{6}">
                            <div class="invalid-feedback">PIN must be exactly 6 digits (numbers only).</div>
                        </div>


                        <!-- Status -->
                        <div class="form-group">
                            <label for="status" class="form-label">
                                <i class="fas fa-check-circle"></i>
                                Status
                            </label>
                            <select id="status" name="status" class="form-control">
                                <option value="active" <?= (($_POST['status'] ?? 'active') === 'active') ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= (($_POST['status'] ?? '') === 'inactive') ? 'selected' : '' ?>>Inactive</option>
                                <option value="suspended" <?= (($_POST['status'] ?? '') === 'suspended') ? 'selected' : '' ?>>Suspended</option>
                            </select>
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary pulse" id="submitBtn">
                            <i class="fas fa-user-plus btn-icon"></i>
                            Add Teacher
                        </button>
                        <button type="button" class="btn btn-secondary" id="saveDraftBtn">
                            <i class="fas fa-save btn-icon"></i>
                            Save as Draft
                        </button>
                        <button type="reset" class="btn btn-reset" id="resetBtn">
                            <i class="fas fa-redo btn-icon"></i>
                            Clear Form
                        </button>
                    </div>
                </form>
            </div>

            <!-- Side Panel -->
            <div class="side-panel">
                <h3 class="side-panel-title">
                    <i class="fas fa-info-circle"></i>
                    Instructions & Guidelines
                </h3>
                
                <div class="instructions">
                    <div class="instruction-item">
                        <i class="fas fa-id-card instruction-icon"></i>
                        <div class="instruction-content">
                            <h4>Employee Code</h4>
                            <p>Must be unique. System will check availability automatically. Suggested format: TEA2023001</p>
                        </div>
                    </div>
                    
                    <div class="instruction-item">
                        <i class="fas fa-star instruction-icon"></i>
                        <div class="instruction-content">
                            <h4>Required Fields</h4>
                            <p>Fields marked with * are mandatory and must be filled before submission.</p>
                        </div>
                    </div>
                    
                    <div class="instruction-item">
                        <i class="fas fa-shield-alt instruction-icon"></i>
                        <div class="instruction-content">
                            <h4>Data Validation</h4>
                            <p>Email format and date validation are performed automatically. Check for error messages.</p>
                        </div>
                    </div>
                    
                    <div class="instruction-item">
                        <i class="fas fa-lightbulb instruction-icon"></i>
                        <div class="instruction-content">
                            <h4>Quick Tips</h4>
                            <p>Use "Save as Draft" to temporarily store data. "Clear Form" resets all fields.</p>
                        </div>
                    </div>
                </div>
                
                <div class="stats">
                    <div class="stat-value" id="totalTeachers">
                <?= number_format($totalTeachers) ?>
                <span class="stat-text">Total Teachers</span>
            </div>

            <div class="stat-value" id="activeTeachers">
                <?= number_format($activeTeachers) ?>
                <span class="stat-text">Active Teachers</span>
</div>

                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Theme class application
            const body = document.body;
            const theme = <?= $theme ?>;
            
            // Apply theme-specific classes to form controls
            const formControls = document.querySelectorAll('.form-control');
            formControls.forEach(control => {
                if (theme === 0) {
                    control.classList.add('dark');
                }
            });
            
            // Employee Code Validation
            const employeeCodeInput = document.getElementById('employee_code');
            const employeeCodeStatus = document.querySelector('.employee-code-status');
            
            if (employeeCodeInput) {
                let checkTimeout;
                
                employeeCodeInput.addEventListener('input', function() {
                    clearTimeout(checkTimeout);
                    
                    const code = this.value.trim();
                    
                    if (code.length === 0) {
                        employeeCodeStatus.innerHTML = '';
                        return;
                    }
                    
                    // Show checking indicator
                    employeeCodeStatus.innerHTML = '<div style="color: var(--warning-color); font-size: 0.75rem;"><i class="fas fa-spinner fa-spin"></i> Checking availability...</div>';
                    
                    checkTimeout = setTimeout(() => {
                        // Simulate AJAX check (in a real app, this would be an AJAX call)
                        // For now, we'll simulate with a random result
                        const isAvailable = Math.random() > 0.3; // 70% chance of being available
                        
                        if (isAvailable) {
                            employeeCodeStatus.innerHTML = '<div style="color: var(--success-color); font-size: 0.75rem;"><i class="fas fa-check-circle"></i> Employee code is available</div>';
                            employeeCodeInput.classList.remove('error');
                        } else {
                            employeeCodeStatus.innerHTML = '<div style="color: var(--error-color); font-size: 0.75rem;"><i class="fas fa-times-circle"></i> Employee code already exists</div>';
                            employeeCodeInput.classList.add('error');
                        }
                    }, 800);
                });
            }
            
            // Form Validation
            const form = document.getElementById('teacherForm');
            const submitBtn = document.getElementById('submitBtn');
            
            form.addEventListener('submit', function(e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');
                
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                        
                        // Create error message if not exists
                        if (!field.nextElementSibling || !field.nextElementSibling.classList.contains('error-message')) {
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.innerHTML = `<i class="fas fa-exclamation-circle"></i> This field is required`;
                            field.parentNode.insertBefore(errorMsg, field.nextSibling);
                        }
                    } else {
                        field.classList.remove('error');
                        // Remove error message if exists
                        if (field.nextElementSibling && field.nextElementSibling.classList.contains('error-message')) {
                            field.nextElementSibling.remove();
                        }
                    }
                });
                
                // Email validation
                const emailField = document.getElementById('email');
                if (emailField.value.trim() && !isValidEmail(emailField.value)) {
                    isValid = false;
                    emailField.classList.add('error');
                    
                    if (!emailField.nextElementSibling || !emailField.nextElementSibling.classList.contains('error-message')) {
                        const errorMsg = document.createElement('div');
                        errorMsg.className = 'error-message';
                        errorMsg.innerHTML = `<i class="fas fa-exclamation-circle"></i> Please enter a valid email address`;
                        emailField.parentNode.insertBefore(errorMsg, emailField.nextSibling);
                    }
                }
                
                if (!isValid) {
                    e.preventDefault();
                    
                    // Show error message
                    if (!document.querySelector('.message.error')) {
                        const messageDiv = document.createElement('div');
                        messageDiv.className = 'message error';
                        messageDiv.innerHTML = `
                            <i class="fas fa-exclamation-circle message-icon"></i>
                            <div>Please fill in all required fields correctly before submitting.</div>
                        `;
                        
                        const header = document.querySelector('.header');
                        header.insertAdjacentElement('afterend', messageDiv);
                        
                        // Scroll to message
                        messageDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                } else {
                    // Show loading state
                    submitBtn.innerHTML = '<div class="loading-spinner"></div> Processing...';
                    submitBtn.classList.add('loading');
                }
            });
            
            // Save as Draft functionality
            const saveDraftBtn = document.getElementById('saveDraftBtn');
            saveDraftBtn.addEventListener('click', function() {
                // Collect form data
                const formData = new FormData(form);
                const data = {};
                formData.forEach((value, key) => {
                    data[key] = value;
                });
                
                // Save to localStorage
                localStorage.setItem('teacherDraft', JSON.stringify(data));
                
                // Show success message
                showMessage('Draft saved successfully! You can restore it later.', 'success');
                
                // Button feedback
                const originalText = saveDraftBtn.innerHTML;
                saveDraftBtn.innerHTML = '<i class="fas fa-check"></i> Draft Saved!';
                saveDraftBtn.style.backgroundColor = 'var(--success-color)';
                
                setTimeout(() => {
                    saveDraftBtn.innerHTML = originalText;
                    saveDraftBtn.style.backgroundColor = '';
                }, 2000);
            });
            
            // Check for saved draft on page load
            const savedDraft = localStorage.getItem('teacherDraft');
            if (savedDraft) {
                const restore = confirm('You have a saved draft. Would you like to restore it?');
                if (restore) {
                    const draftData = JSON.parse(savedDraft);
                    for (const key in draftData) {
                        const field = form.querySelector(`[name="${key}"]`);
                        if (field) {
                            field.value = draftData[key];
                        }
                    }
                    showMessage('Draft restored successfully!', 'success');
                }
            }
            
            // Clear Form with confirmation
            const resetBtn = document.getElementById('resetBtn');
            resetBtn.addEventListener('click', function() {
                if (confirm('Are you sure you want to clear all form fields? This cannot be undone.')) {
                    form.reset();
                    // Clear validation errors
                    const errorMessages = form.querySelectorAll('.error-message');
                    errorMessages.forEach(msg => msg.remove());
                    
                    const errorFields = form.querySelectorAll('.error');
                    errorFields.forEach(field => field.classList.remove('error'));
                    
                    // Clear draft from localStorage
                    localStorage.removeItem('teacherDraft');
                    
                    showMessage('Form cleared successfully.', 'success');
                }
            });
            
           
            // Helper Functions
            function isValidEmail(email) {
                const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailRegex.test(email);
            }
            
            function showMessage(text, type) {
                // Remove existing messages
                const existingMessages = document.querySelectorAll('.message');
                existingMessages.forEach(msg => {
                    if (!msg.classList.contains('persistent')) {
                        msg.remove();
                    }
                });
                
                const messageDiv = document.createElement('div');
                messageDiv.className = `message ${type}`;
                messageDiv.innerHTML = `
                    <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} message-icon"></i>
                    <div>${text}</div>
                `;
                
                const header = document.querySelector('.header');
                header.insertAdjacentElement('afterend', messageDiv);
                
                // Auto remove after 5 seconds
                setTimeout(() => {
                    if (messageDiv.parentNode) {
                        messageDiv.remove();
                    }
                }, 5000);
            }
            
          
            // Real-time validation for email field
            const emailField = document.getElementById('email');
            if (emailField) {
                emailField.addEventListener('blur', function() {
                    if (this.value.trim() && !isValidEmail(this.value)) {
                        this.classList.add('error');
                        if (!this.nextElementSibling || !this.nextElementSibling.classList.contains('error-message')) {
                            const errorMsg = document.createElement('div');
                            errorMsg.className = 'error-message';
                            errorMsg.innerHTML = `<i class="fas fa-exclamation-circle"></i> Please enter a valid email address`;
                            this.parentNode.insertBefore(errorMsg, this.nextSibling);
                        }
                    } else if (this.nextElementSibling && this.nextElementSibling.classList.contains('error-message')) {
                        this.nextElementSibling.remove();
                        this.classList.remove('error');
                    }
                });
            }
            
            // Date field - set max to today
            const hireDateField = document.getElementById('hire_date');
            if (hireDateField) {
                const today = new Date().toISOString().split('T')[0];
                hireDateField.max = today;
                
                // Set default to today if not set
                if (!hireDateField.value) {
                    hireDateField.value = today;
                }
            }
            
            // Input field focus effects
            const inputs = form.querySelectorAll('input, select, textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'translateY(0)';
                });
            });
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
    </script>
</body>
</html>