<?php
// api/maintenance/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'client') {
        $client_id = $_SESSION['client_id'] ?? null;

        // Fallback: fetch client_id dynamically if missing from session
        if (!$client_id) {
            $cstmt = $pdo->prepare("SELECT id FROM clients WHERE user_id = ?");
            $cstmt->execute([$_SESSION['user_id']]);
            $client = $cstmt->fetch();
            if ($client) {
                $client_id = $client['id'];
                $_SESSION['client_id'] = $client_id;
            }
        }

        $stmt = $pdo->prepare("
            SELECT m.*, t.client_id, c.company_name, t.subject as ticket_subject, u.name as performed_by_name
            FROM maintenance_logs m
            JOIN tickets t ON m.ticket_id = t.id
            JOIN clients c ON t.client_id = c.id
            JOIN users u ON m.performed_by = u.id
            WHERE t.client_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$client_id]);
    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, t.client_id, c.company_name, t.subject as ticket_subject, u.name as performed_by_name
            FROM maintenance_logs m
            JOIN tickets t ON m.ticket_id = t.id
            JOIN clients c ON t.client_id = c.id
            JOIN users u ON m.performed_by = u.id
            ORDER BY m.created_at DESC
        ");
        $stmt->execute();
    }

    $logs = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $logs]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch maintenance logs.']);
}
