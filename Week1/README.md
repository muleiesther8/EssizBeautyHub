# Essiz Beauty Hub — Week 1
## BIT3208 Advanced Web Design and Development

---

## Week 1 Theme
**Environment Setup, Hello World & Basic Database Connectivity**

---

## What Was Done This Week

### ✅ Task 1 — Installed Localhost Tools
- Installed **XAMPP** (Apache + MySQL + PHP)
- Verified Apache and MySQL services are running
- Accessed localhost via browser at `http://localhost`

### ✅ Task 2 — Tested Localhost
- Confirmed XAMPP dashboard loads
- PHP info page verified via `phpinfo()`

### ✅ Task 3 — Created Hello World Page
- Built `index.php` with PHP outputting:
  `"Hello World! Welcome to Essiz Beauty Hub"`
- Page includes brand identity, status indicators, and feature preview

### ✅ Task 4 — Tested Basic Database Connectivity
- Created database: `essizdb_w1`
- Created tables: `users`, `products`
- Inserted sample data
- Verified connection via `db_test.php`

---

## Folder Structure

```
Week1/
│
├── index.php            ← Main Hello World page
├── db_test.php          ← Database connection test page
│
├── css/
│   └── style.css        ← Main stylesheet
│
├── js/
│   └── main.js          ← JavaScript (DOM manipulation demo)
│
├── includes/
│   └── db_connect.php   ← Reusable DB connection file
│
├── database/
│   └── essizdb_w1.sql   ← Full database schema + sample data
│
└── README.md            ← This file
```

---

## How to Run

1. Copy the `Week1` folder into: `C:\xampp\htdocs\EssizBeautyHub\`
2. Start XAMPP — turn on **Apache** and **MySQL**
3. Open **phpMyAdmin**: `http://localhost/phpmyadmin`
4. Create a new database named `essizdb_w1`
5. Import `database/essizdb_w1.sql`
6. Visit: `http://localhost/EssizBeautyHub/Week1/index.php`
7. Visit: `http://localhost/EssizBeautyHub/Week1/db_test.php` to verify DB

---

## Technologies Used
- PHP 8.x
- MySQL (via XAMPP)
- HTML5
- CSS3
- JavaScript (Vanilla)
- Google Fonts (Cormorant Garamond, DM Sans)

---

## GitHub Commit
**Repository:** `EssizBeautyHub`  
**Folder:** `Week1/`  
**Commit message:** `"Initial setup: XAMPP, Hello World, DB connection — Week 1"`

---

## Logbook Entry

| Detail | Info |
|---|---|
| Week | 1 |
| Course | BIT3208 Advanced Web Design and Development |
| Project | Essiz Beauty Hub |
| Tasks completed | XAMPP setup, Hello World, DB connection, sample data |
| Challenges | Ensuring XAMPP MySQL port not blocked by other services |
| Solution | Changed MySQL port to 3307 in XAMPP config |
| Next week | Wireframes, ERD, GUI planning |

---

*Essiz Beauty Hub — Intelligent Beauty. Campus Confidence.*