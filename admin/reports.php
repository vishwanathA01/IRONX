<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Reports';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Business overview</h2><?php $rev=$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid'")->fetchColumn();$count=$pdo->query("SELECT COUNT(*) FROM subscriptions WHERE status='active' AND end_date>=CURDATE()")->fetchColumn(); ?><div class="stats-grid"><article class="stat-card"><small>Lifetime recorded revenue</small><strong><?= money($rev) ?></strong></article><article class="stat-card"><small>Active subscriptions</small><strong><?= (int)$count ?></strong></article></div><p>Reports are calculated from records currently stored in the database.</p></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>