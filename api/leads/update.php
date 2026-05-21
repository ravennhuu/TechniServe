<?php
// api/leads/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$id     = $_POST['id']     ?? null;
$status = $_POST['status'] ?? null;

if (!$id || !$status) {
    echo json_encode(['success' => false, 'message' => 'Lead ID and status are required.']);
    exit();
}

try {
    $pdo->beginTransaction();

    // 1. Fetch the lead
    $stmt = $pdo->prepare("SELECT * FROM leads WHERE id = ?");
    $stmt->execute([$id]);
    $lead = $stmt->fetch();

    if (!$lead) {
        throw new Exception("Lead not found.");
    }

    // 2. Update the lead status
    $stmt = $pdo->prepare("
        UPDATE leads
        SET status = ?, reviewed_by = ?, reviewed_at = NOW()
        WHERE id = ?
    ");
    $stmt->execute([$status, $_SESSION['user_id'], $id]);

    // 3. Automate onboarding if approved
    if ($status === 'approved') {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$lead['email']]);
        if ($stmt->fetch()) {
            throw new Exception("A user with this email already exists in the system.");
        }

        // Create User
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password_hash, role, is_active) VALUES (?, ?, ?, 'client', 1)");
        $password_hash = password_hash('password123', PASSWORD_DEFAULT);
        $stmt->execute([$lead['contact_person'], $lead['email'], $password_hash]);
        $user_id = $pdo->lastInsertId();

        // Create Client
        $stmt = $pdo->prepare("
            INSERT INTO clients (user_id, company_name, contact_person, contact_email, contact_phone)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$user_id, $lead['company_name'], $lead['contact_person'], $lead['email'], $lead['phone']]);
        $client_id = $pdo->lastInsertId();

        // Create SLA Contract based on preferred plan
        $plan = strtolower(trim($lead['preferred_plan'] ?? ''));
        $hours_pool = 10; $visits = 2; $resp_time = 8; $res_time = 48; // Defaults (Standard)
        if (strpos($plan, 'professional') !== false) {
            $hours_pool = 20; $visits = 5; $resp_time = 4; $res_time = 24;
        } elseif (strpos($plan, 'enterprise') !== false) {
            $hours_pool = 40; $visits = 8; $resp_time = 2; $res_time = 12;
        }

        $start_date = date('Y-m-d');
        $end_date = date('Y-m-d', strtotime('+1 year'));

        $stmt = $pdo->prepare("
            INSERT INTO sla_contracts (client_id, monthly_hours_pool, site_visits_included, response_time_hrs, resolution_time_hrs, start_date, end_date, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, 1)
        ");
        $stmt->execute([$client_id, $hours_pool, $visits, $resp_time, $res_time, $start_date, $end_date]);
    }

    $pdo->commit();
    
    $action = ucfirst($status);
    $extraMsg = ($status === 'approved') ? " User account, Client profile, and SLA Contract auto-generated." : "";
    echo json_encode(['success' => true, 'message' => "Lead has been {$action}.{$extraMsg}"]);

} catch (Exception $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to process lead: ' . $e->getMessage()]);
}
