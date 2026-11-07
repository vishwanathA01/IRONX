<?php
require_once __DIR__.'/../config/app.php';
require_role('trainer');
$pageTitle='Trainer dashboard';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>

<?php $s=$pdo->prepare("SELECT COUNT(*) FROM workout_plans WHERE trainer_id=?");$s->execute([current_user()['id']]);$plans=$s->fetchColumn();$s=$pdo->prepare("SELECT COUNT(*) FROM bookings WHERE trainer_id=? AND session_date>=CURDATE()");$s->execute([current_user()['id']]);$bookings=$s->fetchColumn(); ?>
<div class="stats-grid"><article class="stat-card"><small>Workout plans created</small><strong><?= (int)$plans ?></strong></article><article class="stat-card"><small>Upcoming bookings</small><strong><?= (int)$bookings ?></strong></article></div><section class="panel"><h2>Trainer workspace</h2><p>Use the sidebar to manage assigned clients, create workout and diet plans, review bookings and monitor progress.</p></section>
</main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>