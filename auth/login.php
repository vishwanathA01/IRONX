<?php
require_once __DIR__.'/../config/app.php';
$adminMode = (($_GET['role'] ?? '') === 'admin') || str_contains($_SERVER['SCRIPT_NAME'] ?? '', '/admin/login.php');
if (is_logged_in()) {
    if (!$adminMode || (current_user()['role'] ?? '') === 'admin') redirect(current_user()['role'].'/dashboard.php');
}
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $stmt = $pdo->prepare("SELECT id,name,email,password_hash,role FROM users WHERE email=? AND status='active' LIMIT 1");
    $stmt->execute([strtolower(trim($_POST['email'] ?? ''))]);
    $user = $stmt->fetch();
    if ($user && password_verify($_POST['password'] ?? '', $user['password_hash'])) {
        if ($adminMode && $user['role'] !== 'admin') {
            $error = 'This account is not an administrator. Ask an existing administrator to grant admin access.';
        } else {
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>(int)$user['id'],'name'=>$user['name'],'email'=>$user['email'],'role'=>$user['role']];
            if (($_GET['next'] ?? '') === 'membership' && $user['role'] === 'member') {
                redirect('member/membership.php');
            }
            redirect($user['role'].'/dashboard.php');
        }
    } else {
        $error = 'Invalid email or password.';
    }
}
$pageTitle='Login';include __DIR__.'/../includes/header.php';include __DIR__.'/../includes/navbar.php';?>
<main class="container auth-wrap"><form class="form-card" method="post"><span class="eyebrow"><?= $adminMode ? 'IRONX ADMINISTRATOR ACCESS' : 'WELCOME BACK' ?></span><h1><?= $adminMode ? 'ADMIN LOG IN.' : 'LOG IN.' ?></h1><?= csrf_field() ?><?php if($error): ?><p class="error"><?= e($error) ?></p><?php endif; ?><label>Email address<input type="email" name="email" required autocomplete="email"></label><label>Password<input type="password" name="password" required autocomplete="current-password"></label><button class="btn btn-dark btn-full">Log in</button><p><a href="<?= BASE_URL ?>/auth/forgot-password.php">Forgot password?</a><?php if (!$adminMode): ?> · <a href="<?= BASE_URL ?>/auth/register.php">Create account</a><?php endif; ?></p><?php if (!$adminMode): ?><p><a href="<?= BASE_URL ?>/admin/login.php">Administrator login</a></p><?php endif; ?></form></main><?php include __DIR__.'/../includes/footer.php'; ?>
