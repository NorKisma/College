<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* ===================== TRANSLATIONS ===================== */
$teacher_id = $_SESSION['user_id'];

// Get teacher's language
$lang_query = $conn->query("SELECT language_id FROM teachers WHERE id = $teacher_id");
$teacher_lang = $lang_query->fetch_assoc();
$currentLangId = $teacher_lang['language_id'] ?? 1;

$translations = [
    1 => [ // English
        'page_title' => 'Take Attendance',
        'header_title' => 'Take Attendance',
        'header_subtitle' => 'Record student attendance for today\'s classes. Select a subject and mark attendance status for each student.',
        'back_to_classes' => 'Back to Classes',
        'present' => 'Present',
        'absent' => 'Absent',
        'late' => 'Late',
        'excused' => 'Excused',
        'success' => 'Success!',
        'error' => 'Error!',
        'attendance_saved' => 'Attendance saved successfully.',
        'attendance_failed' => 'Failed to save attendance',
        'attendance_recorded' => 'Attendance has already been recorded for this subject today. You can update it below.',
        'select_subject' => 'Select Subject',
        'student_attendance' => 'Student Attendance',
        'students' => 'Students',
        'student' => 'Student',
        'number' => '#',
        'student_name' => 'Student Name',
        'status' => 'Status',
        'remarks' => 'Remarks',
        'remarks_placeholder' => 'Optional remarks...',
        'save_attendance' => 'Save Attendance',
        'saving' => 'Saving...',
        'no_classes_scheduled' => 'No Classes Scheduled',
        'no_classes_message' => 'There are no subjects scheduled for',
        'in_this_class' => 'in this class.',
        'invalid_class' => 'Invalid class ID'
    ],
    2 => [ // Arabic
        'page_title' => 'تسجيل الحضور',
        'header_title' => 'تسجيل الحضور',
        'header_subtitle' => 'سجل حضور الطلاب لفصول اليوم. اختر المادة وحدد حالة الحضور لكل طالب.',
        'back_to_classes' => 'العودة إلى الفصول',
        'present' => 'حاضر',
        'absent' => 'غائب',
        'late' => 'متأخر',
        'excused' => 'معذور',
        'success' => 'نجاح!',
        'error' => 'خطأ!',
        'attendance_saved' => 'تم حفظ الحضور بنجاح.',
        'attendance_failed' => 'فشل في حفظ الحضور',
        'attendance_recorded' => 'تم تسجيل الحضور لهذه المادة اليوم. يمكنك تحديثه أدناه.',
        'select_subject' => 'اختر المادة',
        'student_attendance' => 'حضور الطلاب',
        'students' => 'الطلاب',
        'student' => 'طالب',
        'number' => '#',
        'student_name' => 'اسم الطالب',
        'status' => 'الحالة',
        'remarks' => 'ملاحظات',
        'remarks_placeholder' => 'ملاحظات اختيارية...',
        'save_attendance' => 'حفظ الحضور',
        'saving' => 'جاري الحفظ...',
        'no_classes_scheduled' => 'لا توجد فصول مجدولة',
        'no_classes_message' => 'لا توجد مواد مجدولة لـ',
        'in_this_class' => 'في هذا الفصل.',
        'invalid_class' => 'معرف الفصل غير صالح'
    ],
    3 => [ // Somali
        'page_title' => 'Qor Xuduurka',
        'header_title' => 'Qor Xuduurka Fasalka',
        'header_subtitle' => 'Qor Xuduurka ardayda fasallada maanta. Sax arday kasta iyo xuduurkiisa.',
        'back_to_classes' => 'Dib u noqo',
        'present' => 'Jooga',
        'absent' => 'Maqan',
        'late' => 'Daah',
        'excused' => 'Daahfur',
        'success' => 'Guul!',
        'error' => 'Khalad!',
        'attendance_saved' => 'Xuduurkii si guul leh loo keydiyay.',
        'attendance_failed' => 'Khalad ayaa dhacay markii la keydinayay Xuduurka',
        'attendance_recorded' => 'Horay ayaa loo Saxay xuduurka maaddadan maanta!',
        'select_subject' => 'Dooro Maadda',
        'student_attendance' => 'Sax Ardayda',
        'students' => 'Ardayda',
        'student' => 'Arday',
        'number' => '#',
        'student_name' => 'Magaca Ardayga',
        'status' => 'Xaaladda',
        'remarks' => 'Qoraallo',
        'remarks_placeholder' => 'Qoraallo ikhtiyaari ah...',
        'save_attendance' => 'Keydi Xuduurka',
        'saving' => 'Keydinaya...',
        'no_classes_scheduled' => 'Kuma lihid maanta',
        'no_classes_message' => 'Kuma lihid wax maadooyiin ah',
        'in_this_class' => 'fasalkan.',
        'invalid_class' => 'Aqoonsiga fasalka ma ahan mid sax ah'
    ]
];

function t($key) {
    global $translations, $currentLangId;
    return $translations[$currentLangId][$key] ?? $key;
}

$todayDay = date('l'); // e.g., Monday, Tuesday ...
$today = date('Y-m-d');
$class_id = (int)($_GET['class_id'] ?? 0);
$subject_id = (int)($_GET['subject_id'] ?? 0);

if ($class_id <= 0) {
    die(t('invalid_class'));
}

// Handle attendance submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subject_id = (int)($_POST['subject_id'] ?? 0);
    $statuses = $_POST['status'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    if ($subject_id > 0 && !empty($statuses)) {
        $conn->begin_transaction();
        try {
            foreach ($statuses as $student_id => $status) {
                $student_id = (int)$student_id;
                $status = $conn->real_escape_string($status);
                $remark = $conn->real_escape_string($remarks[$student_id] ?? '');
                $recorded_by = $_SESSION['user_name'] ?? "Teacher ID $teacher_id";

                // Get section_id from class (optional)
                $classInfo = $conn->query("SELECT section_id FROM classes WHERE id=$class_id")->fetch_assoc();
                $section_id = $classInfo['section_id'] ?? null;

                // Check if attendance exists
                $exists = $conn->query("
                    SELECT id FROM attendance
                    WHERE student_id=$student_id
                      AND class_id=$class_id
                      AND subject_id=$subject_id
                      AND date='$today'
                ")->fetch_assoc();

                if ($exists) {
                    // UPDATE existing record
                    $stmt = $conn->prepare("
                        UPDATE attendance
                        SET status=?, remarks=?, recorded_by=?, recorded_at=NOW()
                        WHERE id=?
                    ");
                    $stmt->bind_param('sssi', $status, $remark, $recorded_by, $exists['id']);
                    $stmt->execute();
                } else {
                    // INSERT new record
                    $stmt = $conn->prepare("
                        INSERT INTO attendance
                        (student_id, class_id, section_id, date, status, remarks, recorded_by, subject_id)
                        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                    ");
                    $stmt->bind_param(
                        "iiissssi",
                        $student_id,
                        $class_id,
                        $section_id,
                        $today,
                        $status,
                        $remark,
                        $recorded_by,
                        $subject_id
                    );
                    $stmt->execute();
                }
            }

            $conn->commit();
            $message = t('attendance_saved');
        } catch (Exception $e) {
            $conn->rollback();
            $message = t('attendance_failed') . ": " . $e->getMessage();
        }
    }
}

// Fetch class name
$class_name = $conn->query("SELECT name FROM classes WHERE id=$class_id")->fetch_assoc()['name'] ?? 'Unknown';

// Fetch subjects scheduled today for this class & teacher
$subjectsRes = $conn->query("
    SELECT s.id AS subject_id, s.name
    FROM class_subject_schedule css
    JOIN class_subjects cs ON cs.id = css.class_subject_id
    JOIN subjects s ON s.id = cs.subject_id
    WHERE cs.class_id=$class_id
      AND cs.teacher_id=$teacher_id
      AND css.day_of_week='$todayDay'
    ORDER BY css.start_time
");

$subjects = [];
while ($s = $subjectsRes->fetch_assoc()) $subjects[] = $s;

// Default to first subject if none selected
if (!$subject_id && !empty($subjects)) $subject_id = $subjects[0]['subject_id'];

// Fetch class info first (including section_id)
$classInfo = $conn->query("SELECT name, section_id FROM classes WHERE id=$class_id")->fetch_assoc();
$class_name = $classInfo['name'] ?? 'Unknown';
$section_id = $classInfo['section_id'] ?? null;

// Fetch students in class
$studentsRes = $conn->query("
    SELECT st.id, st.name
    FROM class_students cs
    JOIN students st ON st.id = cs.student_id
    WHERE cs.class_id=$class_id
    ORDER BY st.name
");

$students = [];
while ($st = $studentsRes->fetch_assoc()) $students[] = $st;

// Fetch existing attendance for selected subject
$attendance = [];
$attendanceStats = ['present' => 0, 'absent' => 0, 'late' => 0, 'excused' => 0];
if ($subject_id > 0) {
    $attRes = $conn->query("
        SELECT student_id, status, remarks
        FROM attendance
        WHERE class_id=$class_id
          AND subject_id=$subject_id
          AND date='$today'
    ");
    while ($a = $attRes->fetch_assoc()) {
        $attendance[$a['student_id']] = $a;
        if (isset($attendanceStats[$a['status']])) {
            $attendanceStats[$a['status']]++;
        }
    }
}

// Get current subject name
$current_subject_name = '';
foreach ($subjects as $s) {
    if ($s['subject_id'] == $subject_id) {
        $current_subject_name = $s['name'];
        break;
    }
}

// Check if attendance already taken for this subject today
$attendance_taken = $conn->query("
    SELECT COUNT(*) as count 
    FROM attendance 
    WHERE class_id = $class_id 
    AND subject_id = $subject_id 
    AND date = '$today'
")->fetch_assoc()['count'] > 0;
?>
<!DOCTYPE html>
<html lang="<?= $currentLangId == 2 ? 'ar' : ($currentLangId == 3 ? 'so' : 'en') ?>" 
      dir="<?= $currentLangId == 2 ? 'rtl' : 'ltr' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= t('page_title') ?> | <?= htmlspecialchars($class_name) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

</head>
<body>
    <div class="attendance-container">
        <!-- Header Section -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="header-title">
                        <i class="fas fa-clipboard-check" style="color: var(--primary-color);"></i> 
                        <?= t('header_title') ?>
                    </h1>
                    <p class="header-subtitle">
                        <?= t('header_subtitle') ?>
                    </p>
                    
                    <div class="badge-container">
                        <span class="class-badge">
                            <i class="fas fa-chalkboard"></i> <?= htmlspecialchars($class_name) ?>
                        </span>
                        <?php if ($current_subject_name): ?>
                        <span class="subject-badge">
                            <i class="fas fa-book"></i> <?= htmlspecialchars($current_subject_name) ?>
                        </span>
                        <?php endif; ?>
                        <span class="date-badge">
                            <i class="fas fa-calendar"></i> <?= date('F j, Y') ?>
                        </span>
                    </div>
                </div>
                
                <a href="classes.php" class="back-button">
                    <i class="fas fa-arrow-left"></i> <?= t('back_to_classes') ?>
                </a>
            </div>
            
            <!-- Status Legend -->
            <div class="legend-container">
                <div class="legend-item">
                    <div class="legend-color legend-present"></div>
                    <span><?= t('present') ?></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-absent"></div>
                    <span><?= t('absent') ?></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-late"></div>
                    <span><?= t('late') ?></span>
                </div>
                <div class="legend-item">
                    <div class="legend-color legend-excused"></div>
                    <span><?= t('excused') ?></span>
                </div>
            </div>
        </div>

        <!-- Message Alert -->
        <?php if (!empty($message)): ?>
            <div class="alert <?= strpos($message, t('attendance_saved')) !== false ? 'alert-success' : 'alert-danger' ?> alert-custom">
                <div class="d-flex align-items-center">
                    <i class="fas <?= strpos($message, t('attendance_saved')) !== false ? 'fa-check-circle' : 'fa-exclamation-triangle' ?> fa-lg me-3"></i>
                    <div>
                        <strong><?= strpos($message, t('attendance_saved')) !== false ? t('success') : t('error') ?></strong> 
                        <?= htmlspecialchars($message) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Attendance Status -->
        <?php if ($attendance_taken && $subject_id > 0): ?>
            <div class="attendance-status">
                <i class="fas fa-info-circle text-warning"></i>
                <span><?= t('attendance_recorded') ?></span>
            </div>
        <?php endif; ?>

        <!-- Stats Overview -->
        <?php if ($subject_id > 0): ?>
        <div class="stats-grid">
            <div class="stat-item stat-present">
                <div class="stat-number"><?= $attendanceStats['present'] ?></div>
                <div class="stat-label"><?= t('present') ?></div>
            </div>
            <div class="stat-item stat-absent">
                <div class="stat-number"><?= $attendanceStats['absent'] ?></div>
                <div class="stat-label"><?= t('absent') ?></div>
            </div>
            <div class="stat-item stat-late">
                <div class="stat-number"><?= $attendanceStats['late'] ?></div>
                <div class="stat-label"><?= t('late') ?></div>
            </div>
            <div class="stat-item stat-excused">
                <div class="stat-number"><?= $attendanceStats['excused'] ?></div>
                <div class="stat-label"><?= t('excused') ?></div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Subject Selector -->
        <?php if (count($subjects) > 0): ?>
        <div class="subject-selector">
            <label class="selector-label">
                <i class="fas fa-book-open"></i> <?= t('select_subject') ?>
            </label>
            <form method="get" id="subjectForm">
                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                <select name="subject_id" class="subject-dropdown" onchange="document.getElementById('subjectForm').submit()">
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['subject_id'] ?>" <?= $s['subject_id'] == $subject_id ? 'selected' : '' ?>>
                            <?= htmlspecialchars($s['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>

        <!-- Attendance Form -->
        <?php if ($subject_id > 0): ?>
        <div class="attendance-card">
            <div class="attendance-header">
                <h3 class="attendance-title">
                    <i class="fas fa-user-graduate"></i> <?= t('student_attendance') ?>
                </h3>
                <span class="student-count">
                    <i class="fas fa-users"></i> <?= count($students) ?> <?= t('students') ?>
                </span>
            </div>
            
            <form method="post" id="attendanceForm">
                <input type="hidden" name="class_id" value="<?= $class_id ?>">
                <input type="hidden" name="subject_id" value="<?= $subject_id ?>">
                
                <div class="table-container">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;"><?= t('number') ?></th>
                                <th><?= t('student_name') ?></th>
                                <th style="width: 160px;"><?= t('status') ?></th>
                                <th><?= t('remarks') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($students as $i => $st): 
                                $current = $attendance[$st['id']] ?? ['status' => 'present', 'remarks' => ''];
                                $statusClass = 'status-' . $current['status'];
                            ?>
                            <tr>
                                <td data-label="<?= t('number') ?>" class="student-number"><?= $i+1 ?></td>
                                <td data-label="<?= t('student_name') ?>" class="student-name">
                                    <i class="fas fa-user-circle text-muted me-2"></i>
                                    <?= htmlspecialchars($st['name']) ?>
                                </td>
                                <td data-label="<?= t('status') ?>">
                                    <select name="status[<?= $st['id'] ?>]" class="form-select status-select <?= $statusClass ?>"
                                            onchange="updateStatusStyle(this)">
                                        <?php 
                                        $statusOptions = [
                                            'present' => [t('present'), 'fas fa-check-circle text-success'],
                                            'absent' => [t('absent'), 'fas fa-times-circle text-danger'],
                                            'late' => [t('late'), 'fas fa-clock text-warning'],
                                            'excused' => [t('excused'), 'fas fa-user-check text-info']
                                        ];
                                        
                                        foreach($statusOptions as $key => $val):
                                        ?>
                                            <option value="<?= $key ?>" <?= $current['status'] == $key ? 'selected' : '' ?>>
                                                <?= $val[0] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td data-label="<?= t('remarks') ?>">
                                    <input type="text" 
                                           name="remarks[<?= $st['id'] ?>]" 
                                           class="form-control remarks-input" 
                                           value="<?= htmlspecialchars($current['remarks']) ?>" 
                                           placeholder="<?= t('remarks_placeholder') ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="p-4 text-center border-top">
                    <button type="submit" class="save-button" id="saveButton">
                        <i class="fas fa-save"></i> <?= t('save_attendance') ?>
                    </button>
                </div>
            </form>
        </div>
        <?php elseif (count($subjects) == 0): ?>
            <div class="alert alert-warning alert-custom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-times fa-lg me-3"></i>
                    <div>
                        <strong><?= t('no_classes_scheduled') ?></strong>
                        <p class="mb-0"><?= t('no_classes_message') ?> <?= $todayDay ?> <?= t('in_this_class') ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update status select border based on selection
            function updateStatusStyle(select) {
                // Remove all status classes
                select.classList.remove('status-present', 'status-absent', 'status-late', 'status-excused');
                
                // Add appropriate class
                const statusClass = 'status-' + select.value;
                select.classList.add(statusClass);
                
                // Update stats in real-time
                updateStats();
            }
            
            // Initialize status styles
            document.querySelectorAll('.status-select').forEach(select => {
                updateStatusStyle(select);
            });
            
            // Real-time stats update
            function updateStats() {
                const stats = {
                    'present': 0,
                    'absent': 0,
                    'late': 0,
                    'excused': 0
                };
                
                document.querySelectorAll('.status-select').forEach(select => {
                    stats[select.value]++;
                });
                
                // Update stat numbers if stats grid exists
                document.querySelectorAll('.stat-number').forEach(stat => {
                    const parent = stat.closest('.stat-item');
                    if (parent.classList.contains('stat-present')) {
                        stat.textContent = stats.present;
                    } else if (parent.classList.contains('stat-absent')) {
                        stat.textContent = stats.absent;
                    } else if (parent.classList.contains('stat-late')) {
                        stat.textContent = stats.late;
                    } else if (parent.classList.contains('stat-excused')) {
                        stat.textContent = stats.excused;
                    }
                });
            }
            
            // Form submission handling
            const form = document.getElementById('attendanceForm');
            const saveButton = document.getElementById('saveButton');
            
            if (form) {
                form.addEventListener('submit', function() {
                    saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> <?= t('saving') ?>';
                    saveButton.disabled = true;
                });
            }
            
            // Quick status shortcuts
            document.addEventListener('keydown', function(e) {
                // Only trigger if not in input field
                if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT') return;
                
                const activeSelect = document.querySelector('.status-select:focus');
                if (activeSelect) {
                    switch(e.key.toLowerCase()) {
                        case 'p':
                            activeSelect.value = 'present';
                            updateStatusStyle(activeSelect);
                            e.preventDefault();
                            break;
                        case 'a':
                            activeSelect.value = 'absent';
                            updateStatusStyle(activeSelect);
                            e.preventDefault();
                            break;
                        case 'l':
                            activeSelect.value = 'late';
                            updateStatusStyle(activeSelect);
                            e.preventDefault();
                            break;
                        case 'e':
                            activeSelect.value = 'excused';
                            updateStatusStyle(activeSelect);
                            e.preventDefault();
                            break;
                    }
                }
            });
        });
    </script>
</body>
</html>

    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3a0ca3;
            --success-color: #06d6a0;
            --warning-color: #ffd166;
            --danger-color: #ef476f;
            --info-color: #4cc9f0;
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
            background: linear-gradient(135deg, #f5f7fb 0%, #f0f4ff 100%);
            min-height: 100vh;
            padding: 20px 0;
        }

        .attendance-container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            padding: 25px 30px;
            margin-bottom: 25px;
            position: relative;
            overflow: hidden;
        }

        .header-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 6px;
            height: 100%;
            background: linear-gradient(to bottom, var(--primary-color), var(--secondary-color));
        }

        .header-title {
            font-size: 28px;
            font-weight: 700;
            color: var(--dark-color);
            margin-bottom: 5px;
        }

        .header-subtitle {
            color: #6c757d;
            font-size: 16px;
            font-weight: 400;
        }

        .badge-container {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 15px;
        }

        .class-badge {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .subject-badge {
            background: linear-gradient(135deg, var(--info-color), #2a9d8f);
            color: white;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .date-badge {
            background: linear-gradient(135deg, #ff9a9e, #fad0c4);
            color: #333;
            padding: 8px 16px;
            border-radius: 20px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 25px;
        }

        .stat-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            text-align: center;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: var(--transition);
            border-top: 4px solid;
        }

        .stat-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }

        .stat-present { border-color: var(--success-color); }
        .stat-absent { border-color: var(--danger-color); }
        .stat-late { border-color: var(--warning-color); }
        .stat-excused { border-color: var(--info-color); }

        .stat-number {
            font-size: 32px;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .stat-present .stat-number { color: var(--success-color); }
        .stat-absent .stat-number { color: var(--danger-color); }
        .stat-late .stat-number { color: #f8961e; }
        .stat-excused .stat-number { color: var(--info-color); }

        .stat-label {
            font-size: 14px;
            color: #6c757d;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .back-button {
            background: #6c757d;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: var(--border-radius);
            font-weight: 600;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: var(--transition);
        }

        .back-button:hover {
            background: #5a6268;
            color: white;
            transform: translateY(-2px);
            text-decoration: none;
        }

        .subject-selector {
            background: white;
            border-radius: var(--border-radius);
            padding: 20px;
            margin-bottom: 25px;
            box-shadow: var(--card-shadow);
        }

        .selector-label {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .subject-dropdown {
            border: 2px solid #e9ecef;
            border-radius: 8px;
            padding: 12px 15px;
            font-size: 16px;
            font-weight: 500;
            transition: var(--transition);
            width: 100%;
            max-width: 400px;
        }

        .subject-dropdown:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .attendance-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
        }

        .attendance-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
        }

        .attendance-title {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-count {
            background: rgba(255, 255, 255, 0.2);
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .table-container {
            overflow-x: auto;
        }

        .attendance-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .attendance-table thead th {
            background: #f8f9fa;
            color: var(--dark-color);
            font-weight: 600;
            padding: 18px 15px;
            border-bottom: 2px solid #e9ecef;
            text-transform: uppercase;
            font-size: 14px;
            letter-spacing: 0.5px;
        }

        .attendance-table tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid #f1f1f1;
        }

        .attendance-table tbody tr:hover {
            background-color: #f8f9ff;
        }

        .attendance-table tbody td {
            padding: 16px 15px;
            vertical-align: middle;
        }

        .student-name {
            font-weight: 500;
            color: var(--dark-color);
        }

        .student-number {
            font-weight: 600;
            color: var(--primary-color);
            text-align: center;
        }

        .status-select {
            min-width: 140px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 12px;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
            position: relative;
        }

        .status-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .status-present { border-left: 4px solid var(--success-color); }
        .status-absent { border-left: 4px solid var(--danger-color); }
        .status-late { border-left: 4px solid var(--warning-color); }
        .status-excused { border-left: 4px solid var(--info-color); }

        .remarks-input {
            width: 100%;
            min-width: 200px;
            border-radius: 8px;
            border: 2px solid #e9ecef;
            padding: 10px 12px;
            transition: var(--transition);
        }

        .remarks-input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(67, 97, 238, 0.25);
        }

        .save-button {
            background: linear-gradient(135deg, var(--success-color), #0cb48a);
            color: white;
            border: none;
            padding: 14px 40px;
            border-radius: var(--border-radius);
            font-weight: 600;
            font-size: 16px;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(6, 214, 160, 0.3);
        }

        .save-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(6, 214, 160, 0.4);
        }

        .save-button:disabled {
            background: #6c757d;
            cursor: not-allowed;
            transform: none;
            box-shadow: none;
        }

        .alert-custom {
            border-radius: var(--border-radius);
            border: none;
            padding: 16px 20px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            animation: slideDown 0.3s ease;
            margin-bottom: 25px;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .legend-container {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
            font-weight: 500;
        }

        .legend-color {
            width: 12px;
            height: 12px;
            border-radius: 3px;
        }

        .legend-present { background: var(--success-color); }
        .legend-absent { background: var(--danger-color); }
        .legend-late { background: var(--warning-color); }
        .legend-excused { background: var(--info-color); }

        .attendance-status {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 15px;
            background: rgba(255, 209, 102, 0.1);
            border-radius: 8px;
            border-left: 4px solid var(--warning-color);
            margin-bottom: 20px;
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 22px;
            }
            
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .attendance-table thead {
                display: none;
            }
            
            .attendance-table tbody tr {
                display: block;
                margin-bottom: 20px;
                border: 1px solid #e9ecef;
                border-radius: var(--border-radius);
                padding: 15px;
            }
            
            .attendance-table tbody td {
                display: block;
                text-align: left;
                padding: 10px 0;
                border: none;
            }
            
            .attendance-table tbody td:before {
                content: attr(data-label);
                font-weight: 600;
                color: var(--primary-color);
                display: block;
                margin-bottom: 5px;
                font-size: 13px;
                text-transform: uppercase;
            }
            
            .status-select, .remarks-input {
                width: 100%;
            }
            
            .attendance-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>