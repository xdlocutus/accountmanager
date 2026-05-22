<?php require_once __DIR__ . '/../includes/bootstrap.php'; $error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
 if(!verifyCsrf($_POST['csrf_token'] ?? '')){ $error='Invalid CSRF'; }
 elseif(!rateLimit('login', appConfig()['rate_limit']['login']['limit'], appConfig()['rate_limit']['login']['window'])){$error='Too many attempts';}
 else { $u=$userService->authenticate($_POST['email']??'', $_POST['password']??''); if($u){$_SESSION['user']=['id'=>$u['id'],'role'=>$u['role'],'email'=>$u['email']]; header('Location: '.($u['role']==='admin'?'/admin/index.php':'/dashboard.php')); exit;} $error='Invalid credentials';}}
?><form method='post'><input type='hidden' name='csrf_token' value='<?=csrfToken()?>'><?=$error?><input name='email'><input type='password' name='password'><button>Login</button></form>
