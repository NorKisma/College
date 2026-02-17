<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';
require_once __DIR__ . '/../../../config/helpers.php';

// Auth
$logged_in_id = $_SESSION['user_id'] ?? null;
if (!$logged_in_id) { 
    header('Location: ../../auth/login.php'); 
    exit; 
}

// Load user
$me_stmt = $conn->prepare("SELECT u.id, u.username, r.name AS role_name 
                           FROM users u 
                           JOIN roles r ON r.id = u.role_id 
                           WHERE u.id=?");
$me_stmt->bind_param('i', $logged_in_id);
$me_stmt->execute();
$me = $me_stmt->get_result()->fetch_assoc();
$my_role_name = $me['role_name'] ?? '';

// Admin check
function is_admin() { global $my_role_name; return $my_role_name === 'admin'; }
if (!is_admin()) die("Unauthorized access.");

// CSRF check
if ($_SERVER['REQUEST_METHOD'] !== 'POST') die("Invalid request method.");
if (!isset($_POST['csrf_token']) || !verify_csrf_token($_POST['csrf_token'])) die("Invalid CSRF token.");

// Fetch POST & GET data
$class_id   = intval($_GET['id'] ?? 0);
$name       = trim($_POST['name'] ?? '');
$section_id = intval($_POST['section_id'] ?? 0);
$save_as_new = intval($_POST['save_as_new'] ?? 0);

// Validation
if ($name === '' || $section_id <= 0) die("Class name and section are required.");

// Check for alumni
$alumni_count = 0;
if ($class_id > 0) {
    $stmt = $conn->prepare("SELECT COUNT(*) AS cnt FROM alumni WHERE class_id=?");
    $stmt->bind_param('i', $class_id);
    $stmt->execute();
    $alumni_count = $stmt->get_result()->fetch_assoc()['cnt'] ?? 0;
}

if ($alumni_count > 0 && $save_as_new === 0) {
    die("Cannot update class with alumni.");
}

if ($save_as_new === 1 && $class_id > 0) {
    // === DUPLICATE STUDENTS ONLY ===
    $dup_stmt = $conn->prepare("
        INSERT INTO class_students (class_id, student_id, batch_id, academic_year, created_at, is_active)
        SELECT class_id, student_id, batch_id, academic_year, NOW(), is_active
        FROM class_students
        WHERE class_id = ?
    ");
    $dup_stmt->bind_param('i', $class_id);
    if ($dup_stmt->execute()) {
        header("Location: classes.php?duplicated=1");
        exit;
    } else {
        die("Duplication Error: " . $conn->error);
    }
} else {
    // === NORMAL UPDATE OR NEW CLASS ===
    if ($class_id === 0) {
        // Create new class
        $stmt = $conn->prepare("INSERT INTO classes (name, section_id) VALUES (?, ?)");
        $stmt->bind_param('si', $name, $section_id);
        if ($stmt->execute()) {
            header("Location: classes.php?created=1");
            exit;
        } else {
            die("Insert Error: " . $conn->error);
        }
    } else {
        // Update existing class
        $stmt = $conn->prepare("UPDATE classes SET name=?, section_id=? WHERE id=?");
        $stmt->bind_param('sii', $name, $section_id, $class_id);
        if ($stmt->execute()) {
            header("Location: classes.php?updated=1");
            exit;
        } else {
            die("Update Error: " . $conn->error);
        }
    }
}
