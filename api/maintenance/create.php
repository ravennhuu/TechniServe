<?php
// api/maintenance/create.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

// Only admin can log maintenance
if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$ticket_id     = $_POST['ticket_id']     ?? null;
$title         = trim($_POST['title']    ?? '');
$description   = trim($_POST['description'] ?? '');
$activity_type = $_POST['activity_type'] ?? 'other';
$hours_spent   = $_POST['hours_spent']   ?? 0;
$status        = $_POST['status']        ?? 'scheduled';
$performed_by  = $_POST['performed_by']  ?? $_SESSION['user_id'];

if (!$ticket_id || !$title) {
    echo json_encode(['success' => false, 'message' => 'Ticket ID and Title are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare("
        INSERT INTO maintenance_logs (ticket_id, performed_by, title, description, activity_type, hours_spent, status, completed_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;

    $stmt->execute([
        $ticket_id,
        $performed_by,
        $title,
        $description,
        $activity_type,
        $hours_spent,
        $status,
        $completed_at
    ]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Maintenance log created successfully.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create maintenance log.']);
}
