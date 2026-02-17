<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Student Discounts Management',
        'add_new_discount' => 'Add New Discount',
        'total_discounts' => 'Total Discounts',
        'active_discounts' => 'Active Discounts',
        'inactive_discounts' => 'Inactive Discounts',
        'edit_discount' => 'Edit Discount',
        'add_new_discount_form' => 'Add New Discount',
        'class' => 'Class',
        'select_class' => 'Select Class',
        'student' => 'Student',
        'select_class_first' => 'Select class first',
        'select_student' => 'Select Student',
        'fee_category' => 'Fee Category',
        'select_fee_category' => 'Select Fee Category',
        'category_amount' => 'Category Amount',
        'discount_percentage' => 'Discount Percentage',
        'reason_optional' => 'Reason (Optional)',
        'reason_placeholder' => 'Enter reason for discount...',
        'save_discount' => 'Save Discount',
        'update_discount' => 'Update Discount',
        'cancel_edit' => 'Cancel Edit',
        'filter_discounts' => 'Filter Discounts',
        'search_student' => 'Search Student',
        'search_placeholder' => 'Enter student name...',
        'all_classes' => 'All Classes',
        'all_categories' => 'All Categories',
        'apply_filters' => 'Apply Filters',
        'clear_filters' => 'Clear Filters',
        'discount_records' => 'Discount Records',
        'found' => 'found',
        'id' => 'ID',
        'student_name' => 'Student',
        'fee_category_name' => 'Fee Category',
        'discount_amount' => 'Discount Amount',
        'reason' => 'Reason',
        'status' => 'Status',
        'created' => 'Created',
        'actions' => 'Actions',
        'no_reason' => 'No reason provided',
        'active' => 'Active',
        'inactive' => 'Inactive',
        'confirm_delete' => 'Are you sure you want to delete this discount? This action cannot be undone.',
        'percentage_error' => 'Discount percentage must be between 0 and 100.',
        'deleted_success' => 'Discount deleted successfully!',
        'error_deleting' => 'Error deleting discount: ',
        'updated_success' => 'Discount updated successfully!',
        'existing_updated' => 'Existing discount updated for this student & fee category!',
        'added_success' => 'Discount added successfully!'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة خصومات الطلاب',
        'add_new_discount' => 'إضافة خصم جديد',
        'total_discounts' => 'إجمالي الخصومات',
        'active_discounts' => 'الخصومات النشطة',
        'inactive_discounts' => 'الخصومات غير النشطة',
        'edit_discount' => 'تعديل الخصم',
        'add_new_discount_form' => 'إضافة خصم جديد',
        'class' => 'الصف',
        'select_class' => 'اختر الصف',
        'student' => 'الطالب',
        'select_class_first' => 'اختر الصف أولاً',
        'select_student' => 'اختر الطالب',
        'fee_category' => 'فئة الرسوم',
        'select_fee_category' => 'اختر فئة الرسوم',
        'category_amount' => 'مبلغ الفئة',
        'discount_percentage' => 'نسبة الخصم',
        'reason_optional' => 'السبب (اختياري)',
        'reason_placeholder' => 'أدخل سبب الخصم...',
        'save_discount' => 'حفظ الخصم',
        'update_discount' => 'تحديث الخصم',
        'cancel_edit' => 'إلغاء التعديل',
        'filter_discounts' => 'تصفية الخصومات',
        'search_student' => 'بحث عن طالب',
        'search_placeholder' => 'أدخل اسم الطالب...',
        'all_classes' => 'جميع الصفوف',
        'all_categories' => 'جميع الفئات',
        'apply_filters' => 'تطبيق الفلاتر',
        'clear_filters' => 'مسح الفلاتر',
        'discount_records' => 'سجلات الخصم',
        'found' => 'موجود',
        'id' => 'الرقم',
        'student_name' => 'الطالب',
        'fee_category_name' => 'فئة الرسوم',
        'discount_amount' => 'مبلغ الخصم',
        'reason' => 'السبب',
        'status' => 'الحالة',
        'created' => 'تاريخ الإنشاء',
        'actions' => 'الإجراءات',
        'no_reason' => 'لا يوجد سبب مقدم',
        'active' => 'نشط',
        'inactive' => 'غير نشط',
        'confirm_delete' => 'هل أنت متأكد أنك تريد حذف هذا الخصم؟ لا يمكن التراجع عن هذا الإجراء.',
        'percentage_error' => 'يجب أن تكون نسبة الخصم بين 0 و 100.',
        'deleted_success' => 'تم حذف الخصم بنجاح!',
        'error_deleting' => 'خطأ في حذف الخصم: ',
        'updated_success' => 'تم تحديث الخصم بنجاح!',
        'existing_updated' => 'تم تحديث الخصم الحالي لهذا الطالب وفئة الرسوم!',
        'added_success' => 'تم إضافة الخصم بنجاح!'
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

$msg = "";
$edit_id = 0;

// ----------- Read theme from DB -----------
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1; // default light
$isDark = $themeMode == 0;

// ----------- Handle delete -----------
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $stmt = $conn->prepare("DELETE FROM student_discounts WHERE id=?");
    $stmt->bind_param("i", $del_id);
    if($stmt->execute()) $msg = t('deleted_success');
    else $msg = t('error_deleting') . $stmt->error;
    $stmt->close();
}

// ----------- Handle form submission -----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $edit_id = intval($_POST['edit_id'] ?? 0);
    $student_id = intval($_POST['student_id']);
    $class_id = intval($_POST['class_id']);
    $fee_category_id = intval($_POST['fee_category_id']);
    $discount_percentage = floatval($_POST['discount_percentage']);
    $reason = $conn->real_escape_string($_POST['reason']);

    // ----------- Proper Fee Amount Reading ----------- 
    $classRow = $conn->query("SELECT section_id FROM classes WHERE id=$class_id LIMIT 1")->fetch_assoc();
    $section_id = $classRow['section_id'] ?? 0;

    $feeRow = $conn->query("
        SELECT amount 
        FROM section_fees 
        WHERE section_id=$section_id AND fee_category_id=$fee_category_id 
        LIMIT 1
    ")->fetch_assoc();
    $fee_amount = $feeRow['amount'] ?? 0;
    $discount_amount = $fee_amount * $discount_percentage / 100;

    // Insert or update
    if($edit_id > 0){
        $stmt = $conn->prepare("UPDATE student_discounts SET student_id=?, fee_category_id=?, discount_percentage=?, discount_amount=?, reason=? WHERE id=?");
        $stmt->bind_param("iiddsi", $student_id, $fee_category_id, $discount_percentage, $discount_amount, $reason, $edit_id);
        $stmt->execute();
        $stmt->close();
        $msg = t('updated_success');
    } else {
        $existing = $conn->query("
            SELECT id 
            FROM student_discounts 
            WHERE student_id=$student_id AND fee_category_id=$fee_category_id 
            LIMIT 1
        ")->fetch_assoc();

        if($existing){
            $stmt = $conn->prepare("UPDATE student_discounts SET discount_percentage=?, discount_amount=?, reason=? WHERE id=?");
            $stmt->bind_param("ddsi", $discount_percentage, $discount_amount, $reason, $existing['id']);
            $stmt->execute();
            $stmt->close();
            $msg = t('existing_updated');
        } else {
            $stmt = $conn->prepare("INSERT INTO student_discounts (student_id, fee_category_id, discount_percentage, discount_amount, reason, active) VALUES (?, ?, ?, ?, ?, 1)");
            $stmt->bind_param("iidds", $student_id, $fee_category_id, $discount_percentage, $discount_amount, $reason);
            $stmt->execute();
            $stmt->close();
            $msg = t('added_success');
        }
    }

    // Redirect to avoid resubmission
    header("Location: ".$_SERVER['PHP_SELF']."?msg=".urlencode($msg));
    exit;
}

// ------------------ Filters & Pagination ------------------
$search = $_GET['search'] ?? '';
$classFilter = $_GET['class'] ?? '';
$categoryFilter = $_GET['category'] ?? '';
$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 15;

// ------------------ Discounts Query ------------------
$discountsSql = "
SELECT sd.id, s.id AS student_id, s.name AS student_name, cs.class_id,
       fc.id AS fee_category_id, fc.name AS fee_category_name,
       sd.discount_percentage, sd.discount_amount, sd.reason, sd.active, sd.created_at,
       sf.amount AS category_amount
FROM student_discounts sd
JOIN students s ON s.id = sd.student_id
JOIN class_students cs ON cs.student_id = s.id
JOIN classes c ON c.id = cs.class_id
JOIN fee_categories fc ON fc.id = sd.fee_category_id
LEFT JOIN section_fees sf ON sf.fee_category_id = fc.id AND sf.section_id = c.section_id
WHERE 1
";
if($search) $discountsSql .= " AND s.name LIKE '%".$conn->real_escape_string($search)."%'";
if($classFilter) $discountsSql .= " AND cs.class_id=". (int)$classFilter;
if($categoryFilter) $discountsSql .= " AND fc.id=". (int)$categoryFilter;

$totalRes = $conn->query($discountsSql);
$totalRows = $totalRes->num_rows;
$totalPages = ceil($totalRows / $perPage);
$discountsSql .= " ORDER BY sd.created_at DESC LIMIT ".(($page-1)*$perPage).", $perPage";
$discounts = $conn->query($discountsSql);

// ------------------ Classes & Fee Categories ------------------
$classes = $conn->query("
    SELECT DISTINCT c.id, c.name
    FROM classes c
    JOIN class_students cs ON cs.class_id = c.id
    WHERE cs.is_active = 1
    ORDER BY c.name ASC
");

$fee_categories = $conn->query("
    SELECT fc.id, fc.name, sf.amount, sf.section_id
    FROM fee_categories fc
    LEFT JOIN section_fees sf ON sf.fee_category_id = fc.id
    ORDER BY fc.name ASC
");

$categories_js = [];
while($fc = $fee_categories->fetch_assoc()){
    $categories_js[$fc['id']] = ['name'=>$fc['name'], 'amount'=>$fc['amount'] ?? 0];
}

// ------------------ Students by class ------------------
$all_students = $conn->query("SELECT s.id, s.name, cs.class_id FROM students s JOIN class_students cs ON cs.student_id = s.id ORDER BY s.name ASC");
$students_by_class = [];
while($s = $all_students->fetch_assoc()){
    $students_by_class[$s['class_id']][] = ['id'=>$s['id'], 'name'=>$s['name']];
}
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="header-title">
                <i class="fas fa-percentage"></i>
                <h1><?= t('page_title') ?></h1>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="window.scrollTo({top:0,behavior:'smooth'})">
                    <i class="fas fa-plus"></i> <?= t('add_new_discount') ?>
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="stats-cards">
            <?php
            $totalDiscounts = $conn->query("SELECT COUNT(*) as total FROM student_discounts")->fetch_assoc()['total'];
            $activeDiscounts = $conn->query("SELECT COUNT(*) as active FROM student_discounts WHERE active = 1")->fetch_assoc()['active'];
            $inactiveDiscounts = $totalDiscounts - $activeDiscounts;
            ?>
            <div class="stat-card fade-in">
                <div class="stat-icon total">
                    <i class="fas fa-percentage"></i>
                </div>
                <div class="stat-content">
                    <h3><?= t('total_discounts') ?></h3>
                    <div class="value"><?= $totalDiscounts ?></div>
                </div>
            </div>
            
            <div class="stat-card fade-in">
                <div class="stat-icon active">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= t('active_discounts') ?></h3>
                    <div class="value"><?= $activeDiscounts ?></div>
                </div>
            </div>
            
            <div class="stat-card fade-in">
                <div class="stat-icon inactive">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3><?= t('inactive_discounts') ?></h3>
                    <div class="value"><?= $inactiveDiscounts ?></div>
                </div>
            </div>
        </div>

        <!-- Message Alert -->
        <?php if($msg): ?>
            <div class="alert alert-success fade-in">
                <i class="fas fa-check-circle"></i>
                <?= htmlspecialchars($msg) ?>
            </div>
        <?php endif; ?>

        <!-- Form Section -->
        <div class="form-section fade-in">
            <div class="section-title">
                <i class="fas fa-edit"></i>
                <span><?= $edit_id ? t('edit_discount') : t('add_new_discount_form') ?></span>
            </div>
            
            <form method="POST" id="discountForm">
                <input type="hidden" name="edit_id" id="edit_id" value="0">
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="class_id"><i class="fas fa-graduation-cap"></i> <?= t('class') ?></label>
                        <select name="class_id" id="class_id" class="form-control" onchange="updateStudentDropdown()" required>
                            <option value=""><?= t('select_class') ?></option>
                            <?php
                            $classes->data_seek(0);
                            while($c = $classes->fetch_assoc()):
                            ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="student_id"><i class="fas fa-user-graduate"></i> <?= t('student') ?></label>
                        <select name="student_id" id="student_id" class="form-control" required>
                            <option value=""><?= t('select_class_first') ?></option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="fee_category_id"><i class="fas fa-tags"></i> <?= t('fee_category') ?></label>
                        <select name="fee_category_id" id="fee_category_id" class="form-control" onchange="updateCategoryAmount()" required>
                            <option value=""><?= t('select_fee_category') ?></option>
                            <?php foreach($categories_js as $id=>$cat): ?>
                                <option value="<?= $id ?>"><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="category_amount"><i class="fas fa-dollar-sign"></i> <?= t('category_amount') ?></label>
                        <input type="text" id="category_amount" class="form-control" readonly value="0">
                    </div>
                    
                    <div class="form-group">
                        <label for="discount_percentage"><i class="fas fa-percent"></i> <?= t('discount_percentage') ?></label>
                        <input type="number" step="0.01" name="discount_percentage" id="discount_percentage" 
                               class="form-control" value="0" min="0" max="100" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="reason"><i class="fas fa-comment-alt"></i> <?= t('reason_optional') ?></label>
                        <input type="text" name="reason" id="reason" class="form-control" 
                               placeholder="<?= t('reason_placeholder') ?>">
                    </div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> <?= $edit_id ? t('update_discount') : t('save_discount') ?>
                    </button>
                    <?php if($edit_id): ?>
                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                            <i class="fas fa-times"></i> <?= t('cancel_edit') ?>
                        </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Filter Section -->
        <div class="filter-section fade-in">
            <div class="section-title">
                <i class="fas fa-filter"></i>
                <span><?= t('filter_discounts') ?></span>
            </div>
            
            <form method="get" class="filter-grid">
                <div class="form-group">
                    <label for="search"><i class="fas fa-search"></i> <?= t('search_student') ?></label>
                    <input type="text" name="search" id="search" class="form-control" 
                           placeholder="<?= t('search_placeholder') ?>" value="<?= htmlspecialchars($search) ?>">
                </div>
                
                <div class="form-group">
                    <label for="class"><i class="fas fa-graduation-cap"></i> <?= t('class') ?></label>
                    <select name="class" id="class" class="form-control">
                        <option value=""><?= t('all_classes') ?></option>
                        <?php
                        $classes->data_seek(0);
                        while($c = $classes->fetch_assoc()):
                        ?>
                            <option value="<?= $c['id'] ?>" <?= $classFilter==$c['id']?'selected':'' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category"><i class="fas fa-tags"></i> <?= t('fee_category') ?></label>
                    <select name="category" id="category" class="form-control">
                        <option value=""><?= t('all_categories') ?></option>
                        <?php foreach($categories_js as $cid=>$c): ?>
                            <option value="<?= $cid ?>" <?= $categoryFilter==$cid?'selected':'' ?>>
                                <?= htmlspecialchars($c['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> <?= t('apply_filters') ?>
                    </button>
                    <?php if($search || $classFilter || $categoryFilter): ?>
                        <a href="?" class="btn btn-secondary">
                            <i class="fas fa-times"></i> <?= t('clear_filters') ?>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table Section -->
        <div class="table-container fade-in">
            <div class="section-title" style="margin-bottom: 1rem;">
                <i class="fas fa-list"></i>
                <span><?= t('discount_records') ?> (<?= $totalRows ?> <?= t('found') ?>)</span>
            </div>
            
            <table>
                <thead>
                    <tr>
                        <th><?= t('id') ?></th>
                        <th><?= t('student_name') ?></th>
                        <th><?= t('fee_category_name') ?></th>
                        <th><?= t('category_amount') ?></th>
                        <th><?= t('discount_percentage') ?></th>
                        <th><?= t('discount_amount') ?></th>
                        <th><?= t('reason') ?></th>
                        <th><?= t('status') ?></th>
                        <th><?= t('created') ?></th>
                        <th><?= t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($d = $discounts->fetch_assoc()): ?>
                    <tr data-id="<?= $d['id'] ?>"
                        data-class-id="<?= $d['class_id'] ?>"
                        data-student-id="<?= $d['student_id'] ?>"
                        data-fee-category-id="<?= $d['fee_category_id'] ?>"
                        data-discount-percentage="<?= $d['discount_percentage'] ?>"
                        data-reason="<?= htmlspecialchars($d['reason'], ENT_QUOTES) ?>"
                        data-category-amount="<?= $d['category_amount'] ?>">
                        <td>#<?= $d['id'] ?></td>
                        <td><strong><?= htmlspecialchars($d['student_name']) ?></strong></td>
                        <td><?= htmlspecialchars($d['fee_category_name']) ?></td>
                        <td>$<?= number_format($d['category_amount']??0,2) ?></td>
                        <td>
                            <span class="status-badge" style="background:rgba(59,130,246,0.1);color:<?= $isDark?'var(--dark-primary)':'var(--light-primary)' ?>;">
                                <?= number_format($d['discount_percentage'],2) ?>%
                            </span>
                        </td>
                        <td>
                            <span class="status-badge" style="background:rgba(16,185,129,0.1);color:<?= $isDark?'var(--dark-success)':'var(--light-success)' ?>;">
                                $<?= number_format($d['discount_amount'],2) ?>
                            </span>
                        </td>
                        <td><?= htmlspecialchars($d['reason']) ?: '<span style="color:#94a3b8;font-style:italic;">' . t('no_reason') . '</span>' ?></td>
                        <td>
                            <span class="status-badge <?= $d['active']?'status-active':'status-inactive' ?>">
                                <i class="fas fa-<?= $d['active']?'check':'times' ?>-circle"></i>
                                <?= $d['active'] ? t('active') : t('inactive') ?>
                            </span>
                        </td>
                        <td><?= date('M d, Y', strtotime($d['created_at'])) ?></td>
                        <td>
                            <div class="action-buttons">
                                <button class="btn btn-sm btn-primary" onclick="editDiscount(this.parentNode.parentNode.parentNode)">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $d['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
            
            <!-- Pagination -->
            <?php if($totalPages > 1): ?>
            <div class="pagination">
                <?php for($i=1;$i<=$totalPages;$i++):
                    $link="?page=$i&search=".urlencode($search)."&class=".urlencode($classFilter)."&category=".urlencode($categoryFilter);
                ?>
                    <a href="<?= $link ?>" class="page-link <?= $i==$page?'page-current':'' ?>">
                        <?= $i ?>
                    </a>
                <?php endfor; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const studentsByClass = <?= json_encode($students_by_class) ?>;
        const feeCategories = <?= json_encode($categories_js) ?>;
        const currentLangId = <?= $currentLangId ?>;

        function updateStudentDropdown(selectedStudent=0) {
            const classId = document.getElementById('class_id').value;
            const studentSelect = document.getElementById('student_id');
            studentSelect.innerHTML = '<option value=""><?= t('select_student') ?></option>';
            if(classId && studentsByClass[classId]){
                studentsByClass[classId].forEach(s => {
                    const opt = document.createElement('option');
                    opt.value = s.id; 
                    opt.text = s.name;
                    if(s.id==selectedStudent) opt.selected=true;
                    studentSelect.appendChild(opt);
                });
            }
        }

        function updateCategoryAmount(selectedAmount=null) {
            const categoryId = document.getElementById('fee_category_id').value;
            const amountInput = document.getElementById('category_amount');
            if(selectedAmount!==null){ 
                amountInput.value = selectedAmount; 
                return; 
            }
            if(categoryId && feeCategories[categoryId]){
                amountInput.value = feeCategories[categoryId].amount ?? 0;
            } else {
                amountInput.value = 0;
            }
        }

        function editDiscount(row){
            const data = row.dataset;
            document.getElementById('edit_id').value = data.id;
            document.getElementById('class_id').value = data.classId;
            updateStudentDropdown(parseInt(data.studentId));
            document.getElementById('fee_category_id').value = data.feeCategoryId;
            updateCategoryAmount(parseFloat(data.categoryAmount));
            document.getElementById('discount_percentage').value = data.discountPercentage;
            document.getElementById('reason').value = data.reason;
            
            // Scroll to form and change title
            document.querySelector('.form-section .section-title span').textContent = '<?= t('edit_discount') ?>';
            window.scrollTo({top: 0, behavior: 'smooth'});
            
            // Add fade animation
            const formSection = document.querySelector('.form-section');
            formSection.classList.remove('fade-in');
            void formSection.offsetWidth;
            formSection.classList.add('fade-in');
        }

        function resetForm(){
            document.getElementById('edit_id').value = 0;
            document.getElementById('class_id').value = '';
            document.getElementById('student_id').innerHTML = '<option value=""><?= t('select_class_first') ?></option>';
            document.getElementById('fee_category_id').value = '';
            document.getElementById('category_amount').value = '0';
            document.getElementById('discount_percentage').value = '0';
            document.getElementById('reason').value = '';
            document.querySelector('.form-section .section-title span').textContent = '<?= t('add_new_discount_form') ?>';
        }

        function confirmDelete(id){
            if(confirm("<?= t('confirm_delete') ?>")){
                window.location="?delete_id="+id;
            }
        }

        // Initialize tooltips for icons
        document.addEventListener('DOMContentLoaded', function() {
            // Add fade-in animation to all sections
            const sections = document.querySelectorAll('.fade-in');
            sections.forEach((section, index) => {
                section.style.animationDelay = `${index * 0.1}s`;
            });
            
            // Highlight current page in pagination
            const currentPage = document.querySelector('.page-current');
            if(currentPage) {
                currentPage.style.fontWeight = 'bold';
            }
        });

        // Form validation
        document.getElementById('discountForm').addEventListener('submit', function(e) {
            const discountPercent = document.getElementById('discount_percentage').value;
            if(discountPercent < 0 || discountPercent > 100) {
                e.preventDefault();
                alert("<?= t('percentage_error') ?>");
                return false;
            }
            return true;
        });
    </script>
</body>
</html>

   <style>
        :root {
            /* Light Theme */
            --light-bg: #f8fafc;
            --light-card: #ffffff;
            --light-text: #1e293b;
            --light-border: #e2e8f0;
            --light-primary: #3b82f6;
            --light-primary-hover: #2563eb;
            --light-secondary: #64748b;
            --light-success: #10b981;
            --light-danger: #ef4444;
            --light-warning: #f59e0b;
            --light-hover: #f1f5f9;
            --light-input: #ffffff;
            
            /* Dark Theme */
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-text: #f1f5f9;
            --dark-border: #334155;
            --dark-primary: #60a5fa;
            --dark-primary-hover: #3b82f6;
            --dark-secondary: #94a3b8;
            --dark-success: #34d399;
            --dark-danger: #f87171;
            --dark-warning: #fbbf24;
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
            background: <?= $isDark ? 'var(--dark-bg)' : 'var(--light-bg)' ?>;
            color: <?= $isDark ? 'var(--dark-text)' : 'var(--light-text)' ?>;
            padding: 1.5rem;
        }

        .container {
            max-width: 1600px;
            margin: 0 auto;
        }

        /* Header Styles */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .header-title {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .header-title h1 {
            font-size: 1.75rem;
            font-weight: 700;
            background: <?= $isDark ? 'linear-gradient(135deg, #60a5fa, #3b82f6)' : 'linear-gradient(135deg, #3b82f6, #1d4ed8)' ?>;
            -webkit-background-clip: text;
            background-clip: text;
            color: transparent;
        }

        .header-title i {
            font-size: 2rem;
            color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            padding: 1.5rem;
            border-radius: 12px;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        .stat-icon.total {
            background: rgba(59, 130, 246, 0.1);
            color: var(--light-primary);
        }

        .stat-icon.active {
            background: rgba(16, 185, 129, 0.1);
            color: var(--light-success);
        }

        .stat-icon.inactive {
            background: rgba(239, 68, 68, 0.1);
            color: var(--light-danger);
        }

        .stat-content h3 {
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            margin-bottom: 0.25rem;
        }

        .stat-content .value {
            font-size: 1.5rem;
            font-weight: 700;
            color: <?= $isDark ? 'var(--dark-text)' : 'var(--light-text)' ?>;
        }

        /* Form Section */
        .form-section {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }

        .section-title {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.25rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
            color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        .section-title i {
            font-size: 1.25rem;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .form-group label {
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .form-group label i {
            font-size: 0.875rem;
            width: 16px;
        }

        .form-control {
            padding: 0.875rem 1rem;
            background: <?= $isDark ? 'var(--dark-input)' : 'var(--light-input)' ?>;
            border: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            border-radius: 10px;
            font-size: 1rem;
            color: inherit;
            outline: none;
            width: 100%;
        }

        .form-control:focus {
            border-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }

        .form-control:read-only {
            background: <?= $isDark ? '#0f172a' : '#f1f5f9' ?>;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .form-actions {
            display: flex;
            gap: 1rem;
            margin-top: 1.5rem;
            padding-top: 1.5rem;
            border-top: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        /* Filter Section */
        .filter-section {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            align-items: end;
        }

        /* Table Styles */
        .table-container {
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            border-radius: 16px;
            padding: 1.5rem;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        thead {
            background: <?= $isDark ? '#0f172a' : '#f1f5f9' ?>;
        }

        th {
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            font-size: 0.875rem;
            color: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 2px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
        }

        td {
            padding: 1rem;
            border-bottom: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            vertical-align: middle;
        }

        tbody tr {
            transition: background-color 0.2s ease;
        }

        tbody tr:hover {
            background: <?= $isDark ? 'var(--dark-hover)' : 'var(--light-hover)' ?>;
        }

        /* Status Badges */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.375rem 0.875rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        .status-active {
            background: rgba(16, 185, 129, 0.1);
            color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
        }

        .status-inactive {
            background: rgba(239, 68, 68, 0.1);
            color: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.875rem;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }

        .btn-sm {
            padding: 0.375rem 0.75rem;
            font-size: 0.75rem;
        }

        .btn-primary {
            background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            color: white;
        }

        .btn-primary:hover {
            background: <?= $isDark ? 'var(--dark-primary-hover)' : 'var(--light-primary-hover)' ?>;
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: <?= $isDark ? 'var(--dark-secondary)' : 'var(--light-secondary)' ?>;
            color: white;
        }

        .btn-secondary:hover {
            background: <?= $isDark ? '#475569' : '#475569' ?>;
        }

        .btn-success {
            background: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
            color: white;
        }

        .btn-danger {
            background: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
            color: white;
        }

        .btn-danger:hover {
            background: <?= $isDark ? '#dc2626' : '#dc2626' ?>;
        }

        /* Message Alert */
        .alert {
            padding: 1rem 1.5rem;
            border-radius: 12px;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-weight: 500;
            border-left: 4px solid;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border-left-color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
            color: <?= $isDark ? 'var(--dark-success)' : 'var(--light-success)' ?>;
        }

        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border-left-color: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
            color: <?= $isDark ? 'var(--dark-danger)' : 'var(--light-danger)' ?>;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
            margin-top: 2rem;
            flex-wrap: wrap;
        }

        .page-link {
            padding: 0.5rem 1rem;
            border-radius: 8px;
            background: <?= $isDark ? 'var(--dark-card)' : 'var(--light-card)' ?>;
            color: <?= $isDark ? 'var(--dark-text)' : 'var(--light-text)' ?>;
            text-decoration: none;
            border: 1px solid <?= $isDark ? 'var(--dark-border)' : 'var(--light-border)' ?>;
            font-weight: 500;
            transition: all 0.2s ease;
        }

        .page-link:hover {
            background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            color: white;
            border-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        .page-current {
            background: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
            color: white;
            border-color: <?= $isDark ? 'var(--dark-primary)' : 'var(--light-primary)' ?>;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            body {
                padding: 1rem;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .form-grid {
                grid-template-columns: 1fr;
            }
            
            .filter-grid {
                grid-template-columns: 1fr;
            }
            
            .form-actions {
                flex-direction: column;
            }
            
            .btn {
                width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .table-container {
                padding: 1rem;
            }
            
            th, td {
                padding: 0.75rem;
            }
        }

        @media (max-width: 480px) {
            .header-title h1 {
                font-size: 1.5rem;
            }
            
            .stats-cards {
                grid-template-columns: 1fr;
            }
            
            .form-section, .filter-section {
                padding: 1.25rem;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .fade-in {
            animation: fadeIn 0.3s ease forwards;
        }
    </style>