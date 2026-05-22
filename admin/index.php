<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('admin');
$stats=[
 'users'=>$pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'],
 'active'=>$pdo->query("SELECT COUNT(*) c FROM users WHERE status='active'")->fetch()['c'],
 'expired'=>$pdo->query("SELECT COUNT(*) c FROM users WHERE status='expired'")->fetch()['c'],
 'revenue'=>$pdo->query('SELECT COALESCE(SUM(price),0) revenue FROM users u JOIN packages p ON p.id=u.package_id')->fetch()['revenue']
];
?><h1>Admin Dashboard</h1><ul><li>Total <?=$stats['users']?></li><li>Active <?=$stats['active']?></li><li>Expired <?=$stats['expired']?></li><li>Revenue <?=$stats['revenue']?></li></ul>
