<?php require_once __DIR__ . '/../includes/bootstrap.php';
$packages = $pdo->query('SELECT * FROM packages ORDER BY price ASC')->fetchAll();
$settings = $pdo->query('SELECT * FROM settings LIMIT 1')->fetch() ?: ['site_name' => 'StreamBox', 'site_logo' => ''];
?><!doctype html><html><head><meta charset='utf-8'><title><?=htmlspecialchars($settings['site_name'])?></title><link rel='stylesheet' href='/../assets/css/style.css'></head><body>
<header><h1><?=htmlspecialchars($settings['site_name'])?></h1><a href='login.php'>Login</a></header>
<section class='hero'><h2>Unlimited streaming, one subscription.</h2></section>
<section class='cards'><?php foreach($packages as $pkg): ?><div class='card'><h3><?=htmlspecialchars($pkg['name'])?></h3><p>$<?=number_format((float)$pkg['price'],2)?></p><p><?=$pkg['duration_days']?> days</p></div><?php endforeach; ?></section>
</body></html>
