<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Handle "Add to cart" form submissions (POST) — works without JS (progressive enhancement)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid form submission. Please try again.';
        redirect('menu.php');
    }
    $item_id  = (int) ($_POST['item_id'] ?? 0);
    $quantity = max(1, min(20, (int) ($_POST['quantity'] ?? 1)));

    $stmt = $pdo->prepare('SELECT id, name, price FROM menu_items WHERE id = ? AND is_available = 1');
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();

    if ($item) {
        if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];
        if (isset($_SESSION['cart'][$item_id])) {
            $_SESSION['cart'][$item_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$item_id] = [
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $quantity,
            ];
        }
        $_SESSION['flash_success'] = $item['name'] . ' added to your cart.';
    } else {
        $_SESSION['flash_error'] = 'Sorry, that item is not available.';
    }
    redirect('menu.php' . (isset($_GET['category']) ? '?category=' . urlencode($_GET['category']) : ''));
}

// Category filter (whitelisted against DB, safe from injection)
$categories = $pdo->query('SELECT * FROM categories ORDER BY name')->fetchAll();
$selected_category = clean($_GET['category'] ?? '');

if ($selected_category !== '') {
    $stmt = $pdo->prepare(
        "SELECT mi.*, c.name AS category_name, c.slug FROM menu_items mi
         JOIN categories c ON c.id = mi.category_id
         WHERE mi.is_available = 1 AND c.slug = ?
         ORDER BY mi.name"
    );
    $stmt->execute([$selected_category]);
} else {
    $stmt = $pdo->query(
        "SELECT mi.*, c.name AS category_name, c.slug FROM menu_items mi
         JOIN categories c ON c.id = mi.category_id
         WHERE mi.is_available = 1
         ORDER BY c.id, mi.name"
    );
}
$items = $stmt->fetchAll();

$page_title = 'Our Menu | Momo Ghar Nepali Kitchen & Momo Bar, Sydney';
$meta_description = 'Browse the full Momo Ghar menu — steamed & fried momo, Nepali curries, thukpa, snacks and desserts. Order online for pickup or delivery in Sydney.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">Our Menu</h1>
  <p class="section-subtitle">Freshly prepared Nepali dishes — filter by category or browse everything below.</p>

  <nav class="filter-bar" aria-label="Menu category filter">
    <a href="menu.php" class="<?= $selected_category === '' ? 'active' : '' ?>">All</a>
    <?php foreach ($categories as $cat): ?>
      <a href="menu.php?category=<?= h($cat['slug']) ?>" class="<?= $selected_category === $cat['slug'] ? 'active' : '' ?>">
        <?= h($cat['name']) ?>
      </a>
    <?php endforeach; ?>
  </nav>

  <?php if (empty($items)): ?>
    <p class="empty-state">No dishes found in this category right now — please check back soon.</p>
  <?php else: ?>
    <div class="grid">
      <?php foreach ($items as $item): ?>
        <article class="card">
          <img src="<?= h($item['image_url'] ?: 'assets/images/placeholder-food.jpg') ?>" alt="<?= h($item['name']) ?>, a Nepali dish served at Momo Ghar" loading="lazy" width="400" height="190">
          <div class="card-body">
            <h3><?= h($item['name']) ?></h3>
            <p><?= h($item['description']) ?></p>
            <div class="badge-row">
              <span class="price-tag"><?= format_price($item['price']) ?></span>
              <span class="veg-badge" style="background:#eef; color:#10316b;"><?= h($item['category_name']) ?></span>
              <?php if ($item['is_vegetarian']): ?><span class="veg-badge">Vegetarian</span><?php endif; ?>
            </div>
            <form method="post" action="menu.php<?= $selected_category ? '?category=' . h($selected_category) : '' ?>" style="display:flex; gap:8px; align-items:center; margin-top:8px;">
              <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
              <input type="hidden" name="item_id" value="<?= (int) $item['id'] ?>">
              <label for="qty-<?= (int) $item['id'] ?>" class="visually-hidden">Quantity for <?= h($item['name']) ?></label>
              <input type="number" id="qty-<?= (int) $item['id'] ?>" name="quantity" value="1" min="1" max="20" class="qty-input" aria-label="Quantity">
              <button type="submit" name="add_to_cart" value="1" class="btn btn-small">Add to Cart</button>
            </form>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
