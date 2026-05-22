<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && verifyCsrf($_POST['csrf_token']??'')){
$pdo->prepare('INSERT INTO packages (name,price,duration_days,description) VALUES (:n,:p,:d,:x)')->execute([':n'=>$_POST['name'],':p'=>$_POST['price'],':d'=>$_POST['duration_days'],':x'=>$_POST['description']]);}
$pkgs=$pdo->query('SELECT * FROM packages')->fetchAll(); ?><form method='post'><input type='hidden' name='csrf_token' value='<?=csrfToken()?>'><input name='name'><input name='price'><input name='duration_days'><input name='description'><button>Add</button></form><?php foreach($pkgs as $p){echo "<p>{$p['name']}</p>";}
