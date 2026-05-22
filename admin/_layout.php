<?php
require_once __DIR__ . '/../includes/bootstrap.php';
requireAuth('admin');
function adminHeader(string $title): void {
?>
<!doctype html>
<html><head><meta charset='utf-8'><meta name='viewport' content='width=device-width, initial-scale=1'>
<title><?= htmlspecialchars($title) ?></title><link rel='stylesheet' href='/assets/css/style.css'></head><body>
<header class='topbar'><div class='container topbar-inner'><div class='brand'>Admin Panel</div><a class='btn' href='/login.php'>Switch account</a></div></header>
<main class='container admin-main'>
<h1><?= htmlspecialchars($title) ?></h1>
<nav class='admin-nav'>
<a class='btn' href='/admin/index.php'>Dashboard</a>
<a class='btn' href='/admin/users.php'>Users</a>
<a class='btn' href='/admin/packages.php'>Packages</a>
<a class='btn' href='/admin/settings.php'>Settings</a>
<a class='btn' href='/admin/logs.php'>Audit logs</a>
</nav>
<?php }
function adminFooter(): void { echo "</main></body></html>"; }
