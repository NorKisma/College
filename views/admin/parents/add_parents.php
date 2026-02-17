<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    $email = trim($_POST['email']);
    $address = trim($_POST['address']);
    $occupation = trim($_POST['occupation']);
    $pin = trim($_POST['pin']);

    // Validate PIN: exactly 4 digits
    if (!preg_match('/^\d{4}$/', $pin)) {
        die("Error: PIN must be exactly 4 digits (numbers only).");
    }

    if (empty($name) || empty($phone)) {
        die("Error: Name and phone number are required.");
    }

    // Check if the phone exists in students table
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM students WHERE guardian_phone = ?");
    $stmt->bind_param("s", $phone);
    $stmt->execute();
    $res = $stmt->get_result()->fetch_assoc();
    if ($res['total'] == 0) {
        die("Error: No student found with this guardian phone number.");
    }

    // Update all students with this guardian phone to the new email
    $update = $conn->prepare("UPDATE students SET guardian_email = ? WHERE guardian_phone = ?");
    $update->bind_param("ss", $email, $phone);
    $update->execute();

    // Insert parent
    $stmt = $conn->prepare("INSERT INTO parents (name, phone, email, address, occupation, login_pin) 
                            VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $name, $phone, $email, $address, $occupation, $pin);

    if ($stmt->execute()) {
        header("Location: parents.php");
        exit;
    } else {
        die("Error: Failed to add parent. " . $stmt->error);
    }
}
?>





<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Parent | Academic System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #4361ee;
            --primary-dark: #3a56d4;
            --secondary-color: #7209b7;
            --success-color: #06d6a0;
            --info-color: #118ab2;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --light-bg: #f8fafc;
            --dark-bg: #1e293b;
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
            max-width: 600px;
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
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
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
        
        .form-header p {
            opacity: 0.9;
            margin: 8px 0 0 0;
            font-size: 0.95rem;
        }
        
        .form-body {
            padding: 40px;
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #475569;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .form-label .required {
            color: #ef476f;
            font-size: 0.9em;
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
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(67, 97, 238, 0.1);
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
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary-color), var(--primary-dark));
            border: none;
            border-radius: 8px;
            padding: 15px 30px;
            font-weight: 600;
            font-size: 16px;
            transition: var(--transition);
            box-shadow: 0 4px 12px rgba(67, 97, 238, 0.3);
            width: 100%;
            height: 50px;
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
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .form-note {
            background: #f8fafc;
            border-left: 4px solid var(--primary-color);
            padding: 15px;
            border-radius: 4px;
            margin-top: 30px;
            font-size: 0.9rem;
            color: #64748b;
        }
        
        .form-note i {
            color: var(--primary-color);
            margin-right: 8px;
        }
        
        .password-strength {
            height: 4px;
            background: #e2e8f0;
            border-radius: 2px;
            margin-top: 5px;
            overflow: hidden;
            position: relative;
        }
        
        .password-strength-bar {
            height: 100%;
            width: 0;
            background: #ef476f;
            transition: width 0.3s ease;
            border-radius: 2px;
        }
        
        .password-strength-text {
            font-size: 0.8rem;
            color: #94a3b8;
            margin-top: 5px;
        }
        
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
        }
        
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
        }
        
        .form-actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
        }
        
        .btn-secondary {
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
        
        .btn-secondary:hover {
            background: #e2e8f0;
            color: #475569;
        }
        
        /* Animation */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }
        input::-webkit-clear-button {
            display: none;
        }
        input::-ms-clear {
            display: none;
        }

    </style>
</head>
<body>
    <div class="form-container fade-in">
        <div class="glass-card">
            <div class="form-header">
                <h2>
                    <i class="bi bi-person-plus-fill"></i>
                    Add New Parent
                </h2>
                <p>Register a new parent to the academic system</p>
            </div>
            
            <form method="POST" class="form-body">
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-person-circle"></i>
                            Full Name
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-person input-group-icon"></i>
                            <input type="text" name="name" class="form-control" 
                                   placeholder="Enter parent's full name" required>
                        </div>
                    </div>
                    
                   <div class="form-group">
    <label class="form-label">
        <i class="bi bi-telephone"></i>
        Phone Number
        <span class="required">*</span>
    </label>
    <div class="input-group">
        <i class="bi bi-telephone-fill input-group-icon"></i>
        <input list="guardianPhones" name="phone" id="phone" class="form-control" 
               placeholder="Select or type guardian's phone" required>
        <datalist id="guardianPhones">
            <?php foreach($guardian_data as $phone => $email): ?>
                <option value="<?= htmlspecialchars($phone) ?>"></option>
            <?php endforeach; ?>
        </datalist>
    </div>
</div>

<div class="form-group">
    <label class="form-label">
        <i class="bi bi-envelope"></i>
        Email Address
    </label>
    <div class="input-group">
        <i class="bi bi-envelope-fill input-group-icon"></i>
        <input type="email" name="email" id="email" class="form-control" 
               placeholder="parent@example.com">
    </div>
</div>

                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-briefcase"></i>
                            Occupation
                        </label>
                        <div class="input-group">
                            <i class="bi bi-briefcase-fill input-group-icon"></i>
                            <input type="text" name="occupation" class="form-control" 
                                   placeholder="e.g., Engineer, Teacher, Business">
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-geo-alt"></i>
                        Address
                    </label>
                    <div class="input-group">
                        <i class="bi bi-geo-alt-fill input-group-icon"></i>
                        <textarea name="address" class="form-control" 
                                  placeholder="Enter complete address"></textarea>
                    </div>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-shield-lock"></i>
                            Parent PIN
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-lock-fill input-group-icon"></i>
                            <input type="password" name="pin" class="form-control" 
                                   placeholder="4-digit PIN" 
                                   pattern="[0-9]{4}" 
                                   maxlength="4"
                                   required
                                   oninput="updatePasswordStrength(this.value)">
                        </div>
                        <div class="password-strength">
                            <div class="password-strength-bar" id="passwordStrengthBar"></div>
                        </div>
                        <div class="password-strength-text" id="passwordStrengthText">
                            PIN must be 4 digits (0-9)
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label">
                            <i class="bi bi-shield-lock"></i>
                            Confirm PIN
                            <span class="required">*</span>
                        </label>
                        <div class="input-group">
                            <i class="bi bi-lock-fill input-group-icon"></i>
                            <input type="password" class="form-control" 
                                   placeholder="Re-enter 4-digit PIN" 
                                   pattern="[0-9]{4}" 
                                   maxlength="4"
                                   oninput="validatePinMatch()">
                        </div>
                        <div class="password-strength-text" id="pinMatchText"></div>
                    </div>
                </div>
                
                <div class="form-note">
                    <i class="bi bi-info-circle-fill"></i>
                    <strong>Important:</strong> The 4-digit PIN will be used by parents to access their portal. 
                    Please ensure they receive this PIN securely.
                </div>
                
                <div class="form-actions">
                    <a href="parents.php" class="btn-secondary">
                        <i class="bi bi-arrow-left"></i>
                        Back to Parents
                    </a>
                    <button type="submit" class="btn-primary">
                        <i class="bi bi-check-circle-fill"></i>
                        Save Parent
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function updatePasswordStrength(pin) {
            const strengthBar = document.getElementById('passwordStrengthBar');
            const strengthText = document.getElementById('passwordStrengthText');
            
            if (pin.length === 0) {
                strengthBar.style.width = '0%';
                strengthBar.style.background = '#ef476f';
                strengthText.textContent = 'PIN must be 4 digits (0-9)';
                strengthText.style.color = '#94a3b8';
            } else if (pin.length < 4) {
                const percentage = (pin.length / 4) * 100;
                strengthBar.style.width = percentage + '%';
                strengthBar.style.background = '#ffd166';
                strengthText.textContent = 'Too short - ' + pin.length + '/4 digits';
                strengthText.style.color = '#f59e0b';
            } else if (pin.length === 4 && /^\d+$/.test(pin)) {
                strengthBar.style.width = '100%';
                strengthBar.style.background = '#06d6a0';
                strengthText.textContent = '✓ PIN is valid';
                strengthText.style.color = '#06d6a0';
            } else {
                strengthBar.style.width = '100%';
                strengthBar.style.background = '#ef476f';
                strengthText.textContent = '✗ PIN must contain only digits (0-9)';
                strengthText.style.color = '#ef476f';
            }
        }
        
        function validatePinMatch() {
            const pinInput = document.querySelector('input[name="pin"]');
            const confirmPinInput = document.querySelector('input[placeholder="Re-enter 4-digit PIN"]');
            const matchText = document.getElementById('pinMatchText');
            
            if (confirmPinInput.value.length === 0) {
                matchText.textContent = '';
                matchText.style.color = '#94a3b8';
            } else if (confirmPinInput.value === pinInput.value) {
                matchText.textContent = '✓ PINs match';
                matchText.style.color = '#06d6a0';
            } else {
                matchText.textContent = '✗ PINs do not match';
                matchText.style.color = '#ef476f';
            }
        }
        
        // Form validation before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const pinInput = document.querySelector('input[name="pin"]');
            const confirmPinInput = document.querySelector('input[placeholder="Re-enter 4-digit PIN"]');
            
            // Validate PIN format
            if (!/^\d{4}$/.test(pinInput.value)) {
                e.preventDefault();
                alert('Please enter a valid 4-digit PIN containing only numbers.');
                pinInput.focus();
                return false;
            }
            
            // Validate PIN match
            if (pinInput.value !== confirmPinInput.value) {
                e.preventDefault();
                alert('PINs do not match. Please re-enter the same PIN in both fields.');
                confirmPinInput.focus();
                return false;
            }
            
            // Validate phone number (basic validation)
            const phoneInput = document.querySelector('input[name="phone"]');
            if (phoneInput.value.trim().length < 5) {
                e.preventDefault();
                alert('Please enter a valid phone number.');
                phoneInput.focus();
                return false;
            }
            
            return true;
        });
        
       
        
        // Focus on first input
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelector('input[name="name"]').focus();
        });
    </script>
</body>
</html>
<script>
const guardianData = <?= json_encode($guardian_data) ?>;
const phoneInput = document.getElementById('phone');
const dataList = document.getElementById('guardianPhones');
const emailInput = document.getElementById('email');

phoneInput.addEventListener('input', function () {
    const value = this.value.trim();
    dataList.innerHTML = ''; // reset options

    // Only start autocomplete after 2 characters
    if (value.length < 2) {
        emailInput.value = '';
        return;
    }

    // Filter phone numbers starting with typed digits
    Object.keys(guardianData).forEach(phone => {
        if (phone.startsWith(value)) {
            const option = document.createElement('option');
            option.value = phone;
            dataList.appendChild(option);
        }
    });

    // Auto-fill email if exact phone match
    if (guardianData[value]) {
        emailInput.value = guardianData[value];
    } else {
        emailInput.value = '';
    }
});
</script>

