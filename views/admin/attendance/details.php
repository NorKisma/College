<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

/* ---------- LANGUAGE & TRANSLATION ---------- */
// Get current language from app_settings
$settings = $conn->query("SELECT language_id FROM app_settings ORDER BY id DESC LIMIT 1")->fetch_assoc();
$currentLangId = $settings['language_id'] ?? 1;

// Translation array for this page
$translations = [
    1 => [ // English
        // Page Title & Headers
        'page_title' => 'Attendance Session Details',
        'header_title' => 'Attendance Session Details',
        'access_denied' => 'Access denied',
        'invalid_session' => 'Invalid attendance session',
        'db_error' => 'DB Error: ',
        
        // Header Labels
        'label_class' => 'Class',
        'label_subject' => 'Subject',
        'label_section' => 'Section',
        'label_date' => 'Date',
        'label_recorded_by' => 'Recorded by:',
        'label_duration' => 'Duration:',
        
        // Role Labels
        'role_admin_view' => 'Administrator View',
        'role_member_view' => 'Member View',
        
        // Action Buttons
        'btn_edit_session' => 'Edit Session',
        'btn_back' => 'Back',
        'btn_export' => 'Export',
        'btn_apply_filters' => 'Apply Filters',
        'btn_view_student' => 'View',
        
        // Statistics
        'stat_present' => 'Present',
        'stat_absent' => 'Absent',
        'stat_late' => 'Late',
        'stat_excused' => 'Excused',
        'stat_percentage' => '% of total',
        
        // Integrity Alert
        'integrity_title' => 'Session Integrity Analysis',
        'integrity_description' => 'Total records captured in this session',
        'integrity_complete' => 'Complete Session',
        'integrity_empty' => 'Empty Session',
        
        // Filter Section
        'filter_title' => 'Filter Students',
        'filter_search_placeholder' => 'Search by student name...',
        'filter_status_all' => 'All Statuses',
        'filter_status_present' => '📗 Present',
        'filter_status_absent' => '📕 Absent',
        'filter_status_late' => '📒 Late',
        'filter_status_excused' => '📘 Excused',
        'filter_records' => 'Records',
        
        // Table Section
        'table_title' => 'Student Attendance Records',
        'table_number' => '#',
        'table_student_name' => 'Student Name',
        'table_attendance_status' => 'Attendance Status',
        'table_actions' => 'Actions',
        
        // Status Badges
        'status_present' => 'Present',
        'status_absent' => 'Absent',
        'status_late' => 'Late',
        'status_excused' => 'Excused',
        
        // Footer
        'footer_generated' => 'Session generated on',
        'footer_help' => 'Need help?',
        
        // Icons (for status badges)
        'icon_present' => 'check-circle',
        'icon_absent' => 'x-circle',
        'icon_late' => 'clock',
        'icon_excused' => 'calendar-check',
        
        // Easter Egg
        'easter_egg_title' => '🎉 EPIC Attendance Session! 🎉',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'تفاصيل جلسة الحضور',
        'header_title' => 'تفاصيل جلسة الحضور',
        'access_denied' => 'الوصول مرفوض',
        'invalid_session' => 'جلسة حضور غير صالحة',
        'db_error' => 'خطأ في قاعدة البيانات: ',
        
        // Header Labels
        'label_class' => 'الصف',
        'label_subject' => 'المادة',
        'label_section' => 'القسم',
        'label_date' => 'التاريخ',
        'label_recorded_by' => 'مسجل بواسطة:',
        'label_duration' => 'المدة:',
        
        // Role Labels
        'role_admin_view' => 'عرض المسؤول',
        'role_member_view' => 'عرض العضو',
        
        // Action Buttons
        'btn_edit_session' => 'تعديل الجلسة',
        'btn_back' => 'رجوع',
        'btn_export' => 'تصدير',
        'btn_apply_filters' => 'تطبيق الفلاتر',
        'btn_view_student' => 'عرض',
        
        // Statistics
        'stat_present' => 'حاضر',
        'stat_absent' => 'غائب',
        'stat_late' => 'متأخر',
        'stat_excused' => 'معذور',
        'stat_percentage' => '% من الإجمالي',
        
        // Integrity Alert
        'integrity_title' => 'تحليل سلامة الجلسة',
        'integrity_description' => 'إجمالي السجلات المسجلة في هذه الجلسة',
        'integrity_complete' => 'جلسة كاملة',
        'integrity_empty' => 'جلسة فارغة',
        
        // Filter Section
        'filter_title' => 'تصفية الطلاب',
        'filter_search_placeholder' => 'البحث باسم الطالب...',
        'filter_status_all' => 'جميع الحالات',
        'filter_status_present' => '📗 حاضر',
        'filter_status_absent' => '📕 غائب',
        'filter_status_late' => '📒 متأخر',
        'filter_status_excused' => '📘 معذور',
        'filter_records' => 'سجل',
        
        // Table Section
        'table_title' => 'سجلات حضور الطلاب',
        'table_number' => '#',
        'table_student_name' => 'اسم الطالب',
        'table_attendance_status' => 'حالة الحضور',
        'table_actions' => 'الإجراءات',
        
        // Status Badges
        'status_present' => 'حاضر',
        'status_absent' => 'غائب',
        'status_late' => 'متأخر',
        'status_excused' => 'معذور',
        
        // Footer
        'footer_generated' => 'تم إنشاء الجلسة في',
        'footer_help' => 'تحتاج مساعدة؟',
        
        // Icons (for status badges)
        'icon_present' => 'check-circle',
        'icon_absent' => 'x-circle',
        'icon_late' => 'clock',
        'icon_excused' => 'calendar-check',
        
        // Easter Egg
        'easter_egg_title' => '🎉 جلسة حضور رائعة! 🎉',
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

/* ---------- AUTH ---------- */
$logged_in_id = $_SESSION['user_id'] ?? null;
$role = $_SESSION['role'] ?? null;
if (!$logged_in_id || !in_array($role, ['admin','member'])) die(t('access_denied'));

/* ---------- INPUT (SESSION CONTEXT) ---------- */
$date        = $_GET['date'] ?? null;
$class_id    = (int)($_GET['class_id'] ?? 0);
$subject_id  = (int)($_GET['subject_id'] ?? 0);
$recorded_by = $_GET['recorded_by'] ?? null;
if (!$date || !$class_id || !$subject_id) die(t('invalid_session'));

/* ---------- SESSION META ---------- */
$session_query = "
SELECT 
    c.name AS class_name,
    sec.name AS section_name,
    sub.name AS subject_name,
    cs.start_time,
    cs.end_time
FROM classes c
LEFT JOIN sections sec ON sec.id = c.section_id
JOIN subjects sub ON sub.id = $subject_id
JOIN class_subjects cs ON cs.class_id = c.id AND cs.subject_id = sub.id
WHERE c.id = $class_id
";
$result = $conn->query($session_query);
if (!$result) die(t('db_error') . $conn->error);
$session = $result->fetch_assoc();

/* ---------- THEME ---------- */
$theme_res = $conn->query("SELECT mode FROM theme LIMIT 1");
$theme_row = $theme_res->fetch_assoc();
$theme = (int)($theme_row['mode'] ?? 0); // 0 = dark, 1 = light
$theme_class = ($theme === 0) ? 'dark-theme' : 'light-theme';

/* ---------- RECORDED BY NAME ---------- */
function recordedByName($conn, $val) {
    global $translations, $currentLangId;
    if (!$val) return '-';
    if (str_starts_with($val,'Admin') || str_starts_with($val,'Teacher') || str_starts_with($val,'Member')) return $val;
    if (is_numeric($val)) {
        $r = $conn->query("SELECT CONCAT(first_name,' ',last_name) n FROM teachers WHERE id=$val")->fetch_assoc();
        return ($r['n'] ?? 'Teacher') . ' (' . t('label_teacher') . ')';
    }
    return '-';
}
$recorded_name = recordedByName($conn, $recorded_by);

/* ---------- RECORDED BY FILTER ---------- */
$whereRecorded = '';
if ($recorded_by) {
    if (str_starts_with($recorded_by,'Admin') || str_starts_with($recorded_by,'Teacher') || str_starts_with($recorded_by,'Member')) {
        $whereRecorded = "AND a.recorded_by LIKE '" . $conn->real_escape_string($recorded_by) . "%'";
    } elseif (is_numeric($recorded_by)) {
        $whereRecorded = "AND a.recorded_by = '" . $conn->real_escape_string($recorded_by) . "'";
    }
}

/* ---------- STUDENT LIST ---------- */
$res = $conn->query("
    SELECT st.id, st.name, a.status
    FROM attendance a
    JOIN students st ON st.id = a.student_id
    WHERE a.date = '$date'
      AND a.class_id = $class_id
      AND a.subject_id = $subject_id
      $whereRecorded
    ORDER BY st.name
");

$stats = ['present'=>0,'absent'=>0,'late'=>0,'excused'=>0];
$rows = [];
while ($r = $res->fetch_assoc()) {
    $stats[$r['status']]++;
    $rows[] = $r;
}
$total = array_sum($stats);
$percent = fn($v) => $total ? round(($v / $total) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= t('page_title') ?> | <?= htmlspecialchars($session['class_name']) ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Montserrat:wght@800;900&display=swap" rel="stylesheet">

</head>
<body class="<?= $theme_class ?>">
<div class="container">

<!-- ENHANCED HEADER -->
<div class="session-header header-card fade-in-up">
    <div class="d-flex justify-content-between align-items-start p-4">
        <div class="header-content">
            <div class="d-flex align-items-center gap-3 mb-3">
                <i class="bi bi-calendar-check-fill text-primary" style="font-size: 2rem;"></i>
                <div>
                    <h1 class="h3 fw-bold mb-1"><?= t('header_title') ?></h1>
                    <div class="d-flex align-items-center gap-3 flex-wrap">
                        <span class="text-primary">
                            <i class="bi bi-people-fill"></i>
                            <?= t('label_class') ?>: <?= htmlspecialchars($session['class_name']) ?>
                        </span>
                        <span class="text-info">
                            <i class="bi bi-book-fill"></i>
                            <?= t('label_subject') ?>: <?= htmlspecialchars($session['subject_name']) ?>
                        </span>
                        <span class="text-warning">
                            <i class="bi bi-grid-3x3-gap-fill"></i>
                            <?= t('label_section') ?>: <?= htmlspecialchars($session['section_name'] ?? '-') ?>
                        </span>
                        <span class="text-success">
                            <i class="bi bi-calendar-date-fill"></i>
                            <?= t('label_date') ?>: <?= htmlspecialchars($date) ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="d-flex align-items-center gap-4 mt-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-person-check-fill text-muted"></i>
                    <span class="small"><?= t('label_recorded_by') ?></span>
                    <strong class="text-highlight"><?= htmlspecialchars($recorded_name) ?></strong>
                </div>
                <?php if($session['start_time'] && $session['end_time']): ?>
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-fill text-muted"></i>
                    <span class="small"><?= t('label_duration') ?></span>
                    <strong class="text-primary"><?= htmlspecialchars($session['start_time']) ?> - <?= htmlspecialchars($session['end_time']) ?></strong>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="d-flex flex-column gap-2 align-items-end">
            <span class="session-badge bg-primary text-white">
                <i class="bi bi-shield-check"></i>
                <?= $role === 'admin' ? t('role_admin_view') : t('role_member_view') ?>
            </span>
            <div class="d-flex gap-2 mt-3">
                <?php if ($role === 'admin'): ?>
                    <a href="edit_session.php?<?= http_build_query($_GET) ?>" class="btn btn-gradient">
                        <i class="bi bi-pencil-square"></i> <?= t('btn_edit_session') ?>
                    </a>
                <?php endif; ?>
                <a href="attendance.php" class="btn btn-glass">
                    <i class="bi bi-arrow-left"></i> <?= t('btn_back') ?>
                </a>
                <a href="export_session.php?<?= http_build_query($_GET) ?>" class="btn btn-success btn-gradient">
                    <i class="bi bi-download"></i> <?= t('btn_export') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ENHANCED STATS CARDS -->
<div class="row g-4 mb-5">
    <?php foreach ($stats as $k=>$v): ?>
    <div class="col-xl-3 col-lg-6 col-md-6 fade-in-up delay-<?= array_search($k, array_keys($stats)) + 1 ?>">
        <div class="stats-card <?= $k ?> shadow">
            <div class="card-body text-center p-4">
                <div class="d-flex justify-content-center mb-3">
                    <div class="rounded-circle p-3" style="background: rgba(255, 255, 255, 0.1); width: 70px; height: 70px;">
                        <i class="bi bi-<?= t('icon_' . $k) ?>-fill" 
                           style="font-size: 2rem; color: <?= $k === 'present' ? '#4facfe' : ($k === 'absent' ? '#f5576c' : ($k === 'late' ? '#f6d365' : '#5ee7df')) ?>"></i>
                    </div>
                </div>
                <div class="text-uppercase small fw-bold mb-2" style="letter-spacing: 1px; color: inherit;">
                    <?= t('stat_' . $k) ?>
                </div>
                <div class="stat-number mb-2">
                    <?= $v ?>
                </div>
                <div class="small mb-3" style="color: inherit;">
                    <?= $percent($v) ?><?= t('stat_percentage') ?>
                </div>
                <div class="stat-progress">
                    <div class="progress-bar" role="progressbar" 
                         style="width: <?= $percent($v) ?>%; background: <?= $k === 'present' ? 'var(--success-gradient)' : ($k === 'absent' ? 'var(--danger-gradient)' : ($k === 'late' ? 'var(--warning-gradient)' : 'var(--info-gradient)')) ?>;"></div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- ENHANCED INTEGRITY ALERT -->
<div class="integrity-alert fade-in-up delay-4">
    <div class="d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle p-2" style="background: rgba(255, 255, 255, 0.1);">
                <i class="bi bi-shield-fill-check text-primary" style="font-size: 1.5rem;"></i>
            </div>
            <div>
                <h6 class="fw-bold mb-1"><?= t('integrity_title') ?></h6>
                <p class="small mb-0"><?= t('integrity_description') ?></p>
            </div>
        </div>
        <div class="d-flex align-items-center gap-4">
            <div class="text-center">
                <div class="h2 fw-bold mb-0"><?= $total ?></div>
                <div class="small"><?= t('table_student_name') ?></div>
            </div>
            <span class="integrity-badge bg-<?= $total ? 'success' : 'danger' ?> text-white">
                <i class="bi bi-<?= $total ? 'check-circle' : 'exclamation-circle' ?>-fill"></i>
                <?= $total ? t('integrity_complete') : t('integrity_empty') ?>
            </span>
        </div>
    </div>
</div>

<!-- ENHANCED FILTER SECTION -->
<div class="filter-section fade-in-up delay-3">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h6 class="fw-bold mb-0"><i class="bi bi-funnel-fill text-primary"></i> <?= t('filter_title') ?></h6>
        <span class="badge bg-primary"><?= $total ?> <?= t('filter_records') ?></span>
    </div>
    <div class="d-flex gap-3 flex-wrap">
        <div class="filter-input flex-grow-1">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" class="form-control" placeholder="<?= t('filter_search_placeholder') ?>">
        </div>
        <select class="form-select w-auto" id="statusFilter" style="min-width: 200px;">
            <option value=""><?= t('filter_status_all') ?></option>
            <option value="present"><?= t('filter_status_present') ?></option>
            <option value="absent"><?= t('filter_status_absent') ?></option>
            <option value="late"><?= t('filter_status_late') ?></option>
            <option value="excused"><?= t('filter_status_excused') ?></option>
        </select>
        <button class="btn btn-glass" onclick="applyFilters()">
            <i class="bi bi-filter"></i> <?= t('btn_apply_filters') ?>
        </button>
    </div>
</div>

<!-- ENHANCED STUDENT TABLE -->
<div class="table-container fade-in-up delay-4">
    <div class="p-4 border-bottom">
        <h6 class="fw-bold mb-0">
            <i class="bi bi-people-fill text-primary"></i>
            <?= t('table_title') ?>
            <span class="badge bg-primary ms-2"><?= $total ?></span>
        </h6>
    </div>
   <div class="table-responsive">
    <table class="table table-hover align-middle mb-0">
        <thead>
            <tr>
                <th style="width: 60px;"><?= t('table_number') ?></th>
                <th><?= t('table_student_name') ?></th>
                <th style="width: 180px;"><?= t('table_attendance_status') ?></th>
                <th class="text-center" style="width: 140px;"><?= t('table_actions') ?></th>
            </tr>
        </thead>
        <tbody>
        <?php $i = 1; foreach ($rows as $r): ?>
            <tr class="<?= $r['status'] === 'absent' ? 'table-light' : '' ?>">
                
                <!-- # -->
                <td class="fw-bold text-muted">
                    <?= $i++ ?>
                </td>

                <!-- Student -->
                <td>
                    <div class="d-flex align-items-center gap-3">
                        <div class="rounded-circle d-flex align-items-center justify-content-center"
                             style="width:40px;height:40px;background:#eef2ff;">
                            <i class="bi bi-person-fill text-primary"></i>
                        </div>
                        <span class="fw-medium">
                            <?= htmlspecialchars($r['name']) ?>
                        </span>
                    </div>
                </td>

                <!-- Status -->
                <td>
                    <span class="badge rounded-pill px-3 py-2
                        <?= $r['status']==='present' ? 'bg-success' :
                           ($r['status']==='absent' ? 'bg-danger' :
                           ($r['status']==='late' ? 'bg-warning text-dark' : 'bg-info')) ?>">
                        <?= t('status_' . $r['status']) ?>
                    </span>
                </td>

                <!-- Actions -->
                <td class="text-center">
                    <a href="student_attendance_details.php?student_id=<?= $r['id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-eye"></i> <?= t('btn_view_student') ?>
                    </a>
                </td>

            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

</div>

<!-- FOOTER -->
<div class="mt-5 text-center fade-in-up">
    <div class="small text-muted">
        <i class="bi bi-info-circle-fill"></i>
        <?= t('footer_generated') ?> <?= date('F j, Y \a\t g:i A') ?> • 
        <a href="#" class="text-decoration-none text-primary"><?= t('footer_help') ?></a>
    </div>
</div>

</div>

<script>
// JavaScript translations
const jsTranslations = {
    filter_records: "<?= t('filter_records') ?>",
    easter_egg_title: "<?= t('easter_egg_title') ?>",
    search_placeholder: "<?= t('filter_search_placeholder') ?>"
};

const statusFilter = document.getElementById('statusFilter');
const searchInput = document.getElementById('searchInput');

function applyFilters() {
    const status = statusFilter.value.toLowerCase();
    const search = searchInput.value.toLowerCase();
    let visibleCount = 0;
    
    document.querySelectorAll('tbody tr').forEach(tr => {
        const name = tr.children[1].innerText.toLowerCase();
        const statusEl = tr.querySelector('.badge');
        const st = statusEl ? statusEl.innerText.toLowerCase() : '';
        const okStatus = !status || st.includes(status);
        const okSearch = !search || name.includes(search);
        
        if (okStatus && okSearch) {
            tr.style.display = '';
            visibleCount++;
            tr.classList.add('animate__animated', 'animate__fadeIn');
            setTimeout(() => tr.classList.remove('animate__animated', 'animate__fadeIn'), 500);
        } else {
            tr.style.display = 'none';
            tr.classList.remove('animate__animated', 'animate__fadeIn');
        }
    });
    
    // Update count badge with a fun animation
    const countBadge = document.querySelector('.filter-section .badge');
    if (countBadge) {
        countBadge.classList.add('animate__animated', 'animate__pulse');
        setTimeout(() => {
            countBadge.textContent = `${visibleCount} ${jsTranslations.filter_records}`;
            setTimeout(() => countBadge.classList.remove('animate__animated', 'animate__pulse'), 500);
        }, 200);
    }
}

// Auto-apply filters on input
statusFilter.addEventListener('change', applyFilters);
searchInput.addEventListener('keyup', applyFilters);

// Add animation on load
document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.fade-in-up').forEach((el, index) => {
        el.style.animationDelay = `${index * 0.05}s`;
    });
    
    // Add confetti to stats cards on hover (because why not?)
    document.querySelectorAll('.stats-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px) scale(1.03) rotateX(5deg)';
            this.style.boxShadow = 'var(--shadow-hover)';
            
            if(this.classList.contains('present')) {
                const emoji = document.createElement('div');
                emoji.innerHTML = '🎉';
                emoji.style.position = 'absolute';
                emoji.style.top = '10px';
                emoji.style.right = '10px';
                emoji.style.fontSize = '1.5rem';
                emoji.style.zIndex = '100';
                emoji.style.opacity = '0';
                emoji.style.animation = 'fadeInUp 0.5s forwards';
                this.appendChild(emoji);
                
                setTimeout(() => {
                    if(this.contains(emoji)) this.removeChild(emoji);
                }, 1000);
            }
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1) rotateX(0)';
            this.style.boxShadow = 'var(--shadow-epic)';
        });
    });
    
    // Easter egg: Click the header 5 times
    let clickCount = 0;
    const header = document.querySelector('.session-header');
    header.addEventListener('click', () => {
        clickCount++;
        if (clickCount === 5) {
            const title = document.querySelector('.header-content h1');
            const original = title.textContent;
            title.textContent = jsTranslations.easter_egg_title;
            title.style.animation = 'rainbow 2s linear infinite';
            
            setTimeout(() => {
                title.textContent = original;
                title.style.animation = '';
            }, 3000);
            clickCount = 0;
        }
    });
    
    // Auto-animate progress bars
    setTimeout(() => {
        document.querySelectorAll('.progress-bar').forEach(bar => {
            const width = bar.style.width;
            bar.style.width = '0';
            setTimeout(() => {
                bar.style.width = width;
            }, 100);
        });
    }, 500);
    
    // RTL-specific adjustments
    const isRTL = document.documentElement.dir === 'rtl';
    if (isRTL) {
        // Adjust position of emoji in stats cards
        document.querySelectorAll('.stats-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                if(this.classList.contains('present')) {
                    const emoji = document.createElement('div');
                    emoji.innerHTML = '🎉';
                    emoji.style.position = 'absolute';
                    emoji.style.top = '10px';
                    emoji.style.left = '10px'; // Changed from right to left for RTL
                    emoji.style.right = 'auto';
                    emoji.style.fontSize = '1.5rem';
                    emoji.style.zIndex = '100';
                    emoji.style.opacity = '0';
                    emoji.style.animation = 'fadeInUp 0.5s forwards';
                    this.appendChild(emoji);
                    
                    setTimeout(() => {
                        if(this.contains(emoji)) this.removeChild(emoji);
                    }, 1000);
                }
            });
        });
        
        // Adjust filter input icon position
        const searchIcon = document.querySelector('.filter-input i');
        if (searchIcon) {
            searchIcon.style.left = 'auto';
            searchIcon.style.right = '12px';
        }
    }
});

</script>
</body>
</html>

<style>
    /* === EPIC GLOBAL STYLES === */
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --danger-gradient: linear-gradient(135deg, #f5576c 0%, #f093fb 100%);
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            --info-gradient: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%);
            --dark-gradient: linear-gradient(135deg, #2c3e50 0%, #4a6491 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --shadow-epic: 0 20px 60px rgba(0, 0, 0, 0.3), 0 0 0 1px rgba(255, 255, 255, 0.1);
            --shadow-hover: 0 30px 80px rgba(0, 0, 0, 0.4), 0 0 0 1px rgba(255, 255, 255, 0.2);
            --neon-glow: 0 0 20px rgba(102, 126, 234, 0.5);
        }

        * {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: 
                radial-gradient(circle at 20% 80%, rgba(102, 126, 234, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(253, 203, 110, 0.1) 0%, transparent 50%);
            z-index: -1;
        }

        /* === MODERN CONTAINER === */
        .container {
            max-width: 1400px;
            padding: 2rem 1.5rem;
            position: relative;
            z-index: 1;
        }

        /* === EPIC HEADER STYLES === */
        .session-header {
            background: var(--dark-gradient);
            background-size: 200% 200%;
            animation: gradientShift 15s ease infinite;
            border-radius: 24px;
            color: white;
            margin-bottom: 3rem;
            position: relative;
            overflow: hidden;
            box-shadow: var(--shadow-epic);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
        }

        .session-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f5576c, #f6d365);
            background-size: 400% 100%;
            animation: rainbow 8s linear infinite;
        }

        @keyframes gradientShift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        @keyframes rainbow {
            0% { background-position: 0% 50%; }
            100% { background-position: 400% 50%; }
        }

        .header-content h1 {
            font-family: 'Montserrat', sans-serif;
            font-weight: 900;
            font-size: 2.5rem;
            background: linear-gradient(135deg, #fff 0%, #a5b4fc 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .text-highlight {
            background: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }

        /* === BADGE OF AWESOMENESS === */
        .session-badge {
            padding: 0.8rem 1.5rem;
            border-radius: 50px;
            font-weight: 600;
            letter-spacing: 0.5px;
            box-shadow: var(--shadow-epic);
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            overflow: hidden;
        }

        .session-badge::after {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: rotate(45deg);
            animation: shine 3s infinite;
        }

        @keyframes shine {
            0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
            100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
        }

        /* === STATS CARDS THAT WILL BLOW YOUR MIND === */
        .stats-card {
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            border: none;
            height: 100%;
            cursor: pointer;
            transform-style: preserve-3d;
            perspective: 1000px;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            inset: 0;
            background: inherit;
            filter: blur(20px);
            opacity: 0;
            transition: opacity 0.5s;
        }

        .stats-card:hover::before {
            opacity: 0.5;
        }

        .stats-card.present { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: white; }
        .stats-card.absent { background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%); color: white; }
        .stats-card.late { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #2c3e50; }
        .stats-card.excused { background: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%); color: white; }

        .stat-number {
            font-size: 3.5rem;
            font-weight: 800;
            font-family: 'Montserrat', sans-serif;
            line-height: 1;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
        }

        .stat-progress {
            height: 6px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 3px;
            overflow: hidden;
        }

        .stat-progress .progress-bar {
            height: 100%;
            border-radius: 3px;
            transition: width 1.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        /* === INTEGRITY ALERT WITH SUPERPOWERS === */
        .integrity-alert {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.9) 0%, rgba(255, 255, 255, 0.7) 100%);
            border-radius: 20px;
            padding: 1.5rem 2rem;
            margin: 2rem 0;
            box-shadow: var(--shadow-epic);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            position: relative;
            overflow: hidden;
        }

        .integrity-alert::after {
            content: '🔐';
            position: absolute;
            right: -30px;
            top: -30px;
            font-size: 8rem;
            opacity: 0.05;
            transform: rotate(15deg);
        }

        .integrity-badge {
            padding: 0.6rem 1.2rem;
            border-radius: 50px;
            font-weight: 600;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        /* === FILTER SECTION - MORE FILTERS THAN A COFFEE SHOP === */
        .filter-section {
            background: white;
            border-radius: 20px;
            padding: 1.5rem 2rem;
            box-shadow: var(--shadow-epic);
            margin: 2rem 0;
            border: 1px solid #e9ecef;
        }

        .filter-input {
            position: relative;
            flex: 1;
        }

        .filter-input i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            z-index: 1;
        }

        .filter-input input {
            padding-left: 3rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            height: 48px;
            font-size: 1rem;
        }

        .filter-input input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        /* === TABLE THAT WILL MAKE EXCEL JEALOUS === */
        .table-container {
            background: white;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: var(--shadow-epic);
            margin: 2rem 0;
            border: 1px solid #e9ecef;
        }

        .table-container table {
            margin-bottom: 0;
        }

        .table-container thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .table-container th {
            border: none;
            padding: 1.25rem 1.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        .table-container tbody tr {
            border-bottom: 1px solid #f1f3f4;
        }

        .table-container tbody tr:hover {
            background: linear-gradient(90deg, rgba(102, 126, 234, 0.05) 0%, transparent 100%);
            transform: translateX(8px);
        }

        .table-container td {
            padding: 1.25rem 1.5rem;
            vertical-align: middle;
            border: none;
        }

        .absent-row {
            position: relative;
        }

        .absent-row::before {
            content: '⚠️';
            position: absolute;
            left: -25px;
            top: 50%;
            transform: translateY(-50%);
            font-size: 1.2rem;
            opacity: 0;
            transition: opacity 0.3s;
        }

        .absent-row:hover::before {
            opacity: 1;
        }

        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 50px;
            font-weight: 500;
            font-size: 0.85rem;
            min-width: 100px;
            text-align: center;
            display: inline-block;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .status-badge.bg-success { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important; }
        .status-badge.bg-danger { background: linear-gradient(135deg, #f5576c 0%, #f093fb 100%) !important; }
        .status-badge.bg-warning { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%) !important; color: #2c3e50; }
        .status-badge.bg-info { background: linear-gradient(135deg, #5ee7df 0%, #b490ca 100%) !important; }

        /* === BUTTONS THAT DEMAND ATTENTION === */
        .btn-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            color: white;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }

        .btn-gradient:hover {
            background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.4);
        }

        .btn-glass {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #2c3e50;
            padding: 0.75rem 1.5rem;
            border-radius: 12px;
        }

        .btn-glass:hover {
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
        }

        /* === ANIMATIONS THAT WOULD MAKE DISNEY PROUD === */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .fade-in-up {
            animation: fadeInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1) forwards;
            opacity: 0;
        }

        .delay-1 { animation-delay: 0.1s !important; }
        .delay-2 { animation-delay: 0.2s !important; }
        .delay-3 { animation-delay: 0.3s !important; }
        .delay-4 { animation-delay: 0.4s !important; }
        .delay-5 { animation-delay: 0.5s !important; }

        /* === MOBILE RESPONSIVENESS - WORKS ON A WATCH === */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .session-header {
                border-radius: 16px;
                padding: 1.5rem !important;
            }
            
            .header-content h1 {
                font-size: 1.75rem;
            }
            
            .stats-card .card-body {
                padding: 1.5rem !important;
            }
            
            .stat-number {
                font-size: 2.5rem;
            }
            
            .table-container {
                border-radius: 16px;
            }
            
            .table-container th,
            .table-container td {
                padding: 1rem;
            }
            
            .filter-section .d-flex {
                flex-direction: column;
                gap: 1rem !important;
            }
            
            .filter-section .form-select {
                width: 100% !important;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .header-content .d-flex.gap-3 {
                gap: 1rem !important;
                flex-wrap: wrap;
            }
        }

        @media (max-width: 576px) {
            .session-badge {
                font-size: 0.8rem;
                padding: 0.6rem 1rem;
            }
            
            .integrity-alert {
                padding: 1rem;
            }
            
            .integrity-alert .d-flex {
                flex-direction: column;
                text-align: center;
                gap: 1rem !important;
            }
            
            .status-badge {
                min-width: 80px;
                padding: 0.4rem 0.8rem;
                font-size: 0.75rem;
            }
            
            body::before {
                background: none;
            }
        }

        /* === HILARIOUS EASTER EGGS === */
        @media (hover: hover) {
            .table-container tbody tr:nth-child(3n):hover {
                background: linear-gradient(90deg, rgba(253, 203, 110, 0.1) 0%, transparent 100%);
            }
            
            .table-container tbody tr:nth-child(5n):hover td:first-child::after {
                content: '✨';
                margin-left: 5px;
            }
        }

        /* === PRINT STYLES - FOR WHEN PAPER STILL EXISTS === */
        @media print {
            .session-header,
            .stats-card,
            .integrity-alert,
            .filter-section,
            .btn,
            .badge {
                box-shadow: none !important;
                border: 1px solid #ddd !important;
            }
            
            .btn-gradient,
            .btn-glass {
                background: white !important;
                color: black !important;
                border: 1px solid #ddd !important;
            }
        }

        /* === DARK MODE READY === */
        @media (prefers-color-scheme: dark) {
            body {
                background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
                color: #e9ecef;
            }
            
            .filter-section,
            .table-container {
                background: #2c3e50;
                color: #e9ecef;
                border-color: #4a6491;
            }
            
            .filter-input input,
            .form-select {
                background: #34495e;
                border-color: #4a6491;
                color: #e9ecef;
            }
            
            .table-container tbody tr {
                border-bottom-color: #4a6491;
            }
            
            .btn-glass {
                background: rgba(255, 255, 255, 0.05);
                border-color: rgba(255, 255, 255, 0.1);
                color: #e9ecef;
            }
        }

        /* === ACCESSIBILITY - FOR EVERYONE INCLUDING YOUR GRANDMA === */
        @media (prefers-reduced-motion: reduce) {
            * {
                animation: none !important;
                transition: none !important;
            }
            
            .fade-in-up {
                opacity: 1;
                transform: none;
            }
        }
</style>