<?php
// api/sla/update.php
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
    echo json_encode(['success' => false, 'message' => 'SLA ID is required.']);
    exit();
}

// Only allow updating certain fields
$client_id            = $_POST['client_id']            ?? null;
$monthly_hours_pool   = $_POST['monthly_hours_pool']   ?? null;
$site_visits_included = $_POST['site_visits_included'] ?? null;
$response_time_hrs    = $_POST['response_time_hrs']    ?? null;
$resolution_time_hrs  = $_POST['resolution_time_hrs']  ?? null;
$start_date           = $_POST['start_date']           ?? null;
$end_date             = $_POST['end_date']             ?? null;
$is_active            = $_POST['is_active']            ?? null;

try {
    $updates = [];
    $params  = [];

    if ($client_id !== null) { $updates[] = "client_id = ?"; $params[] = $client_id; }
    if ($monthly_hours_pool !== null) { $updates[] = "monthly_hours_pool = ?"; $params[] = $monthly_hours_pool; }
    if ($site_visits_included !== null) { $updates[] = "site_visits_included = ?"; $params[] = $site_visits_included; }
    if ($response_time_hrs !== null) { $updates[] = "response_time_hrs = ?"; $params[] = $response_time_hrs; }
    if ($resolution_time_hrs !== null) { $updates[] = "resolution_time_hrs = ?"; $params[] = $resolution_time_hrs; }
    if ($start_date !== null) { $updates[] = "start_date = ?"; $params[] = $start_date; }
    if ($end_date !== null) { $updates[] = "end_date = ?"; $params[] = $end_date; }
    if ($is_active !== null) { $updates[] = "is_active = ?"; $params[] = $is_active; }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No fields provided for update.']);
        exit();
    }

    $params[] = $id;
    $sql = "UPDATE sla_contracts SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'SLA contract updated successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update SLA contract.']);
}
