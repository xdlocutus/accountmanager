<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('admin');
if($_SERVER['REQUEST_METHOD']==='POST'&&verifyCsrf($_POST['csrf_token']??'')){$pdo->prepare('UPDATE settings SET site_name=:name WHERE id=1')->execute([':name'=>$_POST['site_name']]);}
$s=$pdo->query('SELECT * FROM settings WHERE id=1')->fetch(); ?><form method='post'><input type='hidden' name='csrf_token' value='<?=csrfToken()?>'><input name='site_name' value='<?=htmlspecialchars($s['site_name']??'')?>'><button>Save</button></form>
