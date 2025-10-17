<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Global Layout
 * ------------------------------------------------------------
 * Shared by all frontend pages.
 * Includes:
 * - Meta + SEO tags
 * - Header navigation
 * - Dynamic content
 * - Footer
 * - Responsive layout and scripts
 * ------------------------------------------------------------
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($meta['title'] ?? Config::SITE_NAME) ?></title>
  <meta name="description" content="<?= e($meta['description'] ?? Config::DEFAULT_META_DESC) ?>">
  <link rel="icon" href="<?= base_url('/assets/img/favicon.png') ?>" type="image/png">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

  <!-- Global Styles -->
  <link rel="stylesheet" href="<?= base_url('/assets/css/style.css') ?>">

  <!-- Animation libraries -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/3.12.3/gsap.min.js"></script>
  <script src="https://unpkg.com/aos@2.3.4/dist/aos.js"></script>
  <link rel="stylesheet" href="https://unpkg.com/aos@2.3.4/dist/aos.css" />

  <script>
    document.addEventListener('DOMContentLoaded', () => {
      AOS.init({ duration: 1000, once: true });
    });
  </script>
</head>
<body>

  <!-- Header -->
  <header class="site-header">
    <div class="container header-inner">
      <a href="/" class="logo">
        <img src="<?= base_url('/assets/img/logo-light.png') ?>" alt="Flyboost Media Logo" />
      </a>

      <nav class="nav-menu" id="navMenu">
        <a href="/">Home</a>
        <a href="/services">Services</a>
        <a href="/blog">Blog</a>
        <a href="/contact">Contact</a>
        <?php if (isLoggedIn()): ?>
          <a href="/account">My Account</a>
          <a href="/logout" class="logout-btn">Logout</a>
        <?php else: ?>
          <a href="/login" class="login-btn">Login</a>
        <?php endif; ?>
      </nav>

      <button id="menuToggle" class="menu-toggle">
        <span></span><span></span><span></span>
      </button>
    </div>
  </header>

  <!-- Dynamic Page Content -->
  <main class="page-content">
    <?php include $viewPath; ?>
  </main>

  <!-- Footer -->
  <footer class="site-footer">
    <div class="container footer-inner">
      <div class="footer-left">
        <img src="<?= base_url('/assets/img/logo-dark.png') ?>" alt="Flyboost Media" class="footer-logo" />
        <p>Empowering digital growth with websites, apps, and AI-driven marketing strategies.</p>
      </div>

      <div class="footer-right">
        <h4>Quick Links</h4>
        <a href="/services">Services</a>
        <a href="/blog">Blog</a>
        <a href="/contact">Contact</a>
        <a href="/privacy-policy">Privacy Policy</a>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> Flyboost Media. All rights reserved.</p>
    </div>
  </footer>

  <!-- Mobile Menu Toggle -->
  <script>
    document.getElementById('menuToggle').addEventListener('click', function() {
      document.getElementById('navMenu').classList.toggle('active');
      this.classList.toggle('open');
    });
  </script>

  <!-- Global JS -->
  <script src="<?= base_url('/assets/js/main.js') ?>"></script>
</body>
</html>
