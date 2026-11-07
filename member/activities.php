<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='Activity tracker';
$userId=(int)current_user()['id']; $message=''; $error='';
$types=['Strength training','Cardio','Running','Cycling','Yoga & mobility','Sports','Walking','Other'];
if($_SERVER['REQUEST_METHOD']==='POST'){
 verify_csrf();
 $type=trim($_POST['activity_type']??''); $date=$_POST['activity_date']??date('Y-m-d');
 $minutes=(int)($_POST['duration_minutes']??0);
 $calories=($_POST['calories_burned']??'')!==''?(int)$_POST['calories_burned']:null;
 $notes=trim($_POST['notes']??'');
 if(!in_array($type,$types,true)||$minutes<1||$minutes>1440||($calories!==null&&($calories<0||$calories>10000))||$date>date('Y-m-d')||$date<date('Y-m-d',strtotime('-365 days')))
  $error='Check your activity details. Duration must be 1–1440 minutes and the date must be within the last year.';
 else{
  $stmt=$pdo->prepare('INSERT INTO member_activities(member_id,activity_type,activity_date,duration_minutes,calories_burned,notes) VALUES(?,?,?,?,?,?)');
  $stmt->execute([$userId,$type,$date,$minutes,$calories,$notes]);
  $message='Activity saved. Your fitness overview has been updated.';
 }
}
$stmt=$pdo->prepare('SELECT COUNT(*) sessions,COALESCE(SUM(duration_minutes),0) minutes,COALESCE(SUM(calories_burned),0) calories FROM member_activities WHERE member_id=? AND activity_date>=DATE_SUB(CURDATE(),INTERVAL 30 DAY)');
$stmt->execute([$userId]);$summary=$stmt->fetch();
$stmt=$pdo->prepare('SELECT COUNT(*) sessions,COALESCE(SUM(duration_minutes),0) minutes FROM member_activities WHERE member_id=? AND YEARWEEK(activity_date,1)=YEARWEEK(CURDATE(),1)');
$stmt->execute([$userId]);$weekSummary=$stmt->fetch();
$stmt=$pdo->prepare("SELECT activity_date,COUNT(*) sessions,SUM(duration_minutes) minutes,COALESCE(SUM(calories_burned),0) calories FROM member_activities WHERE member_id=? AND activity_date>=DATE_SUB(CURDATE(),INTERVAL 6 DAY) GROUP BY activity_date ORDER BY activity_date");
$stmt->execute([$userId]);$dailyRows=$stmt->fetchAll();
$daily=[];for($i=6;$i>=0;$i--){$d=date('Y-m-d',strtotime("-$i days"));$daily[$d]=['sessions'=>0,'minutes'=>0,'calories'=>0];}
foreach($dailyRows as $r){$daily[$r['activity_date']]=['sessions'=>(int)$r['sessions'],'minutes'=>(int)$r['minutes'],'calories'=>(int)$r['calories']];}
$stmt=$pdo->prepare("SELECT activity_type,COUNT(*) sessions,SUM(duration_minutes) minutes,COALESCE(SUM(calories_burned),0) calories FROM member_activities WHERE member_id=? AND activity_date>=DATE_SUB(CURDATE(),INTERVAL 30 DAY) GROUP BY activity_type ORDER BY minutes DESC");
$stmt->execute([$userId]);$breakdown=$stmt->fetchAll();
$stmt=$pdo->prepare('SELECT * FROM member_activities WHERE member_id=? ORDER BY activity_date DESC,id DESC LIMIT 50');$stmt->execute([$userId]);$rows=$stmt->fetchAll();
$stmt=$pdo->prepare('SELECT COUNT(*) FROM member_activities WHERE member_id=? AND activity_date>=DATE_SUB(CURDATE(),INTERVAL 6 DAY)');$stmt->execute([$userId]);$weeklySessions=(int)$stmt->fetchColumn();
$stmt=$pdo->prepare('SELECT MAX(duration_minutes) FROM member_activities WHERE member_id=?');$stmt->execute([$userId]);$longest=(int)$stmt->fetchColumn();
$dailyJson=json_encode(array_values($daily),JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
$labelsJson=json_encode(array_map(fn($d)=>date('D',strtotime($d)),array_keys($daily)));
include __DIR__.'/../includes/header.php'; ?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main activity-page">
 <div class="dash-top"><div><span class="eyebrow">IRONX FITNESS INTELLIGENCE</span><h1>Activity tracker</h1><p class="dash-subtitle">Understand your training consistency, review your progress and build better habits.</p></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
 <section class="activity-hero"><div><span class="activity-kicker">EVERY REP. EVERY STEP. EVERY SESSION.</span><h2>Your effort,<br><em>made visible.</em></h2><p>Track your workouts, see your weekly activity trend and learn where your time and effort are going.</p><div class="activity-hero-tags"><span>Training insights</span><span>Weekly trends</span><span>Personal history</span></div></div><div class="activity-hero-mark"><span>IRONX</span><b>↗</b></div></section>
 <?php if($message): ?><p class="activity-flash success"><?= e($message) ?></p><?php endif; ?><?php if($error): ?><p class="activity-flash error"><?= e($error) ?></p><?php endif; ?>
 <nav class="activity-tabs" aria-label="Activity sections"><button class="activity-tab active" data-tab="overview" type="button">Overview</button><button class="activity-tab" data-tab="log" type="button">Log activity</button><button class="activity-tab" data-tab="history" type="button">Activity history</button><button class="activity-tab" data-tab="insights" type="button">Insights</button></nav>

 <section class="activity-panel active" data-panel="overview">
  <div class="activity-kpi-grid">
   <article><span class="activity-kpi-icon">◷</span><small>Sessions · 30 days</small><strong><?= (int)$summary['sessions'] ?></strong><p>Activities recorded</p></article>
   <article><span class="activity-kpi-icon">⌁</span><small>Training time · 30 days</small><strong><?= number_format((int)$summary['minutes']) ?><i> min</i></strong><p><?= number_format(round((int)$summary['minutes']/60,1),1) ?> total hours</p></article>
   <article><span class="activity-kpi-icon">✦</span><small>Estimated calories · 30 days</small><strong><?= number_format((int)$summary['calories']) ?></strong><p>Based on your entries</p></article>
   <article><span class="activity-kpi-icon">↗</span><small>Sessions · this week</small><strong><?= (int)$weekSummary['sessions'] ?></strong><p><?= (int)$weekSummary['minutes'] ?> training minutes</p></article>
  </div>
  <div class="activity-overview-grid">
   <article class="activity-chart-card"><div class="activity-card-heading"><div><span class="eyebrow">LAST 7 DAYS</span><h2>Your activity trend</h2><p>Minutes logged per day</p></div><span class="activity-chart-label">MINUTES</span></div>
    <div class="activity-bars" id="activityBars"><?php $maxMinutes=max(1,max(array_column(array_values($daily),'minutes')));foreach($daily as $date=>$data): ?><div class="activity-bar-column"><strong><?= (int)$data['minutes'] ?></strong><div class="activity-bar-track"><i style="height:<?= round($data['minutes']/$maxMinutes*100) ?>%"></i></div><small><?= e(date('D',strtotime($date))) ?></small></div><?php endforeach; ?></div>
   </article>
   <article class="activity-week-card"><span class="eyebrow">WEEKLY CONSISTENCY</span><h2>Show up for yourself</h2><div class="activity-week-number"><strong><?= $weeklySessions ?></strong><span>sessions this week</span></div><div class="activity-week-track"><i style="width:<?= min(100,$weeklySessions/4*100) ?>%"></i></div><p><?= $weeklySessions>=4?'Great consistency. Keep balancing training with recovery.':(4-$weeklySessions).' more sessions to reach the optional 4-session weekly target.' ?></p><small>Suggested target only — choose a frequency that suits your fitness level and recovery.</small><a href="#" class="activity-cta" data-open-tab="log">＋ Log a workout</a></article>
  </div>
  <div class="activity-bottom-grid">
   <article class="activity-insight-card"><span class="eyebrow">YOUR TRAINING MIX</span><h2>Activity breakdown</h2><?php if($breakdown): foreach($breakdown as $b): ?><div class="activity-breakdown-row"><div><strong><?= e($b['activity_type']) ?></strong><small><?= (int)$b['sessions'] ?> sessions · <?= (int)$b['minutes'] ?> min</small></div><b><?= (int)$b['minutes'] ?>m</b></div><?php endforeach;else:?><p class="activity-empty-small">Log activities to see your training mix.</p><?php endif;?></article>
   <article class="activity-insight-card"><span class="eyebrow">PERSONAL BEST</span><h2>Your longest session</h2><div class="activity-personal-best"><strong><?= $longest ?></strong><span>minutes</span></div><p>Your longest single activity duration recorded so far.</p><a href="#" class="activity-cta" data-open-tab="history">View activity history →</a></article>
  </div>
 </section>

 <section class="activity-panel" data-panel="log">
  <article class="activity-form-card"><div class="activity-card-heading"><div><span class="eyebrow">FITNESS JOURNAL</span><h2>Log a new activity</h2><p>Record the session after you complete it. Add only estimates you trust.</p></div><span class="activity-form-icon">＋</span></div>
   <form method="post" class="activity-entry-form"><?= csrf_field() ?>
    <label>Activity type<select name="activity_type" required><option value="">Choose activity</option><?php foreach($types as $t): ?><option><?= e($t) ?></option><?php endforeach; ?></select></label>
    <label>Activity date<input type="date" name="activity_date" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>" min="<?= date('Y-m-d',strtotime('-365 days')) ?>" required></label>
    <label>Duration (minutes)<input type="number" name="duration_minutes" min="1" max="1440" placeholder="45" required></label>
    <label>Calories burned <span>Optional estimate</span><input type="number" name="calories_burned" min="0" max="10000" placeholder="e.g. 250"></label>
    <label class="activity-wide">Session notes <span>Optional</span><textarea name="notes" rows="4" maxlength="500" placeholder="Exercises, distance, pace, how you felt, or what you want to improve..."></textarea></label>
    <div class="activity-wide activity-form-footer"><small>Tip: Log activities consistently to make your trends more useful.</small><button class="btn btn-dark" type="submit">Save activity <span>→</span></button></div>
   </form>
  </article>
 </section>

 <section class="activity-panel" data-panel="history">
  <article class="activity-history-card"><div class="activity-card-heading"><div><span class="eyebrow">YOUR FITNESS JOURNAL</span><h2>Recent activity history</h2><p>Latest 50 saved sessions, newest first.</p></div><span class="activity-history-count"><?= count($rows) ?> ENTRIES</span></div>
   <?php if($rows): ?><div class="activity-history-list"><?php foreach($rows as $r): ?><article class="activity-history-item"><div class="activity-history-date"><strong><?= e(date('d',strtotime($r['activity_date']))) ?></strong><small><?= e(date('M Y',strtotime($r['activity_date']))) ?></small></div><div class="activity-history-detail"><strong><?= e($r['activity_type']) ?></strong><span><?= (int)$r['duration_minutes'] ?> minutes<?= $r['calories_burned']!==null?' · '.(int)$r['calories_burned'].' estimated kcal':'' ?></span><?php if($r['notes']): ?><p><?= e($r['notes']) ?></p><?php endif; ?></div><span class="activity-history-check">✓</span></article><?php endforeach; ?></div><?php else: ?><div class="activity-empty-state"><span>◷</span><strong>Your activity journal is ready</strong><p>Save your first workout to start building your personal fitness history.</p><a href="#" class="activity-cta" data-open-tab="log">Log your first activity →</a></div><?php endif; ?>
  </article>
 </section>

 <section class="activity-panel" data-panel="insights">
  <div class="activity-insight-grid">
   <article class="activity-insight-card"><span class="eyebrow">30-DAY SNAPSHOT</span><h2>Training volume</h2><strong class="activity-big-number"><?= number_format((int)$summary['minutes']) ?><small> minutes</small></strong><p>Time spent on activities you recorded over the last 30 days.</p></article>
   <article class="activity-insight-card"><span class="eyebrow">SESSION RECORD</span><h2>Longest session</h2><strong class="activity-big-number"><?= $longest ?><small> minutes</small></strong><p>Longest single session in your saved activity history.</p></article>
   <article class="activity-insight-card"><span class="eyebrow">TRAINING MIX</span><h2>Different activity types</h2><strong class="activity-big-number"><?= count($breakdown) ?><small> types</small></strong><p>How many activity categories you logged in the last 30 days.</p></article>
  </div>
  <div class="activity-guidance"><span>✦</span><div><strong>Use your data wisely</strong><p>Look for gradual consistency rather than trying to beat every previous session. Include rest days, warm-ups and recovery. Calorie burn is an estimate, not an exact measurement of energy expenditure.</p></div></div>
 </section>
 <p class="activity-disclaimer">Activity records are based on information you enter. Calorie burn estimates vary with body size, intensity and other factors. This tracker is a general fitness journal, not a medical device or a substitute for professional coaching.</p>
</main></div>
<script>
document.querySelectorAll('.activity-tab').forEach(b=>b.addEventListener('click',()=>openActivityTab(b.dataset.tab)));
document.querySelectorAll('[data-open-tab]').forEach(b=>b.addEventListener('click',e=>{e.preventDefault();openActivityTab(b.dataset.openTab)}));
function openActivityTab(name){document.querySelectorAll('.activity-tab').forEach(b=>b.classList.toggle('active',b.dataset.tab===name));document.querySelectorAll('.activity-panel').forEach(p=>p.classList.toggle('active',p.dataset.panel===name));}
</script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
