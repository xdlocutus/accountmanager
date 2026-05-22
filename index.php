<?php
require_once __DIR__ . '/includes/bootstrap.php';
$packages = $subscriptionService->packages();
$settings = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch() ?: ['site_name' => 'StreamBox', 'site_logo' => ''];
?><!doctype html>
<html>
<head>
<meta charset='utf-8'>
<meta name='viewport' content='width=device-width, initial-scale=1'>
<title><?= htmlspecialchars($settings['site_name']) ?></title>
<link rel='stylesheet' href='/assets/css/style.css'>
</head>
<body>
<header class='topbar'>
  <div class='container topbar-inner'>
    <div class='brand'><?= htmlspecialchars($settings['site_name']) ?></div>
    <a class='btn btn-primary' href='login.php'>Login</a>
  </div>
</header>
<main class='container'>
  <section class='hero'>
    <h1>Streaming subscriptions made simple.</h1>
    <p>Choose a package, create your account, and start enjoying content with a fast, clean, and reliable member area.</p>
  </section>
  <section class='cards'>
    <?php foreach ($packages as $pkg): ?>
      <article class='card'>
        <h3><?= htmlspecialchars($pkg['name']) ?></h3>
        <div class='price'>$<?= number_format((float) $pkg['price'], 2) ?></div>
        <div class='meta'>
          <span class='pill'><?= (int) $pkg['duration_days'] ?> days</span>
        </div>
        <p class='muted'><?= htmlspecialchars((string) ($pkg['description'] ?? 'Great value plan for uninterrupted streaming.')) ?></p>
        <a class='btn btn-primary' href='/register.php?package_id=<?= (int) $pkg['id'] ?>'>Sign up</a>
      </article>
    <?php endforeach; ?>
  </section>
</main>
</body>
</html>
