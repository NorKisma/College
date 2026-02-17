<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
date_default_timezone_set('Africa/Mogadishu');

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Fee & Section Management',
        'back_to_fees' => 'Back to Fees',
        'fee_categories' => 'Fee Categories',
        'sections' => 'Sections',
        'section_fees' => 'Section Fees',
        'add_fee_category' => 'Add Fee Category',
        'add_section' => 'Add Section',
        'add_section_fee' => 'Add Section Fee',
        'id' => 'ID',
        'name' => 'Name',
        'description' => 'Description',
        'status' => 'Status',
        'actions' => 'Actions',
        'protected' => 'Protected',
        'editable' => 'Editable',
        'payable' => 'Payable',
        'yes' => 'Yes',
        'no' => 'No',
        'active' => 'Active',
        'amount' => 'Amount',
        'save_fee_category' => 'Save Fee Category',
        'save_section' => 'Save Section',
        'save_section_fee' => 'Save Section Fee',
        'select_fee_category' => 'Select Fee Category',
        'select_section' => 'Select Section',
        'payable_section_note' => 'Payable Section (Auto-adds Fee Salary)',
        'delete_fee_category' => 'Delete Fee Category?',
        'delete_fee_confirmation' => 'Are you sure you want to delete <b>{name}</b>?<br><br><small class="text-muted">This will also remove all associated section fees.</small>',
        'delete_section' => 'Delete Section?',
        'delete_section_confirmation' => 'Are you sure you want to delete <b>{name}</b>?<br><br><small class="text-muted">Fee salary fees will be protected.</small>',
        'remove_section_fee' => 'Remove Section Fee?',
        'remove_section_fee_confirmation' => 'Are you sure you want to remove <b>{name}</b> from this section?',
        'confirm_delete' => 'Yes, delete it!',
        'confirm_remove' => 'Yes, remove it!',
        'cancel' => 'Cancel',
        'fee_category_added' => 'Fee Category added successfully!',
        'fee_category_updated' => 'Fee Category updated successfully!',
        'fee_category_deleted' => 'Fee Category deleted successfully!',
        'cannot_delete_fee_salary' => 'Cannot delete \'Fee Salary\' category',
        'section_added' => 'Section added successfully!',
        'section_updated' => 'Section updated successfully!',
        'section_deleted' => 'Section deleted! Fee salary fees are protected.',
        'section_fee_added' => 'Section fee added successfully!',
        'section_fee_exists' => 'Section fee already exists',
        'section_fee_updated' => 'Section fee updated successfully!',
        'section_fee_removed' => 'Section fee removed successfully!',
        'cannot_remove_fee_salary' => 'Cannot remove Fee Salary'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة الرسوم والأقسام',
        'back_to_fees' => 'العودة إلى الرسوم',
        'fee_categories' => 'فئات الرسوم',
        'sections' => 'الأقسام',
        'section_fees' => 'رسوم الأقسام',
        'add_fee_category' => 'إضافة فئة رسوم',
        'add_section' => 'إضافة قسم',
        'add_section_fee' => 'إضافة رسوم قسم',
        'id' => 'الرقم',
        'name' => 'الاسم',
        'description' => 'الوصف',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'protected' => 'محمية',
        'editable' => 'قابلة للتعديل',
        'payable' => 'قابل للدفع',
        'yes' => 'نعم',
        'no' => 'لا',
        'active' => 'نشط',
        'amount' => 'المبلغ',
        'save_fee_category' => 'حفظ فئة الرسوم',
        'save_section' => 'حفظ القسم',
        'save_section_fee' => 'حفظ رسوم القسم',
        'select_fee_category' => 'اختر فئة الرسوم',
        'select_section' => 'اختر القسم',
        'payable_section_note' => 'قسم قابل للدفع (يضيف تلقائياً راتب الرسوم)',
        'delete_fee_category' => 'حذف فئة الرسوم؟',
        'delete_fee_confirmation' => 'هل أنت متأكد أنك تريد حذف <b>{name}</b>؟<br><br><small class="text-muted">سيؤدي هذا أيضاً إلى إزالة جميع رسوم الأقسام المرتبطة بها.</small>',
        'delete_section' => 'حذف القسم؟',
        'delete_section_confirmation' => 'هل أنت متأكد أنك تريد حذف <b>{name}</b>؟<br><br><small class="text-muted">سيتم حماية رسوم راتب الرسوم.</small>',
        'remove_section_fee' => 'إزالة رسوم القسم؟',
        'remove_section_fee_confirmation' => 'هل أنت متأكد أنك تريد إزالة <b>{name}</b> من هذا القسم؟',
        'confirm_delete' => 'نعم، احذفه!',
        'confirm_remove' => 'نعم، أزله!',
        'cancel' => 'إلغاء',
        'fee_category_added' => 'تمت إضافة فئة الرسوم بنجاح!',
        'fee_category_updated' => 'تم تحديث فئة الرسوم بنجاح!',
        'fee_category_deleted' => 'تم حذف فئة الرسوم بنجاح!',
        'cannot_delete_fee_salary' => 'لا يمكن حذف فئة \'راتب الرسوم\'',
        'section_added' => 'تمت إضافة القسم بنجاح!',
        'section_updated' => 'تم تحديث القسم بنجاح!',
        'section_deleted' => 'تم حذف القسم! تمت حماية رسوم راتب الرسوم.',
        'section_fee_added' => 'تمت إضافة رسوم القسم بنجاح!',
        'section_fee_exists' => 'رسوم القسم موجودة بالفعل',
        'section_fee_updated' => 'تم تحديث رسوم القسم بنجاح!',
        'section_fee_removed' => 'تمت إزالة رسوم القسم بنجاح!',
        'cannot_remove_fee_salary' => 'لا يمكن إزالة راتب الرسوم'
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

// ---------- Theme ----------
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1;
$isDark = $themeMode == 0;

// ---------- Handle POST ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $id = intval($_POST['id'] ?? 0);

    // ---------- Fee Categories ----------
    if ($action === 'add_fee') {
        $name = trim($_POST['name']);
        $desc = trim($_POST['description']);
        $stmt = $conn->prepare("INSERT INTO fee_categories (name, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $desc);
        $stmt->execute();
        $stmt->close();
        $msg = t('fee_category_added');
    }

    if ($action === 'edit_fee') {
        $desc = trim($_POST['description']);
        $current = $conn->query("SELECT name FROM fee_categories WHERE id=$id")->fetch_assoc();
        if (strtolower($current['name']) === 'fee salary') {
            $stmt = $conn->prepare("UPDATE fee_categories SET description=? WHERE id=?");
            $stmt->bind_param("si", $desc, $id);
        } else {
            $name = trim($_POST['name']);
            $stmt = $conn->prepare("UPDATE fee_categories SET name=?, description=? WHERE id=?");
            $stmt->bind_param("ssi", $name, $desc, $id);
        }
        $stmt->execute(); $stmt->close();
        $msg = t('fee_category_updated');
    }

    if ($action === 'delete_fee') {
        $current = $conn->query("SELECT name FROM fee_categories WHERE id=$id")->fetch_assoc();
        if (strtolower($current['name']) !== 'fee salary') {
            $conn->query("DELETE FROM fee_categories WHERE id=$id");
            $conn->query("DELETE FROM section_fees WHERE fee_category_id=$id");
            $msg = t('fee_category_deleted');
        } else {
            $msg = t('cannot_delete_fee_salary');
        }
    }

    // ---------- Sections ----------
    if ($action === 'add_section') {
        $name = trim($_POST['name']);
        $payable = isset($_POST['payable']) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO sections (name,payable) VALUES (?,?)");
        $stmt->bind_param("si", $name, $payable);
        $stmt->execute();
        $new_section_id = $stmt->insert_id;
        $stmt->close();

        // If payable, auto assign Fee Salary
        if ($payable) {
            $fee_salary = $conn->query("SELECT id FROM fee_categories WHERE LOWER(name)='fee salary'")->fetch_assoc();
            if ($fee_salary) {
                $fs_id = $fee_salary['id'];
                $conn->query("INSERT INTO section_fees (fee_category_id, section_id, amount) VALUES ($fs_id,$new_section_id,0)");
            }
        }
        $msg = t('section_added');
    }

    if ($action === 'edit_section') {
        $name = trim($_POST['name']);
        $payable = isset($_POST['payable']) ? 1 : 0;
        $stmt = $conn->prepare("UPDATE sections SET name=?, payable=? WHERE id=?");
        $stmt->bind_param("sii", $name,$payable,$id);
        $stmt->execute(); $stmt->close();
        $msg = t('section_updated');
    }

    if ($action === 'delete_section') {
        $conn->query("DELETE FROM section_fees WHERE section_id=$id AND fee_category_id NOT IN (SELECT id FROM fee_categories WHERE LOWER(name)='fee salary')");
        $conn->query("DELETE FROM sections WHERE id=$id");
        $msg = t('section_deleted');
    }

    // ---------- Section Fees ----------
    if ($action === 'add_section_fee') {
        $fee_id = intval($_POST['fee_category_id']);
        $section_id = intval($_POST['section_id']);
        $amount = floatval($_POST['amount']);
        $exists = $conn->query("SELECT id FROM section_fees WHERE fee_category_id=$fee_id AND section_id=$section_id")->fetch_assoc();
        if (!$exists) {
            $stmt = $conn->prepare("INSERT INTO section_fees (fee_category_id, section_id, amount) VALUES (?,?,?)");
            $stmt->bind_param("iid",$fee_id,$section_id,$amount);
            $stmt->execute(); $stmt->close();
            $msg = t('section_fee_added');
        } else { $msg = t('section_fee_exists'); }
    }

    if ($action === 'edit_section_fee') {
        $sf_id = intval($_POST['section_fee_id']);
        $amount = floatval($_POST['amount']);
        $stmt = $conn->prepare("UPDATE section_fees SET amount=? WHERE id=?");
        $stmt->bind_param("di",$amount,$sf_id);
        $stmt->execute(); $stmt->close();
        $msg = t('section_fee_updated');
    }

    if ($action === 'delete_section_fee') {
        $sf_id = intval($_POST['section_fee_id']);
        $sf = $conn->query("SELECT fc.name AS fee_name FROM section_fees sf JOIN fee_categories fc ON fc.id=sf.fee_category_id WHERE sf.id=$sf_id")->fetch_assoc();
        if (strtolower($sf['fee_name']) !== 'fee salary') {
            $conn->query("DELETE FROM section_fees WHERE id=$sf_id");
            $msg = t('section_fee_removed');
        } else { $msg = t('cannot_remove_fee_salary'); }
    }

    header("Location: ".$_SERVER['PHP_SELF']."?msg=".urlencode($msg));
    exit;
}

// ---------- Fetch Data ----------
$fee_categories = $conn->query("SELECT * FROM fee_categories ORDER BY name ASC");
$sections = $conn->query("SELECT * FROM sections ORDER BY name ASC");
$section_fees = $conn->query("
    SELECT sf.id AS sf_id, sf.amount, sf.section_id, sf.fee_category_id, s.name AS section_name, s.payable, fc.name AS fee_name
    FROM section_fees sf
    JOIN sections s ON s.id=sf.section_id
    JOIN fee_categories fc ON fc.id=sf.fee_category_id
    ORDER BY fc.name, s.name
");
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="<?= $isDark ? 'dark' : '' ?>">
    <div class="container">
        <!-- Back Button -->
        <div class="header">
            <div class="header-left">
                <h1><i class="fas fa-cog"></i> <?= t('page_title') ?></h1>
                <a href="fees.php" class="back-btn">
                    <i class="fas fa-arrow-left"></i> <?= t('back_to_fees') ?>
                </a>
            </div>
        </div>

        <!-- Quick Stats -->
        <div class="stats-grid">
            <div class="stat-card stat-1">
                <div class="stat-icon">
                    <i class="fas fa-tags"></i>
                </div>
                <div class="stat-content">
                    <h3 id="feeCount">0</h3>
                    <p><?= t('fee_categories') ?></p>
                </div>
            </div>
            <div class="stat-card stat-2">
                <div class="stat-icon">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div class="stat-content">
                    <h3 id="sectionCount">0</h3>
                    <p><?= t('sections') ?></p>
                </div>
            </div>
            <div class="stat-card stat-3">
                <div class="stat-icon">
                    <i class="fas fa-money-bill-wave"></i>
                </div>
                <div class="stat-content">
                    <h3 id="sectionFeeCount">0</h3>
                    <p><?= t('section_fees') ?></p>
                </div>
            </div>
        </div>

        <!-- Tabs Navigation -->
        <div class="content-tabs">
            <button class="tab-btn active" onclick="showTab('fee-categories')">
                <i class="fas fa-tags"></i> <?= t('fee_categories') ?>
            </button>
            <button class="tab-btn" onclick="showTab('sections')">
                <i class="fas fa-layer-group"></i> <?= t('sections') ?>
            </button>
            <button class="tab-btn" onclick="showTab('section-fees')">
                <i class="fas fa-money-bill-wave"></i> <?= t('section_fees') ?>
            </button>
        </div>

        <!-- Fee Categories Section -->
        <div class="data-section active" id="fee-categories-section">
            <div class="section-header">
                <h3><i class="fas fa-tags"></i> <?= t('fee_categories') ?></h3>
                <button class="action-btn" onclick="openModal('feeModal')">
                    <i class="fas fa-plus"></i> <?= t('add_fee_category') ?>
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><?= t('id') ?></th>
                            <th><?= t('name') ?></th>
                            <th><?= t('description') ?></th>
                            <th><?= t('status') ?></th>
                            <th><?= t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($fc = $fee_categories->fetch_assoc()):
                        $isSalary = strtolower($fc['name']) === 'fee salary';
                        ?>
                        <tr>
                            <td>#<?= $fc['id'] ?></td>
                            <td><strong><?= htmlspecialchars($fc['name']) ?></strong></td>
                            <td><?= htmlspecialchars($fc['description']) ?: '-' ?></td>
                            <td>
                                <?php if($isSalary): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-lock"></i> <?= t('protected') ?>
                                </span>
                                <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> <?= t('editable') ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editFee(<?= $fc['id'] ?>, '<?= htmlspecialchars($fc['name'], ENT_QUOTES) ?>', '<?= htmlspecialchars($fc['description'], ENT_QUOTES) ?>', <?= $isSalary ? 'true' : 'false' ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(!$isSalary): ?>
                                    <button class="btn-icon btn-delete" onclick="confirmDeleteFee(<?= $fc['id'] ?>, '<?= htmlspecialchars($fc['name'], ENT_QUOTES) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Sections Section -->
        <div class="data-section" id="sections-section">
            <div class="section-header">
                <h3><i class="fas fa-layer-group"></i> <?= t('sections') ?></h3>
                <button class="action-btn" onclick="openModal('sectionModal')">
                    <i class="fas fa-plus"></i> <?= t('add_section') ?>
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><?= t('id') ?></th>
                            <th><?= t('name') ?></th>
                            <th><?= t('payable') ?></th>
                            <th><?= t('status') ?></th>
                            <th><?= t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($s = $sections->fetch_assoc()): ?>
                        <tr>
                            <td>#<?= $s['id'] ?></td>
                            <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                            <td>
                                <?php if($s['payable'] == 1): ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> <?= t('yes') ?>
                                </span>
                                <?php else: ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-times-circle"></i> <?= t('no') ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td><?= t('active') ?></td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editSection(<?= $s['id'] ?>, '<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>', <?= $s['payable'] ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn-icon btn-delete" onclick="confirmDeleteSection(<?= $s['id'] ?>, '<?= htmlspecialchars($s['name'], ENT_QUOTES) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Section Fees Section -->
        <div class="data-section" id="section-fees-section">
            <div class="section-header">
                <h3><i class="fas fa-money-bill-wave"></i> <?= t('section_fees') ?></h3>
                <button class="action-btn" onclick="openModal('sectionFeeModal')">
                    <i class="fas fa-plus"></i> <?= t('add_section_fee') ?>
                </button>
            </div>
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th><?= t('id') ?></th>
                            <th><?= t('fee_categories') ?></th>
                            <th><?= t('sections') ?></th>
                            <th><?= t('amount') ?></th>
                            <th><?= t('status') ?></th>
                            <th><?= t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while($sf = $section_fees->fetch_assoc()):
                        $isSalary = strtolower($sf['fee_name']) === 'fee salary';
                        ?>
                        <tr>
                            <td>#<?= $sf['sf_id'] ?></td>
                            <td><strong><?= htmlspecialchars($sf['fee_name']) ?></strong></td>
                            <td><?= htmlspecialchars($sf['section_name']) ?></td>
                            <td>$<?= number_format($sf['amount'], 2) ?></td>
                            <td>
                                <?php if($isSalary): ?>
                                <span class="badge badge-warning">
                                    <i class="fas fa-lock"></i> <?= t('protected') ?>
                                </span>
                                <?php else: ?>
                                <span class="badge badge-success">
                                    <i class="fas fa-check-circle"></i> <?= t('editable') ?>
                                </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button class="btn-icon btn-edit" onclick="editSectionFee(<?= $sf['sf_id'] ?>, <?= $sf['fee_category_id'] ?>, <?= $sf['section_id'] ?>, <?= $sf['amount'] ?>, <?= $isSalary ? 'true' : 'false' ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <?php if(!$isSalary): ?>
                                    <button class="btn-icon btn-delete" onclick="confirmDeleteSectionFee(<?= $sf['sf_id'] ?>, '<?= htmlspecialchars($sf['fee_name'], ENT_QUOTES) ?>')">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Message Toast -->
        <?php if(isset($_GET['msg'])): ?>
        <div class="toast" id="messageToast">
            <i class="fas fa-check-circle"></i>
            <span><?= htmlspecialchars($_GET['msg']) ?></span>
        </div>
        <?php endif; ?>
    </div>

    <!-- Fee Modal -->
    <div class="modal-overlay" id="feeModal">
        <div class="modal">
            <button class="modal-close" onclick="closeModal('feeModal')">&times;</button>
            <div class="modal-header">
                <h3><i class="fas fa-tags"></i> <?= t('fee_categories') ?></h3>
            </div>
            <form id="feeForm" method="POST">
                <input type="hidden" name="id" id="fee_id">
                <input type="hidden" name="action" id="fee_action" value="add_fee">
                <div class="form-group">
                    <label for="fee_name"><i class="fas fa-heading"></i> <?= t('name') ?></label>
                    <input type="text" name="name" id="fee_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="fee_description"><i class="fas fa-align-left"></i> <?= t('description') ?></label>
                    <textarea name="description" id="fee_description" class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="modal-submit">
                    <i class="fas fa-save"></i> <?= t('save_fee_category') ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Section Modal -->
    <div class="modal-overlay" id="sectionModal">
        <div class="modal">
            <button class="modal-close" onclick="closeModal('sectionModal')">&times;</button>
            <div class="modal-header">
                <h3><i class="fas fa-layer-group"></i> <?= t('sections') ?></h3>
            </div>
            <form id="sectionForm" method="POST">
                <input type="hidden" name="id" id="section_id">
                <input type="hidden" name="action" id="section_action" value="add_section">
                <div class="form-group">
                    <label for="section_name"><i class="fas fa-heading"></i> <?= t('name') ?></label>
                    <input type="text" name="name" id="section_name" class="form-control" required>
                </div>
                <div class="checkbox-group">
                    <input type="checkbox" name="payable" id="section_payable" value="1" checked>
                    <label for="section_payable"><?= t('payable_section_note') ?></label>
                </div>
                <button type="submit" class="modal-submit">
                    <i class="fas fa-save"></i> <?= t('save_section') ?>
                </button>
            </form>
        </div>
    </div>

    <!-- Section Fee Modal -->
    <div class="modal-overlay" id="sectionFeeModal">
        <div class="modal">
            <button class="modal-close" onclick="closeModal('sectionFeeModal')">&times;</button>
            <div class="modal-header">
                <h3><i class="fas fa-money-bill-wave"></i> <?= t('section_fees') ?></h3>
            </div>
            <form id="sectionFeeForm" method="POST">
                <input type="hidden" name="section_fee_id" id="sf_id">
                <input type="hidden" name="action" id="sf_action" value="add_section_fee">
                <div class="form-group">
                    <label for="sf_fee_id"><i class="fas fa-tag"></i> <?= t('fee_categories') ?></label>
                    <select name="fee_category_id" id="sf_fee_id" class="form-control" required>
                        <option value=""><?= t('select_fee_category') ?></option>
                        <?php 
                        $fee_categories2 = $conn->query("SELECT * FROM fee_categories ORDER BY name");
                        while($fc2 = $fee_categories2->fetch_assoc()): 
                        ?>
                        <option value="<?= $fc2['id'] ?>"><?= htmlspecialchars($fc2['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sf_section_id"><i class="fas fa-layer-group"></i> <?= t('sections') ?></label>
                    <select name="section_id" id="sf_section_id" class="form-control" required>
                        <option value=""><?= t('select_section') ?></option>
                        <?php 
                        $sections2 = $conn->query("SELECT * FROM sections ORDER BY name");
                        while($s2 = $sections2->fetch_assoc()): 
                        ?>
                        <option value="<?= $s2['id'] ?>"><?= htmlspecialchars($s2['name']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="sf_amount"><i class="fas fa-dollar-sign"></i> <?= t('amount') ?></label>
                    <input type="number" step="0.01" name="amount" id="sf_amount" class="form-control" required>
                </div>
                <button type="submit" class="modal-submit">
                    <i class="fas fa-save"></i> <?= t('save_section_fee') ?>
                </button>
            </form>
        </div>
    </div>

    <script>
        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            // Update stats
            updateStats();
            
            // Show message toast
            const toast = document.getElementById('messageToast');
            if(toast) {
                toast.classList.add('show');
                setTimeout(() => {
                    toast.classList.remove('show');
                    setTimeout(() => toast.remove(), 300);
                }, 5000);
            }
        });

        // Stats counter animation
        function updateStats() {
            const feeCount = document.querySelectorAll('#fee-categories-section tbody tr').length;
            const sectionCount = document.querySelectorAll('#sections-section tbody tr').length;
            const sectionFeeCount = document.querySelectorAll('#section-fees-section tbody tr').length;
            
            animateCounter('feeCount', feeCount);
            animateCounter('sectionCount', sectionCount);
            animateCounter('sectionFeeCount', sectionFeeCount);
        }

        function animateCounter(elementId, target) {
            const element = document.getElementById(elementId);
            if(!element) return;
            
            let current = 0;
            const increment = target / 50;
            const timer = setInterval(() => {
                current += increment;
                if(current >= target) {
                    current = target;
                    clearInterval(timer);
                }
                element.textContent = Math.floor(current);
            }, 20);
        }

        // Tab Navigation
        function showTab(tabName) {
            // Update tabs
            document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            // Update sections
            document.querySelectorAll('.data-section').forEach(section => {
                section.classList.remove('active');
            });
            document.getElementById(tabName + '-section').classList.add('active');
        }

        // Modal Functions
        function openModal(modalId) {
            document.getElementById(modalId).classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        function closeModal(modalId) {
            document.getElementById(modalId).classList.remove('active');
            document.body.style.overflow = 'auto';
        }

        // Edit Functions
        function editFee(id, name, desc, isSalary) {
            document.getElementById('fee_id').value = id;
            document.getElementById('fee_name').value = name;
            document.getElementById('fee_name').disabled = isSalary;
            document.getElementById('fee_description').value = desc;
            document.getElementById('fee_action').value = 'edit_fee';
            openModal('feeModal');
        }

        function editSection(id, name, payable) {
            document.getElementById('section_id').value = id;
            document.getElementById('section_name').value = name;
            document.getElementById('section_payable').checked = payable == 1;
            document.getElementById('section_action').value = 'edit_section';
            openModal('sectionModal');
        }

        function editSectionFee(sf_id, fee_id, section_id, amount, isSalary) {
            document.getElementById('sf_id').value = sf_id;
            document.getElementById('sf_fee_id').value = fee_id;
            document.getElementById('sf_section_id').value = section_id;
            document.getElementById('sf_amount').value = amount;
            document.getElementById('sf_action').value = 'edit_section_fee';
            document.getElementById('sf_section_id').disabled = isSalary;
            openModal('sectionFeeModal');
        }

        // Confirmation Dialogs
        function confirmDeleteFee(id, name) {
            Swal.fire({
                title: '<?= t('delete_fee_category') ?>',
                html: `<?= str_replace('{name}', '${name}', t('delete_fee_confirmation')) ?>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<?= t('confirm_delete') ?>',
                cancelButtonText: '<?= t('cancel') ?>',
                background: document.body.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.body.classList.contains('dark') ? '#e2e8f0' : '#334155'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="id" value="${id}">
                        <input type="hidden" name="action" value="delete_fee">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmDeleteSection(id, name) {
            Swal.fire({
                title: '<?= t('delete_section') ?>',
                html: `<?= str_replace('{name}', '${name}', t('delete_section_confirmation')) ?>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<?= t('confirm_delete') ?>',
                cancelButtonText: '<?= t('cancel') ?>',
                background: document.body.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.body.classList.contains('dark') ? '#e2e8f0' : '#334155'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="id" value="${id}">
                        <input type="hidden" name="action" value="delete_section">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        function confirmDeleteSectionFee(id, name) {
            Swal.fire({
                title: '<?= t('remove_section_fee') ?>',
                html: `<?= str_replace('{name}', '${name}', t('remove_section_fee_confirmation')) ?>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                cancelButtonColor: '#6b7280',
                confirmButtonText: '<?= t('confirm_remove') ?>',
                cancelButtonText: '<?= t('cancel') ?>',
                background: document.body.classList.contains('dark') ? '#1e293b' : '#ffffff',
                color: document.body.classList.contains('dark') ? '#e2e8f0' : '#334155'
            }).then((result) => {
                if (result.isConfirmed) {
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.innerHTML = `
                        <input type="hidden" name="section_fee_id" value="${id}">
                        <input type="hidden" name="action" value="delete_section_fee">
                    `;
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }

        // Close modals on ESC key
        document.addEventListener('keydown', function(e) {
            if(e.key === 'Escape') {
                document.querySelectorAll('.modal-overlay.active').forEach(modal => {
                    modal.classList.remove('active');
                });
                document.body.style.overflow = 'auto';
            }
        });

        // Close modals when clicking outside
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('click', function(e) {
                if(e.target === this) {
                    this.classList.remove('active');
                    document.body.style.overflow = 'auto';
                }
            });
        });

        // Reset forms when modal closes
        document.querySelectorAll('.modal-overlay').forEach(overlay => {
            overlay.addEventListener('transitionend', function(e) {
                if(!this.classList.contains('active')) {
                    this.querySelectorAll('form').forEach(form => {
                        if(form.id !== 'feeForm' && form.id !== 'sectionForm' && form.id !== 'sectionFeeForm') {
                            form.reset();
                        }
                    });
                }
            });
        });
    </script>
</body>
</html>

   <style>
        :root {
            --primary: #7c3aed;
            --primary-light: #8b5cf6;
            --secondary: #0ea5e9;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --info: #3b82f6;
            --dark-bg: #0f172a;
            --dark-card: #1e293b;
            --dark-border: #334155;
            --dark-text: #e2e8f0;
            --light-bg: #f8fafc;
            --light-card: #ffffff;
            --light-border: #e2e8f0;
            --light-text: #334155;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            --radius: 12px;
            --radius-sm: 8px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        }

        body {
            background: linear-gradient(135deg, var(--light-bg) 0%, #f1f5f9 100%);
            color: var(--light-text);
            min-height: 100vh;
            padding: 20px;
            transition: var(--transition);
        }

        body.dark {
            background: linear-gradient(135deg, var(--dark-bg) 0%, #1e293b 100%);
            color: var(--dark-text);
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid;
            border-image: linear-gradient(90deg, var(--primary), var(--secondary)) 1;
        }

        .header-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .header-left h1 {
            font-size: 32px;
            font-weight: 800;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .header-left h1 i {
            font-size: 36px;
        }

        .theme-toggle {
            background: var(--light-card);
            border: 2px solid var(--light-border);
            color: var(--light-text);
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: var(--shadow);
        }

        .dark .theme-toggle {
            background: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }

        .theme-toggle:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        /* Quick Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }

        .stat-card {
            background: var(--light-card);
            border-radius: var(--radius);
            padding: 25px;
            display: flex;
            align-items: center;
            gap: 20px;
            box-shadow: var(--shadow);
            transition: var(--transition);
            border-left: 5px solid var(--primary);
        }

        .dark .stat-card {
            background: var(--dark-card);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-lg);
        }

        .stat-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-1 .stat-icon { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .stat-2 .stat-icon { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .stat-3 .stat-icon { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

        .stat-content h3 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .stat-content p {
            font-size: 14px;
            opacity: 0.8;
        }

        /* Main Content */
        .content-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 30px;
            border-bottom: 2px solid var(--light-border);
            padding-bottom: 10px;
            flex-wrap: wrap;
        }

        .dark .content-tabs {
            border-bottom-color: var(--dark-border);
        }

        .tab-btn {
            padding: 12px 24px;
            background: none;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 16px;
            font-weight: 600;
            color: var(--light-text);
            cursor: pointer;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .dark .tab-btn {
            color: var(--dark-text);
        }

        .tab-btn:hover {
            background: rgba(124, 58, 237, 0.1);
        }

        .tab-btn.active {
            background: var(--primary);
            color: white;
        }

        .tab-content {
            display: none;
            animation: fadeIn 0.5s ease;
        }

        .tab-content.active {
            display: block;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Data Tables */
        .data-section {
            background: var(--light-card);
            border-radius: var(--radius);
            overflow: hidden;
            box-shadow: var(--shadow-lg);
            margin-bottom: 30px;
            display: none;
        }

        .dark .data-section {
            background: var(--dark-card);
        }

        .data-section.active {
            display: block;
        }

        .section-header {
            padding: 25px;
            border-bottom: 1px solid var(--light-border);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .dark .section-header {
            border-bottom-color: var(--dark-border);
        }

        .section-header h3 {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .action-btn {
            padding: 12px 24px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
        }

        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Table Styles */
        .table-container {
            overflow-x: auto;
            padding: 0 25px 25px;
        }

        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            min-width: 800px;
        }

        thead {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
        }

        th {
            padding: 18px 20px;
            text-align: left;
            font-weight: 600;
            color: white;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        th:first-child {
            border-radius: var(--radius-sm) 0 0 0;
        }

        th:last-child {
            border-radius: 0 var(--radius-sm) 0 0;
        }

        td {
            padding: 20px;
            border-bottom: 1px solid var(--light-border);
            font-size: 15px;
        }

        .dark td {
            border-bottom-color: var(--dark-border);
        }

        tbody tr {
            transition: var(--transition);
        }

        tbody tr:hover {
            background: rgba(124, 58, 237, 0.05);
        }

        /* Status Badges */
        .badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-success {
            background: #d1fae5;
            color: #065f46;
        }

        .dark .badge-success {
            background: #064e3b;
            color: #a7f3d0;
        }

        .badge-warning {
            background: #fef3c7;
            color: #92400e;
        }

        .dark .badge-warning {
            background: #78350f;
            color: #fde68a;
        }

        /* Action Buttons */
        .table-actions {
            display: flex;
            gap: 8px;
        }

        .btn-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: none;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: var(--transition);
        }

        .btn-edit {
            background: rgba(59, 130, 246, 0.1);
            color: var(--info);
        }

        .btn-edit:hover {
            background: var(--info);
            color: white;
            transform: scale(1.1);
        }

        .btn-delete {
            background: rgba(239, 68, 68, 0.1);
            color: var(--danger);
        }

        .btn-delete:hover {
            background: var(--danger);
            color: white;
            transform: scale(1.1);
        }

        /* Modals */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            opacity: 0;
            visibility: hidden;
            transition: var(--transition);
            backdrop-filter: blur(5px);
        }

        .modal-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .modal {
            background: var(--light-card);
            border-radius: var(--radius);
            padding: 40px;
            width: 90%;
            max-width: 500px;
            transform: translateY(30px);
            transition: var(--transition);
            position: relative;
            box-shadow: var(--shadow-lg);
        }

        .dark .modal {
            background: var(--dark-card);
        }

        .modal-overlay.active .modal {
            transform: translateY(0);
        }

        .modal-close {
            position: absolute;
            top: 20px;
            right: 20px;
            background: none;
            border: none;
            font-size: 24px;
            color: var(--light-text);
            cursor: pointer;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: var(--transition);
        }

        .modal-close:hover {
            background: rgba(0, 0, 0, 0.1);
        }

        .modal-header {
            margin-bottom: 30px;
        }

        .modal-header h3 {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--light-text);
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dark .form-group label {
            color: var(--dark-text);
        }

        .form-control {
            width: 100%;
            padding: 14px 16px;
            border: 2px solid var(--light-border);
            border-radius: var(--radius-sm);
            background: var(--light-bg);
            color: var(--light-text);
            font-size: 15px;
            transition: var(--transition);
        }

        .dark .form-control {
            background: var(--dark-bg);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(124, 58, 237, 0.1);
        }

        .form-control[disabled] {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
            margin: 20px 0;
        }

        .checkbox-group input[type="checkbox"] {
            width: 20px;
            height: 20px;
            accent-color: var(--primary);
        }

        .modal-submit {
            width: 100%;
            padding: 16px;
            background: linear-gradient(90deg, var(--primary), var(--primary-light));
            color: white;
            border: none;
            border-radius: var(--radius-sm);
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            transition: var(--transition);
            margin-top: 10px;
        }

        .modal-submit:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow);
        }

        /* Message Toast */
        .toast {
            position: fixed;
            bottom: 30px;
            right: 30px;
            padding: 20px 30px;
            background: linear-gradient(90deg, var(--success), #34d399);
            color: white;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: var(--shadow-lg);
            z-index: 1001;
            transform: translateY(100px);
            opacity: 0;
            transition: var(--transition);
        }

        .toast.show {
            transform: translateY(0);
            opacity: 1;
        }

        .toast i {
            font-size: 24px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 20px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .section-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .action-btn {
                width: 100%;
                justify-content: center;
            }

            .modal {
                width: 95%;
                padding: 30px 20px;
            }

            table {
                min-width: 600px;
            }

            .table-actions {
                flex-direction: column;
            }

            .btn-icon {
                width: 32px;
                height: 32px;
            }
        }

        @media (max-width: 480px) {
            .header-left h1 {
                font-size: 24px;
            }

            .tab-btn {
                padding: 10px 16px;
                font-size: 14px;
            }

            th, td {
                padding: 12px 15px;
            }

            .stat-card {
                padding: 20px;
            }

            .stat-icon {
                width: 50px;
                height: 50px;
                font-size: 20px;
            }
        }

        /* Loading Animation */
        .loading {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 2px solid #fff;
            border-top-color: transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Back Button */
        .back-btn {
            /* position: relative; */
            top: 20px;
            left: 20px;
            background: var(--light-card);
            border: 2px solid var(--light-border);
            color: var(--light-text);
            padding: 10px 20px;
            border-radius: var(--radius-sm);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: var(--transition);
            z-index: 100;
        }

        .dark .back-btn {
            background: var(--dark-card);
            border-color: var(--dark-border);
            color: var(--dark-text);
        }

        .back-btn:hover {
            transform: translateX(-5px);
            background: var(--primary);
            color: white;
            border-color: var(--primary);
        }
    </style>