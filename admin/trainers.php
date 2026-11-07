<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Trainers';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Trainer directory</h2><div class="table-wrap"><table><tr><th>Name</th><th>Email</th><th>Specialization</th><th>Experience</th></tr><?php foreach($pdo->query("SELECT u.name,u.email,t.specialization,t.experience_years FROM users u LEFT JOIN trainer_profiles t ON t.user_id=u.id WHERE u.role='trainer'") as $r): ?><tr><td><?= e($r['name']) ?></td><td><?= e($r['email']) ?></td><td><?= e($r['specialization']) ?></td><td><?= e($r['experience_years']) ?></td></tr><?php endforeach; ?></table></div></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>