<?php
// api/sla/create.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$client_id            = $_POST['client_id']            ?? null;
$monthly_hours_pool   = $_POST['monthly_hours_pool']   ?? 20;
$site_visits_included = $_POST['site_visits_included'] ?? 5;
$response_time_hrs    = $_POST['response_time_hrs']    ?? 4;
$resolution_time_hrs  = $_POST['resolution_time_hrs']  ?? 24;
$start_date           = $_POST['start_date']           ?? null;
$end_date             = $_POST['end_date']             ?? null;

if (!$client_id || !$start_date || !$end_date) {
    echo json_encode(['success' => false, 'message' => 'Client ID, Start Date, and End Date are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // Deactivate existing contracts for this client
    $stmt = $pdo->prepare("UPDATE sla_contracts SET is_active = 0 WHERE client_id = ?");
    $stmt->execute([$client_id]);

    // Insert new contract
    $stmt = $pdo->prepare("
        INSERT INTO sla_contracts (client_id, monthly_hours_pool, site_visits_included, response_time_hrs, resolution_time_hrs, start_date, end_date, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, 1)
    ");
    $stmt->execute([
        $client_id,
        $monthly_hours_pool,
        $site_visits_included,
        $response_time_hrs,
        $resolution_time_hrs,
        $start_date,
        $end_date
    ]);

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'SLA contract created and activated.']);
} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create SLA contract.']);
}
