<?php
// api/maintenance/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'client') {
        $stmt = $pdo->prepare("
            SELECT m.*, c.company_name, t.subject as ticket_subject
            FROM maintenance_logs m
            JOIN clients c ON m.client_id = c.id
            JOIN tickets t ON m.ticket_id = t.id
            WHERE m.client_id = ?
            ORDER BY m.created_at DESC
        ");
        $stmt->execute([$_SESSION['client_id']]);
    } else {
        $stmt = $pdo->prepare("
            SELECT m.*, c.company_name, t.subject as ticket_subject
            FROM maintenance_logs m
            JOIN clients c ON m.client_id = c.id
            JOIN tickets t ON m.ticket_id = t.id
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
