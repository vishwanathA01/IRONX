<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Bookings';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Bookings</h2><div class="table-wrap"><table><tr><th>Member</th><th>Trainer</th><th>Session</th><th>Status</th></tr><?php foreach($pdo->query("SELECT b.*,m.name member_name,t.name trainer_name FROM bookings b JOIN users m ON m.id=b.member_id LEFT JOIN users t ON t.id=b.trainer_id ORDER BY b.session_date DESC") as $r): ?><tr><td><?= e($r['member_name']) ?></td><td><?= e($r['trainer_name']??'Unassigned') ?></td><td><?= e($r['session_date']) ?></td><td><?= e($r['status']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>