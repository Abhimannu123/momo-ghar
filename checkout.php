<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

require_login(); // Access control: only logged-in members/admins can check out

if (empty($_SESSION['cart'])) {
    $_SESSION['flash_error'] = 'Your cart is empty. Add some items before checking out.';
    redirect('menu.php');
}

$errors = [];
$order_type = 'pickup';
$delivery_address = '';
$contact_phone = '';
$notes = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    }

    $order_type       = ($_POST['order_type'] ?? '') === 'delivery' ? 'delivery' : 'pickup';
    $delivery_address = clean($_POST['delivery_address'] ?? '');
    $contact_phone    = clean($_POST['contact_phone'] ?? '');
    $notes            = clean($_POST['notes'] ?? '');

    // ----- Server-side validation -----
    if (!preg_match('/^[0-9+ ()-]{8,20}$/', $contact_phone)) {
        $errors[] = 'Please enter a valid contact phone number.';
    }
    if ($order_type === 'delivery' && mb_strlen($delivery_address) < 8) {
        $errors[] = 'Please enter a complete delivery address (minimum 8 characters).';
    }
    if (mb_strlen($notes) > 500) {
        $errors[] = 'Order notes must be under 500 characters.';
    }
    if (empty($_SESSION['cart'])) {
        $errors[] = 'Your cart is empty.';
    }

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $total = 0;
            foreach ($_SESSION['cart'] as $item) {
                $total += $item['price'] * $item['quantity'];
            }

            $stmt = $pdo->prepare(
                'INSERT INTO orders (user_id, order_type, delivery_address, contact_phone, notes, status, total_amount)
                 VALUES (?, ?, ?, ?, ?, ?, ?)'
            );
            $stmt->execute([
                current_user_id(),
                $order_type,
                $order_type === 'delivery' ? $delivery_address : null,
                $contact_phone,
                $notes,
                'pending',
                $total,
            ]);
            $order_id = $pdo->lastInsertId();

            $itemStmt = $pdo->prepare(
                'INSERT INTO order_items (order_id, menu_item_id, item_name, quantity, unit_price) VALUES (?, ?, ?, ?, ?)'
            );
            foreach ($_SESSION['cart'] as $item_id => $item) {
                $itemStmt->execute([$order_id, (int) $item_id, $item['name'], $item['quantity'], $item['price']]);
            }

            $pdo->commit();
            $_SESSION['cart'] = [];
            redirect('order-confirmation.php?order_id=' . $order_id);
        } catch (Exception $e) {
            $pdo->rollBack();
            error_log('Checkout error: ' . $e->getMessage());
            $errors[] = 'Sorry, something went wrong placing your order. Please try again.';
        }
    }
}

$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }

$page_title = 'Checkout | Momo Ghar';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">Checkout</h1>

  <?php if ($errors): ?>
    <div class="alert alert-error" role="alert">
      <ul style="margin:0; padding-left:18px;"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <div class="two-col" style="align-items:flex-start;">
    <div class="form-card" style="max-width:none;">
      <h2>Delivery Details</h2>
      <form method="post" action="checkout.php" data-validate novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

        <fieldset>
          <legend>Order Type</legend>
          <div class="radio-row">
            <label><input type="radio" name="order_type" value="pickup" <?= $order_type === 'pickup' ? 'checked' : '' ?>> Pickup</label>
            <label><input type="radio" name="order_type" value="delivery" <?= $order_type === 'delivery' ? 'checked' : '' ?>> Delivery</label>
          </div>
        </fieldset>

        <div class="form-group" id="addressGroup">
          <label for="delivery_address">Delivery address</label>
          <input type="text" id="delivery_address" name="delivery_address" value="<?= h($delivery_address) ?>" minlength="8">
        </div>

        <div class="form-group">
          <label for="contact_phone">Contact phone</label>
          <input type="tel" id="contact_phone" name="contact_phone" required pattern="^[0-9+ ()-]{8,20}$" value="<?= h($contact_phone) ?>">
        </div>

        <div class="form-group">
          <label for="notes">Order notes (optional)</label>
          <textarea id="notes" name="notes" maxlength="500"><?= h($notes) ?></textarea>
        </div>

        <button type="submit" class="btn btn-block">Place Order</button>
      </form>
    </div>

    <div class="cart-summary">
      <h2>Order Summary</h2>
      <?php foreach ($cart as $item): ?>
        <div class="row"><span><?= h($item['quantity']) ?> × <?= h($item['name']) ?></span><span><?= format_price($item['price'] * $item['quantity']) ?></span></div>
      <?php endforeach; ?>
      <div class="row total"><span>Total</span><span><?= format_price($subtotal) ?></span></div>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
