<?php
declare(strict_types=1);

require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

// Authentication check
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$teacherId = $_SESSION['user_id'];

/* ===================== TRANSLATIONS ===================== */
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacherId");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Teacher Timetable | Academic Portal',
        'dashboard_title' => 'Timetable',
        'dashboard_subtitle' => 'Weekly schedule overview and teaching statistics',
        'total_classes' => 'Total Classes',
        'teaching_hours' => 'Teaching Hours',
        'subjects' => 'Subjects',
        'class_groups' => 'Class Groups',
        'weekly_schedule' => 'Weekly Schedule',
        'time_display' => 'All times are displayed in 24-hour format',
        'time_slot' => 'Time Slot',
        'no_classes_scheduled' => 'No Classes Scheduled',
        'empty_timetable' => 'Your timetable is currently empty.',
        'note' => 'Note',
        'note_text' => 'This timetable displays your weekly teaching schedule. All times are in 24-hour format. For any discrepancies, please contact the academic office.',
        'monday' => 'Monday',
        'tuesday' => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday' => 'Thursday',
        'friday' => 'Friday',
        'saturday' => 'Saturday',
        'sunday' => 'Sunday'
    ],
    2 => [ // Arabic
        'page_title' => 'جدول المعلم | البوابة الأكاديمية',
        'dashboard_title' => 'جدول المواعيد',
        'dashboard_subtitle' => 'نظرة عامة على الجدول الأسبوعي وإحصائيات التدريس',
        'total_classes' => 'إجمالي الحصص',
        'teaching_hours' => 'ساعات التدريس',
        'subjects' => 'المواد',
        'class_groups' => 'مجموعات الفصول',
        'weekly_schedule' => 'الجدول الأسبوعي',
        'time_display' => 'جميع الأوقات معروضة بتنسيق 24 ساعة',
        'time_slot' => 'الفترة الزمنية',
        'no_classes_scheduled' => 'لا توجد حصص مجدولة',
        'empty_timetable' => 'جدولك الزمني فارغ حاليًا.',
        'note' => 'ملاحظة',
        'note_text' => 'يعرض هذا الجدول جدول التدريس الأسبوعي الخاص بك. جميع الأوقات بتنسيق 24 ساعة. لأي تناقضات، يرجى التواصل مع المكتب الأكاديمي.',
        'monday' => 'الاثنين',
        'tuesday' => 'الثلاثاء',
        'wednesday' => 'الأربعاء',
        'thursday' => 'الخميس',
        'friday' => 'الجمعة',
        'saturday' => 'السبت',
        'sunday' => 'الأحد'
    ],
    3 => [ // Somali
        'page_title' => 'Jadwalka Macallinka | Portal-ka Tacliinta',
        'dashboard_title' => 'Jadwalka',
        'dashboard_subtitle' => 'Dulmarka jadwalka toddobaadka iyo xisaabta waxbarashada',
        'total_classes' => 'Wadarta Fasallada',
        'teaching_hours' => 'Saacadaha Waxbarashada',
        'subjects' => 'Xisado',
        'class_groups' => 'Fasalada',
        'weekly_schedule' => 'Jadwalka Usbuuca',
        'time_display' => 'Dhammaan wakhtiyadu waxaa lagu muujiyaa qaabka 24 saac',
        'time_slot' => 'Wakhtiga',
        'no_classes_scheduled' => 'Fasallo lama qorsheyeen',
        'empty_timetable' => 'Jadwalkaagu waa mid madhan hadda.',
        'note' => 'Ogeysiin',
        'note_text' => 'Jadwalkaan wuxuu ku muujinayaa jadwalkaaga waxbarashada usbuuca. Dhammaan wakhtiyadu waxay ku jiraan qaabka 24-saac. Wax kasta oo khalad ah, fadlan la xiriir xafiiska tacliinta.',
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

// Configuration
$daysOfWeek = [
    'Monday', 'Tuesday', 'Wednesday',
    'Thursday', 'Friday', 'Saturday', 'Sunday'
];

// Translated days for display
$translatedDays = [
    t('monday'), t('tuesday'), t('wednesday'),
    t('thursday'), t('friday'), t('saturday'), t('sunday')
];

$timeRange = [
    'start' => 7,  // 07:00
    'end' => 20    // 20:00
];

// Fetch timetable data
$query = "
    SELECT 
        css.day_of_week,
        css.start_time,
        css.end_time,
        c.name AS class_name,
        s.name AS subject_name
    FROM class_subject_schedule css
    JOIN class_subjects cs ON cs.id = css.class_subject_id
    JOIN classes c ON c.id = cs.class_id
    JOIN subjects s ON s.id = cs.subject_id
    WHERE cs.teacher_id = ?
    ORDER BY 
        FIELD(
            css.day_of_week,
            'Monday', 'Tuesday', 'Wednesday',
            'Thursday', 'Friday', 'Saturday', 'Sunday'
        ),
        css.start_time
";

$stmt = $conn->prepare($query);
if (!$stmt) {
    die("SQL prepare error: " . $conn->error);
}

$stmt->bind_param('i', $teacherId);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("SQL execution error: " . $conn->error);
}

// Process timetable data
$timetable = [];
$uniqueTimeSlots = [];
$statistics = [
    'total_classes' => 0,
    'total_minutes' => 0,
    'subjects' => [],
    'classes' => []
];

while ($row = $result->fetch_assoc()) {
    $day = $row['day_of_week'];
    $startTime = $row['start_time'];
    $endTime = $row['end_time'];
    
    // Organize by day
    $timetable[$day][] = $row;
    
    // Collect unique time slots
    $timeSlotKey = $startTime . '-' . $endTime;
    $uniqueTimeSlots[$timeSlotKey] = [
        'start' => $startTime,
        'end' => $endTime
    ];
    
    // Calculate statistics
    $statistics['total_classes']++;
    
    $startTimestamp = strtotime($startTime);
    $endTimestamp = strtotime($endTime);
    $statistics['total_minutes'] += ($endTimestamp - $startTimestamp) / 60;
    
    $statistics['subjects'][$row['subject_name']] = true;
    $statistics['classes'][$row['class_name']] = true;
}

$stmt->close();

// Sort time slots chronologically
usort($uniqueTimeSlots, function($a, $b) {
    return strtotime($a['start']) <=> strtotime($b['start']);
});

// Calculate final statistics
$totalHours = round($statistics['total_minutes'] / 60, 1);
$totalSubjects = count($statistics['subjects']);
$totalClassGroups = count($statistics['classes']);

// Helper function to format time
function formatTime(string $time): string {
    return substr($time, 0, 5);
}
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
    <div class="header-container">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold">
                        <i class="fas fa-calendar-alt me-3"></i><?= t('dashboard_title') ?>
                    </h1>
                    <p class="lead mb-0"><?= t('dashboard_subtitle') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Statistics Cards -->
        <div class="row g-4 mb-5">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-chalkboard-teacher"></i>
                    </div>
                    <div class="stat-value"><?= $statistics['total_classes'] ?></div>
                    <div class="stat-label"><?= t('total_classes') ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="stat-value"><?= $totalHours ?>h</div>
                    <div class="stat-label"><?= t('teaching_hours') ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <div class="stat-value"><?= $totalSubjects ?></div>
                    <div class="stat-label"><?= t('subjects') ?></div>
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stat-value"><?= $totalClassGroups ?></div>
                    <div class="stat-label"><?= t('class_groups') ?></div>
                </div>
            </div>
        </div>

        <!-- Timetable -->
        <div class="timetable-container">
            <div class="timetable-header">
                <h3 class="mb-0">
                    <i class="fas fa-table me-2"></i><?= t('weekly_schedule') ?>
                </h3>
                <p class="mb-0 mt-2 opacity-75"><?= t('time_display') ?></p>
            </div>
            
            <div class="table-responsive">
                <table class="table timetable-table">
                    <thead>
                        <tr>
                            <th class="time-slot-header" style="width: 140px;">
                                <i class="fas fa-clock me-2"></i><?= t('time_slot') ?>
                            </th>
                            <?php foreach ($translatedDays as $day): ?>
                                <th class="day-header"><?= htmlspecialchars($day) ?></th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($uniqueTimeSlots)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                        <h4><?= t('no_classes_scheduled') ?></h4>
                                        <p class="text-muted"><?= t('empty_timetable') ?></p>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($uniqueTimeSlots as $slot): ?>
                                <tr>
                                    <th class="time-slot-header">
                                        <?= formatTime($slot['start']) ?> – <?= formatTime($slot['end']) ?>
                                    </th>
                                    
                                    <?php foreach ($daysOfWeek as $dayIndex => $day): ?>
                                        <td>
                                            <?php
                                            $hasClass = false;
                                            if (!empty($timetable[$day])) {
                                                foreach ($timetable[$day] as $class) {
                                                    if ($class['start_time'] === $slot['start'] && 
                                                        $class['end_time'] === $slot['end']) {
                                                        $hasClass = true;
                                                        ?>
                                                        <div class="class-event">
                                                            <div class="subject">
                                                                <?= htmlspecialchars($class['subject_name']) ?>
                                                            </div>
                                                            <div class="class-name">
                                                                <?= htmlspecialchars($class['class_name']) ?>
                                                            </div>
                                                            <small class="mt-2">
                                                                <?= formatTime($class['start_time']) ?> - <?= formatTime($class['end_time']) ?>
                                                            </small>
                                                        </div>
                                                        <?php
                                                        break;
                                                    }
                                                }
                                            }
                                            
                                            if (!$hasClass) {
                                                echo '<div class="empty-slot">—</div>';
                                            }
                                            ?>
                                        </td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Footer Notes -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle fa-2x me-3"></i>
                        <div>
                            <strong><?= t('note') ?>:</strong> <?= t('note_text') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add current time highlighting
        document.addEventListener('DOMContentLoaded', function() {
            const now = new Date();
            const currentDay = now.toLocaleDateString('en-US', { weekday: 'long' });
            const currentHour = now.getHours();
            const currentMinute = now.getMinutes();
            const currentTime = currentHour + ':' + (currentMinute < 10 ? '0' : '') + currentMinute;
            
            // Find and highlight current time slot
            const timeSlots = document.querySelectorAll('.time-slot-header');
            timeSlots.forEach(slot => {
                const slotText = slot.textContent.trim();
                const [start, end] = slotText.split(' – ');
                
                if (start <= currentTime && currentTime <= end) {
                    const row = slot.parentElement;
                    const cells = row.querySelectorAll('td');
                    cells.forEach(cell => {
                        cell.classList.add('current-time');
                    });
                }
            });
            
            // Add animation to class events
            const classEvents = document.querySelectorAll('.class-event');
            classEvents.forEach(event => {
                event.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                });
                
                event.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
  <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --accent-color: #e74c3c;
            --light-bg: #f8f9fa;
            --card-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --hover-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }
        
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            padding-bottom: 2rem;
        }
        
        .header-container {
            background: linear-gradient(to right, var(--primary-color), #34495e);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
            border-radius: 0 0 20px 20px;
            box-shadow: var(--card-shadow);
        }
        
        .stat-card {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            border: none;
            height: 100%;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }
        
        .stat-card .stat-icon {
            font-size: 2rem;
            color: var(--secondary-color);
            margin-bottom: 1rem;
        }
        
        .stat-card .stat-value {
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 0.5rem;
        }
        
        .stat-card .stat-label {
            color: #6c757d;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .timetable-container {
            background: white;
            border-radius: 15px;
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-top: 2rem;
        }
        
        .timetable-header {
            background: linear-gradient(to right, var(--primary-color), #3a506b);
            color: white;
            padding: 1.5rem;
        }
        
        .timetable-table {
            margin: 0;
        }
        
        .timetable-table thead th {
            background-color: #2c3e50;
            color: white;
            font-weight: 600;
            text-align: center;
            padding: 1rem;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        
        .timetable-table tbody td {
            height: 100px;
            vertical-align: top;
            padding: 0.5rem;
            border: 1px solid #e9ecef;
            transition: background-color 0.2s;
        }
        
        .timetable-table tbody td:hover {
            background-color: #f8f9fa;
        }
        
        .time-slot-header {
            background-color: #f8f9fa;
            font-weight: 600;
            color: var(--primary-color);
            padding: 1rem;
            border: 1px solid #dee2e6;
        }
        
        .class-event {
            background: linear-gradient(135deg, var(--secondary-color), #2980b9);
            color: white;
            padding: 0.75rem 0.5rem;
            border-radius: 8px;
            margin: 2px;
            height: 90px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            border-left: 4px solid var(--accent-color);
        }
        
        .class-event:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
        }
        
        .class-event .subject {
            font-weight: 600;
            font-size: 0.9rem;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }
        
        .class-event .class-name {
            font-size: 0.8rem;
            opacity: 0.9;
            line-height: 1.2;
        }
        
        .day-header {
            color: var(--primary-color);
            font-weight: 600;
            position: relative;
        }
        
        .day-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 25%;
            width: 50%;
            height: 3px;
            background: var(--accent-color);
            border-radius: 2px;
        }
        
        .empty-slot {
            color: #adb5bd;
            font-style: italic;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        
        .current-time {
            background-color: #fff3cd !important;
            border: 2px solid #ffc107 !important;
        }
        
        @media (max-width: 768px) {
            .timetable-container {
                overflow-x: auto;
            }
            
            .timetable-table {
                min-width: 800px;
            }
            
            .stat-card {
                margin-bottom: 1rem;
            }
        }
    </style>