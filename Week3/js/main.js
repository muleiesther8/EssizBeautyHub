// ============================================================
// ESSIZ BEAUTY HUB — Week 3 JavaScript
// BIT3208 Advanced Web Design and Development
// Features: Form Validation, DOM Manipulation, Cart, Filters
// ============================================================

// ============================================================
// 1. NAVBAR — scroll effect + mobile toggle
// ============================================================
document.addEventListener('DOMContentLoaded', function () {

  const navbar   = document.getElementById('navbar');
  const navToggle = document.getElementById('navToggle');
  const navLinks  = document.getElementById('navLinks');

  // Scroll effect
  if (navbar) {
    window.addEventListener('scroll', function () {
      if (window.scrollY > 50) {
        navbar.classList.add('scrolled');
      } else {
        navbar.classList.remove('scrolled');
      }
    });
  }

  // Mobile toggle
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
  }

  // ============================================================
  // 2. CART SYSTEM — add/remove/update (DOM manipulation)
  // ============================================================
  let cart = JSON.parse(localStorage.getItem('essiz_cart') || '[]');
  updateCartBadge();

  // ============================================================
  // 3. LOGIN FORM VALIDATION
  // ============================================================
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let valid = true;

      const email    = document.getElementById('email');
      const password = document.getElementById('password');

      // Clear previous errors
      clearErrors();

      // Validate email
      if (!email.value.trim()) {
        showError('emailError', 'Email address is required');
        email.classList.add('error');
        valid = false;
      } else if (!isValidEmail(email.value)) {
        showError('emailError', 'Please enter a valid email address');
        email.classList.add('error');
        valid = false;
      } else {
        email.classList.add('success');
      }

      // Validate password
      if (!password.value.trim()) {
        showError('passwordError', 'Password is required');
        password.classList.add('error');
        valid = false;
      } else if (password.value.length < 6) {
        showError('passwordError', 'Password must be at least 6 characters');
        password.classList.add('error');
        valid = false;
      } else {
        password.classList.add('success');
      }

      // If valid — show success and submit
      if (valid) {
        const btn = document.getElementById('loginBtn');
        btn.textContent = 'Logging in...';
        btn.style.opacity = '0.8';

        // Week 3: JS validation done — PHP backend in Week 4
        setTimeout(function () {
          loginForm.submit();
        }, 800);
      }
    });
  }

  // ============================================================
  // 4. REGISTER FORM VALIDATION
  // ============================================================
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {

    // Live password strength checker
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
      passwordInput.addEventListener('input', function () {
        checkPasswordStrength(this.value);
      });
    }

    // Confirm password live check
    const confirmInput = document.getElementById('confirm_password');
    if (confirmInput) {
      confirmInput.addEventListener('input', function () {
        const pass = document.getElementById('password').value;
        if (this.value && this.value !== pass) {
          showError('confirmError', 'Passwords do not match');
          this.classList.add('error');
        } else {
          clearError('confirmError');
          this.classList.remove('error');
          if (this.value) this.classList.add('success');
        }
      });
    }

    registerForm.addEventListener('submit', function (e) {
      e.preventDefault();
      let valid = true;
      clearErrors();

      const fullName = document.getElementById('full_name');
      const email    = document.getElementById('email');
      const phone    = document.getElementById('phone');
      const skinType = document.getElementById('skin_type');
      const password = document.getElementById('password');
      const confirm  = document.getElementById('confirm_password');
      const terms    = document.getElementById('terms');

      // Validate full name
      if (!fullName.value.trim()) {
        showError('nameError', 'Full name is required');
        fullName.classList.add('error'); valid = false;
      } else if (fullName.value.trim().length < 3) {
        showError('nameError', 'Name must be at least 3 characters');
        fullName.classList.add('error'); valid = false;
      } else { fullName.classList.add('success'); }

      // Validate email
      if (!email.value.trim()) {
        showError('emailError', 'Email address is required');
        email.classList.add('error'); valid = false;
      } else if (!isValidEmail(email.value)) {
        showError('emailError', 'Please enter a valid email address');
        email.classList.add('error'); valid = false;
      } else { email.classList.add('success'); }

      // Validate phone
      if (!phone.value.trim()) {
        showError('phoneError', 'Phone number is required');
        phone.classList.add('error'); valid = false;
      } else if (!isValidKenyanPhone(phone.value)) {
        showError('phoneError', 'Enter a valid Kenyan number e.g 07XXXXXXXX');
        phone.classList.add('error'); valid = false;
      } else { phone.classList.add('success'); }

      // Validate skin type
      if (!skinType.value) {
        showError('skinError', 'Please select your skin type');
        valid = false;
      }

      // Validate password
      if (!password.value.trim()) {
        showError('passwordError', 'Password is required');
        password.classList.add('error'); valid = false;
      } else if (password.value.length < 8) {
        showError('passwordError', 'Password must be at least 8 characters');
        password.classList.add('error'); valid = false;
      } else { password.classList.add('success'); }

      // Validate confirm password
      if (!confirm.value.trim()) {
        showError('confirmError', 'Please confirm your password');
        confirm.classList.add('error'); valid = false;
      } else if (confirm.value !== password.value) {
        showError('confirmError', 'Passwords do not match');
        confirm.classList.add('error'); valid = false;
      } else { confirm.classList.add('success'); }

      // Validate terms
      if (!terms.checked) {
        showError('termsError', 'You must agree to the terms to continue');
        valid = false;
      }

      if (valid) {
        const btn = document.getElementById('registerBtn');
        btn.textContent = 'Creating Account...';
        btn.style.opacity = '0.8';
        setTimeout(function () { registerForm.submit(); }, 800);
      }
    });
  }

  // ============================================================
  // 5. PRODUCTS PAGE — Search, Filter, Sort
  // ============================================================
  const searchInput  = document.getElementById('searchInput');
  const priceRange   = document.getElementById('priceRange');
  const priceValue   = document.getElementById('priceValue');
  const sortSelect   = document.getElementById('sortSelect');
  const clearFilters = document.getElementById('clearFilters');
  const resetFilters = document.getElementById('resetFilters');
  const filterToggle = document.getElementById('filterToggleBtn');
  const sidebar      = document.getElementById('filtersSidebar');

  if (priceRange && priceValue) {
    priceRange.addEventListener('input', function () {
      priceValue.textContent = 'KES ' + parseInt(this.value).toLocaleString();
      filterProducts();
    });
  }

  if (searchInput) {
    searchInput.addEventListener('input', filterProducts);
  }

  if (sortSelect) {
    sortSelect.addEventListener('change', filterProducts);
  }

  // Category and skin checkboxes
  document.querySelectorAll('input[name="category"], input[name="skin"]').forEach(function (cb) {
    cb.addEventListener('change', filterProducts);
  });

  if (clearFilters) {
    clearFilters.addEventListener('click', resetAllFilters);
  }
  if (resetFilters) {
    resetFilters.addEventListener('click', resetAllFilters);
  }

  // Mobile filter toggle
  if (filterToggle && sidebar) {
    filterToggle.addEventListener('click', function () {
      sidebar.classList.toggle('open');
    });
  }

  // ============================================================
  // 6. PASSWORD TOGGLE
  // ============================================================
  const toggleBtn = document.getElementById('togglePassword');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const input = document.getElementById('password');
      if (input.type === 'password') {
        input.type = 'text';
        this.textContent = '🙈';
      } else {
        input.type = 'password';
        this.textContent = '👁';
      }
    });
  }

  // ============================================================
  // Console greeting
  // ============================================================
  console.log('%c✦ Essiz Beauty Hub', 'color:#C85B7A;font-size:18px;font-weight:bold;');
  console.log('%cWeek 3 — BIT3208 | JS Validation + DOM Manipulation ✓', 'color:#9B84CC;font-size:13px;');

});

// ============================================================
// HELPER FUNCTIONS
// ============================================================

// Email validator
function isValidEmail(email) {
  return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Kenyan phone validator
function isValidKenyanPhone(phone) {
  return /^(07|01)\d{8}$/.test(phone.replace(/\s/g, ''));
}

// Show error message
function showError(id, message) {
  const el = document.getElementById(id);
  if (el) el.textContent = message;
}

// Clear single error
function clearError(id) {
  const el = document.getElementById(id);
  if (el) el.textContent = '';
}

// Clear all errors
function clearErrors() {
  document.querySelectorAll('.error-msg').forEach(function (el) { el.textContent = ''; });
  document.querySelectorAll('input').forEach(function (el) {
    el.classList.remove('error', 'success');
  });
}

// Password strength checker
function checkPasswordStrength(password) {
  const bar  = document.getElementById('strengthBar');
  const text = document.getElementById('strengthText');
  if (!bar || !text) return;

  let strength = 0;
  if (password.length >= 8)                    strength++;
  if (/[A-Z]/.test(password))                  strength++;
  if (/[0-9]/.test(password))                  strength++;
  if (/[^A-Za-z0-9]/.test(password))           strength++;

  const levels = [
    { width: '0%',   color: '',              label: '' },
    { width: '25%',  color: '#E53E3E',       label: 'Weak' },
    { width: '50%',  color: '--warning',     label: 'Fair' },
    { width: '75%',  color: '#9B84CC',       label: 'Good' },
    { width: '100%', color: 'var(--success)',label: 'Strong ✓' },
  ];

  const level = levels[strength];
  bar.style.width     = level.width;
  bar.style.background = strength === 4 ? 'var(--success)' : strength === 3 ? '#9B84CC' : strength === 2 ? '#E6A855' : '#E53E3E';
  text.textContent    = level.label;
  text.style.color    = bar.style.background;
}

// ============================================================
// CART FUNCTIONS
// ============================================================
function addToCart(id, name, price) {
  let cart = JSON.parse(localStorage.getItem('essiz_cart') || '[]');

  const existing = cart.find(function (item) { return item.id === id; });
  if (existing) {
    existing.qty += 1;
  } else {
    cart.push({ id: id, name: name, price: price, qty: 1 });
  }

  localStorage.setItem('essiz_cart', JSON.stringify(cart));
  updateCartBadge();
  showToast('✓ ' + name + ' added to cart!');
}

function updateCartBadge() {
  const cart  = JSON.parse(localStorage.getItem('essiz_cart') || '[]');
  const total = cart.reduce(function (sum, item) { return sum + item.qty; }, 0);
  const badges = document.querySelectorAll('#cartBadge');
  badges.forEach(function (badge) { badge.textContent = total; });
}

// ============================================================
// TOAST NOTIFICATION
// ============================================================
function showToast(message) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(function () { toast.classList.remove('show'); }, 3000);
}

// ============================================================
// PRODUCT FILTER FUNCTION
// ============================================================
function filterProducts() {
  const search    = document.getElementById('searchInput')?.value.toLowerCase() || '';
  const maxPrice  = parseInt(document.getElementById('priceRange')?.value || 5000);
  const sortBy    = document.getElementById('sortSelect')?.value || 'default';

  const categories = Array.from(document.querySelectorAll('input[name="category"]:checked')).map(function (cb) { return cb.value; });
  const skins      = Array.from(document.querySelectorAll('input[name="skin"]:checked')).map(function (cb) { return cb.value; });

  const cards = Array.from(document.querySelectorAll('.product-card'));
  let visible = 0;

  cards.forEach(function (card) {
    const name     = card.dataset.name     || '';
    const category = card.dataset.category || '';
    const price    = parseInt(card.dataset.price || 0);
    const skin     = card.dataset.skin     || '';
    const rating   = parseFloat(card.dataset.rating || 0);

    const matchSearch   = name.includes(search);
    const matchCategory = categories.includes(category);
    const matchPrice    = price <= maxPrice;
    const matchSkin     = skins.includes(skin) || skins.includes('All');

    if (matchSearch && matchCategory && matchPrice && matchSkin) {
      card.style.display = '';
      visible++;
    } else {
      card.style.display = 'none';
    }
  });

  // Sort visible cards
  const grid = document.getElementById('productsGrid');
  if (grid && sortBy !== 'default') {
    const visibleCards = cards.filter(function (c) { return c.style.display !== 'none'; });
    visibleCards.sort(function (a, b) {
      if (sortBy === 'price-low')  return parseInt(a.dataset.price) - parseInt(b.dataset.price);
      if (sortBy === 'price-high') return parseInt(b.dataset.price) - parseInt(a.dataset.price);
      if (sortBy === 'rating')     return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
      return 0;
    });
    visibleCards.forEach(function (card) { grid.appendChild(card); });
  }

  // Update count
  const countEl = document.getElementById('productCount');
  if (countEl) countEl.textContent = visible + ' products found';

  // Show/hide no results
  const noResults = document.getElementById('noResults');
  if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}

function resetAllFilters() {
  document.querySelectorAll('input[name="category"], input[name="skin"]').forEach(function (cb) { cb.checked = true; });
  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';
  const priceRange = document.getElementById('priceRange');
  if (priceRange) { priceRange.value = 5000; }
  const priceValue = document.getElementById('priceValue');
  if (priceValue) priceValue.textContent = 'KES 5,000';
  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) sortSelect.value = 'default';
  filterProducts();
}

// ============================================================
// BUNDLE MODAL
// ============================================================
const bundles = {
  campus: {
    icon: '🎓',
    title: 'Campus Essentials',
    desc: 'Everything you need for your campus beauty routine — affordable and effective.',
    original: 'KES 2,400',
    price: 'KES 1,800',
    save: 'Save 25%',
    items: [
      { icon: '🫧', name: 'Acne Control Cleanser',   detail: 'Gentle daily cleanser',        price: 'KES 550' },
      { icon: '🫙', name: 'Hydrating Moisturizer',   detail: 'Lightweight daily moisturizer', price: 'KES 1,200' },
      { icon: '💋', name: 'Nude Lip Gloss',           detail: 'Glossy everyday lip color',     price: 'KES 380' },
      { icon: '☀️', name: 'SPF 50 Sunscreen',         detail: 'Daily sun protection',          price: 'KES 270 (bonus)' },
    ]
  },
  glow: {
    icon: '✨',
    title: 'Glow Package',
    desc: 'Complete glow routine for radiant, luminous skin. Our most popular bundle!',
    original: 'KES 4,200',
    price: 'KES 3,000',
    save: 'Save 29%',
    items: [
      { icon: '🧴', name: 'Glow Serum 30ml',         detail: 'Brightening vitamin C serum',   price: 'KES 850' },
      { icon: '🧴', name: 'Vitamin C Toner',          detail: 'Brightening prep toner',        price: 'KES 750' },
      { icon: '🫙', name: 'Hydrating Moisturizer',   detail: 'Deep hydration',                price: 'KES 1,200' },
      { icon: '☀️', name: 'SPF 50 Sunscreen',         detail: 'Protect your glow daily',       price: 'KES 1,100' },
    ]
  },
  acne: {
    icon: '🌿',
    title: 'Acne Care Bundle',
    desc: 'Targeted skincare system for acne-prone skin. Clear, calm and balanced skin.',
    original: 'KES 3,100',
    price: 'KES 2,200',
    save: 'Save 29%',
    items: [
      { icon: '🫧', name: 'Acne Control Cleanser',   detail: 'Removes impurities gently',     price: 'KES 550' },
      { icon: '🧴', name: 'Vitamin C Toner',          detail: 'Balances and brightens skin',   price: 'KES 750' },
      { icon: '🌹', name: 'Rose Water Mist',           detail: 'Soothes irritated skin',        price: 'KES 480' },
      { icon: '🫙', name: 'Oil-Free Moisturizer',     detail: 'Hydrates without clogging',     price: 'KES 900' },
    ]
  }
};

function openBundle(key) {
  const bundle  = bundles[key];
  const modal   = document.getElementById('bundleModal');
  if (!bundle || !modal) return;

  document.getElementById('modalIcon').textContent    = bundle.icon;
  document.getElementById('modalTitle').textContent   = bundle.title;
  document.getElementById('modalDesc').textContent    = bundle.desc;
  document.getElementById('modalOriginal').textContent = bundle.original;
  document.getElementById('modalPrice').textContent   = bundle.price;
  document.getElementById('modalSave').textContent    = bundle.save;

  // Build items
  const itemsEl = document.getElementById('modalItems');
  itemsEl.innerHTML = '';
  bundle.items.forEach(function (item) {
    itemsEl.innerHTML += `
      <div class="modal-item">
        <div class="modal-item-icon">${item.icon}</div>
        <div class="modal-item-info">
          <div class="modal-item-name">${item.name}</div>
          <div class="modal-item-detail">${item.detail}</div>
        </div>
        <div class="modal-item-price">${item.price}</div>
      </div>`;
  });

  // Cart button
  document.getElementById('modalCartBtn').onclick = function () {
    showToast('✓ ' + bundle.title + ' added to cart!');
    closeBundle();
  };

  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeBundle() {
  const modal = document.getElementById('bundleModal');
  if (modal) modal.classList.remove('open');
  document.body.style.overflow = '';
}

// Close modal on overlay click or close button
document.addEventListener('DOMContentLoaded', function () {
  const modal     = document.getElementById('bundleModal');
  const closeBtn  = document.getElementById('modalClose');
  if (closeBtn) closeBtn.addEventListener('click', closeBundle);
  if (modal) {
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeBundle();
    });
  }
});