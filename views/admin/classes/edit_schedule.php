<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

$logged_in_id = $_SESSION['user_id'] ?? null;
if(!$logged_in_id) die('Access denied');

/* ---------- ADMIN CHECK ---------- */
$me_stmt = $conn->prepare("
    SELECT r.name AS role_name
    FROM users u
    JOIN roles r ON r.id = u.role_id
    WHERE u.id=?
");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
if(($me['role_name'] ?? '') !== 'admin') die('Admins only');

/* ---------- THEME ---------- */
$theme_mode = 1; // default light
$themeRes = $conn->query("SELECT mode FROM theme ORDER BY id DESC LIMIT 1");
if($themeRes && $row = $themeRes->fetch_assoc()){
    $theme_mode = (int)$row['mode'];
}

// Set theme data attribute for Bootstrap color modes[citation:3]
$theme_attr = $theme_mode === 0 ? 'data-bs-theme="dark"' : 'data-bs-theme="light"';

/* ---------- INPUT ---------- */
$cs_id = intval($_GET['cs_id'] ?? 0);
if($cs_id <= 0) die('Invalid request');

/* ---------- LOAD CLASS + SUBJECT ---------- */
$stmt = $conn->prepare("
    SELECT 
        cs.id,
        cs.class_id,
        cs.teacher_id,
        s.name AS subject_name,
        c.name AS class_name
    FROM class_subjects cs
    JOIN subjects s ON s.id = cs.subject_id
    JOIN classes c ON c.id = cs.class_id
    WHERE cs.id=?
");
$stmt->bind_param('i', $cs_id);
$stmt->execute();
$info = $stmt->get_result()->fetch_assoc();
if(!$info) die('Not found');

$class_id = $info['class_id'];
$current_teacher = $info['teacher_id'];

/* ---------- LOAD CURRENT SCHEDULES ---------- */
$schedules = [];
$stmt = $conn->prepare("
    SELECT id, day_of_week, start_time, end_time
    FROM class_subject_schedule
    WHERE class_subject_id=?
    ORDER BY FIELD(day_of_week,'Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'), start_time
");
$stmt->bind_param('i', $cs_id);
$stmt->execute();
$res = $stmt->get_result();
while($r = $res->fetch_assoc()) $schedules[] = $r;

/* ---------- LOAD ACTIVE TEACHERS ---------- */
$teachers = [];
$res = $conn->query("SELECT id, CONCAT(first_name,' ',last_name) AS name FROM teachers WHERE status='active' ORDER BY first_name");
while($r = $res->fetch_assoc()) $teachers[$r['id']] = $r['name'];

$csrf = csrf_token();
$popup_error = null;

/* ================= SAVE ================= */
if($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['save'])){
    if(!validate_csrf_token($_POST['csrf_token'])) die('Invalid CSRF');

    $teacher_id = !empty($_POST['teacher_id']) ? intval($_POST['teacher_id']) : null;
    $days   = $_POST['day'] ?? [];
    $starts = $_POST['start'] ?? [];
    $ends   = $_POST['end'] ?? [];

    $MIN_DURATION = 30 * 60; // 30 minutes

    /* ---- LOAD OTHER SUBJECTS SCHEDULES (SAME CLASS) ---- */
    $existing = [];
    $stmt = $conn->prepare("
        SELECT sched.day_of_week, sched.start_time, sched.end_time
        FROM class_subject_schedule sched
        JOIN class_subjects cs ON cs.id = sched.class_subject_id
        WHERE cs.class_id=? AND cs.id!=?
    ");
$stmt->bind_param('ii', $class_id, $cs_id);
$stmt->execute();
$res = $stmt->get_result();
while($r = $res->fetch_assoc()) $existing[] = $r;

/* ---- LOAD TEACHER SCHEDULES (OTHER CLASSES) ---- */
$teacher_schedules = [];
if($teacher_id){
    $stmt = $conn->prepare("
        SELECT cs.class_id, sched.day_of_week, sched.start_time, sched.end_time
        FROM class_subject_schedule sched
        JOIN class_subjects cs ON cs.id = sched.class_subject_id
        WHERE cs.teacher_id = ? AND cs.id != ?
    ");
    $stmt->bind_param('ii', $teacher_id, $cs_id);
    $stmt->execute();
    $res = $stmt->get_result();
    while($r = $res->fetch_assoc()) $teacher_schedules[] = $r;
}

/* ---- FETCH CLASS NAMES WITH SECTION ---- */
$class_names = [];
$res = $conn->query("
    SELECT c.id, c.name AS class_name, s.name AS section_name
    FROM classes c
    LEFT JOIN sections s ON s.id = c.section_id
");
while($row = $res->fetch_assoc()){
    $class_names[$row['id']] = $row['class_name'] . ($row['section_name'] ? " ({$row['section_name']})" : '');
}

/* ---- VALIDATION ---- */
for($i=0; $i<count($days); $i++){
    if(!$days[$i] || !$starts[$i] || !$ends[$i]) continue;

    $newStart = strtotime($starts[$i]);
    $newEnd   = strtotime($ends[$i]);

    if($newEnd <= $newStart){
        $popup_error = "End time must be after start time ({$days[$i]})";
        break;
    }

    if(($newEnd - $newStart) < $MIN_DURATION){
        $popup_error = "Each subject period must be at least 30 minutes ({$days[$i]})";
        break;
    }

    // Conflict: same class
    foreach($existing as $ex){
        if($ex['day_of_week'] !== $days[$i]) continue;

        $exStart = strtotime($ex['start_time']);
        $exEnd   = strtotime($ex['end_time']);

        if($newStart < $exEnd && $newEnd > $exStart){
            $popup_error = "Schedule conflict in this class on {$days[$i]} (".date('H:i',$newStart)." - ".date('H:i',$newEnd).")";
            break 2;
        }
    }

    // Conflict: teacher other classes
    foreach($teacher_schedules as $sched){
        if($sched['day_of_week'] !== $days[$i]) continue;

        $schedStart = strtotime($sched['start_time']);
        $schedEnd   = strtotime($sched['end_time']);

        if($newStart < $schedEnd && $newEnd > $schedStart){
            $conflict_class = $class_names[$sched['class_id']] ?? $sched['class_id'];
            $popup_error = "Conflict: Teacher already assigned to another class (Class: $conflict_class) on {$days[$i]} (".date('H:i',$newStart)."-".date('H:i',$newEnd).")";
            break 2;
        }
    }
}

/* ---- SAVE IF NO ERROR ---- */
if(!$popup_error){
    $stmt = $conn->prepare("UPDATE class_subjects SET teacher_id=? WHERE id=?");
    $stmt->bind_param('ii', $teacher_id, $cs_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM class_subject_schedule WHERE class_subject_id=?");
    $stmt->bind_param('i', $cs_id);
    $stmt->execute();

    $stmt = $conn->prepare("
        INSERT INTO class_subject_schedule 
        (class_subject_id, day_of_week, start_time, end_time)
        VALUES (?,?,?,?)
    ");
    for($i=0; $i<count($days); $i++){
        if($days[$i] && $starts[$i] && $ends[$i]){
            $stmt->bind_param('isss', $cs_id, $days[$i], $starts[$i], $ends[$i]);
            $stmt->execute();
        }
    }

    header("Location: class_subject_assign.php?class_id=$class_id&updated=1");
    exit;
}
}
?>
<!DOCTYPE html>
<html lang="en" <?= $theme_attr ?>>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Schedule & Teacher - Education Management</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS for additional styling -->
    <style>
        :root {
            --primary-color: #4a6bff;
            --primary-hover: #3a5bef;
            --success-color: #28a745;
            --border-radius: 8px;
            --transition-speed: 0.2s;
        }
        
        body {
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            background-color: var(--bs-tertiary-bg);
            min-height: 100vh;
        }
        
        .card {
            border: none;
            border-radius: var(--border-radius);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform var(--transition-speed), box-shadow var(--transition-speed);
            overflow: hidden;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
        }
        
        .form-control, .form-select {
            border-radius: var(--border-radius);
            border: 1px solid var(--bs-border-color);
            transition: all var(--transition-speed);
            padding: 0.625rem 1rem;
        }
        
        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(74, 107, 255, 0.15);
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            border-radius: var(--border-radius);
            padding: 0.625rem 1.5rem;
            font-weight: 500;
            transition: all var(--transition-speed);
        }
        
        .btn-primary:hover {
            background-color: var(--primary-hover);
            border-color: var(--primary-hover);
            transform: translateY(-1px);
            box-shadow: 0 4px 8px rgba(74, 107, 255, 0.25);
        }
        
        .btn-outline-secondary {
            border-radius: var(--border-radius);
            padding: 0.625rem 1.5rem;
            font-weight: 500;
        }
        
        .schedule-row {
            background-color: var(--bs-secondary-bg);
            border-radius: var(--border-radius);
            padding: 1rem;
            margin-bottom: 0.75rem;
            border-left: 3px solid var(--primary-color);
            transition: all var(--transition-speed);
        }
        
        .schedule-row:hover {
            background-color: var(--bs-tertiary-bg);
            transform: translateX(2px);
        }
        
        .schedule-row .btn-outline-danger {
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
        }
        
        .header-icon {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, var(--primary-color), #6a8bff);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.25rem;
            margin-right: 1rem;
        }
        
        .stat-card {
            border-radius: var(--border-radius);
            padding: 1.25rem;
            background-color: var(--bs-secondary-bg);
        }
        
        .stat-card .stat-value {
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--primary-color);
        }
        
        .stat-card .stat-label {
            font-size: 0.875rem;
            color: var(--bs-secondary-color);
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .schedule-row .col-md-4 {
                margin-bottom: 0.75rem;
            }
            
            .schedule-row .col-md-1 {
                text-align: center;
                margin-top: 0.75rem;
            }
            
            .header-icon {
                width: 40px;
                height: 40px;
                font-size: 1rem;
                margin-right: 0.75rem;
            }
        }
        
        @media (max-width: 576px) {
            .card-body {
                padding: 1rem;
            }
            
            .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
            
            .d-flex.gap-3 {
                flex-direction: column;
            }
        }
        
        /* Modal customization */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
        }
        
        .modal-header {
            border-bottom: 1px solid var(--bs-border-color);
            background-color: var(--bs-secondary-bg);
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }
        
        /* Toast notification */
        .toast {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <!-- Toast container for error messages -->
    <div class="toast-container position-fixed top-0 end-0 p-3" style="z-index: 1100">
        <div id="errorToast" class="toast" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="toast-header">
                <i class="fas fa-exclamation-triangle text-danger me-2"></i>
                <strong class="me-auto">Schedule Conflict</strong>
                <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body" id="toastMessage"></div>
        </div>
    </div>

    <div class="container py-4">
        <!-- Header -->
        <div class="d-flex align-items-center mb-4">
            <div class="header-icon">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <div>
                <h1 class="h3 mb-1">Edit Schedule & Teacher</h1>
                <p class="text-body-secondary mb-0">
                    <i class="fas fa-graduation-cap me-1"></i>
                    <?= htmlspecialchars($info['class_name']) ?> – <?= htmlspecialchars($info['subject_name']) ?>
                </p>
            </div>
        </div>

        <!-- Stats Row -->
        <div class="row mb-4">
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?= count($schedules) ?></div>
                    <div class="stat-label">Scheduled Days</div>
                </div>
            </div>
            <div class="col-md-3 col-6 mb-3">
                <div class="stat-card">
                    <div class="stat-value"><?= count($teachers) ?></div>
                    <div class="stat-label">Active Teachers</div>
                </div>
            </div>
        </div>

        <!-- Main Form Card -->
        <div class="card mb-4">
            <div class="card-body">
                <form method="post" id="scheduleForm">
                    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

                    <!-- Teacher Assignment Section -->
                    <div class="mb-4">
                        <h5 class="card-title mb-3">
                            <i class="fas fa-chalkboard-teacher me-2"></i>
                            Assign Teacher
                        </h5>
                        <div class="row">
                            <div class="col-lg-6">
                                <label class="form-label fw-medium">Select Teacher</label>
                                <select name="teacher_id" class="form-select" required>
                                    <option value="">-- Select a Teacher --</option>
                                    <?php foreach($teachers as $tid => $tname): ?>
                                        <option value="<?= $tid ?>" <?= $current_teacher==$tid?'selected':'' ?>>
                                            <?= htmlspecialchars($tname) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <div class="form-text">Select a teacher to assign to this subject</div>
                            </div>
                        </div>
                    </div>

                    <!-- Schedule Management Section -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5 class="card-title mb-0">
                                <i class="fas fa-clock me-2"></i>
                                Class Schedule
                            </h5>
                            <button type="button" onclick="addRow()" class="btn btn-outline-primary btn-sm">
                                <i class="fas fa-plus me-1"></i> Add Time Slot
                            </button>
                        </div>
                        
                        <div id="rows">
                            <?php if(empty($schedules)): ?>
                                <!-- Default empty row -->
                                <div class="schedule-row">
                                    <div class="row g-3 align-items-center">
                                        <div class="col-md-4">
                                            <label class="form-label">Day</label>
                                            <select name="day[]" class="form-select">
                                                <?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                                    <option><?= $d ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-4">
                                            <label class="form-label">Start Time</label>
                                            <input type="time" name="start[]" class="form-control">
                                        </div>
                                        <div class="col-md-3">
                                            <label class="form-label">End Time</label>
                                            <input type="time" name="end[]" class="form-control">
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" onclick="this.closest('.schedule-row').remove()" class="btn btn-outline-danger btn-sm mt-4">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <!-- Existing schedule rows -->
                                <?php foreach($schedules as $s): ?>
                                    <div class="schedule-row">
                                        <div class="row g-3 align-items-center">
                                            <div class="col-md-4">
                                                <label class="form-label">Day</label>
                                                <select name="day[]" class="form-select">
                                                    <?php foreach(['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'] as $d): ?>
                                                        <option <?= $s['day_of_week']===$d?'selected':'' ?>><?= $d ?></option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label">Start Time</label>
                                                <input type="time" name="start[]" value="<?= substr($s['start_time'],0,5) ?>" class="form-control">
                                            </div>
                                            <div class="col-md-3">
                                                <label class="form-label">End Time</label>
                                                <input type="time" name="end[]" value="<?= substr($s['end_time'],0,5) ?>" class="form-control">
                                            </div>
                                            <div class="col-md-1">
                                                <button type="button" onclick="this.closest('.schedule-row').remove()" class="btn btn-outline-danger btn-sm mt-4">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-text mt-2">
                            <i class="fas fa-info-circle me-1"></i>
                            Each class period must be at least 30 minutes
                        </div>
                    </div>

                    <!-- Form Actions -->
                    <div class="d-flex flex-column flex-md-row gap-3 pt-3 border-top">
                        <a href="class_subject_assign.php?class_id=<?= $class_id ?>" class="btn btn-outline-secondary order-2 order-md-1">
                            <i class="fas fa-times me-1"></i> Cancel
                        </a>
                        <button type="submit" name="save" class="btn btn-primary order-1 order-md-2">
                            <i class="fas fa-save me-1"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Quick Tips Card -->
        <div class="card">
            <div class="card-body">
                <h6 class="card-title mb-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    Quick Tips
                </h6>
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Check for schedule conflicts before saving
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Ensure minimum 30-minute duration for each slot
                    </li>
                    <li>
                        <i class="fas fa-check-circle text-success me-2"></i>
                        Teacher can only teach one class at a time
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Modern toast notification for errors instead of alert
        <?php if(!empty($popup_error)): ?>
        document.addEventListener('DOMContentLoaded', function() {
            const toastMessage = document.getElementById('toastMessage');
            const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
            
            toastMessage.textContent = "<?= addslashes($popup_error) ?>";
            errorToast.show();
        });
        <?php endif; ?>

        // Function to add new schedule row
        function addRow() {
            const wrap = document.getElementById('rows');
            const div = document.createElement('div');
            div.className = 'schedule-row';
            div.innerHTML = `
                <div class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label">Day</label>
                        <select name="day[]" class="form-select">
                            <option>Monday</option>
                            <option>Tuesday</option>
                            <option>Wednesday</option>
                            <option>Thursday</option>
                            <option>Friday</option>
                            <option>Saturday</option>
                            <option>Sunday</option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Start Time</label>
                        <input type="time" name="start[]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End Time</label>
                        <input type="time" name="end[]" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="button" onclick="this.closest('.schedule-row').remove()" class="btn btn-outline-danger btn-sm mt-4">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            `;
            wrap.appendChild(div);
            
            // Add subtle animation
            div.style.opacity = '0';
            div.style.transform = 'translateY(10px)';
            
            requestAnimationFrame(() => {
                div.style.transition = 'opacity 0.3s, transform 0.3s';
                div.style.opacity = '1';
                div.style.transform = 'translateY(0)';
            });
        }

        // Form validation
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            const timeInputs = this.querySelectorAll('input[type="time"]');
            let hasError = false;
            
            timeInputs.forEach((input, index) => {
                if(index % 2 === 0) { // Start time
                    const endTime = timeInputs[index + 1];
                    if(input.value && endTime.value) {
                        const start = new Date('1970-01-01T' + input.value + ':00');
                        const end = new Date('1970-01-01T' + endTime.value + ':00');
                        
                        if(end <= start) {
                            showError('End time must be after start time');
                            input.focus();
                            hasError = true;
                        }
                        
                        const duration = (end - start) / (1000 * 60); // minutes
                        if(duration < 30) {
                            showError('Each subject period must be at least 30 minutes');
                            input.focus();
                            hasError = true;
                        }
                    }
                }
            });
            
            if(hasError) {
                e.preventDefault();
            }
        });
        
        function showError(message) {
            const toastMessage = document.getElementById('toastMessage');
            const errorToast = new bootstrap.Toast(document.getElementById('errorToast'));
            
            toastMessage.textContent = message;
            errorToast.show();
        }
    </script>
</body>
</html>