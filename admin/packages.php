<?php
require_once __DIR__ . '/_layout.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && verifyCsrf($_POST['csrf_token'] ?? '')) {
    $pdo->prepare('INSERT INTO packages (name,price,duration_days,description) VALUES (:n,:p,:d,:x)')->execute([
        ':n' => $_POST['name'], ':p' => $_POST['price'], ':d' => $_POST['duration_days'], ':x' => $_POST['description']
    ]);
}
$pkgs = $pdo->query('SELECT * FROM packages ORDER BY id DESC')->fetchAll();
adminHeader('Packages');
?>
<form class='card' method='post'>
<input type='hidden' name='csrf_token' value='<?= csrfToken() ?>'>
<label>Name</label><input name='name' required>
<label>Price</label><input name='price' type='number' step='0.01' required>
<label>Duration days</label><input name='duration_days' type='number' min='1' required>
<label>Description</label><textarea name='description'></textarea>
<button class='btn btn-primary'>Add package</button>
</form>
<div class='table-wrap'><table><thead><tr><th>Name</th><th>Price</th><th>Duration</th><th>Description</th></tr></thead><tbody>
<?php foreach ($pkgs as $p): ?><tr><td><?= htmlspecialchars((string) $p['name']) ?></td><td>$<?= number_format((float) $p['price'], 2) ?></td><td><?= (int) $p['duration_days'] ?> days</td><td><?= htmlspecialchars((string) $p['description']) ?></td></tr><?php endforeach; ?>
</tbody></table></div>
<?php adminFooter();
