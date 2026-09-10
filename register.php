<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$full_name = $email = $phone = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    }

    $full_name = clean($_POST['full_name'] ?? '');
    $email     = strtolower(clean($_POST['email'] ?? ''));
    $phone     = clean($_POST['phone'] ?? '');
    $password  = $_POST['password'] ?? '';
    $confirm   = $_POST['confirm_password'] ?? '';

    // ----- Server-side validation (never trust the client) -----
    if ($full_name === '' || mb_strlen($full_name) < 2) {
        $errors[] = 'Please enter your full name (at least 2 characters).';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    if (!preg_match('/^[0-9+ ()-]{8,20}$/', $phone)) {
        $errors[] = 'Please enter a valid phone number.';
    }
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[0-9]/', $password)) {
        $errors[] = 'Password must be at least 8 characters and include an uppercase letter and a number.';
    }
    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (empty($errors)) {
        // Check for duplicate email
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $errors[] = 'An account with that email already exists. Please log in instead.';
        }
    }

    if (empty($errors)) {
        $hash = password_hash($password, PASSWORD_DEFAULT); // bcrypt, securely salted
        $stmt = $pdo->prepare(
            'INSERT INTO users (full_name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$full_name, $email, $phone, $hash, 'member']);

        $_SESSION['flash_success'] = 'Account created successfully! Please log in.';
        redirect('login.php');
    }
}

$page_title = 'Register | Momo Ghar';
$meta_description = 'Create a free Momo Ghar account to order authentic Nepali food online for pickup or delivery in Sydney.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <div class="form-card">
    <h1>Create your account</h1>
    <p>Register to place orders, track your order history and save your details for faster checkout.</p>

    <?php if ($errors): ?>
      <div class="alert alert-error" role="alert">
        <ul style="margin:0; padding-left:18px;">
          <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="register.php" data-validate novalidate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">

      <div class="form-group">
        <label for="full_name">Full name</label>
        <input type="text" id="full_name" name="full_name" required minlength="2" value="<?= h($full_name) ?>" autocomplete="name">
      </div>

      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required value="<?= h($email) ?>" autocomplete="email">
      </div>

      <div class="form-group">
        <label for="phone">Phone number</label>
        <input type="tel" id="phone" name="phone" required pattern="^[0-9+ ()-]{8,20}$" value="<?= h($phone) ?>" autocomplete="tel">
        <span class="hint">e.g. 0400 000 000</span>
      </div>

      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required minlength="8" autocomplete="new-password" aria-describedby="pwHint">
        <span class="hint" id="pwHint">Minimum 8 characters, including one uppercase letter and one number.</span>
      </div>

      <div class="form-group">
        <label for="confirm_password">Confirm password</label>
        <input type="password" id="confirm_password" name="confirm_password" required minlength="8" autocomplete="new-password">
      </div>

      <button type="submit" class="btn btn-block">Create account</button>
    </form>
    <p style="margin-top:16px;">Already have an account? <a href="login.php">Log in</a></p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
