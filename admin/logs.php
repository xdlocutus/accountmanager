<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('admin');
$logs=$pdo->query('SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 200')->fetchAll(); foreach($logs as $l){echo "<div>{$l['created_at']} {$l['action']} {$l['details']}</div>";}
