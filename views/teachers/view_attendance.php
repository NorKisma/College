<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = $_SESSION['user_id'];
$class_id = (int)($_GET['class_id'] ?? 0);
$subject_id = (int)($_GET['subject_id'] ?? 0);
$date = $_GET['date'] ?? date('Y-m-d');

if ($class_id <= 0 || $subject_id <= 0) {
    die("Invalid class or subject.");
}

// Fetch class and subject names
$class_name = $conn->query("SELECT name FROM classes WHERE id=$class_id")->fetch_assoc()['name'] ?? 'Unknown';
$subject_name = $conn->query("SELECT name FROM subjects WHERE id=$subject_id")->fetch_assoc()['name'] ?? 'Unknown';

// Fetch students in this class
$studentsRes = $conn->query("
    SELECT st.id, st.name
    FROM class_students cs
    JOIN students st ON st.id = cs.student_id
    WHERE cs.class_id=$class_id
    ORDER BY st.name
");
$students = [];
while ($st = $studentsRes->fetch_assoc()) $students[] = $st;

// Fetch existing attendance for this date/subject/class
$recorded_by = $_SESSION['user_name'] ?? "Teacher ID $teacher_id";

// Fetch all existing attendance for this date/subject/class
$attendance = [];
$attRes = $conn->query("
    SELECT *
    FROM attendance
    WHERE class_id=$class_id
      AND subject_id=$subject_id
      AND date='$date'
");
while ($a = $attRes->fetch_assoc()) {
    // Store by student_id. If multiple records per student exist, pick first (or latest)
    if (!isset($attendance[$a['student_id']])) {
        $attendance[$a['student_id']] = $a;
    }
}

// Handle form submission
$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $statuses = $_POST['status'] ?? [];
    $remarks = $_POST['remarks'] ?? [];

    $conn->begin_transaction();
    try {
        $section_id = $conn->query("SELECT section_id FROM classes WHERE id=$class_id")->fetch_assoc()['section_id'] ?? null;

        foreach ($students as $st) {
            $student_id = $st['id'];
            $status = $conn->real_escape_string($statuses[$student_id] ?? 'present');
            $remark = $conn->real_escape_string($remarks[$student_id] ?? '');

            if (isset($attendance[$student_id])) {
                // Update existing record
                $stmt = $conn->prepare("
                    UPDATE attendance
                    SET status=?, remarks=?
                    WHERE id=?
                ");
                $stmt->bind_param("ssi", $status, $remark, $attendance[$student_id]['id']);
                $stmt->execute();
            } else {
                // Insert new record
                $stmt = $conn->prepare("
                    INSERT INTO attendance
                    (student_id, class_id, section_id, date, status, remarks, recorded_by, subject_id)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->bind_param("iiissssi", $student_id, $class_id, $section_id, $date, $status, $remark, $recorded_by, $subject_id);
                $stmt->execute();
            }
        }

        $conn->commit();
        $message = "Attendance saved successfully.";
    } catch (Exception $e) {
        $conn->rollback();
        $message = "Failed to save attendance: " . $e->getMessage();
    }

    // Refresh current attendance values after update
    $attendance = [];
    $attRes = $conn->query("
        SELECT *
        FROM attendance
        WHERE class_id=$class_id
          AND subject_id=$subject_id
          AND date='$date'
    ");
    while ($a = $attRes->fetch_assoc()) {
        $attendance[$a['student_id']] = $a;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Attendance | <?= htmlspecialchars($class_name) ?> / <?= htmlspecialchars($subject_name) ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
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
            --border-radius: 10px;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
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
            padding: 25px;
            margin-bottom: 25px;
            border-left: 5px solid var(--primary-color);
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

        .attendance-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            overflow: hidden;
            margin-bottom: 25px;
        }

        .attendance-header {
            background: linear-gradient(135deg, #f8f9ff, #eef1ff);
            padding: 20px;
            border-bottom: 1px solid #e9ecef;
        }

        .attendance-title {
            font-size: 18px;
            font-weight: 600;
            color: var(--primary-color);
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .student-count {
            background: var(--primary-color);
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 600;
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
            transform: translateY(-1px);
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .attendance-table tbody td {
            padding: 16px 15px;
            vertical-align: middle;
        }

        .student-name {
            font-weight: 500;
            color: var(--dark-color);
        }

        .status-select {
            min-width: 140px;
            border-radius: 6px;
            border: 2px solid #e9ecef;
            padding: 8px 12px;
            font-weight: 500;
            transition: var(--transition);
            cursor: pointer;
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
            border-radius: 6px;
            border: 2px solid #e9ecef;
            padding: 8px 12px;
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
            padding: 14px 30px;
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
            color: white;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-top: 25px;
            padding: 25px;
            background: #f8f9ff;
            border-radius: var(--border-radius);
        }

        .status-badges {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 15px;
        }

        .status-badge {
            padding: 6px 12px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }

        .badge-present { background: rgba(6, 214, 160, 0.15); color: #0a8754; }
        .badge-absent { background: rgba(239, 71, 111, 0.15); color: #c81e4a; }
        .badge-late { background: rgba(255, 209, 102, 0.15); color: #b38b00; }
        .badge-excused { background: rgba(76, 201, 240, 0.15); color: #0a7ea3; }

        .alert-custom {
            border-radius: var(--border-radius);
            border: none;
            padding: 16px 20px;
            font-weight: 500;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .student-number {
            font-weight: 600;
            color: var(--primary-color);
            width: 40px;
        }

        @media (max-width: 768px) {
            .header-title {
                font-size: 22px;
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
        }
    </style>
</head>
<body>
    <div class="attendance-container">
        <!-- Header Section -->
        <div class="header-card">
            <div class="d-flex justify-content-between align-items-start flex-wrap gap-3">
                <div>
                    <h1 class="header-title">
                        <i class="fas fa-clipboard-check text-primary"></i> Edit Attendance
                    </h1>
                    <p class="header-subtitle">Update student attendance records for the selected class and subject</p>
                    
                    <div class="d-flex flex-wrap gap-3 mt-3">
                        <span class="class-badge">
                            <i class="fas fa-chalkboard"></i> <?= htmlspecialchars($class_name) ?>
                        </span>
                        <span class="subject-badge">
                            <i class="fas fa-book"></i> <?= htmlspecialchars($subject_name) ?>
                        </span>
                        <span class="date-badge">
                            <i class="fas fa-calendar"></i> <?= $date ?>
                        </span>
                    </div>
                </div>
                
                <a href="attendance_history.php?class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>&date=<?= $date ?>" class="back-button">
                    <i class="fas fa-arrow-left"></i> Back to History
                </a>
            </div>
            
            <!-- Status Legend -->
            <div class="status-badges">
                <span class="status-badge badge-present">
                    <i class="fas fa-check-circle"></i> Present
                </span>
                <span class="status-badge badge-absent">
                    <i class="fas fa-times-circle"></i> Absent
                </span>
                <span class="status-badge badge-late">
                    <i class="fas fa-clock"></i> Late
                </span>
                <span class="status-badge badge-excused">
                    <i class="fas fa-user-check"></i> Excused
                </span>
            </div>
        </div>

        <!-- Message Alert -->
        <?php if ($message): ?>
            <div class="alert alert-success alert-custom">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle fa-lg me-3 text-success"></i>
                    <div>
                        <strong>Success!</strong> <?= htmlspecialchars($message) ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Attendance Form -->
        <div class="attendance-card">
            <div class="attendance-header">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h3 class="attendance-title">
                        <i class="fas fa-user-graduate"></i> Student Attendance Records
                    </h3>
                    <span class="student-count">
                        <i class="fas fa-users me-1"></i> <?= count($students) ?> Students
                    </span>
                </div>
            </div>
            
            <div class="table-container">
                <form method="post">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th style="width: 60px;">#</th>
                                <th>Student Name</th>
                                <th style="width: 160px;">Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $i => $st): 
                                $current = $attendance[$st['id']] ?? ['status'=>'present','remarks'=>''];
                                $statusClass = 'status-' . $current['status'];
                            ?>
                            <tr>
                                <td data-label="No." class="student-number"><?= $i+1 ?></td>
                                <td data-label="Student Name" class="student-name">
                                    <i class="fas fa-user-circle text-muted me-2"></i>
                                    <?= htmlspecialchars($st['name']) ?>
                                </td>
                                <td data-label="Status">
                                    <select name="status[<?= $st['id'] ?>]" class="form-select status-select <?= $statusClass ?>">
                                        <?php 
                                        $statusOptions = [
                                            'present' => ['Present', 'fas fa-check-circle text-success'],
                                            'absent' => ['Absent', 'fas fa-times-circle text-danger'],
                                            'late' => ['Late', 'fas fa-clock text-warning'],
                                            'excused' => ['Excused', 'fas fa-user-check text-info']
                                        ];
                                        
                                        foreach($statusOptions as $k => $v): 
                                        ?>
                                            <option value="<?= $k ?>" <?= $current['status']==$k ? 'selected' : '' ?> data-icon="<?= $v[1] ?>">
                                                <?= $v[0] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </td>
                                <td data-label="Remarks">
                                    <input type="text" 
                                           name="remarks[<?= $st['id'] ?>]" 
                                           class="form-control remarks-input" 
                                           value="<?= htmlspecialchars($current['remarks']) ?>" 
                                           placeholder="Enter remarks (optional)">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    
                    <!-- Action Buttons -->
                    <div class="action-buttons">
                        <button type="submit" class="save-button">
                            <i class="fas fa-save"></i> Save Attendance
                        </button>
                        <a href="attendance_history.php?class_id=<?= $class_id ?>&subject_id=<?= $subject_id ?>&date=<?= $date ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                        <div class="ms-auto d-flex align-items-center text-muted">
                            <i class="fas fa-info-circle me-2"></i>
                            <small>Changes will be saved for all students</small>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <!-- Footer Note -->
        <div class="text-center text-muted mt-4 mb-4">
            <p class="small">
                <i class="fas fa-shield-alt me-1"></i> 
                Attendance records are securely stored and can be reviewed in the history page.
            </p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add status indicator to select options
        document.addEventListener('DOMContentLoaded', function() {
            // Update status select border based on selection
            const statusSelects = document.querySelectorAll('.status-select');
            
            statusSelects.forEach(select => {
                updateSelectStyle(select);
                
                select.addEventListener('change', function() {
                    updateSelectStyle(this);
                });
            });
            
            function updateSelectStyle(select) {
                // Remove all status classes
                select.classList.remove('status-present', 'status-absent', 'status-late', 'status-excused');
                
                // Add appropriate class
                const statusClass = 'status-' + select.value;
                select.classList.add(statusClass);
                
                // Update option icons (optional visual enhancement)
                const iconClass = select.options[select.selectedIndex].getAttribute('data-icon');
                select.style.backgroundImage = `none`; // Reset
            }
            
            // Add animation to rows when status changes
            document.querySelectorAll('select[name^="status"]').forEach(select => {
                select.addEventListener('change', function() {
                    const row = this.closest('tr');
                    row.style.backgroundColor = '#f0f9ff';
                    setTimeout(() => {
                        row.style.backgroundColor = '';
                    }, 500);
                });
            });
            
            // Form submission loading state
            const form = document.querySelector('form');
            const saveButton = document.querySelector('.save-button');
            
            form.addEventListener('submit', function() {
                saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Saving...';
                saveButton.disabled = true;
            });
        });
    </script>
</body>
</html>