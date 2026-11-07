<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='My workouts';
$userId=(int)current_user()['id'];
$s=$pdo->prepare("SELECT * FROM workout_plans WHERE member_id=? ORDER BY created_at DESC");
$s->execute([$userId]); $assignedPlans=$s->fetchAll();

$exerciseImages=[
 'press'=>'https://images.unsplash.com/photo-1534368270820-9de3d8053204?w=700&q=85',
 'pull'=>'https://images.unsplash.com/photo-1598971639058-fab3c3109a00?w=700&q=85',
 'legs'=>'https://images.unsplash.com/photo-1574680096145-d05b474e2155?w=700&q=85',
 'dumbbell'=>'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?w=700&q=85',
 'cardio'=>'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?w=700&q=85',
 'stretch'=>'https://images.unsplash.com/photo-1518611012118-696072aa579a?w=700&q=85',
 'glutes'=>'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?w=700&q=85',
 'gym'=>'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?w=700&q=85'
];
$men=[
 ['Monday','Chest & Triceps','Chest, triceps','Muscle building',[
 ['Bench Press','3 × 10','90 sec','Chest, triceps','Barbell','Intermediate','press',45],
 ['Incline Dumbbell Press','3 × 12','60 sec','Upper chest','Dumbbells','Beginner','dumbbell',35],
 ['Cable Fly','3 × 12','45 sec','Chest','Cable machine','Beginner','press',25],
 ['Triceps Pushdown','3 × 12','45 sec','Triceps','Cable machine','Beginner','pull',20],
 ['Dips','2 × 10','60 sec','Chest, triceps','Dip station','Intermediate','press',25]
 ]],
 ['Tuesday','Back & Biceps','Back, biceps','Strength',[
 ['Lat Pulldown','3 × 12','60 sec','Lats','Cable machine','Beginner','pull',35],
 ['Seated Cable Row','3 × 12','60 sec','Mid back','Cable machine','Beginner','pull',30],
 ['Deadlift','3 × 8','120 sec','Posterior chain','Barbell','Advanced','legs',55],
 ['Dumbbell Curl','3 × 12','45 sec','Biceps','Dumbbells','Beginner','dumbbell',20],
 ['Hammer Curl','3 × 12','45 sec','Biceps, forearms','Dumbbells','Beginner','dumbbell',20]
 ]],
 ['Wednesday','Legs','Quads, hamstrings, calves','Strength & fitness',[
 ['Squats','3 × 10','90 sec','Quads, glutes','Barbell','Intermediate','legs',50],
 ['Leg Press','3 × 12','75 sec','Quads, glutes','Leg press machine','Beginner','legs',40],
 ['Lunges','3 × 10 each leg','60 sec','Glutes, quads','Bodyweight / dumbbells','Beginner','glutes',35],
 ['Leg Curl','3 × 12','60 sec','Hamstrings','Leg curl machine','Beginner','legs',25],
 ['Calf Raises','3 × 15','45 sec','Calves','Machine / bodyweight','Beginner','legs',15]
 ]],
 ['Thursday','Shoulders & Abs','Shoulders, core','Strength',[
 ['Shoulder Press','3 × 10','75 sec','Deltoids','Dumbbells','Intermediate','dumbbell',30],
 ['Lateral Raises','3 × 12','45 sec','Side delts','Dumbbells','Beginner','dumbbell',18],
 ['Front Raises','3 × 12','45 sec','Front delts','Dumbbells','Beginner','dumbbell',18],
 ['Plank','3 × 30 sec','45 sec','Core','Mat','Beginner','stretch',15],
 ['Crunches','3 × 15','30 sec','Abdominals','Mat','Beginner','stretch',15]
 ]],
 ['Friday','Cardio & Full Body','Full body, cardiovascular','Fat loss',[
 ['Treadmill','15 minutes','—','Cardiovascular','Treadmill','Beginner','cardio',100],
 ['Push-ups','3 × 12','45 sec','Chest, triceps','Bodyweight','Beginner','press',25],
 ['Kettlebell Swings','3 × 12','60 sec','Glutes, posterior chain','Kettlebell','Intermediate','glutes',35],
 ['Burpees','2 × 10','60 sec','Full body','Bodyweight','Advanced','cardio',45]
 ]]
];
$women=[
 ['Monday','Lower Body','Quads, hamstrings, calves','Strength & fitness',[
 ['Squats','3 × 12','90 sec','Quads, glutes','Barbell / bodyweight','Beginner','legs',45],
 ['Leg Press','3 × 12','75 sec','Quads, glutes','Leg press machine','Beginner','legs',40],
 ['Lunges','3 × 10 each leg','60 sec','Glutes, quads','Bodyweight / dumbbells','Beginner','glutes',30],
 ['Leg Extension','3 × 12','45 sec','Quadriceps','Leg extension machine','Beginner','legs',25],
 ['Calf Raises','3 × 15','45 sec','Calves','Machine / bodyweight','Beginner','legs',15]
 ]],
 ['Tuesday','Back & Arms','Back, biceps, triceps','Strength',[
 ['Lat Pulldown','3 × 12','60 sec','Lats','Cable machine','Beginner','pull',35],
 ['Seated Row','3 × 12','60 sec','Mid back','Cable machine','Beginner','pull',30],
 ['Dumbbell Curl','3 × 12','45 sec','Biceps','Dumbbells','Beginner','dumbbell',20],
 ['Triceps Pushdown','3 × 12','45 sec','Triceps','Cable machine','Beginner','pull',20],
 ['Face Pull','3 × 15','45 sec','Rear delts, upper back','Cable machine','Beginner','pull',20]
 ]],
 ['Wednesday','Glutes & Legs','Glutes, hamstrings','Muscle building',[
 ['Hip Thrust','3 × 12','75 sec','Glutes','Bench, barbell','Intermediate','glutes',40],
 ['Glute Bridge','3 × 15','45 sec','Glutes','Mat','Beginner','glutes',20],
 ['Romanian Deadlift','3 × 10','90 sec','Hamstrings, glutes','Dumbbells / barbell','Intermediate','legs',40],
 ['Cable Kickbacks','3 × 12','45 sec','Glutes','Cable machine','Beginner','glutes',20],
 ['Leg Curl','3 × 12','60 sec','Hamstrings','Leg curl machine','Beginner','legs',25]
 ]],
 ['Thursday','Upper Body & Core','Chest, shoulders, core','Strength',[
 ['Dumbbell Press','3 × 12','60 sec','Chest','Dumbbells','Beginner','dumbbell',30],
 ['Shoulder Press','3 × 10','75 sec','Shoulders','Dumbbells','Intermediate','dumbbell',30],
 ['Lateral Raises','3 × 12','45 sec','Side delts','Dumbbells','Beginner','dumbbell',18],
 ['Plank','3 × 30 sec','45 sec','Core','Mat','Beginner','stretch',15],
 ['Bicycle Crunches','3 × 15','30 sec','Abdominals','Mat','Beginner','stretch',18]
 ]],
 ['Friday','Cardio & Full Body','Cardiovascular, full body','Fat loss',[
 ['Treadmill','15 minutes','—','Cardiovascular','Treadmill','Beginner','cardio',100],
 ['Cycling','10 minutes','—','Legs, cardiovascular','Stationary bike','Beginner','cardio',65],
 ['Bodyweight Squats','3 × 15','45 sec','Quads, glutes','Bodyweight','Beginner','legs',25],
 ['Modified Push-ups','3 × 10','45 sec','Chest, triceps','Bodyweight','Beginner','press',18],
 ['Stretching','10 minutes','—','Full body mobility','Mat','Beginner','stretch',15]
 ]]
];
$planData=['men'=>$men,'women'=>$women];
$planJson=json_encode($planData,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT);
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main workouts-page">
 <div class="dash-top"><div><span class="eyebrow">IRONX TRAINING SPACE</span><h1>My Workouts</h1><p class="dash-subtitle">Choose your goal, customize your weekly split and train with clear exercise guidance.</p></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>

 <section class="workout-hero">
  <div><span class="workout-kicker">BUILD YOUR ROUTINE</span><h2>Train smarter.<br><em>Get stronger.</em></h2><p>Explore structured workout plans with exercise photos, target muscles, equipment, sets, reps and rest guidance.</p><div class="workout-hero-tags"><span>Exercise library</span><span>Weekly planner</span><span>Progress tracking</span></div></div>
  <div class="workout-hero-photo" role="img" aria-label="Gym training"></div>
 </section>

 <section class="workout-builder-card">
  <div class="workout-section-head"><div><span class="eyebrow">PERSONALIZE YOUR TRAINING</span><h2>Build your workout plan</h2><p>Select your preferences and generate a weekly training schedule.</p></div><span class="workout-plan-chip">PLAN BUILDER</span></div>
  <div class="workout-preferences">
   <label>Workout collection<select id="planAudience"><option value="men">Men's workout plans</option><option value="women">Women's workout plans</option></select></label>
   <label>Fitness goal<select id="planGoal"><option value="Muscle building">Muscle building</option><option value="Fat loss">Fat loss</option><option value="Strength">Strength</option><option value="General fitness">General fitness</option><option value="Flexibility">Flexibility</option></select></label>
   <label>Fitness level<select id="planLevel"><option>Beginner</option><option selected>Intermediate</option><option>Advanced</option></select></label>
   <label>Days per week<select id="planDays"><option value="3">3 days</option><option value="4">4 days</option><option value="5" selected>5 days</option><option value="6">6 days</option></select></label>
  </div>
  <button class="btn btn-dark" id="generatePlan" type="button">Generate workout plan <span>→</span></button>
  <p class="workout-builder-note" id="builderMessage">Choose your preferences, then generate your plan. The displayed routines are sample templates and can be adjusted by your trainer.</p>
 </section>

 <section class="workout-library-section">
  <div class="workout-section-head"><div><span class="eyebrow">YOUR WEEKLY ROUTINE</span><h2 id="weeklyPlanTitle">Men's 5-day workout plan</h2><p id="weeklyPlanSubtitle">Select a day to explore your exercises and training details.</p></div><span class="workout-plan-chip" id="weeklyPlanChip">5 TRAINING DAYS</span></div>
  <div class="workout-day-tabs" id="workoutDayTabs"></div>
  <div id="workoutDayContent"></div>
 </section>

 <section class="workout-categories">
  <div class="workout-section-head"><div><span class="eyebrow">TRAINING PATHWAYS</span><h2>Explore workout categories</h2><p>Choose a training style that supports your current goal.</p></div></div>
  <div class="workout-category-grid">
   <article><img src="<?= e($exerciseImages['gym']) ?>" alt="Muscle building training" loading="lazy"><div><h3>Muscle Building</h3><p>Progressive resistance training to build strength and muscle over time.</p><span>Strength · Resistance</span></div></article>
   <article><img src="<?= e($exerciseImages['cardio']) ?>" alt="Cardio workout" loading="lazy"><div><h3>Fat Loss</h3><p>Combine manageable cardio with resistance training and recovery.</p><span>Cardio · Full body</span></div></article>
   <article><img src="<?= e($exerciseImages['stretch']) ?>" alt="Yoga and stretching" loading="lazy"><div><h3>Flexibility</h3><p>Mobility, yoga and stretching to support movement quality.</p><span>Mobility · Recovery</span></div></article>
   <article><img src="<?= e($exerciseImages['dumbbell']) ?>" alt="General fitness strength" loading="lazy"><div><h3>General Fitness</h3><p>Build a balanced foundation of strength, endurance and mobility.</p><span>Balanced · All-round</span></div></article>
  </div>
 </section>

 <section class="assigned-workouts"><div class="workout-section-head"><div><span class="eyebrow">PERSONALIZED BY YOUR TRAINER</span><h2>Assigned Workout Plans</h2><p>Plans saved specifically for your member account.</p></div></div>
 <?php if($assignedPlans): ?><div class="assigned-plan-grid"><?php foreach($assignedPlans as $r): ?><article class="assigned-plan-card"><span class="assigned-plan-icon">▤</span><div><h3><?= e($r['title']) ?></h3><p><?= nl2br(e($r['description'])) ?></p><small>Added <?= e(date('d M Y',strtotime($r['created_at']))) ?></small></div></article><?php endforeach; ?></div><?php else: ?><p class="workout-no-plan">No trainer-assigned plans yet. Your generated sample plan is available above while you wait for a personalized plan.</p><?php endif; ?>
 </section>
 <p class="workout-disclaimer">Warm up for 5–10 minutes, select manageable weights and focus on controlled technique. Calories are estimates and vary by body size, intensity and duration. This is a general sample plan, not medical advice. Ask your trainer to adapt exercises to your needs and take rest when required.</p>
</main></div>
<script>
const IRONX_PLANS = <?= $planJson ?>;
const IRONX_IMAGES = <?= json_encode($exerciseImages,JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) ?>;
let activeDay = 0, completedExercises = 0, currentExercises = [];
function buildWorkoutPlan(){
 const audience=document.getElementById('planAudience').value;
 const goal=document.getElementById('planGoal').value;
 const level=document.getElementById('planLevel').value;
 const days=parseInt(document.getElementById('planDays').value,10);
 const all=IRONX_PLANS[audience].slice();
 if(goal==='Flexibility'){
   const flexDay=['Daily mobility','Mobility & Stretching','Full body mobility','Flexibility',[
    ['Cat-Cow Stretch','2 × 10','30 sec','Spine mobility','Mat','Beginner','stretch',8],
    ['Standing Hamstring Stretch','2 × 30 sec','20 sec','Hamstrings','Mat','Beginner','stretch',6],
    ['Hip Flexor Stretch','2 × 30 sec each side','20 sec','Hip flexors','Mat','Beginner','stretch',6],
    ['Child’s Pose','2 × 30 sec','20 sec','Back, hips','Mat','Beginner','stretch',5],
    ['Shoulder Mobility','2 × 12','20 sec','Shoulders','Bodyweight','Beginner','stretch',5]
   ]];
   all.splice(0,all.length,flexDay,flexDay,flexDay,flexDay,flexDay);
 }
 if(days===6 && goal!=='Flexibility'){
   const extra=['Saturday','Cardio & Mobility','Cardiovascular, full body','General fitness',[
    ['Easy Cycling','12 minutes','—','Cardiovascular','Stationary bike','Beginner','cardio',55],
    ['Incline Walk','10 minutes','—','Cardiovascular','Treadmill','Beginner','cardio',60],
    ['Full Body Stretch','8 minutes','—','Full body mobility','Mat','Beginner','stretch',10]
   ]];
   all.push(extra);
 }
 const selected=all.slice(0,days);
 const labels=['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'];
 const schedule=selected.map((day,i)=>({...day,0:labels[i]||day[0]}));
 document.getElementById('weeklyPlanTitle').textContent=(audience==='men'?'Men’s':'Women’s')+' '+days+'-day '+goal.toLowerCase()+' plan';
 document.getElementById('weeklyPlanSubtitle').textContent=level+' level · '+days+' training days · '+goal;
 document.getElementById('weeklyPlanChip').textContent=days+' TRAINING DAYS';
 document.getElementById('builderMessage').textContent='Your '+level.toLowerCase()+' sample schedule is ready. Adjust load and volume with your trainer; this template is not a personalized medical or coaching assessment.';
 const tabs=document.getElementById('workoutDayTabs');
 tabs.innerHTML=schedule.map((d,i)=>`<button type="button" class="workout-day-tab ${i===0?'active':''}" data-day="${i}"><strong>${d[0].slice(0,3)}</strong><small>${d[1]}</small></button>`).join('');
 tabs.querySelectorAll('button').forEach(btn=>btn.addEventListener('click',()=>showWorkoutDay(schedule,Number(btn.dataset.day))));
 showWorkoutDay(schedule,0);
}
function showWorkoutDay(schedule,index){
 activeDay=index;
 document.querySelectorAll('.workout-day-tab').forEach((b,i)=>b.classList.toggle('active',i===index));
 const d=schedule[index]; currentExercises=d[4];
 const level=document.getElementById('planLevel').value;
 document.getElementById('workoutDayContent').innerHTML=`
 <div class="workout-day-title"><div><span class="workout-day-icon">✦</span><div><h3>${d[0]} — ${d[1]}</h3><p>${d[3]} · Focus: ${d[2]}</p></div></div><span class="workout-day-count">${d[4].length} exercises</span></div>
 <div class="workout-exercise-grid">${d[4].map((ex,i)=>`<article class="workout-exercise-card">
  <div class="exercise-photo"><img src="${IRONX_IMAGES[ex[6]]}" alt="${ex[0]}" loading="lazy"><span class="exercise-index">${String(i+1).padStart(2,'0')}</span></div>
  <div class="exercise-card-body"><div class="exercise-card-title"><h3>${ex[0]}</h3><label class="exercise-done"><input type="checkbox" class="exercise-check"> Done</label></div>
   <div class="exercise-prescription"><span><b>${ex[1]}</b><small>Sets / reps</small></span><span><b>${ex[2]}</b><small>Rest</small></span></div>
   <div class="exercise-meta"><span>Target: ${ex[3]}</span><span>Equipment: ${ex[4]}</span><span>Level: ${level}</span></div>
   <div class="exercise-card-footer"><span class="calorie-estimate">~${ex[7]} kcal <small>estimate</small></span><button type="button" class="rest-timer" data-seconds="${parseInt(ex[2])||60}">Start rest timer</button></div>
   <div class="exercise-timer-output" aria-live="polite"></div>
  </div></article>`).join('')}</div>
 <div class="workout-progress-footer"><span id="exerciseProgressText">0 of ${d[4].length} exercises completed</span><div class="exercise-progress-track"><i id="exerciseProgressBar"></i></div></div>
 <div class="workout-coach-note"><strong>Trainer guidance</strong><p>Warm up before your first set. Use a load that lets you complete each rep with good form. Stop if you feel sharp pain, and ask a trainer for an alternative if needed.</p></div>`;
 const checks=document.querySelectorAll('.exercise-check');
 checks.forEach(c=>c.addEventListener('change',updateExerciseProgress));
 document.querySelectorAll('.rest-timer').forEach(btn=>btn.addEventListener('click',()=>startRestTimer(btn)));
 updateExerciseProgress();
}
function updateExerciseProgress(){
 const total=document.querySelectorAll('.exercise-check').length;
 const done=document.querySelectorAll('.exercise-check:checked').length;
 const txt=document.getElementById('exerciseProgressText');
 const bar=document.getElementById('exerciseProgressBar');
 if(txt)txt.textContent=done+' of '+total+' exercises completed';
 if(bar)bar.style.width=(total?done/total*100:0)+'%';
}
function startRestTimer(button){
 if(button.dataset.running==='1')return;
 let seconds=Math.max(5,Number(button.dataset.seconds)||60);
 const output=button.closest('.exercise-card-body').querySelector('.exercise-timer-output');
 button.dataset.running='1';button.disabled=true;
 output.textContent='Rest: '+seconds+'s';
 const timer=setInterval(()=>{seconds--;output.textContent=seconds>0?'Rest: '+seconds+'s':'Rest complete — get ready!';if(seconds<=0){clearInterval(timer);button.dataset.running='0';button.disabled=false;}},1000);
}
document.getElementById('generatePlan').addEventListener('click',buildWorkoutPlan);
buildWorkoutPlan();
</script>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script></body></html>
