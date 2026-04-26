<?php
// api/tickets/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'client') {
        // Clients only see tickets belonging to their company
        $stmt = $pdo->prepare("
            SELECT t.*, c.company_name 
            FROM tickets t
            JOIN clients c ON t.client_id = c.id
            WHERE t.client_id = ?
            ORDER BY t.created_at DESC
        ");
        $stmt->execute([$_SESSION['client_id']]);
    } else {
        // Admins see everything
        $stmt = $pdo->prepare("
            SELECT t.*, c.company_name, u.name as creator_name
            FROM tickets t
            JOIN clients c ON t.client_id = c.id
            JOIN users u ON t.created_by = u.id
            ORDER BY t.created_at DESC
        ");
        $stmt->execute();
    }

    $tickets = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $tickets]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch tickets.']);
}
