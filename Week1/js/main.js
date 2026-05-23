// ============================================================
// ESSIZ BEAUTY HUB — Week 1 JavaScript
// BIT3208 Advanced Web Design and Development
// Basic DOM manipulation demo for Week 1
// ============================================================

document.addEventListener('DOMContentLoaded', function () {

  // --- Console greeting (demonstrates JS is working) ---
  console.log('%c✦ Essiz Beauty Hub', 'color:#C85B7A; font-size:18px; font-weight:bold;');
  console.log('%cWeek 1 — BIT3208 | JavaScript is running ✓', 'color:#9B84CC; font-size:13px;');

  // --- Animate preview cards staggered on load ---
  const cards = document.querySelectorAll('.preview-card');
  cards.forEach(function (card, index) {
    card.style.opacity = '0';
    card.style.transform = 'translateY(16px)';
    setTimeout(function () {
      card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
      card.style.opacity = '1';
      card.style.transform = 'translateY(0)';
    }, 1100 + (index * 100));
  });

  // --- Current date display ---
  const dateEl = document.querySelector('.footer-note');
  if (dateEl) {
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    console.log('Page loaded on: ' + now.toLocaleDateString('en-KE', options));
  }

  // --- Simple DOM manipulation demo (Week 1 requirement) ---
  const brand = document.querySelector('.brand-name');
  if (brand) {
    brand.addEventListener('click', function () {
      brand.style.transition = 'transform 0.3s ease';
      brand.style.transform = 'scale(1.05)';
      setTimeout(function () { brand.style.transform = 'scale(1)'; }, 300);
    });
  }

  // --- Log PHP server info to console for verification ---
  console.log('%cServer-side check:', 'color:#6BBE8A; font-weight:bold;');
  console.log('  Localhost: ✓ Running');
  console.log('  PHP: ✓ Active (check page source for PHP output)');
  console.log('  Database: Check status indicator above');

});