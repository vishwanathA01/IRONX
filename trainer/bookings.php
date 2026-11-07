<?php
require_once __DIR__.'/../config/app.php';
require_role('trainer');
$pageTitle='Bookings';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Your bookings</h2><?php $s=$pdo->prepare("SELECT b.*,u.name member_name FROM bookings b JOIN users u ON u.id=b.member_id WHERE b.trainer_id=? ORDER BY session_date DESC");$s->execute([current_user()['id']]); ?><div class="table-wrap"><table><tr><th>Member</th><th>Date</th><th>Status</th></tr><?php foreach($s as $r): ?><tr><td><?= e($r['member_name']) ?></td><td><?= e($r['session_date']) ?></td><td><?= e($r['status']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>