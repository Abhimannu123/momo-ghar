<?php
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';
$page_title = 'Privacy Notice | Momo Ghar';
$meta_description = 'Read the Momo Ghar privacy notice to understand how we collect, use and protect your personal information.';
require_once __DIR__ . '/includes/header.php';
?>
<div class="container section">
  <div class="form-card" style="max-width:760px;">
    <h1>Privacy Notice</h1>
    <p>Momo Ghar ("we", "us") respects your privacy. This notice explains what personal information we collect through this website, why we collect it, and how we protect it, in line with the Australian Privacy Principles (APPs).</p>

    <h2>What we collect</h2>
    <ul>
      <li><strong>Account details:</strong> full name, email address and phone number when you register.</li>
      <li><strong>Order details:</strong> delivery address, contact phone and order contents when you place an order.</li>
      <li><strong>Contact form details:</strong> name, email and message when you contact us.</li>
    </ul>

    <h2>How we use it</h2>
    <p>We use your information only to: create and manage your account, process and fulfil your orders, respond to enquiries, and improve our service. We do not sell your personal information to third parties.</p>

    <h2>How we protect it</h2>
    <ul>
      <li>Passwords are never stored in plain text — they are hashed using industry-standard bcrypt hashing before being saved to the database.</li>
      <li>All database queries use parameterised (prepared) statements to prevent SQL injection.</li>
      <li>Access to order and account data is restricted by role-based access control — customers can only view their own orders; only staff with an admin account can view all orders.</li>
      <li>Session cookies are configured as HTTP-only to reduce the risk of theft via cross-site scripting.</li>
    </ul>

    <h2>Your rights</h2>
    <p>You may request access to, correction of, or deletion of your personal information at any time by contacting us at <a href="mailto:privacy@momoghar.com.au">privacy@momoghar.com.au</a>.</p>

    <h2>Cookies</h2>
    <p>We use only strictly-necessary session cookies to keep you logged in and to remember the contents of your shopping cart. We do not use third-party tracking or advertising cookies.</p>

    <p><em>Last updated: July 2025.</em></p>
  </div>
</div>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
