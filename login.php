<?php
require_once __DIR__ . '/includes/bootstrap.php';
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid CSRF';
    } elseif (!rateLimit('login', appConfig()['rate_limit']['login']['limit'], appConfig()['rate_limit']['login']['window'])) {
        $error = 'Too many attempts';
    } else {
        $u = $userService->authenticate($_POST['email'] ?? '', $_POST['password'] ?? '');
        if ($u) {
            $userService->setSessionUser($u);
            header('Location: ' . ($u['role'] === 'admin' ? '/admin/index.php' : '/dashboard.php'));
            exit;
        }
        $error = 'Invalid credentials';
    }
}
?>
<!doctype html><html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'><title>Login</title><link rel='stylesheet' href='/assets/css/style.css'></head><body>
<main class='auth-wrap'>
  <form class='auth-card' method='post'>
    <h1>Welcome back</h1><p>Login to access your subscription dashboard.</p>
    <?php if ($error): ?><div class='alert alert-danger'><?= htmlspecialchars($error) ?></div><?php endif; ?>
    <input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'>
    <label>Email</label><input name='email' type='email' required>
    <label>Password</label><input type='password' name='password' required>
    <button class='btn btn-primary' style='width:100%'>Login</button>
    <p class='muted'>No account? <a href='/register.php'>Create one</a></p>
  </form>
</main>
</body></html>
