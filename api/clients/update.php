<?php
// api/clients/update.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

$id = $_POST['id'] ?? null;
if (!$id) {
    echo json_encode(['success' => false, 'message' => 'Client ID is required.']);
    exit();
}

// Ownership check for clients
if ($_SESSION['role'] === 'client' && $_SESSION['client_id'] != $id) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$company_name   = trim($_POST['company_name']   ?? '');
$contact_person = trim($_POST['contact_person'] ?? '');
$contact_email  = trim($_POST['contact_email']  ?? '');
$contact_phone  = trim($_POST['contact_phone']  ?? '');
$address        = trim($_POST['address']        ?? '');

try {
    $updates = [];
    $params  = [];

    if ($company_name)   { $updates[] = "company_name = ?";   $params[] = $company_name; }
    if ($contact_person) { $updates[] = "contact_person = ?"; $params[] = $contact_person; }
    if ($contact_email)  { $updates[] = "contact_email = ?";  $params[] = $contact_email; }
    if ($contact_phone)  { $updates[] = "contact_phone = ?";  $params[] = $contact_phone; }
    if ($address)        { $updates[] = "address = ?";        $params[] = $address; }

    if (empty($updates)) {
        echo json_encode(['success' => false, 'message' => 'No fields provided for update.']);
        exit();
    }

    $params[] = $id;
    $sql = "UPDATE clients SET " . implode(', ', $updates) . " WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    echo json_encode(['success' => true, 'message' => 'Client profile updated successfully.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update client profile.']);
}
