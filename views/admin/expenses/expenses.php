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
        'page_title' => 'Expense Management | School System',
        'header_title' => 'Expense Management',
        'recorded_by' => 'Recorded by',
        'add_expense' => 'Add New Expense',
        'back_button' => 'Back',
        
        // Stats Cards
        'stats_total' => 'Total Expenses',
        'stats_records' => 'Total Records',
        'stats_average' => 'Average Expense',
        'stats_maximum' => 'Maximum Expense',
        
        // Filter Section
        'filter_month' => 'Month',
        'filter_category' => 'Category',
        'filter_all_categories' => 'All Categories',
        'filter_search' => 'Search',
        'filter_search_placeholder' => 'Search title or category...',
        'filter_apply' => 'Apply Filters',
        'filter_reset' => 'Reset',
        
        // Table Headers
        'table_id' => '#',
        'table_title' => 'Title',
        'table_category' => 'Category',
        'table_amount' => 'Amount',
        'table_date' => 'Date',
        'table_recorded_by' => 'Recorded By',
        'table_created_at' => 'Created At',
        'table_actions' => 'Actions',
        'table_view' => 'View',
        'table_edit' => 'Edit',
        'table_delete' => 'Delete',
        
        // Table Status & Messages
        'table_empty_title' => 'No Expenses Found',
        'table_empty_message' => 'No expenses match your current filters.',
        'table_empty_action' => 'Add Your First Expense',
        'table_showing' => 'Showing',
        'table_records' => 'records',
        'table_filtered_results' => 'Filtered results',
        'table_selected_period' => 'Selected period',
        'table_filtered_total' => 'Filtered total',
        
        // Status Badges
        'category_other' => 'Other',
        
        // Modal Titles
        'modal_add_title' => 'Add New Expense',
        'modal_edit_title' => 'Edit Expense',
        'modal_view_title' => 'Expense Details',
        'modal_delete_title' => 'Confirm Delete',
        
        // Form Labels
        'form_title' => 'Title *',
        'form_title_placeholder' => 'Enter expense title',
        'form_category' => 'Category',
        'form_category_select' => 'Select Category',
        'form_new_category' => 'Or enter new category',
        'form_amount' => 'Amount *',
        'form_date' => 'Date *',
        'form_notes' => 'Notes (Optional)',
        'form_notes_placeholder' => 'Add any additional notes...',
        'form_recorded_info' => 'This expense will be recorded under your name',
        
        // Category Options
        'category_salaries' => 'Salaries',
        'category_utilities' => 'Utilities',
        'category_maintenance' => 'Maintenance',
        'category_supplies' => 'Supplies',
        'category_equipment' => 'Equipment',
        'category_transportation' => 'Transportation',
        'category_training' => 'Training',
        'category_marketing' => 'Marketing',
        'category_other' => 'Other',
        
        // Buttons
        'btn_save' => 'Save Expense',
        'btn_update' => 'Update Expense',
        'btn_cancel' => 'Cancel',
        'btn_close' => 'Close',
        'btn_print' => 'Export/Print',
        'btn_confirm_delete' => 'Delete Expense',
        
        // Messages
        'delete_confirm' => 'Are you sure you want to delete the expense:',
        'delete_warning' => 'This action cannot be undone.',
        'success_deleted' => 'Expense deleted successfully!',
        'error_deleted' => 'Failed to delete expense.',
        'success_added' => 'Expense added successfully!',
        'success_updated' => 'Expense updated successfully!',
        'error_general' => 'An error occurred. Please try again.',
        
        // DataTable Text
        'datatable_search' => 'Search expenses:',
        'datatable_show' => 'Show',
        'datatable_entries' => 'entries',
        'datatable_showing' => 'Showing',
        'datatable_to' => 'to',
        'datatable_of' => 'of',
        'datatable_total' => 'total expenses',
        'datatable_first' => 'First',
        'datatable_last' => 'Last',
        'datatable_next' => 'Next',
        'datatable_previous' => 'Previous',
        
        // Month Names
        'month_jan' => 'Jan',
        'month_feb' => 'Feb',
        'month_mar' => 'Mar',
        'month_apr' => 'Apr',
        'month_may' => 'May',
        'month_jun' => 'Jun',
        'month_jul' => 'Jul',
        'month_aug' => 'Aug',
        'month_sep' => 'Sep',
        'month_oct' => 'Oct',
        'month_nov' => 'Nov',
        'month_dec' => 'Dec',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'إدارة المصروفات | نظام المدرسة',
        'header_title' => 'إدارة المصروفات',
        'recorded_by' => 'مسجل بواسطة',
        'add_expense' => 'إضافة مصروف جديد',
        'back_button' => 'رجوع',
        
        // Stats Cards
        'stats_total' => 'إجمالي المصروفات',
        'stats_records' => 'إجمالي السجلات',
        'stats_average' => 'متوسط المصروف',
        'stats_maximum' => 'أقصى مصروف',
        
        // Filter Section
        'filter_month' => 'الشهر',
        'filter_category' => 'الفئة',
        'filter_all_categories' => 'جميع الفئات',
        'filter_search' => 'بحث',
        'filter_search_placeholder' => 'بحث بالعنوان أو الفئة...',
        'filter_apply' => 'تطبيق الفلاتر',
        'filter_reset' => 'إعادة تعيين',
        
        // Table Headers
        'table_id' => '#',
        'table_title' => 'العنوان',
        'table_category' => 'الفئة',
        'table_amount' => 'المبلغ',
        'table_date' => 'التاريخ',
        'table_recorded_by' => 'مسجل بواسطة',
        'table_created_at' => 'تاريخ الإنشاء',
        'table_actions' => 'الإجراءات',
        'table_view' => 'عرض',
        'table_edit' => 'تعديل',
        'table_delete' => 'حذف',
        
        // Table Status & Messages
        'table_empty_title' => 'لم يتم العثور على مصروفات',
        'table_empty_message' => 'لا توجد مصروفات تطابق الفلاتر الحالية.',
        'table_empty_action' => 'أضف أول مصروف',
        'table_showing' => 'عرض',
        'table_records' => 'سجل',
        'table_filtered_results' => 'نتائج مفلترة',
        'table_selected_period' => 'الفترة المحددة',
        'table_filtered_total' => 'المجموع المفتر',
        
        // Status Badges
        'category_other' => 'أخرى',
        
        // Modal Titles
        'modal_add_title' => 'إضافة مصروف جديد',
        'modal_edit_title' => 'تعديل المصروف',
        'modal_view_title' => 'تفاصيل المصروف',
        'modal_delete_title' => 'تأكيد الحذف',
        
        // Form Labels
        'form_title' => 'العنوان *',
        'form_title_placeholder' => 'أدخل عنوان المصروف',
        'form_category' => 'الفئة',
        'form_category_select' => 'اختر الفئة',
        'form_new_category' => 'أو أدخل فئة جديدة',
        'form_amount' => 'المبلغ *',
        'form_date' => 'التاريخ *',
        'form_notes' => 'ملاحظات (اختياري)',
        'form_notes_placeholder' => 'أضف أي ملاحظات إضافية...',
        'form_recorded_info' => 'سيتم تسجيل هذا المصروف تحت اسمك',
        
        // Category Options
        'category_salaries' => 'رواتب',
        'category_utilities' => 'مرافق',
        'category_maintenance' => 'صيانة',
        'category_supplies' => 'مستلزمات',
        'category_equipment' => 'معدات',
        'category_transportation' => 'نقل',
        'category_training' => 'تدريب',
        'category_marketing' => 'تسويق',
        'category_other' => 'أخرى',
        
        // Buttons
        'btn_save' => 'حفظ المصروف',
        'btn_update' => 'تحديث المصروف',
        'btn_cancel' => 'إلغاء',
        'btn_close' => 'إغلاق',
        'btn_print' => 'طباعة',
        'btn_confirm_delete' => 'حذف المصروف',
        
        // Messages
        'delete_confirm' => 'هل أنت متأكد أنك تريد حذف المصروف:',
        'delete_warning' => 'لا يمكن التراجع عن هذا الإجراء.',
        'success_deleted' => 'تم حذف المصروف بنجاح!',
        'error_deleted' => 'فشل في حذف المصروف.',
        'success_added' => 'تم إضافة المصروف بنجاح!',
        'success_updated' => 'تم تحديث المصروف بنجاح!',
        'error_general' => 'حدث خطأ. يرجى المحاولة مرة أخرى.',
        
        // DataTable Text
        'datatable_search' => 'بحث في المصروفات:',
        'datatable_show' => 'عرض',
        'datatable_entries' => 'سجلاً',
        'datatable_showing' => 'عرض',
        'datatable_to' => 'إلى',
        'datatable_of' => 'من',
        'datatable_total' => 'مصروف إجمالي',
        'datatable_first' => 'الأول',
        'datatable_last' => 'الأخير',
        'datatable_next' => 'التالي',
        'datatable_previous' => 'السابق',
        
        // Month Names
        'month_jan' => 'يناير',
        'month_feb' => 'فبراير',
        'month_mar' => 'مارس',
        'month_apr' => 'أبريل',
        'month_may' => 'مايو',
        'month_jun' => 'يونيو',
        'month_jul' => 'يوليو',
        'month_aug' => 'أغسطس',
        'month_sep' => 'سبتمبر',
        'month_oct' => 'أكتوبر',
        'month_nov' => 'نوفمبر',
        'month_dec' => 'ديسمبر',
    ]
];

// Translation helper function with string replacement
function t($key, $replacements = []) {
    global $translations, $currentLangId;
    $text = $translations[$currentLangId][$key] ?? $key;
    
    foreach ($replacements as $placeholder => $value) {
        $text = str_replace('{' . $placeholder . '}', $value, $text);
    }
    
    return $text;
}

// Helper function for month names
function get_month_name($month, $langId) {
    $monthNames = [
        1 => 'month_jan', 2 => 'month_feb', 3 => 'month_mar',
        4 => 'month_apr', 5 => 'month_may', 6 => 'month_jun',
        7 => 'month_jul', 8 => 'month_aug', 9 => 'month_sep',
        10 => 'month_oct', 11 => 'month_nov', 12 => 'month_dec'
    ];
    return t($monthNames[$month]);
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$logged_in_user_id = $_SESSION['user_id'];

// Get theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
if ($theme_res && $theme_row = $theme_res->fetch_assoc()) {
    $theme = (int)$theme_row['mode'];
} else {
    $theme = 1; // default light mode
}

// Get current user info
$user_stmt = $conn->prepare("SELECT first_name, last_name FROM users WHERE id = ?");
$user_stmt->bind_param("i", $logged_in_user_id);
$user_stmt->execute();
$user = $user_stmt->get_result()->fetch_assoc();
$current_user_name = $user ? $user['first_name'] . ' ' . $user['last_name'] : t('unknown');

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM expenses WHERE id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        $_SESSION['success'] = t('success_deleted');
    } else {
        $_SESSION['error'] = t('error_deleted');
    }
    header("Location: expenses.php");
    exit;
}

// Get filter parameters
$month = $_GET['month'] ?? date('Y-m');
$category = $_GET['category'] ?? '';
$search = $_GET['search'] ?? '';

// Build query with filters
$query = "SELECT e.*, u.first_name, u.last_name 
          FROM expenses e 
          LEFT JOIN users u ON e.recorded_by = u.id 
          WHERE 1=1";

$params = [];
$types = '';

if ($month) {
    $query .= " AND DATE_FORMAT(e.expense_date, '%Y-%m') = ?";
    $params[] = $month;
    $types .= 's';
}

if ($category) {
    $query .= " AND e.category = ?";
    $params[] = $category;
    $types .= 's';
}

if ($search) {
    $query .= " AND (e.title LIKE ? OR e.category LIKE ?)";
    $search_term = "%$search%";
    $params[] = $search_term;
    $params[] = $search_term;
    $types .= 'ss';
}

$query .= " ORDER BY e.expense_date DESC, e.created_at DESC";

// Execute query
$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$expenses = $stmt->get_result();

// Get total expenses
$total_query = "SELECT 
                SUM(amount) as total_amount,
                COUNT(*) as total_count,
                AVG(amount) as avg_amount,
                MAX(amount) as max_amount
                FROM expenses WHERE 1=1";

$total_params = [];
$total_types = '';

if ($month) {
    $total_query .= " AND DATE_FORMAT(expense_date, '%Y-%m') = ?";
    $total_params[] = $month;
    $total_types .= 's';
}
if ($category) {
    $total_query .= " AND category = ?";
    $total_params[] = $category;
    $total_types .= 's';
}
if ($search) {
    $total_query .= " AND (title LIKE ? OR category LIKE ?)";
    $search_term = "%$search%";
    $total_params[] = $search_term;
    $total_params[] = $search_term;
    $total_types .= 'ss';
}

$total_stmt = $conn->prepare($total_query);
if ($total_params) {
    $total_stmt->bind_param($total_types, ...$total_params);
}
$total_stmt->execute();
$stats = $total_stmt->get_result()->fetch_assoc();

// Get unique categories for filter
$categories_result = $conn->query("SELECT DISTINCT category FROM expenses WHERE category IS NOT NULL AND category != '' ORDER BY category");
$categories = $categories_result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <!-- Tom Select -->
    <link href="https://cdn.jsdelivr.net/npm/tom-select/dist/css/tom-select.bootstrap5.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/tom-select/dist/js/tom-select.complete.min.js"></script>
    <style>
         /* Fix for Bootstrap 5 modal focus issue with Tom Select */
        .ts-dropdown {
            z-index: 1060; /* Higher than modal */
        }
    </style> 
</head>
<body class="<?= $theme === 1 ? 'light' : 'dark' ?>">
    <div class="container-main">
        <!-- Header -->
        <div class="header-section">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-2">
                        <i class="bi bi-cash-stack me-2"></i> <?= t('header_title') ?>
                    </h1>
                    <p class="mb-0 opacity-75">
                        <?= t('recorded_by') ?>: <strong><?= htmlspecialchars($current_user_name) ?></strong>
                    </p>
                </div>
                <div>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                        <i class="bi bi-plus-circle me-2"></i> <?= t('add_expense') ?>
                    </button>
                    <button onclick="openPrintPage()" class="btn btn-outline-light">
                        <i class="bi bi-printer me-2"></i> <?= t('btn_print') ?>
                    </button>
                    <a href="export_pdf.php" target="_blank" class="btn btn-danger">
                        📄 Export PDF
                    </a>


                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <div class="stat-card total">
                <div class="stat-icon">
                    <i class="bi bi-currency-dollar text-primary"></i>
                </div>
                <div class="stat-value">$<?= number_format($stats['total_amount'] ?? 0, 2) ?></div>
                <div class="stat-label"><?= t('stats_total') ?></div>
            </div>
            
            <div class="stat-card count">
                <div class="stat-icon">
                    <i class="bi bi-list-check text-success"></i>
                </div>
                <div class="stat-value"><?= $stats['total_count'] ?? 0 ?></div>
                <div class="stat-label"><?= t('stats_records') ?></div>
            </div>
            
            <div class="stat-card avg">
                <div class="stat-icon">
                    <i class="bi bi-graph-up text-warning"></i>
                </div>
                <div class="stat-value">$<?= number_format($stats['avg_amount'] ?? 0, 2) ?></div>
                <div class="stat-label"><?= t('stats_average') ?></div>
            </div>
            
            <div class="stat-card max">
                <div class="stat-icon">
                    <i class="bi bi-arrow-up-right-square text-danger"></i>
                </div>
                <div class="stat-value">$<?= number_format($stats['max_amount'] ?? 0, 2) ?></div>
                <div class="stat-label"><?= t('stats_maximum') ?></div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label"><?= t('filter_month') ?></label>
                    <input type="month" name="month" class="form-control" value="<?= htmlspecialchars($month) ?>">
                </div>
                
                <div class="col-md-3">
                    <label class="form-label"><?= t('filter_category') ?></label>
                    <select name="category" class="form-select tom-select-search" placeholder="<?= t('filter_all_categories') ?>">
                        <option value=""><?= t('filter_all_categories') ?></option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= htmlspecialchars($cat['category']) ?>" 
                                    <?= $category == $cat['category'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($cat['category']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label"><?= t('filter_search') ?></label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="<?= t('filter_search_placeholder') ?>" 
                           value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="col-md-3 d-flex align-items-end">
                    <div class="d-flex gap-2 w-100">
                        <button type="submit" class="btn btn-primary flex-grow-1">
                            <i class="bi bi-filter me-2"></i> <?= t('filter_apply') ?>
                        </button>
                        <a href="expenses.php" class="btn btn-secondary">
                            <i class="bi bi-arrow-clockwise"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>

        <!-- Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill me-2"></i>
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-circle-fill me-2"></i>
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Expenses Table -->
      <div class="table-container">
    <div class="table-responsive">
        <table class="table table-hover" id="expensesTable">
            <thead>
                <tr>
                    <th><?= t('table_id') ?></th>
                    <th><?= t('table_title') ?></th>
                    <th><?= t('table_category') ?></th>
                    <th><?= t('table_amount') ?></th>
                    <th><?= t('table_date') ?></th>
                    <th><?= t('table_recorded_by') ?></th>
                    <th><?= t('table_created_at') ?></th>
                    <th><?= t('table_actions') ?></th>
                </tr>
            </thead>

            <tbody>
                <?php if ($expenses && $expenses->num_rows > 0): ?>
                    <?php while ($expense = $expenses->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$expense['id'] ?></td>

                            <td>
                                <strong><?= htmlspecialchars($expense['title']) ?></strong>
                            </td>

                            <td>
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($expense['category'] ?: t('category_other')) ?>
                                </span>
                            </td>

                            <td>
                                <span class="fw-bold text-danger">
                                    $<?= number_format((float)$expense['amount'], 2) ?>
                                </span>
                            </td>

                            <td>
                                <?= date('M d, Y', strtotime($expense['expense_date'])) ?>
                            </td>

                            <td>
                                <?= htmlspecialchars($expense['first_name'] . ' ' . $expense['last_name']) ?>
                            </td>

                            <td>
                                <small class="text-muted">
                                    <?= date('M d, Y H:i', strtotime($expense['created_at'])) ?>
                                </small>
                            </td>

                            <td>
                                <div class="action-buttons">
                                    <button class="btn btn-action btn-view"
                                            onclick="viewExpense(<?= (int)$expense['id'] ?>)">
                                        <i class="bi bi-eye"></i> <?= t('table_view') ?>
                                    </button>

                                    <button class="btn btn-action btn-edit"
                                            onclick="editExpense(<?= (int)$expense['id'] ?>)">
                                        <i class="bi bi-pencil"></i> <?= t('table_edit') ?>
                                    </button>

                                    <button class="btn btn-action btn-delete"
                                            onclick="confirmDelete(
                                                <?= (int)$expense['id'] ?>,
                                                '<?= htmlspecialchars($expense['title'], ENT_QUOTES) ?>'
                                            )">
                                        <i class="bi bi-trash"></i> <?= t('table_delete') ?>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>


        <!-- Summary -->
        <?php if ($expenses->num_rows > 0): ?>
            <div class="mt-4 p-3 bg-light rounded">
                <div class="row">
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle text-primary me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-0"><?= t('table_showing') ?> <?= $expenses->num_rows ?> <?= t('table_records') ?></h6>
                                <small class="text-muted"><?= t('table_filtered_results') ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-calendar-check text-success me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-0"><?= t('filter_month') ?>: <?= date('F Y', strtotime($month . '-01')) ?></h6>
                                <small class="text-muted"><?= t('table_selected_period') ?></small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-cash-coin text-danger me-3 fs-4"></i>
                            <div>
                                <h6 class="mb-0"><?= t('stats_total') ?>: $<?= number_format($stats['total_amount'] ?? 0, 2) ?></h6>
                                <small class="text-muted"><?= t('table_filtered_total') ?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Add Expense Modal -->
    <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle me-2"></i> <?= t('modal_add_title') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="addExpenseForm" method="POST" action="ajax/add_expense.php">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= t('form_title') ?></label>
                                <input type="text" name="title" class="form-control" required 
                                       placeholder="<?= t('form_title_placeholder') ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= t('form_category') ?></label>
                                <select name="category" class="form-select tom-select-create" placeholder="<?= t('form_category_select') ?>">
                                    <option value=""><?= t('form_category_select') ?></option>
                                    <option value="Salaries"><?= t('category_salaries') ?></option>
                                    <option value="Utilities"><?= t('category_utilities') ?></option>
                                    <option value="Maintenance"><?= t('category_maintenance') ?></option>
                                    <option value="Supplies"><?= t('category_supplies') ?></option>
                                    <option value="Equipment"><?= t('category_equipment') ?></option>
                                    <option value="Transportation"><?= t('category_transportation') ?></option>
                                    <option value="Training"><?= t('category_training') ?></option>
                                    <option value="Marketing"><?= t('category_marketing') ?></option>
                                    <option value="Other"><?= t('category_other') ?></option>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?= htmlspecialchars($cat['category']) ?>">
                                            <?= htmlspecialchars($cat['category']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= t('form_amount') ?></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="amount" class="form-control" 
                                           step="0.01" min="0.01" required placeholder="0.00">
                                </div>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label"><?= t('form_date') ?></label>
                                <input type="date" name="expense_date" class="form-control" 
                                       value="<?= date('Y-m-d') ?>" required>
                            </div>
                            
                            <div class="col-12 mb-3">
                                <label class="form-label"><?= t('form_notes') ?></label>
                                <textarea name="notes" class="form-control" rows="3" 
                                          placeholder="<?= t('form_notes_placeholder') ?>"></textarea>
                            </div>
                        </div>
                        
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            <?= t('form_recorded_info') ?>: 
                            <strong><?= htmlspecialchars($current_user_name) ?></strong>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('btn_cancel') ?></button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save me-2"></i> <?= t('btn_save') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Expense Modal -->
    <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-pencil-square me-2"></i> <?= t('modal_edit_title') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editExpenseForm" method="POST" action="ajax/update_expense.php">
                    <input type="hidden" name="id" id="edit_id">
                    <div class="modal-body">
                        <div id="editFormContent">
                            <!-- Content will be loaded via AJAX -->
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('btn_cancel') ?></button>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i> <?= t('btn_update') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- View Expense Modal -->
    <div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-labelledby="viewExpenseModalLabel">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-eye me-2"></i> <?= t('modal_view_title') ?>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div id="viewExpenseContent">
                        <!-- Content will be loaded via AJAX -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('btn_close') ?></button>
                    <button type="button" class="btn btn-primary" onclick="printExpense()">
                        <i class="bi bi-printer me-2"></i> <?= t('btn_print') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-exclamation-triangle me-2"></i> <?= t('modal_delete_title') ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><?= t('delete_confirm') ?></p>
                    <h5 id="deleteExpenseTitle" class="text-danger"></h5>
                    <p class="text-muted mt-3"><?= t('delete_warning') ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('btn_cancel') ?></button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i> <?= t('btn_confirm_delete') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script>
        // Initialize DataTable with translations
        $(document).ready(function() {
            $('#expensesTable').DataTable({
                pageLength: 25,
                responsive: true,
                order: [[0, 'desc']],
                language: {
                    search: "<?= t('datatable_search') ?>",
                    lengthMenu: "<?= t('datatable_show') ?> _MENU_ <?= t('datatable_entries') ?>",
                    info: "<?= t('datatable_showing') ?> _START_ <?= t('datatable_to') ?> _END_ <?= t('datatable_of') ?> _TOTAL_ <?= t('datatable_total') ?>",
                    paginate: {
                        first: "<?= t('datatable_first') ?>",
                        last: "<?= t('datatable_last') ?>",
                        next: "<?= t('datatable_next') ?>",
                        previous: "<?= t('datatable_previous') ?>"
                    }
                }
            });

            // Handle add form submission
            $('#addExpenseForm').submit(function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'ajax/add_expense.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            alert('<?= t('success_added') ?>');
                            $('#addExpenseModal').modal('hide');
                            location.reload();
                        } else {
                            alert('<?= t('error_general') ?>: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('<?= t('error_general') ?>');
                    }
                });
            });

            // Handle edit form submission
            $('#editExpenseForm').submit(function(e) {
                e.preventDefault();
                
                const formData = new FormData(this);
                
                $.ajax({
                    url: 'ajax/update_expense.php',
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        const data = JSON.parse(response);
                        if (data.success) {
                            alert('<?= t('success_updated') ?>');
                            $('#editExpenseModal').modal('hide');
                            location.reload();
                        } else {
                            alert('<?= t('error_general') ?>: ' + data.message);
                        }
                    },
                    error: function() {
                        alert('<?= t('error_general') ?>');
                    }
                });
            });
        });

        // View expense details
        function viewExpense(id) {
            $.ajax({
                url: 'ajax/get_expense.php',
                method: 'GET',
                data: { id: id, action: 'view' },
                success: function(response) {
                    $('#viewExpenseContent').html(response);
                    $('#viewExpenseModal').modal('show');
                }
            });
        }

        // Edit expense
        function editExpense(id) {
            $.ajax({
                url: 'ajax/get_expense.php',
                method: 'GET',
                data: { id: id, action: 'edit' },
                success: function(response) {
                    $('#editFormContent').html(response);
                    $('#edit_id').val(id);
                    $('#editExpenseModal').modal('show');
                    // Initialize Tom Select for the new content
                    initTomSelect(document.getElementById('editFormContent'));
                }
            });
        }

        // Delete confirmation
        function confirmDelete(id, title) {
            $('#deleteExpenseTitle').text(title);
            $('#confirmDeleteBtn').attr('href', 'expenses.php?delete=' + id);
            $('#deleteConfirmModal').modal('show');
        }

        // Print expense details
        function printExpense() {
            const printContent = $('#viewExpenseContent').html();
            const originalContent = document.body.innerHTML;
            
            document.body.innerHTML = `
                <!DOCTYPE html>
                <html>
                <head>
                    <title><?= t('modal_view_title') ?></title>
                    <style>
                        body { font-family: Arial, sans-serif; padding: 20px; }
                        .print-header { text-align: center; margin-bottom: 30px; }
                        .print-section { margin-bottom: 20px; }
                        .print-label { font-weight: bold; color: #666; }
                        .print-value { margin-bottom: 10px; }
                        .amount { font-size: 24px; font-weight: bold; color: #dc3545; }
                        hr { border-top: 2px solid #000; }
                    </style>
                </head>
                <body>
                    ${printContent}
                </body>
                </html>
            `;
            
            window.print();
            document.body.innerHTML = originalContent;
            location.reload();
        }

        // Auto-close alerts after 5 seconds
        setTimeout(() => {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);

        // Theme switcher (if needed)
        function toggleTheme() {
            $.ajax({
                url: 'ajax/toggle_theme.php',
                method: 'POST',
                success: function() {
                    location.reload();
                }
            });
        }
        function openPrintPage() {
            const params = new URLSearchParams(window.location.search);
            window.open('print_expenses.php?' + params.toString(), '_blank');
        }

        // Initialize Tom Select
        function initTomSelect(scope = document) {
            // Search-only selects (e.g. Filters)
            scope.querySelectorAll('.tom-select-search').forEach((el) => {
                if (el.tomselect) return; // Prevent re-initialization
                new TomSelect(el, {
                    create: false,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    plugins: ['remove_button'] // Ensure remove_button plugin is loaded
                });
            });

            // Create-enabled selects (e.g. Add/Edit Category)
            scope.querySelectorAll('.tom-select-create').forEach((el) => {
                if (el.tomselect) return; // Prevent re-initialization
                new TomSelect(el, {
                    create: true,
                    sortField: {
                        field: "text",
                        direction: "asc"
                    },
                    plugins: ['remove_button']
                });
            });
        }

        // Initialize on load
        document.addEventListener('DOMContentLoaded', function() {
            initTomSelect();
        });
    </script>
</body>
</html>

   <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
            --border-radius: 8px;
            --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        .light {
            --bg-primary: #ffffff;
            --bg-secondary: #f8f9fa;
            --bg-card: #ffffff;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --table-header-bg: #f1f3f4;
            --hover-bg: rgba(0, 0, 0, 0.02);
        }

        .dark {
            --bg-primary: #1a1a2e;
            --bg-secondary: #16213e;
            --bg-card: #0f3460;
            --text-primary: #e9ecef;
            --text-secondary: #adb5bd;
            --border-color: #495057;
            --table-header-bg: #2d3748;
            --hover-bg: rgba(255, 255, 255, 0.05);
        }

        body {
            background-color: var(--bg-primary);
            color: var(--text-primary);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: var(--transition);
        }

        .container-main {
            padding: 20px;
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header Styles */
        .header-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 25px 30px;
            border-radius: var(--border-radius);
            margin-bottom: 30px;
            box-shadow: var(--box-shadow);
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--box-shadow);
            border-left: 4px solid var(--secondary-color);
            transition: var(--transition);
        }

        .stat-card:hover {
            transform: translateY(-3px);
        }

        .stat-card.total {
            border-left-color: var(--primary-color);
        }

        .stat-card.count {
            border-left-color: var(--success-color);
        }

        .stat-card.avg {
            border-left-color: var(--warning-color);
        }

        .stat-card.max {
            border-left-color: var(--danger-color);
        }

        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 15px;
            opacity: 0.8;
        }

        .stat-value {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-label {
            color: var(--text-secondary);
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Filter Section */
        .filter-section {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 30px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .form-control, .form-select {
            background-color: var(--bg-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-primary);
            border-radius: 6px;
            padding: 10px 15px;
        }

        .form-control:focus, .form-select:focus {
            background-color: var(--bg-secondary);
            border-color: var(--secondary-color);
            color: var(--text-primary);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        /* Table Styles */
        .table-container {
            background: var(--bg-card);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--box-shadow);
        }

        .table {
            margin-bottom: 0;
            color: var(--text-primary);
        }

        .table thead th {
            background-color: var(--table-header-bg);
            border-bottom: 2px solid var(--border-color);
            font-weight: 600;
            padding: 15px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
        }

        .table tbody tr {
            transition: var(--transition);
        }

        .table tbody tr:hover {
            background-color: var(--hover-bg);
        }

        .table td {
            padding: 15px;
            vertical-align: middle;
            border-color: var(--border-color);
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        .btn-action {
            padding: 6px 12px;
            border-radius: 4px;
            font-size: 0.85rem;
            display: inline-flex;
            align-items: center;
            gap: 5px;
            transition: var(--transition);
        }

        .btn-view {
            background: var(--secondary-color);
            color: white;
            border: none;
        }

        .btn-edit {
            background: var(--warning-color);
            color: white;
            border: none;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
            border: none;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            opacity: 0.9;
        }

        /* Modal Styles */
        .modal-content {
            background-color: var(--bg-card);
            color: var(--text-primary);
            border: 1px solid var(--border-color);
        }

        .modal-header {
            border-bottom: 1px solid var(--border-color);
        }

        .modal-footer {
            border-top: 1px solid var(--border-color);
        }

        /* Alert Messages */
        .alert {
            border-radius: var(--border-radius);
            border: none;
            padding: 15px 20px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
            border-left: 4px solid var(--success-color);
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
            border-left: 4px solid var(--danger-color);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container-main {
                padding: 10px;
            }
            
            .header-section {
                padding: 20px;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn-action {
                width: 100%;
                justify-content: center;
            }
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: var(--bg-secondary);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--secondary-color);
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--primary-color);
        }
    </style>
</body>
</html>
