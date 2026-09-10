-- =========================================================
-- Momo Ghar - Nepali Kitchen & Momo Bar
-- Database Schema
-- ICT726 Assignment 4 - Dynamic Website
-- =========================================================

CREATE DATABASE IF NOT EXISTS momo_ghar CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE momo_ghar;

-- ---------------------------------------------------------
-- Table: users
-- Stores registered customers and admin accounts.
-- Passwords are stored using PHP password_hash() (bcrypt).
-- ---------------------------------------------------------
CREATE TABLE users (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    full_name       VARCHAR(100) NOT NULL,
    email           VARCHAR(150) NOT NULL UNIQUE,
    phone           VARCHAR(20)  NOT NULL,
    password_hash   VARCHAR(255) NOT NULL,
    role            ENUM('admin','member') NOT NULL DEFAULT 'member',
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: categories
-- Menu categories e.g. Momo, Snacks, Curry, Drinks, Desserts
-- ---------------------------------------------------------
CREATE TABLE categories (
    id      INT AUTO_INCREMENT PRIMARY KEY,
    name    VARCHAR(100) NOT NULL,
    slug    VARCHAR(100) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: menu_items
-- ---------------------------------------------------------
CREATE TABLE menu_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    category_id     INT NOT NULL,
    name            VARCHAR(150) NOT NULL,
    description     TEXT,
    price           DECIMAL(6,2) NOT NULL,
    image_url       VARCHAR(255),
    is_vegetarian   TINYINT(1) NOT NULL DEFAULT 0,
    is_available    TINYINT(1) NOT NULL DEFAULT 1,
    created_at      TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: orders
-- ---------------------------------------------------------
CREATE TABLE orders (
    id                  INT AUTO_INCREMENT PRIMARY KEY,
    user_id             INT NOT NULL,
    order_type          ENUM('pickup','delivery') NOT NULL DEFAULT 'pickup',
    delivery_address    VARCHAR(255),
    contact_phone       VARCHAR(20) NOT NULL,
    notes               TEXT,
    status              ENUM('pending','confirmed','preparing','ready','completed','cancelled') NOT NULL DEFAULT 'pending',
    total_amount        DECIMAL(8,2) NOT NULL,
    created_at          TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: order_items (line items of each order)
-- ---------------------------------------------------------
CREATE TABLE order_items (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    order_id        INT NOT NULL,
    menu_item_id    INT NOT NULL,
    item_name       VARCHAR(150) NOT NULL,
    quantity        INT NOT NULL,
    unit_price      DECIMAL(6,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (menu_item_id) REFERENCES menu_items(id)
) ENGINE=InnoDB;

-- ---------------------------------------------------------
-- Table: contact_messages
-- ---------------------------------------------------------
CREATE TABLE contact_messages (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    name        VARCHAR(100) NOT NULL,
    email       VARCHAR(150) NOT NULL,
    subject     VARCHAR(150),
    message     TEXT NOT NULL,
    created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- =========================================================
-- Sample data
-- =========================================================

-- Admin login: admin@momoghar.com.au / Admin@123
-- Member login: sita@example.com    / Member@123
INSERT INTO users (full_name, email, phone, password_hash, role) VALUES
('Admin User', 'admin@momoghar.com.au', '0400000000', '$2b$10$HQ7t1Vs5O/WFk5hDmaNtwuURYfQnamjQgEzxvCMh1to//3rZwOrme', 'admin'),
('Sita Gurung', 'sita@example.com', '0410111222', '$2b$10$CLxsD3LVQhqGdUUD83iYwuV9KLybxVTJtBblHJaZonuHIFtj3/PIm', 'member');

INSERT INTO categories (name, slug) VALUES
('Momo', 'momo'),
('Snacks & Starters', 'snacks-starters'),
('Curry Mains', 'curry-mains'),
('Rice & Noodles', 'rice-noodles'),
('Drinks', 'drinks'),
('Desserts', 'desserts');

INSERT INTO menu_items (category_id, name, description, price, image_url, is_vegetarian, is_available) VALUES
(1, 'Steamed Chicken Momo', 'Traditional Nepali dumplings filled with minced chicken, onion, garlic and Himalayan spices, served with tomato achar.', 14.90, 'assets/images/momo-chicken.jpg', 0, 1),
(1, 'Steamed Vegetable Momo', 'Handmade dumplings filled with a mix of cabbage, carrot, paneer and spices, steamed and served with tomato achar.', 13.90, 'assets/images/momo-veg.jpg', 1, 1),
(1, 'Pan-Fried Buff Momo', 'Kathmandu-style buffalo momo, pan seared for a crispy base, served with spicy sesame achar.', 15.90, 'assets/images/momo-buff.jpg', 0, 1),
(1, 'Jhol Momo', 'Steamed momo served in a warm sesame-peanut soup with Nepali spices.', 16.90, 'assets/images/momo-jhol.jpg', 0, 1),
(2, 'Chicken Chhoila', 'Smoky grilled chicken tossed in mustard oil, timur (Sichuan pepper) and fresh herbs.', 17.90, 'assets/images/chhoila.jpg', 0, 1),
(2, 'Aloo Sadeko', 'Nepali-style spiced potato salad with mustard oil, chilli and coriander.', 10.90, 'assets/images/aloo-sadeko.jpg', 1, 1),
(2, 'Samosa Chaat', 'Crushed samosa topped with chickpea curry, yoghurt, tamarind and mint chutney.', 11.90, 'assets/images/samosa-chaat.jpg', 1, 1),
(3, 'Dal Bhat Set', 'The Nepali staple: steamed rice, lentil soup, mixed vegetable curry, achar and papad.', 21.90, 'assets/images/dal-bhat.jpg', 1, 1),
(3, 'Chicken Curry', 'Slow-cooked bone-in chicken curry with traditional Nepali spices, served with rice.', 19.90, 'assets/images/chicken-curry.jpg', 0, 1),
(3, 'Goat Curry (Khasi ko Masu)', 'Tender goat curry simmered with Nepali spices, a Momo Ghar signature dish.', 24.90, 'assets/images/goat-curry.jpg', 0, 1),
(4, 'Chow Mein', 'Nepali-style stir fried noodles with vegetables, egg and choice of chicken or vegetarian.', 15.90, 'assets/images/chowmein.jpg', 0, 1),
(4, 'Thukpa', 'Warming Himalayan noodle soup with vegetables and your choice of chicken or tofu.', 16.90, 'assets/images/thukpa.jpg', 0, 1),
(5, 'Masala Chiya', 'Traditional Nepali spiced milk tea.', 4.50, 'assets/images/chiya.jpg', 1, 1),
(5, 'Mango Lassi', 'Creamy yoghurt drink blended with mango.', 6.50, 'assets/images/lassi.jpg', 1, 1),
(6, 'Sel Roti with Honey', 'Traditional Nepali rice-flour ring doughnut, lightly sweet, served with honey.', 8.90, 'assets/images/sel-roti.jpg', 1, 1),
(6, 'Kheer', 'Nepali rice pudding with cardamom, cashew and raisins.', 7.90, 'assets/images/kheer.jpg', 1, 1);
