<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='My progress';
$userId=(int)current_user()['id'];
$msg='';$error='';
if($_SERVER['REQUEST_METHOD']==='POST'){
    verify_csrf();
    $weight=filter_input(INPUT_POST,'weight',FILTER_VALIDATE_FLOAT);
    $height=filter_input(INPUT_POST,'height',FILTER_VALIDATE_FLOAT);
    $notes=trim((string)($_POST['notes']??''));
    if($weight===false||$weight===null||$weight<20||$weight>400||$height===false||$height===null||$height<80||$height>250){
        $error='Enter a valid weight (20–400 kg) and height (80–250 cm).';
    }else{
        $pdo->prepare('INSERT INTO progress_records(member_id,weight_kg,height_cm,notes) VALUES(?,?,?,?)')->execute([$userId,$weight,$height,$notes]);
        $msg='Progress record saved successfully.';
    }
}
$stmt=$pdo->prepare('SELECT * FROM progress_records WHERE member_id=? ORDER BY recorded_at DESC,id DESC');
$stmt->execute([$userId]);$records=$stmt->fetchAll(PDO::FETCH_ASSOC);
$latest=$records[0]??null;$oldest=count($records)>1?$records[count($records)-1]:$latest;
$bmi=null;$weightChange=null;
if($latest){$h=(float)$latest['height_cm']/100;$bmi=$h>0?(float)$latest['weight_kg']/($h*$h):null;$weightChange=(float)$latest['weight_kg']-(float)$oldest['weight_kg'];}
$destinations=[
 ['title'=>'Build muscle & strength','desc'=>'Track body-weight trends while building strength with progressive training.','image'=>'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=900&q=85','tag'=>'STRENGTH'],
 ['title'=>'Fat loss & transformation','desc'=>'Monitor long-term trends alongside your workouts, nutrition and recovery.','image'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=900&q=85','tag'=>'TRANSFORMATION'],
 ['title'=>'Endurance & stamina','desc'=>'Pair your body metrics with consistent cardio and conditioning sessions.','image'=>'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?auto=format&fit=crop&w=900&q=85','tag'=>'ENDURANCE'],
 ['title'=>'Mobility & balanced fitness','desc'=>'Build a sustainable routine with mobility, flexibility and recovery work.','image'=>'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=85','tag'=>'WELLNESS']
];
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main progress-main">
 <div class="dash-top"><div><span class="eyebrow">IRONX WORKSPACE</span><h1><?= e($pageTitle) ?></h1><p class="progress-subtitle">Measure your consistency, understand your trends and keep moving toward your destination.</p></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
 <section class="progress-hero"><div><span class="progress-kicker">YOUR JOURNEY, VISUALISED</span><h2>Every record is a step forward.</h2><p>Log your measurements regularly and use the visual destinations below to stay focused on the result you want to achieve.</p><a href="#record-progress" class="progress-hero-link">Update my progress <span>→</span></a></div><div class="progress-hero-stat"><span>RECORDS LOGGED</span><strong><?= count($records) ?></strong><small>Keep your tracking consistent</small></div></section>
 <?php if($msg): ?><div class="progress-alert success"><?= e($msg) ?></div><?php endif; ?><?php if($error): ?><div class="progress-alert error"><?= e($error) ?></div><?php endif; ?>
 <section class="progress-destination-section"><div class="progress-section-heading"><div><span class="eyebrow">CHOOSE YOUR DIRECTION</span><h2>What are you working toward?</h2></div><p>Use these destinations as inspiration for your fitness journey.</p></div><div class="progress-destination-grid"><?php foreach($destinations as $d): ?><article class="progress-destination-card"><img src="<?= e($d['image']) ?>" alt="<?= e($d['title']) ?>" loading="lazy"><div class="progress-destination-content"><span><?= e($d['tag']) ?></span><h3><?= e($d['title']) ?></h3><p><?= e($d['desc']) ?></p></div></article><?php endforeach; ?></div></section>
 <section class="progress-metric-grid">
  <article class="progress-metric"><span>LATEST WEIGHT</span><strong><?= $latest?e(number_format((float)$latest['weight_kg'],1)).' <small>kg</small>':'—' ?></strong><p><?= $latest?'Last recorded measurement':'Add your first record' ?></p></article>
  <article class="progress-metric"><span>BODY MASS INDEX</span><strong><?= $bmi!==null?e(number_format($bmi,1)):'—' ?></strong><p>Calculated from latest height and weight</p></article>
  <article class="progress-metric"><span>WEIGHT CHANGE</span><strong><?= $weightChange!==null?(($weightChange>0?'+':'').e(number_format($weightChange,1)).' <small>kg</small>'):'—' ?></strong><p><?= count($records)>1?'Latest compared with earliest record':'Add another record to compare' ?></p></article>
  <article class="progress-metric"><span>TRACKING RECORDS</span><strong><?= count($records) ?></strong><p>Total saved measurements</p></article>
 </section>
 <div class="progress-content-grid">
  <section class="progress-panel" id="record-progress"><div class="progress-panel-heading"><div><span class="eyebrow">MEASUREMENT LOG</span><h2>Record your progress</h2></div><span class="progress-panel-icon">＋</span></div><p class="progress-muted">For a useful comparison, measure under similar conditions each time.</p><form method="post" class="progress-entry-form"><?= csrf_field() ?><label>Weight <span>kg</span><input type="number" min="20" max="400" step="0.1" name="weight" placeholder="e.g. 72.5" required></label><label>Height <span>cm</span><input type="number" min="80" max="250" step="0.1" name="height" placeholder="e.g. 175" required></label><label class="progress-notes">Notes <small>Optional</small><textarea name="notes" rows="3" maxlength="1000" placeholder="How are you feeling? Any training or lifestyle changes?"></textarea></label><button class="btn btn-dark" type="submit">Save progress record</button></form></section>
  <section class="progress-panel"><div class="progress-panel-heading"><div><span class="eyebrow">YOUR TREND</span><h2>Weight history</h2></div><span class="progress-panel-icon">↗</span></div><?php if(count($records)>0): $chart=array_reverse(array_slice($records,0,8));$maxWeight=max(array_map(fn($r)=>(float)$r['weight_kg'],$chart));$minWeight=min(array_map(fn($r)=>(float)$r['weight_kg'],$chart));$range=max(1,$maxWeight-$minWeight); ?><div class="progress-chart" aria-label="Weight history chart"><?php foreach($chart as $r):$v=(float)$r['weight_kg'];$heightPct=28+(($v-$minWeight)/$range)*65; ?><div class="progress-chart-column"><strong><?= e(number_format($v,1)) ?></strong><div class="progress-chart-track"><i style="height:<?= (int)$heightPct ?>%"></i></div><small><?= e(date('d M',strtotime($r['recorded_at']))) ?></small></div><?php endforeach; ?></div><p class="progress-muted">Showing up to your latest 8 measurements. Weight is one indicator—not a complete measure of fitness.</p><?php else: ?><div class="progress-empty"><span>📈</span><strong>Your trend will appear here</strong><p>Save your first measurement to start building your personal history.</p></div><?php endif; ?></section>
 </div>
 <section class="progress-panel progress-history-panel"><div class="progress-panel-heading"><div><span class="eyebrow">YOUR JOURNEY</span><h2>Measurement history</h2></div><span class="progress-history-count"><?= count($records) ?> records</span></div><?php if($records): ?><div class="progress-table-wrap"><table class="progress-table"><thead><tr><th>Date</th><th>Weight</th><th>Height</th><th>BMI*</th><th>Notes</th></tr></thead><tbody><?php foreach($records as $r):$rh=(float)$r['height_cm']/100;$rbmi=$rh>0?(float)$r['weight_kg']/($rh*$rh):0; ?><tr><td><?= e(date('d M Y, h:i A',strtotime($r['recorded_at']))) ?></td><td><strong><?= e(number_format((float)$r['weight_kg'],1)) ?> kg</strong></td><td><?= e(number_format((float)$r['height_cm'],1)) ?> cm</td><td><?= e(number_format($rbmi,1)) ?></td><td><?= e($r['notes']?:'—') ?></td></tr><?php endforeach; ?></tbody></table></div><p class="progress-footnote">*BMI is a screening estimate and does not distinguish muscle from fat or replace professional assessment.</p><?php else: ?><div class="progress-empty"><span>📝</span><strong>No measurements yet</strong><p>Use the form above to save your first progress record.</p></div><?php endif; ?></section>
</main></div><script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
