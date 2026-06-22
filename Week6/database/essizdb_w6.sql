-- ============================================================
-- ESSIZ BEAUTY HUB — Week 6 Database
-- BIT3208 Advanced Web Design and Development
-- Database: essizdb_w6
-- ============================================================

CREATE DATABASE IF NOT EXISTS essizdb_w6
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE essizdb_w6;

CREATE TABLE IF NOT EXISTS users (
  user_id        INT PRIMARY KEY AUTO_INCREMENT,
  full_name      VARCHAR(100) NOT NULL,
  email          VARCHAR(150) UNIQUE NOT NULL,
  phone_number   VARCHAR(20),
  password       VARCHAR(255) NOT NULL,
  role           ENUM('admin','customer') DEFAULT 'customer',
  skin_type      VARCHAR(50),
  budget         ENUM('low','medium','high') DEFAULT 'medium',
  bio            TEXT,
  login_attempts INT DEFAULT 0,
  locked_until   DATETIME DEFAULT NULL,
  created_at     TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS products (
  product_id   INT PRIMARY KEY AUTO_INCREMENT,
  name         VARCHAR(150) NOT NULL,
  category     VARCHAR(80) NOT NULL,
  description  TEXT,
  price        DECIMAL(10,2) NOT NULL,
  stock        INT DEFAULT 0,
  skin_type    VARCHAR(80),
  rating       DECIMAL(3,1) DEFAULT 0.0,
  review_count INT DEFAULT 0,
  created_at   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
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
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY unique_wishlist (user_id, product_id),
  FOREIGN KEY (user_id)    REFERENCES users(user_id) ON DELETE CASCADE,
  FOREIGN KEY (product_id) REFERENCES products(product_id) ON DELETE CASCADE
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
  routine_name      VARCHAR(100),
  routine_type      ENUM('morning','night') NOT NULL,
  products_selected TEXT,
  notes             TEXT,
  created_at        TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(user_id)
);

CREATE TABLE IF NOT EXISTS login_attempts (
  attempt_id   INT PRIMARY KEY AUTO_INCREMENT,
  email        VARCHAR(150),
  ip_address   VARCHAR(50),
  attempted_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SAMPLE DATA
INSERT INTO users (full_name, email, phone_number, password, role, skin_type, budget) VALUES
('Admin User',    'admin@essizbeautyhub.com',    '0734756300', 'placeholder', 'admin',    'Normal',      'high'),
('Jane Wanjiru',  'janewanjiru254@gmail.com',    '0714319876', 'placeholder', 'customer', 'Oily',        'medium'),
('Aisha Kamau',   'aishakamau.beauty@gmail.com', '0722124351', 'placeholder', 'customer', 'Dry',         'high'),
('Grace Muthoni', 'gracemuthoni95@gmail.com',    '0735648709', 'placeholder', 'customer', 'Combination', 'low');

INSERT INTO products (name, category, description, price, stock, skin_type, rating, review_count) VALUES
('Glow Serum 30ml',       'Skincare',    'Brightening vitamin C serum for radiant skin',       850.00, 25, 'All',       4.5, 28),
('Matte Lipstick - Rose', 'Makeup',      'Long lasting matte finish lipstick',                 450.00, 40, 'All',       4.2, 15),
('Hydrating Moisturizer', 'Skincare',    'Deep hydration cream for dry skin',                 1200.00, 15, 'Dry',       4.8, 42),
('Castor Hair Oil',       'Haircare',    'Promotes hair growth and shine',                     650.00, 30, 'All',       4.3, 19),
('Floral Perfume 50ml',   'Perfumes',    'Light floral scent for everyday wear',              2200.00, 10, 'All',       4.6, 11),
('Makeup Brush Set',      'Accessories', 'Professional 12 piece brush set',                    980.00, 20, 'All',       4.4, 23),
('Vitamin C Toner',       'Skincare',    'Brightening toner that controls oil',                750.00, 35, 'Oily',      4.7, 31),
('Acne Control Cleanser', 'Skincare',    'Gentle cleanser for acne-prone skin',                550.00,  8, 'Oily',      4.1, 18),
('Nude Lip Gloss',        'Makeup',      'Glossy nude finish lip gloss',                       380.00, 50, 'All',       4.0,  9),
('Argan Hair Serum',      'Haircare',    'Frizz control and shine serum',                      890.00, 22, 'All',       4.5, 14),
('Rose Water Mist',       'Skincare',    'Refreshing hydrating face mist for sensitive skin',  480.00,  5, 'Sensitive', 4.3, 22),
('SPF 50 Sunscreen',      'Skincare',    'Lightweight daily sun protection SPF50',            1100.00, 18, 'All',       4.9, 56),
('Retinol Night Cream',   'Skincare',    'Anti-aging retinol cream for night use',            1800.00, 12, 'Dry',       4.6, 33),
('Micellar Water 200ml',  'Skincare',    'Gentle makeup remover and cleanser',                 420.00, 28, 'Sensitive', 4.4, 17),
('BB Cream SPF30',        'Makeup',      'Tinted moisturizer with sun protection',             780.00, 24, 'All',       4.3, 21),
('Biotin Hair Vitamins',  'Haircare',    'Hair growth supplement vitamins',                    950.00, 16, 'All',       4.2, 13);

INSERT INTO reviews (user_id, product_id, rating, comment) VALUES
(2, 1, 5, 'This serum is amazing! My skin glows so much now.'),
(3, 1, 4, 'Great product, noticed results in 2 weeks.'),
(2, 3, 5, 'Best moisturizer I have ever used for dry skin!'),
(4, 7, 4, 'Really helps control my oily skin throughout the day.'),
(3, 12, 5, 'Must have! Lightweight and doesnt leave white cast.');