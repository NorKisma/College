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
        // Page Title & Header
        'page_title' => 'Student Fee Management | Premium Dashboard',
        'header_title' => 'Student Fee Management',
        'header_subtitle' => 'Secure Payment Processing',
        'back_to_fees' => 'Back to fees',
        
        // Card Header
        'card_title' => 'Add New Fee Payment',
        
        // Form Labels
        'label_class' => 'CLASS',
        'label_student' => 'STUDENT',
        'label_fee_category' => 'FEE CATEGORY',
        'label_original_fee' => 'ORIGINAL FEE',
        'label_discount' => 'Apply Special Discount',
        'label_discount_percentage' => 'DISCOUNT PERCENTAGE',
        'label_discount_amount' => 'DISCOUNT AMOUNT',
        'label_final_amount' => 'FINAL AMOUNT',
        'label_discount_reason' => 'DISCOUNT REASON',
        'label_paid_amount' => 'PAID AMOUNT',
        'label_payment_date' => 'PAYMENT DATE',
        
        // Tooltips & Placeholders
        'placeholder_original_fee' => 'Will auto-fill based on selection',
        'placeholder_discount_percentage' => 'Enter %',
        'placeholder_discount_reason' => 'E.g., Scholarship, Early Payment, Promotion',
        'placeholder_paid_amount' => 'Enter amount paid',
        'discount_tooltip' => 'Apply discounts for special cases like scholarships, early payments, or promotions.',
        
        // Select Options
        'select_class' => 'Select Class',
        'select_student' => 'Select Student',
        'select_category' => 'Select Category',
        'loading_students' => 'Loading students...',
        'error_loading_students' => 'Error loading students',
        
        // Submit Button
        'submit_button' => 'PROCESS PAYMENT',
        
        // Status Messages
        'success_updated' => 'Payment updated successfully.',
        'success_recorded' => 'Student fee recorded successfully.',
        'error_amount_exceed' => 'Cannot add payment. Total paid exceeds original amount.',
        
        // Alerts
        'alert_select_class' => 'Please select a class first',
        'alert_select_fee_category' => 'Please select a fee category to get the original amount',
    ],
    2 => [ // Arabic
        // Page Title & Header
        'page_title' => 'إدارة رسوم الطلاب | لوحة تحكم مميزة',
        'header_title' => 'إدارة رسوم الطلاب',
        'header_subtitle' => 'معالجة مدفوعات آمنة',
        'back_to_fees' => 'العودة إلى الرسوم',
        
        // Card Header
        'card_title' => 'إضافة دفعة رسوم جديدة',
        
        // Form Labels
        'label_class' => 'الصف',
        'label_student' => 'الطالب',
        'label_fee_category' => 'فئة الرسوم',
        'label_original_fee' => 'الرسوم الأصلية',
        'label_discount' => 'تطبيق خصم خاص',
        'label_discount_percentage' => 'نسبة الخصم',
        'label_discount_amount' => 'مبلغ الخصم',
        'label_final_amount' => 'المبلغ النهائي',
        'label_discount_reason' => 'سبب الخصم',
        'label_paid_amount' => 'المبلغ المدفوع',
        'label_payment_date' => 'تاريخ الدفع',
        
        // Tooltips & Placeholders
        'placeholder_original_fee' => 'سيتم تعبئته تلقائياً بناءً على الاختيار',
        'placeholder_discount_percentage' => 'أدخل النسبة %',
        'placeholder_discount_reason' => 'مثال: منحة دراسية، دفع مبكر، عرض ترويجي',
        'placeholder_paid_amount' => 'أدخل المبلغ المدفوع',
        'discount_tooltip' => 'تطبيق الخصومات للحالات الخاصة مثل المنح الدراسية، الدفع المبكر، أو العروض الترويجية.',
        
        // Select Options
        'select_class' => 'اختر الصف',
        'select_student' => 'اختر الطالب',
        'select_category' => 'اختر الفئة',
        'loading_students' => 'جاري تحميل الطلاب...',
        'error_loading_students' => 'خطأ في تحميل الطلاب',
        
        // Submit Button
        'submit_button' => 'معالجة الدفع',
        
        // Status Messages
        'success_updated' => 'تم تحديث الدفع بنجاح.',
        'success_recorded' => 'تم تسجيل رسوم الطالب بنجاح.',
        'error_amount_exceed' => 'لا يمكن إضافة الدفع. المبلغ الإجمالي المدفوع يتجاوز المبلغ الأصلي.',
        
        // Alerts
        'alert_select_class' => 'الرجاء اختيار صف أولاً',
        'alert_select_fee_category' => 'الرجاء اختيار فئة الرسوم للحصول على المبلغ الأصلي',
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

/* ---------- THEME ---------- */
$themeRow  = $conn->query("SELECT mode FROM theme LIMIT 1")->fetch_assoc();
$lightMode = $themeRow ? (int)$themeRow['mode'] === 1 : true;

/* ---------- DATA ---------- */
$classes = $conn->query("
    SELECT DISTINCT 
        c.id,
        c.name,
        s.id AS section_id,
        s.name AS section
    FROM classes c
    LEFT JOIN sections s ON s.id = c.section_id
    INNER JOIN class_students cs 
        ON cs.class_id = c.id 
       AND cs.is_active = 1
    ORDER BY c.name
");


$categories = $conn->query("SELECT id, name FROM fee_categories ORDER BY name");

$success = $error = '';

/* ---------- SUBMIT ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $student_id      = (int)$_POST['student_id'];
    $class_id        = (int)$_POST['class_id'];
    $fee_category_id = (int)$_POST['fee_category'];
    $payment_date    = $_POST['payment_date'];

    $original = (float)$_POST['original_amount'];
    $paid     = (float)$_POST['paid_amount'];

    $useDiscount = isset($_POST['use_discount']);
    $discPercent = $useDiscount ? (float)$_POST['discount_percentage'] : 0;
    $discReason  = $useDiscount ? trim($_POST['discount_reason']) : null;

    // Calculate discount and final amount
    $discAmount = ($discPercent / 100) * $original;
    $final      = $original - $discAmount;

    /* ---------- PERIOD ---------- */
    $month = (int)date('m', strtotime($payment_date));
    $year  = (int)date('Y', strtotime($payment_date));

    /* ---------- FIND OR CREATE CLASS_FEE ---------- */
    $cf = $conn->prepare("
        SELECT id, amount
        FROM class_fees
        WHERE class_id = ? AND fee_category_id = ? AND month = ? AND year = ?
        LIMIT 1
    ");
    $cf->bind_param("iiii", $class_id, $fee_category_id, $month, $year);
    $cf->execute();
    $cf->store_result();

    if ($cf->num_rows > 0) {
        $cf->bind_result($class_fee_id, $class_fee_amount);
        $cf->fetch();
    } else {
        $insCF = $conn->prepare("
            INSERT INTO class_fees
            (class_id, fee_category_id, amount, month, year)
            VALUES (?,?,?,?,?)
        ");
        $insCF->bind_param("iidii", $class_id, $fee_category_id, $original, $month, $year);
        $insCF->execute();
        $class_fee_id = $insCF->insert_id;
    }

    /* ---------- CHECK EXISTING STUDENT PAYMENT ---------- */
    $stmt = $conn->prepare("
        SELECT id, original_amount, paid_amount
        FROM student_fees
        WHERE student_id = ? AND class_fee_id = ?
        LIMIT 1
    ");
    $stmt->bind_param("ii", $student_id, $class_fee_id);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($sf_id, $existing_original, $existing_paid);
        $stmt->fetch();

        $total_paid = $existing_paid + $paid;

        if ($total_paid > $existing_original) {
            $error = t('error_amount_exceed');
        } else {
            $status = $total_paid < $existing_original ? 'partial' : 'paid';

            $update = $conn->prepare("
                UPDATE student_fees
                SET paid_amount = ?, discount_percentage = ?, discount_amount = ?, discount_reason = ?, status = ?, payment_date = ?
                WHERE id = ?
            ");
            $update->bind_param(
                "ddsdssi",
                $total_paid,
                $discPercent,
                $discAmount,
                $discReason,
                $status,
                $payment_date,
                $sf_id
            );
            $update->execute();

            $success = t('success_updated') . " Total paid: $total_paid.";
            header("Location: add_fees.php?success=" . urlencode($success));
            exit;
        }

    } else {
        // No existing payment, insert new
        $status = $paid < $final ? 'partial' : 'paid';

        $insert = $conn->prepare("
            INSERT INTO student_fees
            (student_id, class_fee_id, original_amount, discount_percentage, discount_amount, discount_reason, paid_amount, status, payment_date)
            VALUES (?,?,?,?,?,?,?,?,?)
        ");
        $insert->bind_param(
            "iidddsdss",
            $student_id,
            $class_fee_id,
            $original,
            $discPercent,
            $discAmount,
            $discReason,
            $paid,
            $status,
            $payment_date
        );
        $insert->execute();

        $success = t('success_recorded');
        header("Location: add_fees.php?success=" . urlencode($success));
        exit;
    }

    if ($error) {
        header("Location: add_fees.php?error=" . urlencode($error));
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
<meta charset="UTF-8">
<title><?= t('page_title') ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;500;600&display=swap" rel="stylesheet">
<!-- Tom Select -->
<link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
<style>
    /* Tom Select Premium Theme Integration */
    .ts-wrapper .ts-control {
        border-radius: 14px;
        border: 2px solid <?= $lightMode ? 'var(--border-light)' : 'var(--border-dark)' ?>;
        background: <?= $lightMode ? 'rgba(255, 255, 255, 0.8)' : 'rgba(30, 41, 59, 0.6)' ?>;
        color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
        padding: 12px 16px;
        font-size: 1rem;
        font-weight: 500;
        box-shadow: none;
        min-height: 56px; /* Match approximate height of premium inputs */
    }
    .ts-wrapper.focus .ts-control {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
    }
    .ts-dropdown {
        border-radius: 14px;
        border: 1px solid <?= $lightMode ? 'var(--border-light)' : 'var(--border-dark)' ?>;
        background: <?= $lightMode ? 'var(--card-light)' : 'var(--card-dark)' ?>;
        color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
        box-shadow: <?= $lightMode ? 'var(--shadow-light)' : 'var(--shadow-dark)' ?>;
        padding: 0;
        overflow: hidden;
    }
    .ts-dropdown .option {
        padding: 12px 16px;
        color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
    }
    .ts-dropdown .option.active, .ts-dropdown .option:hover {
        background: <?= $lightMode ? 'rgba(37, 99, 235, 0.05)' : 'rgba(37, 99, 235, 0.1)' ?>;
        color: var(--primary);
    }
    .ts-control input {
        color: inherit !important;
    }
    .ts-wrapper.single .ts-control:after {
        border-color: #64748b transparent transparent transparent;
    }
</style>
</head>
<body>

<!-- Floating Background Elements -->
<div class="floating-element floating-1"></div>
<div class="floating-element floating-2"></div>

<div class="container">
    <!-- Premium Header -->
    <div class="header">
        <div>
            <h1><?= t('header_title') ?></h1>
            <p style="color: <?= $lightMode ? 'var(--text-secondary-light)' : 'var(--text-secondary-dark)' ?>; margin-top: 8px;">
                • <?= t('header_subtitle') ?>
            </p>
        </div>
        <div class="status-indicator">
            <a href="fees.php" class="date-badge">
                <i class="fas fa-arrow-left"></i>
                <?= t('back_to_fees') ?>
            </a>
        </div>
    </div>

    <!-- Premium Card -->
    <div class="premium-card">
        <div class="card-header">
            <h2><?= t('card_title') ?></h2>
        </div>

        <!-- Status Messages -->
        <?php if($success): ?>
            <div class="status-message success-message">
                <i class="fas fa-check-circle"></i>
                <span><?= $success ?></span>
            </div>
        <?php endif; ?>
        
        <?php if($error): ?>
            <div class="status-message error-message">
                <i class="fas fa-exclamation-circle"></i>
                <span><?= $error ?></span>
            </div>
        <?php endif; ?>

        <form method="POST" id="feeForm">
            <!-- Advanced Grid Layout -->
            <div class="advanced-grid">
                <div class="grid-item">
                    <label for="class_id"><i class="fas fa-school"></i> <?= t('label_class') ?></label>
                    <select class="premium-select" name="class_id" id="class_id" required>
                        <option value=""><?= t('select_class') ?></option>
                        <?php while($c = $classes->fetch_assoc()): ?>
                            <option value="<?= $c['id'] ?>" data-section="<?= $c['section_id'] ?>">
                                <?= $c['name'] ?> • <?= $c['section'] ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid-item">
                    <label for="student_id"><i class="fas fa-user-graduate"></i> <?= t('label_student') ?></label>
                    <select class="premium-select" name="student_id" id="student_id" required>
                        <option value=""><?= t('select_student') ?></option>
                    </select>
                </div>

                <div class="grid-item">
                    <label for="fee_category"><i class="fas fa-tags"></i> <?= t('label_fee_category') ?></label>
                    <select class="premium-select" name="fee_category" id="fee_category" required>
                        <option value=""><?= t('select_category') ?></option>
                        <?php while($cat = $categories->fetch_assoc()): ?>
                            <option value="<?= $cat['id'] ?>"><?= $cat['name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="grid-item">
                    <label for="original_amount"><i class="fas fa-money-bill-wave"></i> <?= t('label_original_fee') ?></label>
                    <input type="number" class="premium-input" id="original_amount" name="original_amount" readonly 
                           placeholder="<?= t('placeholder_original_fee') ?>">
                </div>
            </div>

            <!-- Premium Discount Toggle -->
            <div class="discount-toggle-container">
                <div class="discount-toggle" onclick="toggleDiscount()">
                    <div class="premium-switch">
                        <input type="checkbox" id="use_discount" name="use_discount">
                        <span class="switch-slider"></span>
                    </div>
                    <div class="toggle-label">
                        <i class="fas fa-percentage"></i>
                        <span><?= t('label_discount') ?></span>
                        <div class="tooltip">
                            <i class="fas fa-info-circle" style="color: var(--primary);"></i>
                            <span class="tooltip-text"><?= t('discount_tooltip') ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Discount Box - Premium Layout -->
            <div class="discount-box" id="discountBox">
                <div class="discount-grid">
                    <div class="discount-item">
                        <label for="discount_percentage"><i class="fas fa-percent"></i> <?= t('label_discount_percentage') ?></label>
                        <input type="number" step="0.01" class="premium-input" id="discount_percentage" 
                               name="discount_percentage" placeholder="<?= t('placeholder_discount_percentage') ?>">
                    </div>

                    <div class="discount-item">
                        <label for="discount_amount"><i class="fas fa-calculator"></i> <?= t('label_discount_amount') ?></label>
                        <input type="number" class="premium-input" id="discount_amount" readonly>
                    </div>

                    <div class="discount-item">
                        <label for="final_amount"><i class="fas fa-file-invoice-dollar"></i> <?= t('label_final_amount') ?></label>
                        <input type="number" class="premium-input" id="final_amount" readonly>
                    </div>

                    <div class="discount-item" style="grid-column: span 2;">
                        <label for="discount_reason"><i class="fas fa-sticky-note"></i> <?= t('label_discount_reason') ?></label>
                        <input type="text" class="premium-input" name="discount_reason" 
                               placeholder="<?= t('placeholder_discount_reason') ?>" readonly>
                    </div>
                </div>
            </div>

            <!-- Payment Details -->
            <div class="advanced-grid" style="margin-top: 30px;">
                <div class="grid-item">
                    <label for="paid_amount"><i class="fas fa-credit-card"></i> <?= t('label_paid_amount') ?></label>
                    <input type="number" step="0.01" class="premium-input" name="paid_amount" required 
                           placeholder="<?= t('placeholder_paid_amount') ?>">
                </div>
                <div class="grid-item">
                    <label for="payment_date"><i class="fas fa-calendar-alt"></i> <?= t('label_payment_date') ?></label>
                    <input type="date" class="premium-input" name="payment_date" required 
                           value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <!-- Premium Submit Button -->
            <button type="submit" class="premium-button" id="submitButton">
                <i class="fas fa-paper-plane"></i>
                <span><?= t('submit_button') ?></span>
            </button>

            <!-- Loading Spinner -->
            <div class="loading-spinner" id="loadingSpinner"></div>
        </form>
    </div>
</div>

<script>
// Add language direction support
document.addEventListener('DOMContentLoaded', function() {
    const isRTL = document.documentElement.dir === 'rtl';
    
    // RTL adjustments if needed
    if (isRTL) {
        // You can add any RTL-specific JS adjustments here if needed
    }
});

// DOM Elements
const useDiscount = document.getElementById('use_discount');
const discountBox = document.getElementById('discountBox');
const discountPercent = document.getElementById('discount_percentage');
const discountAmount = document.getElementById('discount_amount');
const finalAmount = document.getElementById('final_amount');
const originalAmount = document.getElementById('original_amount');
const paidAmount = document.querySelector('[name="paid_amount"]');
const submitButton = document.getElementById('submitButton');
const loadingSpinner = document.getElementById('loadingSpinner');
const feeForm = document.getElementById('feeForm');
const studentSelect = document.getElementById('student_id');
const classSelect = document.getElementById('class_id');
const feeCategorySelect = document.getElementById('fee_category');

// ------------------- FUNCTIONS -------------------

// Toggle Discount Box with Animation
function toggleDiscount() {
    useDiscount.checked = !useDiscount.checked;
    discountBox.style.display = useDiscount.checked ? 'block' : 'none';
    
    if (useDiscount.checked) {
        discountBox.style.animation = 'slideDown 0.5s ease forwards';
        setTimeout(() => discountPercent.focus(), 500);
    }
    calculateDiscount();
}

// Calculate Discount Values
function calculateDiscount() {
    const fee = parseFloat(originalAmount.value || 0);
    const percent = parseFloat(discountPercent.value || 0);
    
    const discount = fee * percent / 100;
    const final = fee - discount;
    
    discountAmount.value = discount.toFixed(2);
    finalAmount.value = final.toFixed(2);
    
    if (useDiscount.checked && percent > 0) {
        paidAmount.value = final.toFixed(2);
    }
}

// Fetch Previous Discount for Selected Student & Fee Category
function fetchPreviousDiscount(studentId, feeCategoryId) {
    if (!studentId || !feeCategoryId) return;

    fetch(`get_student_discount.php?student_id=${studentId}&fee_category_id=${feeCategoryId}`)
        .then(res => res.json())
        .then(data => {
    if (data.discount_percentage || data.discount_amount) {
        useDiscount.checked = true;
        discountBox.style.display = 'block';
        discountPercent.value = data.discount_percentage;
        discountAmount.value = parseFloat(data.discount_amount).toFixed(2);
        finalAmount.value = (parseFloat(originalAmount.value) - parseFloat(data.discount_amount)).toFixed(2);
        document.querySelector('[name="discount_reason"]').value = data.reason || '';
    } else {
        useDiscount.checked = false;
        discountBox.style.display = 'none';
        discountPercent.value = '';
        discountAmount.value = '';
        finalAmount.value = '';
        document.querySelector('[name="discount_reason"]').value = '';
    }
})
        .catch(err => console.error('Error fetching previous discount:', err));
}

// ------------------- EVENT LISTENERS -------------------

// Toggle Discount Checkbox
useDiscount.addEventListener('change', function() {
    discountBox.style.display = this.checked ? 'block' : 'none';
    calculateDiscount();
});
discountPercent.addEventListener('input', calculateDiscount);

</script>
<script>
    // Initialize Tom Selects declared globally so we can access them
    let classTom, studentTom, categoryTom;

    document.addEventListener('DOMContentLoaded', function() {
        // Init 3 dropdowns
        classTom = new TomSelect('#class_id', {
            create: false,
            sortField: {field: "text", direction: "asc"},
            placeholder: '<?= t('select_class') ?>'
        });
        
        studentTom = new TomSelect('#student_id', {
            create: false,
            sortField: {field: "text", direction: "asc"},
            placeholder: '<?= t('select_student') ?>',
            valueField: 'value',
            labelField: 'text',
            searchField: 'text'
        });

        categoryTom = new TomSelect('#fee_category', {
            create: false,
            sortField: {field: "text", direction: "asc"},
            placeholder: '<?= t('select_category') ?>'
        });

        // Use Tom Select events instead of native change events where appropriate
        // (Tom Select triggers native change, so native listeners still work, 
        // but we need to update the logic inside them)
    });

// Fetch Students when Class is Selected
classSelect.addEventListener('change', function() {
    const classId = this.value;
    
    // Clear student dropdown
    if(studentTom) {
        studentTom.clear();
        studentTom.clearOptions();
        studentTom.addOption({value: '', text: '<?= t('loading_students') ?>'});
        studentTom.setValue('');
        studentTom.disable();
    }
    
    if (!classId) return;
    
    fetch('get_students.php?class_id=' + classId)
        .then(res => res.json())
        .then(data => {
            if(studentTom) {
                studentTom.enable();
                studentTom.clearOptions();
                studentTom.addOption({value: '', text: '<?= t('select_student') ?>'});
                
                data.forEach(s => {
                    studentTom.addOption({value: s.id, text: s.name});
                });
                studentTom.sync();
            } 
        })
        .catch(err => {
            if(studentTom) {
                studentTom.clearOptions();
                studentTom.addOption({value: '', text: '<?= t('error_loading_students') ?>'});
            }
            console.error(err);
        });
});

// Fetch Original Fee & Previous Discount when Fee Category Changes
feeCategorySelect.addEventListener('change', function() {
    // FIX: Get section from original class select option since Tom Select hides it
    const classId = classSelect.value;
    const selectedClassOption = classSelect.querySelector(`option[value="${classId}"]`);
    
    if (!selectedClassOption) {
        alert('<?= t('alert_select_class') ?>');
        // Reset category if no class selected
        if(categoryTom) categoryTom.clear();
        return;
    }
    
    const sectionId = selectedClassOption.dataset.section;

    fetch(`get_section_fee.php?section_id=${sectionId}&fee_category_id=${this.value}`)
        .then(res => res.json())
        .then(data => {
            originalAmount.value = data.amount || 0;
            calculateDiscount();

            // Fetch previous discount if a student is selected
            if (studentSelect.value) {
                fetchPreviousDiscount(studentSelect.value, this.value);
            }
        })
        .catch(err => {
            console.error('Error fetching fee:', err);
            originalAmount.value = '';
        });
});

// Fetch Previous Discount when Student is Selected
studentSelect.addEventListener('change', function() {
    const studentId = this.value;
    const feeCategoryId = feeCategorySelect.value;
    if (studentId && feeCategoryId) {
        fetchPreviousDiscount(studentId, feeCategoryId);
    }
});

// Form Submission with Loading Animation
feeForm.addEventListener('submit', function(e) {
    if (!originalAmount.value || originalAmount.value <= 0) {
        e.preventDefault();
        alert('<?= t('alert_select_fee_category') ?>');
        return;
    }
    submitButton.style.display = 'none';
    loadingSpinner.style.display = 'block';
    setTimeout(() => {
        loadingSpinner.style.display = 'none';
        submitButton.style.display = 'flex';
    }, 2000);
});

// Recalculate when Original Amount Changes
originalAmount.addEventListener('input', calculateDiscount);

// Initialize Discount Box on Page Load
document.addEventListener('DOMContentLoaded', function() {
    if (useDiscount.checked) {
        discountBox.style.display = 'block';
        calculateDiscount();
    }

    const formElements = document.querySelectorAll('.grid-item');
    formElements.forEach((el, i) => el.style.animationDelay = `${i * 0.1}s`);
});
</script>

</body>
</html>

<style>
            :root {
                /* Light Theme */
                --bg-light: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
                --card-light: rgba(255, 255, 255, 0.95);
                --text-light: #1e293b;
                --text-secondary-light: #64748b;
                --primary: #2563eb;
                --primary-light: #3b82f6;
                --primary-dark: #1d4ed8;
                --success: #10b981;
                --warning: #f59e0b;
                --danger: #ef4444;
                --border-light: rgba(203, 213, 225, 0.3);
                --shadow-light: 0 20px 40px rgba(0, 0, 0, 0.05);
                --shadow-hover: 0 30px 60px rgba(37, 99, 235, 0.1);
                
                /* Dark Theme */
                --bg-dark: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
                --card-dark: rgba(30, 41, 59, 0.9);
                --text-dark: #f1f5f9;
                --text-secondary-dark: #94a3b8;
                --border-dark: rgba(71, 85, 105, 0.4);
                --shadow-dark: 0 20px 40px rgba(0, 0, 0, 0.2);
                
                /* Premium Accents */
                --gold: #fbbf24;
                --platinum: #e5e7eb;
                --glass: rgba(255, 255, 255, 0.1);
                --gradient-primary: linear-gradient(135deg, #2563eb 0%, #4f46e5 100%);
                --gradient-gold: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Inter', sans-serif;
                background: <?= $lightMode ? 'var(--bg-light)' : 'var(--bg-dark)' ?>;
                color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
                min-height: 100vh;
                position: relative;
                overflow-x: hidden;
            }

            /* Premium Background Effects */
            body::before {
                content: '';
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background-image: 
                    radial-gradient(circle at 10% 20%, rgba(37, 99, 235, 0.05) 0%, transparent 20%),
                    radial-gradient(circle at 90% 80%, rgba(251, 191, 36, 0.03) 0%, transparent 20%);
                z-index: -1;
            }

            .container {
                max-width: 1200px;
                margin: 0 auto;
                padding: 30px;
                position: relative;
            }

            /* Header with Premium Design */
            .header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 40px;
                padding-bottom: 20px;
                border-bottom: 2px solid <?= $lightMode ? 'var(--border-light)' : 'var(--border-dark)' ?>;
                position: relative;
            }

            .header::after {
                content: '';
                position: absolute;
                bottom: -2px;
                left: 0;
                width: 150px;
                height: 4px;
                background: var(--gradient-primary);
                border-radius: 0 0 4px 4px;
            }

            .header h1 {
                font-family: 'Playfair Display', serif;
                font-size: 2.5rem;
                font-weight: 600;
                background: <?= $lightMode ? 'var(--gradient-primary)' : 'var(--gradient-primary)' ?>;
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
                position: relative;
                display: inline-block;
            }

            .header h1::after {
                content: '⚡';
                position: absolute;
                right: -35px;
                top: 50%;
                transform: translateY(-50%);
                font-size: 1.8rem;
                color: var(--gold);
            }

            .status-indicator {
                display: flex;
                align-items: center;
                gap: 10px;
                font-size: 0.9rem;
                color: <?= $lightMode ? 'var(--text-secondary-light)' : 'var(--text-secondary-dark)' ?>;
            }

            .status-dot {
                width: 10px;
                height: 10px;
                background: var(--success);
                border-radius: 50%;
                animation: pulse 2s infinite;
            }

            @keyframes pulse {
                0% { opacity: 1; }
                50% { opacity: 0.5; }
                100% { opacity: 1; }
            }

            /* Premium Card Design */
            .premium-card {
                background: <?= $lightMode ? 'var(--card-light)' : 'var(--card-dark)' ?>;
                border-radius: 24px;
                padding: 40px;
                box-shadow: <?= $lightMode ? 'var(--shadow-light)' : 'var(--shadow-dark)' ?>;
                border: 1px solid <?= $lightMode ? 'var(--border-light)' : 'var(--border-dark)' ?>;
                backdrop-filter: blur(20px);
                position: relative;
                overflow: hidden;
                transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            }

            .premium-card:hover {
                transform: translateY(-5px);
                box-shadow: var(--shadow-hover);
            }

            .premium-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 4px;
                background: var(--gradient-primary);
            }

            .card-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 30px;
            }

            .card-header h2 {
                font-size: 1.8rem;
                font-weight: 600;
                color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
                position: relative;
                display: inline-block;
            }

            .card-header h2::before {
                content: '';
                position: absolute;
                bottom: -8px;
                left: 0;
                width: 50px;
                height: 3px;
                background: var(--gradient-gold);
                border-radius: 2px;
            }

            .premium-badge {
                background: var(--gradient-gold);
                color: #1e293b;
                padding: 6px 16px;
                border-radius: 20px;
                font-size: 0.85rem;
                font-weight: 600;
                letter-spacing: 0.5px;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            /* Modern Grid Layout */
            .advanced-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
                gap: 24px;
                margin-bottom: 30px;
            }

            .grid-item {
                position: relative;
            }

            .grid-item label {
                display: block;
                font-size: 0.9rem;
                font-weight: 600;
                margin-bottom: 10px;
                color: <?= $lightMode ? 'var(--text-secondary-light)' : 'var(--text-secondary-dark)' ?>;
                text-transform: uppercase;
                letter-spacing: 0.5px;
            }

            /* Premium Form Controls */
            .premium-select, .premium-input {
                width: 100%;
                padding: 16px 20px;
                border-radius: 14px;
                border: 2px solid <?= $lightMode ? 'var(--border-light)' : 'var(--border-dark)' ?>;
                background: <?= $lightMode ? 'rgba(255, 255, 255, 0.8)' : 'rgba(30, 41, 59, 0.6)' ?>;
                color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
                font-size: 1rem;
                font-weight: 500;
                transition: all 0.3s ease;
                position: relative;
                appearance: none;
                -webkit-appearance: none;
            }

            .premium-select {
                background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' fill='%2364748b' viewBox='0 0 16 16'%3E%3Cpath d='M7.247 11.14L2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z'/%3E%3C/svg%3E");
                background-repeat: no-repeat;
                background-position: calc(100% - 20px) center;
                background-size: 16px;
                padding-right: 50px;
            }

            .premium-select:focus, .premium-input:focus {
                outline: none;
                border-color: var(--primary);
                box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.1);
                transform: translateY(-2px);
            }

            .premium-select option {
                background: <?= $lightMode ? 'white' : '#1e293b' ?>;
                color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
                padding: 10px;
            }

            /* Discount Toggle - Premium Design */
            .discount-toggle-container {
                background: <?= $lightMode ? 'rgba(37, 99, 235, 0.05)' : 'rgba(37, 99, 235, 0.1)' ?>;
                border-radius: 18px;
                padding: 24px;
                margin: 30px 0;
                border: 2px dashed <?= $lightMode ? 'rgba(37, 99, 235, 0.2)' : 'rgba(37, 99, 235, 0.3)' ?>;
                transition: all 0.3s ease;
            }

            .discount-toggle-container:hover {
                border-color: var(--primary);
                background: <?= $lightMode ? 'rgba(37, 99, 235, 0.08)' : 'rgba(37, 99, 235, 0.15)' ?>;
            }

            .discount-toggle {
                display: flex;
                align-items: center;
                gap: 20px;
                cursor: pointer;
                user-select: none;
            }

            .toggle-label {
                font-size: 1.1rem;
                font-weight: 600;
                color: <?= $lightMode ? 'var(--text-light)' : 'var(--text-dark)' ?>;
                display: flex;
                align-items: center;
                gap: 12px;
            }

            .toggle-label i {
                color: var(--gold);
                font-size: 1.2rem;
            }

            /* Premium Switch */
            .premium-switch {
                position: relative;
                width: 68px;
                height: 36px;
            }

            .premium-switch input {
                opacity: 0;
                width: 0;
                height: 0;
            }

            .switch-slider {
                position: absolute;
                cursor: pointer;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: <?= $lightMode ? '#e2e8f0' : '#475569' ?>;
                border-radius: 50px;
                transition: .4s;
            }

            .switch-slider:before {
                position: absolute;
                content: '';
                height: 28px;
                width: 28px;
                left: 4px;
                bottom: 4px;
                background: white;
                border-radius: 50%;
                transition: .4s;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            }

            .premium-switch input:checked + .switch-slider {
                background: var(--gradient-primary);
            }

            .premium-switch input:checked + .switch-slider:before {
                transform: translateX(32px);
            }

            /* Discount Box - Premium Layout */
            .discount-box {
                display: none;
                margin-top: 30px;
                animation: slideDown 0.5s ease forwards;
            }

            @keyframes slideDown {
                from { opacity: 0; transform: translateY(-20px); }
                to { opacity: 1; transform: translateY(0); }
            }

            .discount-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 20px;
                background: <?= $lightMode ? 'rgba(37, 99, 235, 0.05)' : 'rgba(37, 99, 235, 0.1)' ?>;
                border-radius: 20px;
                padding: 30px;
                border: 1px solid <?= $lightMode ? 'rgba(37, 99, 235, 0.1)' : 'rgba(37, 99, 235, 0.2)' ?>;
            }

            .discount-item {
                position: relative;
            }

            .discount-item label {
                font-size: 0.85rem;
                font-weight: 600;
                margin-bottom: 8px;
                color: <?= $lightMode ? 'var(--text-secondary-light)' : 'var(--text-secondary-dark)' ?>;
                display: flex;
                align-items: center;
                gap: 8px;
            }

            .discount-item label i {
                color: var(--gold);
            }

            /* Status Messages */
            .status-message {
                padding: 18px 24px;
                border-radius: 14px;
                margin-bottom: 24px;
                font-weight: 500;
                display: flex;
                align-items: center;
                gap: 14px;
                animation: slideIn 0.5s ease;
                border-left: 4px solid transparent;
            }

            @keyframes slideIn {
                from { transform: translateX(-20px); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }

            .success-message {
                background: rgba(16, 185, 129, 0.1);
                color: var(--success);
                border-left-color: var(--success);
            }

            .error-message {
                background: rgba(239, 68, 68, 0.1);
                color: var(--danger);
                border-left-color: var(--danger);
            }

            /* Premium Button */
            .premium-button {
                background: var(--gradient-primary);
                color: white;
                padding: 18px 40px;
                border: none;
                border-radius: 14px;
                font-size: 1.1rem;
                font-weight: 600;
                cursor: pointer;
                transition: all 0.3s ease;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 12px;
                width: 100%;
                margin-top: 20px;
                letter-spacing: 0.5px;
                position: relative;
                overflow: hidden;
            }

            .premium-button::before {
                content: '';
                position: absolute;
                top: 0;
                left: -100%;
                width: 100%;
                height: 100%;
                background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
                transition: 0.5s;
            }

            .premium-button:hover {
                transform: translateY(-3px);
                box-shadow: 0 20px 40px rgba(37, 99, 235, 0.3);
            }

            .premium-button:hover::before {
                left: 100%;
            }

            .premium-button:active {
                transform: translateY(-1px);
            }

            .premium-button i {
                font-size: 1.2rem;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .container {
                    padding: 20px;
                }
                
                .header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 20px;
                }
                
                .header h1 {
                    font-size: 2rem;
                }
                
                .premium-card {
                    padding: 25px;
                }
                
                .advanced-grid {
                    grid-template-columns: 1fr;
                    gap: 20px;
                }
                
                .discount-grid {
                    grid-template-columns: 1fr;
                }
            }

            @media (max-width: 480px) {
                .header h1 {
                    font-size: 1.7rem;
                }
                
                .premium-card {
                    padding: 20px;
                }
                
                .card-header {
                    flex-direction: column;
                    align-items: flex-start;
                    gap: 15px;
                }
            }

            /* Loading Animation */
            .loading-spinner {
                display: none;
                width: 40px;
                height: 40px;
                border: 3px solid rgba(37, 99, 235, 0.1);
                border-radius: 50%;
                border-top-color: var(--primary);
                animation: spin 1s ease-in-out infinite;
                margin: 20px auto;
            }

            @keyframes spin {
                to { transform: rotate(360deg); }
            }

            /* Floating Elements for Premium Feel */
            .floating-element {
                position: absolute;
                width: 300px;
                height: 300px;
                border-radius: 50%;
                background: radial-gradient(circle, rgba(37, 99, 235, 0.05) 0%, transparent 70%);
                z-index: -1;
                pointer-events: none;
            }

            .floating-1 {
                top: 10%;
                right: 5%;
                animation: float 20s ease-in-out infinite;
            }

            .floating-2 {
                bottom: 10%;
                left: 5%;
                animation: float 25s ease-in-out infinite reverse;
            }

            @keyframes float {
                0%, 100% { transform: translate(0, 0) rotate(0deg); }
                25% { transform: translate(20px, 20px) rotate(5deg); }
                50% { transform: translate(0, 40px) rotate(0deg); }
                75% { transform: translate(-20px, 20px) rotate(-5deg); }
            }

            /* Tooltip */
            .tooltip {
                position: relative;
                display: inline-block;
            }

            .tooltip .tooltip-text {
                visibility: hidden;
                width: 200px;
                background: <?= $lightMode ? '#1e293b' : '#0f172a' ?>;
                color: white;
                text-align: center;
                border-radius: 8px;
                padding: 10px;
                position: absolute;
                z-index: 1;
                bottom: 125%;
                left: 50%;
                transform: translateX(-50%);
                opacity: 0;
                transition: opacity 0.3s;
                font-size: 0.85rem;
                font-weight: normal;
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            }

            .tooltip:hover .tooltip-text {
                visibility: visible;
                opacity: 1;
            }
</style>