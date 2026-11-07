<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Members';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Member directory</h2><div class="table-wrap"><table><tr><th>Name</th><th>Email</th><th>Joined</th><th>Status</th></tr><?php foreach($pdo->query("SELECT name,email,created_at,status FROM users WHERE role='member' ORDER BY id DESC") as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['created_at']) ?></td><td><?= e($r['status']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>