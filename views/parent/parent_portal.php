<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../config/sessions.php';

// Check if parent is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'parent') {
    header("Location: /College/public/login.php");
    exit;
}

$parent_id    = $_SESSION['user_id'];
$parent_phone = $_SESSION['parent_phone'];


// Fetch parent details
$parent_query = $conn->prepare("SELECT * FROM parents WHERE id = ?");
$parent_query->bind_param("i", $parent_id);
$parent_query->execute();
$parent_result = $parent_query->get_result();
$parent = $parent_result->fetch_assoc();

// Fetch parent's children (matching guardian phone)
$children_query = $conn->prepare("
    SELECT 
        s.*,
        cs.class_id,
        c.name as class_name,
        c.section_id,
        sec.name as section_name
    FROM students s
    LEFT JOIN class_students cs ON s.id = cs.student_id AND cs.is_active = 1
    LEFT JOIN classes c ON cs.class_id = c.id
    LEFT JOIN sections sec ON c.section_id = sec.id
    WHERE s.guardian_phone = ?
    ORDER BY s.name ASC
");
$children_query->bind_param("s", $parent_phone);
$children_query->execute();
$children_result = $children_query->get_result();
$children = [];
while ($child = $children_result->fetch_assoc()) {
    $children[] = $child;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parent Portal - <?php echo htmlspecialchars($parent['name']); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --secondary-color: #3498db;
            --success-color: #27ae60;
            --warning-color: #f39c12;
            --danger-color: #e74c3c;
            --light-bg: #f8f9fa;
        }
        
        body {
            background-color: var(--light-bg);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .parent-header {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
            margin-bottom: 1.5rem;
        }
        
        .card:hover {
            transform: translateY(-5px);
        }
        
        .card-header {
            background-color: white;
            border-bottom: 2px solid var(--primary-color);
            font-weight: 600;
        }
        
        .nav-tabs .nav-link {
            color: var(--primary-color);
            font-weight: 500;
        }
        
        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }
        
        .stat-card {
            text-align: center;
            padding: 1.5rem;
        }
        
        .stat-icon {
            font-size: 2.5rem;
            margin-bottom: 1rem;
        }
        
        .child-avatar {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid var(--secondary-color);
        }
        
        .attendance-present {
            background-color: #d4edda;
            color: #155724;
        }
        
        .attendance-absent {
            background-color: #f8d7da;
            color: #721c24;
        }
        
        .attendance-late {
            background-color: #fff3cd;
            color: #856404;
        }
        
        .grade-excellent {
            color: #28a745;
            font-weight: bold;
        }
        
        .grade-good {
            color: #17a2b8;
            font-weight: bold;
        }
        
        .grade-average {
            color: #ffc107;
            font-weight: bold;
        }
        
        .grade-poor {
            color: #dc3545;
            font-weight: bold;
        }
        
        .messages-list {
            max-height: 400px;
            overflow-y: auto;
        }
        
        .message-item {
            border-left: 4px solid var(--secondary-color);
            margin-bottom: 1rem;
            padding-left: 1rem;
        }
        
        .message-unread {
            background-color: #e3f2fd;
            border-left-color: var(--danger-color);
        }
        
        .sidebar {
            position: sticky;
            top: 20px;
        }
        
        .sidebar .list-group-item {
            border: none;
            border-left: 4px solid transparent;
            font-weight: 500;
        }
        
        .sidebar .list-group-item.active {
            background-color: var(--light-bg);
            color: var(--primary-color);
            border-left-color: var(--primary-color);
        }
        
        .section-title {
            color: var(--primary-color);
            border-bottom: 2px solid var(--primary-color);
            padding-bottom: 0.5rem;
            margin-bottom: 1.5rem;
        }
        
        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .print-btn {
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <!-- Parent Header -->
    <div class="parent-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1><i class="bi bi-person-badge"></i> <?php echo htmlspecialchars($parent['name']); ?></h1>
                    <h4>Ku soo dhawoow, <?php echo htmlspecialchars($parent['name']); ?> Qeybta aad ka arki karto ardayda aad masuulka ka tahay.</h4>
                    <p class="mb-0">Tell: <?php echo htmlspecialchars($parent['phone']); ?> | Email: <?php echo htmlspecialchars($parent['email']); ?></p>
                </div>
                <div class="col-md-4 text-end">
                    <!-- <button onclick="window.print()" class="btn btn-light me-2"><i class="bi bi-printer"></i> Print</button> -->
                    <a href="/../college/public/logout.php" class="btn btn-outline-light"><i class="bi bi-box-arrow-right"></i> Ka Bax</a>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-lg-3 col-md-4 sidebar">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people"></i> Ardaydayda</h5>
                        <div class="list-group" id="childrenList">
                            <?php if (count($children) > 0): ?>
                                <?php foreach ($children as $index => $child): ?>
                                    <a href="#" class="list-group-item list-group-item-action child-selector <?php echo $index === 0 ? 'active' : ''; ?>" 
                                       data-child-id="<?php echo $child['id']; ?>">
                                        <i class="bi bi-person-circle"></i>
                                        <?php echo htmlspecialchars($child['name']); ?>
                                        <small class="d-block text-muted">Class: <?php echo $child['class_name'] ?? 'Not Assigned'; ?></small>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="alert alert-warning">
                                    Ma jiraan arday aad masuul ka tahay.
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <hr>
                        
                        <h6 class="mt-3"><i class="bi bi-gear"></i> Waad Badali kartaa passwordkaga</h6>
                        <div class="d-grid gap-2">
                            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#updateProfileModal">
                                <i class="bi bi-key"></i> Badal password-kaaga
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Stats -->
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-bar-chart"></i> Xog deg deg ah</h5>
                        <div id="quickStats">
                            <!-- Stats will be loaded via AJAX -->
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-lg-9 col-md-8">
                <!-- Child Overview -->
                <div class="card" id="childOverview">
                    <div class="card-header">
                        <h4 class="mb-0"><i class="bi bi-person-vcard"></i> Xogta Ardayga</h4>
                    </div>
                    <div class="card-body">
                        <div id="overviewContent">
                            <!-- Overview content will be loaded via AJAX -->
                            <p class="text-center text-muted">Dooro Arday...</p>
                        </div>
                    </div>
                </div>

                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="parentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="attendance-tab" data-bs-toggle="tab" data-bs-target="#attendance" type="button">
                            <i class="bi bi-calendar-check"></i> Imaanshaha
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="exams-tab" data-bs-toggle="tab" data-bs-target="#exams" type="button">
                            <i class="bi bi-journal-text"></i> Imtixaanka & Natiijooyinka
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance" type="button">
                            <i class="bi bi-cash-stack"></i> Lacgaha
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="messages-tab" data-bs-toggle="tab" data-bs-target="#messages" type="button">
                            <i class="bi bi-chat-left-text"></i> Farriimaha
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="timetable-tab" data-bs-toggle="tab" data-bs-target="#timetable" type="button">
                            <i class="bi bi-clock"></i> Jadwalka
                        </button>
                    </li>
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="parentTabsContent">
                    <!-- Attendance Tab -->
                    <div class="tab-pane fade show active" id="attendance" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Xogta Imaanshaha</h5>
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <label for="attendanceMonth" class="form-label">Dooro bisha</label>
                                        <input type="month" class="form-control" id="attendanceMonth" value="<?php echo date('Y-m'); ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <button class="btn btn-primary mt-4" id="loadAttendance">
                                            <i class="bi bi-search"></i> Load Attendance
                                        </button>
                                    </div>
                                </div>
                                <div id="attendanceContent">
                                    <!-- Attendance will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Exams & Results Tab -->
                    <div class="tab-pane fade" id="exams" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Natiijooyinka Imtixaanka</h5>
                                <div id="examsContent">
                                    <!-- Exams will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Finance Tab -->
                    <div class="tab-pane fade" id="finance" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Xogta Lacgaha</h5>
                                <div class="alert alert-info">
                                    <i class="bi bi-info-circle"></i> Fadlan ku bixi lacagaha waqtigooda.
                                </div>
                                <div id="financeContent">
                                    <!-- Finance data will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Messages Tab -->
                    <div class="tab-pane fade" id="messages" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Farriimaha Iskuulka</h5>
                                <div id="messagesContent">
                                    <!-- Messages will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Timetable Tab -->
                    <div class="tab-pane fade" id="timetable" role="tabpanel">
                        <div class="card">
                            <div class="card-body">
                                <h5 class="section-title">Jadwalka Isbuuclaha</h5>
                                <div id="timetableContent">
                                    <!-- Timetable will be loaded via AJAX -->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Profile Modal -->
    <div class="modal fade" id="updateProfileModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-key"></i> Badal Password-ka</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="updatePinForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="currentPin" class="form-label">Password-ka Hadda</label>
                            <input type="password" class="form-control" id="currentPin" required>
                        </div>
                        <div class="mb-3">
                            <label for="newPin" class="form-label">Password Cusub (4-6 number)</label>
                            <input type="password" class="form-control" id="newPin" pattern="\d{4,6}" required>
                            <small class="text-muted">Must be 4-6 digits</small>
                        </div>
                        <div class="mb-3">
                            <label for="confirmPin" class="form-label">Xaqiiji Password-ka cusub</label>
                            <input type="password" class="form-control" id="confirmPin" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">OK</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        let currentChildId = <?php echo count($children) > 0 ? $children[0]['id'] : 'null'; ?>;
        
        $(document).ready(function() {
            // Load initial data for first child
            if (currentChildId) {
                loadChildData(currentChildId);
                loadQuickStats(currentChildId);
            }
            
            // Child selection
            $('.child-selector').click(function(e) {
                e.preventDefault();
                $('.child-selector').removeClass('active');
                $(this).addClass('active');
                
                currentChildId = $(this).data('child-id');
                loadChildData(currentChildId);
                loadQuickStats(currentChildId);
            });
            
            // Load attendance
            $('#loadAttendance').click(function() {
                const month = $('#attendanceMonth').val();
                loadAttendance(currentChildId, month);
            });
            
            // Refresh data
            $('#refreshData').click(function() {
                if (currentChildId) {
                    loadChildData(currentChildId);
                    loadQuickStats(currentChildId);
                    showAlert('Data refreshed successfully!', 'success');
                }
            });
            
            // Update PIN form
            $('#updatePinForm').submit(function(e) {
                e.preventDefault();
                updatePin();
            });
            
            // Auto-load current month's attendance
            if (currentChildId) {
                loadAttendance(currentChildId, $('#attendanceMonth').val());
            }
        });
        
        function loadChildData(childId) {
            // Load overview
            $.ajax({
                url: 'ajax/get_child_overview.php',
                method: 'POST',
                data: { child_id: childId },
                success: function(response) {
                    $('#overviewContent').html(response);
                }
            });
            
            // Load exams
            $.ajax({
                url: 'ajax/get_child_exams.php',
                method: 'POST',
                data: { child_id: childId },
                success: function(response) {
                    $('#examsContent').html(response);
                }
            });
            
            // Load finance
            $.ajax({
                url: 'ajax/get_child_finance.php',
                method: 'POST',
                data: { child_id: childId },
                success: function(response) {
                    $('#financeContent').html(response);
                }
            });
            
            // Load messages
            $.ajax({
                url: 'ajax/get_child_messages.php',
                method: 'POST',
                data: { child_id: childId, parent_phone: '<?php echo $parent_phone; ?>' },
                success: function(response) {
                    $('#messagesContent').html(response);
                }
            });
            
            // Load timetable
            $.ajax({
                url: 'ajax/get_child_timetable.php',
                method: 'POST',
                data: { child_id: childId },
                success: function(response) {
                    $('#timetableContent').html(response);
                }
            });
        }
        
        function loadQuickStats(childId) {
            $.ajax({
                url: 'ajax/get_quick_stats.php',
                method: 'POST',
                data: { child_id: childId },
                success: function(response) {
                    $('#quickStats').html(response);
                }
            });
        }
        
        function loadAttendance(childId, month) {
            $.ajax({
                url: 'ajax/get_child_attendance.php',
                method: 'POST',
                data: { 
                    child_id: childId,
                    month: month
                },
                success: function(response) {
                    $('#attendanceContent').html(response);
                }
            });
        }
        
        function updatePin() {
            const currentPin = $('#currentPin').val();
            const newPin = $('#newPin').val();
            const confirmPin = $('#confirmPin').val();
            
            if (newPin !== confirmPin) {
                showAlert('New PINs do not match!', 'danger');
                return;
            }
            
            $.ajax({
                url: 'ajax/update_parent_pin.php',
                method: 'POST',
                data: {
                    parent_id: <?php echo $parent_id; ?>,
                    current_pin: currentPin,
                    new_pin: newPin
                },
                success: function(response) {
                    const data = JSON.parse(response);
                    if (data.success) {
                        showAlert(data.message, 'success');
                        $('#updateProfileModal').modal('hide');
                        $('#updatePinForm')[0].reset();
                    } else {
                        showAlert(data.message, 'danger');
                    }
                }
            });
        }
        
        function showAlert(message, type) {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
            alertDiv.style.cssText = 'top: 20px; right: 20px; z-index: 1050; min-width: 300px;';
            alertDiv.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.body.appendChild(alertDiv);
            
            setTimeout(() => {
                alertDiv.remove();
            }, 5000);
        }
    </script>
</body>
</html>