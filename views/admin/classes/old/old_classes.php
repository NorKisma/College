<?php
declare(strict_types=1);

require_once __DIR__ . '/../../../../config/db.php';
require_once __DIR__ . '/../../../../config/sessions.php';
require_once __DIR__ . '/../../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Old Classes Management | School System',
        'page_subtitle' => 'View and manage graduated/inactive classes',
        'back_to_active_classes' => 'Back to Active Classes',
        'total_classes' => 'Total Classes',
        'showing' => 'Showing',
        'on_this_page' => 'on this page',
        'fully_graduated' => 'Fully Graduated',
        'classes_label' => 'classes',
        'partially_graduated' => 'Partially Graduated',
        'filter_classes' => 'Filter Classes',
        'search_classes' => 'Search Classes',
        'enter_class_name' => 'Enter class name...',
        'section' => 'Section',
        'all_sections' => 'All Sections',
        'graduation_status' => 'Graduation Status',
        'all_classes' => 'All Classes',
        'fully_graduated_option' => 'Fully Graduated',
        'partially_graduated_option' => 'Partially Graduated',
        'apply_filters' => 'Apply Filters',
        'reset' => 'Reset',
        'classes_list' => 'Classes List',
        'page_of' => 'Page',
        'total_classes_label' => 'total classes',
        'class_name' => 'Class Name',
        'total_students' => 'Total Students',
        'alumni_count' => 'Alumni Count',
        'status' => 'Status',
        'actions' => 'Actions',
        'no_classes_found' => 'No classes found matching your filters.',
        'try_adjusting_search' => 'Try adjusting your search criteria',
        'id_label' => 'ID',
        'na' => 'N/A',
        'view' => 'View',
        'view_details' => 'View Details',
        'first_page' => 'First',
        'previous_page' => 'Previous',
        'next_page' => 'Next',
        'last_page' => 'Last',
        'unauthorized_access' => 'Unauthorized access. Admin privileges required.',
        'active_status' => 'Active',
        'partially_graduated_status' => 'Partially Graduated',
        'fully_graduated_status' => 'Fully Graduated'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة الصفوف القديمة | نظام المدرسة',
        'page_subtitle' => 'عرض وإدارة الصفوف المتخرجة/غير النشطة',
        'back_to_active_classes' => 'العودة إلى الصفوف النشطة',
        'total_classes' => 'إجمالي الصفوف',
        'showing' => 'عرض',
        'on_this_page' => 'في هذه الصفحة',
        'fully_graduated' => 'متخرج بالكامل',
        'classes_label' => 'صفوف',
        'partially_graduated' => 'متخرج جزئياً',
        'filter_classes' => 'تصفية الصفوف',
        'search_classes' => 'بحث عن الصفوف',
        'enter_class_name' => 'أدخل اسم الصف...',
        'section' => 'القسم',
        'all_sections' => 'جميع الأقسام',
        'graduation_status' => 'حالة التخرج',
        'all_classes' => 'جميع الصفوف',
        'fully_graduated_option' => 'متخرج بالكامل',
        'partially_graduated_option' => 'متخرج جزئياً',
        'apply_filters' => 'تطبيق الفلاتر',
        'reset' => 'إعادة تعيين',
        'classes_list' => 'قائمة الصفوف',
        'page_of' => 'صفحة',
        'total_classes_label' => 'إجمالي الصفوف',
        'class_name' => 'اسم الصف',
        'total_students' => 'إجمالي الطلاب',
        'alumni_count' => 'عدد الخريجين',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'no_classes_found' => 'لم يتم العثور على صفوف تطابق فلاتر البحث الخاصة بك.',
        'try_adjusting_search' => 'حاول تعديل معايير البحث',
        'id_label' => 'الرقم',
        'na' => 'غير متاح',
        'view' => 'عرض',
        'view_details' => 'عرض التفاصيل',
        'first_page' => 'الأول',
        'previous_page' => 'السابق',
        'next_page' => 'التالي',
        'last_page' => 'الأخير',
        'unauthorized_access' => 'وصول غير مصرح به. مطلوب صلاحيات المدير.',
        'active_status' => 'نشط',
        'partially_graduated_status' => 'متخرج جزئياً',
        'fully_graduated_status' => 'متخرج بالكامل',

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

/**
 * Old Classes Management Dashboard
 * Admin-only interface to view and manage graduated/inactive classes
 */

// Authentication and Authorization Check
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) {
    header('Location: ../../auth/login.php');
    exit;
}
// Determine theme mode //
$theme_mode = 1; // default light

$themeRes = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1");

if($themeRes && $row = $themeRes->fetch_assoc()){
    $theme_mode = (int)$row['mode']; // 1 = light, 0 = dark
}
/**
 * Get current user's role
 */
function getUserRole(mysqli $conn, int $userId): string
{
    $stmt = $conn->prepare("
        SELECT r.name AS role_name 
        FROM users u 
        JOIN roles r ON r.id = u.role_id 
        WHERE u.id = ?
    ");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
    
    return $user['role_name'] ?? '';
}

/**
 * Validate admin access
 */
function validateAdminAccess(string $role): void
{
    if ($role !== 'admin') {
        http_response_code(403);
        die('<div class="alert alert-danger m-4">' . t('unauthorized_access') . '</div>');
    }
}

/**
 * Get all sections for filter dropdown
 */
function getAllSections(mysqli $conn): array
{
    $result = $conn->query("SELECT id, name FROM sections ORDER BY name ASC");
    return $result->fetch_all(MYSQLI_ASSOC);
}

/**
 * Build SQL query for filtering classes
 */
function buildClassQuery(array $filters): array
{
    $sql = "
        SELECT 
            c.id, 
            c.name AS class_name, 
            s.name AS section_name,
            (SELECT COUNT(*) FROM class_students cs WHERE cs.class_id = c.id AND cs.is_active = 1) AS total_students,
            (SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id) AS alumni_count
        FROM classes c
        LEFT JOIN sections s ON c.section_id = s.id
        WHERE 1
    ";
    
    $params = [];
    $types = '';
    
    // Search filter
    if (!empty($filters['search'])) {
        $sql .= " AND c.name LIKE ? ";
        $params[] = "%{$filters['search']}%";
        $types .= 's';
    }
    
    // Section filter
    if ($filters['section_id'] > 0) {
        $sql .= " AND c.section_id = ? ";
        $params[] = $filters['section_id'];
        $types .= 'i';
    }
    
    // Alumni status filter
   // Graduation status filter
if ($filters['alumni'] === 'full') {

    // Fully graduated = alumni == active students AND > 0
    $sql .= " AND (
        SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id
    ) > 0
    AND (
        SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id
    ) = (
        SELECT COUNT(*) FROM class_students cs
        WHERE cs.class_id = c.id AND cs.is_active = 1
    )";

} elseif ($filters['alumni'] === 'partial') {

    // Partially graduated = some alumni but not all
    $sql .= " AND (
        SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id
    ) > 0
    AND (
        SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id
    ) < (
        SELECT COUNT(*) FROM class_students cs
        WHERE cs.class_id = c.id AND cs.is_active = 1
    )";

} elseif ($filters['alumni'] === 'none') {

    // No graduations yet
    $sql .= " AND (
        SELECT COUNT(*) FROM alumni a WHERE a.class_id = c.id
    ) = 0";

}

// Graduation date range filter
if (!empty($filters['from_date'])) {
    $sql .= " AND EXISTS (
        SELECT 1 FROM alumni a2
        WHERE a2.class_id = c.id
          AND a2.graduation_date >= ?
    )";
    $params[] = $filters['from_date'];
    $types .= 's';
}

if (!empty($filters['to_date'])) {
    $sql .= " AND EXISTS (
        SELECT 1 FROM alumni a3
        WHERE a3.class_id = c.id
          AND a3.graduation_date <= ?
    )";
    $params[] = $filters['to_date'];
    $types .= 's';
}

    
    return ['sql' => $sql, 'params' => $params, 'types' => $types];
}

/**
 * Get paginated class data
 */
function getPaginatedClasses(mysqli $conn, array $filters, int $page, int $perPage): array
{
    // Build base query
    $query = buildClassQuery($filters);
    $sql = $query['sql'];
    
    // Add sorting
    $validSortColumns = ['class_name', 'section_name', 'alumni_count'];
    $sortBy = in_array($filters['sort_by'], $validSortColumns) ? $filters['sort_by'] : 'class_name';
    $sortDir = strtoupper($filters['sort_dir']) === 'DESC' ? 'DESC' : 'ASC';
    $sql .= " ORDER BY {$sortBy} {$sortDir}";
    
    // Add pagination
    $offset = ($page - 1) * $perPage;
    $sql .= " LIMIT {$perPage} OFFSET {$offset}";
    
    // Execute query
    $stmt = $conn->prepare($sql);
    if ($query['types']) {
        $stmt->bind_param($query['types'], ...$query['params']);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $classes = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    
    return $classes;
}

/**
 * Get total class count for pagination
 */
function getTotalClassCount(mysqli $conn, array $filters): int
{
    $query = buildClassQuery($filters);
    $sql = "SELECT COUNT(*) as cnt FROM classes c WHERE 1";
    
    // Reuse filter conditions
    $whereClauses = [];
    if (!empty($filters['search'])) {
        $whereClauses[] = "c.name LIKE '%{$filters['search']}%'";
    }
    if ($filters['section_id'] > 0) {
        $whereClauses[] = "c.section_id = {$filters['section_id']}";
    }
    
    if (!empty($whereClauses)) {
        $sql .= " AND " . implode(" AND ", $whereClauses);
    }
    
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    return (int)($row['cnt'] ?? 0);
}

// Get user role and validate admin access
$userRole = getUserRole($conn, $logged_in_id);
validateAdminAccess($userRole);

// Get filter parameters
$filters = [
    'search' => trim($_GET['search'] ?? ''),
    'section_id' => intval($_GET['section_id'] ?? 0),
    'alumni' => $_GET['alumni'] ?? '',
    'from_date' => $_GET['from_date'] ?? '',
    'to_date' => $_GET['to_date'] ?? '',
    'sort_by' => $_GET['sort_by'] ?? 'class_name',
    'sort_dir' => $_GET['sort_dir'] ?? 'asc'
];


// Pagination settings
$currentPage = max(intval($_GET['page'] ?? 1), 1);
$itemsPerPage = 10;

// Get data
$sections = getAllSections($conn);
$classes = getPaginatedClasses($conn, $filters, $currentPage, $itemsPerPage);
$totalClasses = getTotalClassCount($conn, $filters);
$totalPages = ceil($totalClasses / $itemsPerPage);

// Calculate pagination range for display
$paginationStart = max(1, $currentPage - 2);
$paginationEnd = min($totalPages, $currentPage + 2);

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    
    <!-- CSS Dependencies -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css" rel="stylesheet">

</head>
<body class="<?= $theme_mode === 0 ? 'dark bg-gray-900 text-gray-100' : 'bg-gradient-to-br from-indigo-50 to-blue-100 text-gray-800' ?>">
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="dashboard-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-1">
                        <i class="bi bi-archive me-2"></i><?= t('page_title') ?>
                    </h1>
                    <p class="mb-0 opacity-75"><?= t('page_subtitle') ?></p>
                </div>
                <div>
                    <a href="../classes.php" class="btn btn-light back-btn">
                        <i class="bi bi-arrow-left me-1"></i><?= t('back_to_active_classes') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Statistics Overview -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card stats-card border-start border-primary">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('total_classes') ?></h6>
                                <h3 class="mb-0"><?= $totalClasses ?></h3>
                            </div>
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people text-primary fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stats-card border-start border-success">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('showing') ?></h6>
                                <h3 class="mb-0"><?= count($classes) ?></h3>
                                <small class="text-muted"><?= t('on_this_page') ?></small>
                            </div>
                            <div class="bg-success bg-opacity-10 p-3 rounded">
                                <i class="bi bi-list-ol text-success fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stats-card border-start border-info">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('fully_graduated') ?></h6>
                                <h3 class="mb-0" id="fullyGraduatedCount">0</h3>
                                <small class="text-muted"><?= t('classes_label') ?></small>
                            </div>
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="bi bi-mortarboard text-info fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="card stats-card border-start border-warning">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="text-muted mb-1"><?= t('partially_graduated') ?></h6>
                                <h3 class="mb-0" id="partiallyGraduatedCount">0</h3>
                                <small class="text-muted"><?= t('classes_label') ?></small>
                            </div>
                            <div class="bg-warning bg-opacity-10 p-3 rounded">
                                <i class="bi bi-people text-warning fs-4"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="card filter-card mb-4">
            <div class="card-body">
                <h5 class="card-title mb-3">
                    <i class="bi bi-funnel me-2"></i><?= t('filter_classes') ?>
                </h5>
                
                <form method="GET" class="row g-3">
                    <!-- Search Input -->
                    <div class="col-md-4">
                        <label class="form-label fw-medium">
                            <i class="bi bi-search me-1"></i><?= t('search_classes') ?>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light">
                                <i class="bi bi-building"></i>
                            </span>
                            <input type="text" 
                                   name="search" 
                                   class="form-control" 
                                   placeholder="<?= t('enter_class_name') ?>" 
                                   value="<?= htmlspecialchars($filters['search']) ?>">
                        </div>
                    </div>

                    <div class="col-md-3">
    <label class="form-label fw-medium">
        From Graduation Date
    </label>
    <input type="date"
           name="from_date"
           class="form-control"
           value="<?= htmlspecialchars($filters['from_date']) ?>">
</div>

<div class="col-md-3">
    <label class="form-label fw-medium">
        To Graduation Date
    </label>
    <input type="date"
           name="to_date"
           class="form-control"
           value="<?= htmlspecialchars($filters['to_date']) ?>">
</div>

                    
                    <!-- Section Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium">
                            <i class="bi bi-layers me-1"></i><?= t('section') ?>
                        </label>
                        <select name="section_id" class="form-select">
                            <option value="0"><?= t('all_sections') ?></option>
                            <?php foreach ($sections as $section): ?>
                                <option value="<?= $section['id'] ?>" 
                                    <?= $filters['section_id'] == $section['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($section['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Alumni Status Filter -->
                    <div class="col-md-3">
                        <label class="form-label fw-medium">
                            <i class="bi bi-mortarboard me-1"></i><?= t('graduation_status') ?>
                        </label>
                       <select name="alumni" class="form-select">
    <option value=""><?= t('all_classes') ?></option>

    <option value="full" <?= $filters['alumni'] == 'full' ? 'selected' : '' ?>>
        <?= t('fully_graduated_option') ?>
    </option>

    <option value="partial" <?= $filters['alumni'] == 'partial' ? 'selected' : '' ?>>
        <?= t('partially_graduated_option') ?>
    </option>

    <option value="none" <?= $filters['alumni'] == 'none' ? 'selected' : '' ?>>
        Active / No Graduations
    </option>
</select>

                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-grid gap-2 w-100">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-filter me-1"></i><?= t('apply_filters') ?>
                            </button>
                            <a href="?" class="btn btn-outline-secondary">
                                <i class="bi bi-arrow-clockwise me-1"></i><?= t('reset') ?>
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Classes Table -->
        <div class="card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title m-0">
                        <i class="bi bi-table me-2"></i><?= t('classes_list') ?>
                    </h5>
                    <div class="text-muted small">
                        <?= t('page_of') ?> <?= $currentPage ?> <?= t('of') ?> <?= $totalPages ?> • 
                        <?= $totalClasses ?> <?= t('total_classes_label') ?>
                    </div>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover class-table">
                        <thead>
                            <tr>
                                <th>
                                    <a href="?<?= http_build_query(array_merge($filters, ['sort_by' => 'class_name', 'sort_dir' => $filters['sort_dir'] === 'asc' ? 'desc' : 'asc'])) ?>" 
                                       class="sort-link">
                                        <?= t('class_name') ?>
                                        <?php if ($filters['sort_by'] === 'class_name'): ?>
                                            <i class="bi bi-arrow-<?= $filters['sort_dir'] === 'asc' ? 'up' : 'down' ?> sort-icon ms-1"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th>
                                    <a href="?<?= http_build_query(array_merge($filters, ['sort_by' => 'section_name', 'sort_dir' => $filters['sort_dir'] === 'asc' ? 'desc' : 'asc'])) ?>" 
                                       class="sort-link">
                                        <?= t('section') ?>
                                        <?php if ($filters['sort_by'] === 'section_name'): ?>
                                            <i class="bi bi-arrow-<?= $filters['sort_dir'] === 'asc' ? 'up' : 'down' ?> sort-icon ms-1"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th><?= t('total_students') ?></th>
                                <th>
                                    <a href="?<?= http_build_query(array_merge($filters, ['sort_by' => 'alumni_count', 'sort_dir' => $filters['sort_dir'] === 'asc' ? 'desc' : 'asc'])) ?>" 
                                       class="sort-link">
                                        <?= t('alumni_count') ?>
                                        <?php if ($filters['sort_by'] === 'alumni_count'): ?>
                                            <i class="bi bi-arrow-<?= $filters['sort_dir'] === 'asc' ? 'up' : 'down' ?> sort-icon ms-1"></i>
                                        <?php endif; ?>
                                    </a>
                                </th>
                                <th><?= t('status') ?></th>
                                <th><?= t('actions') ?></th>
                            </tr>
                        </thead>
                        <tbody id="classesTableBody">
                            <?php if (empty($classes)): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox display-6 d-block mb-3"></i>
                                        <p class="mb-1"><?= t('no_classes_found') ?></p>
                                        <small><?= t('try_adjusting_search') ?></small>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php 
                                $fullyGraduated = 0;
                                $partiallyGraduated = 0;
                                ?>
                                <?php foreach ($classes as $class): ?>
                                    <?php
                                    $isFullyGraduated = $class['total_students'] > 0 && $class['total_students'] == $class['alumni_count'];
                                    $isPartiallyGraduated = $class['alumni_count'] > 0 && !$isFullyGraduated;
                                    
                                    if ($isFullyGraduated) $fullyGraduated++;
                                    if ($isPartiallyGraduated) $partiallyGraduated++;
                                    
                                    $rowClass = '';
                                    $statusBadge = '';
                                    
                                    if ($isFullyGraduated) {
                                        $rowClass = 'fully-graduated';
                                        $statusBadge = '<span class="badge bg-danger graduation-badge">' . t('fully_graduated_status') . '</span>';
                                    } elseif ($isPartiallyGraduated) {
                                        $rowClass = 'partially-graduated';
                                        $statusBadge = '<span class="badge bg-warning text-dark graduation-badge">' . t('partially_graduated_status') . '</span>';
                                    } else {
                                        $statusBadge = '<span class="badge bg-secondary graduation-badge">' . t('active_status') . '</span>';
                                    }
                                    ?>
                                    <tr class="<?= $rowClass ?>">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0">
                                                    <i class="bi bi-people-fill text-primary me-2"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <strong><?= htmlspecialchars($class['class_name']) ?></strong>
                                                    <div class="text-muted small">
                                                        <?= t('id_label') ?>: <?= $class['id'] ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light small text-dark border">
                                                <?= htmlspecialchars($class['section_name'] ?? t('na')) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-person-circle small me-2 text-muted"></i>
                                                <span class="fw-medium"><?= $class['total_students'] ?></span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-mortarboard small me-2 text-muted"></i>
                                                <span class="fw-medium"><?= $class['alumni_count'] ?></span>
                                            </div>
                                        </td>
                                        <td><?= $statusBadge ?></td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="view_class.php?id=<?= $class['id'] ?>" 
                                                   class="btn btn-outline-primary" 
                                                   title="<?= t('view_details') ?>">
                                                    <i class="bi bi-eye"></i> <?= t('view') ?>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Classes pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- First Page -->
                            <?php if ($currentPage > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => 1])) ?>">
                                        <i class="bi bi-chevron-double-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage - 1])) ?>">
                                        <i class="bi bi-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                            
                            <!-- Page Numbers -->
                            <?php for ($p = $paginationStart; $p <= $paginationEnd; $p++): ?>
                                <li class="page-item <?= $p == $currentPage ? 'active' : '' ?>">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $p])) ?>">
                                        <?= $p ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                            
                            <!-- Last Page -->
                            <?php if ($currentPage < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $currentPage + 1])) ?>">
                                        <i class="bi bi-chevron-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?<?= http_build_query(array_merge($filters, ['page' => $totalPages])) ?>">
                                        <i class="bi bi-chevron-double-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- JavaScript Dependencies -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <script>
    $(document).ready(function() {
        // Update statistics counts
        $('#fullyGraduatedCount').text(<?= $fullyGraduated ?>);
        $('#partiallyGraduatedCount').text(<?= $partiallyGraduated ?>);
        
        // Initialize DataTable (optional - if you want enhanced table features)
        $('.class-table').DataTable({
            paging: false,
            searching: false,
            info: false,
            order: [],
            columnDefs: [
                { orderable: false, targets: [2, 4, 5] } // Disable sorting on some columns
            ]
        });
        
        // Add row hover effects
        $('.class-table tbody tr').hover(
            function() {
                $(this).addClass('table-active');
            },
            function() {
                $(this).removeClass('table-active');
            }
        );
        
        // Auto-submit form on filter change (optional)
        $('select[name="alumni"], select[name="section_id"]').on('change', function() {
            $(this).closest('form').submit();
        });
    });
    </script>
</body>
</html>

<style>
/* Dark mode overrides */
body.dark {
    background-color: #000 !important;
    color: #fff !important;
}

/* Dashboard header */
body.dark .dashboard-header {
    background: linear-gradient(135deg, #222222 0%, #333333 100%);
    color: #fff !important;
}

/* Stats cards */
body.dark .stats-card {
    background-color: #111 !important;
    color: #fff !important;
}

/* Filter card */
body.dark .filter-card {
    background-color: #111 !important;
    color: #fff !important;
}

body.dark .filter-card label,
body.dark .filter-card .form-label {
    color: #fff !important;
}

/* Tables */
body.dark .class-table th,
body.dark .class-table td {
    background-color: #111 !important;
    color: #fff !important;
}

body.dark .class-table th {
    background-color: #222 !important;
}

body.dark .badge {
    color: #fff !important;
}

/* Buttons */
body.dark .btn-outline-primary,
body.dark .btn-outline-secondary,
body.dark .btn-outline-success,
body.dark .btn-outline-info,
body.dark .btn-outline-warning {
    color: #fff !important;
    border-color: #fff !important;
}

/* Pagination */
body.dark .pagination .page-link {
    color: #fff !important;
    background-color: #111 !important;
    border-color: #444 !important;
}

body.dark .pagination .page-item.active .page-link {
    background-color: #444 !important;
    border-color: #fff !important;
}

/* Inputs/selects */
body.dark .form-control,
body.dark .form-select {
    background-color: #222 !important;
    color: #fff !important;
    border-color: #444 !important;
}

body.dark .form-control::placeholder,
body.dark .form-select option ,
body.dark h6,
body.dark small,
body.dark .small{
    color: #aaa !important;
}
</style>
