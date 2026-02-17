<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Edit Payment',
        'header_title' => 'Edit Payment Record',
        'student_info_label' => 'Student',
        
        // Status Labels
        'status_paid' => 'Paid',
        'status_partial' => 'Partial',
        'status_unpaid' => 'Unpaid',
        
        // Form Labels
        'label_fee_category' => 'Fee Category',
        'label_payment_date' => 'Payment Date',
        'label_original_amount' => 'Original Amount',
        'label_discount_percentage' => 'Discount Percentage',
        'label_discount_amount' => 'Discount Amount',
        'label_paid_amount' => 'Paid Amount',
        'label_discount_reason' => 'Discount Reason (Optional)',
        'placeholder_discount_reason' => 'Enter reason for discount...',
        
        // Summary Labels
        'summary_original' => 'Original Amount',
        'summary_discount' => 'Discount Applied',
        'summary_final' => 'Final Amount',
        'summary_paid' => 'Amount Paid',
        
        // Buttons
        'btn_cancel' => 'Cancel',
        'btn_update' => 'Update Payment',
        
        // Messages
        'invalid_payment_id' => 'Invalid payment ID.',
        'payment_not_found' => 'Payment record not found.',
        'success_updated' => 'Payment updated successfully!',
        'error_discount_exceed' => 'Discount cannot exceed the original amount.',
        'error_paid_exceed' => 'Paid amount cannot exceed the final amount {final}.',
        'error_invalid_date' => 'Invalid payment date.',
        
        // JavaScript Alerts
        'js_discount_exceed' => 'Discount cannot exceed the original amount.',
        'js_paid_exceed' => 'Paid amount cannot exceed the final amount ${final}.',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'تعديل الدفع',
        'header_title' => 'تعديل سجل الدفع',
        'student_info_label' => 'الطالب',
        
        // Status Labels
        'status_paid' => 'مدفوع',
        'status_partial' => 'جزئي',
        'status_unpaid' => 'غير مدفوع',
        
        // Form Labels
        'label_fee_category' => 'فئة الرسوم',
        'label_payment_date' => 'تاريخ الدفع',
        'label_original_amount' => 'المبلغ الأصلي',
        'label_discount_percentage' => 'نسبة الخصم',
        'label_discount_amount' => 'مبلغ الخصم',
        'label_paid_amount' => 'المبلغ المدفوع',
        'label_discount_reason' => 'سبب الخصم (اختياري)',
        'placeholder_discount_reason' => 'أدخل سبب الخصم...',
        
        // Summary Labels
        'summary_original' => 'المبلغ الأصلي',
        'summary_discount' => 'الخصم المطبق',
        'summary_final' => 'المبلغ النهائي',
        'summary_paid' => 'المبلغ المدفوع',
        
        // Buttons
        'btn_cancel' => 'إلغاء',
        'btn_update' => 'تحديث الدفع',
        
        // Messages
        'invalid_payment_id' => 'معرف دفع غير صالح.',
        'payment_not_found' => 'سجل الدفع غير موجود.',
        'success_updated' => 'تم تحديث الدفع بنجاح!',
        'error_discount_exceed' => 'لا يمكن أن يتجاوز الخصم المبلغ الأصلي.',
        'error_paid_exceed' => 'لا يمكن أن يتجاوز المبلغ المدفوع المبلغ النهائي {final}.',
        'error_invalid_date' => 'تاريخ دفع غير صالح.',
        
        // JavaScript Alerts
        'js_discount_exceed' => 'لا يمكن أن يتجاوز الخصم المبلغ الأصلي.',
        'js_paid_exceed' => 'لا يمكن أن يتجاوز المبلغ المدفوع المبلغ النهائي ${final}.',
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

/* ---------- GET PAYMENT ID ---------- */
$payment_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($payment_id <= 0) die(t('invalid_payment_id'));

/* ---------- GET THEME ---------- */
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $row = $theme_res->fetch_assoc()) ? (int)$row['mode'] : 1; // 1 = light, 2 = dark

/* ---------- FETCH PAYMENT ---------- */
$stmt = $conn->prepare("
    SELECT sf.*, cf.amount AS class_fee_amount, fc.name AS fee_category_name, s.name AS student_name
    FROM student_fees sf
    JOIN class_fees cf ON sf.class_fee_id = cf.id
    JOIN fee_categories fc ON cf.fee_category_id = fc.id
    JOIN students s ON sf.student_id = s.id
    WHERE sf.id = ?
    LIMIT 1
");
$stmt->bind_param("i", $payment_id);
$stmt->execute();
$payment = $stmt->get_result()->fetch_assoc();
$stmt->close();
if (!$payment) die(t('payment_not_found'));

/* ---------- HANDLE SUBMIT ---------- */
$success = $error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $original_amount = (float)$_POST['original_amount'];
    $paid_amount = (float)$_POST['paid_amount'];
    $discount_percent = (float)$_POST['discount_percentage'];
    $discount_amount = (float)$_POST['discount_amount'];
    $discount_reason = trim($_POST['discount_reason']);
    $payment_date = $_POST['payment_date'];

    $final_amount = $original_amount - $discount_amount;

    // Validation
    if ($discount_amount > $original_amount) $error = t('error_discount_exceed');
    if ($paid_amount > $final_amount) $error = t('error_paid_exceed', ['final' => $final_amount]);
    if (!$payment_date || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $payment_date)) $error = t('error_invalid_date');

    if (!$error) {
        // Determine status
        if (round($paid_amount,2) >= round($final_amount,2)) $status = 'paid';
        elseif ($paid_amount > 0) $status = 'partial';
        else $status = 'unpaid';

        $upd = $conn->prepare("
            UPDATE student_fees
            SET original_amount=?, paid_amount=?, discount_percentage=?, discount_amount=?,
                discount_reason=?, status=?, payment_date=?
            WHERE id=?
        ");
        $upd->bind_param(
            "ddddsssi",
            $original_amount, $paid_amount, $discount_percent, $discount_amount,
            $discount_reason, $status, $payment_date, $payment_id
        );
        $upd->execute();
        $upd->close();
        $success = t('success_updated');

        // Refresh payment
        $stmt = $conn->prepare("
            SELECT sf.*, cf.amount AS class_fee_amount, fc.name AS fee_category_name, s.name AS student_name
            FROM student_fees sf
            JOIN class_fees cf ON sf.class_fee_id = cf.id
            JOIN fee_categories fc ON cf.fee_category_id = fc.id
            JOIN students s ON sf.student_id = s.id
            WHERE sf.id = ? LIMIT 1
        ");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $payment = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> • <?= htmlspecialchars($payment['student_name']) ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
 
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>
                <i class="fas fa-edit"></i>
                <?= t('header_title') ?>
            </h1>
            <div class="student-info">
                <i class="fas fa-user-graduate"></i>
                <span><?= t('student_info_label') ?>: <?= htmlspecialchars($payment['student_name']) ?></span>
                <?php 
                $status = $payment['status'];
                $statusClass = 'status-' . $status;
                $statusIcon = $status === 'paid' ? 'fa-check-circle' : ($status === 'partial' ? 'fa-clock' : 'fa-exclamation-circle');
                ?>
                <div class="status-badge <?= $statusClass ?>">
                    <i class="fas <?= $statusIcon ?>"></i>
                    <?= t('status_' . $status) ?>
                </div>
            </div>
        </div>

        <div class="content">
            <?php if($error): ?>
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-circle"></i>
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>
            
            <?php if($success): ?>
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <?= htmlspecialchars($success) ?>
                </div>
            <?php endif; ?>

            <form method="post" id="editPaymentForm">
                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-tags"></i> <?= t('label_fee_category') ?></label>
                        <input type="text" value="<?= htmlspecialchars($payment['fee_category_name']) ?>" readonly>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-calendar-day"></i> <?= t('label_payment_date') ?></label>
                        <input type="date" name="payment_date" value="<?= htmlspecialchars($payment['payment_date']) ?>" required>
                    </div>
                </div>

                <div class="amount-summary">
                    <div class="amount-grid">
                        <div class="amount-item">
                            <div class="amount-label"><?= t('summary_original') ?></div>
                            <div class="amount-value">$<span id="original_display"><?= sprintf('%.2f', $payment['original_amount']) ?></span></div>
                        </div>
                        
                        <div class="amount-item">
                            <div class="amount-label"><?= t('summary_discount') ?></div>
                            <div class="amount-value">$<span id="discount_display"><?= sprintf('%.2f', $payment['discount_amount']) ?></span></div>
                        </div>
                        
                        <div class="amount-item">
                            <div class="amount-label"><?= t('summary_final') ?></div>
                            <div class="amount-value final-amount">$<span id="final_display"><?= sprintf('%.2f', $payment['original_amount'] - $payment['discount_amount']) ?></span></div>
                        </div>
                        
                        <div class="amount-item">
                            <div class="amount-label"><?= t('summary_paid') ?></div>
                            <div class="amount-value">$<span id="paid_display"><?= sprintf('%.2f', $payment['paid_amount']) ?></span></div>
                        </div>
                    </div>
                </div>

                <div class="form-grid">
                    <div class="form-group">
                        <label><i class="fas fa-dollar-sign"></i> <?= t('label_original_amount') ?></label>
                        <div class="input-wrapper">
                            <input type="number" step="0.01" id="original_amount" name="original_amount" 
                                   value="<?= sprintf('%.2f', $payment['original_amount']) ?>" readonly>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-percentage"></i> <?= t('label_discount_percentage') ?></label>
                        <div class="input-wrapper">
                            <input type="number" step="0.01" id="discount_percentage" name="discount_percentage" 
                                   value="<?= sprintf('%.2f', $payment['discount_percentage']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-money-bill-wave"></i> <?= t('label_discount_amount') ?></label>
                        <div class="input-wrapper">
                            <input type="number" step="0.01" id="discount_amount" name="discount_amount" 
                                   value="<?= sprintf('%.2f', $payment['discount_amount']) ?>">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label><i class="fas fa-file-invoice-dollar"></i> <?= t('label_paid_amount') ?></label>
                        <div class="input-wrapper">
                            <input type="number" step="0.01" name="paid_amount" id="paid_amount" 
                                   value="<?= sprintf('%.2f', $payment['paid_amount']) ?>" required>
                        </div>
                    </div>
                </div>
                
                <div class="form-group">
                    <label><i class="fas fa-comment-alt"></i> <?= t('label_discount_reason') ?></label>
                    <input type="text" name="discount_reason" placeholder="<?= t('placeholder_discount_reason') ?>" 
                           value="<?= htmlspecialchars($payment['discount_reason']) ?>">
                </div>

                <div class="form-actions">
                    <a href="fees.php?id=<?= $payment['student_id'] ?>" class="btn btn-secondary">
                        <i class="fas fa-times"></i> <?= t('btn_cancel') ?>
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> <?= t('btn_update') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const originalAmount = document.getElementById('original_amount');
        const discountPercent = document.getElementById('discount_percentage');
        const discountAmount = document.getElementById('discount_amount');
        const paidAmount = document.getElementById('paid_amount');
        
        // Display elements
        const originalDisplay = document.getElementById('original_display');
        const discountDisplay = document.getElementById('discount_display');
        const finalDisplay = document.getElementById('final_display');
        const paidDisplay = document.getElementById('paid_display');

        // Update all calculations and displays
        function updateCalculations() {
            const orig = parseFloat(originalAmount.value) || 0;
            const percent = parseFloat(discountPercent.value) || 0;
            const disc = parseFloat(discountAmount.value) || 0;
            const paid = parseFloat(paidAmount.value) || 0;
            
            // If discount % changed, update discount amount
            if (discountPercent === document.activeElement) {
                const newDisc = orig * percent / 100;
                discountAmount.value = newDisc.toFixed(2);
                discountDisplay.textContent = newDisc.toFixed(2);
            }
            
            // If discount amount changed, update discount %
            if (discountAmount === document.activeElement) {
                const newPercent = orig ? (disc / orig) * 100 : 0;
                discountPercent.value = newPercent.toFixed(2);
                discountDisplay.textContent = disc.toFixed(2);
            }
            
            // If original amount changed, recalc everything
            if (originalAmount === document.activeElement) {
                const currentDisc = parseFloat(discountAmount.value) || 0;
                const newPercent = orig ? (currentDisc / orig) * 100 : 0;
                discountPercent.value = newPercent.toFixed(2);
                discountDisplay.textContent = currentDisc.toFixed(2);
            }
            
            // Always update final amount
            const final = Math.max(orig - (parseFloat(discountAmount.value) || 0), 0);
            
            // Update displays
            originalDisplay.textContent = orig.toFixed(2);
            discountDisplay.textContent = (parseFloat(discountAmount.value) || 0).toFixed(2);
            finalDisplay.textContent = final.toFixed(2);
            paidDisplay.textContent = paid.toFixed(2);
            
            // Update paid amount max attribute
            paidAmount.max = final;
        }

        // Add event listeners to all relevant inputs
        [originalAmount, discountPercent, discountAmount, paidAmount].forEach(input => {
            input.addEventListener('input', updateCalculations);
            input.addEventListener('change', updateCalculations);
        });

        // Form validation
        document.getElementById('editPaymentForm').addEventListener('submit', function(e) {
            const orig = parseFloat(originalAmount.value) || 0;
            const disc = parseFloat(discountAmount.value) || 0;
            const paid = parseFloat(paidAmount.value) || 0;
            const final = orig - disc;
            
            if (disc > orig) {
                e.preventDefault();
                alert("<?= t('js_discount_exceed') ?>");
                discountAmount.focus();
                return;
            }
            
            if (paid > final) {
                e.preventDefault();
                alert(`<?= t('js_paid_exceed') ?>`.replace('{final}', final.toFixed(2)));
                paidAmount.focus();
                return;
            }
        });

        // Initialize calculations on page load
        document.addEventListener('DOMContentLoaded', updateCalculations);
    </script>
</body>
</html>
   <style>
        :root {
            /* Light theme variables */
            --light-bg: #f8fafc;
            --light-card: #ffffff;
            --light-text: #1e293b;
            --light-border: #e2e8f0;
            --light-primary: #3b82f6;
            --light-success: #10b981;
            --light-warning: #f59e0b;
            --light-danger: #ef4444;
            --light-hover: #f1f5f9;
            --light-input: #ffffff;
            
            /* Dark theme variables */
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-text: #f1f5f9;
            --dark-border: #334155;
            --dark-primary: #60a5fa;
            --dark-success: #34d399;
            --dark-warning: #fbbf24;
            --dark-danger: #f87171;
            --dark-hover: #334155;
            --dark-input: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        body {
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 1rem;
            line-height: 1.5;
            background: <?= $theme === 1 ? 'var(--light-bg)' : 'var(--dark-bg)' ?>;
            color: <?= $theme === 1 ? 'var(--light-text)' : 'var(--dark-text)' ?>;
        }

        .container {
            width: 100%;
            max-width: 800px;
            background: <?= $theme === 1 ? 'var(--light-card)' : 'var(--dark-card)' ?>;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid <?= $theme === 1 ? 'var(--light-border)' : 'var(--dark-border)' ?>;
        }

        .header {
            padding: 1.5rem 2rem;
            background: <?= $theme === 1 ? 'linear-gradient(135deg, #3b82f6, #1d4ed8)' : 'linear-gradient(135deg, #1e40af, #1e3a8a)' ?>;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header h1 {
            font-size: 1.5rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .header h1 i {
            font-size: 1.25rem;
        }

        .student-info {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 10px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .content {
            padding: 2rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 10px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
        }

        .alert-success {
            background-color: rgba(16, 185, 129, 0.1);
            color: <?= $theme === 1 ? 'var(--light-success)' : 'var(--dark-success)' ?>;
            border-left: 4px solid <?= $theme === 1 ? 'var(--light-success)' : 'var(--dark-success)' ?>;
        }

        .alert-danger {
            background-color: rgba(239, 68, 68, 0.1);
            color: <?= $theme === 1 ? 'var(--light-danger)' : 'var(--dark-danger)' ?>;
            border-left: 4px solid <?= $theme === 1 ? 'var(--light-danger)' : 'var(--dark-danger)' ?>;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $theme === 1 ? '#64748b' : '#94a3b8' ?>;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group label i {
            font-size: 0.875rem;
            width: 16px;
        }

        .input-wrapper {
            position: relative;
        }

        input, select, textarea {
            width: 100%;
            padding: 0.875rem 1rem;
            background: <?= $theme === 1 ? 'var(--light-input)' : 'var(--dark-input)' ?>;
            border: 2px solid <?= $theme === 1 ? 'var(--light-border)' : 'var(--dark-border)' ?>;
            border-radius: 10px;
            font-size: 1rem;
            color: inherit;
            outline: none;
        }

        input:focus, select:focus, textarea:focus {
            border-color: <?= $theme === 1 ? 'var(--light-primary)' : 'var(--dark-primary)' ?>;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        input:read-only {
            background: <?= $theme === 1 ? '#f1f5f9' : '#334155' ?>;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .amount-summary {
            background: <?= $theme === 1 ? '#f8fafc' : '#0f172a' ?>;
            border-radius: 15px;
            padding: 1.5rem;
            margin: 2rem 0;
            border: 1px solid <?= $theme === 1 ? 'var(--light-border)' : 'var(--dark-border)' ?>;
        }

        .amount-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
        }

        .amount-item {
            text-align: center;
            padding: 1rem;
            border-radius: 10px;
            background: <?= $theme === 1 ? 'white' : '#1e293b' ?>;
            border: 1px solid <?= $theme === 1 ? 'var(--light-border)' : 'var(--dark-border)' ?>;
        }

        .amount-label {
            font-size: 0.875rem;
            color: <?= $theme === 1 ? '#64748b' : '#94a3b8' ?>;
            margin-bottom: 0.5rem;
        }

        .amount-value {
            font-size: 1.5rem;
            font-weight: 700;
            color: <?= $theme === 1 ? 'var(--light-primary)' : 'var(--dark-primary)' ?>;
        }

        .final-amount {
            color: <?= $theme === 1 ? 'var(--light-success)' : 'var(--dark-success)' ?> !important;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            justify-content: flex-end;
            padding-top: 1.5rem;
            border-top: 1px solid <?= $theme === 1 ? 'var(--light-border)' : 'var(--dark-border)' ?>;
        }

        .btn {
            padding: 0.875rem 1.75rem;
            border-radius: 10px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-primary {
            background: <?= $theme === 1 ? 'var(--light-primary)' : 'var(--dark-primary)' ?>;
            color: white;
        }

        .btn-primary:hover {
            background: <?= $theme === 1 ? '#2563eb' : '#3b82f6' ?>;
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(59, 130, 246, 0.2);
        }

        .btn-secondary {
            background: <?= $theme === 1 ? '#64748b' : '#475569' ?>;
            color: white;
        }

        .btn-secondary:hover {
            background: <?= $theme === 1 ? '#475569' : '#334155' ?>;
            transform: translateY(-2px);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 600;
        }

        .status-paid {
            background: rgba(16, 185, 129, 0.1);
            color: <?= $theme === 1 ? 'var(--light-success)' : 'var(--dark-success)' ?>;
        }

        .status-partial {
            background: rgba(245, 158, 11, 0.1);
            color: <?= $theme === 1 ? 'var(--light-warning)' : 'var(--dark-warning)' ?>;
        }

        .status-unpaid {
            background: rgba(239, 68, 68, 0.1);
            color: <?= $theme === 1 ? 'var(--light-danger)' : 'var(--dark-danger)' ?>;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                border-radius: 15px;
            }
            
            .header, .content {
                padding: 1.25rem;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            
            .amount-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
        }

        @media (max-width: 480px) {
            .header {
                flex-direction: column;
                text-align: center;
            }
            
            .header h1 {
                font-size: 1.25rem;
            }
            
            .amount-value {
                font-size: 1.25rem;
            }
        }
    </style>