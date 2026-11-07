<?php
require_once __DIR__.'/../config/app.php';
require_role('trainer');
$pageTitle='Clients';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Assigned clients</h2><div class="table-wrap"><table><tr><th>Name</th><th>Email</th></tr><?php $s=$pdo->prepare("SELECT DISTINCT u.name,u.email FROM users u JOIN workout_plans w ON w.member_id=u.id WHERE w.trainer_id=?");$s->execute([current_user()['id']]);foreach($s as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>