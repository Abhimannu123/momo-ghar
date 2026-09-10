<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$stmt = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$stmt->execute([current_user_id()]);
$orders = $stmt->fetchAll();

$page_title = 'My Orders | Momo Ghar';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">My Orders</h1>

  <?php if (empty($orders)): ?>
    <div class="empty-state"><p>You haven't placed any orders yet.</p><a href="menu.php" class="btn">Browse the Menu</a></div>
  <?php else: ?>
    <table>
      <caption class="visually-hidden">Your past orders</caption>
      <thead>
        <tr><th scope="col">Order #</th><th scope="col">Date</th><th scope="col">Type</th><th scope="col">Status</th><th scope="col">Total</th></tr>
      </thead>
      <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><a href="order-confirmation.php?order_id=<?= (int) $o['id'] ?>">#<?= (int) $o['id'] ?></a></td>
          <td><?= h(date('d M Y, g:ia', strtotime($o['created_at']))) ?></td>
          <td><?= h(ucfirst($o['order_type'])) ?></td>
          <td><span class="status-pill status-<?= h($o['status']) ?>"><?= h($o['status']) ?></span></td>
          <td><?= format_price($o['total_amount']) ?></td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
