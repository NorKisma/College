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
$my_role_name = $me['role_name'];

// Admin check
function is_admin() { 
    global $my_role_name; 
    return $my_role_name === 'admin'; 
}

if (!is_admin()) {
    die("Unauthorized access.");
}

// CSRF check
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $class_id = intval($_GET['id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $section_id = intval($_POST['section_id'] ?? 0);
    $save_as_new = intval($_POST['save_as_new'] ?? 0);

    if ($class_id <= 0) {
        die("Invalid class ID for editing.");
    }

    if ($name === '' || $section_id <= 0) {
        die("Class name and section are required.");
    }

    // Check if class has alumni
    $alumni_check = $conn->prepare("SELECT COUNT(*) as cnt FROM alumni WHERE class_id=?");
    $alumni_check->bind_param('i', $class_id);
    $alumni_check->execute();
    $alumni_count = $alumni_check->get_result()->fetch_assoc()['cnt'] ?? 0;

    if ($alumni_count > 0) {
        die("Cannot edit class with alumni.");
    }

    // ==== Save as New Class (duplicate students) ====
    // ==== Save as New Class (duplicate students) ====
if ($save_as_new === 1) {
    // Insert new class
    $stmt = $conn->prepare("INSERT INTO classes (name, section_id) VALUES (?, ?)");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param('si', $name, $section_id);
    if ($stmt->execute()) {
        $new_class_id = $stmt->insert_id;

        // Duplicate students from old class first, set new ones as active
        $dup_stmt = $conn->prepare("
            INSERT INTO class_students (class_id, student_id, batch_id, academic_year, created_at, is_active)
            SELECT ?, student_id, batch_id, academic_year, NOW(), 1
            FROM class_students
            WHERE class_id = ?
        ");
        $dup_stmt->bind_param('ii', $new_class_id, $class_id);
        $dup_stmt->execute();

        // Now set old class students as inactive
        $update_old = $conn->prepare("UPDATE class_students SET is_active=0 WHERE class_id=?");
        $update_old->bind_param('i', $class_id);
        $update_old->execute();

        header("Location: classes.php?created=1");
        exit;
    } else {
        die("Insert Error: " . $conn->error);
    }
}


    // ==== Normal Edit (update existing class) ====
    $stmt = $conn->prepare("UPDATE classes SET name=?, section_id=? WHERE id=?");
    if (!$stmt) die("Prepare failed: " . $conn->error);
    $stmt->bind_param('sii', $name, $section_id, $class_id);
    if ($stmt->execute()) {
        header("Location: classes.php?updated=1");
        exit;
    } else {
        die("Update Error: " . $conn->error);
    }

} else {
    die("Invalid request method.");
}
