<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Attendance';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Recent attendance</h2><div class="table-wrap"><table><tr><th>Member</th><th>Check in</th><th>Check out</th></tr><?php foreach($pdo->query("SELECT a.*,u.name FROM attendance a JOIN users u ON u.id=a.user_id ORDER BY a.id DESC LIMIT 100") as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['check_in']) ?></td><td><?= e($r['check_out']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>