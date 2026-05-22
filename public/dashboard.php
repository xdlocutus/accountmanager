<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireAuth('user');
$stmt = $pdo->prepare('SELECT u.*,p.name package_name FROM users u LEFT JOIN packages p ON p.id=u.package_id WHERE u.id=:id');
$stmt->execute([':id' => $_SESSION['user']['id']]);
$u = $stmt->fetch();
?>
<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Dashboard</title><link rel='stylesheet' href='/assets/css/style.css'></head><body>
<header class='topbar'><div class='container topbar-inner'><div class='brand'>Account Dashboard</div><a class='btn' href='/login.php'>Switch account</a></div></header>
<main class='container dashboard'>
  <h1>Welcome, <?= htmlspecialchars($u['email']) ?></h1>
  <section class='cards'>
    <article class='card'><h3>Account status</h3><p class='price' style='font-size:1.2rem'><?= htmlspecialchars((string) $u['status']) ?></p></article>
    <article class='card'><h3>Current package</h3><p class='price' style='font-size:1.2rem'><?= htmlspecialchars((string) ($u['package_name'] ?? 'None')) ?></p></article>
    <article class='card'><h3>Expiration date</h3><p class='price' style='font-size:1.2rem'><?= htmlspecialchars((string) ($u['expires_at'] ?? 'Not set')) ?></p></article>
  </section>
</main>
</body></html>
