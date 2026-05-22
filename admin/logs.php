<?php
require_once __DIR__ . '/_layout.php';
$logs = $pdo->query('SELECT * FROM audit_logs ORDER BY created_at DESC LIMIT 200')->fetchAll();
adminHeader('Audit Logs');
?>
<div class='table-wrap'><table><thead><tr><th>Time</th><th>Action</th><th>Details</th></tr></thead><tbody>
<?php foreach ($logs as $l): ?><tr><td><?= htmlspecialchars((string) $l['created_at']) ?></td><td><?= htmlspecialchars((string) $l['action']) ?></td><td><?= htmlspecialchars((string) $l['details']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php adminFooter();
