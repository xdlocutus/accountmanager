<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('user');
$stmt=$pdo->prepare('SELECT u.*,p.name package_name FROM users u LEFT JOIN packages p ON p.id=u.package_id WHERE u.id=:id');$stmt->execute([':id'=>$_SESSION['user']['id']]);$u=$stmt->fetch();
?><h1>Welcome <?=htmlspecialchars($u['email'])?></h1><p>Status: <?=$u['status']?></p><p>Package: <?=$u['package_name']?></p><p>Expires: <?=$u['expires_at']?></p>
