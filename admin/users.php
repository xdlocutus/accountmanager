<?php require_once __DIR__ . '/../includes/bootstrap.php'; requireAuth('admin');
if($_SERVER['REQUEST_METHOD']==='POST' && verifyCsrf($_POST['csrf_token']??'')){
 if(isset($_POST['extend_days'])){$pdo->prepare('UPDATE users SET expires_at=DATE_ADD(expires_at, INTERVAL :d DAY) WHERE id=:id')->execute([':d'=>(int)$_POST['extend_days'],':id'=>(int)$_POST['user_id']]);}
 if(isset($_POST['disable'])){$subscriptionService->enqueueJob('disable_user',['user_id'=>(int)$_POST['user_id'],'jellyfin_user_id'=>$_POST['jellyfin_user_id']]);}
 if(isset($_POST['enable'])){$subscriptionService->enqueueJob('enable_user',['user_id'=>(int)$_POST['user_id'],'jellyfin_user_id'=>$_POST['jellyfin_user_id']]);}
}
$users=$pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll(); foreach($users as $u){echo "<div>{$u['email']} {$u['status']}</div>";}
