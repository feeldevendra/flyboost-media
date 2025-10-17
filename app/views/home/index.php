<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Homepage
 * ------------------------------------------------------------
 * Dynamic landing page featuring:
 * - Hero section (editable from Admin Settings)
 * - Dynamic services grid
 * - Portfolio showcase
 * - Testimonials
 * - Latest blog posts
 * ------------------------------------------------------------
 */
?>

<!-- HERO SECTION -->
<section class="hero" data-aos="fade-up">
  <div class="container hero-content">
    <div class="hero-text">
      <h1>Empowering <span>Digital Growth</span></h1>
      <p>We build websites, apps, and marketing systems that accelerate your business success.</p>
      <a href="/services" class="btn primary">Explore Services</a>
      <a href="/contact" class="btn secondary">Get a Quote</a>
    </div>
    <div class="hero-visual">
      <img src="<?= base_url('/assets/img/hero-visual.png') ?>" alt="Digital Innovation" />
    </div>
  </div>
</section>

<!-- SERVICES SECTION -->
<section class="services" id="services" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title">Our Core Services</h2>
    <p class="section-subtitle">Comprehensive solutions designed to elevate your brand and performance.</p>

    <div class="services-grid">
      <?php foreach ($services as $service): ?>
        <div class="service-card" data-aos="zoom-in">
          <div class="icon">
            <img src="<?= base_url($service['icon'] ?? '/assets/img/default-icon.png') ?>" alt="<?= e($service['title']) ?>">
          </div>
          <h3><?= e($service['title']) ?></h3>
          <p><?= e(substr(strip_tags($service['description']), 0, 90)) ?>...</p>
          <a href="/service/<?= e($service['slug']) ?>" class="btn-small">Learn More</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- PORTFOLIO SECTION -->
<section class="portfolio" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title">Our Recent Work</h2>
    <p class="section-subtitle">A glimpse into the projects we’ve delivered with passion and precision.</p>

    <div class="portfolio-grid">
      <?php foreach ($portfolio as $item): ?>
        <div class="portfolio-card" data-aos="zoom-in">
          <img src="<?= base_url($item['image']) ?>" alt="<?= e($item['title']) ?>">
          <div class="overlay">
            <h4><?= e($item['title']) ?></h4>
            <p><?= e($item['category']) ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-3">
      <a href="/portfolio" class="btn secondary">View Full Portfolio</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="testimonials" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title">What Our Clients Say</h2>
    <div class="testimonial-slider">
      <?php foreach ($testimonials as $t): ?>
        <div class="testimonial-card">
          <p>“<?= e($t['message']) ?>”</p>
          <h4>- <?= e($t['client_name']) ?></h4>
          <span><?= e($t['company']) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- BLOG SECTION -->
<section class="latest-blogs" id="blog" data-aos="fade-up">
  <div class="container">
    <h2 class="section-title">Insights & Updates</h2>
    <p class="section-subtitle">Read our latest articles on web, design, and marketing innovations.</p>

    <div class="blog-grid">
      <?php foreach ($blogs as $blog): ?>
        <div class="blog-card" data-aos="fade-up">
          <div class="blog-image">
            <img src="<?= base_url($blog['feature_image'] ?? '/assets/img/blog-placeholder.jpg') ?>" alt="<?= e($blog['title']) ?>">
          </div>
          <div class="blog-content">
            <h3><?= e($blog['title']) ?></h3>
            <p><?= e(substr(strip_tags($blog['content']), 0, 100)) ?>...</p>
            <a href="/blog/<?= e($blog['slug']) ?>" class="btn-small">Read More</a>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-3">
      <a href="/blog" class="btn secondary">View All Articles</a>
    </div>
  </div>
</section>
