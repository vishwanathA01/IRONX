<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='Payment history';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Your payments</h2><?php $s=$pdo->prepare('SELECT * FROM payments WHERE user_id=? ORDER BY created_at DESC');$s->execute([current_user()['id']]); ?><div class="table-wrap"><table><tr><th>Amount</th><th>Status</th><th>Reference</th><th>Date</th></tr><?php foreach($s as $r): ?><tr><td><?= money($r['amount']) ?></td><td><?= e($r['status']) ?></td><td><?= e($r['gateway_payment_id']) ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></table></div><p>Online checkout integration requires your own Razorpay account credentials.</p></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>