<?php
// api/users/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'admin') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("SELECT id, client_id, name, email, role, is_active, created_at FROM users WHERE id = ?");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } else {
            $stmt = $pdo->prepare("SELECT id, client_id, name, email, role, is_active, created_at FROM users ORDER BY name ASC");
            $stmt->execute();
            $data = $stmt->fetchAll();
        }
    } else {
        // Users can only fetch their own profile info
        $stmt = $pdo->prepare("SELECT id, client_id, name, email, role, is_active, created_at FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $data = $stmt->fetch();
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch user data.']);
}
