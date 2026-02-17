<?php
require_once __DIR__ . '/../../../config/db.php';
require_once __DIR__ . '/../../../config/sessions.php';

if (!isset($_POST['payment_id'])) {
    http_response_code(400);
    exit('Invalid request');
}

$payment_id = (int)$_POST['payment_id'];

// OPTIONAL: security check (admin only)
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    exit('Unauthorized');
}

$stmt = $conn->prepare("DELETE FROM student_fees WHERE id = ?");
$stmt->bind_param("i", $payment_id);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo 'success';
} else {
    http_response_code(404);
    echo 'not_found';
}
$stmt->close();
