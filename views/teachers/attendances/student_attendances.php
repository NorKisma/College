<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$teacher_id = (int)$_SESSION['user_id'];

// Fetch classes this teacher teaches
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
while ($row = $res->fetch_assoc()) {
    $classes[] = $row;
}

// Fetch subjects this teacher teaches
$subjects = [];
$stmt = $conn->prepare("
    SELECT DISTINCT s.id, s.name
    FROM subjects s
    JOIN class_subjects cs ON cs.subject_id = s.id
    WHERE cs.teacher_id = ?
    ORDER BY s.name
");
$stmt->bind_param("i", $teacher_id);
$stmt->execute();
$res = $stmt->get_result();
while ($row = $res->fetch_assoc()) {
    $subjects[] = $row;
}

// For AJAX search by student name
if (isset($_GET['search_name'])) {
    $search = "%{$_GET['search_name']}%";
    $stmt = $conn->prepare("
        SELECT id, name, gender
        FROM students
        WHERE name LIKE ?
        ORDER BY name
        LIMIT 20
    ");
    $stmt->bind_param("s", $search);
    $stmt->execute();
    $results = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    echo json_encode($results);
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Teacher Attendance Dashboard</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
    .present { background:#d4edda; }
    .absent { background:#f8d7da; }
    .attendance-table th, .attendance-table td { text-align:center; vertical-align:middle; }
</style>
</head>
<body>
<div class="container mt-3">
    <h3>Teacher Attendance Dashboard</h3>

    <!-- FILTERS -->
    <div class="card mb-3 p-3">
        <div class="row g-2">
            <div class="col-md-3">
                <label>Classes</label>
                <select id="filterClasses" class="form-select" multiple>
                    <?php foreach($classes as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label>Subjects</label>
                <select id="filterSubjects" class="form-select" multiple>
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label>Gender</label>
                <select id="filterGender" class="form-select">
                    <option value="">All</option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
            </div>
            <div class="col-md-2">
                <label>Date From</label>
                <input type="date" id="dateFrom" class="form-control">
            </div>
            <div class="col-md-2">
                <label>Date To</label>
                <input type="date" id="dateTo" class="form-control">
            </div>
        </div>
        <div class="row g-2 mt-2">
            <div class="col-md-4">
                <input type="text" id="searchName" class="form-control" placeholder="Search student by name">
            </div>
            <div class="col-md-2">
                <button id="btnFilter" class="btn btn-primary w-100">Apply Filters</button>
            </div>
        </div>
    </div>

    <!-- TABLE -->
    <div class="table-responsive">
        <table class="table table-bordered attendance-table" id="attendanceTable">
            <thead class="table-light">
                <tr>
                    <th>Student Name</th>
                    <th>Class</th>
                    <th>Gender</th>
                    <th>Subject</th>
                    <th>Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <!-- AJAX data will load here -->
            </tbody>
        </table>
    </div>

    <!-- CHART -->
    <div class="card mt-3 p-3">
        <h5>Attendance Summary</h5>
        <canvas id="attendanceChart" height="150"></canvas>
    </div>
</div>

<script>
$(document).ready(function(){
    $('#filterClasses, #filterSubjects').select2({ width:'100%' });

    function loadAttendance(filters = {}) {
        $.get('fetch_attendance.php', filters, function(data){
            $('#attendanceTable tbody').html(data.html);

            // Update chart
            const ctx = document.getElementById('attendanceChart').getContext('2d');
            if(window.attChart) window.attChart.destroy();
            window.attChart = new Chart(ctx, {
                type:'bar',
                data:{
                    labels: data.labels,
                    datasets:[{
                        label:'Present',
                        data:data.present,
                        backgroundColor:'#28a745'
                    },{
                        label:'Absent',
                        data:data.absent,
                        backgroundColor:'#dc3545'
                    }]
                },
                options:{ responsive:true, plugins:{ legend:{ position:'top' } } }
            });
        }, 'json');
    }

    $('#btnFilter').on('click', function(){
        loadAttendance({
            classes: $('#filterClasses').val(),
            subjects: $('#filterSubjects').val(),
            gender: $('#filterGender').val(),
            date_from: $('#dateFrom').val(),
            date_to: $('#dateTo').val(),
            search_name: $('#searchName').val()
        });
    });

    // Search on typing
    $('#searchName').on('input', function(){
        loadAttendance({
            search_name: $(this).val()
        });
    });

    // Initial load
    loadAttendance();
});
</script>
</body>
</html>
