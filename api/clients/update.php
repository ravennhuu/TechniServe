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
    $stmt = $pdo->prepare("SELECT user_id FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $client = $stmt->fetch();
    if (!$client) {
        echo json_encode(['success' => false, 'message' => 'Client not found.']);
        exit();
    }

    $userId = $client['user_id'];

    $clientUpdates = [];
    $clientParams  = [];
    $userUpdates   = [];
    $userParams    = [];

    if ($company_name)   { $clientUpdates[] = "company_name = ?";   $clientParams[] = $company_name; }
    if ($contact_person) { $clientUpdates[] = "contact_person = ?"; $clientParams[] = $contact_person; }
    if ($contact_email)  { $clientUpdates[] = "contact_email = ?";  $clientParams[] = $contact_email; }
    if ($contact_phone)  { $clientUpdates[] = "contact_phone = ?";  $clientParams[] = $contact_phone; }
    if ($address)        { $clientUpdates[] = "address = ?";        $clientParams[] = $address; }

    if ($contact_person) {
        $userUpdates[] = "name = ?";
        $userParams[]  = $contact_person;
    }
    if ($contact_email) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$contact_email, $userId]);
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'That email address is already used by another account.']);
            exit();
        }

        $userUpdates[] = "email = ?";
        $userParams[]  = $contact_email;
    }

    if (empty($clientUpdates) && empty($userUpdates)) {
        echo json_encode(['success' => false, 'message' => 'No fields provided for update.']);
        exit();
    }

    $pdo->beginTransaction();

    if (!empty($userUpdates)) {
        $userParams[] = $userId;
        $sql = "UPDATE users SET " . implode(', ', $userUpdates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($userParams);
    }

    if (!empty($clientUpdates)) {
        $clientParams[] = $id;
        $sql = "UPDATE clients SET " . implode(', ', $clientUpdates) . " WHERE id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($clientParams);
    }

    $pdo->commit();
    echo json_encode(['success' => true, 'message' => 'Client profile updated successfully.']);
} catch (PDOException $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to update client profile.']);
}