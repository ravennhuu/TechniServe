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
    // This query calculates stats for ALL clients for the given period
    $sql = "
        SELECT 
            c.id as client_id,
            COUNT(t.id) as total_tickets,
            SUM(CASE WHEN t.status = 'resolved' OR t.status = 'closed' THEN 1 ELSE 0 END) as resolved_tickets,
            SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END) as critical_count,
            SUM(CASE WHEN t.priority = 'high' THEN 1 ELSE 0 END) as high_count,
            SUM(CASE WHEN t.priority = 'low' THEN 1 ELSE 0 END) as low_count,
            AVG(TIMESTAMPDIFF(HOUR, t.created_at, t.resolved_at)) as avg_response_hrs,
            sc.hours_used,
            sc.site_visits_used
        FROM clients c
        LEFT JOIN tickets t ON c.id = t.client_id AND MONTH(t.created_at) = ? AND YEAR(t.created_at) = ?
        LEFT JOIN sla_contracts sc ON c.id = sc.client_id AND sc.is_active = 1
        GROUP BY c.id
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([$month, $year]);
    $stats = $stmt->fetchAll();

    foreach ($stats as $row) {
        $compliance = calcCompliance($row['resolved_tickets'], $row['total_tickets']);
        
        // Insert or update report
        $stmt = $pdo->prepare("
            INSERT INTO reports (client_id, generated_by, month, year, total_tickets, resolved_tickets, critical_count, high_count, low_count, compliance_pct, avg_response_hrs, hours_used, site_visits_used)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE 
                total_tickets = VALUES(total_tickets),
                resolved_tickets = VALUES(resolved_tickets),
                critical_count = VALUES(critical_count),
                high_count = VALUES(high_count),
                low_count = VALUES(low_count),
                compliance_pct = VALUES(compliance_pct),
                avg_response_hrs = VALUES(avg_response_hrs),
                hours_used = VALUES(hours_used),
                site_visits_used = VALUES(site_visits_used)
        ");
        
        $stmt->execute([
            $row['client_id'],
            $_SESSION['user_id'],
            $month,
            $year,
            $row['total_tickets'],
            $row['resolved_tickets'],
            $row['critical_count'],
            $row['high_count'],
            $row['low_count'],
            $compliance,
            $row['avg_response_hrs'] ?? 0,
            $row['hours_used'] ?? 0,
            $row['site_visits_used'] ?? 0
        ]);
    }

    echo json_encode(['success' => true, 'message' => "Reports for $month/$year generated successfully."]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to generate reports: ' . $e->getMessage()]);
}
