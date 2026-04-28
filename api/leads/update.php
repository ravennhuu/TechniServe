<?php
// api/leads/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$id     = $_POST['id']     ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Lead ID and status are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        UPDATE leads
        SET status = ?, reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$status, $_SESSION['user_id'], $id]);

    if ($stmt->rowCount() > 0) {
        $action = ucfirst($status);
        echo json_encode(['success' => true, 'message' => "Lead has been {$action}d successfully."]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Lead not found or status is already set.']);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update lead status.']);
}
