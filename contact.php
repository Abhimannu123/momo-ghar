<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$errors = [];
$name = $email = $subject = $message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? '')) {
        $errors[] = 'Invalid form submission. Please try again.';
    }
    // Honeypot spam trap - real users never fill this hidden field.
    if (!empty($_POST['website'])) {
        $errors[] = 'Spam detected.';
    }

    $name    = clean($_POST['name'] ?? '');
    $email   = strtolower(clean($_POST['email'] ?? ''));
    $subject = clean($_POST['subject'] ?? '');
    $message = clean($_POST['message'] ?? '');

    if (mb_strlen($name) < 2) $errors[] = 'Please enter your name.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Please enter a valid email address.';
    if (mb_strlen($message) < 10) $errors[] = 'Please enter a message of at least 10 characters.';
    if (mb_strlen($message) > 1000) $errors[] = 'Message must be under 1000 characters.';

    if (empty($errors)) {
        $stmt = $pdo->prepare('INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)');
        $stmt->execute([$name, $email, $subject, $message]);
        $_SESSION['flash_success'] = 'Thank you! Your message has been sent — we will get back to you shortly.';
        redirect('contact.php');
    }
}

$page_title = 'Contact & Reservations | Momo Ghar Sydney';
$meta_description = 'Get in touch with Momo Ghar Nepali restaurant in Sydney for reservations, catering enquiries or feedback.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <h1 class="section-title">Contact Us</h1>
  <p class="section-subtitle">Questions, catering enquiries or feedback — we'd love to hear from you.</p>

  <div class="two-col" style="align-items:flex-start;">
    <div class="form-card" style="max-width:none;">
      <?php if ($errors): ?>
        <div class="alert alert-error" role="alert">
          <ul style="margin:0; padding-left:18px;"><?php foreach ($errors as $e): ?><li><?= h($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>
      <form method="post" action="contact.php" data-validate novalidate>
        <input type="hidden" name="csrf_token" value="<?= h(csrf_token()) ?>">
        <div style="position:absolute; left:-9999px;" aria-hidden="true">
          <label for="website">Leave this field blank</label>
          <input type="text" id="website" name="website" tabindex="-1" autocomplete="off">
        </div>

        <div class="form-group">
          <label for="name">Your name</label>
          <input type="text" id="name" name="name" required minlength="2" value="<?= h($name) ?>">
        </div>
        <div class="form-group">
          <label for="email">Email address</label>
          <input type="email" id="email" name="email" required value="<?= h($email) ?>">
        </div>
        <div class="form-group">
          <label for="subject">Subject</label>
          <input type="text" id="subject" name="subject" value="<?= h($subject) ?>">
        </div>
        <div class="form-group">
          <label for="message">Message</label>
          <textarea id="message" name="message" required minlength="10" maxlength="1000"><?= h($message) ?></textarea>
        </div>
        <button type="submit" class="btn btn-block">Send Message</button>
      </form>
    </div>
    <div>
      <h2>Visit Us</h2>
      <address style="font-style:normal;">
        Momo Ghar — Nepali Kitchen &amp; Momo Bar<br>
        Shop 4, 212 Cleveland St<br>
        Redfern, Sydney NSW 2016<br>
        Phone: <a href="tel:+61290001234">(02) 9000 1234</a><br>
        Email: <a href="mailto:hello@momoghar.com.au">hello@momoghar.com.au</a>
      </address>
      <h3>Opening Hours</h3>
      <p>Tuesday – Sunday: 11:30am – 9:30pm<br>Closed Mondays</p>
    </div>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
