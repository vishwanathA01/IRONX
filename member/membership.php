<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle = 'Membership application';
$user = current_user();
$pdo->exec("CREATE TABLE IF NOT EXISTS membership_applications (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    user_id BIGINT UNSIGNED NOT NULL,
    plan_id BIGINT UNSIGNED NOT NULL,
    phone VARCHAR(30) NOT NULL,
    age TINYINT UNSIGNED NOT NULL,
    gender VARCHAR(30) NOT NULL,
    fitness_goal VARCHAR(100) NOT NULL,
    preferred_start_date DATE NOT NULL,
    notes TEXT NULL,
    status ENUM('submitted','reviewed','approved','rejected') NOT NULL DEFAULT 'submitted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (plan_id) REFERENCES membership_plans(id)
) ENGINE=InnoDB");
$plans = $pdo->query("SELECT id,name,price,duration_days,description FROM membership_plans WHERE is_active=1 ORDER BY price ASC")->fetchAll();
$selectedPlan = (int)($_GET['plan'] ?? $_POST['plan_id'] ?? 0);
$error = '';
$success = flash('membership_success');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $planId = (int)($_POST['plan_id'] ?? 0);
    $phone = trim($_POST['phone'] ?? '');
    $age = (int)($_POST['age'] ?? 0);
    $gender = trim($_POST['gender'] ?? '');
    $goal = trim($_POST['fitness_goal'] ?? '');
    $startDate = trim($_POST['preferred_start_date'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $planStmt = $pdo->prepare('SELECT id,price,duration_days FROM membership_plans WHERE id=? AND is_active=1');
    $planStmt->execute([$planId]);
    $plan = $planStmt->fetch();
    $validDate = DateTime::createFromFormat('Y-m-d', $startDate);
    if (!$plan) $error = 'Please select an available membership plan.';
    elseif (!preg_match('/^[0-9+() .-]{7,30}$/', $phone)) $error = 'Please enter a valid phone number.';
    elseif ($age < 16 || $age > 100) $error = 'Please enter an age between 16 and 100.';
    elseif (!in_array($gender, ['Male','Female','Prefer not to say','Other'], true)) $error = 'Please select your gender.';
    elseif (!in_array($goal, ['Weight loss','Muscle building','General fitness','Strength training','Sports performance'], true)) $error = 'Please select your fitness goal.';
    elseif (!$validDate || $validDate->format('Y-m-d') !== $startDate || $startDate < date('Y-m-d')) $error = 'Please choose today or a future start date.';
    else {
        try {
            $pdo->beginTransaction();
            $end = (new DateTimeImmutable($startDate))->modify('+' . (int)$plan['duration_days'] . ' days')->format('Y-m-d');
            $sub = $pdo->prepare("INSERT INTO subscriptions (user_id,plan_id,start_date,end_date,status) VALUES (?,?,?,?,'pending')");
            $sub->execute([(int)$user['id'], $planId, $startDate, $end]);
            $app = $pdo->prepare('INSERT INTO membership_applications (user_id,plan_id,phone,age,gender,fitness_goal,preferred_start_date,notes) VALUES (?,?,?,?,?,?,?,?)');
            $app->execute([(int)$user['id'], $planId, $phone, $age, $gender, $goal, $startDate, $notes !== '' ? $notes : null]);
            $pdo->commit();
            flash('membership_success', 'Your membership request has been submitted. The IRONX team will review it and update your account.');
            redirect('member/membership.php');
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) $pdo->rollBack();
            error_log('Membership application: '.$e->getMessage());
            $error = 'We could not submit your request. Please try again or contact the gym team.';
        }
    }
    $selectedPlan = $planId;
}
$selected = null;
foreach ($plans as $p) if ((int)$p['id'] === $selectedPlan) $selected = $p;
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout">
<?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main">
  <div class="dash-top"><div><span class="eyebrow">IRONX MEMBER AREA</span><h1>Membership application</h1><p>Choose a plan and complete your details — no new account required.</p></div><span class="user-pill"><?= e($user['name']) ?></span></div>
  <?php if ($success): ?><div class="membership-alert success"><?= e($success) ?></div><?php endif; ?>
  <?php if ($error): ?><div class="membership-alert error"><?= e($error) ?></div><?php endif; ?>
  <section class="panel membership-form-panel">
    <div class="membership-form-heading"><span class="eyebrow">STEP 1 · SELECT YOUR PLAN</span><h2>Choose your membership</h2><p>Your account details are already linked to this application.</p></div>
    <form method="post" class="member-application-form">
      <?= csrf_field() ?>
      <label>Membership plan<select name="plan_id" required><option value="">Select a plan</option><?php foreach ($plans as $p): ?><option value="<?= (int)$p['id'] ?>" <?= $selectedPlan === (int)$p['id'] ? 'selected' : '' ?>><?= e($p['name']) ?> — <?= money($p['price']) ?> / <?= (int)$p['duration_days'] ?> days</option><?php endforeach; ?></select></label>
      <div class="membership-form-heading second"><span class="eyebrow">STEP 2 · YOUR DETAILS</span><h2>Tell us about yourself</h2><p>We use these details to set up your membership and help plan your training.</p></div>
      <div class="membership-fields">
        <label>Full name<input type="text" value="<?= e($user['name']) ?>" readonly></label>
        <label>Email address<input type="email" value="<?= e($user['email']) ?>" readonly></label>
        <label>Phone number<input type="tel" name="phone" placeholder="Enter your contact number" value="<?= e($_POST['phone'] ?? '') ?>" required></label>
        <label>Age<input type="number" name="age" min="16" max="100" placeholder="Your age" value="<?= e($_POST['age'] ?? '') ?>" required></label>
        <label>Gender<select name="gender" required><option value="">Select gender</option><?php foreach (['Male','Female','Other','Prefer not to say'] as $g): ?><option <?= ($_POST['gender'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select></label>
        <label>Fitness goal<select name="fitness_goal" required><option value="">Select your goal</option><?php foreach (['Weight loss','Muscle building','General fitness','Strength training','Sports performance'] as $g): ?><option <?= ($_POST['fitness_goal'] ?? '') === $g ? 'selected' : '' ?>><?= e($g) ?></option><?php endforeach; ?></select></label>
        <label>Preferred start date<input type="date" name="preferred_start_date" min="<?= date('Y-m-d') ?>" value="<?= e($_POST['preferred_start_date'] ?? date('Y-m-d')) ?>" required></label>
        <label class="full-field">Additional information / training notes<textarea name="notes" rows="4" maxlength="1500" placeholder="Any training preferences or information you would like our team to know (optional)"><?= e($_POST['notes'] ?? '') ?></textarea></label>
      </div>
      <button class="btn btn-dark" type="submit">Submit membership request ↗</button>
      <p class="membership-form-footnote">Your request will be marked as pending until the IRONX team reviews it. Submission does not take payment.</p>
    </form>
  </section>
</main></div>
<style>
.membership-alert{padding:15px 18px;border-radius:12px;margin:0 0 18px;font-weight:600}.membership-alert.success{background:#e8f7ee;color:#17683b}.membership-alert.error{background:#fff0f0;color:#a52828}.membership-form-panel{max-width:1000px;padding:clamp(22px,4vw,42px)}.membership-form-heading{margin-bottom:22px}.membership-form-heading h2{font-size:clamp(22px,3vw,30px);margin:8px 0}.membership-form-heading p,.membership-form-footnote{color:var(--muted,#777)}.membership-form-heading.second{margin-top:34px;padding-top:28px;border-top:1px solid rgba(120,120,120,.2)}.member-application-form label{display:flex;flex-direction:column;gap:8px;font-weight:650;font-size:14px}.member-application-form input,.member-application-form select,.member-application-form textarea{width:100%;padding:13px 14px;border:1px solid #d9d9df;border-radius:9px;background:#fff;color:#19191d;font:inherit;min-height:48px}.member-application-form input[readonly]{background:#f4f4f6;color:#666}.membership-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:20px;margin-bottom:25px}.membership-fields .full-field{grid-column:1/-1}.membership-form-footnote{font-size:12px;margin-top:14px}@media(max-width:650px){.membership-fields{grid-template-columns:1fr}.membership-fields .full-field{grid-column:auto}}
</style>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
