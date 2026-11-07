<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Admin dashboard';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>

<div class="stats-grid"><?php $members=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='member'")->fetchColumn();$trainers=(int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='trainer'")->fetchColumn();$revenue=(float)$pdo->query("SELECT COALESCE(SUM(amount),0) FROM payments WHERE status='paid'")->fetchColumn();$today=(int)$pdo->query("SELECT COUNT(*) FROM attendance WHERE DATE(check_in)=CURDATE()")->fetchColumn(); foreach([['Total members',$members],['Active trainers',$trainers],['Recorded revenue',money($revenue)],["Today's check-ins",$today]] as $x): ?><article class="stat-card"><small><?= e($x[0]) ?></small><strong><?= e($x[1]) ?></strong></article><?php endforeach; ?></div>
<section class="panel"><h2>Recent members</h2><div class="table-wrap"><table><tr><th>Name</th><th>Email</th><th>Joined</th></tr><?php foreach($pdo->query("SELECT name,email,created_at FROM users WHERE role='member' ORDER BY id DESC LIMIT 8") as $m): ?><tr><td><?= e($m['name']) ?></td><td><?= e($m['email']) ?></td><td><?= e($m['created_at']) ?></td></tr><?php endforeach; ?></table></div></section>
</main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>