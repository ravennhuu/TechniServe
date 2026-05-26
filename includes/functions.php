<?php
// Pair B
// functions.php — shared helper functions for validation and formatting
date_default_timezone_set('Asia/Manila');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

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

/**
 * Deducts hours from a client's SLA pool.
 * Under the 3NF system, this is implemented by inserting a completed
 * maintenance log entry.
 */
function deductSLAHours($pdo, $client_id, $hours, $ticket_id = null, $performed_by = null, $note = '') {
    if ($hours <= 0) {
        return false;
    }

    if (!$performed_by) {
        $performed_by = $_SESSION['user_id'] ?? null;
        if (!$performed_by) {
            // Find the first admin user as fallback
            $stmt = $pdo->query("SELECT id FROM users WHERE role = 'admin' LIMIT 1");
            $performed_by = $stmt->fetchColumn() ?: 1;
        }
    }

    // If ticket_id is not provided, try to find the client's latest ticket
    if (!$ticket_id) {
        $stmt = $pdo->prepare("SELECT id FROM tickets WHERE client_id = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$client_id]);
        $ticket_id = $stmt->fetchColumn();

        // If no ticket exists, create a general SLA adjustments ticket first
        if (!$ticket_id) {
            $stmt = $pdo->prepare("
                INSERT INTO tickets (client_id, created_by, subject, description, priority, status)
                VALUES (?, ?, 'SLA Adjustment Log', 'System ticket created for manual SLA hours deduction', 'low', 'closed')
            ");
            $stmt->execute([$client_id, $performed_by]);
            $ticket_id = $pdo->lastInsertId();

            // Log activity
            $stmt_act = $pdo->prepare("INSERT INTO ticket_activities (ticket_id, user_id, action) VALUES (?, ?, ?)");
            $stmt_act->execute([$ticket_id, $performed_by, 'System ticket created for SLA adjustment.']);
        }
    }

    // Insert the completed maintenance log (which reduces remaining hours via v_sla_usage view)
    $stmt_m = $pdo->prepare("
        INSERT INTO maintenance_logs (ticket_id, performed_by, title, description, activity_type, hours_spent, status, completed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    return $stmt_m->execute([
        $ticket_id,
        $performed_by,
        'Ticket Resolution Deduction',
        $note ?: 'SLA hours deduction applied.',
        'other',
        $hours,
        'completed',
        date('Y-m-d H:i:s')
    ]);
}
?>