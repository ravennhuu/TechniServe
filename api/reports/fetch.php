<?php
// api/reports/fetch.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

try {
    if ($_SESSION['role'] === 'client') {
        $stmt = $pdo->prepare("
            SELECT r.*, c.company_name 
            FROM reports r
            JOIN clients c ON r.client_id = c.id
            WHERE r.client_id = ?
            ORDER BY r.year DESC, r.month DESC
        ");
        $stmt->execute([$_SESSION['client_id']]);
    } else {
        $client_id = $_GET['client_id'] ?? null;
        if ($client_id) {
            $stmt = $pdo->prepare("
                SELECT r.*, c.company_name 
                FROM reports r
                JOIN clients c ON r.client_id = c.id
                WHERE r.client_id = ?
                ORDER BY r.year DESC, r.month DESC
            ");
            $stmt->execute([$client_id]);
        } else {
            $stmt = $pdo->prepare("
                SELECT r.*, c.company_name 
                FROM reports r
                JOIN clients c ON r.client_id = c.id
                ORDER BY r.year DESC, r.month DESC
            ");
            $stmt->execute();
        }
    }

    $reports = $stmt->fetchAll();
    echo json_encode(['success' => true, 'data' => $reports]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to fetch reports.']);
}
