<?php
require_once __DIR__.'/../config/app.php';
require_role('member');
$pageTitle='Nutrition & Diet';
$userId=(int)current_user()['id'];
$s=$pdo->prepare("SELECT * FROM diet_plans WHERE member_id=? ORDER BY created_at DESC");
$s->execute([$userId]); $assignedDiets=$s->fetchAll();
include __DIR__.'/../includes/header.php';
?>
<div class="dashboard-layout"><?php include __DIR__.'/../includes/sidebar.php'; ?>
<main class="dashboard-main diet-page">
 <div class="dash-top"><div><span class="eyebrow">IRONX NUTRITION SPACE</span><h1>Nutrition & Diet</h1><p class="dash-subtitle">Track your food, understand your calories and build healthier daily habits.</p></div><span class="user-pill"><?= e(current_user()['name']) ?></span></div>
 <section class="diet-hero"><div><span class="diet-kicker">FUEL YOUR PROGRESS</span><h2>Eat well.<br><em>Train stronger.</em></h2><p>One simple place to monitor calories, macros, hydration and your nutrition routine.</p><div class="diet-hero-tags"><span>Calorie tracker</span><span>Macro insights</span><span>Daily habits</span></div></div><div class="diet-hero-image" role="img" aria-label="Healthy balanced meal"></div></section>

 <nav class="diet-tabs" role="tablist" aria-label="Nutrition sections">
  <button class="diet-tab active" data-tab="overview" type="button">Overview</button>
  <button class="diet-tab" data-tab="tracker" type="button">Calorie tracker</button>
  <button class="diet-tab" data-tab="calculator" type="button">Calorie calculator</button>
  <button class="diet-tab" data-tab="meal" type="button">Meal ideas</button>
  <button class="diet-tab" data-tab="hydration" type="button">Hydration</button>
  <button class="diet-tab" data-tab="plans" type="button">My diet plans</button>
 </nav>

 <section class="diet-panel active" id="diet-overview" data-panel="overview">
  <div class="diet-overview-grid">
   <article class="diet-calorie-card"><div class="diet-card-head"><div><span class="eyebrow">TODAY'S INTAKE</span><h2>Daily calorie balance</h2></div><span class="diet-today" id="dietToday"></span></div>
    <div class="diet-calorie-number"><strong id="overviewCalories">0</strong><span>kcal consumed</span></div>
    <div class="diet-goal-track"><i id="overviewCalorieBar"></i></div>
    <div class="diet-goal-foot"><span>Goal: <b id="overviewGoal">2,000</b> kcal</span><span id="overviewRemaining">2,000 kcal remaining</span></div>
    <a class="diet-inline-link" href="#tracker" data-open-tab="tracker">Log food +</a>
   </article>
   <article class="diet-water-card"><span class="eyebrow">DAILY HYDRATION</span><h2>Water tracker</h2><div class="diet-water-amount"><strong id="overviewWater">0</strong><span>/ 8 glasses</span></div><div class="diet-water-drops" id="overviewWaterDrops"></div><button class="diet-secondary-btn" type="button" data-open-tab="hydration">Track water</button></article>
  </div>
  <div class="diet-macro-grid">
   <article><span class="macro-dot protein"></span><small>Protein</small><strong id="overviewProtein">0 g</strong><div><i id="proteinBar"></i></div></article>
   <article><span class="macro-dot carbs"></span><small>Carbs</small><strong id="overviewCarbs">0 g</strong><div><i id="carbsBar"></i></div></article>
   <article><span class="macro-dot fats"></span><small>Fats</small><strong id="overviewFats">0 g</strong><div><i id="fatsBar"></i></div></article>
   <article><span class="macro-dot fiber"></span><small>Fiber</small><strong id="overviewFiber">0 g</strong><div><i id="fiberBar"></i></div></article>
  </div>
  <div class="diet-tip-grid"><article><span>🥗</span><div><strong>Build balanced meals</strong><p>Include protein, vegetables or fruit, whole grains and healthy fats.</p></div></article><article><span>⚖️</span><div><strong>Focus on consistency</strong><p>Use calorie estimates as a guide, not a score. Daily needs vary between people.</p></div></article><article><span>💧</span><div><strong>Stay hydrated</strong><p>Drink regularly throughout the day and adjust for heat and exercise.</p></div></article></div>
 </section>

 <section class="diet-panel" id="diet-tracker" data-panel="tracker">
  <div class="diet-section-heading"><div><span class="eyebrow">FOOD JOURNAL</span><h2>Calorie & food tracker</h2><p>Add foods to calculate your daily calories and macros. Your entries stay in this browser.</p></div><span class="diet-date-pill" id="trackerDate"></span></div>
  <div class="diet-tracker-layout">
   <div class="diet-food-form-card"><h3>Add food item</h3><form id="foodForm" class="diet-food-form">
    <label>Food name<input id="foodName" required maxlength="80" placeholder="e.g. 2 boiled eggs"></label>
    <div class="diet-form-row"><label>Calories (kcal)<input id="foodCalories" type="number" min="0" max="5000" required placeholder="150"></label><label>Meal<select id="foodMeal"><option>Breakfast</option><option>Lunch</option><option>Snack</option><option>Dinner</option></select></label></div>
    <div class="diet-form-row diet-macro-inputs"><label>Protein (g)<input id="foodProtein" type="number" min="0" max="500" step=".1" value="0"></label><label>Carbs (g)<input id="foodCarbs" type="number" min="0" max="1000" step=".1" value="0"></label><label>Fats (g)<input id="foodFats" type="number" min="0" max="500" step=".1" value="0"></label><label>Fiber (g)<input id="foodFiber" type="number" min="0" max="200" step=".1" value="0"></label></div>
    <button class="btn btn-dark" type="submit">Add to today's log +</button>
   </form></div>
   <div class="diet-log-card"><div class="diet-log-head"><h3>Today's food log</h3><button type="button" class="diet-text-btn" id="clearFoodLog">Clear today</button></div><div id="foodLogList" class="diet-food-log-list"><p class="diet-empty">No food added yet. Start by logging your first meal.</p></div><div class="diet-log-total"><span>Total consumed</span><strong id="trackerTotal">0 kcal</strong></div></div>
  </div>
 </section>

 <section class="diet-panel" id="diet-calculator" data-panel="calculator">
  <div class="diet-section-heading"><div><span class="eyebrow">PERSONAL NUTRITION ESTIMATE</span><h2>Daily calorie calculator</h2><p>Estimate your resting energy and daily calorie needs using the Mifflin–St Jeor equation.</p></div></div>
  <div class="diet-calculator-layout"><form id="calorieCalcForm" class="diet-calculator-form">
   <div class="diet-form-row"><label>Sex used for equation<select id="calcSex"><option value="male">Male</option><option value="female">Female</option></select></label><label>Age (years)<input id="calcAge" type="number" min="18" max="100" value="25" required></label></div>
   <div class="diet-form-row"><label>Weight (kg)<input id="calcWeight" type="number" min="30" max="300" step=".1" value="70" required></label><label>Height (cm)<input id="calcHeight" type="number" min="120" max="230" value="170" required></label></div>
   <label>Activity level<select id="calcActivity"><option value="1.2">Mostly sitting / little exercise</option><option value="1.375">Light exercise 1–3 days/week</option><option value="1.55" selected>Moderate exercise 3–5 days/week</option><option value="1.725">Hard exercise 6–7 days/week</option><option value="1.9">Very intense training / physical job</option></select></label>
   <label>Goal<select id="calcGoal"><option value="maintain">Maintain weight</option><option value="loss">Gradual fat loss</option><option value="gain">Gradual weight gain</option></select></label>
   <button class="btn btn-dark" type="submit">Calculate my estimate</button>
  </form><div class="diet-calculator-result"><span class="eyebrow">YOUR ESTIMATED DAILY TARGET</span><strong id="calcResult">—</strong><span>kcal / day</span><div class="diet-calc-detail"><p>Resting metabolic rate <b id="calcBmr">—</b></p><p>Estimated maintenance <b id="calcTdee">—</b></p></div><p class="diet-calc-note">This is a rough estimate, not a diagnosis or prescription. Individual needs vary. Avoid aggressive calorie restriction; consult a registered dietitian or healthcare professional for personalized advice.</p><button type="button" class="diet-secondary-btn" id="useCalcGoal">Use as today's calorie goal</button></div></div>
 </section>

 <section class="diet-panel" id="diet-meal" data-panel="meal">
  <div class="diet-section-heading"><div><span class="eyebrow">SIMPLE FOOD INSPIRATION</span><h2>Balanced meal ideas</h2><p>Sample combinations to inspire varied meals. Portions and nutrition values vary by preparation.</p></div></div>
  <div class="diet-meal-grid">
   <article><img src="https://images.unsplash.com/photo-1490645935967-10de6ba17061?w=700&q=85" alt="Healthy breakfast bowl" loading="lazy"><div><span>BREAKFAST</span><h3>Oats & fruit bowl</h3><p>Oats, milk or yogurt, banana, seasonal fruit and nuts.</p><small>Fiber · Carbohydrates · Protein</small></div></article>
   <article><img src="https://images.unsplash.com/photo-1512621776951-a57141f2eefd?w=700&q=85" alt="Fresh balanced salad" loading="lazy"><div><span>LUNCH</span><h3>Balanced grain bowl</h3><p>Rice or whole grains, dal or beans, vegetables and curd.</p><small>Protein · Fiber · Micronutrients</small></div></article>
   <article><img src="https://images.unsplash.com/photo-1547592180-85f173990554?w=700&q=85" alt="Healthy snack" loading="lazy"><div><span>SNACK</span><h3>Yogurt & nuts</h3><p>Plain yogurt with nuts or roasted chana and fresh fruit.</p><small>Protein · Healthy fats</small></div></article>
   <article><img src="https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=700&q=85" alt="Healthy dinner plate" loading="lazy"><div><span>DINNER</span><h3>Protein & vegetables</h3><p>Paneer, tofu, eggs, fish or chicken with vegetables and roti.</p><small>Protein · Fiber · Balanced energy</small></div></article>
  </div>
 </section>

 <section class="diet-panel" id="diet-hydration" data-panel="hydration">
  <div class="diet-section-heading"><div><span class="eyebrow">DAILY WELLNESS</span><h2>Hydration tracker</h2><p>Log glasses of water through the day. Adjust your intake to your needs, activity and climate.</p></div></div>
  <div class="diet-hydration-card"><div class="diet-water-large"><strong id="waterCount">0</strong><span>glasses logged</span></div><div class="diet-water-progress"><i id="waterProgress"></i></div><p id="waterMessage">A little water at a time can help build a consistent habit.</p><div class="diet-water-actions"><button class="btn btn-dark" id="addWater" type="button">＋ Add 1 glass</button><button class="diet-secondary-btn" id="removeWater" type="button">− Remove 1</button><button class="diet-text-btn" id="resetWater" type="button">Reset today</button></div><small>One glass is counted as approximately 250 ml. This is a tracking aid, not a prescribed fluid target.</small></div>
 </section>

 <section class="diet-panel" id="diet-plans" data-panel="plans">
  <div class="diet-section-heading"><div><span class="eyebrow">TRAINER GUIDANCE</span><h2>My assigned diet plans</h2><p>Nutrition plans shared with your member account by your trainer.</p></div></div>
  <?php if($assignedDiets): ?><div class="assigned-plan-grid"><?php foreach($assignedDiets as $r): ?><article class="assigned-plan-card"><span class="assigned-plan-icon">🥗</span><div><h3><?= e($r['title']) ?></h3><p><?= nl2br(e($r['notes'])) ?></p><small>Added <?= e(date('d M Y',strtotime($r['created_at']))) ?></small></div></article><?php endforeach; ?></div><?php else: ?><p class="workout-no-plan">No trainer-assigned diet plans yet. Your trainer can add a nutrition plan for you here.</p><?php endif; ?>
 </section>
 <p class="diet-disclaimer">Nutrition information and calorie calculations are estimates only. This page is a general wellness tool, not medical advice. Your calorie and hydration needs depend on individual circumstances. Consult a registered dietitian or healthcare professional for personalized guidance, especially if you have a medical condition or a history of disordered eating.</p>
</main></div>
<script src="<?= BASE_URL ?>/assets/js/main.js"></script>
<script>
(function(){
 const today=new Date().toISOString().slice(0,10), storageKey='ironxDiet_'+<?= (int)$userId ?>+'_'+today;
 const goalKey='ironxDietGoal_'+<?= (int)$userId ?>, waterKey='ironxWater_'+<?= (int)$userId ?>+'_'+today;
 let state={foods:[],goal:2000};
 try{state=Object.assign(state,JSON.parse(localStorage.getItem(storageKey)||'{}'))}catch(e){}
 let water=Number(localStorage.getItem(waterKey)||0);
 const $=id=>document.getElementById(id), sum=(key)=>state.foods.reduce((a,f)=>a+Number(f[key]||0),0);
 state.goal=Number(localStorage.getItem(goalKey)||state.goal);
 $('dietToday').textContent=new Date().toLocaleDateString(undefined,{day:'numeric',month:'short'});
 $('trackerDate').textContent=new Date().toLocaleDateString(undefined,{day:'numeric',month:'short',year:'numeric'});
 function save(){localStorage.setItem(storageKey,JSON.stringify(state));render()}
 function render(){
  const kcal=sum('calories'),protein=sum('protein'),carbs=sum('carbs'),fats=sum('fats'),fiber=sum('fiber'),remaining=state.goal-kcal;
  $('overviewCalories').textContent=Math.round(kcal);$('overviewGoal').textContent=Math.round(state.goal).toLocaleString();
  $('overviewRemaining').textContent=remaining>=0?Math.round(remaining)+' kcal remaining':Math.round(Math.abs(remaining))+' kcal over goal';
  $('overviewCalorieBar').style.width=Math.min(100,kcal/state.goal*100)+'%';
  [['Protein',protein,120],['Carbs',carbs,220],['Fats',fats,65],['Fiber',fiber,25]].forEach(([name,val,target])=>{$('overview'+name).textContent=Math.round(val)+' g';$(name.toLowerCase()+'Bar').style.width=Math.min(100,val/target*100)+'%'});
  $('trackerTotal').textContent=Math.round(kcal)+' kcal';
  $('foodLogList').innerHTML=state.foods.length?state.foods.map((f,i)=>`<article class="diet-log-item"><div><strong>${escapeHtml(f.name)}</strong><small>${escapeHtml(f.meal)} · P ${f.protein}g · C ${f.carbs}g · F ${f.fats}g</small></div><b>${Math.round(f.calories)} kcal</b><button type="button" data-remove="${i}" aria-label="Remove ${escapeHtml(f.name)}">×</button></article>`).join(''):'<p class="diet-empty">No food added yet. Start by logging your first meal.</p>';
  $('foodLogList').querySelectorAll('[data-remove]').forEach(b=>b.addEventListener('click',()=>{state.foods.splice(Number(b.dataset.remove),1);save()}));
  $('overviewWater').textContent=water;$('waterCount').textContent=water;
  $('overviewWaterDrops').innerHTML=Array.from({length:8},(_,i)=>`<i class="${i<water?'filled':''}"></i>`).join('');
  $('waterProgress').style.width=Math.min(100,water/8*100)+'%';
  $('waterMessage').textContent=water>=8?'You have logged eight glasses today. Adjust your fluid intake to your personal needs.':(8-water)+' more to reach your optional 8-glass tracker marker.';
 }
 function escapeHtml(v){return String(v).replace(/[&<>"']/g,c=>({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]))}
 document.querySelectorAll('.diet-tab').forEach(b=>b.addEventListener('click',()=>openTab(b.dataset.tab)));
 document.querySelectorAll('[data-open-tab]').forEach(b=>b.addEventListener('click',()=>openTab(b.dataset.openTab)));
 function openTab(name){document.querySelectorAll('.diet-tab').forEach(b=>b.classList.toggle('active',b.dataset.tab===name));document.querySelectorAll('.diet-panel').forEach(p=>p.classList.toggle('active',p.dataset.panel===name));}
 $('foodForm').addEventListener('submit',e=>{e.preventDefault();const f={name:$('foodName').value.trim(),calories:Number($('foodCalories').value),meal:$('foodMeal').value,protein:Number($('foodProtein').value||0),carbs:Number($('foodCarbs').value||0),fats:Number($('foodFats').value||0),fiber:Number($('foodFiber').value||0)};if(!f.name||f.calories<0)return;state.foods.push(f);save();e.target.reset();$('foodProtein').value=$('foodCarbs').value=$('foodFats').value=$('foodFiber').value=0;});
 $('clearFoodLog').addEventListener('click',()=>{if(confirm('Clear all food entries for today?')){state.foods=[];save()}});
 $('calorieCalcForm').addEventListener('submit',e=>{e.preventDefault();const age=Number($('calcAge').value),weight=Number($('calcWeight').value),height=Number($('calcHeight').value),activity=Number($('calcActivity').value),sex=$('calcSex').value;if(age<18||age>100||weight<=0||height<=0)return;const bmr=10*weight+6.25*height-5*age+(sex==='male'?5:-161),tdee=bmr*activity,goal=$('calcGoal').value,target=Math.round(tdee*(goal==='loss'?.85:goal==='gain'?1.1:1));$('calcResult').textContent=target.toLocaleString();$('calcBmr').textContent=Math.round(bmr).toLocaleString()+' kcal';$('calcTdee').textContent=Math.round(tdee).toLocaleString()+' kcal';$('useCalcGoal').dataset.target=target;});
 $('useCalcGoal').addEventListener('click',()=>{const target=Number($('useCalcGoal').dataset.target);if(!target)return;state.goal=target;localStorage.setItem(goalKey,target);render();openTab('overview')});
 $('addWater').addEventListener('click',()=>{water=Math.min(30,water+1);localStorage.setItem(waterKey,water);render()});
 $('removeWater').addEventListener('click',()=>{water=Math.max(0,water-1);localStorage.setItem(waterKey,water);render()});
 $('resetWater').addEventListener('click',()=>{water=0;localStorage.setItem(waterKey,water);render()});
 render();
})();
</script></body></html>
