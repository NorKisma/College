<?php
// Helper functions for the admin system

function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validate_csrf_token($token) {
    return hash_equals($_SESSION['csrf_token'] ?? '', $token);
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match('/^[\d\s\-\+\(\)]{10,20}$/', $phone);
}

function is_email_taken($conn, $email, $exclude_id = null) {
    $sql = "SELECT COUNT(*) as count FROM users WHERE email = ?";
    $params = [$email];
    $types = "s";
    
    if ($exclude_id) {
        $sql .= " AND id != ?";
        $params[] = $exclude_id;
        $types .= "i";
    }
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    return $result['count'] > 0;
}

function flash_message($type, $message) {
    $_SESSION['flash_messages'][] = [
        'type' => $type,
        'message' => $message
    ];
}

function get_flash_messages() {
    $messages = $_SESSION['flash_messages'] ?? [];
    unset($_SESSION['flash_messages']);
    return $messages;
}

/**
 * Log an action to the audit_logs table
 *
 * @param mysqli $conn Database connection
 * @param int $user_id ID of the user performing the action
 * @param string $action Action type (e.g., SUBJECT_CREATE, USER_DELETE)
 * @param string $description Detailed description of the action
 * @return bool Returns true on success, false on failure
 */
function log_audit($conn, $user_id, $action, $description) {
    $stmt = $conn->prepare("INSERT INTO audit_logs (user_id, action, description, created_at) VALUES (?, ?, ?, NOW())");
    if (!$stmt) {
        error_log("Audit log prepare error: " . $conn->error);
        return false;
    }
    $stmt->bind_param('iss', $user_id, $action, $description);
    return $stmt->execute();
}

function find_previous_class_fee(array $classFees, int $class_id, int $year, int $month, int $maxBack = 24)
{
    if (!isset($classFees[$class_id])) {
        return null;
    }

    $y = $year;
    $m = $month;

    for ($i = 0; $i <= $maxBack; $i++) {

        if (isset($classFees[$class_id][$y][$m])) {
            $fee = $classFees[$class_id][$y][$m];

            // Track where the fee came from (VERY useful)
            $fee['source_year'] = $y;
            $fee['source_month'] = $m;

            return $fee;
        }

        // move back one month
        $m--;
        if ($m < 1) {
            $m = 12;
            $y--;
        }
    }

    return null;
}

?>
