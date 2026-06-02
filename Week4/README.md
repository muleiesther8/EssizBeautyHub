# Essiz Beauty Hub — Week 4
## BIT3208 Advanced Web Design and Development

---

## Week 4 Theme
**PHP Backend, Authentication, Sessions, Cart & Admin Dashboard**

---

## What Was Done This Week

### ✅ Task 1 — PHP Authentication System
- Real login with `password_verify()` (hashed passwords)
- Real registration with `password_hash()`
- Session management with `$_SESSION`
- Role-based redirect: Admin → admin/dashboard.php, Customer → dashboard.php
- Logout clears session completely

### ✅ Task 2 — Session Protection
- `requireLogin()` — blocks non-logged-in users
- `requireAdmin()` — blocks non-admin users
- `redirectIfLoggedIn()` — redirects already-logged-in users from login/register

### ✅ Task 3 — Shopping Cart (PHP + DB)
- Add to cart (URL parameter: cart.php?add=ID)
- Remove from cart
- Update quantity
- Calculates subtotal, delivery fee, total
- Stored in MySQL cart table per user

### ✅ Task 4 — Checkout + Mpesa Simulation
- Delivery location input
- Payment method: Mpesa or Cash on Delivery
- Mpesa number field (shown conditionally)
- Order saved to orders + order_items tables
- Cart cleared after successful order
- Mpesa STK Push simulation message

### ✅ Task 5 — Customer Dashboard
- Smart recommendations by skin type
- Order count, cart count, wishlist count
- Quick action links

### ✅ Task 6 — Order Tracking
- Order timeline: Pending → Packed → On the way → Delivered
- Visual progress indicator

### ✅ Task 7 — Admin Dashboard
- Stats: products, customers, orders, revenue
- Chart.js: Sales by category (doughnut) + Order status (bar)
- Recent orders table
- Low stock alert

### ✅ Task 8 — Admin CRUD
- Products: Add, Edit, Delete
- Orders: View all, Update status
- Users: View all, Delete customers

---

## Folder Structure
```
Week4/
├── index.php              ← Dynamic homepage (DB products)
├── login.php              ← PHP login + sessions
├── register.php           ← PHP register + password hashing
├── logout.php             ← Session destroy
├── dashboard.php          ← Customer dashboard
├── products.php           ← Products from DB
├── cart.php               ← Real cart (DB)
├── checkout.php           ← Checkout + Mpesa simulation
├── orders.php             ← Customer order tracking
├── admin/
│   ├── dashboard.php      ← Admin stats + charts
│   ├── products.php       ← Product CRUD
│   ├── orders.php         ← Order management
│   └── users.php          ← User management
├── css/style.css
├── js/main.js
├── includes/
│   ├── db_connect.php     ← DB connection (port 3307)
│   └── session.php        ← Session helpers
├── database/
│   └── essizdb_w4.sql
└── README.md
```

---

## Demo Login Credentials
| Role | Email | Password |
|---|---|---|
| Admin | admin@essizbeautyhub.com | password |
| Customer | janewanjiru254@gmail.com | password |

---

## How to Run
1. Import `essizdb_w4.sql` in phpMyAdmin
2. Run the password UPDATE queries in phpMyAdmin SQL tab
3. Visit: `http://localhost/EssizBeautyHub/Week4/login.php`

---

## GitHub Commit
**Commit message:** `"PHP auth, sessions, cart, checkout, admin dashboard — Week 4"`