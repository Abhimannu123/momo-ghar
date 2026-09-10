<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login();

$order_id = (int) ($_GET['order_id'] ?? 0);

// Access control: members may only view their OWN orders; admins can view any.
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$order_id]);
$order = $stmt->fetch();

if (!$order || (!is_admin() && (int) $order['user_id'] !== (int) current_user_id())) {
    $_SESSION['flash_error'] = 'Order not found.';
    redirect('index.php');
}

$itemsStmt = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$itemsStmt->execute([$order_id]);
$order_items = $itemsStmt->fetchAll();

$page_title = 'Order Confirmed | Momo Ghar';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <div class="form-card" style="max-width:640px;">
    <h1>🎉 Thank you! Your order has been placed.</h1>
    <p>Order reference: <strong>#<?= (int) $order['id'] ?></strong> — status: <span class="status-pill status-<?= h($order['status']) ?>"><?= h($order['status']) ?></span></p>
    <table>
      <caption class="visually-hidden">Order items</caption>
      <thead><tr><th scope="col">Item</th><th scope="col">Qty</th><th scope="col">Total</th></tr></thead>
      <tbody>
      <?php foreach ($order_items as $oi): ?>
        <tr><td><?= h($oi['item_name']) ?></td><td><?= (int) $oi['quantity'] ?></td><td><?= format_price($oi['unit_price'] * $oi['quantity']) ?></td></tr>
      <?php endforeach; ?>
      </tbody>
    </table>
    <p style="margin-top:16px;"><strong>Order type:</strong> <?= h(ucfirst($order['order_type'])) ?></p>
    <?php if ($order['delivery_address']): ?><p><strong>Delivery address:</strong> <?= h($order['delivery_address']) ?></p><?php endif; ?>
    <p><strong>Total paid:</strong> <?= format_price($order['total_amount']) ?></p>
    <a href="my-orders.php" class="btn">View My Orders</a>
    <a href="menu.php" class="btn btn-outline">Order More</a>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
