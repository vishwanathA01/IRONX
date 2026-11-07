<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Payments';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Payment records</h2><div class="table-wrap"><table><tr><th>Member ID</th><th>Amount</th><th>Status</th><th>Reference</th><th>Date</th></tr><?php foreach($pdo->query("SELECT * FROM payments ORDER BY id DESC LIMIT 100") as $r): ?><tr><td><?= (int)$r['user_id'] ?></td><td><?= money($r['amount']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['gateway_payment_id']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>