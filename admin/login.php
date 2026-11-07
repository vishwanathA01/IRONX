<?php
// Dedicated admin sign-in URL; authentication is handled by the shared,
// secure login controller with administrator-role enforcement.
require_once __DIR__ . '/../config/app.php';
if (is_logged_in() && (current_user()['role'] ?? '') === 'admin') {
    redirect('admin/dashboard.php');
}
$_GET['role'] = 'admin';
require __DIR__ . '/../auth/login.php';
