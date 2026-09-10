<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'About Us | Momo Ghar Nepali Kitchen & Momo Bar, Sydney';
$meta_description = 'Learn about Momo Ghar, a family-run Nepali restaurant in Sydney serving authentic momo, curries and Himalayan specialties since 2019.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">About Momo Ghar</h1>
  <div class="two-col">
    <div>
      <h2>From Kathmandu to Sydney</h2>
      <p>Momo Ghar was founded in 2019 by the Gurung family, who set out to share the true taste of Nepal with their new home city of Sydney. What began as a small stall at local markets has grown into a beloved neighbourhood restaurant in Redfern.</p>
      <p>Every dish on our menu is prepared using traditional recipes and techniques passed down through generations — from the delicate pleats of our hand-folded momo to the slow-cooked spices in our khasi ko masu (goat curry).</p>
      <h2>Our Values</h2>
      <ul>
        <li><strong>Authenticity</strong> — we import key spices directly from Nepal.</li>
        <li><strong>Hospitality</strong> — guided by "atithi devo bhava", the guest is treated as god.</li>
        <li><strong>Community</strong> — we proudly support Sydney's Nepali community and local suppliers.</li>
      </ul>
    </div>
    <div>
      <img src="assets/images/restaurant-interior.jpg" alt="Interior of Momo Ghar restaurant in Redfern, Sydney, decorated with Nepali prayer flags" loading="lazy" style="width:100%; border-radius:10px; box-shadow:var(--shadow);">
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
