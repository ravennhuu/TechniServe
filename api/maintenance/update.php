<?php
// api/maintenance/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$id     = $_POST['id']     ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'ID and Status are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Get current status and hours to check if we should deduct now
    $stmt = $pdo->prepare("SELECT status, hours_spent, client_id FROM maintenance_logs WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();

    if (!$current) {
        echo json_encode(['success' => false, 'message' => 'Log not found.']);
        exit();
    }

    $stmt = $pdo->prepare("UPDATE maintenance_logs SET status = ?, completed_at = ? WHERE id = ?");
    $completed_at = ($status === 'completed') ? date('Y-m-d H:i:s') : null;
    $stmt->execute([$status, $completed_at, $id]);

    // If newly completed, deduct hours
    if ($current['status'] !== 'completed' && $status === 'completed' && $current['hours_spent'] > 0) {
        deductSLAHours($pdo, $current['client_id'], $current['hours_spent']);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Maintenance log updated successfully.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update maintenance log.']);
}
