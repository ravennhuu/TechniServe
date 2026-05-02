<?php
// Pair B
// functions.php — shared helper functions for validation and formatting
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3NF FIX 2: deductSLAHours() and deductSLAVisit() have been REMOVED.
// sla_contracts.hours_used and site_visits_used no longer exist as stored columns.
// SLA usage is now computed on demand via the v_sla_usage VIEW:
//   SELECT * FROM v_sla_usage WHERE client_id = ?

/**
 * Calculates SLA compliance percentage.
 */
function calcCompliance($resolved, $total) {
    if ($total == 0) return 0;
    return round(($resolved / $total) * 100, 2);
}

/**
 * Formats a database timestamp into a readable date.
 */
function formatDate($timestamp) {
    return date('M d, Y h:i A', strtotime($timestamp));
}

/**
 * Hashes a plain-text password.
 */
function hashPassword($plain) {
    return password_hash($plain, PASSWORD_BCRYPT);
}

/**
 * Role-based access control helper.
 */
function guardRole($allowed_role) {
    if (!isset($_SESSION['role']) || $_SESSION['role'] !== $allowed_role) {
        if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
            header('Content-Type: application/json');
            http_response_code(403);
            echo json_encode(['success' => false, 'message' => 'Forbidden.']);
        } else {
            header('Location: ../pages/dashboard.php');
        }
        exit();
    }
}
?>