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
        'page_title' => 'Exams Dashboard',
        'header_title' => 'Exams Dashboard',
        'year_filter_all' => 'All Academic Years',
        'current_badge' => 'Current',
        
        // Summary Cards
        'summary_total_exams' => 'Total Exams',
        'summary_midterm_exams' => 'Midterm Exams',
        'summary_final_exams' => 'Final Exams',
        'summary_active_classes' => 'Active Classes',
        'summary_sections' => 'Sections',
        
        // Controls
        'btn_table_view' => 'Table View',
        'btn_folder_view' => 'Folder View',
        'btn_add_exam' => 'Add Exam',
        'btn_expand_all' => 'Expand All',
        'btn_collapse_all' => 'Collapse All',
        
        // Table Headers
        'table_exam_name' => 'Exam Name',
        'table_type' => 'Type',
        'table_section' => 'Section',
        'table_class' => 'Class',
        'table_subjects' => 'Subjects',
        'table_date_range' => 'Date Range',
        'table_created' => 'Created',
        'table_actions' => 'Actions',
        
        // Exam Types
        'type_midterm' => 'Midterm',
        'type_final' => 'Final',
        
        // Folder View Labels
        'folder_section' => 'Section',
        'folder_class' => 'Class',
        'folder_exams_count' => 'exams',
        
        // Empty State
        'empty_title' => 'No Exams Found',
        'empty_message' => 'No active exams available for the selected criteria.',
        
        // Action Buttons
        'btn_view_details' => 'View Details',
        'btn_delete' => 'Delete',
        'btn_delete_confirm' => 'Are you sure you want to delete this exam?',
        'btn_delete_no_subjects' => 'No subjects',
        
        // Year Summary
        'year_summary_exams' => 'exams',
        'year_summary_midterms' => 'midterms',
        'year_summary_finals' => 'finals',
        
        // Date Format
        'date_n_a' => 'N/A',
        'Academic Year' => 'Academic Year',
    ],
    2 => [ // Arabic
        // Page Title & Headers
        'page_title' => 'لوحة الامتحانات',
        'header_title' => 'لوحة الامتحانات',
        'year_filter_all' => 'جميع السنوات الأكاديمية',
        'current_badge' => 'حالي',
        
        // Summary Cards
        'summary_total_exams' => 'إجمالي الامتحانات',
        'summary_midterm_exams' => 'امتحانات منتصف الفصل',
        'summary_final_exams' => 'امتحانات نهاية الفصل',
        'summary_active_classes' => 'الصفوف النشطة',
        'summary_sections' => 'الأقسام',
        
        // Controls
        'btn_table_view' => 'عرض الجدول',
        'btn_folder_view' => 'عرض المجلدات',
        'btn_add_exam' => 'إضافة امتحان',
        'btn_expand_all' => 'توسيع الكل',
        'btn_collapse_all' => 'طي الكل',
        
        // Table Headers
        'table_exam_name' => 'اسم الامتحان',
        'table_type' => 'النوع',
        'table_section' => 'القسم',
        'table_class' => 'الصف',
        'table_subjects' => 'المواد',
        'table_date_range' => 'نطاق التاريخ',
        'table_created' => 'تاريخ الإنشاء',
        'table_actions' => 'الإجراءات',
        
        // Exam Types
        'type_midterm' => 'منتصف الفصل',
        'type_final' => 'نهاية الفصل',
        
        // Folder View Labels
        'folder_section' => 'القسم',
        'folder_class' => 'الصف',
        'folder_exams_count' => 'امتحانات',



        
        // Empty State
        'empty_title' => 'لم يتم العثور على امتحانات',
        'empty_message' => 'لا توجد امتحانات نشطة للمعايير المحددة.',
        
        // Action Buttons
        'btn_view_details' => 'عرض التفاصيل',
        'btn_delete' => 'حذف',
        'btn_delete_confirm' => 'هل أنت متأكد أنك تريد حذف هذا الامتحان؟',
        'btn_delete_no_subjects' => 'لا توجد مواد',
        
        // Year Summary
        'year_summary_exams' => 'امتحانات',
        'year_summary_midterms' => 'منتصف الفصل',
        'year_summary_finals' => 'نهاية الفصل',
        
        // Date Format
        'date_n_a' => 'غير متوفر',
        'Academic Year' => 'السنة الدراسية',
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

// Get current academic year
$current_academic_year = '2025-2026'; // Example - adjust based on your logic

// ---------- SECURITY: CSRF Token ----------
$csrf_token = bin2hex(random_bytes(32));
$_SESSION['csrf_token'] = $csrf_token;

// ---------- PERFORMANCE: Single optimized query ----------
$sql = "
SELECT 
    e.id AS exam_id,
    e.name AS exam_name,
    e.exam_type,
    e.class_id,
    e.section_id,
    e.academic_year,
    e.start_date,
    e.end_date,
    e.created_at,
    e.status,
    c.name AS class_name,
    s.name AS section_name,
    COUNT(DISTINCT es.id) AS subject_count
FROM exams e
LEFT JOIN classes c ON c.id = e.class_id AND c.id IS NOT NULL
LEFT JOIN sections s ON s.id = e.section_id AND s.id IS NOT NULL
LEFT JOIN exam_subjects es ON es.exam_id = e.id
WHERE e.is_deleted = 0 
    AND e.status = 'active'
    AND c.id IS NOT NULL
    AND s.id IS NOT NULL
GROUP BY e.id, e.name, e.exam_type, e.class_id, e.section_id, e.academic_year, 
         e.start_date, e.end_date, e.created_at, e.status, c.name, s.name
ORDER BY e.academic_year DESC, e.created_at DESC
";

$result = $conn->query($sql);
if (!$result) {
    error_log("Database error: " . $conn->error);
    die("Error loading exams data. Please try again later.");
}

// ---------- Process data into structured arrays ----------
$exams_by_year = [];
$summary_by_year = [];
$all_years = [];

while ($row = $result->fetch_assoc()) {
    $year = $row['academic_year'];
    $section_id = $row['section_id'];
    $class_id = $row['class_id'];
    
    // Initialize year if not exists
    if (!isset($exams_by_year[$year])) {
        $exams_by_year[$year] = [];
        $summary_by_year[$year] = [
            'total' => 0,
            'midterm' => 0,
            'final' => 0,
            'classes' => [],
            'sections' => []
        ];
        $all_years[] = $year;
    }
    
    // Initialize section if not exists
    if (!isset($exams_by_year[$year][$section_id])) {
        $exams_by_year[$year][$section_id] = [
            'name' => $row['section_name'],
            'classes' => []
        ];
    }
    
    // Initialize class if not exists
    if (!isset($exams_by_year[$year][$section_id]['classes'][$class_id])) {
        $exams_by_year[$year][$section_id]['classes'][$class_id] = [
            'name' => $row['class_name'],
            'exams' => []
        ];
    }
    
    // Add exam
    $exams_by_year[$year][$section_id]['classes'][$class_id]['exams'][] = [
        'id' => $row['exam_id'],
        'name' => $row['exam_name'],
        'type' => $row['exam_type'],
        'subjects' => $row['subject_count'],
        'start_date' => $row['start_date'],
        'end_date' => $row['end_date'],
        'created_at' => $row['created_at']
    ];
    
    // Update summary for this year
    $summary_by_year[$year]['total']++;
    if ($row['exam_type'] == 'midterm') {
        $summary_by_year[$year]['midterm']++;
    } elseif ($row['exam_type'] == 'final') {
        $summary_by_year[$year]['final']++;
    }
    $summary_by_year[$year]['classes'][$class_id] = true;
    $summary_by_year[$year]['sections'][$section_id] = true;
}

// ---------- Overall Summary ----------
$overall_summary = [
    'total' => 0,
    'midterm' => 0,
    'final' => 0,
    'classes' => 0,
    'sections' => 0
];

foreach ($summary_by_year as $year => $summary) {
    $overall_summary['total'] += $summary['total'];
    $overall_summary['midterm'] += $summary['midterm'];
    $overall_summary['final'] += $summary['final'];
    $overall_summary['classes'] = max($overall_summary['classes'], count($summary['classes']));
    $overall_summary['sections'] = max($overall_summary['sections'], count($summary['sections']));
}

// ---------- Get theme ----------
$themeRow = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1")->fetch_assoc();
$themeMode = $themeRow['mode'] ?? 1;
$isDark = $themeMode == 0;

// Helper function for consistent date formatting
function format_date($date, $format = 'd M Y') {
    global $translations, $currentLangId;
    if (!$date) return t('date_n_a');
    return date($format, strtotime($date));
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : 'en' ?>" dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
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
            <h1><i class="fas fa-chart-line"></i> <?= t('header_title') ?></h1>
            <div class="year-filter">
                <select id="yearFilter">
                    <option value="all"><?= t('year_filter_all') ?></option>
                    <?php foreach ($all_years as $year): ?>
                        <option value="<?= htmlspecialchars($year) ?>"><?= htmlspecialchars($year) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="summary-grid">
            <div class="summary-card total">
                <i class="fas fa-file-alt"></i>
                <h3><?= $overall_summary['total'] ?></h3>
                <p><?= t('summary_total_exams') ?></p>
            </div>
            <div class="summary-card midterm">
                <i class="fas fa-clipboard-check"></i>
                <h3><?= $overall_summary['midterm'] ?></h3>
                <p><?= t('summary_midterm_exams') ?></p>
            </div>
            <div class="summary-card final">
                <i class="fas fa-flag-checkered"></i>
                <h3><?= $overall_summary['final'] ?></h3>
                <p><?= t('summary_final_exams') ?></p>
            </div>
            <div class="summary-card classes">
                <i class="fas fa-school"></i>
                <h3><?= $overall_summary['classes'] ?></h3>
                <p><?= t('summary_active_classes') ?></p>
            </div>
            <div class="summary-card sections">
                <i class="fas fa-layer-group"></i>
                <h3><?= $overall_summary['sections'] ?></h3>
                <p><?= t('summary_sections') ?></p>
            </div>
        </div>

        <!-- Controls -->
        <div class="controls">
            <div class="view-toggle">
                <button class="toggle-btn active" data-view="table">
                    <i class="fas fa-table"></i> <?= t('btn_table_view') ?>
                </button>
                <button class="toggle-btn" data-view="folder">
                    <i class="fas fa-folder"></i> <?= t('btn_folder_view') ?>
                </button>
            </div>
            <div>
                <a href="add_exam.php" class="btn btn-view" style="text-decoration: none;"><?= t('btn_add_exam') ?></a>
                <button class="btn btn-view" id="expandAll">
                    <i class="fas fa-expand"></i> <?= t('btn_expand_all') ?>
                </button>
                <button class="btn btn-view" id="collapseAll">
                    <i class="fas fa-compress"></i> <?= t('btn_collapse_all') ?>
                </button>
            </div>
        </div>

        <!-- Exams Content -->
        <div id="dashboardContent">
            <?php if (empty($exams_by_year)): ?>
                <div class="empty-state">
                    <i class="fas fa-inbox"></i>
                    <h3><?= t('empty_title') ?></h3>
                    <p><?= t('empty_message') ?></p>
                </div>
            <?php else: ?>
                <?php foreach ($exams_by_year as $year => $sections): ?>
                    <div class="year-section" data-year="<?= htmlspecialchars($year) ?>">
                        <div class="year-header">
                           <h2>
                            <i class="fas fa-calendar-alt"></i>
                            <?= htmlspecialchars($year) ?> : <?= t('Academic Year') ?>
                            <?php if ($year == $current_academic_year): ?>
                                <span class="badge" style="background: rgba(0,123,255,0.2); color: var(--primary-color);">
                                    <?= t('current_badge') ?>
                                </span>
                            <?php endif; ?>
                        </h2>

                            <div class="year-summary">
                                <span><i class="fas fa-file-alt"></i> <?= $summary_by_year[$year]['total'] ?> <?= t('year_summary_exams') ?></span>
                                <span><i class="fas fa-clipboard-check"></i> <?= $summary_by_year[$year]['midterm'] ?> <?= t('year_summary_midterms') ?></span>
                                <span><i class="fas fa-flag-checkered"></i> <?= $summary_by_year[$year]['final'] ?> <?= t('year_summary_finals') ?></span>
                                <span><i class="fas fa-school"></i> <?= count($summary_by_year[$year]['classes']) ?> <?= t('summary_active_classes') ?></span>
                                <span><i class="fas fa-layer-group"></i> <?= count($summary_by_year[$year]['sections']) ?> <?= t('summary_sections') ?></span>
                            </div>
                        </div>
                        <div class="year-content">
                            <!-- Table View -->
                            <div class="table-view">
                                <table class="exams-table">
                                    <thead>
                                        <tr>
                                            <th><?= t('table_exam_name') ?></th>
                                            <th><?= t('table_type') ?></th>
                                            <th><?= t('table_section') ?></th>
                                            <th><?= t('table_class') ?></th>
                                            <th><?= t('table_subjects') ?></th>
                                            <th><?= t('table_date_range') ?></th>
                                            <th><?= t('table_created') ?></th>
                                            <th><?= t('table_actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($sections as $section_id => $section): ?>
                                            <?php foreach ($section['classes'] as $class_id => $class): ?>
                                                <?php foreach ($class['exams'] as $exam): ?>
                                                    <tr data-year="<?= htmlspecialchars($year) ?>" 
                                                        data-section="<?= htmlspecialchars($section_id) ?>"
                                                        data-class="<?= htmlspecialchars($class_id) ?>">
                                                        <td>
                                                            <strong><?= htmlspecialchars($exam['name']) ?></strong>
                                                        </td>
                                                        <td>
                                                            <span class="badge <?= $exam['type'] ?>">
                                                                <?= t('type_' . $exam['type']) ?>
                                                            </span>
                                                        </td>
                                                        <td><?= htmlspecialchars($section['name']) ?></td>
                                                        <td><?= htmlspecialchars($class['name']) ?></td>
                                                        <td>
                                                            <?php if ($exam['subjects'] > 0): ?>
                                                                <span class="badge" style="background: rgba(108,117,125,0.2);">
                                                                    <?= $exam['subjects'] ?> <?= t('table_subjects') ?>
                                                                </span>
                                                            <?php else: ?>
                                                                <span class="badge" style="background: rgba(220,53,69,0.2); color: var(--danger-color);">
                                                                    <?= t('btn_delete_no_subjects') ?>
                                                                </span>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td>
                                                            <?php if ($exam['start_date'] && $exam['end_date']): ?>
                                                                <?= format_date($exam['start_date'], 'd M') ?> - <?= format_date($exam['end_date'], 'd M Y') ?>
                                                            <?php else: ?>
                                                                <?= t('date_n_a') ?>
                                                            <?php endif; ?>
                                                        </td>
                                                        <td><?= format_date($exam['created_at']) ?></td>
                                                        <td class="actions">
                                                            <a href="exam_details.php?exam_id=<?= $exam['id'] ?>&class_id=<?= $class_id ?>&section_id=<?= $section_id ?>" 
                                                               class="btn btn-view">
                                                                <i class="fas fa-eye"></i> <?= t('btn_view_details') ?>
                                                            </a>

                                                            <form action="exam_delete.php" method="POST" style="display: inline;">
                                                                <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
                                                                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                                <button type="submit" 
                                                                        class="btn btn-delete"
                                                                        onclick="return confirm('<?= t('btn_delete_confirm') ?>');">
                                                                    <i class="fas fa-trash"></i> <?= t('btn_delete') ?>
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            <?php endforeach; ?>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Folder View -->
                            <div class="folder-view">
                                <?php foreach ($exams_by_year as $year => $sections): ?>
                                    <?php foreach ($sections as $section_id => $section): ?>
                                        <div class="section-folder">
                                            <div class="section-header">
                                                <i class="fas fa-folder-open"></i>
                                                <h3><?= t('folder_section') ?>: <?= htmlspecialchars($section['name']) ?></h3>
                                            </div>
                                            <?php foreach ($section['classes'] as $class_id => $class): ?>
                                                <div class="class-folder">
                                                    <div class="class-header">
                                                        <i class="fas fa-book"></i>
                                                        <h4><?= t('folder_class') ?>: <?= htmlspecialchars($class['name']) ?></h4>
                                                        <span>(<?= count($class['exams']) ?> <?= t('folder_exams_count') ?>)</span>
                                                    </div>
                                                    <div class="exam-cards">
                                                        <?php foreach ($class['exams'] as $exam): ?>
                                                            <div class="exam-card">
                                                                <div class="exam-header">
                                                                    <div class="exam-name"><?= htmlspecialchars($exam['name']) ?></div>
                                                                    <span class="badge <?= $exam['type'] ?>">
                                                                        <?= t('type_' . $exam['type']) ?>
                                                                    </span>
                                                                </div>
                                                                <div class="exam-details">
                                                                    <span><i class="fas fa-book"></i> <?= $exam['subjects'] ?> <?= t('table_subjects') ?></span>
                                                                    <span><i class="fas fa-calendar"></i> <?= format_date($exam['created_at']) ?></span>
                                                                    <?php if ($exam['start_date'] && $exam['end_date']): ?>
                                                                        <span><i class="fas fa-calendar-alt"></i> <?= format_date($exam['start_date'], 'd M') ?> - <?= format_date($exam['end_date'], 'd M') ?></span>
                                                                    <?php endif; ?>
                                                                </div>
                                                                <div class="actions">
                                                                    <a href="exam_details.php?exam_id=<?= $exam['id'] ?>&class_id=<?= $class_id ?>&section_id=<?= $section_id ?>" 
                                                                       class="btn btn-view">
                                                                        <i class="fas fa-eye"></i> <?= t('btn_view_details') ?>
                                                                    </a>
                                                                    <form action="exam_delete.php" method="POST" style="display: inline;">
                                                                        <input type="hidden" name="exam_id" value="<?= $exam['id'] ?>">
                                                                        <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                                                                        <button type="submit" 
                                                                                class="btn btn-delete"
                                                                                onclick="return confirm('<?= t('btn_delete_confirm') ?>');">
                                                                            <i class="fas fa-trash"></i> <?= t('btn_delete') ?>
                                                                        </button>
                                                                    </form>
                                                                </div>
                                                            </div>
                                                        <?php endforeach; ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    <?php endforeach; ?>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // View Toggle
        document.querySelectorAll('.toggle-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                const view = this.dataset.view;
                
                // Update active button
                document.querySelectorAll('.toggle-btn').forEach(b => b.classList.remove('active'));
                this.classList.add('active');
                
                // Toggle views
                document.querySelectorAll('.table-view').forEach(el => {
                    el.style.display = view === 'table' ? 'block' : 'none';
                });
                document.querySelectorAll('.folder-view').forEach(el => {
                    el.style.display = view === 'folder' ? 'block' : 'none';
                });
                
                // Save preference to localStorage
                localStorage.setItem('examsViewMode', view);
            });
        });

        // Year Filter
        document.getElementById('yearFilter').addEventListener('change', function() {
            const selectedYear = this.value;
            document.querySelectorAll('.year-section').forEach(section => {
                if (selectedYear === 'all' || section.dataset.year === selectedYear) {
                    section.style.display = 'block';
                } else {
                    section.style.display = 'none';
                }
            });
        });

        // Expand/Collapse All
        document.getElementById('expandAll').addEventListener('click', () => {
            document.querySelectorAll('.year-content').forEach(content => {
                content.style.display = 'block';
            });
        });

        document.getElementById('collapseAll').addEventListener('click', () => {
            document.querySelectorAll('.year-content').forEach(content => {
                content.style.display = 'none';
            });
        });

        // Year header click to toggle content
        document.querySelectorAll('.year-header').forEach(header => {
            header.addEventListener('click', function() {
                const content = this.nextElementSibling;
                content.style.display = content.style.display === 'none' ? 'block' : 'none';
            });
        });

        // Load saved view preference
        document.addEventListener('DOMContentLoaded', function() {
            const savedView = localStorage.getItem('examsViewMode') || 'table';
            const btn = document.querySelector(`.toggle-btn[data-view="${savedView}"]`);
            if (btn) btn.click();
            
            // Auto-expand current academic year
            const currentYearSection = document.querySelector(`[data-year="<?= $current_academic_year ?>"]`);
            if (currentYearSection) {
                currentYearSection.querySelector('.year-content').style.display = 'block';
            }
        });

        // Search functionality (optional enhancement)
        function searchExams() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            document.querySelectorAll('.exams-table tbody tr').forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchTerm) ? '' : 'none';
            });
        }
    </script>
</body>
</html>

  <style>
        :root {
            <?php if ($isDark): ?>
            --bg-primary: #121212;
            --bg-secondary: #1f1f1f;
            --bg-card: #2d2d2d;
            --text-primary: #f0f0f0;
            --text-secondary: #b0b0b0;
            --border-color: #444;
            --primary-color: #80dfff;
            --success-color: #4CAF50;
            --warning-color: #ff9800;
            --danger-color: #f44336;
            --hover-bg: #363636;
            <?php else: ?>
            --bg-primary: #f8f9fa;
            --bg-secondary: #ffffff;
            --bg-card: #ffffff;
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --border-color: #dee2e6;
            --primary-color: #007bff;
            --success-color: #28a745;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --hover-bg: #f1f3f4;
            <?php endif; ?>
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: var(--bg-primary);
            color: var(--text-primary);
            line-height: 1.6;
            padding: 20px;
            min-height: 100vh;
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
            margin-bottom: 30px;
            flex-wrap: wrap;
            gap: 20px;
        }

        .header h1 {
            font-size: 2.2rem;
            background: linear-gradient(135deg, var(--primary-color), #6f42c1);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Summary Cards */
        .summary-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .summary-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--border-color);
        }

        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.15);
        }

        .summary-card i {
            font-size: 2rem;
            margin-bottom: 10px;
            color: var(--primary-color);
        }

        .summary-card.total i { color: var(--primary-color); }
        .summary-card.midterm i { color: var(--success-color); }
        .summary-card.final i { color: var(--warning-color); }
        .summary-card.classes i { color: #6f42c1; }
        .summary-card.sections i { color: #e83e8c; }

        .summary-card h3 {
            font-size: 1.5rem;
            margin-bottom: 5px;
        }

        .summary-card p {
            color: var(--text-secondary);
            font-size: 0.9rem;
        }

        /* Controls */
        .controls {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
            padding: 15px;
            background: var(--bg-card);
            border-radius: 10px;
            border: 1px solid var(--border-color);
        }

        .view-toggle {
            display: flex;
            gap: 10px;
        }

        .toggle-btn {
            padding: 8px 16px;
            background: var(--bg-secondary);
            border: 1px solid var(--border-color);
            border-radius: 6px;
            color: var(--text-primary);
            cursor: pointer;
            transition: all 0.2s;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .toggle-btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        .toggle-btn:hover:not(.active) {
            background: var(--hover-bg);
        }

        .year-filter select {
            padding: 8px 15px;
            border-radius: 6px;
            border: 1px solid var(--border-color);
            background: var(--bg-secondary);
            color: var(--text-primary);
            min-width: 150px;
        }

        /* Year Section */
        .year-section {
            margin-bottom: 30px;
        }

        .year-header {
            background: var(--bg-card);
            padding: 15px 20px;
            border-radius: 10px 10px 0 0;
            border: 1px solid var(--border-color);
            border-bottom: none;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: background 0.2s;
        }

        .year-header:hover {
            background: var(--hover-bg);
        }

        .year-header h2 {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .year-summary {
            display: flex;
            gap: 15px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .year-content {
            border: 1px solid var(--border-color);
            border-top: none;
            border-radius: 0 0 10px 10px;
            overflow: hidden;
        }

        /* Table View */
        .exams-table {
            width: 100%;
            border-collapse: collapse;
            background: var(--bg-card);
        }

        .exams-table th {
            background: var(--bg-secondary);
            padding: 15px;
            text-align: left;
            font-weight: 600;
            border-bottom: 2px solid var(--border-color);
            color: var(--text-primary);
        }

        .exams-table td {
            padding: 12px 15px;
            border-bottom: 1px solid var(--border-color);
        }

        .exams-table tr:hover {
            background: var(--hover-bg);
        }

        .badge {
            display: inline-block;
            padding: 3px 8px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 600;
        }

        .badge.midterm {
            background: rgba(76, 175, 80, 0.2);
            color: var(--success-color);
        }

        .badge.final {
            background: rgba(255, 152, 0, 0.2);
            color: var(--warning-color);
        }

        .actions {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 6px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .btn-view {
            background: var(--primary-color);
            color: white;
        }

        .btn-delete {
            background: var(--danger-color);
            color: white;
        }

        .btn:hover {
            opacity: 0.9;
            transform: translateY(-2px);
        }

        /* Folder View */
        .folder-view {
            display: none;
            padding: 20px;
            background: var(--bg-card);
        }

        .section-folder {
            margin-bottom: 25px;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border-color);
        }

        .class-folder {
            margin-left: 20px;
            margin-bottom: 20px;
        }

        .class-header {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            color: var(--text-secondary);
        }

        .exam-cards {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 15px;
            margin-left: 20px;
        }

        .exam-card {
            background: var(--bg-secondary);
            border-radius: 10px;
            padding: 15px;
            border: 1px solid var(--border-color);
            transition: all 0.3s ease;
        }

        .exam-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }

        .exam-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .exam-name {
            font-weight: 600;
            font-size: 1.1rem;
        }

        .exam-details {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            font-size: 0.9rem;
            color: var(--text-secondary);
        }

        .exam-details span {
            display: flex;
            align-items: center;
            gap: 5px;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: var(--text-secondary);
        }

        .empty-state i {
            font-size: 3rem;
            margin-bottom: 15px;
            opacity: 0.5;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .container {
                padding: 10px;
            }
            
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .summary-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .exams-table {
                display: block;
                overflow-x: auto;
            }
            
            .exam-cards {
                grid-template-columns: 1fr;
            }
            
            .year-summary {
                flex-wrap: wrap;
            }
        }

        @media (max-width: 480px) {
            .summary-grid {
                grid-template-columns: 1fr;
            }
            
            .actions {
                flex-direction: column;
            }
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .year-section {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</body>
</html>
