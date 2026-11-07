<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Membership plans';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Plans</h2><div class="table-wrap"><table><tr><th>Plan</th><th>Price</th><th>Duration</th><th>Active</th></tr><?php foreach($pdo->query("SELECT * FROM membership_plans ORDER BY price") as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= money($r['price']) ?></td><td><?= (int)$r['duration_days'] ?> days</td><td><?= $r['is_active']?'Yes':'No' ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>