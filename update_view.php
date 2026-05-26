<?php
require 'includes/db.php';
try {
    $pdo->exec("
CREATE OR REPLACE VIEW `v_monthly_report` AS
SELECT
  r.id                                                              AS report_id,
  r.client_id,
  c.company_name,
  r.month,
  r.year,
  r.generated_by,
  r.generated_at,

  COUNT(t.id)                                                       AS total_tickets,
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS resolved_tickets,
  SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END) AS closed_tickets,
  SUM(CASE WHEN t.priority = 'critical' THEN 1 ELSE 0 END)         AS critical_count,
  SUM(CASE WHEN t.priority = 'high'     THEN 1 ELSE 0 END)         AS high_count,
  SUM(CASE WHEN t.priority = 'low'      THEN 1 ELSE 0 END)         AS low_count,

  SUM(CASE
    WHEN t.status IN ('resolved', 'closed')
     AND TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at) > sc.response_time_hrs * 3600
    THEN 1 ELSE 0
  END)                                                              AS sla_breaches,

  ROUND(
    SUM(CASE WHEN t.status IN ('resolved', 'closed') THEN 1 ELSE 0 END)
    / NULLIF(COUNT(t.id), 0) * 100, 2
  )                                                                 AS compliance_pct,

  ROUND(
    AVG(TIMESTAMPDIFF(SECOND, t.created_at, t.resolved_at)) / 3600, 2
  )                                                                 AS avg_response_hrs,

  COALESCE(SUM(ml.hours_spent), 0)                                  AS hours_used,

  COUNT(CASE WHEN ml.activity_type = 'site_visit'
             AND ml.status = 'completed'
             THEN 1 END)                                            AS site_visits_used

FROM `reports` r
JOIN `clients`       c  ON c.id         = r.client_id
JOIN `tickets`       t  ON t.client_id  = c.id
                        AND MONTH(t.created_at) = r.month
                        AND YEAR(t.created_at)  = r.year
JOIN `sla_contracts` sc ON sc.client_id = c.id AND sc.is_active = 1
LEFT JOIN `maintenance_logs` ml ON ml.ticket_id = t.id

GROUP BY
  r.id, r.client_id, c.company_name, r.month, r.year,
  r.generated_by, r.generated_at, sc.response_time_hrs;
    ");
    echo "View updated successfully.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
