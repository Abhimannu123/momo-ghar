# Momo Ghar — Nepali Kitchen & Momo Bar (Sydney)
### ICT726 Web Development — Assignment 4: Dynamic Website

A dynamic, data-driven restaurant ordering website built with PHP 8, MySQL (PDO) and
vanilla HTML5/CSS3/JS — no frameworks required. Built as a group project for the
KOI ICT726 Web Development course.

## 1. Features

- **User authentication** — register, login, logout. Passwords hashed with bcrypt
  (`password_hash` / `password_verify`). Session-based login with session
  regeneration on privilege change and a CSRF token on every form.
- **Role-based access control** — `admin` and `member` roles. Admin-only pages
  (`/admin/*`) are protected by `require_admin()`; checkout/order history pages
  are protected by `require_login()`. Members can only view their own orders.
- **Dynamic, database-driven menu** — categories and menu items are stored in
  MySQL and rendered dynamically; supports filtering by category.
- **Shopping cart & checkout** — session-based cart, add/update/remove items,
  checkout form (pickup or delivery) that validates input and writes an
  `orders` + `order_items` transaction to the database.
- **Admin CRUD** — full Create/Read/Update/Delete for menu items, plus order
  status management (pending → confirmed → preparing → ready → completed).
- **Forms with validation** — registration, login, contact, checkout and the
  admin menu-item form all validate on both the client (JS + HTML5 attributes,
  for a fast, friendly experience) **and** the server (PHP, since client-side
  validation can always be bypassed).
- **Accessibility** — semantic HTML5 landmarks, a skip-to-content link, visible
  focus states, `aria-*` attributes, labelled form fields, and table captions.
- **SEO** — unique `<title>`/meta description per page, semantic heading
  hierarchy, descriptive `alt` text, `robots.txt`, `sitemap.xml`, canonical
  links, and Restaurant structured data (JSON-LD / schema.org).
- **Privacy & security** — see `privacy-policy.php`; prepared statements
  everywhere (no string-concatenated SQL), output escaping (`h()` helper) to
  prevent XSS, honeypot spam trap on the contact form, HttpOnly session
  cookies, and a login attempt limiter.

## 2. File Structure

```
momo-ghar/
├── admin/
│   ├── dashboard.php        Admin stats overview (admin-only)
│   ├── menu-manage.php      List/Delete/Toggle availability (CRUD: R,D)
│   ├── menu-form.php        Add / Edit menu item (CRUD: C,U)
│   └── orders-manage.php    View all orders, update status
├── assets/
│   ├── css/style.css        Single stylesheet, CSS variables, responsive
│   ├── js/script.js         Nav toggle + client-side form validation
│   └── images/              Sample dish photos (compressed JPGs) + favicon
├── config/
│   └── db.php                PDO database connection
├── includes/
│   ├── functions.php        Auth/session helpers, sanitisation, CSRF
│   ├── header.php           Shared <head>, nav, meta/SEO tags
│   └── footer.php           Shared footer + closing tags
├── index.php                 Home page (dynamic featured dishes)
├── menu.php                  Full menu, category filter, add-to-cart
├── cart.php                  View/update/remove cart items
├── checkout.php               Checkout form → creates order (login required)
├── order-confirmation.php    Order receipt (owner or admin only)
├── my-orders.php             Customer's own order history
├── register.php / login.php / logout.php
├── contact.php                Contact form → stored in DB
├── about.php / privacy-policy.php
├── database.sql              Full schema + seed data
├── robots.txt / sitemap.xml  SEO files
└── .htaccess                 Directory listing off, blocks .sql/.md access
```

## 3. Database Schema (see `database.sql` for full DDL)

| Table            | Purpose                                              |
|------------------|-------------------------------------------------------|
| `users`          | Customer & admin accounts, hashed passwords, roles   |
| `categories`     | Menu categories (Momo, Curry Mains, Drinks, ...)     |
| `menu_items`     | Dishes: name, description, price, image, category FK |
| `orders`         | One row per placed order: type, status, total, FK user |
| `order_items`    | Line items per order (menu item snapshot + qty)      |
| `contact_messages` | Messages submitted via the contact form            |

Relationships: `menu_items.category_id → categories.id`,
`orders.user_id → users.id`, `order_items.order_id → orders.id`,
`order_items.menu_item_id → menu_items.id`.

## 4. Setup Instructions

1. Install a local PHP + MySQL stack (XAMPP / MAMP / WAMP) or use a free host
   that supports PHP 8 and MySQL (e.g. InfinityFree).
2. Create the database by importing `database.sql` (via phpMyAdmin or
   `mysql -u root -p < database.sql`).
3. Update the credentials in `config/db.php` (`DB_HOST`, `DB_NAME`, `DB_USER`,
   `DB_PASS`) to match your environment.
4. Copy the `momo-ghar` folder into your server's document root
   (e.g. `htdocs/momo-ghar`) and browse to `http://localhost/momo-ghar/`.
5. Demo accounts (seeded by `database.sql`):
   - **Admin:** `admin@momoghar.com.au` / `Admin@123`
   - **Member:** `sita@example.com` / `Member@123`

## 5. Testing Checklist

- Register a new account → confirm duplicate emails and weak passwords are rejected.
- Log in / log out → confirm session persists across pages and logout clears it.
- Browse menu, filter by category, add items to cart, update quantities, remove items.
- Checkout as a logged-out user → confirm redirect to login (access control).
- Checkout as a logged-in member → confirm order + order_items are saved and total is correct.
- Log in as admin → add/edit/delete a menu item, change an order's status.
- Submit the contact form with an empty message → confirm validation errors display.
- View the site on a narrow (mobile) viewport → confirm the nav collapses and layout reflows.

## 6. Individual Contributions

_To be completed by each group member before submission — replace with your
own names, features developed and Git commit history summary as required by
the assignment brief._

| Student | Features Implemented | Key Files |
|---|---|---|
| Member 1 | e.g. Authentication, session/RBAC, security hardening | `login.php`, `register.php`, `includes/functions.php` |
| Member 2 | e.g. Menu, cart, checkout, order flow | `menu.php`, `cart.php`, `checkout.php`, `order-confirmation.php` |
| Member 3 | e.g. Admin CRUD, SEO, accessibility, styling | `admin/*`, `assets/css/style.css`, SEO meta tags |

## 7. Deviations / Notes

- Product images are lightweight generated placeholders; replace with real,
  compressed photography (WebP/JPG, <150KB) before going live.
- This build uses a session-based cart rather than a database cart table for
  simplicity; it can be swapped for a `cart_items` table without changing the
  overall architecture if persistence across devices is required.
