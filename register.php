<?php
require_once __DIR__ . '/includes/bootstrap.php';
$error = '';
$packages = array_map(fn($p)=>['id'=>$p['id'],'name'=>$p['name']], $subscriptionService->packages());
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF';
    } elseif (!rateLimit('signup', appConfig()['rate_limit']['signup']['limit'], appConfig()['rate_limit']['signup']['window'])) {
        $error = 'Rate limit';
    } else {
        try {
            $userService->register($_POST['email'] ?? '', $_POST['password'] ?? '', (int) $_POST['package_id']);
            header('Location: /login.php');
            exit;
        } catch (Throwable $e) {
            $error = $e->getMessage();
        }
    }
}
?>
<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Register</title><link rel='stylesheet' href='/assets/css/style.css'></head><body>
<main class='auth-wrap'>
  <form class='auth-card' method='post'>
    <h1>Create account</h1><p>Pick a plan and start your subscription instantly.</p>
    <?php if ($error): ?><div class='alert alert-danger'><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'>
    <label>Email</label><input name='email' type='email' required>
    <label>Password</label><input type='password' name='password' minlength='8' required>
    <label>Package</label><select name='package_id' required><?php foreach ($packages as $p): ?><option value='<?= $p['id'] ?>'><?= htmlspecialchars($p['name']) ?></option><?php endforeach; ?></select>
    <button class='btn btn-primary' style='width:100%'>Register</button>
    <p class='muted'>Already registered? <a href='/login.php'>Login</a></p>
  </form>
</main>
</body></html>
