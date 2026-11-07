<?php
require_once __DIR__.'/../config/app.php';
require_role('admin');
$pageTitle='Settings';
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?><main class="dashboard-main"><div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
<section class="panel"><h2>Application settings</h2><p>Configure environment variables and payment/AI credentials on the server. Never store secret keys in public-facing settings.</p><p>Gym name: <?= e(setting($pdo,'gym_name','IRONX Fitness')) ?></p></section></main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>