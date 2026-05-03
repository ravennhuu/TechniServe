<?php
// api/users/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'admin') {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $stmt = $pdo->prepare("
                SELECT u.id, u.name, u.email, u.role, u.is_active, u.created_at, COALESCE(c.company_name, 'N/A') AS company_name 
                FROM users u 
                LEFT JOIN clients c ON c.user_id = u.id 
                WHERE u.id = ?
            ");
            $stmt->execute([$id]);
            $data = $stmt->fetch();
        } else {
            $stmt = $pdo->prepare("
                SELECT u.id, u.name, u.email, u.role, u.is_active, u.created_at, COALESCE(c.company_name, 'N/A') AS company_name 
                FROM users u 
                LEFT JOIN clients c ON c.user_id = u.id 
                ORDER BY u.created_at DESC
            ");
            $stmt->execute();
            $data = $stmt->fetchAll();
        }
    } else {
        // Users can only fetch their own profile info
        $stmt = $pdo->prepare("
            SELECT u.id, c.id AS client_id, u.name, u.email, u.role, u.is_active, u.created_at, COALESCE(c.company_name, 'N/A') AS company_name 
            FROM users u 
            LEFT JOIN clients c ON c.user_id = u.id 
            WHERE u.id = ?
        ");
        $stmt->execute([$_SESSION['user_id']]);
        $data = $stmt->fetch();
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch user data.']);
}
