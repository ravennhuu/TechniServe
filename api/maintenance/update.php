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
$title         = trim($_POST['title']    ?? '');
$description   = trim($_POST['description'] ?? '');
$activity_type = $_POST['activity_type'] ?? 'other';
$hours_spent   = $_POST['hours_spent']   ?? 0;
$status        = $_POST['status']        ?? 'scheduled';

if (!$id || !$ticket_id || !$title) {
    echo json_encode(['success' => false, 'message' => 'Log ID, Ticket ID, and Title are required.']);
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
        SET ticket_id = ?, title = ?, description = ?, activity_type = ?, hours_spent = ?, status = ?, completed_at = ?
        WHERE id = ?
    ");
    
    $stmt->execute([
        $ticket_id,
        $title,
        $description,
        $activity_type,
        $hours_spent,
        $status,
        $completed_at,
        $id
    ]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Maintenance log updated successfully.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update maintenance log.']);
}
