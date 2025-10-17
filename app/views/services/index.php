<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Services Listing Page
 * ------------------------------------------------------------
 * Displays all available services dynamically
 * with icons, descriptions, and links to detail pages.
 * ------------------------------------------------------------
 */
?>
<section class="services-hero" data-aos="fade-up">
  <div class="container">
    <h1>Our Services</h1>
    <p class="hero-subtext">
      We provide creative, data-driven, and scalable digital solutions for modern businesses.
    </p>
  </div>
</section>

<section class="services-list-section" data-aos="fade-up">
  <div class="container">
    <div class="services-grid">
      <?php foreach ($services as $service): ?>
        <div class="service-card" data-aos="zoom-in">
          <div class="service-icon">
            <img src="<?= base_url($service['icon'] ?? '/assets/img/default-icon.png') ?>" 
                 alt="<?= e($service['title']) ?>">
          </div>
          <h3><?= e($service['title']) ?></h3>
          <p><?= e(substr(strip_tags($service['description']), 0, 110)) ?>...</p>
          <a href="/service/<?= e($service['slug']) ?>" class="btn-small">View Details</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="cta-section" data-aos="fade-up">
  <div class="container text-center">
    <h2>Have a Project in Mind?</h2>
    <p>Let’s discuss your goals and tailor a digital solution that fits perfectly for you.</p>
    <a href="/contact" class="btn primary">Get a Free Quote</a>
  </div>
</section>
