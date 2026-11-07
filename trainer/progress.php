<?php
require_once __DIR__.'/../config/app.php';
require_role('trainer');
$pageTitle='Client progress';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Client progress records</h2><div class="table-wrap"><table><tr><th>Member</th><th>Weight</th><th>Height</th><th>Date</th></tr><?php foreach($pdo->query("SELECT p.*,u.name FROM progress_records p JOIN users u ON u.id=p.member_id ORDER BY p.recorded_at DESC LIMIT 100") as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['weight_kg']) ?> kg</td><td><?= e($r['height_cm']) ?> cm</td><td><?= e($r['recorded_at']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>