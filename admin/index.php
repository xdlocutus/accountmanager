<?php
require_once __DIR__ . '/_layout.php';
$stats = [
    'users' => $pdo->query('SELECT COUNT(*) c FROM users')->fetch()['c'],
    'active' => $pdo->query("SELECT COUNT(*) c FROM users WHERE status='active'")->fetch()['c'],
    'expired' => $pdo->query("SELECT COUNT(*) c FROM users WHERE status='expired'")->fetch()['c'],
    'revenue' => $pdo->query('SELECT COALESCE(SUM(price),0) revenue FROM users u JOIN packages p ON p.id=u.package_id')->fetch()['revenue']
];
adminHeader('Admin Dashboard');
?>
<section class='cards'>
  <article class='card'><h3>Total users</h3><p class='price'><?= (int) $stats['users'] ?></p></article>
  <article class='card'><h3>Active</h3><p class='price'><?= (int) $stats['active'] ?></p></article>
  <article class='card'><h3>Expired</h3><p class='price'><?= (int) $stats['expired'] ?></p></article>
  <article class='card'><h3>Revenue</h3><p class='price'>$<?= number_format((float) $stats['revenue'], 2) ?></p></article>
</section>
<?php adminFooter();
