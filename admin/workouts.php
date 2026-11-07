<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Workout plans';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Workout plans</h2><div class="table-wrap"><table><tr><th>Title</th><th>Member</th><th>Trainer</th><th>Created</th></tr><?php foreach($pdo->query("SELECT w.*,m.name member_name,t.name trainer_name FROM workout_plans w JOIN users m ON m.id=w.member_id LEFT JOIN users t ON t.id=w.trainer_id ORDER BY w.id DESC") as $r): ?><tr><td><?= e($r['title']) ?></td><td><?= e($r['member_name']) ?></td><td><?= e($r['trainer_name']??'—') ?></td><td><?= e($r['created_at']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>