<?php
// api/users/deactivate.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'User ID is required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("UPDATE users SET is_active = 0 WHERE id = ?");
    $stmt->execute([$id]);
    echo json_encode(['success' => true, 'message' => 'User account deactivated.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to deactivate user account.']);
}
