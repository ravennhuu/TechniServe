<?php
// api/reports/generate.php
require_once '../../includes/auth.php';
require_once '../../includes/db.php';
require_once '../../includes/functions.php';
header('Content-Type: application/json');

if ($_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$month = $_POST['month'] ?? date('n');
$year  = $_POST['year']  ?? date('Y');

try {
    // Fetch all active clients for the given period
    $stmt = $pdo->prepare("SELECT id FROM clients");
    $stmt->execute();
    $clients = $stmt->fetchAll();

    foreach ($clients as $row) {
        // 3NF FIX 4: reports only stores metadata (who generated, which month).
        // All aggregate figures are computed live via v_monthly_report VIEW.
        $stmt = $pdo->prepare("
            INSERT INTO reports (client_id, generated_by, month, year)
            VALUES (?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
                generated_by = VALUES(generated_by),
                generated_at = CURRENT_TIMESTAMP
        ");
        $stmt->execute([
            $row['id'],
            $_SESSION['user_id'],
            $month,
            $year
        ]);
    }

    echo json_encode(['success' => true, 'message' => "Reports for $month/$year generated successfully."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to generate reports: ' . $e->getMessage()]);
}
