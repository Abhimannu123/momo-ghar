<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$errors = [];
$email = '';

// Very small login rate-limiter using the session, to slow brute-force attempts.
if (!isset($_SESSION['login_attempts'])) $_SESSION['login_attempts'] = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    }

    $email = strtolower(clean($_POST['email'] ?? ''));
    $password = $_POST['password'] ?? '';

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors[] = 'Please enter a valid email and password.';
    }

    if (empty($errors)) {
        if ($_SESSION['login_attempts'] >= 6) {
            $errors[] = 'Too many failed attempts. Please wait a moment and try again.';
        } else {
            $stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role FROM users WHERE email = ?');
            $stmt->execute([$email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                // Regenerate session ID on privilege change to prevent session fixation.
                session_regenerate_id(true);
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['login_attempts'] = 0;

                $_SESSION['flash_success'] = 'Welcome back, ' . $user['full_name'] . '!';
                redirect($user['role'] === 'admin' ? 'admin/dashboard.php' : 'index.php');
            } else {
                $_SESSION['login_attempts']++;
                $errors[] = 'Incorrect email or password.';
            }
        }
    }
}

$page_title = 'Login | Momo Ghar';
$meta_description = 'Log in to your Momo Ghar account to order authentic Nepali food online in Sydney.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <div class="form-card">
    <h1>Log in</h1>

    <?php if ($errors): ?>
      <div class="alert alert-error" role="alert">
        <ul style="margin:0; padding-left:18px;">
          <?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" data-validate novalidate>
      <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
      <div class="form-group">
        <label for="email">Email address</label>
        <input type="email" id="email" name="email" required value="<?= h($email) ?>" autocomplete="email">
      </div>
      <div class="form-group">
        <label for="password">Password</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button type="submit" class="btn btn-block">Log in</button>
    </form>
    <p style="margin-top:16px;">Don't have an account? <a href="register.php">Register here</a></p>
    <p class="hint">Demo accounts — Admin: admin@momoghar.com.au / Admin@123 &nbsp;|&nbsp; Member: sita@example.com / Member@123</p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
