<?php
require_once __DIR__ . '/../../../config/db.php';

header('Content-Type: application/json');

$mode = $_GET['mode'] ?? null;

/* =====================
   1) LOAD SECTIONS
   ===================== */
if ($mode === 'sections') {
    $res = $conn->query("SELECT id, name FROM sections ORDER BY name");
    echo json_encode([
        'sections' => $res->fetch_all(MYSQLI_ASSOC)
    ]);
    exit;
}

/* =====================
   2) LOAD CLASSES BY SECTION
   ===================== */
if ($mode === 'classes') {
   $section_id = isset($_GET['section_id']) ? (int)$_GET['section_id'] : 0;

$sql = "SELECT id, name, section_id FROM classes";
if ($section_id > 0) {
    $sql .= " WHERE section_id = $section_id";
}
$sql .= " ORDER BY name";

$res = $conn->query($sql);
$classes = $res->fetch_all(MYSQLI_ASSOC);

echo json_encode(['classes' => $classes]);

    exit;
}


/* =====================
   3) LOAD SUBJECTS BY CLASS
   ===================== */
if ($mode === 'subjects') {
    $class_id = (int)($_GET['class_id'] ?? 0);

    $section = $conn->query("
        SELECT s.id, s.name
        FROM classes c
        JOIN sections s ON s.id = c.section_id
        WHERE c.id = $class_id
    ")->fetch_assoc();

    $subjects = $conn->query("
        SELECT 
            sub.id,
            sub.name,
            cs.day_of_week,
            cs.start_time,
            cs.end_time
        FROM class_subjects cs
        JOIN subjects sub ON sub.id = cs.subject_id
        WHERE cs.class_id = $class_id
        ORDER BY cs.day_of_week, cs.start_time
    ")->fetch_all(MYSQLI_ASSOC);

    echo json_encode([
        'section' => $section,
        'subjects' => $subjects
    ]);
    exit;
}

/* =====================
   4) LOAD STUDENTS + ATTENDANCE
   ===================== */
if ($mode === 'students') {
    $class_id   = (int)$_GET['class_id'];
    $section_id = (int)$_GET['section_id'];
    $subject_id = (int)$_GET['subject_id'];
    $date       = $_GET['date'];

    /* STUDENTS */
    $students = $conn->query("
        SELECT st.id, st.name, st.gender
        FROM class_students cs
        JOIN students st ON st.id = cs.student_id
        WHERE cs.class_id = $class_id
        ORDER BY st.name
    ")->fetch_all(MYSQLI_ASSOC);

    /* EXISTING ATTENDANCE */
    $attendance = [];
    $stmt = $conn->prepare("
        SELECT *
        FROM attendance
        WHERE class_id=? AND section_id=? AND subject_id=? AND `date`=?
    ");
    $stmt->bind_param('iiis', $class_id, $section_id, $subject_id, $date);
    $stmt->execute();

    $res = $stmt->get_result();
    while ($row = $res->fetch_assoc()) {
        $attendance[$row['student_id']] = $row;
    }

    echo json_encode([
        'students' => $students,
        'attendance' => $attendance
    ]);
    exit;
}

/* =====================
   FALLBACK
   ===================== */
echo json_encode(['error' => 'Invalid request']);
