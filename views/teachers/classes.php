<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$today = date('Y-m-d');
$todayDay = date('l');

/* ===================== TRANSLATIONS ===================== */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Teacher Dashboard | Class Management',
        'dashboard_title' => 'Teacher Dashboard',
        'dashboard_subtitle' => 'Monitor and manage all your classes in one place',
        'no_classes' => 'No classes assigned',
        'no_classes_msg' => 'You haven\'t been assigned to any classes yet.',
        'request_class' => 'Request a Class',
        'my_classes' => 'My Classes',
        'students' => 'Students',
        'attendance_percent' => 'Attendance %',
        'trend' => 'Trend',
        'new_30d' => 'New (30d)',
        'good' => 'Good',
        'average' => 'Average',
        'critical' => 'Critical',
        'improving' => 'Improving',
        'declining' => 'Declining',
        'stable' => 'Stable',
        'todays_subjects' => 'Today\'s Subjects',
        'attendance_taken' => 'Attendance Taken',
        'missing_attendance' => 'Missing Attendance',
        'attendance_breakdown' => 'Attendance Breakdown',
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'excused' => 'Excused',
        'frequent_issues' => 'Frequent Absences/Late',
        'no_frequent_issues' => 'No frequent issues',
        'issues' => 'issues',
        'next_class_today' => 'Next Class Today',
        'at' => 'at',
        'attention_needed' => 'Attention Needed',
        'attendance_missing' => 'Attendance missing for today\'s classes',
        'critical_attendance' => 'Critical attendance level (< 60%)',
        'view_class_details' => 'View Class Details',
        'take_attendance' => 'Take Attendance',
        'view_history' => 'View History',
        'class_notes' => 'Class Notes',
        'notes_placeholder' => 'Add notes about this class (upcoming tests, student concerns, reminders)...',
        'save_note' => 'Save Note',
        'copyright' => '© School Management System | Teacher Dashboard v2.0'
    ],
    2 => [ // Arabic
        'page_title' => 'لوحة تحكم المعلم | إدارة الفصول',
        'dashboard_title' => 'لوحة تحكم المعلم',
        'dashboard_subtitle' => 'راقب وادفع جميع فصولك في مكان واحد',
        'no_classes' => 'لا توجد فصول معينة',
        'no_classes_msg' => 'لم يتم تعيينك في أي فصول بعد.',
        'request_class' => 'طلب فصل',
        'my_classes' => 'فصولي',
        'students' => 'الطلاب',
        'attendance_percent' => 'نسبة الحضور %',
        'trend' => 'الاتجاه',
        'new_30d' => 'جديد (30 يوم)',
        'good' => 'جيد',
        'average' => 'متوسط',
        'critical' => 'حرج',
        'improving' => 'يتحسن',
        'declining' => 'يتدهور',
        'stable' => 'مستقر',
        'todays_subjects' => 'مواد اليوم',
        'attendance_taken' => 'تم تسجيل الحضور',
        'missing_attendance' => 'الحضور مفقود',
        'attendance_breakdown' => 'تفاصيل الحضور',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'excused' => 'معذور',
        'frequent_issues' => 'الغيابات/التأخير المتكرر',
        'no_frequent_issues' => 'لا توجد مشاكل متكررة',
        'issues' => 'مشاكل',
        'next_class_today' => 'الفصل التالي اليوم',
        'at' => 'في',
        'attention_needed' => 'يتطلب الاهتمام',
        'attendance_missing' => 'الحضور مفقود لفصول اليوم',
        'critical_attendance' => 'مستوى حضور حرج (< 60%)',
        'view_class_details' => 'عرض تفاصيل الفصل',
        'take_attendance' => 'تسجيل الحضور',
        'view_history' => 'عرض السجل',
        'class_notes' => 'ملاحظات الفصل',
        'notes_placeholder' => 'أضف ملاحظات حول هذا الفصل (الاختبارات القادمة، مخاوف الطلاب، التذكيرات)...',
        'save_note' => 'حفظ الملاحظة',
        'copyright' => '© نظام إدارة المدارس | لوحة تحكم المعلم v2.0'
    ],
    3 => [ // Somali
        'page_title' => 'Dashboard-ka Macallinka | Maamulka Fasalka',
        'dashboard_title' => 'Dashboard-ka Macallinka',
        'dashboard_subtitle' => 'Maamulka Xuduurka dhamaan fasalladaada hal meel',
        'no_classes' => 'Fasallo lama qoondeyn',
        'no_classes_msg' => 'Weli lama qoondeyn fasallo.',
        'request_class' => 'Codso Fasalka',
        'my_classes' => 'Fasalladayda',
        'students' => 'Ardayda',
        'attendance_percent' => 'Xuduurka %',
        'trend' => 'Isbadallada',
        'new_30d' => 'Ardayda Cusub (30 maalmood u danbeyey)',
        'good' => 'Wanaagsan',
        'average' => 'Dhexdhexaad',
        'critical' => 'Khatar ah',
        'improving' => 'Si wanaagsan u kordhaya',
        'declining' => 'Si xun u dhacaya',
        'stable' => 'Xasilloon',
        'todays_subjects' => 'Maaddooyinka Maanta',
        'attendance_taken' => 'Xuduurki la qoray',
        'missing_attendance' => 'Xuduur la\'aan',
        'attendance_breakdown' => 'Kormarka Xuduurka',
        'present' => 'Jooga',
        'absent' => 'Maqan',
        'late' => 'Soo Daahey',
        'excused' => 'La fasaxay',
        'frequent_issues' => 'Maqnaansho/Daah Badan',
        'no_frequent_issues' => 'Dhibaatooyin badan ma jiraan',
        'issues' => 'mar soo daahey ama maqan',
        'next_class_today' => 'Fasalka Xiga Maanta',
        'at' => 'saacadda',
        'attention_needed' => 'Diyaarinta Loo Baahan Yahay',
        'attendance_missing' => 'Xuduur la\'aan fasallada maanta',
        'critical_attendance' => 'Heerka xuduur khatar ah (< 60%)',
        'view_class_details' => 'Arag Faahfaahinta Fasalka',
        'take_attendance' => 'Qor Xuduurka',
        'view_history' => 'Arag Taariikhda',
        'class_notes' => 'Note-ka Fasalka',
        'notes_placeholder' => 'Ku dar qoraallo fasalkan ku saabsan (imtixaannada soo socda, Xuduurka ardayda, IWM)...',
        'save_note' => 'Save garee',
        'copyright' => '© Nidaamka Maareynta Dugsiga | Dashboard-ka Macallinka v2.0'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

/* ===================== CLASSES ===================== */
$classesRes = $conn->query("
    SELECT DISTINCT 
        c.id AS class_id,
        c.name AS class_name
    FROM class_subjects cs
    JOIN classes c ON c.id = cs.class_id
    WHERE cs.teacher_id = $teacher_id
    ORDER BY c.name
");

$classes = [];
while ($row = $classesRes->fetch_assoc()) {
    $classes[] = $row;
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* ... (keep all existing CSS styles exactly as they are) ... */
        /* All CSS remains unchanged */
    </style>
</head>
<body>
    <div class="container-fluid">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <div>
                <h1 class="dashboard-title">
                    <i class="fas fa-chalkboard-teacher"></i> <?= t('dashboard_title') ?>
                </h1>
                <p class="text-muted mb-0"><?= t('dashboard_subtitle') ?></p>
            </div>
            <div class="date-display">
                <i class="fas fa-calendar-alt me-2"></i> <?= date('F j, Y') ?>
            </div>
        </div>

        <?php if (empty($classes)): ?>
            <!-- Empty State -->
            <div class="empty-state">
                <i class="fas fa-school"></i>
                <h3><?= t('no_classes') ?></h3>
                <p><?= t('no_classes_msg') ?></p>
                <a href="#" class="btn btn-custom btn-view mt-3"><?= t('request_class') ?></a>
            </div>
        <?php else: ?>
            <!-- Classes List -->
            <h4 class="mb-4" style="color: var(--primary-color); font-weight: 600;">
                <i class="fas fa-list-ul me-2"></i> <?= t('my_classes') ?> (<?= count($classes) ?>)
            </h4>

            <?php foreach ($classes as $class): ?>
            <?php
            $class_id = $class['class_id'];

            /* Students count */
            $students = $conn->query("
                SELECT COUNT(*) c 
                FROM class_students 
                WHERE class_id = $class_id
            ")->fetch_assoc()['c'];

            /* Subjects taught by teacher */
            $subjectsRes = $conn->query("
                SELECT s.name 
                FROM class_subjects cs
                JOIN subjects s ON s.id = cs.subject_id
                WHERE cs.teacher_id = $teacher_id
                  AND cs.class_id = $class_id
            ");

            $subjects = [];
            while ($s = $subjectsRes->fetch_assoc()) {
                $subjects[] = $s['name'];
            }

            /* Attendance stats */
            $att = $conn->query("
                SELECT 
                    SUM(status='present') present,
                    SUM(status='absent') absent,
                    SUM(status='late') late,
                    SUM(status='excused') excused,
                    COUNT(*) total
                FROM attendance
                WHERE class_id = $class_id
                  AND subject_id IN (
                      SELECT subject_id 
                      FROM class_subjects 
                      WHERE teacher_id = $teacher_id 
                        AND class_id = $class_id
                  )
            ")->fetch_assoc();

            $percent = $att['total'] > 0
                ? round(($att['present'] / $att['total']) * 100, 1)
                : 0;

            /* ================= TODAY'S SUBJECTS & ATTENDANCE ================= */
            $todaySubjectsRes = $conn->query("
                SELECT s.id AS subject_id, s.name
                FROM class_subject_schedule css
                JOIN class_subjects cs ON cs.id = css.class_subject_id
                JOIN subjects s ON s.id = cs.subject_id
                WHERE cs.class_id = $class_id
                  AND cs.teacher_id = $teacher_id
                  AND css.day_of_week = '$todayDay'
            ");

            $todaySubjects = [];
            $todayAttCount = 0;

            while ($ts = $todaySubjectsRes->fetch_assoc()) {
                $subject_id = $ts['subject_id'];

                // Check if attendance exists for this subject today
                $attTaken = $conn->query("
                    SELECT COUNT(*) c
                    FROM attendance
                    WHERE class_id = $class_id
                      AND subject_id = $subject_id
                      AND date = '$today'
                ")->fetch_assoc()['c'];

                $ts['taken'] = $attTaken > 0 ? 1 : 0;   // 1 = Taken, 0 = Missing
                $todayAttCount += $ts['taken'];
                $todaySubjects[] = $ts;
            }

            /* Next scheduled class today */
            $nextScheduleRes = $conn->query("
                SELECT s.name AS subject_name, css.start_time, css.end_time, css.day_of_week
                FROM class_subject_schedule css
                JOIN class_subjects cs ON cs.id = css.class_subject_id
                JOIN subjects s ON s.id = cs.subject_id
                WHERE cs.teacher_id = $teacher_id
                  AND cs.class_id = $class_id
                  AND css.day_of_week = '$todayDay'
                  AND css.start_time > CURTIME()
                ORDER BY css.start_time ASC
                LIMIT 1
            ");
            $nextSchedule = $nextScheduleRes->fetch_assoc();

            /* Risk Level */
            if ($percent >= 80) {
                $risk = [t('good'),'status-good', 'fa-check-circle'];
                $progressColor = 'background: linear-gradient(to right, #06d6a0, #0cb48a);';
            } elseif ($percent >= 60) {
                $risk = [t('average'),'status-warning', 'fa-exclamation-triangle'];
                $progressColor = 'background: linear-gradient(to right, #ffd166, #f8961e);';
            } else {
                $risk = [t('critical'),'status-critical', 'fa-exclamation-circle'];
                $progressColor = 'background: linear-gradient(to right, #ef476f, #f72585);';
            }

            /* Top 3 Late/Absent Students */
            $problemStudents = $conn->query("
                SELECT st.name,
                       SUM(a.status IN ('late','absent')) AS issues
                FROM attendance a
                JOIN students st ON st.id = a.student_id
                WHERE a.class_id = $class_id
                  AND a.subject_id IN (
                      SELECT subject_id FROM class_subjects
                      WHERE teacher_id = $teacher_id AND class_id = $class_id
                  )
                GROUP BY st.id
                ORDER BY issues DESC
                LIMIT 3
            ");

            /* Attendance Trend */
            $trendRes = $conn->query("
                SELECT date, SUM(status='present')/COUNT(*)*100 AS percent
                FROM attendance
                WHERE class_id = $class_id
                  AND date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)
                GROUP BY date
            ");
            $trend = t('stable');
            $trendIcon = 'fa-minus';
            $trendClass = '';
            if ($trendRes->num_rows >= 2) {
                $rows = $trendRes->fetch_all(MYSQLI_ASSOC);
                if (end($rows)['percent'] > $rows[0]['percent']) {
                    $trend = t('improving');
                    $trendIcon = 'fa-arrow-up';
                    $trendClass = 'trend-up';
                } else {
                    $trend = t('declining');
                    $trendIcon = 'fa-arrow-down';
                    $trendClass = 'trend-down';
                }
            }

            /* Class Comparison */
            $classCompare = $conn->query("
                SELECT c.name, ROUND(SUM(a.status='present')/COUNT(*)*100,1) percent
                FROM attendance a
                JOIN classes c ON c.id = a.class_id
                JOIN class_subjects cs ON cs.class_id = c.id
                WHERE cs.teacher_id = $teacher_id
                GROUP BY c.id
            ");

            /* Student Changes (30 days) */
            $studentChange = $conn->query("
                SELECT SUM(created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)) AS joined
                FROM class_students
                WHERE class_id = $class_id
            ")->fetch_assoc()['joined'];

            /* Alerts */
            $alerts = [];
            if(count($todaySubjects) > 0 && $todayAttCount == 0) $alerts[] = t('attendance_missing');
            if ($percent < 60) $alerts[] = t('critical_attendance');
            ?>
            
            <!-- Class Card -->
            <div class="class-card">
                <!-- Card Header -->
                <div class="card-header-custom d-flex justify-content-between align-items-center">
                    <div>
                        <h3 class="class-name"><?= htmlspecialchars($class['class_name']) ?></h3>
                        <div class="mt-2">
                            <?php foreach ($subjects as $subject): ?>
                                <span class="subject-pill"><?= htmlspecialchars($subject) ?></span>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <span class="class-badge">
                        <i class="fas <?= $risk[2] ?> me-1"></i> <?= $risk[0] ?>
                    </span>
                </div>

                <!-- Card Body -->
                <div class="card-body">
                    <!-- Stats Grid -->
                    <div class="stats-grid">
                        <div class="stat-card">
                            <h6><i class="fas fa-users me-1"></i> <?= t('students') ?></h6>
                            <div class="value"><?= $students ?></div>
                        </div>
                        <div class="stat-card">
                            <h6><i class="fas fa-clipboard-check me-1"></i> <?= t('attendance_percent') ?></h6>
                            <div class="value"><?= $percent ?>%</div>
                            <div class="attendance-progress">
                                <div class="attendance-progress-bar" style="width: <?= $percent ?>%; <?= $progressColor ?>"></div>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h6><i class="fas fa-chart-line me-1"></i> <?= t('trend') ?></h6>
                            <div class="value trend-indicator <?= $trendClass ?>">
                                <i class="fas <?= $trendIcon ?>"></i> <?= $trend ?>
                            </div>
                        </div>
                        <div class="stat-card">
                            <h6><i class="fas fa-user-plus me-1"></i> <?= t('new_30d') ?></h6>
                            <div class="value">+<?= $studentChange ?></div>
                        </div>
                    </div>

                    <!-- Today's Subjects -->
                    <?php if (!empty($todaySubjects)): ?>
                    <div class="today-subjects">
                        <h6><i class="fas fa-calendar-day me-2"></i> <?= t('todays_subjects') ?></h6>
                        <?php foreach($todaySubjects as $ts): ?>
                            <div class="subject-status">
                                <span><?= htmlspecialchars($ts['name']) ?></span>
                                <span class="<?= $ts['taken'] ? 'taken' : 'missing' ?>">
                                    <i class="fas <?= $ts['taken'] ? 'fa-check-circle' : 'fa-times-circle' ?> me-1"></i>
                                    <?= $ts['taken'] ? t('attendance_taken') : t('missing_attendance') ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <!-- Detailed Stats -->
                    <div class="row mt-4">
                        <div class="col-md-6">
                            <h6><i class="fas fa-chart-pie me-2"></i> <?= t('attendance_breakdown') ?></h6>
                            <div class="row">
                                <div class="col-6 mb-2">
                                    <small class="text-muted"><?= t('present') ?></small>
                                    <div class="fw-bold text-success"><?= $att['present'] ?></div>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted"><?= t('absent') ?></small>
                                    <div class="fw-bold text-danger"><?= $att['absent'] ?></div>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted"><?= t('late') ?></small>
                                    <div class="fw-bold text-warning"><?= $att['late'] ?></div>
                                </div>
                                <div class="col-6 mb-2">
                                    <small class="text-muted"><?= t('excused') ?></small>
                                    <div class="fw-bold text-info"><?= $att['excused'] ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-exclamation-triangle me-2"></i> <?= t('frequent_issues') ?></h6>
                            <ul class="list-unstyled">
                                <?php while($ps = $problemStudents->fetch_assoc()): ?>
                                    <li class="mb-1">
                                        <i class="fas fa-user-circle me-2 text-muted"></i>
                                        <?= $ps['name'] ?> <span class="badge bg-warning text-dark"><?= $ps['issues'] ?> <?= t('issues') ?></span>
                                    </li>
                                <?php endwhile; ?>
                                <?php if ($problemStudents->num_rows == 0): ?>
                                    <li class="text-muted"><?= t('no_frequent_issues') ?></li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Next Class -->
                    <?php if ($nextSchedule): ?>
                    <div class="alert alert-info alert-custom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-clock fa-lg me-3"></i>
                            <div>
                                <strong><?= t('next_class_today') ?>:</strong> 
                                <?= $nextSchedule['subject_name'] ?> 
                                <?= t('at') ?> <?= date('g:i A', strtotime($nextSchedule['start_time'])) ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Alerts -->
                    <?php if (!empty($alerts)): ?>
                    <div class="alert alert-warning alert-custom">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-bell fa-lg me-3"></i>
                            <div>
                                <strong><?= t('attention_needed') ?>:</strong>
                                <ul class="mb-0 mt-2">
                                    <?php foreach($alerts as $alert): ?>
                                        <li><?= $alert ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-wrap gap-3 mt-4">
                        <!-- <a href="class_details.php?id=<?= $class_id ?>" class="btn btn-custom btn-view">
                            <i class="fas fa-eye me-2"></i> <?= t('view_class_details') ?>
                        </a> -->
                        <a href="take_attendance.php?class_id=<?= $class_id ?>" class="btn btn-custom btn-attendance">
                            <i class="fas fa-clipboard-list me-2"></i> <?= t('take_attendance') ?>
                        </a>
                        <a href="attendance_history.php?class_id=<?= $class_id ?>" class="btn btn-custom btn-history">
                            <i class="fas fa-history me-2"></i> <?= t('view_history') ?>
                        </a>
                    </div>

                    <!-- Notes Section -->
                    <div class="note-section">
                        <h6><i class="fas fa-edit me-2"></i> <?= t('class_notes') ?></h6>
                        <form method="post" action="save_note.php" class="mt-2">
                            <input type="hidden" name="class_id" value="<?= $class_id ?>">
                            <textarea name="note" class="form-control note-textarea" 
                                      placeholder="<?= t('notes_placeholder') ?>"
                                      rows="3"></textarea>
                            <div class="d-flex justify-content-end mt-3">
                                <button class="btn btn-save">
                                    <i class="fas fa-save me-2"></i> <?= t('save_note') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        <?php endif; ?>

        <!-- Footer -->
        <div class="text-center text-muted mt-5 mb-4">
            <p><?= t('copyright') ?></p>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add some interactive effects
        document.addEventListener('DOMContentLoaded', function() {
            // Animate progress bars on scroll
            const observerOptions = {
                threshold: 0.2
            };
            
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        const progressBar = entry.target.querySelector('.attendance-progress-bar');
                        if (progressBar) {
                            const width = progressBar.style.width;
                            progressBar.style.width = '0%';
                            setTimeout(() => {
                                progressBar.style.width = width;
                                progressBar.style.transition = 'width 1.5s ease-in-out';
                            }, 300);
                        }
                    }
                });
            }, observerOptions);
            
            // Observe all class cards
            document.querySelectorAll('.class-card').forEach(card => {
                observer.observe(card);
            });
            
            // Add hover effect to buttons
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(btn => {
                btn.addEventListener('mouseenter', function() {
                    this.style.transform = 'translateY(-3px)';
                });
                
                btn.addEventListener('mouseleave', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
</body>
</html>

 <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #4cc9f0;
            --warning-color: #f8961e;
            --danger-color: #f72585;
            --light-color: #f8f9fa;
            --dark-color: #212529;
            --border-radius: 12px;
            --card-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
        }

        * {
            font-family: 'Inter', sans-serif;
        }

        body {
            background-color: #f5f7fb;
            color: #333;
            padding-top: 20px;
        }

        .dashboard-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .dashboard-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .dashboard-title i {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .date-display {
            background: white;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            font-weight: 500;
            color: var(--primary-color);
        }

        .class-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            border: none;
            margin-bottom: 25px;
            overflow: hidden;
            transition: var(--transition);
        }

        .class-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.12);
        }

        .card-header-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            border-bottom: none;
        }

        .class-name {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }

        .class-badge {
            background: rgba(255,255,255,0.2);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin: 20px 0;
        }

        .stat-card {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 15px;
            border-left: 4px solid var(--primary-color);
        }

        .stat-card h6 {
            font-size: 13px;
            text-transform: uppercase;
            color: #6c757d;
            margin-bottom: 5px;
            font-weight: 600;
        }

        .stat-card .value {
            font-size: 24px;
            font-weight: 700;
            color: var(--dark-color);
        }

        .attendance-progress {
            height: 10px;
            border-radius: 5px;
            background-color: #e9ecef;
            overflow: hidden;
            margin-top: 8px;
        }

        .attendance-progress-bar {
            height: 100%;
            border-radius: 5px;
            background: linear-gradient(to right, var(--success-color), #2a9d8f);
        }

        .subject-pill {
            display: inline-block;
            background: #e9ecef;
            padding: 5px 12px;
            border-radius: 20px;
            margin-right: 8px;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: 500;
        }

        .status-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
        }

        .status-good {
            background-color: rgba(76, 201, 240, 0.15);
            color: #118ab2;
        }

        .status-warning {
            background-color: rgba(248, 150, 30, 0.15);
            color: #f3722c;
        }

        .status-critical {
            background-color: rgba(247, 37, 133, 0.15);
            color: #f72585;
        }

        .today-subjects {
            background: #f8f9ff;
            border-radius: 10px;
            padding: 15px;
            margin: 15px 0;
        }

        .subject-status {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }

        .subject-status:last-child {
            border-bottom: none;
        }

        .taken {
            color: #06d6a0;
            font-weight: 600;
        }

        .missing {
            color: #ef476f;
            font-weight: 600;
        }

        .btn-custom {
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: var(--transition);
            border: none;
        }

        .btn-view {
            background: var(--primary-color);
            color: white;
        }

        .btn-view:hover {
            background: var(--secondary-color);
            color: white;
            transform: translateY(-2px);
        }

        .btn-attendance {
            background: linear-gradient(135deg, #06d6a0, #0cb48a);
            color: white;
        }

        .btn-attendance:hover {
            background: linear-gradient(135deg, #05c593, #0aa37c);
            color: white;
            transform: translateY(-2px);
        }

        .btn-history {
            background: #6c757d;
            color: white;
        }

        .btn-history:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
        }

        .alert-custom {
            border-radius: 10px;
            border: none;
            padding: 15px;
            font-weight: 500;
            margin-top: 15px;
        }

        .trend-indicator {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-weight: 600;
        }

        .trend-up {
            color: #06d6a0;
        }

        .trend-down {
            color: #ef476f;
        }

        .note-section {
            margin-top: 20px;
        }

        .note-textarea {
            border-radius: 10px;
            border: 1px solid #dee2e6;
            padding: 15px;
            font-size: 14px;
            resize: vertical;
            transition: var(--transition);
        }

        .note-textarea:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .btn-save {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 10px 25px;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67, 97, 238, 0.3);
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 60px;
            margin-bottom: 20px;
            color: #dee2e6;
        }

        .empty-state h3 {
            font-weight: 600;
            margin-bottom: 10px;
        }

        @media (max-width: 768px) {
            .dashboard-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .class-name {
                font-size: 18px;
            }
        }
    </style>