<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Academic Calendar Manager',
        'subtitle' => 'Define payable months and holidays for fee calculation',
        'total_months' => 'Months',
        'payable_months' => 'Payable',
        'edit_month' => 'Edit Academic Month',
        'update_month' => 'Update month details',
        'add_month' => 'Add New Month',
        'define_month' => 'Define payable/holiday months',
        'year' => 'Year',
        'enter_year' => 'Enter year',
        'month' => 'Month',
        'select_month' => 'Select Month',
        'january' => 'January',
        'february' => 'February',
        'march' => 'March',
        'april' => 'April',
        'may' => 'May',
        'june' => 'June',
        'july' => 'July',
        'august' => 'August',
        'september' => 'September',
        'october' => 'October',
        'november' => 'November',
        'december' => 'December',
        'payment_status' => 'Payment Status',
        'holiday' => 'Holiday',
        'reason' => 'Reason (Optional)',
        'reason_placeholder' => 'e.g., Ramadan, Summer break, Exams',
        'update' => 'Update Month',
        'add_calendar' => 'Add to Calendar',
        'cancel' => 'Cancel',
        'calendar_entries' => 'Academic Calendar Entries',
        'showing_all' => 'Showing all configured months',
        'entries' => 'entries',
        'status' => 'Status',
        'payable' => 'Payable',
        'reason' => 'Reason',
        'actions' => 'Actions',
        'no_reason' => 'No reason provided',
        'edit' => 'Edit',
        'delete' => 'Delete',
        'confirm_delete' => 'Are you sure you want to delete',
        'calendar_overview' => 'Calendar Overview',
        'no_entries' => 'No Calendar Entries',
        'no_entries_desc' => 'Start by adding months to your academic calendar. Define which months are payable and which are holidays.',
        'add_first_month' => 'Add Your First Month',
        'total_months_stat' => 'Total Months',
        'configured' => 'Configured',
        'payable_months_stat' => 'Payable Months',
        'holiday_months_stat' => 'Holiday Months',
        'no_fees' => 'No fees',
        'processing' => 'Processing...',
        'copy_year' => 'Copy year',
        'copied' => 'Copied to clipboard!',
        'failed' => 'Failed',
        'already_exists' => 'already exists in the calendar.',
        'updated_success' => 'Updated successfully.',
        'inserted_success' => 'Inserted successfully.',
        'database_error' => 'Database error',
        'deleted_success' => 'Deleted successfully.',
        'all_fields_required' => 'Please fill in all required fields',
        'search' => 'Search...'
    ],
    2 => [ // Arabic
        'page_title' => 'مدير التقويم الأكاديمي',
        'subtitle' => 'تحديد الأشهر القابلة للدفع والإجازات لحساب الرسوم',
        'total_months' => 'شهر',
        'payable_months' => 'قابل للدفع',
        'edit_month' => 'تعديل شهر أكاديمي',
        'update_month' => 'تحديث تفاصيل الشهر',
        'add_month' => 'إضافة شهر جديد',
        'define_month' => 'تحديد أشهر الدفع/الإجازات',
        'year' => 'السنة',
        'enter_year' => 'أدخل السنة',
        'month' => 'الشهر',
        'select_month' => 'اختر الشهر',
        'january' => 'يناير',
        'february' => 'فبراير',
        'march' => 'مارس',
        'april' => 'أبريل',
        'may' => 'مايو',
        'june' => 'يونيو',
        'july' => 'يوليو',
        'august' => 'أغسطس',
        'september' => 'سبتمبر',
        'october' => 'أكتوبر',
        'november' => 'نوفمبر',
        'december' => 'ديسمبر',
        'payment_status' => 'حالة الدفع',
        'holiday' => 'إجازة',
        'reason' => 'السبب (اختياري)',
        'reason_placeholder' => 'مثال: رمضان، إجازة الصيف، الامتحانات',
        'update' => 'تحديث الشهر',
        'add_calendar' => 'إضافة إلى التقويم',
        'cancel' => 'إلغاء',
        'calendar_entries' => 'إدخالات التقويم الأكاديمي',
        'showing_all' => 'عرض جميع الأشهر المكونة',
        'entries' => 'إدخالات',
        'status' => 'الحالة',
        'payable' => 'قابل للدفع',
        'reason' => 'السبب',
        'actions' => 'الإجراءات',
        'no_reason' => 'لا يوجد سبب مقدم',
        'edit' => 'تعديل',
        'delete' => 'حذف',
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف',
        'calendar_overview' => 'نظرة عامة على التقويم',
        'no_entries' => 'لا توجد إدخالات في التقويم',
        'no_entries_desc' => 'ابدأ بإضافة أشهر إلى تقويمك الأكاديمي. حدد الأشهر القابلة للدفع والأشهر التي تكون إجازات.',
        'add_first_month' => 'أضف شهرك الأول',
        'total_months_stat' => 'إجمالي الأشهر',
        'configured' => 'مكون',
        'payable_months_stat' => 'الأشهر القابلة للدفع',
        'holiday_months_stat' => 'أشهر الإجازات',
        'no_fees' => 'لا رسوم',
        'processing' => 'جاري المعالجة...',
        'copy_year' => 'نسخ السنة',
        'copied' => 'تم النسخ إلى الحافظة!',
        'failed' => 'فشل',
        'already_exists' => 'موجود بالفعل في التقويم.',
        'updated_success' => 'تم التحديث بنجاح.',
        'inserted_success' => 'تم الإدخال بنجاح.',
        'database_error' => 'خطأ في قاعدة البيانات',
        'deleted_success' => 'تم الحذف بنجاح.',
        'all_fields_required' => 'يرجى ملء جميع الحقول المطلوبة',
        'search' => 'بحث...'
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

/* ---------- THEME ---------- */
$themeRes = $conn->query("SELECT mode FROM theme LIMIT 1");
$themeMode = ($themeRes && $themeRes->num_rows) ? (int)$themeRes->fetch_assoc()['mode'] : 1;
$isDark = $themeMode === 0;

// Modern color palette
$primary = $isDark ? '#8b5cf6' : '#7c3aed';
$secondary = $isDark ? '#10b981' : '#059669';
$danger = $isDark ? '#ef4444' : '#dc2626';
$warning = $isDark ? '#f59e0b' : '#d97706';
$bg = $isDark ? '#0f172a' : '#f8fafc';
$cardBg = $isDark ? '#1e293b' : '#ffffff';
$text = $isDark ? '#f1f5f9' : '#1e293b';
$textMuted = $isDark ? '#94a3b8' : '#64748b';
$border = $isDark ? '#334155' : '#e2e8f0';

/* ---------- HANDLE INSERT / UPDATE ---------- */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $year = (int)$_POST['year'];
    $month = (int)$_POST['month'];
    $is_payable = (int)$_POST['is_payable'];
    $reason = trim($_POST['reason'] ?? '');
    $id = $_POST['id'] ?? null;

    // Check for duplicates (same month & year) if inserting
    if (!$id) {
        $check = $conn->query("SELECT id FROM academic_calendar WHERE year=$year AND month=$month");
        if ($check && $check->num_rows > 0) {
            $monthName = [
                1 => t('january'), 2 => t('february'), 3 => t('march'),
                4 => t('april'), 5 => t('may'), 6 => t('june'),
                7 => t('july'), 8 => t('august'), 9 => t('september'),
                10 => t('october'), 11 => t('november'), 12 => t('december')
            ];
            $_SESSION['error'] = t('failed') . ": " . $monthName[$month] . " $year " . t('already_exists');
            header("Location: academic_year.php");
            exit;
        }
    }

    if ($id) {
        $stmt = $conn->prepare("
            UPDATE academic_calendar 
            SET year=?, month=?, is_payable=?, reason=?
            WHERE id=?
        ");
        $stmt->bind_param("iiisi", $year, $month, $is_payable, $reason, $id);
    } else {
        $stmt = $conn->prepare("
            INSERT INTO academic_calendar (year, month, is_payable, reason)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->bind_param("iiis", $year, $month, $is_payable, $reason);
    }

    if ($stmt->execute()) {
        $_SESSION['success'] = $id ? t('updated_success') : t('inserted_success');
    } else {
        $_SESSION['error'] = t('database_error') . ": " . $stmt->error;
    }

    header("Location: academic_year.php");
    exit;
}

/* ---------- DELETE ---------- */
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM academic_calendar WHERE id = $id");
    $_SESSION['success'] = t('deleted_success');
    header("Location: academic_year.php");
    exit;
}

/* ---------- EDIT MODE ---------- */
$edit = null;
if (isset($_GET['edit'])) {
    $id = (int)$_GET['edit'];
    $edit = $conn->query("SELECT * FROM academic_calendar WHERE id = $id")->fetch_assoc();
}

/* ---------- FETCH DATA ---------- */
$rows = $conn->query("
    SELECT * FROM academic_calendar
    ORDER BY year DESC, month DESC
");
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> | Premium Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="dashboard-header fade-in">
            <div class="header-content">
                <div class="header-text">
                    <h1>📅 <?= t('page_title') ?></h1>
                    <p><?= t('subtitle') ?></p>
                </div>
                <div class="header-stats">
                    <div class="stat-badge">
                        <i class="fas fa-calendar-alt"></i>
                        <?php 
                            $totalMonths = $conn->query("SELECT COUNT(*) as count FROM academic_calendar")->fetch_assoc()['count'];
                            echo number_format($totalMonths) . ' ' . t('total_months');
                        ?>
                    </div>
                    <div class="stat-badge">
                        <i class="fas fa-money-bill-wave"></i>
                        <?php 
                            $payableMonths = $conn->query("SELECT COUNT(*) as count FROM academic_calendar WHERE is_payable = 1")->fetch_assoc()['count'];
                            echo number_format($payableMonths) . ' ' . t('payable_months');
                        ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Layout -->
        <div class="dashboard-layout">
            <!-- Form Section -->
            <div class="form-card slide-in">
                <div class="form-card-header">
                    <div class="form-icon">
                        <i class="fas fa-<?= $edit ? 'edit' : 'plus' ?>"></i>
                    </div>
                    <div>
                        <div class="form-title">
                            <?= $edit ? t('edit_month') : t('add_month') ?>
                        </div>
                        <div class="form-subtitle">
                            <?= $edit ? t('update_month') : t('define_month') ?>
                        </div>
                    </div>
                </div>

                <form method="post" id="academicForm">
                    <?php if ($edit): ?>
                        <input type="hidden" name="id" value="<?= $edit['id'] ?>">
                    <?php endif; ?>

                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar"></i> <?= t('year') ?>
                            </label>
                            <input type="number" 
                                   name="year" 
                                   class="form-control"
                                   required 
                                   min="2000"
                                   max="2100"
                                   value="<?= $edit['year'] ?? date('Y') ?>"
                                   placeholder="<?= t('enter_year') ?>">
                        </div>

                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-calendar-day"></i> <?= t('month') ?>
                            </label>
                            <select name="month" class="form-control" required>
                                <option value=""><?= t('select_month') ?></option>
                                <?php
                                $months = [
                                    1 => t('january'), 2 => t('february'), 3 => t('march'),
                                    4 => t('april'), 5 => t('may'), 6 => t('june'),
                                    7 => t('july'), 8 => t('august'), 9 => t('september'),
                                    10 => t('october'), 11 => t('november'), 12 => t('december')
                                ];
                                foreach ($months as $num => $name):
                                    $selected = ($edit['month'] ?? '') == $num ? 'selected' : '';
                                ?>
                                    <option value="<?= $num ?>" <?= $selected ?>>
                                        <?= $name ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-cash-register"></i> <?= t('payment_status') ?>
                        </label>
                        <div style="display: flex; gap: 15px; margin-top: 10px;">
                            <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                <input type="radio" checked
                                       name="is_payable" 
                                       value="0"
                                       <?= (isset($edit) && $edit['is_payable'] == 0) ? 'checked' : '' ?>
                                       style="accent-color: var(--danger);">
                                <span style="color: var(--danger); font-weight: 600;">
                                    <i class="fas fa-ban"></i> <?= t('holiday') ?>
                                </span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">
                            <i class="fas fa-sticky-note"></i> <?= t('reason') ?>
                        </label>
                        <input type="text" 
                               name="reason" 
                               class="form-control"
                               value="<?= htmlspecialchars($edit['reason'] ?? '') ?>"
                               placeholder="<?= t('reason_placeholder') ?>">
                    </div>

                    <div class="btn-group">
                        <button type="submit" class="btn btn-primary" style="flex: 1;">
                            <i class="fas fa-<?= $edit ? 'save' : 'plus' ?>"></i>
                            <?= $edit ? t('update') : t('add_calendar') ?>
                        </button>
                        
                        <?php if ($edit): ?>
                            <a href="academic_year.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> <?= t('cancel') ?>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Table Section -->
            <div class="table-card fade-in">
                <div class="table-header">
                    <div>
                        <h2>
                            <i class="fas fa-list-alt"></i>
                            <?= t('calendar_entries') ?>
                        </h2>
                        <div class="table-info">
                            <?= t('showing_all') ?>
                        </div>
                    </div>
                    
                    <div style="display: flex; gap: 10px; align-items: center;">
                        <div class="stat-badge" style="background: var(--bg); color: var(--text); border-color: var(--border);">
                            <i class="fas fa-filter"></i>
                            <span id="filterCount"><?= t('all') ?></span>
                        </div>
                    </div>
                </div>

                <?php if ($rows->num_rows > 0): ?>
                    <div class="table-container">
                        <table class="modern-table">
                            <thead>
                                <tr>
                                    <th><?= t('month') ?></th>
                                    <th><?= t('year') ?></th>
                                    <th><?= t('status') ?></th>
                                    <th><?= t('reason') ?></th>
                                    <th><?= t('actions') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $payableCount = 0;
                                $holidayCount = 0;
                                $monthAbbr = [
                                    'Jan' => t('january'), 'Feb' => t('february'), 'Mar' => t('march'),
                                    'Apr' => t('april'), 'May' => t('may'), 'Jun' => t('june'),
                                    'Jul' => t('july'), 'Aug' => t('august'), 'Sep' => t('september'),
                                    'Oct' => t('october'), 'Nov' => t('november'), 'Dec' => t('december')
                                ];
                                while ($r = $rows->fetch_assoc()):
                                    $payableCount += $r['is_payable'];
                                    $holidayCount += !$r['is_payable'];
                                ?>
                                    <tr>
                                        <td>
                                            <div class="month-display">
                                                <div class="month-icon">
                                                    <?= substr($monthAbbr[date('M', mktime(0,0,0,$r['month'],1))], 0, 3) ?>
                                                </div>
                                                <span style="font-weight: 600;">
                                                    <?= $monthAbbr[date('M', mktime(0,0,0,$r['month'],1))] ?>
                                                </span>
                                            </div>
                                        </td>
                                        <td>
                                            <div style="font-size: 1.1rem; font-weight: 700; color: var(--primary);">
                                                <?= $r['year'] ?>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="status-badge <?= $r['is_payable'] ? 'status-payable' : 'status-holiday' ?>">
                                                <i class="fas fa-<?= $r['is_payable'] ? 'check-circle' : 'ban' ?>"></i>
                                                <?= $r['is_payable'] ? t('payable') : t('holiday') ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($r['reason'])): ?>
                                                <div style="max-width: 200px;">
                                                    <span style="color: var(--text);"><?= htmlspecialchars($r['reason']) ?></span>
                                                </div>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted); font-style: italic;"><?= t('no_reason') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="?edit=<?= $r['id'] ?>" 
                                                   class="action-btn action-edit"
                                                   data-tooltip="<?= t('edit') ?>">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <a href="?delete=<?= $r['id'] ?>" 
                                                   class="action-btn action-delete"
                                                   data-tooltip="<?= t('delete') ?>"
                                                   onclick="return confirm('<?= t('confirm_delete') ?> <?= $monthAbbr[date('M', mktime(0,0,0,$r['month'],1))] ?> <?= $r['year'] ?>?')">
                                                    <i class="fas fa-trash"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Calendar View -->
                    <div style="padding: 30px; border-top: 1px solid var(--border);">
                        <h3 style="margin-bottom: 20px; display: flex; align-items: center; gap: 10px;">
                            <i class="fas fa-calendar-week"></i>
                            <?= t('calendar_overview') ?>
                        </h3>
                        
                        <div class="calendar-grid">
                            <?php 
                            // Reset pointer and fetch again
                            $rows->data_seek(0);
                            while ($r = $rows->fetch_assoc()):
                            ?>
                                <div class="calendar-month <?= $r['is_payable'] ? 'payable' : 'holiday' ?>">
                                    <div class="calendar-header">
                                        <div class="calendar-month-name">
                                            <?= substr($monthAbbr[date('M', mktime(0,0,0,$r['month'],1))], 0, 3) ?>
                                        </div>
                                        <div class="calendar-year">
                                            <?= $r['year'] ?>
                                        </div>
                                    </div>
                                    <div style="margin-top: 10px;">
                                        <span class="status-badge <?= $r['is_payable'] ? 'status-payable' : 'status-holiday' ?>" style="font-size: 0.75rem; padding: 4px 8px;">
                                            <?= $r['is_payable'] ? '💰 ' . t('payable') : '🎉 ' . t('holiday') ?>
                                        </span>
                                        <?php if (!empty($r['reason'])): ?>
                                            <div style="margin-top: 8px; font-size: 0.85rem; color: var(--text-muted);">
                                                <?= htmlspecialchars($r['reason']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-calendar-times"></i>
                        </div>
                        <h3 style="margin-bottom: 10px; color: var(--text);"><?= t('no_entries') ?></h3>
                        <p style="color: var(--text-muted); max-width: 400px; margin: 0 auto;">
                            <?= t('no_entries_desc') ?>
                        </p>
                        <div style="margin-top: 20px;">
                            <div class="btn btn-primary" onclick="document.getElementById('academicForm').scrollIntoView({behavior: 'smooth'})">
                                <i class="fas fa-plus"></i> <?= t('add_first_month') ?>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Summary Stats -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-top: 30px;">
            <div style="background: linear-gradient(135deg, var(--primary), var(--secondary)); color: white; padding: 25px; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;"><?= t('total_months_stat') ?></div>
                <div style="font-size: 2.5rem; font-weight: 800;"><?= number_format($totalMonths) ?></div>
                <div style="font-size: 0.85rem; opacity: 0.8; margin-top: 5px;"><?= t('configured') ?></div>
            </div>
            
            <div style="background: linear-gradient(135deg, var(--danger), #f87171); color: white; padding: 25px; border-radius: var(--radius);">
                <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 5px;"><?= t('holiday_months_stat') ?></div>
                <div style="font-size: 2.5rem; font-weight: 800;"><?= number_format($holidayCount) ?></div>
                <div style="font-size: 0.85rem; opacity: 0.8; margin-top: 5px;"><?= t('no_fees') ?></div>
            </div>
        </div>
    </div>

    <script>
        // Enhanced interactivity
        document.addEventListener('DOMContentLoaded', function() {
            // Update filter count
            const filterCount = document.getElementById('filterCount');
            if (filterCount) {
                const tableRows = document.querySelectorAll('.modern-table tbody tr');
                filterCount.textContent = `${tableRows.length} <?= t('entries') ?>`;
            }

            // Form validation
            const form = document.getElementById('academicForm');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const year = this.querySelector('input[name="year"]').value;
                    const month = this.querySelector('select[name="month"]').value;
                    
                    if (!year || !month) {
                        e.preventDefault();
                        alert('<?= t('all_fields_required') ?>');
                        return false;
                    }
                    
                    // Show loading state
                    const submitBtn = this.querySelector('button[type="submit"]');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= t('processing') ?>';
                    submitBtn.disabled = true;
                    
                    // Re-enable after 3 seconds if submission fails
                    setTimeout(() => {
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    }, 3000);
                });
            }

            // Status badge animation
            const statusBadges = document.querySelectorAll('.status-badge');
            statusBadges.forEach(badge => {
                badge.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.05)';
                });
                badge.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });

            // Calendar month hover effects
            const calendarMonths = document.querySelectorAll('.calendar-month');
            calendarMonths.forEach(month => {
                month.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-8px)';
                });
                month.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(-4px)';
                });
            });

            // Auto-fill current month/year on add mode
            if (!<?= $edit ? 'true' : 'false' ?>) {
                const today = new Date();
                const currentYear = today.getFullYear();
                const currentMonth = today.getMonth() + 1;
                
                // Only auto-fill if not already set
                const yearInput = document.querySelector('input[name="year"]');
                const monthSelect = document.querySelector('select[name="month"]');
                
                if (yearInput && !yearInput.value) {
                    yearInput.value = currentYear;
                }
                
                if (monthSelect && !monthSelect.value) {
                    monthSelect.value = currentMonth;
                }
            }

            // Keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl + N for new entry
                if (e.ctrlKey && e.key === 'n') {
                    e.preventDefault();
                    window.location.href = 'academic_year.php';
                }
                // Escape to cancel edit
                if (e.key === 'Escape' && <?= $edit ? 'true' : 'false' ?>) {
                    window.location.href = 'academic_year.php';
                }
            });

            // Highlight current month in calendar
            const today = new Date();
            const currentMonthYear = today.getFullYear() + '-' + (today.getMonth() + 1);
            
            calendarMonths.forEach(month => {
                const monthText = month.querySelector('.calendar-month-name').textContent;
                const yearText = month.querySelector('.calendar-year').textContent;
                
                if (yearText == today.getFullYear() && 
                    monthText == today.toLocaleString('default', { month: 'short' })) {
                    month.style.boxShadow = '0 0 0 3px rgba(139, 92, 246, 0.5)';
                    month.style.border = '2px solid var(--primary)';
                }
            });

            // Smooth scrolling for anchor links
            document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                anchor.addEventListener('click', function(e) {
                    e.preventDefault();
                    const target = document.querySelector(this.getAttribute('href'));
                    if (target) {
                        target.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start'
                        });
                    }
                });
            });

            // Copy current month/year to clipboard
            const copyToClipboard = (text) => {
                navigator.clipboard.writeText(text).then(() => {
                    // Show success message
                    const toast = document.createElement('div');
                    toast.textContent = '<?= t('copied') ?>';
                    toast.style.position = 'fixed';
                    toast.style.bottom = '20px';
                    toast.style.right = '20px';
                    toast.style.background = 'var(--success)';
                    toast.style.color = 'white';
                    toast.style.padding = '10px 20px';
                    toast.style.borderRadius = 'var(--radius-sm)';
                    toast.style.zIndex = '10000';
                    document.body.appendChild(toast);
                    
                    setTimeout(() => toast.remove(), 3000);
                });
            };

            // Add copy buttons to year cells
            document.querySelectorAll('.modern-table td:nth-child(2)').forEach(cell => {
                const year = cell.textContent.trim();
                const copyBtn = document.createElement('button');
                copyBtn.innerHTML = '<i class="far fa-copy"></i>';
                copyBtn.style.cssText = `
                    background: transparent;
                    border: none;
                    color: var(--text-muted);
                    cursor: pointer;
                    margin-left: 8px;
                    opacity: 0.6;
                    transition: opacity 0.3s;
                `;
                copyBtn.title = '<?= t('copy_year') ?>';
                copyBtn.onclick = (e) => {
                    e.stopPropagation();
                    copyToClipboard(year);
                };
                copyBtn.onmouseenter = () => copyBtn.style.opacity = '1';
                copyBtn.onmouseleave = () => copyBtn.style.opacity = '0.6';
                
                cell.style.display = 'flex';
                cell.style.alignItems = 'center';
                cell.appendChild(copyBtn);
            });
        });

        // Chart-like progress indicator
        function updateProgressIndicators() {
            const total = <?= $totalMonths ?: 1 ?>;
            const payable = <?= $payableCount ?>;
            const holiday = <?= $holidayCount ?>;
            
            const payablePercent = Math.round((payable / total) * 100);
            const holidayPercent = Math.round((holiday / total) * 100);
            
            // Update visual indicators if elements exist
            const payableEl = document.querySelector('[data-progress="payable"]');
            const holidayEl = document.querySelector('[data-progress="holiday"]');
            
            if (payableEl) {
                payableEl.style.width = payablePercent + '%';
            }
            if (holidayEl) {
                holidayEl.style.width = holidayPercent + '%';
            }
        }
        
        // Initialize progress indicators
        window.addEventListener('load', updateProgressIndicators);
    </script>
</body>
</html>

<style>
        :root {
            --primary: <?= $primary ?>;
            --secondary: <?= $secondary ?>;
            --danger: <?= $danger ?>;
            --warning: <?= $warning ?>;
            --bg: <?= $bg ?>;
            --card-bg: <?= $cardBg ?>;
            --text: <?= $text ?>;
            --text-muted: <?= $textMuted ?>;
            --border: <?= $border ?>;
            --success: <?= $isDark ? '#22c55e' : '#16a34a' ?>;
            --radius: 16px;
            --radius-sm: 8px;
            --shadow: <?= $isDark ? '0 4px 6px -1px rgba(0, 0, 0, 0.5)' : '0 4px 6px -1px rgba(0, 0, 0, 0.1)' ?>;
            --shadow-lg: <?= $isDark ? '0 10px 25px -5px rgba(0, 0, 0, 0.5)' : '0 10px 25px -5px rgba(0, 0, 0, 0.1)' ?>;
            --shadow-inner: <?= $isDark ? 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.5)' : 'inset 0 2px 4px 0 rgba(0, 0, 0, 0.06)' ?>;
            --glass: <?= $isDark ? 'rgba(30, 41, 59, 0.7)' : 'rgba(255, 255, 255, 0.7)' ?>;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            padding: 0;
            overflow-x: hidden;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        /* Header */
        .dashboard-header {
            background: linear-gradient(135deg, var(--primary), #a78bfa);
            padding: 40px;
            border-radius: var(--radius);
            margin-bottom: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-lg);
        }

        .dashboard-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        }

        .header-content {
            position: relative;
            z-index: 2;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header-text h1 {
            font-size: 2.5rem;
            font-weight: 800;
            color: white;
            margin-bottom: 8px;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .header-text p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 1.1rem;
        }

        .header-stats {
            display: flex;
            gap: 20px;
        }

        .stat-badge {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            padding: 10px 20px;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Layout */
        .dashboard-layout {
            display: grid;
            grid-template-columns: 350px 1fr;
            gap: 30px;
            margin-bottom: 40px;
        }

        @media (max-width: 1024px) {
            .dashboard-layout {
                grid-template-columns: 1fr;
            }
        }

        /* Form Card */
        .form-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            padding: 30px;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
            position: sticky;
            top: 20px;
            height: fit-content;
        }

        .form-card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 25px;
            padding-bottom: 15px;
            border-bottom: 2px solid var(--border);
        }

        .form-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.5rem;
        }

        .form-title {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .form-subtitle {
            color: var(--text-muted);
            font-size: 0.9rem;
            margin-top: 4px;
        }

        /* Form Elements */
        .form-group {
            margin-bottom: 20px;
        }

        .form-label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text);
            font-size: 0.95rem;
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            background: var(--bg);
            border: 2px solid var(--border);
            border-radius: var(--radius-sm);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
            background: var(--card-bg);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 14px 24px;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            position: relative;
            overflow: hidden;
        }

        .btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(139, 92, 246, 0.4);
        }

        .btn-secondary {
            background: var(--bg);
            border: 2px solid var(--border);
            color: var(--text);
        }

        .btn-secondary:hover {
            border-color: var(--primary);
            color: var(--primary);
            transform: translateY(-2px);
        }

        .btn-danger {
            background: linear-gradient(135deg, var(--danger), #f87171);
            color: white;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
        }

        .btn-danger:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
        }

        .btn-success {
            background: linear-gradient(135deg, var(--success), #34d399);
            color: white;
            box-shadow: 0 4px 12px rgba(34, 197, 94, 0.3);
        }

        .btn-success:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(34, 197, 94, 0.4);
        }

        .btn-group {
            display: flex;
            gap: 12px;
            margin-top: 30px;
        }

        /* Table Card */
        .table-card {
            background: var(--card-bg);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            border: 1px solid var(--border);
        }

        .table-header {
            padding: 25px 30px;
            border-bottom: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-header h2 {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .table-info {
            color: var(--text-muted);
            font-size: 0.9rem;
        }

        .table-container {
            overflow-x: auto;
        }

        /* Table */
        .modern-table {
            width: 100%;
            min-width: 800px;
            border-collapse: collapse;
        }

        .modern-table thead {
            background: linear-gradient(135deg, var(--bg), color-mix(in srgb, var(--bg) 90%, var(--primary)));
        }

        .modern-table th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: var(--text);
            font-size: 0.85rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 2px solid var(--border);
            position: relative;
        }

        .modern-table th:after {
            content: '';
            position: absolute;
            right: 0;
            top: 25%;
            height: 50%;
            width: 1px;
            background: var(--border);
        }

        .modern-table th:last-child:after {
            display: none;
        }

        .modern-table tbody tr {
            border-bottom: 1px solid var(--border);
            transition: all 0.3s ease;
        }

        .modern-table tbody tr:hover {
            background: <?= $isDark ? 'rgba(139, 92, 246, 0.05)' : 'rgba(139, 92, 246, 0.02)' ?>;
        }

        .modern-table td {
            padding: 20px;
            color: var(--text);
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 8px 16px;
            border-radius: 50px;
            font-size: 0.85rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }

        .status-payable {
            background: linear-gradient(135deg, rgba(34, 197, 94, 0.1), rgba(34, 197, 94, 0.2));
            color: var(--success);
            border: 1px solid rgba(34, 197, 94, 0.3);
        }

        .status-holiday {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.1), rgba(239, 68, 68, 0.2));
            color: var(--danger);
            border: 1px solid rgba(239, 68, 68, 0.3);
        }

        /* Month Display */
        .month-display {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .month-icon {
            width: 36px;
            height: 36px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.9rem;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            width: 36px;
            height: 36px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: var(--card-bg);
            color: var(--text);
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        .action-edit:hover {
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }

        .action-delete:hover {
            background: var(--danger);
            color: white;
            border-color: var(--danger);
        }

        /* Calendar View */
        .calendar-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            margin-top: 30px;
        }

        .calendar-month {
            background: var(--card-bg);
            border-radius: var(--radius-sm);
            padding: 20px;
            border: 2px solid var(--border);
            transition: all 0.3s ease;
        }

        .calendar-month.payable {
            border-color: var(--success);
            background: linear-gradient(135deg, var(--card-bg), color-mix(in srgb, var(--card-bg) 95%, var(--success)));
        }

        .calendar-month.holiday {
            border-color: var(--danger);
            background: linear-gradient(135deg, var(--card-bg), color-mix(in srgb, var(--card-bg) 95%, var(--danger)));
        }

        .calendar-month:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-lg);
        }

        .calendar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .calendar-month-name {
            font-weight: 600;
            font-size: 1rem;
        }

        .calendar-year {
            color: var(--text-muted);
            font-size: 0.85rem;
        }

        /* Empty State */
        .empty-state {
            padding: 60px 20px;
            text-align: center;
            color: var(--text-muted);
        }

        .empty-icon {
            font-size: 3rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        /* Animations */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .fade-in {
            animation: fadeIn 0.5s ease-out;
        }

        .slide-in {
            animation: slideIn 0.5s ease-out;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }
            
            .dashboard-header {
                padding: 25px;
                text-align: center;
            }
            
            .header-content {
                flex-direction: column;
                text-align: center;
            }
            
            .header-stats {
                justify-content: center;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .btn-group {
                flex-direction: column;
            }
            
            .table-header {
                flex-direction: column;
                align-items: stretch;
            }
            
            .calendar-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }

        @media (max-width: 480px) {
            .header-text h1 {
                font-size: 2rem;
            }
            
            .form-card {
                padding: 20px;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .calendar-grid {
                grid-template-columns: 1fr 1fr;
            }
        }

        /* Glass Effect */
        .glass {
            background: var(--glass);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        /* Tooltip */
        [data-tooltip] {
            position: relative;
        }

        [data-tooltip]:before {
            content: attr(data-tooltip);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            padding: 6px 12px;
            background: var(--text);
            color: var(--bg);
            border-radius: 6px;
            font-size: 0.8rem;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        [data-tooltip]:hover:before {
            opacity: 1;
            visibility: visible;
            bottom: calc(100% + 8px);
        }

        /* Loading Skeleton */
        .skeleton {
            background: linear-gradient(
                90deg,
                <?= $isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' ?>,
                <?= $isDark ? 'rgba(255, 255, 255, 0.1)' : 'rgba(0, 0, 0, 0.1)' ?>,
                <?= $isDark ? 'rgba(255, 255, 255, 0.05)' : 'rgba(0, 0, 0, 0.05)' ?>
            );
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
            border-radius: var(--radius-sm);
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>