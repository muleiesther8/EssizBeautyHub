-- ============================================================
-- ESSIZ BEAUTY HUB — Week 3 Database
-- BIT3208 Advanced Web Design and Development
-- Database: essizdb_w3
-- Built on Week 2 — same structure, ready for Week 4 backend
-- ============================================================

CREATE DATABASE IF NOT EXISTS essizdb_w3
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE essizdb_w3;

CREATE TABLE IF NOT EXISTS users (
  user_id      INT PRIMARY KEY AUTO_INCREMENT,
  full_name    VARCHAR(100)        NOT NULL,
  email        VARCHAR(150) UNIQUE NOT NULL,
  phone_number VARCHAR(20),
  password     VARCHAR(255)        NOT NULL,
  role         ENUM('admin','customer') DEFAULT 'customer',
  skin_type    VARCHAR(50),
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  product_id  INT PRIMARY KEY AUTO_INCREMENT,
  name        VARCHAR(150)  NOT NULL,
  category    VARCHAR(80)   NOT NULL,
  description TEXT,
  price       DECIMAL(10,2) NOT NULL,
  stock       INT DEFAULT 0,
  image       VARCHAR(255),
  skin_type   VARCHAR(80),
  rating      DECIMAL(3,1) DEFAULT 0.0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cart (
  cart_id    INT PRIMARY KEY AUTO_INCREMENT,
  user_id    INT NOT NULL,
  product_id INT NOT NULL,
  quantity   INT DEFAULT 1,
  FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS orders (
  order_id          INT PRIMARY KEY AUTO_INCREMENT,
  user_id           INT NOT NULL,
  total_amount      DECIMAL(10,2) NOT NULL,
  order_status      ENUM('Pending','Packed','On the way','Delivered') DEFAULT 'Pending',
  payment_method    VARCHAR(50),
  delivery_location VARCHAR(255),
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS order_items (
  item_id    INT PRIMARY KEY AUTO_INCREMENT,
  order_id   INT NOT NULL,
  product_id INT NOT NULL,
  quantity   INT NOT NULL,
  unit_price DECIMAL(10,2) NOT NULL,
  FOREIGN KEY (order_id)   REFERENCES orders(order_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS wishlist (
  wishlist_id INT PRIMARY KEY AUTO_INCREMENT,
  user_id     INT NOT NULL,
  product_id  INT NOT NULL,
  FOREIGN KEY (user_id)    REFERENCES users(user_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS reviews (
  review_id  INT PRIMARY KEY AUTO_INCREMENT,
  user_id    INT NOT NULL,
  product_id INT NOT NULL,
  rating     INT CHECK (rating BETWEEN 1 AND 5),
  comment    TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id)    REFERENCES users(user_id),
  FOREIGN KEY (product_id) REFERENCES products(product_id)
);

CREATE TABLE IF NOT EXISTS beauty_routines (
  routine_id        INT PRIMARY KEY AUTO_INCREMENT,
  user_id           INT NOT NULL,
  routine_type      ENUM('morning','night') NOT NULL,
  products_selected TEXT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- SAMPLE DATA
INSERT INTO users (full_name, email, phone_number, password, role, skin_type) VALUES
  ('Admin User',    'admin@essizbeautyhub.com',    '0700000000', 'admin123', 'admin',    'Normal'),
  ('Jane Wanjiru',  'janewanjiru254@gmail.com',    '0711111111', 'pass1234', 'customer', 'Oily'),
  ('Aisha Kamau',   'aishakamau.beauty@gmail.com', '0722222222', 'pass1234', 'customer', 'Dry'),
  ('Grace Muthoni', 'gracemuthoni95@gmail.com',    '0733333333', 'pass1234', 'customer', 'Combination');

INSERT INTO products (name, category, description, price, stock, skin_type, rating) VALUES
  ('Glow Serum 30ml',       'Skincare',    'Brightening serum for all skin types',  850.00,  25, 'All',       4.5),
  ('Matte Lipstick - Rose', 'Makeup',      'Long lasting matte finish lipstick',    450.00,  40, 'All',       4.2),
  ('Hydrating Moisturizer', 'Skincare',    'Deep hydration for dry skin',           1200.00, 15, 'Dry',       4.8),
  ('Castor Hair Oil',       'Haircare',    'Promotes hair growth and shine',        650.00,  30, 'All',       4.3),
  ('Floral Perfume 50ml',   'Perfumes',    'Light floral scent for everyday wear',  2200.00, 10, 'All',       4.6),
  ('Makeup Brush Set',      'Accessories', 'Professional 12 piece brush set',       980.00,  20, 'All',       4.4),
  ('Vitamin C Toner',       'Skincare',    'Brightening toner with Vitamin C',      750.00,  35, 'Oily',      4.7),
  ('Acne Control Cleanser', 'Skincare',    'Gentle cleanser for acne-prone skin',   550.00,  28, 'Oily/Acne', 4.1),
  ('Nude Lip Gloss',        'Makeup',      'Glossy nude finish lip gloss',          380.00,  50, 'All',       4.0),
  ('Argan Hair Serum',      'Haircare',    'Frizz control and shine serum',         890.00,  22, 'All',       4.5),
  ('Rose Water Mist',       'Skincare',    'Refreshing hydrating face mist',        480.00,  40, 'Sensitive', 4.3),
  ('SPF 50 Sunscreen',      'Skincare',    'Lightweight daily sun protection',      1100.00, 18, 'All',       4.9);