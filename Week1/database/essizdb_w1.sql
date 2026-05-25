-- ============================================================
-- ESSIZ BEAUTY HUB — Week 1 Database
-- BIT3208 Advanced Web Design and Development
-- Database: essizdb_w1
-- ============================================================

CREATE DATABASE IF NOT EXISTS essizdb_w1
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE essizdb_w1;

-- TABLE: users
CREATE TABLE IF NOT EXISTS users (
  user_id     INT PRIMARY KEY AUTO_INCREMENT,
  full_name   VARCHAR(100)        NOT NULL,
  email       VARCHAR(150) UNIQUE NOT NULL,
  password    VARCHAR(255)        NOT NULL,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- TABLE: products
CREATE TABLE IF NOT EXISTS products (
  product_id  INT PRIMARY KEY AUTO_INCREMENT,
  name        VARCHAR(150)   NOT NULL,
  category    VARCHAR(80)    NOT NULL,
  price       DECIMAL(10,2)  NOT NULL,
  stock       INT DEFAULT 0,
  created_at  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- SAMPLE DATA: users
INSERT INTO users (full_name, email, password) VALUES
  ('Admin User',    'admin@essizbeautyhub.com',    'admin123'),
  ('Jane Wanjiru',  'janewanjiru254@gmail.com',    'pass1234'),
  ('Aisha Kamau',   'aishakamau.beauty@gmail.com', 'pass1234'),
  ('Grace Muthoni', 'gracemuthoni95@gmail.com',    'pass1234');

-- SAMPLE DATA: products
INSERT INTO products (name, category, price, stock) VALUES
  ('Glow Serum 30ml',       'Skincare',    850.00,  25),
  ('Matte Lipstick - Rose', 'Makeup',      450.00,  40),
  ('Hydrating Moisturizer', 'Skincare',    1200.00, 15),
  ('Castor Hair Oil',       'Haircare',    650.00,  30),
  ('Floral Perfume 50ml',   'Perfumes',    2200.00, 10),
  ('Makeup Brush Set',      'Accessories', 980.00,  20);