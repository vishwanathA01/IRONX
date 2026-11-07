<?php
$pageTitle = 'Gallery';
include __DIR__ . '/includes/header.php';
include __DIR__ . '/includes/navbar.php';
$galleryItems = [
  ['Training floor', 'A focused space to build your routine.', 'https://images.unsplash.com/photo-1534438327276-14e5300c3a48?auto=format&fit=crop&w=1200&q=85', 'Modern gym interior with strength equipment'],
  ['Strength training', 'Build strength one deliberate rep at a time.', 'https://images.unsplash.com/photo-1583454110551-21f2fa2afe61?auto=format&fit=crop&w=1000&q=85', 'Athlete lifting weights'],
  ['Personal training', 'Make every session purposeful.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1000&q=85', 'Personal training session'],
  ['Cardio & conditioning', 'Build stamina and everyday energy.', 'https://images.unsplash.com/photo-1538805060514-97d9cc17730c?auto=format&fit=crop&w=1000&q=85', 'Cardio workout'],
  ['Movement & mobility', 'Make space for balance and flexibility.', 'https://images.unsplash.com/photo-1518611012118-696072aa579a?auto=format&fit=crop&w=1000&q=85', 'Mobility and flexibility exercise'],
  ['Training mindset', 'Consistency is where progress begins.', 'https://images.unsplash.com/photo-1517836357463-d25dfeac3438?auto=format&fit=crop&w=1000&q=85', 'Athlete focused during workout'],
  ['Gym equipment', 'Tools to support a well-rounded routine.', 'https://images.unsplash.com/photo-1581009146145-b5ef050c2e1e?auto=format&fit=crop&w=1000&q=85', 'Gym strength equipment'],
  ['Train together', 'Find motivation in a shared fitness space.', 'https://images.unsplash.com/photo-1571019613454-1cb2f99b2d8b?auto=format&fit=crop&w=1000&q=85', 'Group fitness training']
];
?>
<main class="gallery-page">
  <section class="gallery-hero">
    <div class="container"><span class="eyebrow">INSIDE IRONX · TRAINING IN ACTION</span><h1>THE WORK.<br><span>THE ENERGY.</span><br>THE PROGRESS.</h1><p>Explore the spaces, movements and training moments that bring a fitness journey to life. Find your focus, put in the work and keep moving forward.</p><a class="btn btn-light" href="<?= BASE_URL ?>/membership.php">Explore memberships ↗</a></div>
  </section>
  <section class="container gallery-main section">
    <div class="section-heading-row"><div class="section-head"><span class="eyebrow">THE IRONX GALLERY</span><h2>BUILT THROUGH<br><span class="purple-text">EVERY SESSION.</span></h2><p>A visual look at strength training, conditioning, movement and the gym environment.</p></div><span class="gallery-count">08 MOMENTS · ONE MINDSET</span></div>
    <div class="gallery-photo-grid">
      <?php foreach ($galleryItems as $i => $item): ?>
        <figure class="gallery-photo <?= $i === 0 ? 'gallery-photo-featured' : '' ?>"><img src="<?= e($item[2]) ?>" alt="<?= e($item[3]) ?>" loading="lazy"><figcaption><span>0<?= $i + 1 ?> / IRONX</span><h3><?= e($item[0]) ?></h3><p><?= e($item[1]) ?></p></figcaption></figure>
      <?php endforeach; ?>
    </div>
    <div class="gallery-bottom-cta"><div><span class="eyebrow">READY TO GET STARTED?</span><h2>MAKE YOUR NEXT MOVE COUNT.</h2></div><a class="btn btn-dark" href="<?= BASE_URL ?>/auth/register.php">Join IRONX ↗</a></div>
  </section>
</main>
<?php include __DIR__ . '/includes/footer.php'; ?>
