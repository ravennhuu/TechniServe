<?php
// api/leads/submit.php — public endpoint, no auth check
require_once '../../includes/db.php';
header('Content-Type: application/json');

$company  = trim($_POST['company_name']   ?? '');
$contact  = trim($_POST['contact_person'] ?? '');
$email    = trim($_POST['email']          ?? '');
$phone    = trim($_POST['phone']          ?? '');
$plan     = trim($_POST['preferred_plan'] ?? '');
$message  = trim($_POST['message']        ?? '');

if (!$company || !$contact || !$email) {
    echo json_encode(['success' => false, 'message' => 'Company name, contact person, and email are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO leads (company_name, contact_person, email, phone, preferred_plan, message, status)
        VALUES (?, ?, ?, ?, ?, ?, 'pending')
    ");
    $stmt->execute([$company, $contact, $email, $phone, $plan, $message]);

    echo json_encode(['success' => true, 'message' => 'Your request has been received.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to submit request.']);
}
