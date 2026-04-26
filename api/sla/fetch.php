<?php
// api/sla/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'client') {
        $stmt = $pdo->prepare("SELECT * FROM sla_contracts WHERE client_id = ? AND is_active = 1");
        $stmt->execute([$_SESSION['client_id']]);
        $data = $stmt->fetch();
    } else {
        $client_id = $_GET['client_id'] ?? null;
        if ($client_id) {
            $stmt = $pdo->prepare("SELECT * FROM sla_contracts WHERE client_id = ? ORDER BY created_at DESC");
            $stmt->execute([$client_id]);
        } else {
            $stmt = $pdo->prepare("
                SELECT s.*, c.company_name 
                FROM sla_contracts s
                JOIN clients c ON s.client_id = c.id
                ORDER BY s.is_active DESC, s.created_at DESC
            ");
            $stmt->execute();
        }
        $data = $stmt->fetchAll();
    }

    echo json_encode(['success' => true, 'data' => $data]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch SLA data.']);
}
