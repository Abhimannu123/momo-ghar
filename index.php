<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Dynamic data: pull a handful of featured / popular menu items for the homepage.
$stmt = $pdo->query(
    "SELECT mi.*, c.name AS category_name FROM menu_items mi
     JOIN categories c ON c.id = mi.category_id
     WHERE mi.is_available = 1
     ORDER BY mi.id ASC LIMIT 6"
);
$featured_items = $stmt->fetchAll();

$page_title = 'Momo Ghar — Authentic Nepali Kitchen & Momo Bar | Sydney';
$meta_description = 'Order authentic Nepali momo, dal bhat, curry and thukpa online from Momo Ghar in Sydney. Fast pickup and delivery, fresh handmade dumplings daily.';
require_once __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <h1>Handmade Momo &amp; Himalayan Flavours, Delivered Fresh in Sydney</h1>
    <p>From steaming baskets of momo to slow-cooked dal bhat, Momo Ghar brings authentic Nepali home cooking to your door. Order online for pickup or delivery.</p>
    <a href="menu.php" class="btn">View Our Menu</a>
    <a href="contact.php" class="btn btn-secondary">Book a Table</a>
  </div>
</section>

<section class="section container">
  <h2 class="section-title">Popular Right Now</h2>
  <p class="section-subtitle">Handpicked favourites from our Kathmandu-style kitchen</p>
  <div class="grid">
    <?php foreach ($featured_items as $item): ?>
      <article class="card">
        <img src="<?= h($item['image_url'] ?: 'assets/images/placeholder-food.jpg') ?>" alt="<?= h($item['name']) ?>, a Nepali dish served at Momo Ghar" loading="lazy" width="400" height="190">
        <div class="card-body">
          <h3><?= h($item['name']) ?></h3>
          <p><?= h(mb_strimwidth($item['description'], 0, 90, '…')) ?></p>
          <div class="badge-row">
            <span class="price-tag"><?= format_price($item['price']) ?></span>
            <?php if ($item['is_vegetarian']): ?><span class="veg-badge">Vegetarian</span><?php endif; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
  <p style="text-align:center; margin-top:30px;"><a href="menu.php" class="btn btn-outline">See Full Menu &amp; Order Online</a></p>
</section>

<section class="section" style="background:#fff;">
  <div class="container two-col">
    <div>
      <h2>Our Story</h2>
      <p>Momo Ghar (मोमो घर, meaning "Momo House") was founded by a Nepali family who moved to Sydney and wanted to share the flavours of home — hand-folded momo, slow-simmered curries, and the warmth of Nepali hospitality (<em>atithi devo bhava</em>, "the guest is god").</p>
      <p>Every dumpling is folded by hand daily using recipes passed down through generations in Kathmandu, using authentic Himalayan spices imported specially for our kitchen.</p>
      <a href="about.php" class="btn btn-outline">Read Our Story</a>
    </div>
    <div>
      <img src="assets/images/story.jpg" alt="Chef hand-folding momo dumplings in the Momo Ghar kitchen" loading="lazy" style="width:100%; border-radius:10px; box-shadow:var(--shadow);">
    </div>
  </div>
</section>

<section class="section container" style="text-align:center;">
  <h2>Order Online in 3 Easy Steps</h2>
  <div class="grid" style="margin-top:30px;">
    <a class="card card-link" href="menu.php" aria-label="Step 1: Browse the menu">
      <div class="card-body"><h3>1. Browse the Menu</h3><p>Explore our momo, curries, snacks and drinks — all made fresh to order.</p></div>
    </a>
    <a class="card card-link" href="menu.php" aria-label="Step 2: Add items to your cart">
      <div class="card-body"><h3>2. Add to Cart</h3><p>Choose your favourites and customise your order quantity.</p></div>
    </a>
    <a class="card card-link" href="cart.php" aria-label="Step 3: Choose pickup or delivery and check out">
      <div class="card-body"><h3>3. Pickup or Delivery</h3><p>Check out securely and choose pickup or delivery to your door.</p></div>
    </a>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
