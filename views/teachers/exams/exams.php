<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

// Authentication
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];

/* ===================== TRANSLATIONS ===================== */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Exams Management | Teacher Dashboard',
        'dashboard_title' => 'Exams Management',
        'dashboard_subtitle' => 'Manage, filter, and track all your exams from a single dashboard',
        'create_new_exam' => 'Create New Exam',
        'total_exams' => 'Total Exams',
        'upcoming_exams' => 'Upcoming Exams',
        'ongoing_exams' => 'Ongoing Exams',
        'completed_exams' => 'Completed Exams',
        'filter_exams' => 'Filter Exams',
        'class' => 'Class',
        'all_classes' => 'All Classes',
        'exam_type' => 'Exam Type',
        'all_types' => 'All Types',
        'midterm' => 'Midterm',
        'final' => 'Final',
        'quiz' => 'Quiz',
        'assignment' => 'Assignment',
        'date_range' => 'Date Range',
        'select_date_range' => 'Select date range',
        'search' => 'Search',
        'search_exams' => 'Search exams...',
        'apply_filters' => 'Apply Filters',
        'reset' => 'Reset',
        'exam_name' => 'Exam Name',
        'type' => 'Type',
        'class_col' => 'Class',
        'date_range_col' => 'Date Range',
        'status' => 'Status',
        'actions' => 'Actions',
        'view_details' => 'View Details',
        'edit_exam' => 'Edit Exam',
        'enter_marks' => 'Enter Marks',
        'delete_exam' => 'Delete Exam',
        'no_exams_found' => 'No exams found',
        'no_exams_message' => 'Create your first exam or adjust your filters',
        'confirm_deletion' => 'Confirm Deletion',
        'delete_confirm_message' => 'Are you sure you want to delete the exam',
        'delete_warning' => 'This action cannot be undone. All related marks and results will be permanently deleted.',
        'cancel' => 'Cancel',
        'export_data' => 'Export Data',
        'calendar_view' => 'Calendar View',
        'print_table' => 'Print Table',
        'auto_refresh' => 'Auto-refresh',
        'starts_in' => 'Starts in',
        'ends_in' => 'Ends in',
        'ended' => 'Ended',
        'days' => 'days',
        'days_ago' => 'days ago',
        'upcoming' => 'upcoming',
        'ongoing' => 'ongoing',
        'completed' => 'completed',
        'all' => 'All',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة الامتحانات | لوحة تحكم المعلم',
        'dashboard_title' => 'إدارة الامتحانات',
        'dashboard_subtitle' => 'إدارة وتصفية وتتبع جميع امتحاناتك من لوحة تحكم واحدة',
        'create_new_exam' => 'إنشاء امتحان جديد',
        'total_exams' => 'إجمالي الامتحانات',
        'upcoming_exams' => 'الامتحانات القادمة',
        'ongoing_exams' => 'الامتحانات الجارية',
        'completed_exams' => 'الامتحانات المكتملة',
        'filter_exams' => 'تصفية الامتحانات',
        'class' => 'الفصل',
        'all_classes' => 'جميع الفصول',
        'exam_type' => 'نوع الامتحان',
        'all_types' => 'جميع الأنواع',
        'midterm' => 'منتصف الفصل',
        'final' => 'نهائي',
        'quiz' => 'اختبار قصير',
        'assignment' => 'واجب',
        'date_range' => 'نطاق التاريخ',
        'select_date_range' => 'اختر نطاق التاريخ',
        'search' => 'بحث',
        'search_exams' => 'بحث في الامتحانات...',
        'apply_filters' => 'تطبيق الفلاتر',
        'reset' => 'إعادة تعيين',
        'exam_name' => 'اسم الامتحان',
        'type' => 'النوع',
        'class_col' => 'الفصل',
        'date_range_col' => 'نطاق التاريخ',
        'status' => 'الحالة',
        'actions' => 'الإجراءات',
        'view_details' => 'عرض التفاصيل',
        'edit_exam' => 'تعديل الامتحان',
        'enter_marks' => 'إدخال الدرجات',
        'delete_exam' => 'حذف الامتحان',
        'no_exams_found' => 'لم يتم العثور على امتحانات',
        'no_exams_message' => 'أنشئ أول امتحان لك أو اضبط الفلاتر',
        'confirm_deletion' => 'تأكيد الحذف',
        'delete_confirm_message' => 'هل أنت متأكد أنك تريد حذف الامتحان',
        'delete_warning' => 'لا يمكن التراجع عن هذا الإجراء. سيتم حذف جميع الدرجات والنتائج المرتبطة بشكل دائم.',
        'cancel' => 'إلغاء',
        'export_data' => 'تصدير البيانات',
        'calendar_view' => 'عرض التقويم',
        'print_table' => 'طباعة الجدول',
        'auto_refresh' => 'تحديث تلقائي',
        'starts_in' => 'يبدأ بعد',
        'ends_in' => 'ينتهي بعد',
        'ended' => 'انتهى منذ',
        'days' => 'أيام',
        'days_ago' => 'أيام مضت',
        'upcoming' => 'قادم',
        'ongoing' => 'جاري',
        'completed' => 'مكتمل',
        'all' => 'الكل',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
        'sunday' => 'الأحد'
    ],
    3 => [ // Somali
        'page_title' => 'Maamulka Imtixaanka | Dashboard-ka Macallinka',
        'dashboard_title' => 'Maamulka Imtixaanadada',
        'dashboard_subtitle' => 'Arag dhammaan imtixaankaada hal dashboard',
        'create_new_exam' => 'Ku dar Imtixaan Cusub',
        'total_exams' => 'Wadarta Imtixaanka',
        'upcoming_exams' => 'Imtixaannada soo socda',
        'ongoing_exams' => 'Imtixaannada socda',
        'completed_exams' => 'Imtixaannada Dhamaadey',
        'filter_exams' => 'Ku raadi Imtixaannada',
        'class' => 'Fasalka',
        'all_classes' => 'Dhammaan Fasallada',
        'exam_type' => 'Nooca Imtixaanka',
        'all_types' => 'Dhammaan Noocyada',
        'midterm' => 'Dhexda Fasalka',
        'final' => 'Dambe',
        'quiz' => 'Qiimeyn',
        'assignment' => 'Layli',
        'date_range' => 'Muddada Taariikhda',
        'select_date_range' => 'Dooro muddada taariikhda',
        'search' => 'Raadi',
        'search_exams' => 'Raadi imtixaanno...',
        'apply_filters' => 'Raadi',
        'reset' => 'Ka noqo',
        'exam_name' => 'Magaca Imtixaanka',
        'type' => 'Nooca',
        'class_col' => 'Fasalka',
        'date_range_col' => 'Muddada Taariikhda',
        'status' => 'Xaalada',
        'actions' => 'Ficillo',
        'view_details' => 'Faahfaahin Arag',
        'edit_exam' => 'Wax ka beddel Imtixaanka',
        'enter_marks' => 'Gali Dhibcaha',
        'delete_exam' => 'Tirtir Imtixaanka',
        'no_exams_found' => 'Imtixaan lama helin',
        'no_exams_message' => 'Abuur imtixaankaaga koowaad ama ku habboon filters',
        'confirm_deletion' => 'Xaqiijinta Tirtirka',
        'delete_confirm_message' => 'Ma hubtaa inaad rabto inaad tirtirto imtixaanka',
        'delete_warning' => 'Tallaabadan dib looma noqon karo. Dhammaan dhibcaha iyo natiijooyinka la xidhiidha ayaa si joogto ah loo tirtiri doonaa.',
        'cancel' => 'Jooji',
        'export_data' => 'Xogta u dhoof',
        'calendar_view' => 'Arag Kalandarka',
        'print_table' => 'Daabac Miiska',
        'auto_refresh' => 'Dib-u-cusboonaysiin otomaatig ah',
        'starts_in' => 'Wuxuu bilaabayaa',
        'ends_in' => 'Wuu dhamaadayaa',
        'ended' => 'Dhacay',
        'days' => 'maalmood',
        'days_ago' => 'maalmood ka hor',
        'upcoming' => 'soo socda',
        'ongoing' => 'socda',
        'completed' => 'dhammaystiran',
        'all' => 'Dhammaan',
        'monday' => 'Isniin',
        'tuesday' => 'Talaado',
        'wednesday' => 'Arbaco',
        'thursday' => 'Khamiis',
        'friday' => 'Jimco',
        'saturday' => 'Sabti',
        'sunday' => 'Axad'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

// Fetch teacher's classes for filters
$classes = [];
$stmt = $conn->prepare("
    SELECT DISTINCT c.id, c.name
    FROM classes c
    JOIN class_subjects cs ON cs.class_id = c.id
    WHERE cs.teacher_id = ?
    ORDER BY c.name
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) $classes[] = $row;

// Fetch filters/search
$filter_class = isset($_GET['class_id']) ? (int)$_GET['class_id'] : 0;
$filter_type  = $_GET['exam_type'] ?? '';
$search       = trim($_GET['search'] ?? '');

// Fetch exams
$query = "
    SELECT e.id, e.name, e.exam_type, c.name AS class_name, s.name AS section_name, e.start_date, e.end_date
    FROM exams e
    JOIN classes c ON c.id = e.class_id
    JOIN sections s ON s.id = e.section_id
    JOIN class_subjects cs ON cs.class_id = e.class_id
    WHERE cs.teacher_id = ?
";

$params = [$teacher_id];
$types  = "i";

if ($filter_class) {
    $query .= " AND e.class_id = ?";
    $types .= "i";
    $params[] = $filter_class;
}

if ($filter_type) {
    $query .= " AND e.exam_type = ?";
    $types .= "s";
    $params[] = $filter_type;
}

if ($search) {
    $query .= " AND e.name LIKE ?";
    $types .= "s";
    $params[] = "%$search%";
}

$query .= " GROUP BY e.id ORDER BY e.start_date DESC";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$exams = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    
    <!-- Bootstrap 5 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap5.min.css">

    <!-- Datepicker -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    
    <!-- Select2 -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css">
    
    <!-- Toastr -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    <div class="container-fluid py-4">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="h2 mb-2">
                        <i class="fas fa-file-alt me-3"></i><?= t('dashboard_title') ?>
                    </h1>
                    <p class="mb-0 opacity-75">
                        <?= t('dashboard_subtitle') ?>
                    </p>
                </div>
                <div class="col-md-4 text-md-end">
                    <button class="btn btn-light btn-lg" id="addExamBtn">
                        <i class="fas fa-plus-circle me-2"></i><?= t('create_new_exam') ?>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-primary">
                        <i class="fas fa-file-alt"></i>
                    </div>
                    <h3 class="stat-number" id="totalExams"><?= count($exams) ?></h3>
                    <p class="stat-label"><?= t('total_exams') ?></p>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-success">
                        <i class="fas fa-calendar-check"></i>
                    </div>
                    <h3 class="stat-number" id="upcomingExams">0</h3>
                    <p class="stat-label"><?= t('upcoming_exams') ?></p>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-warning">
                        <i class="fas fa-running"></i>
                    </div>
                    <h3 class="stat-number" id="ongoingExams">0</h3>
                    <p class="stat-label"><?= t('ongoing_exams') ?></p>
                </div>
            </div>
            
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card">
                    <div class="stats-icon icon-info">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="stat-number" id="completedExams">0</h3>
                    <p class="stat-label"><?= t('completed_exams') ?></p>
                </div>
            </div>
        </div>
        
        <!-- Filter Container -->
        <div class="filter-container">
            <h5 class="mb-3">
                <i class="fas fa-filter me-2"></i><?= t('filter_exams') ?>
            </h5>
            
            <form method="get" id="filterForm">
                <div class="row g-3">
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted mb-1"><?= t('class') ?></label>
                        <select name="class_id" class="form-select select2-filter">
                            <option value=""><?= t('all_classes') ?></option>
                            <?php foreach ($classes as $c): ?>
                                <option value="<?= $c['id'] ?>" <?= $filter_class==$c['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($c['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted mb-1"><?= t('exam_type') ?></label>
                        <select name="exam_type" class="form-select select2-filter">
                            <option value=""><?= t('all_types') ?></option>
                            <option <?= $filter_type=='Midterm'?'selected':'' ?>><?= t('midterm') ?></option>
                            <option <?= $filter_type=='Final'?'selected':'' ?>><?= t('final') ?></option>
                            <option <?= $filter_type=='Quiz'?'selected':'' ?>><?= t('quiz') ?></option>
                            <option <?= $filter_type=='Assignment'?'selected':'' ?>><?= t('assignment') ?></option>
                        </select>
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted mb-1"><?= t('date_range') ?></label>
                        <input type="text" class="form-control date-range-picker" 
                               placeholder="<?= t('select_date_range') ?>" 
                               data-range="true"
                               data-mode="range">
                    </div>
                    
                    <div class="col-lg-3 col-md-6">
                        <label class="form-label small text-muted mb-1"><?= t('search') ?></label>
                        <div class="search-box">
                            <i class="fas fa-search"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                   class="form-control" 
                                   placeholder="<?= t('search_exams') ?>">
                        </div>
                    </div>
                </div>
                
                <div class="row mt-4">
                    <div class="col">
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fas fa-filter me-2"></i><?= t('apply_filters') ?>
                        </button>
                        <button type="button" id="resetFilters" class="btn btn-outline-secondary px-4 ms-2">
                            <i class="fas fa-redo me-2"></i><?= t('reset') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Exams Table -->
       <div class="dataTables_wrapper">
         <div class="table-responsive">
             <table id="examsTable" class="table table-hover w-100">

                <thead>
                    <tr>
                        <th><?= t('exam_name') ?></th>
                        <th><?= t('type') ?></th>
                        <th><?= t('class_col') ?></th> 
                        <th><?= t('date_range_col') ?></th>
                        <th><?= t('status') ?></th>
                        <th><?= t('actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($exams): ?>
                        <?php foreach ($exams as $e): 
                            $today = date('Y-m-d');
                            $start_date = $e['start_date'];
                            $end_date = $e['end_date'];
                            
                            $status = '';
                            if ($today < $start_date) {
                                $status = t('upcoming');
                            } else if ($today >= $start_date && $today <= $end_date) {
                                $status = t('ongoing');
                            } else {
                                $status = t('completed');
                            }
                            
                            // Calculate days difference
                            $days_diff = '';
                            if ($status == t('upcoming')) {
                                $days = date_diff(date_create($today), date_create($start_date))->days;
                                $days_diff = t('starts_in') . ' ' . $days . ' ' . t('days');
                            } else if ($status == t('ongoing')) {
                                $days = date_diff(date_create($today), date_create($end_date))->days;
                                $days_diff = t('ends_in') . ' ' . $days . ' ' . t('days');
                            } else {
                                $days = date_diff(date_create($end_date), date_create($today))->days;
                                $days_diff = t('ended') . ' ' . $days . ' ' . t('days_ago');
                            }
                        ?>
                            <tr data-status="<?= $status ?>">
                                <td>
                                    <div class="fw-bold"><?= htmlspecialchars($e['name']) ?></div>
                                    <div class="small text-muted">ID: <?= $e['id'] ?></div>
                                </td>
                                <td>
                                    <span class="exam-type-badge type-<?= strtolower($e['exam_type']) ?>">
                                        <?= htmlspecialchars($e['exam_type']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium"><?= htmlspecialchars($e['class_name']) ?></div>
                                    <small><span class="badge bg-light text-dark"><?= htmlspecialchars($e['section_name']) ?></span></small>
                                </td>
                               
                                <td>
                                    <div class="fw-medium">
                                        <?= date('M j, Y', strtotime($start_date)) ?> 
                                        <i class="fas fa-arrow-right mx-2 text-muted"></i>
                                        <?= date('M j, Y', strtotime($end_date)) ?>
                                    </div>
                                    <div class="date-range small text-muted">
                                        <?= date('l', strtotime($start_date)) ?> - <?= date('l', strtotime($end_date)) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <span class="status-indicator status-<?= $status ?>"></span>
                                        <span class="text-capitalize fw-medium"><?= $status ?></span>
                                    </div>
                                    <div class="small text-muted">
                                        <?= $days_diff ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons d-flex flex-wrap">
                                        <a href="view_exam.php?id=<?= $e['id'] ?>" 
                                           class="btn btn-sm btn-outline-info me-2 mb-2" 
                                           title="<?= t('view_details') ?>">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="edit_exam.php?id=<?= $e['id'] ?>" 
                                           class="btn btn-sm btn-outline-warning me-2 mb-2" 
                                           title="<?= t('edit_exam') ?>">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button class="btn btn-sm btn-outline-danger mb-2 delete-exam" 
                                                data-id="<?= $e['id'] ?>" 
                                                data-name="<?= htmlspecialchars($e['name']) ?>"
                                                title="<?= t('delete_exam') ?>">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="no-data-placeholder">
                                    <i class="fas fa-file-alt"></i>
                                    <h4 class="mb-3"><?= t('no_exams_found') ?></h4>
                                    <p class="mb-4"><?= t('no_exams_message') ?></p>
                                    <a href="add_exams.php" class="btn btn-primary">
                                        <i class="fas fa-plus me-2"></i><?= t('create_new_exam') ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i><?= t('confirm_deletion') ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><?= t('delete_confirm_message') ?> <strong id="examNameToDelete"></strong>?</p>
                    <p class="text-danger small mb-0">
                        <i class="fas fa-exclamation-circle me-1"></i>
                        <?= t('delete_warning') ?>
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= t('cancel') ?></button>
                    <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i><?= t('delete_exam') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.print.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            const table = $('#examsTable').DataTable({
                dom: '<"row"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                pageLength: 10,
                responsive: true,
                order: [[4, 'desc']], // Sort by date by default
                language: {
                    search: "_INPUT_",
                    searchPlaceholder: "<?= t('search_exams') ?>",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ <?= strtolower(t('exams')) ?>",
                    infoEmpty: "Showing 0 to 0 of 0 <?= strtolower(t('exams')) ?>",
                    infoFiltered: "(filtered from _MAX_ total <?= strtolower(t('exams')) ?>)",
                    emptyTable: "No <?= strtolower(t('exams')) ?> available",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 }, // Exam name
                    { responsivePriority: 2, targets: -1 }, // Actions
                    { responsivePriority: 3, targets: 1 }, // Type
                    { orderable: false, targets: -1 } // Disable sorting for actions column
                ]
            });
            
            // Initialize Select2 for filters
            $('.select2-filter').select2({
                width: '100%',
                placeholder: "Select option",
                allowClear: true
            });
            
            // Initialize datepicker
            $('.date-range-picker').flatpickr({
                mode: "range",
                dateFormat: "Y-m-d",
                onClose: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length === 2) {
                        // Update filter form with date range
                        const startDate = selectedDates[0].toISOString().split('T')[0];
                        const endDate = selectedDates[1].toISOString().split('T')[0];
                        
                        // You could add hidden inputs or filter table directly
                        // For now, just show visual feedback
                        toastr.info(`<?= t('filter_exams') ?>: ${startDate} <?= t('to') ?> ${endDate}`);
                    }
                }
            });
            
            // Calculate stats
            function updateStats() {
                const total = table.rows().count();
                const upcoming = table.rows('tr[data-status="<?= t('upcoming') ?>"]').count();
                const ongoing = table.rows('tr[data-status="<?= t('ongoing') ?>"]').count();
                const completed = table.rows('tr[data-status="<?= t('completed') ?>"]').count();
                
                $('#totalExams').text(total);
                $('#upcomingExams').text(upcoming);
                $('#ongoingExams').text(ongoing);
                $('#completedExams').text(completed);
            }
            
            // Initial stats calculation
            updateStats();
            
            // Update stats when table changes
            table.on('draw', function() {
                updateStats();
            });
            
            // Delete exam confirmation
            $('.delete-exam').on('click', function(e) {
                e.preventDefault();
                const examId = $(this).data('id');
                const examName = $(this).data('name');
                
                $('#examNameToDelete').text(examName);
                $('#confirmDeleteBtn').attr('href', 'delete_exam.php?id=' + examId);
                
                const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
                deleteModal.show();
            });
            
            // Reset filters
            $('#resetFilters').on('click', function() {
                $('.select2-filter').val(null).trigger('change');
                $('input[name="search"]').val('');
                $('.date-range-picker').val('');
                
                // Reset table search and filters
                table.search('').columns().search('').draw();
                
                toastr.success('<?= t('reset') ?> <?= strtolower(t('filters')) ?>');
            });
            
            // Add exam button
            $('#addExamBtn').on('click', function() {
                window.location.href = 'add_exams.php';
            });
            
            // Add row highlighting on hover
            $('#examsTable tbody').on('mouseenter', 'tr', function() {
                $(this).addClass('table-active');
            }).on('mouseleave', 'tr', function() {
                $(this).removeClass('table-active');
            });
            
            // Initialize toastr
            toastr.options = {
                "positionClass": "toast-top-right",
                "closeButton": true,
                "progressBar": true,
                "timeOut": "4000"
            };
            
            // Add status filter buttons
            const statusFilterHtml = `
                <div class="btn-group btn-group-sm mb-3" role="group">
                    <button type="button" class="btn btn-outline-primary status-filter" data-status=""><?= t('all') ?></button>
                    <button type="button" class="btn btn-outline-success status-filter" data-status="<?= t('upcoming') ?>"><?= t('upcoming') ?></button>
                    <button type="button" class="btn btn-outline-warning status-filter" data-status="<?= t('ongoing') ?>"><?= t('ongoing') ?></button>
                    <button type="button" class="btn btn-outline-secondary status-filter" data-status="<?= t('completed') ?>"><?= t('completed') ?></button>
                </div>
            `;
            
            $('.dataTables_wrapper .row:first-child .col-md-6:first-child').append(statusFilterHtml);
            
            // Status filter functionality
            $('.status-filter').on('click', function() {
                const status = $(this).data('status');
                
                $('.status-filter').removeClass('active');
                $(this).addClass('active');
                
                if (status) {
                    table.column(5).search(status).draw();
                } else {
                    table.column(5).search('').draw();
                }
            });
        });
    </script>
</body>
</html>

    
    <style>
        :root {
            --primary-color: #4e54c8;
            --secondary-color: #8f94fb;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --info-color: #17a2b8;
            --light-bg: #f8f9fa;
            --card-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            --border-radius: 12px;
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-bottom: 40px;
        }
        /* Prevent horizontal overflow globally */
        html, body {
            max-width: 100%;
            overflow-x: hidden;
        }

        /* DataTable container safety */
        .dataTables_wrapper {
            width: 100%;
            overflow-x: auto;
        }

        /* Table cell wrapping */
        table td, table th {
            white-space: nowrap;
        }

        /* Allow wrapping only where needed */
        td:first-child {
            white-space: normal;
            word-break: break-word;
        }

        .dashboard-header {
            background: linear-gradient(to right, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: var(--card-shadow);
        }
        
        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease;
            height: 100%;
            border: none;
        }
        
        .stats-card:hover {
            transform: translateY(-5px);
        }
        
        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 15px;
        }
        
        .icon-primary { background-color: rgba(78, 84, 200, 0.1); color: var(--primary-color); }
        .icon-success { background-color: rgba(40, 167, 69, 0.1); color: var(--success-color); }
        .icon-warning { background-color: rgba(255, 193, 7, 0.1); color: var(--warning-color); }
        .icon-info { background-color: rgba(23, 162, 184, 0.1); color: var(--info-color); }
        
        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .filter-container {
            background: white;
            border-radius: var(--border-radius);
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
        }
        
        .exam-type-badge {
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 0.85rem;
        }
        
        .type-midterm { background-color: #e3f2fd; color: #1565c0; }
        .type-final { background-color: #f3e5f5; color: #7b1fa2; }
        .type-quiz { background-color: #e8f5e9; color: #2e7d32; }
        .type-assignment { background-color: #fff3e0; color: #ef6c00; }
        
        .action-buttons .btn {
            border-radius: 8px;
            padding: 8px 16px;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        
        .action-buttons .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .dataTables_wrapper {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            box-shadow: var(--card-shadow);
        }
        
        .table-hover tbody tr:hover {
            background-color: rgba(78, 84, 200, 0.03);
        }
        
        .date-range {
            font-size: 0.85rem;
            color: #6c757d;
        }
        
        .status-indicator {
            display: inline-block;
            width: 10px;
            height: 10px;
            border-radius: 50%;
            margin-right: 8px;
        }
        
        .status-upcoming { background-color: #28a745; }
        .status-ongoing { background-color: #ffc107; }
        .status-completed { background-color: #6c757d; }
        
        .no-data-placeholder {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        
        .no-data-placeholder i {
            font-size: 4rem;
            margin-bottom: 20px;
            color: #dee2e6;
        }
        
        .quick-actions {
            margin-bottom: 20px;
        }
        
        .quick-action-btn {
            margin-right: 10px;
            margin-bottom: 10px;
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 500;
        }
        
        .search-box {
            position: relative;
        }
        
        .search-box .form-control {
            padding-left: 45px;
            border-radius: 8px;
        }
        
        .search-box i {
            position: absolute;
            left: 15px;
            top: 12px;
            color: #6c757d;
            z-index: 10;
        }
        
        .date-filter-group {
            background-color: var(--light-bg);
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        @media (max-width: 768px) {
            .dashboard-header {
                padding: 20px 15px;
            }
            
            .stats-card {
                margin-bottom: 15px;
            }
            
            .action-buttons .btn {
                margin-bottom: 5px;
                width: 100%;
            }
        }
        @media (max-width: 576px) {
        .filter-container .row > div {
            margin-bottom: 12px;
        }
        }

    </style>