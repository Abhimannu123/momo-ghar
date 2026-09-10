<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $_SESSION['flash_error'] = 'Invalid form submission.';
        redirect('cart.php');
    }

    if (isset($_POST['update_cart'])) {
        foreach ($_POST['quantity'] ?? [] as $item_id => $qty) {
            $item_id = (int) $item_id;
            $qty = max(0, min(20, (int) $qty));
            if (isset($_SESSION['cart'][$item_id])) {
                if ($qty === 0) {
                    unset($_SESSION['cart'][$item_id]);
                } else {
                    $_SESSION['cart'][$item_id]['quantity'] = $qty;
                }
            }
        }
        $_SESSION['flash_success'] = 'Cart updated.';
    } elseif (isset($_POST['remove_item'])) {
        $item_id = (int) $_POST['remove_item'];
        unset($_SESSION['cart'][$item_id]);
        $_SESSION['flash_success'] = 'Item removed from cart.';
    } elseif (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        $_SESSION['flash_success'] = 'Cart cleared.';
    }
    redirect('cart.php');
}

$cart = $_SESSION['cart'];
$subtotal = 0;
foreach ($cart as $item) { $subtotal += $item['price'] * $item['quantity']; }

$page_title = 'Your Cart | Momo Ghar';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">Your Cart</h1>

  <?php if (empty($cart)): ?>
    <div class="empty-state">
      <p>Your cart is empty.</p>
      <a href="menu.php" class="btn">Browse the Menu</a>
    </div>
  <?php else: ?>
    <form method="post" action="cart.php" data-validate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <table class="cart-table" style="margin-bottom:26px;">
        <caption class="visually-hidden">Items in your cart</caption>
        <thead>
          <tr><th scope="col">Item</th><th scope="col">Price</th><th scope="col">Quantity</th><th scope="col">Line Total</th><th scope="col"><span class="visually-hidden">Remove</span></th></tr>
        </thead>
        <tbody>
        <?php foreach ($cart as $id => $item): ?>
          <tr>
            <td><?= h($item['name']) ?></td>
            <td><?= format_price($item['price']) ?></td>
            <td>
              <label for="qty-cart-<?= (int) $id ?>" class="visually-hidden">Quantity for <?= h($item['name']) ?></label>
              <input type="number" id="qty-cart-<?= (int) $id ?>" name="quantity[<?= (int) $id ?>]" value="<?= (int) $item['quantity'] ?>" min="0" max="20" class="qty-input">
            </td>
            <td><?= format_price($item['price'] * $item['quantity']) ?></td>
            <td>
              <button type="submit" formaction="cart.php" name="remove_item" value="<?= (int) $id ?>" class="btn btn-small btn-danger" formnovalidate>Remove</button>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      <div style="display:flex; gap:10px; margin-bottom:30px;">
        <button type="submit" name="update_cart" value="1" class="btn btn-secondary">Update Cart</button>
        <button type="submit" name="clear_cart" value="1" class="btn btn-outline" formnovalidate>Clear Cart</button>
      </div>
    </form>

    <div class="cart-summary">
      <div class="row"><span>Subtotal</span><span><?= format_price($subtotal) ?></span></div>
      <div class="row total"><span>Total</span><span><?= format_price($subtotal) ?></span></div>
      <a href="checkout.php" class="btn btn-block" style="margin-top:16px;">Proceed to Checkout</a>
    </div>
  <?php endif; ?>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
