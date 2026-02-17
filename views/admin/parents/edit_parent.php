<?php
require_once __DIR__ . '/../../../config/db.php'; 

$id = intval($_GET['id'] ?? 0);
if (!$id) {
    die("Invalid parent ID.");
}

// Fetch parent
$parent = $conn->query("SELECT * FROM parents WHERE id=$id")->fetch_assoc();
if (!$parent) {
    die("Parent not found.");
}

// Fetch all guardian phones for autocomplete
$phones_res = $conn->query("SELECT DISTINCT guardian_phone FROM students");
$guardian_phones = [];
while ($row = $phones_res->fetch_assoc()) {
    $guardian_phones[] = $row['guardian_phone'];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === "POST") {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $occupation = trim($_POST['occupation']);
    $address = trim($_POST['address']);
    $pin = trim($_POST['pin']);

    // Validate PIN: must be exactly 4 digits
    if (!preg_match('/^\d{4}$/', $pin)) {
        die("Error: PIN must be exactly 4 digits.");
    }

    // Check that phone exists in students table
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM students WHERE guardian_phone=?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $count = $stmt->get_result()->fetch_assoc()['total'];
    if ($count == 0) {
        die("Error: No student found with this guardian phone number.");
    }

    // Update all students with this guardian phone
    $update_students = $conn->prepare("UPDATE students SET guardian_email=? WHERE guardian_phone=?");
    $update_students->bind_param("ss", $email, $phone);
    $update_students->execute();

    // Update parent record
    $update_parent = $conn->prepare("UPDATE parents SET name=?, phone=?, email=?, occupation=?, address=?, login_pin=? WHERE id=?");
    $update_parent->bind_param("ssssssi", $name, $phone, $email, $occupation, $address, $pin, $id);
    if ($update_parent->execute()) {
        header("Location: parents.php");
        exit;
    } else {
        die("Error updating parent: " . $update_parent->error);
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Parent | Academic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --light-bg: #f8fafc;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --card-shadow-hover: 0 15px 40px rgba(0, 0, 0, 0.12);
            --border-radius: 12px;
            --transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }
        
        * {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #e4e8f0 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            color: #334155;
        }
        
        .form-container {
            max-width: 700px;
            width: 100%;
            margin: 0 auto;
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
        
        .form-header {
            background: linear-gradient(135deg, #06d6a0, #059669);
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
        
        .form-header h2 {
            font-weight: 700;
            font-size: 1.8rem;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        
        .parent-info-badge {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 8px 16px;
            font-size: 0.9rem;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            margin-top: 10px;
        }
        
        .form-body {
            padding: 40px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 25px;
            margin-bottom: 30px;
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
        
        .input-group {
            position: relative;
        }
        
        .input-group .form-control {
            padding-left: 45px;
            height: 50px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 15px;
            transition: var(--transition);
            background: white;
        }
        
        .input-group .form-control:focus {
            border-color: var(--success-color);
            box-shadow: 0 0 0 3px rgba(6, 214, 160, 0.1);
        }
        
        .input-group-icon {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            z-index: 4;
        }
        
        textarea.form-control {
            min-height: 100px;
            resize: vertical;
            padding: 15px 15px 15px 45px;
        }
        
        .btn-success {
            background: linear-gradient(135deg, #06d6a0, #059669);
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(6, 214, 160, 0.3);
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(6, 214, 160, 0.4);
            background: linear-gradient(135deg, #059669, #047857);
        }
        
        .btn-success:active {
            transform: translateY(0);
        }
        
        .btn-outline-secondary {
            background: #f1f5f9;
            border: 2px solid #e2e8f0;
            color: #64748b;
            border-radius: 8px;
            padding: 15px 30px;
            font-weight: 600;
            transition: var(--transition);
            width: 100%;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .btn-outline-secondary:hover {
            background: #e2e8f0;
            color: #475569;
        }
        
        .form-actions {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
            margin-top: 30px;
        }
        
        .form-note {
            background: #f8fafc;
            border-left: 4px solid var(--success-color);
            padding: 15px;
            border-radius: 4px;
            margin-top: 25px;
            font-size: 0.9rem;
            color: #64748b;
            grid-column: 1 / -1;
        }
        
        .form-note i {
            color: var(--success-color);
            margin-right: 8px;
        }
        
        .form-section-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #1e293b;
            margin: 30px 0 15px 0;
            padding-bottom: 10px;
            border-bottom: 2px solid #e2e8f0;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .form-section-title:first-child {
            margin-top: 0;
        }
        
        .form-full-width {
            grid-column: 1 / -1;
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
        
        /* Validation styles */
        .is-invalid {
            border-color: #ef476f !important;
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef476f'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23ef476f' stroke='none'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right calc(0.375em + 0.1875rem) center;
            background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
        }
        
        .is-invalid:focus {
            box-shadow: 0 0 0 3px rgba(239, 71, 111, 0.1) !important;
        }
        
        .invalid-feedback {
            display: none;
            width: 100%;
            margin-top: 0.25rem;
            font-size: 0.875em;
            color: #ef476f;
        }
        
        .is-invalid ~ .invalid-feedback {
            display: block;
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
                grid-template-columns: 1fr;
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
    </style>
</head>
<body>
    <div class="form-container fade-in">
        <div class="glass-card">
            <div class="form-header">
                <h2>
                    <i class="bi bi-pencil-square"></i>
                    Edit Parent Information
                </h2>
                <div class="parent-info-badge">
                    <i class="bi bi-person-badge"></i>
                    ID: <?php echo htmlspecialchars($id); ?> | Last Updated: <?php 
                        echo isset($parent['updated_at']) ? date('M d, Y', strtotime($parent['updated_at'])) : 'Today';
                    ?>
                </div>
            </div>
            
            <form method="POST" class="form-body" id="editParentForm">
    <h3 class="form-section-title fade-in-delay-1">
        <i class="bi bi-person-lines-fill"></i>
        Personal Information
    </h3>
    
    <div class="form-grid fade-in-delay-1">
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-person-circle"></i>
                Full Name
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <i class="bi bi-person input-group-icon"></i>
                <input type="text" name="name" class="form-control" 
                       value="<?= htmlspecialchars($parent['name']) ?>"
                       placeholder="Enter parent's full name" required>
            </div>
            <div class="invalid-feedback">Please enter a valid name.</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-telephone"></i>
                Phone Number
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <i class="bi bi-telephone-fill input-group-icon"></i>
                <input list="guardianPhones" name="phone" id="phoneInput" class="form-control" 
                       value="<?= htmlspecialchars($parent['phone']) ?>"
                       placeholder="Select guardian phone" required>
                <datalist id="guardianPhones">
                    <?php foreach ($guardian_phones as $g_phone): ?>
                        <option value="<?= htmlspecialchars($g_phone) ?>"></option>
                    <?php endforeach; ?>
                </datalist>
            </div>
            <div class="invalid-feedback">Please select a valid phone number.</div>
        </div>
    </div>
    
    <h3 class="form-section-title fade-in-delay-2">
        <i class="bi bi-briefcase-fill"></i>
        Professional & Contact Details
    </h3>
    
    <div class="form-grid fade-in-delay-2">
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-envelope"></i>
                Email Address
            </label>
            <div class="input-group">
                <i class="bi bi-envelope-fill input-group-icon"></i>
                <input type="email" name="email" id="emailInput" class="form-control" 
                       value="<?= htmlspecialchars($parent['email']) ?>"
                       placeholder="parent@example.com">
            </div>
            <div class="invalid-feedback">Please enter a valid email address.</div>
        </div>
        
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-briefcase"></i>
                Occupation
            </label>
            <div class="input-group">
                <i class="bi bi-briefcase-fill input-group-icon"></i>
                <input type="text" name="occupation" class="form-control" 
                       value="<?= htmlspecialchars($parent['occupation']) ?>"
                       placeholder="e.g., Engineer, Teacher, Business">
            </div>
        </div>
    </div>
    
    <div class="form-group form-full-width fade-in-delay-2">
        <label class="form-label">
            <i class="bi bi-geo-alt"></i>
            Address
        </label>
        <div class="input-group">
            <i class="bi bi-geo-alt-fill input-group-icon"></i>
            <textarea name="address" class="form-control" 
                      placeholder="Enter complete address"><?= htmlspecialchars($parent['address']) ?></textarea>
        </div>
    </div>

    <div class="form-grid fade-in-delay-2">
        <div class="form-group">
            <label class="form-label">
                <i class="bi bi-shield-lock"></i>
                Parent PIN
                <span class="text-danger">*</span>
            </label>
            <div class="input-group">
                <i class="bi bi-lock-fill input-group-icon"></i>
                <input type="text" name="pin" id="pinInput" class="form-control"
                       maxlength="4" pattern="\d{4}" required
                       value="<?= htmlspecialchars($parent['login_pin']) ?>"
                       placeholder="4-digit PIN">
            </div>
            <div class="invalid-feedback">PIN must be exactly 4 digits.</div>
        </div>
    </div>
    
    <div class="form-note fade-in-delay-2">
        <i class="bi bi-info-circle-fill"></i>
        <strong>Note:</strong> All changes will be saved immediately. The parent will be notified of any important updates to their contact information.
    </div>
    
    <div class="form-actions fade-in-delay-2">
        <a href="parents.php" class="btn-outline-secondary">
            <i class="bi bi-x-circle"></i>
            Cancel
        </a>
        <button type="submit" class="btn-success" id="submitBtn">
            <i class="bi bi-check-circle-fill"></i>
            Update Parent
        </button>
    </div>
</form>

<script>
// Auto-fill email from phone selection
const phoneInput = document.getElementById('phoneInput');
const emailInput = document.getElementById('emailInput');

phoneInput.addEventListener('change', () => {
    const phone = phoneInput.value.trim();
    if (!phone) return;

    fetch('fetch_guardian_email.php?phone=' + encodeURIComponent(phone))
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                emailInput.value = data.email;
            } else {
                emailInput.value = '';
                alert(data.message);
            }
        });
});

// Form validation
document.getElementById('editParentForm').addEventListener('submit', function(e){
    let isValid = true;

    // Validate name
    const name = document.querySelector('input[name="name"]');
    if(!name.value.trim()){ isValid=false; name.classList.add('is-invalid'); }

    // Validate phone
    if(!phoneInput.value.trim()){ isValid=false; phoneInput.classList.add('is-invalid'); }

    // Validate PIN
    const pin = document.getElementById('pinInput');
    if(!/^\d{4}$/.test(pin.value)){ isValid=false; pin.classList.add('is-invalid'); }

    // Validate email format
    if(emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)){
        isValid=false; emailInput.classList.add('is-invalid');
    }

    if(!isValid){
        e.preventDefault();
        return false;
    }
});
</script>

        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('editParentForm');
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
                
                // Validate name
                const nameInput = document.querySelector('input[name="name"]');
                if (!nameInput.value.trim()) {
                    nameInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validate phone
                const phoneValue = phoneInput.value.replace(/\D/g, '');
                if (phoneValue.length < 10) {
                    phoneInput.classList.add('is-invalid');
                    isValid = false;
                }
                
                // Validate email if provided
                const emailInput = document.querySelector('input[name="email"]');
                if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
                    emailInput.classList.add('is-invalid');
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
                
                return true;
            });
            
            // Real-time validation
            const inputs = form.querySelectorAll('input[required], textarea[required]');
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
                document.querySelector('input[name="name"]').focus();
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
                    window.location.href = 'parents.php';
                }
            });
            
            // Add unsaved changes warning
            let formChanged = false;
            const formInputs = form.querySelectorAll('input, textarea, select');
            formInputs.forEach(input => {
                const originalValue = input.value;
                input.addEventListener('input', function() {
                    if (input.value !== originalValue) {
                        formChanged = true;
                    }
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
                }
            });
            
            // Clear unsaved changes flag on submit
            form.addEventListener('submit', function() {
                formChanged = false;
            });
            
            // Cancel button also clears flag
            document.querySelector('.btn-outline-secondary').addEventListener('click', function(e) {
                if (formChanged && !confirm('You have unsaved changes. Are you sure you want to cancel?')) {
                    e.preventDefault();
                } else {
                    formChanged = false;
                }
            });
        });
    </script>
</body>
</html>