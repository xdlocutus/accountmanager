<?php
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    if (isset($_POST['extend_days'])) {
        $pdo->prepare('UPDATE users SET expires_at=DATE_ADD(expires_at, INTERVAL :d DAY) WHERE id=:id')->execute([':d' => (int) $_POST['extend_days'], ':id' => (int) $_POST['user_id']]);
    }
    if (isset($_POST['disable'])) {
        $subscriptionService->enqueueJob('disable_user', ['user_id' => (int) $_POST['user_id'], 'jellyfin_user_id' => $_POST['jellyfin_user_id']]);
    }
    if (isset($_POST['enable'])) {
        $subscriptionService->enqueueJob('enable_user', ['user_id' => (int) $_POST['user_id'], 'jellyfin_user_id' => $_POST['jellyfin_user_id']]);
    }
}
$users = $pdo->query('SELECT * FROM users ORDER BY created_at DESC')->fetchAll();
adminHeader('User Management');
?>
<div class='table-wrap'><table><thead><tr><th>Email</th><th>Status</th><th>Expires</th><th>Actions</th></tr></thead><tbody>
<?php foreach ($users as $u): ?>
<tr>
<td><?= htmlspecialchars((string) $u['email']) ?></td>
<td><span class='pill'><?= htmlspecialchars((string) $u['status']) ?></span></td>
<td><?= htmlspecialchars((string) ($u['expires_at'] ?? '')) ?></td>
<td class='actions'>
<form method='post'><input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'><input type='hidden' name='user_id' value='<?= (int) $u['id'] ?>'><input type='hidden' name='jellyfin_user_id' value='<?= htmlspecialchars((string) $u['jellyfin_user_id']) ?>'><input type='number' min='1' name='extend_days' placeholder='Days'><button class='btn' type='submit'>Extend</button></form>
<form method='post'><input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'><input type='hidden' name='user_id' value='<?= (int) $u['id'] ?>'><input type='hidden' name='jellyfin_user_id' value='<?= htmlspecialchars((string) $u['jellyfin_user_id']) ?>'><button class='btn' name='disable' value='1' type='submit'>Disable</button></form>
<form method='post'><input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'><input type='hidden' name='user_id' value='<?= (int) $u['id'] ?>'><input type='hidden' name='jellyfin_user_id' value='<?= htmlspecialchars((string) $u['jellyfin_user_id']) ?>'><button class='btn btn-primary' name='enable' value='1' type='submit'>Enable</button></form>
</td></tr>
<?php endforeach; ?>
</tbody></table></div>
<?php adminFooter();
