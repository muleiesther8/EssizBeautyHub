# Essiz Beauty Hub — Week 5 (Final)
## BIT3208 Advanced Web Design and Development

---

## Week 5 Theme
**Full CRUD, Beauty Routine Builder, Product Reviews, Admin Reviews, Complete Polished System**

---

## New Features This Week

### ✅ Beauty Routine Builder (routine_builder.php)
- Morning and Night routine modes
- Smart product recommendations by skin type
- Select products step by step
- Save routines to database
- Personalized skin tips per skin type

### ✅ Product Detail Page (product_detail.php)
- Full product information
- Star rating system
- Customer reviews with comments
- Submit review (logged-in users only, once per product)
- Ratings auto-update after each review
- Related products section

### ✅ Admin Reviews Management (admin/reviews.php)
- View all customer reviews
- Review stats: total, average rating, 5-star count
- Delete inappropriate reviews
- Rating recalculates automatically after deletion

### ✅ Enhanced Admin Dashboard
- 6 stat cards including reviews and routines
- Real monthly revenue chart from DB
- Real category sales chart from DB
- Real order status chart from DB

### ✅ Enhanced Customer Dashboard
- Total spent amount
- Recent orders preview
- Routine count stat
- Quick link to routine builder

### ✅ Register with Budget
- Low / Medium / High budget selection
- Used for future recommendation engine

---

## Complete File Structure
```
Week5/
├── index.php
├── login.php
├── register.php
├── logout.php
├── dashboard.php
├── products.php
├── product_detail.php      ← NEW
├── routine_builder.php     ← NEW
├── cart.php
├── checkout.php
├── orders.php
├── admin/
│   ├── dashboard.php       ← Enhanced with real charts
│   ├── products.php
│   ├── orders.php
│   ├── users.php
│   └── reviews.php         ← NEW
├── css/style.css           ← Week 5 additions
├── js/main.js
├── includes/
│   ├── db_connect.php
│   └── session.php
├── database/
│   └── essizdb_w6.sql
└── README.md
```

---

## Demo Credentials
| Role | Email | Password |
|---|---|---|
| Admin | admin@essizbeautyhub.com | password |
| Customer | janewanjiru254@gmail.com | password |

---

## GitHub Commit
**`"Full CRUD, routine builder, reviews, admin reviews, complete system — Week 5"`**