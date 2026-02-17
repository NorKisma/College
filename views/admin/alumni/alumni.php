<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Manage Alumni - Student Management System',
        'alumni_management' => 'Alumni Management',
        'total_alumni' => 'Total Alumni',
        'todays_alumni' => 'Today\'s Alumni',
        'by_exam' => 'By Exam',
        'by_attendance' => 'By Attendance',
        'filter_by_section' => 'Filter by Section',
        'all_sections' => 'All Sections',
        'filter_by_class' => 'Filter by Class',
        'all_classes' => 'All Classes',
        'search_alumni' => 'Search Alumni',
        'search_by_name' => 'Search by name...',
        'apply_filters' => 'Apply Filters',
        'reset_filters' => 'Reset Filters',
        'export_csv' => 'Export as CSV',
        'export_pdf' => 'Export as PDF',
        'add_alumni' => 'Add Alumni',
        'student_name' => 'Student Name',
        'section' => 'Section',
        'class' => 'Class',
        'admission_date' => 'Admission Date',
        'graduation_date' => 'Graduation Date',
        'graduation_reason' => 'Graduation Reason',
        'exam_score' => 'Exam Score',
        'attendance_percentage' => 'Attendance %',
        'created_date' => 'Created Date',
        'actions' => 'Actions',
        'no_alumni_found' => 'No Alumni Found',
        'no_records_match' => 'No alumni records match your current filters.',
        'clear_filters' => 'Clear Filters',
        'view' => 'View',
        'edit' => 'Edit',
        'remove' => 'Remove',
        'profile' => 'Profile',
        'remove_alumni' => 'Remove Alumni',
        'confirm_removal' => 'Are you sure you want to remove',
        'from_alumni_list' => 'from alumni list?',
        'reason_for_removal' => 'Reason for removal',
        'enter_reason' => 'Enter reason...',
        'cancel' => 'Cancel',
        'confirm_removal_btn' => 'Confirm Removal',
        'alumni_details' => 'Alumni Details',
        'close' => 'Close',
        'alumni_removed_success' => 'Alumni record removed successfully!',
        'error_removing_alumni' => 'Error removing alumni',
        'exam_performance' => 'Exam Performance',
        'attendance_performance' => 'Attendance Performance',
        'both' => 'Both',
        'id' => 'ID',
        'today' => 'Today',
        'yesterday' => 'Yesterday',
        'last_7_days' => 'Last 7 Days',
        'last_30_days' => 'Last 30 Days',
        'this_month' => 'This Month',
        'last_month' => 'Last Month',
        'custom_range' => 'Custom Range',
        'export' => 'Export',
        'print' => 'Print',
        'refresh' => 'Refresh',
        'help' => 'Help',
        'settings' => 'Settings'
    ],
    2 => [ // Arabic
        'page_title' => 'إدارة الخريجين - نظام إدارة الطلاب',
        'alumni_management' => 'إدارة الخريجين',
        'total_alumni' => 'إجمالي الخريجين',
        'todays_alumni' => 'خريجين اليوم',
        'by_exam' => 'حسب الامتحان',
        'by_attendance' => 'حسب الحضور',
        'filter_by_section' => 'تصفية حسب القسم',
        'all_sections' => 'جميع الأقسام',
        'filter_by_class' => 'تصفية حسب الصف',
        'all_classes' => 'جميع الصفوف',
        'search_alumni' => 'بحث عن الخريجين',
        'search_by_name' => 'بحث بالاسم...',
        'apply_filters' => 'تطبيق الفلاتر',
        'reset_filters' => 'إعادة تعيين الفلاتر',
        'export_csv' => 'تصدير كـ CSV',
        'export_pdf' => 'تصدير كـ PDF',
        'add_alumni' => 'إضافة خريج',
        'student_name' => 'اسم الطالب',
        'section' => 'القسم',
        'class' => 'الصف',
        'admission_date' => 'تاريخ القبول',
        'graduation_date' => 'تاريخ التخرج',
        'graduation_reason' => 'سبب التخرج',
        'exam_score' => 'نتيجة الامتحان',
        'attendance_percentage' => 'نسبة الحضور',
        'created_date' => 'تاريخ الإنشاء',
        'actions' => 'الإجراءات',
        'no_alumni_found' => 'لم يتم العثور على خريجين',
        'no_records_match' => 'لا توجد سجلات خريجين تطابق فلاتر البحث الحالية.',
        'clear_filters' => 'مسح الفلاتر',
        'view' => 'عرض',
        'edit' => 'تعديل',
        'remove' => 'حذف',
        'profile' => 'الملف الشخصي',
        'remove_alumni' => 'حذف الخريج',
        'confirm_removal' => 'هل أنت متأكد من حذف',
        'from_alumni_list' => 'من قائمة الخريجين؟',
        'reason_for_removal' => 'سبب الحذف',
        'enter_reason' => 'أدخل السبب...',
        'cancel' => 'إلغاء',
        'confirm_removal_btn' => 'تأكيد الحذف',
        'alumni_details' => 'تفاصيل الخريج',
        'close' => 'إغلاق',
        'alumni_removed_success' => 'تم حذف سجل الخريج بنجاح!',
        'error_removing_alumni' => 'خطأ في حذف الخريج',
        'exam_performance' => 'أداء الامتحان',
        'attendance_performance' => 'أداء الحضور',
        'both' => 'كلاهما',
        'id' => 'الرقم',
        'today' => 'اليوم',
        'yesterday' => 'أمس',
        'last_7_days' => 'آخر 7 أيام',
        'last_30_days' => 'آخر 30 يوم',
        'this_month' => 'هذا الشهر',
        'last_month' => 'الشهر الماضي',
        'custom_range' => 'نطاق مخصص',
        'export' => 'تصدير',
        'print' => 'طباعة',
        'refresh' => 'تحديث',
        'help' => 'مساعدة',
        'settings' => 'إعدادات'
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

// Helper function to translate graduation reason
function translateReason($reason) {
    switch($reason) {
        case 'exam_performance': return t('exam_performance');
        case 'attendance_performance': return t('attendance_performance');
        case 'both': return t('both');
        default: return ucfirst(str_replace('_', ' ', $reason));
    }
}

$logged_in_id = $_SESSION['user_id'] ?? null;

function is_finance() {
    return ($_SESSION['role'] ?? '') === 'Finance';
}

// theme
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme = ($theme_res && $theme_row = $theme_res->fetch_assoc()) ? (int)$theme_row['mode'] : 1;
$is_dark = ($theme === 0);

// Handle alumni removal
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_alumni'])) {
    $alumni_id = intval($_POST['alumni_id']);
    $reason = $conn->real_escape_string($_POST['remove_reason'] ?? '');
    
    // You might want to create an alumni_history table for tracking removals
    // For now, just delete from alumni
    $stmt = $conn->prepare("DELETE FROM alumni WHERE id = ?");
    $stmt->bind_param("i", $alumni_id);
    
    if ($stmt->execute()) {
        $message = t('alumni_removed_success');
        $message_type = "success";
    } else {
        $message = t('error_removing_alumni') . ": " . $stmt->error;
        $message_type = "error";
    }
}

// Handle filtering
$selected_section = isset($_GET['section']) ? intval($_GET['section']) : 0;
$selected_class = isset($_GET['class']) ? intval($_GET['class']) : 0;
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Get sections for dropdown
$sections = $conn->query("SELECT id, name FROM sections ORDER BY name");
?>

<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="<?= $is_dark ? 'dark' : 'light' ?>">
    <div class="container">
        <h2><i class="fas fa-user-graduate"></i> <?= t('alumni_management') ?></h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-<?= $message_type ?>">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- Statistics Section -->
        <?php
        // Get total alumni count
        $total_alumni = $conn->query("SELECT COUNT(*) as total FROM alumni")->fetch_assoc()['total'];
        
        // Get today's alumni count
        $today_alumni = $conn->query("SELECT COUNT(*) as total FROM alumni WHERE DATE(created_at) = CURDATE()")->fetch_assoc()['total'];
        
        // Get alumni by reason
        $exam_alumni = $conn->query("SELECT COUNT(*) as total FROM alumni WHERE graduation_reason = 'exam_performance'")->fetch_assoc()['total'];
        $attendance_alumni = $conn->query("SELECT COUNT(*) as total FROM alumni WHERE graduation_reason = 'attendance_performance'")->fetch_assoc()['total'];
        $both_alumni = $conn->query("SELECT COUNT(*) as total FROM alumni WHERE graduation_reason = 'both'")->fetch_assoc()['total'];
        ?>
        
        <div class="statistics-section">
            <div class="stat-card">
                <h3><i class="fas fa-user-graduate"></i> <?= t('total_alumni') ?></h3>
                <div class="value"><?= number_format($total_alumni) ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <h3><i class="fas fa-calendar-day"></i> <?= t('todays_alumni') ?></h3>
                <div class="value"><?= number_format($today_alumni) ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <h3><i class="fas fa-graduation-cap"></i> <?= t('by_exam') ?></h3>
                <div class="value"><?= number_format($exam_alumni) ?></div>
            </div>
            <div class="stat-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <h3><i class="fas fa-calendar-check"></i> <?= t('by_attendance') ?></h3>
                <div class="value"><?= number_format($attendance_alumni) ?></div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <form method="GET" id="filterForm">
                <div class="form-row">
                    <div class="form-group">
                        <label for="section"><i class="fas fa-layer-group"></i> <?= t('filter_by_section') ?>:</label>
                        <select id="section" name="section" class="form-control" onchange="loadClassesForFilter()">
                            <option value=""><?= t('all_sections') ?></option>
                            <?php 
                            $sections->data_seek(0); // Reset pointer
                            while ($section = $sections->fetch_assoc()): ?>
                                <option value="<?= $section['id'] ?>" <?= $selected_section == $section['id'] ? 'selected' : '' ?>>
                                    <?= htmlspecialchars($section['name']) ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="class"><i class="fas fa-chalkboard"></i> <?= t('filter_by_class') ?>:</label>
                        <select id="class" name="class" class="form-control" <?= !$selected_section ? 'disabled' : '' ?>>
                            <option value=""><?= t('all_classes') ?></option>
                            <?php if ($selected_section): ?>
                                <?php 
                                $classes = $conn->query("SELECT c.id, c.name FROM classes c WHERE c.section_id = $selected_section ORDER BY c.name");
                                while ($class = $classes->fetch_assoc()):
                                ?>
                                    <option value="<?= $class['id'] ?>" <?= $selected_class == $class['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($class['name']) ?>
                                    </option>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="search"><i class="fas fa-search"></i> <?= t('search_alumni') ?>:</label>
                        <input type="text" id="search" name="search" class="form-control" placeholder="<?= t('search_by_name') ?>" value="<?= htmlspecialchars($search) ?>">
                    </div>
                </div>

                <div class="form-row">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> <?= t('apply_filters') ?>
                    </button>
                    <a href="?" class="btn btn-outline-secondary">
                        <i class="fas fa-redo"></i> <?= t('reset_filters') ?>
                    </a>
                </div>
            </form>
        </div>

        <!-- Export Buttons -->
        <div class="export-buttons">
            <button class="btn btn-success" onclick="exportData('csv')">
                <i class="fas fa-file-csv"></i> <?= t('export_csv') ?>
            </button>
            <button class="btn btn-danger" onclick="exportData('pdf')">
                <i class="fas fa-file-pdf"></i> <?= t('export_pdf') ?>
            </button>
            <?php if(!is_finance()): ?>
            <a href="add_alumni.php" class="btn btn-info">
                <i class="fas fa-user-plus"></i> <?= t('add_alumni') ?>
            </a>
            <?php endif; ?>
        </div>

        <!-- Alumni Table -->
        <div class="table-container">
            <?php
            // Build query based on filters
            $query = "
                SELECT 
                    a.id as alumni_id,
                    a.graduation_date,
                    a.graduation_reason,
                    a.exam_score,
                    a.attendance_percentage,
                    a.created_at,
                    s.id as student_id,
                    s.name as student_name,
                    s.admission_date,
                    s.dob,
                    s.gender,
                    c.name as class_name,
                    sec.name as section_name
                FROM alumni a
                INNER JOIN students s ON a.student_id = s.id
                INNER JOIN classes c ON a.class_id = c.id
                INNER JOIN sections sec ON a.section_id = sec.id
                WHERE 1=1
            ";
            
            $params = [];
            $types = '';
            
            if ($selected_section) {
                $query .= " AND a.section_id = ?";
                $params[] = $selected_section;
                $types .= 'i';
            }
            
            if ($selected_class) {
                $query .= " AND a.class_id = ?";
                $params[] = $selected_class;
                $types .= 'i';
            }
            
            if ($search) {
                $query .= " AND s.name LIKE ?";
                $params[] = "%$search%";
                $types .= 's';
            }
            
            $query .= " ORDER BY a.created_at DESC, sec.name, c.name, s.name";
            
            // Prepare and execute query
            $stmt = $conn->prepare($query);
            if ($params) {
                $stmt->bind_param($types, ...$params);
            }
            $stmt->execute();
            $alumni_result = $stmt->get_result();
            
            if ($alumni_result->num_rows > 0):
            ?>
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th><?= t('student_name') ?></th>
                            <th><?= t('section') ?></th>
                            <th><?= t('class') ?></th>
                            <th><?= t('admission_date') ?></th>
                            <th><?= t('graduation_date') ?></th>
                            <th><?= t('graduation_reason') ?></th>
                            <th><?= t('exam_score') ?></th>
                            <th><?= t('attendance_percentage') ?></th>
                            <th><?= t('created_date') ?></th>
                            <th><?= t('actions') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $counter = 1;
                        while ($alumni = $alumni_result->fetch_assoc()): 
                            // Determine badge color based on graduation reason
                            $badge_class = '';
                            switch($alumni['graduation_reason']) {
                                case 'exam_performance': $badge_class = 'badge-success'; break;
                                case 'attendance_performance': $badge_class = 'badge-warning'; break;
                                case 'both': $badge_class = 'badge-info'; break;
                                default: $badge_class = 'badge-secondary';
                            }
                        ?>
                            <tr>
                                <td><?= $counter++ ?></td>
                                <td>
                                    <strong><?= htmlspecialchars($alumni['student_name']) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= t('id') ?>: <?= $alumni['student_id'] ?></small>
                                </td>
                                <td>
                                    <span class="badge badge-primary"><?= htmlspecialchars($alumni['section_name']) ?></span>
                                </td>
                                <td><?= htmlspecialchars($alumni['class_name']) ?></td>
                                <td><?= $alumni['admission_date'] ?></td>
                                <td>
                                    <span class="badge badge-success"><?= $alumni['graduation_date'] ?></span>
                                </td>
                                <td>
                                    <span class="badge <?= $badge_class ?>">
                                        <?= translateReason($alumni['graduation_reason']) ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($alumni['exam_score']): ?>
                                        <span class="badge badge-info"><?= number_format($alumni['exam_score'], 2) ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($alumni['attendance_percentage']): ?>
                                        <span class="badge badge-warning"><?= number_format($alumni['attendance_percentage'], 2) ?>%</span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= date('Y-m-d H:i', strtotime($alumni['created_at'])) ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button class="btn btn-info btn-sm" onclick="viewAlumniDetails(<?= $alumni['alumni_id'] ?>)">
                                            <i class="fas fa-eye"></i> <?= t('view') ?>
                                        </button>
                                        <?php if(!is_finance()): ?>
                                        <button class="btn btn-warning btn-sm" onclick="editAlumni(<?= $alumni['alumni_id'] ?>)">
                                            <i class="fas fa-edit"></i> <?= t('edit') ?>
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="showRemoveModal(<?= $alumni['alumni_id'] ?>, '<?= htmlspecialchars($alumni['student_name']) ?>')">
                                            <i class="fas fa-trash"></i> <?= t('remove') ?>
                                        </button>
                                        <?php endif; ?>
                                        <a href="alumni_profile.php?id=<?= $alumni['student_id'] ?>" class="btn btn-primary btn-sm" target="_blank">
                                            <i class="fas fa-external-link-alt"></i> <?= t('profile') ?>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
                
                <!-- Pagination would go here if implemented -->
                <!-- <div class="pagination">
                    <a href="#" class="page-link active">1</a>
                    <a href="#" class="page-link">2</a>
                    <a href="#" class="page-link">3</a>
                </div> -->
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-user-graduate fa-3x"></i>
                    <h3><?= t('no_alumni_found') ?></h3>
                    <p><?= t('no_records_match') ?></p>
                    <a href="?" class="btn btn-primary">
                        <i class="fas fa-redo"></i> <?= t('clear_filters') ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Remove Alumni Modal -->
    <div id="removeModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-trash"></i> <?= t('remove_alumni') ?></h5>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <form method="POST" id="removeForm">
                <input type="hidden" id="alumni_id" name="alumni_id">
                <div class="modal-body">
                    <p><?= t('confirm_removal') ?> <strong id="studentName"></strong> <?= t('from_alumni_list') ?></p>
                    <div class="form-group">
                        <label for="remove_reason"><?= t('reason_for_removal') ?>:</label>
                        <textarea id="remove_reason" name="remove_reason" class="form-control" rows="3" placeholder="<?= t('enter_reason') ?>"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" onclick="closeModal()"><?= t('cancel') ?></button>
                    <button type="submit" name="remove_alumni" class="btn btn-danger"><?= t('confirm_removal_btn') ?></button>
                </div>
            </form>
        </div>
    </div>

    <!-- Alumni Details Modal -->
    <div id="detailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-info-circle"></i> <?= t('alumni_details') ?></h5>
                <button class="close" onclick="closeModal()">&times;</button>
            </div>
            <div class="modal-body" id="detailsContent">
                <!-- Details will be loaded here via AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" onclick="closeModal()"><?= t('close') ?></button>
            </div>
        </div>
    </div>

    <script>
        function loadClassesForFilter() {
            const section = document.getElementById('section').value;
            const classSelect = document.getElementById('class');
            
            if (section) {
                classSelect.disabled = false;
                // Fetch classes via AJAX
                fetch('ajax_get_classes.php?section=' + section)
                    .then(response => response.json())
                    .then(data => {
                        classSelect.innerHTML = '<option value=""><?= t('all_classes') ?></option>';
                        data.forEach(classItem => {
                            classSelect.innerHTML += `<option value="${classItem.id}">${classItem.name}</option>`;
                        });
                        
                        // Preserve selected value if exists
                        const selectedClass = new URLSearchParams(window.location.search).get('class');
                        if (selectedClass) {
                            classSelect.value = selectedClass;
                        }
                    });
            } else {
                classSelect.disabled = true;
                classSelect.innerHTML = '<option value=""><?= t('all_classes') ?></option>';
            }
        }

        function showRemoveModal(alumniId, studentName) {
            document.getElementById('alumni_id').value = alumniId;
            document.getElementById('studentName').textContent = studentName;
            document.getElementById('removeModal').style.display = 'block';
        }

        function viewAlumniDetails(alumniId) {
            fetch('ajax_get_alumni_details.php?id=' + alumniId)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('detailsContent').innerHTML = html;
                    document.getElementById('detailsModal').style.display = 'block';
                });
        }

        function editAlumni(alumniId) {
            // Redirect to edit page or open edit modal
            window.location.href = 'edit_alumni.php?id=' + alumniId;
        }

        function closeModal() {
            document.getElementById('removeModal').style.display = 'none';
            document.getElementById('detailsModal').style.display = 'none';
        }

        function exportData(format) {
            const section = document.getElementById('section').value || '';
            const classId = document.getElementById('class').value || '';
            const search = document.getElementById('search').value || '';
            
            let url = 'export_alumni.php?format=' + format;
            if (section) url += '&section=' + section;
            if (classId) url += '&class=' + classId;
            if (search) url += '&search=' + encodeURIComponent(search);
            
            window.open(url, '_blank');
        }

        // Close modal when clicking outside
        window.onclick = function(event) {
            const modals = document.getElementsByClassName('modal');
            for (let modal of modals) {
                if (event.target == modal) {
                    modal.style.display = 'none';
                }
            }
        }

        // Initialize filter classes if section is selected
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($selected_section): ?>
                loadClassesForFilter();
            <?php endif; ?>
        });
    </script>
</body>
</html>

   <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            transition: all 0.3s ease;
            min-height: 100vh;
        }

        body.light {
            background-color: #f5f5f5;
            color: #333;
        }

        body.dark {
            background-color: #1a1a1a;
            color: #fff;
        }

        body.dark .container {
            background-color: #2d2d2d;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.3);
        }

        body.dark .form-control,
        body.dark .table,
        body.dark .btn-outline-secondary,
        body.dark .card,
         {
            background-color: #3d3d3d;
            color: #ffffff;
            border-color: #555;
        }
        
                body.dark .modal-title,
                body.dark .modal-content{
                    Background-Color: #eeecec;
            color: #000000;
                }
        body.dark .table {
            background-color: #2d2d2d;
        }

        body.dark .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(255, 255, 255, 0.05);
        }

        body.dark .table-hover tbody tr:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }

        body.dark .card {
            background-color: #3d3d3d;
            border-color: #555;
        }

        body.dark .modal-header {
            border-bottom-color: #555;
        }

        body.dark .modal-footer {
            border-top-color: #555;
        }

        .container {
            max-width: 1600px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #007bff;
            color: #007bff;
        }

        .filter-section {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        body.dark .filter-section {
            background-color: #3d3d3d;
        }

        .form-row {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }

        .form-group {
            flex: 1;
            min-width: 200px;
        }

        .form-control {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: all 0.3s;
            margin-right: 10px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn i {
            font-size: 14px;
        }

        .btn-primary {
            background-color: #007bff;
            color: white;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .btn-success {
            background-color: #28a745;
            color: white;
        }

        .btn-success:hover {
            background-color: #1e7e34;
        }

        .btn-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .btn-warning:hover {
            background-color: #e0a800;
        }

        .btn-danger {
            background-color: #dc3545;
            color: white;
        }

        .btn-danger:hover {
            background-color: #c82333;
        }

        .btn-info {
            background-color: #17a2b8;
            color: white;
        }

        .btn-info:hover {
            background-color: #138496;
        }

        .btn-outline-secondary {
            background-color: transparent;
            border: 1px solid #6c757d;
            color: #6c757d;
        }

        .btn-outline-secondary:hover {
            background-color: #6c757d;
            color: white;
        }

        .table-container {
            overflow-x: auto;
            margin-top: 20px;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        .table th,
        .table td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .table th {
            background-color: #f8f9fa;
            font-weight: 600;
            position: sticky;
            top: 0;
        }

        body.dark .table th {
            background-color: #3d3d3d;
        }

        .table-striped tbody tr:nth-of-type(odd) {
            background-color: #f8f9fa;
        }

        .table-hover tbody tr:hover {
            background-color: #e9ecef;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }

        body.dark .alert-success {
            background-color: #155724;
            color: #d4edda;
            border-color: #0c3d1a;
        }

        body.dark .alert-error {
            background-color: #721c24;
            color: #f8d7da;
            border-color: #5a151b;
        }

        body.dark .alert-warning {
            background-color: #856404;
            color: #fff3cd;
            border-color: #6b5203;
        }

        .statistics-section {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .stat-card h3 {
            font-size: 14px;
            margin-bottom: 5px;
            opacity: 0.9;
        }

        .stat-card .value {
            font-size: 28px;
            font-weight: bold;
        }

        .badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .badge-success {
            background-color: #28a745;
            color: white;
        }

        .badge-warning {
            background-color: #ffc107;
            color: #212529;
        }

        .badge-info {
            background-color: #17a2b8;
            color: white;
        }

        .badge-danger {
            background-color: #dc3545;
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            flex-wrap: wrap;
        }

        .btn-sm {
            padding: 5px 10px;
            font-size: 12px;
        }

        .export-buttons {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            justify-content: flex-end;
        }

        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }

        .page-link {
            padding: 8px 12px;
            border: 1px solid #ddd;
            background-color: white;
            color: #007bff;
            text-decoration: none;
            border-radius: 4px;
        }

        body.dark .page-link {
            background-color: #3d3d3d;
            border-color: #555;
            color: #fff;
        }

        .page-link:hover {
            background-color: #f8f9fa;
        }

        body.dark .page-link:hover {
            background-color: #555;
        }

        .page-link.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 0;
            border-radius: 10px;
            width: 90%;
            max-width: 500px;
            overflow: hidden;
        }

        .modal-header {
            padding: 15px 20px;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .modal-title {
            margin: 0;
            font-size: 18px;
        }

        .close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6c757d;
        }

        .close:hover {
            color: #000;
        }

        body.dark .close:hover {
            color: #fff;
        }

        .modal-body {
            padding: 20px;
        }

        .modal-footer {
            padding: 15px 20px;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .loading {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 48px;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                padding: 15px;
            }
            
            .form-row {
                flex-direction: column;
            }
            
            .form-group {
                min-width: 100%;
            }
            
            .action-buttons {
                flex-direction: column;
            }
            
            .btn {
                margin-bottom: 5px;
            }
        }
    </style>