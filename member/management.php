<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='My Management';
$userId=(int)current_user()['id'];
$success=''; $error='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
    verify_csrf();
    $subject=trim($_POST['subject']??'');
    $category=$_POST['category']??'other';
    $message=trim($_POST['message']??'');
    $allowed=['membership','billing','training','booking','technical','other'];
    if ($subject==='' || mb_strlen($subject)>160 || $message==='' || !in_array($category,$allowed,true)) {
        $error='Please provide a subject, category and message.';
    } else {
        $stmt=$pdo->prepare('INSERT INTO member_support_requests(user_id,subject,category,message) VALUES(?,?,?,?)');
        $stmt->execute([$userId,$subject,$category,$message]);
        $success='Your request has been submitted to the IRONX team.';
    }
}
$counts=[];
foreach ([
 'Bookings'=>"SELECT COUNT(*) FROM bookings WHERE member_id=? AND status IN ('requested','confirmed')",
 'Workout plans'=>'SELECT COUNT(*) FROM workout_plans WHERE member_id=?',
 'Active goals'=>"SELECT COUNT(*) FROM fitness_goals WHERE member_id=? AND status='active'",
 'Open requests'=>"SELECT COUNT(*) FROM member_support_requests WHERE user_id=? AND status IN ('open','in_progress')"
] as $label=>$sql) { try {$s=$pdo->prepare($sql);$s->execute([$userId]);$counts[$label]=(int)$s->fetchColumn();} catch(Throwable $e) {$counts[$label]=0;} }
$requests=[]; try {$s=$pdo->prepare('SELECT * FROM member_support_requests WHERE user_id=? ORDER BY created_at DESC LIMIT 8');$s->execute([$userId]);$requests=$s->fetchAll();} catch(Throwable $e) {}
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main management-page">
 <div class="dash-top management-top">
  <div><span class="eyebrow">IRONX MEMBER WORKSPACE</span><h1>My Management</h1><p class="dash-subtitle">Your personal fitness hub — manage your account, training, bookings and support.</p></div>
  <div class="management-top-actions"><span class="management-date"><?= date('D, d M Y') ?></span><a class="btn btn-dark btn-small" href="<?= BASE_URL ?>/index.php">IRONX Home ↗</a></div>
 </div>
 <?php if($success): ?><div class="alert alert-success"><?= e($success) ?></div><?php endif; ?>
 <?php if($error): ?><div class="alert alert-error"><?= e($error) ?></div><?php endif; ?>

 <section class="management-hero">
  <div class="management-hero-copy"><span class="management-kicker">YOUR MEMBERSHIP. YOUR MOMENTUM.</span><h2>Stay in control<br><em>of your progress.</em></h2><p>Everything you need to keep your IRONX journey organized, from daily training to support requests.</p><div class="management-hero-actions"><a class="btn btn-white" href="<?= BASE_URL ?>/member/profile.php">Manage my profile <span>→</span></a><a class="management-hero-link" href="<?= BASE_URL ?>/member/activities.php">Log an activity ↗</a></div></div>
  <div class="management-hero-art"><div class="management-orbit management-orbit-one"></div><div class="management-orbit management-orbit-two"></div><img src="<?= BASE_URL ?>/assets/images/ironx-mark.png" alt="IRONX"></div>
  <div class="management-hero-foot"><span>IRONX MEMBER CONTROL CENTRE</span><span>ONE ACCOUNT · ALL YOUR TOOLS</span></div>
 </section>

 <section class="management-metrics">
 <?php $metricMeta=['Bookings'=>['◷','Sessions requested or confirmed'],'Workout plans'=>['▤','Training plans assigned to you'],'Active goals'=>['◎','Fitness targets in progress'],'Open requests'=>['✉','Requests awaiting a response']]; foreach($counts as $label=>$value): $meta=$metricMeta[$label]??['•','Your account overview']; ?>
  <article class="management-metric"><span class="management-metric-icon"><?= $meta[0] ?></span><div><small><?= e($label) ?></small><strong><?= (int)$value ?></strong><p><?= e($meta[1]) ?></p></div></article>
 <?php endforeach; ?>
 </section>

 <section class="management-section">
  <div class="management-section-heading"><div><span class="eyebrow">PERSONAL CONTROLS</span><h2>Your fitness tools</h2><p>Jump straight to the area you want to manage.</p></div><span class="management-section-tag">10 MEMBER TOOLS</span></div>
  <div class="management-tools">
  <?php $tools=[
   ['01','♙','Profile & details','Keep your personal information up to date','profile.php','Account'],
   ['02','▤','Workout management','Review the training plan assigned to you','workouts.php','Training'],
   ['03','✳','Nutrition plan','Check your diet and nutrition guidance','diet.php','Wellness'],
   ['04','◷','Bookings & sessions','Request, review or manage your sessions','bookings.php','Schedule'],
   ['05','▦','Attendance','See your gym visit history','attendance.php','Activity'],
   ['06','◎','Fitness goals','Set targets and track your consistency','goals.php','Progress'],
   ['07','↗','Progress reports','Record weight and body measurements','progress.php','Progress'],
   ['08','₹','Payment history','Review your personal transactions','payments.php','Account'],
   ['09','▥','Activity tracker','Log workouts and daily activity','activities.php','Activity'],
   ['10','✦','Fitness assistant','Get general fitness guidance','ai-assistant.php','Wellness']
  ]; foreach($tools as $tool): ?>
   <a class="management-tool" href="<?= BASE_URL ?>/member/<?= e($tool[4]) ?>"><span class="management-tool-icon"><?= e($tool[1]) ?></span><span class="management-tool-copy"><small><?= e($tool[5]) ?> · <?= e($tool[0]) ?></small><strong><?= e($tool[2]) ?></strong><em><?= e($tool[3]) ?></em></span><b>↗</b></a>
  <?php endforeach; ?>
  </div>
 </section>

 <div class="management-lower-grid">
  <section class="member-card management-support-card">
   <div class="card-heading"><div><span class="eyebrow">WE'RE HERE TO HELP</span><h2>Contact gym management</h2></div><span class="management-card-icon">✉</span></div>
   <p class="management-card-intro">Need help with membership, billing, training or bookings? Send a message to the IRONX team and follow its status below.</p>
   <form method="post" class="form-grid management-support-form"><?= csrf_field() ?>
    <label>Subject<input name="subject" maxlength="160" required placeholder="e.g. Question about my membership"></label>
    <label>Request category<select name="category"><option value="membership">Membership</option><option value="billing">Billing</option><option value="training">Training</option><option value="booking">Booking</option><option value="technical">Technical support</option><option value="other">Other</option></select></label>
    <label class="full-width">How can we help?<textarea name="message" rows="5" required maxlength="5000" placeholder="Tell us a little more about what you need..."></textarea><small class="management-form-hint">Please don't include passwords or sensitive payment details.</small></label>
    <button class="btn btn-dark" type="submit">Send support request <span>→</span></button>
   </form>
  </section>
  <section class="member-card management-history-card">
   <div class="card-heading"><div><span class="eyebrow">YOUR COMMUNICATIONS</span><h2>Request history</h2></div><span class="management-card-icon">◷</span></div>
   <p class="management-card-intro">Track the latest messages you've sent to the IRONX team.</p>
   <?php if($requests): foreach($requests as $r): $statusClass=in_array($r['status'],['resolved','closed'],true)?'is-done':(in_array($r['status'],['in_progress'],true)?'is-progress':'is-open'); ?>
    <article class="management-request"><div class="management-request-top"><strong><?= e($r['subject']) ?></strong><span class="management-status <?= $statusClass ?>"><?= e(ucfirst(str_replace('_',' ',$r['status']))) ?></span></div><div class="management-request-meta"><?= e(ucfirst(str_replace('_',' ',$r['category']))) ?> <span>·</span> <?= e(date('d M Y',strtotime($r['created_at']))) ?></div><?php if(!empty($r['admin_reply'])): ?><div class="management-reply"><strong>IRONX team reply</strong><p><?= e($r['admin_reply']) ?></p></div><?php endif; ?></article>
   <?php endforeach; else: ?><div class="management-empty"><span>✉</span><strong>No requests yet</strong><p>Your submitted support requests and team replies will appear here.</p></div><?php endif; ?>
  </section>
 </div>
 <p class="management-security"><span>⌑</span> Your privacy matters. You can manage only your own member account and records. Payment approvals, membership pricing and website-wide settings remain restricted to authorized administrators.</p>
</main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
