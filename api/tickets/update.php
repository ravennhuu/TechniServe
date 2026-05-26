<?php
// api/tickets/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

$id       = $_POST['id']       ?? null;
$status   = $_POST['status']   ?? null;
$priority = $_POST['priority'] ?? null;
$note          = trim($_POST['note'] ?? '');
$sla_deduction = isset($_POST['sla_deduction']) ? (float)$_POST['sla_deduction'] : 0;

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Ticket ID is required.']);
    exit();
}

try {
    // Check ownership if client
    if ($_SESSION['role'] === 'client') {
        $client_id = $_SESSION['client_id'] ?? null;
        
        // Fallback for missing client_id in session
        if (!$client_id) {
            $cstmt = $pdo->prepare("SELECT id FROM clients WHERE user_id = ?");
            $cstmt->execute([$_SESSION['user_id']]);
            $client = $cstmt->fetch();
            if ($client) {
                $client_id = $client['id'];
                $_SESSION['client_id'] = $client_id;
            }
        }

        $stmt = $pdo->prepare("SELECT client_id FROM tickets WHERE id = ?");
        $stmt->execute([$id]);
        $t = $stmt->fetch();
        if (!$t || $t['client_id'] !== $client_id) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
            exit();
        }
        // Clients can maybe only close or add note? For now let's allow status if it's 'closed'
        if ($status && $status !== 'closed') {
            echo json_encode(['success' => false, 'message' => 'Clients can only close tickets.']);
            exit();
        }
    }

    $updates = [];
    $params  = [];

    if ($status) {
        $updates[] = "status = ?";
        $params[]  = $status;
        if ($status === 'resolved' || $status === 'closed') {
            $updates[] = "resolved_at = COALESCE(resolved_at, NOW())";
        }
    }
    if ($priority && $_SESSION['role'] === 'admin') {
        $updates[] = "priority = ?";
        $params[]  = $priority;
    }

    if (empty($updates) && empty($note)) {
        echo json_encode(['success' => false, 'message' => 'Nothing to update.']);
        exit();
    }

    if (!empty($updates)) {
        $params[] = $id;
        $sql = "UPDATE tickets SET " . implode(', ', $updates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);

        // Log activity for status/priority change
        $action = "Ticket updated.";
        if ($status) $action = "Status changed to " . ucfirst(str_replace('_',' ',$status));
        
        $stmt = $pdo->prepare("INSERT INTO ticket_activities (ticket_id, user_id, action, note) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $_SESSION['user_id'], $action, $note]);

        // Add SLA deduction if provided
        if ($sla_deduction > 0 && $_SESSION['role'] === 'admin' && in_array($status, ['closed', 'resolved'])) {
            $stmt_t = $pdo->prepare("SELECT client_id FROM tickets WHERE id = ?");
            $stmt_t->execute([$id]);
            $ticket_data = $stmt_t->fetch();
            $client_id = $ticket_data ? $ticket_data['client_id'] : null;

            if ($client_id) {
                deductSLAHours($pdo, $client_id, $sla_deduction, $id, $_SESSION['user_id'], $note);
            }
        }
    } elseif (!empty($note)) {
        // Just add a note/activity
        $stmt = $pdo->prepare("INSERT INTO ticket_activities (ticket_id, user_id, action, note) VALUES (?, ?, ?, ?)");
        $stmt->execute([$id, $_SESSION['user_id'], "Note added", $note]);
    }

    echo json_encode(['success' => true, 'message' => 'Ticket updated successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update ticket.']);
}
