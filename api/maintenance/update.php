<?php
// api/maintenance/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

// Only admin can edit maintenance
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$id            = $_POST['id']            ?? null;
$ticket_id     = $_POST['ticket_id']     ?? null;
$client_id     = $_POST['client_id']     ?? null;
$title         = trim($_POST['title']    ?? '');
$description   = trim($_POST['description'] ?? '');
$activity_type = $_POST['activity_type'] ?? 'other';
$hours_spent   = $_POST['hours_spent']   ?? 0;
$status        = $_POST['status']        ?? 'scheduled';

if (!$id || !$ticket_id || !$client_id || !$title) {
    echo json_encode(['success' => false, 'message' => 'Log ID, Ticket ID, Client ID, and Title are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Fetch the old log to calculate SLA diffs
    $stmt = $pdo->prepare("SELECT * FROM maintenance_logs WHERE id = ?");
    $stmt->execute([$id]);
    $old_log = $stmt->fetch();

    if (!$old_log) {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'Maintenance log not found.']);
        exit();
    }

    $completed_at = $old_log['completed_at'];
    if ($old_log['status'] !== 'completed' && $status === 'completed') {
        $completed_at = date('Y-m-d H:i:s');
    } elseif ($status !== 'completed') {
        $completed_at = null;
    }

    $stmt = $pdo->prepare("
        UPDATE maintenance_logs 
        SET ticket_id = ?, client_id = ?, title = ?, description = ?, activity_type = ?, hours_spent = ?, status = ?, completed_at = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $ticket_id,
        $client_id,
        $title,
        $description,
        $activity_type,
        $hours_spent,
        $status,
        $completed_at,
        $id
    ]);

    // SLA Diffs
    $hour_diff = 0;
    $visit_diff = 0;

    if ($old_log['status'] !== 'completed' && $status === 'completed') {
        // Newly completed -> Deduct
        $hour_diff = $hours_spent;
        if ($activity_type === 'site_visit') $visit_diff = 1;
    } elseif ($old_log['status'] === 'completed' && $status !== 'completed') {
        // Un-completed -> Refund
        $hour_diff = -$old_log['hours_spent'];
        if ($old_log['activity_type'] === 'site_visit') $visit_diff = -1;
    } elseif ($old_log['status'] === 'completed' && $status === 'completed') {
        // Remained completed -> Check for changes
        $hour_diff = $hours_spent - $old_log['hours_spent'];
        $old_visit = ($old_log['activity_type'] === 'site_visit') ? 1 : 0;
        $new_visit = ($activity_type === 'site_visit') ? 1 : 0;
        $visit_diff = $new_visit - $old_visit;
    }

    if ($hour_diff != 0) {
        $stmt = $pdo->prepare("UPDATE sla_contracts SET hours_used = hours_used + ? WHERE client_id = ? AND is_active = 1");
        $stmt->execute([$hour_diff, $client_id]);
    }
    if ($visit_diff != 0) {
        $stmt = $pdo->prepare("UPDATE sla_contracts SET site_visits_used = site_visits_used + ? WHERE client_id = ? AND is_active = 1");
        $stmt->execute([$visit_diff, $client_id]);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Maintenance log updated successfully.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update maintenance log.']);
}
