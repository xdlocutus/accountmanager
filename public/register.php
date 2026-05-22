<?php require_once __DIR__ . '/../includes/bootstrap.php'; $error='';
$packages=$pdo->query('SELECT id,name FROM packages')->fetchAll();
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verifyCsrf($_POST['csrf_token'] ?? '')){$error='Invalid CSRF';}
 elseif(!rateLimit('signup', appConfig()['rate_limit']['signup']['limit'], appConfig()['rate_limit']['signup']['window'])){$error='Rate limit';}
 else {try{$userService->register($_POST['email']??'', $_POST['password']??'', (int)$_POST['package_id']); header('Location: /login.php'); exit;}catch(Throwable $e){$error=$e->getMessage();}}}
?><form method='post'><input type='hidden' name='csrf_token' value='<?=csrfToken()?>'><?=$error?><input name='email'><input type='password' name='password'><select name='package_id'><?php foreach($packages as $p):?><option value='<?=$p['id']?>'><?=$p['name']?></option><?php endforeach;?></select><button>Register</button></form>
