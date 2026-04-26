<?php
// api/clients/create.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$user_id        = $_POST['user_id']        ?? null;
$company_name   = trim($_POST['company_name']   ?? '');
$contact_person = trim($_POST['contact_person'] ?? '');
$contact_email  = trim($_POST['contact_email']  ?? '');
$contact_phone  = trim($_POST['contact_phone']  ?? '');
$address        = trim($_POST['address']        ?? '');

if (!$user_id || !$company_name || !$contact_person || !$contact_email) {
    echo json_encode(['success' => false, 'message' => 'User ID, Company Name, and Contact details are required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO clients (user_id, company_name, address, contact_person, contact_email, contact_phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $company_name, $address, $contact_person, $contact_email, $contact_phone]);
    $clientId = $pdo->lastInsertId();

    // Link user back to client
    $stmt = $pdo->prepare("UPDATE users SET client_id = ? WHERE id = ?");
    $stmt->execute([$clientId, $user_id]);

    echo json_encode(['success' => true, 'message' => 'Client profile created successfully.', 'id' => $clientId]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create client profile.']);
}
