// ============================================================
// ESSIZ BEAUTY HUB — Week 4 JavaScript
// BIT3208 Advanced Web Design and Development
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // === Navbar scroll effect ===
  const navbar = document.getElementById('navbar');
  if (navbar) {
    window.addEventListener('scroll', function () {
      navbar.classList.toggle('scrolled', window.scrollY > 50);
    });
  }

  // === Mobile nav toggle ===
  const navToggle = document.getElementById('navToggle');
  const navLinks  = document.getElementById('navLinks');
  if (navToggle && navLinks) {
    navToggle.addEventListener('click', function () {
      navLinks.classList.toggle('open');
    });
  }

  // === Password toggle ===
  const toggleBtn = document.getElementById('togglePassword');
  if (toggleBtn) {
    toggleBtn.addEventListener('click', function () {
      const input = document.getElementById('password');
      input.type  = input.type === 'password' ? 'text' : 'password';
      this.textContent = input.type === 'password' ? '👁' : '🙈';
    });
  }

  // === Password strength ===
  const passwordInput = document.getElementById('password');
  if (passwordInput) {
    passwordInput.addEventListener('input', function () {
      checkPasswordStrength(this.value);
    });
  }

  // === Confirm password ===
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

  // === Login form JS validation ===
  const loginForm = document.getElementById('loginForm');
  if (loginForm) {
    loginForm.addEventListener('submit', function (e) {
      let valid = true;
      clearErrors();
      const email    = document.getElementById('email');
      const password = document.getElementById('password');
      if (!email.value.trim()) {
        showError('emailError', 'Email is required'); email.classList.add('error'); valid = false;
      } else if (!isValidEmail(email.value)) {
        showError('emailError', 'Enter a valid email'); email.classList.add('error'); valid = false;
      }
      if (!password.value.trim()) {
        showError('passwordError', 'Password is required'); password.classList.add('error'); valid = false;
      }
      if (!valid) e.preventDefault();
    });
  }

  // === Register form JS validation ===
  const registerForm = document.getElementById('registerForm');
  if (registerForm) {
    registerForm.addEventListener('submit', function (e) {
      let valid = true;
      clearErrors();
      const fullName = document.getElementById('full_name');
      const email    = document.getElementById('email');
      const phone    = document.getElementById('phone_number');
      const password = document.getElementById('password');
      const confirm  = document.getElementById('confirm_password');
      const terms    = document.getElementById('terms');

      if (!fullName?.value.trim()) { showError('nameError', 'Full name is required'); valid = false; }
      if (!email?.value.trim() || !isValidEmail(email.value)) { showError('emailError', 'Valid email required'); valid = false; }
      if (!phone?.value.trim() || !isValidKenyanPhone(phone.value)) { showError('phoneError', 'Enter valid Kenyan number e.g 07XXXXXXXX'); valid = false; }
      if (!password?.value || password.value.length < 8) { showError('passwordError', 'Min 8 characters'); valid = false; }
      if (confirm?.value !== password?.value) { showError('confirmError', 'Passwords do not match'); valid = false; }
      if (!terms?.checked) { showError('termsError', 'You must agree to continue'); valid = false; }

      if (!valid) e.preventDefault();
    });
  }

  // === Product filters (products page) ===
  const searchInput  = document.getElementById('searchInput');
  const priceRange   = document.getElementById('priceRange');
  const priceValue   = document.getElementById('priceValue');
  const sortSelect   = document.getElementById('sortSelect');
  const filterToggle = document.getElementById('filterToggleBtn');
  const sidebar      = document.getElementById('filtersSidebar');
  const clearFilters = document.getElementById('clearFilters');
  const resetFilters = document.getElementById('resetFilters');

  if (priceRange && priceValue) {
    priceRange.addEventListener('input', function () {
      priceValue.textContent = 'KES ' + parseInt(this.value).toLocaleString();
      filterProducts();
    });
  }
  if (searchInput) searchInput.addEventListener('input', filterProducts);
  if (sortSelect)  sortSelect.addEventListener('change', filterProducts);
  document.querySelectorAll('input[name="category"], input[name="skin"]').forEach(function (cb) {
    cb.addEventListener('change', filterProducts);
  });
  if (clearFilters) clearFilters.addEventListener('click', resetAllFilters);
  if (resetFilters) resetFilters.addEventListener('click', resetAllFilters);
  if (filterToggle && sidebar) {
    filterToggle.addEventListener('click', function () { sidebar.classList.toggle('open'); });
  }

  // === Auto filter from URL ===
  readURLAndFilter();

  // === Bundle modal ===
  const modalClose = document.getElementById('modalClose');
  const modal      = document.getElementById('bundleModal');
  if (modalClose) modalClose.addEventListener('click', closeBundle);
  if (modal) modal.addEventListener('click', function (e) { if (e.target === modal) closeBundle(); });

  console.log('%c✦ Essiz Beauty Hub', 'color:#C85B7A;font-size:18px;font-weight:bold;');
  console.log('%cWeek 4 — BIT3208 | PHP Auth + Sessions + Cart ✓', 'color:#9B84CC;font-size:13px;');
});

// === Helpers ===
function isValidEmail(email)      { return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email); }
function isValidKenyanPhone(phone){ return /^(07|01)\d{8}$/.test(phone.replace(/\s/g,'')); }
function showError(id, msg)       { const el = document.getElementById(id); if (el) el.textContent = msg; }
function clearError(id)           { const el = document.getElementById(id); if (el) el.textContent = ''; }
function clearErrors()            { document.querySelectorAll('.error-msg').forEach(function(el){ el.textContent=''; }); document.querySelectorAll('input').forEach(function(el){ el.classList.remove('error','success'); }); }

function checkPasswordStrength(password) {
  const bar  = document.getElementById('strengthBar');
  const text = document.getElementById('strengthText');
  if (!bar || !text) return;
  let strength = 0;
  if (password.length >= 8)          strength++;
  if (/[A-Z]/.test(password))        strength++;
  if (/[0-9]/.test(password))        strength++;
  if (/[^A-Za-z0-9]/.test(password)) strength++;
  const colors = ['','#E53E3E','#E6A855','#9B84CC','#6BBE8A'];
  const labels = ['','Weak','Fair','Good','Strong ✓'];
  bar.style.width      = (strength * 25) + '%';
  bar.style.background = colors[strength];
  text.textContent     = labels[strength];
  text.style.color     = colors[strength];
}

function showToast(message) {
  const toast = document.getElementById('toast');
  if (!toast) return;
  toast.textContent = message;
  toast.classList.add('show');
  setTimeout(function(){ toast.classList.remove('show'); }, 3000);
}

function addToCart(id, name, price) {
  showToast('✓ ' + name + ' added to cart!');
}

// === Filter products ===
function filterProducts() {
  const search   = document.getElementById('searchInput')?.value.toLowerCase() || '';
  const maxPrice = parseInt(document.getElementById('priceRange')?.value || 5000);
  const sortBy   = document.getElementById('sortSelect')?.value || 'default';
  const categories = Array.from(document.querySelectorAll('input[name="category"]:checked')).map(function(cb){ return cb.value; });
  const skins      = Array.from(document.querySelectorAll('input[name="skin"]:checked')).map(function(cb){ return cb.value; });
  const cards = Array.from(document.querySelectorAll('.product-card'));
  let visible = 0;
  cards.forEach(function(card) {
    const name     = card.dataset.name     || '';
    const category = card.dataset.category || '';
    const price    = parseInt(card.dataset.price || 0);
    const skin     = card.dataset.skin     || '';
    const match = name.includes(search) && categories.includes(category) && price <= maxPrice && (skins.includes(skin) || skins.includes('All'));
    card.style.display = match ? '' : 'none';
    if (match) visible++;
  });
  const grid = document.getElementById('productsGrid');
  if (grid && sortBy !== 'default') {
    const visibleCards = cards.filter(function(c){ return c.style.display !== 'none'; });
    visibleCards.sort(function(a,b){
      if (sortBy === 'price-low')  return parseInt(a.dataset.price) - parseInt(b.dataset.price);
      if (sortBy === 'price-high') return parseInt(b.dataset.price) - parseInt(a.dataset.price);
      if (sortBy === 'rating')     return parseFloat(b.dataset.rating) - parseFloat(a.dataset.rating);
      return 0;
    });
    visibleCards.forEach(function(card){ grid.appendChild(card); });
  }
  const countEl = document.getElementById('productCount');
  if (countEl) countEl.textContent = visible + ' products found';
  const noResults = document.getElementById('noResults');
  if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}

function resetAllFilters() {
  document.querySelectorAll('input[name="category"], input[name="skin"]').forEach(function(cb){ cb.checked = true; });
  const searchInput = document.getElementById('searchInput');
  if (searchInput) searchInput.value = '';
  const priceRange = document.getElementById('priceRange');
  if (priceRange) priceRange.value = 5000;
  const priceValue = document.getElementById('priceValue');
  if (priceValue) priceValue.textContent = 'KES 5,000';
  const sortSelect = document.getElementById('sortSelect');
  if (sortSelect) sortSelect.value = 'default';
  filterProducts();
}

function readURLAndFilter() {
  const params   = new URLSearchParams(window.location.search);
  const category = params.get('category');
  if (category) {
    document.querySelectorAll('input[name="category"]').forEach(function(cb){ cb.checked = cb.value === category; });
    filterProducts();
  }
}

// === Bundle Modal ===
const bundles = {
  campus: {
    icon:'🎓', title:'Campus Essentials', desc:'Everything you need for campus life.',
    original:'KES 2,400', price:'KES 1,800', save:'Save 25%',
    items:[
      {icon:'🫧',name:'Acne Control Cleanser',detail:'Gentle daily cleanser',price:'KES 550'},
      {icon:'🫙',name:'Hydrating Moisturizer',detail:'Lightweight daily moisturizer',price:'KES 1,200'},
      {icon:'💋',name:'Nude Lip Gloss',detail:'Glossy everyday lip color',price:'KES 380'},
      {icon:'☀️',name:'SPF 50 Sunscreen',detail:'Daily sun protection',price:'KES 270 (bonus)'},
    ]
  },
  glow: {
    icon:'✨', title:'Glow Package', desc:'Complete glow routine for radiant skin.',
    original:'KES 4,200', price:'KES 3,000', save:'Save 29%',
    items:[
      {icon:'🧴',name:'Glow Serum 30ml',detail:'Brightening vitamin C serum',price:'KES 850'},
      {icon:'🧴',name:'Vitamin C Toner',detail:'Brightening prep toner',price:'KES 750'},
      {icon:'🫙',name:'Hydrating Moisturizer',detail:'Deep hydration',price:'KES 1,200'},
      {icon:'☀️',name:'SPF 50 Sunscreen',detail:'Protect your glow daily',price:'KES 1,100'},
    ]
  },
  acne: {
    icon:'🌿', title:'Acne Care Bundle', desc:'Targeted skincare for acne-prone skin.',
    original:'KES 3,100', price:'KES 2,200', save:'Save 29%',
    items:[
      {icon:'🫧',name:'Acne Control Cleanser',detail:'Removes impurities gently',price:'KES 550'},
      {icon:'🧴',name:'Vitamin C Toner',detail:'Balances and brightens skin',price:'KES 750'},
      {icon:'🌹',name:'Rose Water Mist',detail:'Soothes irritated skin',price:'KES 480'},
      {icon:'🫙',name:'Oil-Free Moisturizer',detail:'Hydrates without clogging',price:'KES 900'},
    ]
  }
};

function openBundle(key) {
  const bundle = bundles[key];
  const modal  = document.getElementById('bundleModal');
  if (!bundle || !modal) return;
  document.getElementById('modalIcon').textContent    = bundle.icon;
  document.getElementById('modalTitle').textContent   = bundle.title;
  document.getElementById('modalDesc').textContent    = bundle.desc;
  document.getElementById('modalOriginal').textContent = bundle.original;
  document.getElementById('modalPrice').textContent   = bundle.price;
  document.getElementById('modalSave').textContent    = bundle.save;
  const itemsEl = document.getElementById('modalItems');
  itemsEl.innerHTML = '';
  bundle.items.forEach(function(item){
    itemsEl.innerHTML += `<div class="modal-item"><div class="modal-item-icon">${item.icon}</div><div class="modal-item-info"><div class="modal-item-name">${item.name}</div><div class="modal-item-detail">${item.detail}</div></div><div class="modal-item-price">${item.price}</div></div>`;
  });
  document.getElementById('modalCartBtn').onclick = function(){ showToast('✓ ' + bundle.title + ' added to cart!'); closeBundle(); };
  modal.classList.add('open');
  document.body.style.overflow = 'hidden';
}

function closeBundle() {
  const modal = document.getElementById('bundleModal');
  if (modal) modal.classList.remove('open');
  document.body.style.overflow = '';
}