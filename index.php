<?php
$pageTitle = 'Home';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
?>
<main class="home-page">
  <section class="hero home-hero">
    <div class="hero-copy">
      <span class="eyebrow">IRONX FITNESS CLUB · TRAIN WITH PURPOSE</span>
      <h1>BUILT BY<br><span>DISCIPLINE.</span><br>DEFINED BY STRENGTH.</h1>
      <p>Build strength, improve your fitness and create habits that last. IRONX brings purposeful training, practical guidance and progress tracking together in one fitness experience.</p>
      <div class="hero-actions">
        <a class="btn btn-light" href="<?= BASE_URL ?>/membership.php">Explore memberships <span aria-hidden="true">↗</span></a>
        <a class="hero-outline-link" href="<?= BASE_URL ?>/programs.php">Explore training programs <span aria-hidden="true">→</span></a>
      </div>
      <div class="hero-promises" aria-label="IRONX training approach">
        <div><span>01</span><small>Train with purpose</small></div>
        <div><span>02</span><small>Build healthy habits</small></div>
        <div><span>03</span><small>Track your progress</small></div>
      </div>
    </div>
    <div class="hero-image" role="img" aria-label="Athlete training in a modern strength gym"></div>
    <div class="hero-image-caption"><span class="caption-dot"></span> YOUR WORK. YOUR PACE. YOUR PROGRESS.</div>
  </section>

  <section class="home-intro section container">
    <div class="intro-heading">
      <span class="eyebrow">WELCOME TO IRONX</span>
      <h2>MORE THAN A GYM.<br><span class="purple-text">A STRONGER WAY FORWARD.</span></h2>
    </div>
    <div class="intro-copy">
      <p class="intro-lead">Fitness is not a finish line. It is a commitment you make to yourself, one session and one decision at a time.</p>
      <p>At IRONX, our approach brings together structured workouts, coaching support and tools to help you stay consistent. Whether you are beginning your fitness journey or looking to take your training further, make every session count.</p>
      <a class="inline-arrow" href="<?= BASE_URL ?>/about.php">Discover the IRONX approach <span aria-hidden="true">↗</span></a>
    </div>
  </section>

  <section class="home-programs section">
    <div class="container">
      <div class="section-heading-row">
        <div class="section-head"><span class="eyebrow">FIND YOUR FOCUS</span><h2>TRAIN FOR<br><span class="purple-text">WHAT MATTERS.</span></h2><p>Choose a training direction that supports your goals. Start at your level, build steadily and keep showing up.</p></div>
        <a class="btn btn-dark" href="<?= BASE_URL ?>/programs.php">View all programs ↗</a>
      </div>
      <div class="program-cards">
        <article class="program-card">
          <img src="https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=900&q=85" alt="Athlete lifting weights during strength training" loading="lazy">
          <div class="program-card-content"><span>01 / STRENGTH</span><h3>Strength & muscle building</h3><p>Develop strength, improve technique and build a consistent resistance-training routine.</p><a href="<?= BASE_URL ?>/programs.php">Explore strength training <b>↗</b></a></div>
        </article>
        <article class="program-card">
          <img src="https://images.unsplash.com/photo-1538805060514-97d9cc17730c?auto=format&fit=crop&w=900&q=85" alt="Athlete running as part of cardiovascular conditioning" loading="lazy">
          <div class="program-card-content"><span>02 / CONDITIONING</span><h3>Cardio & endurance</h3><p>Work on stamina, cardiovascular fitness and the energy to move through everyday life.</p><a href="<?= BASE_URL ?>/programs.php">Explore conditioning <b>↗</b></a></div>
        </article>
        <article class="program-card">
          <img src="https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=900&q=85" alt="Person practicing mobility and flexibility exercises" loading="lazy">
          <div class="program-card-content"><span>03 / MOBILITY</span><h3>Mobility & movement</h3><p>Make room for flexibility, balance and controlled movement in your training week.</p><a href="<?= BASE_URL ?>/programs.php">Explore mobility <b>↗</b></a></div>
        </article>
      </div>
    </div>
  </section>

  <section class="home-method section container">
    <div class="method-photo-wrap"><img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1200&q=85" alt="Modern gym floor with strength equipment" loading="lazy"><div class="method-photo-label">THE IRONX TRAINING FLOOR</div></div>
    <div class="method-content"><span class="eyebrow">THE IRONX METHOD</span><h2>CONSISTENCY<br>CREATES <span class="purple-text">CHANGE.</span></h2><p class="method-lead">A good fitness routine needs more than motivation. It needs a plan you can follow and a way to see how far you have come.</p>
      <div class="method-steps">
        <div><span>01</span><section><h3>Set a clear goal</h3><p>Define what you want to improve and choose a realistic starting point.</p></section></div>
        <div><span>02</span><section><h3>Follow your plan</h3><p>Use structured workouts and practical guidance to make each session purposeful.</p></section></div>
        <div><span>03</span><section><h3>Review your progress</h3><p>Record activity, monitor milestones and adjust your routine as you develop.</p></section></div>
      </div>
      <a class="btn btn-dark" href="<?= BASE_URL ?>/auth/register.php">Start your IRONX journey ↗</a>
    </div>
  </section>

  <section class="home-benefits section">
    <div class="container">
      <div class="section-head centered-head"><span class="eyebrow">YOUR FITNESS EXPERIENCE</span><h2>EVERYTHING TO HELP<br>YOU <span class="purple-text">MOVE FORWARD.</span></h2><p>Build a balanced routine with support across the different parts of your fitness journey.</p></div>
      <div class="benefit-grid">
        <article><span class="benefit-icon">↗</span><h3>Structured training</h3><p>Follow purposeful workout routines designed around strength, conditioning and movement.</p></article>
        <article><span class="benefit-icon">◎</span><h3>Progress tracking</h3><p>Keep a record of your activities, goals and milestones from your member dashboard.</p></article>
        <article><span class="benefit-icon">＋</span><h3>Coaching support</h3><p>Connect with trainers and use guidance to make your training more focused.</p></article>
        <article><span class="benefit-icon">♡</span><h3>Healthy habits</h3><p>Give attention to recovery, nutrition, consistency and sustainable daily routines.</p></article>
      </div>
    </div>
  </section>

  <section class="home-gallery section container">
    <div class="section-heading-row">
      <div class="section-head"><span class="eyebrow">INSIDE THE IRONX FLOOR</span><h2>SHOW UP.<br>PUT IN THE WORK.</h2><p>Explore the training atmosphere, equipment and energy that make every rep count.</p></div>
      <a class="inline-arrow" href="<?= BASE_URL ?>/gallery.php">View the gallery ↗</a>
    </div>
    <div class="home-photo-grid">
      <figure class="home-photo home-photo-tall"><img src="https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1100&q=85" alt="Modern gym interior and training equipment" loading="lazy"><figcaption>THE TRAINING FLOOR</figcaption></figure>
      <figure class="home-photo"><img src="https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=900&q=85" alt="Athlete performing a strength exercise" loading="lazy"><figcaption>BUILD YOUR STRENGTH</figcaption></figure>
      <figure class="home-photo"><img src="https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=900&q=85" alt="Athlete focused on a workout" loading="lazy"><figcaption>MOVE WITH PURPOSE</figcaption></figure>
    </div>
  </section>

  <section class="home-faq section">
    <div class="container faq-layout"><div class="section-head"><span class="eyebrow">GOOD TO KNOW</span><h2>YOUR QUESTIONS.<br><span class="purple-text">CLEAR ANSWERS.</span></h2><p>New to IRONX? Here are a few useful starting points.</p><a class="inline-arrow" href="<?= BASE_URL ?>/contact.php">Contact the IRONX team ↗</a></div>
      <div class="faq-list">
        <details><summary>Can beginners join IRONX?</summary><p>Yes. You can begin with a level and routine that suits your current ability, then build your training gradually.</p></details>
        <details><summary>How do I choose a membership?</summary><p>Visit the membership page to review the available options and choose the plan that best fits your schedule and goals.</p></details>
        <details><summary>Can I track my fitness activities?</summary><p>Registered members can use the member dashboard to record activities, review goals and follow their progress.</p></details>
        <details><summary>Can I get help with my workout plan?</summary><p>Explore the training programs and trainer information, or contact the IRONX team to discuss the guidance available.</p></details>
      </div>
    </div>
  </section>

  <section class="home-cta"><div class="container cta-inner"><span class="eyebrow">YOUR NEXT CHAPTER STARTS HERE</span><h2>MAKE THE COMMITMENT.<br><span>OWN THE PROGRESS.</span></h2><p>Take the first step towards a stronger, healthier routine. Your journey starts with showing up.</p><div class="hero-actions"><a class="btn btn-light" href="<?= BASE_URL ?>/auth/register.php">Join IRONX ↗</a><a class="cta-contact" href="<?= BASE_URL ?>/contact.php">Talk to our team →</a></div></div></section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
