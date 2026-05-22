<?php
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $pdo->prepare('UPDATE settings SET site_name=:name WHERE id=1')->execute([':name' => $_POST['site_name']]);
}
$s = $pdo->query('SELECT * FROM settings WHERE id=1')->fetch();
adminHeader('Settings');
?>
<form method='post' class='card'>
<input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'>
<label>Site name</label>
<input name='site_name' value='<?= htmlspecialchars((string) ($s['site_name'] ?? '')) ?>'>
<button class='btn btn-primary'>Save settings</button>
</form>
<?php adminFooter();
