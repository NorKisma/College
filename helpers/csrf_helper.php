<?php
// helpers/csrf_helper.php

/**
 * Generate a new CSRF token and store it in session
 *
 * @return string
 */
function generate_csrf_token(): string {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token from POST request
 *
 * @param string|null $token
 * @return bool
 */
function validate_csrf_token(?string $token): bool {
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * Output a hidden input field with CSRF token for forms
 *
 * @return void
 */
function csrf_input_field(): void {
    $token = generate_csrf_token();
    echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
}
