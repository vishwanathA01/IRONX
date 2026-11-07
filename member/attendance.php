<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='My attendance';
$userId=(int)current_user()['id'];
verify_csrf();
$notice=null; $noticeType='success';

$activeStmt=$pdo->prepare("SELECT * FROM attendance WHERE user_id=? AND check_out IS NULL ORDER BY check_in DESC LIMIT 1");
$activeStmt->execute([$userId]); $activeVisit=$activeStmt->fetch();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $action=$_POST['attendance_action']??'';
    if($action==='check_in'){
        if($activeVisit){
            $notice='You are already checked in. Please check out before starting another visit.';
            $noticeType='warning';
        }else{
            $todayStmt=$pdo->prepare("SELECT COUNT(*) FROM attendance WHERE user_id=? AND DATE(check_in)=CURDATE()");
            $todayStmt->execute([$userId]);
            if((int)$todayStmt->fetchColumn()>0){
                $notice='A gym visit is already recorded for today. You can only check in once per day.';
                $noticeType='warning';
            }else{
                $insert=$pdo->prepare("INSERT INTO attendance (user_id,check_in) VALUES (?,NOW())");
                $insert->execute([$userId]);
                $notice='Your gym check-in has been recorded. Have a great workout!';
            }
        }
    }elseif($action==='check_out'){
        if($activeVisit){
            $update=$pdo->prepare("UPDATE attendance SET check_out=NOW() WHERE id=? AND user_id=? AND check_out IS NULL");
            $update->execute([(int)$activeVisit['id'],$userId]);
            $notice='Your check-out has been recorded. Great work today!';
        }else{
            $notice='There is no active gym visit to check out.';
            $noticeType='warning';
        }
    }
    $activeStmt->execute([$userId]); $activeVisit=$activeStmt->fetch();
}

$monthStmt=$pdo->prepare("SELECT COUNT(*) visits, COALESCE(SUM(CASE WHEN check_out IS NOT NULL THEN TIMESTAMPDIFF(MINUTE,check_in,check_out) ELSE 0 END),0) minutes FROM attendance WHERE user_id=? AND YEAR(check_in)=YEAR(CURDATE()) AND MONTH(check_in)=MONTH(CURDATE())");
$monthStmt->execute([$userId]); $monthStats=$monthStmt->fetch();
$todayStmt=$pdo->prepare("SELECT COUNT(*) FROM attendance WHERE user_id=? AND DATE(check_in)=CURDATE()");
$todayStmt->execute([$userId]); $todayVisits=(int)$todayStmt->fetchColumn();
$historyStmt=$pdo->prepare("SELECT * FROM attendance WHERE user_id=? ORDER BY check_in DESC LIMIT 30");
$historyStmt->execute([$userId]); $history=$historyStmt->fetchAll();
$weekStmt=$pdo->prepare("SELECT COUNT(*) FROM attendance WHERE user_id=? AND check_in>=DATE_SUB(CURDATE(),INTERVAL 6 DAY)");
$weekStmt->execute([$userId]); $weekVisits=(int)$weekStmt->fetchColumn();
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main attendance-page">
 <div class="dash-top"><div><span class="eyebrow">IRONX MEMBER SPACE</span><h1>My Attendance</h1><p class="dash-subtitle">Check in when you arrive, check out when you finish, and follow your gym consistency.</p></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
 <section class="attendance-hero"><div class="attendance-hero-copy"><span class="attendance-kicker">SHOW UP. GET STRONGER.</span><h2>Your effort<br><em>counts every day.</em></h2><p>Record your gym visits with a quick check-in and check-out. Your attendance history updates automatically.</p><div class="attendance-hero-tags"><span>Member visit log</span><span>Live session status</span></div></div><div class="attendance-hero-art" aria-hidden="true"><span>IRONX</span><b>✦</b></div></section>
 <?php if($notice): ?><div class="attendance-alert <?= $noticeType==='warning'?'warning':'' ?>"><?= e($notice) ?></div><?php endif; ?>
 <section class="attendance-action-card">
  <div class="attendance-status-area"><span class="attendance-status-dot <?= $activeVisit?'live':'' ?>"></span><div><small>YOUR CURRENT STATUS</small><strong><?= $activeVisit?'Currently checked in':'Not checked in' ?></strong><p><?= $activeVisit?'Session started at '.date('h:i A',strtotime($activeVisit['check_in'])):'Tap check in when you arrive at the gym.' ?></p></div></div>
  <form method="post" class="attendance-action-form">
   <?= csrf_field() ?>
   <?php if($activeVisit): ?><button class="attendance-checkout" type="submit" name="attendance_action" value="check_out">Check out <span>→</span></button>
   <?php elseif($todayVisits===0): ?><button class="attendance-checkin" type="submit" name="attendance_action" value="check_in">Check in now <span>→</span></button>
   <?php else: ?><button class="attendance-checkin" type="button" disabled>Today's visit recorded</button><?php endif; ?>
  </form>
 </section>
 <div class="attendance-stats">
  <article><span class="attendance-stat-icon">▦</span><small>Visits this month</small><strong><?= (int)$monthStats['visits'] ?></strong><p>Gym sessions recorded</p></article>
  <article><span class="attendance-stat-icon">↗</span><small>Visits this week</small><strong><?= $weekVisits ?></strong><p>Last 7 calendar days</p></article>
  <article><span class="attendance-stat-icon">◷</span><small>Time at gym this month</small><strong><?= floor((int)$monthStats['minutes']/60) ?>h <?= (int)$monthStats['minutes']%60 ?>m</strong><p>From completed visits</p></article>
  <article><span class="attendance-stat-icon">✓</span><small>Today's visit</small><strong><?= $todayVisits?'Recorded':'Pending' ?></strong><p><?= $todayVisits?'Your visit is on the log':'Check in when you arrive' ?></p></article>
 </div>
 <section class="attendance-history-card">
  <div class="attendance-section-head"><div><span class="eyebrow">YOUR GYM ACTIVITY</span><h2>Attendance history</h2><p>Your latest 30 recorded visits.</p></div><span class="attendance-history-count"><?= count($history) ?> RECORDS</span></div>
  <?php if($history): ?><div class="attendance-table-wrap"><table class="attendance-table"><thead><tr><th>Visit date</th><th>Check in</th><th>Check out</th><th>Duration</th><th>Status</th></tr></thead><tbody>
  <?php foreach($history as $r): $duration=$r['check_out']?max(0,(int)((strtotime($r['check_out'])-strtotime($r['check_in']))/60)):null; ?>
   <tr><td><strong><?= e(date('d M Y',strtotime($r['check_in']))) ?></strong><small><?= e(date('l',strtotime($r['check_in']))) ?></small></td><td><?= e(date('h:i A',strtotime($r['check_in']))) ?></td><td><?= $r['check_out']?e(date('h:i A',strtotime($r['check_out']))):'—' ?></td><td><?= $duration===null?'In progress':($duration>=60?floor($duration/60).'h '.($duration%60).'m':$duration.' min') ?></td><td><span class="attendance-pill <?= $r['check_out']?'complete':'ongoing' ?>"><?= $r['check_out']?'Completed':'Checked in' ?></span></td></tr>
  <?php endforeach; ?></tbody></table></div><?php else: ?><div class="attendance-empty"><span>◷</span><strong>No visits recorded yet</strong><p>Your attendance history will appear here after your first gym check-in.</p></div><?php endif; ?>
 </section>
 <p class="attendance-footnote">Use check-in only when you physically arrive at IRONX, and check out when you leave. If you forget to check out, contact the gym staff to correct your record. One visit per member per day is allowed through this self-service page.</p>
</main></div>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
