<?php
// api/tickets/create.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
header('Content-Type: application/json');

$subject     = trim($_POST['subject']     ?? '');
$description = trim($_POST['description'] ?? '');
$priority    = trim($_POST['priority']    ?? 'low');
$client_id   = $_POST['client_id'] ?? null;

// Validation
if (!$subject || !$description) {
    echo json_encode(['success' => false, 'message' => 'Subject and description are required.']);
    exit();
}

// If client is logged in, use their client_id
if ($_SESSION['role'] === 'client') {
    $client_id = $_SESSION['client_id'];
}

if (!$client_id) {
    echo json_encode(['success' => false, 'message' => 'Client ID is required.']);
    exit();
}

try {
    $stmt = $pdo->prepare("
        INSERT INTO tickets (client_id, created_by, subject, description, priority, status)
        VALUES (?, ?, ?, ?, ?, 'open')
    ");
    $stmt->execute([
        $client_id,
        $_SESSION['user_id'],
        $subject,
        $description,
        $priority
    ]);

    $ticketId = $pdo->lastInsertId();

    // Log activity
    $stmt = $pdo->prepare("INSERT INTO ticket_activities (ticket_id, user_id, action) VALUES (?, ?, ?)");
    $stmt->execute([$ticketId, $_SESSION['user_id'], 'Ticket created.']);

    echo json_encode(['success' => true, 'message' => 'Ticket created successfully.', 'id' => $ticketId]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to create ticket.']);
}
