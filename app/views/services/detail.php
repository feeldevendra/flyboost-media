<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Service Detail Page
 * ------------------------------------------------------------
 * Dynamic individual service view
 * - Description and media
 * - Configurable options
 * - Integrated “Get Quote” form
 * ------------------------------------------------------------
 */
?>

<section class="service-header" data-aos="fade-up">
  <div class="container">
    <h1><?= e($service['title']) ?></h1>
    <p class="service-subtitle"><?= e($service['short_description'] ?? 'Tailored solutions for your brand’s success.') ?></p>
  </div>
</section>

<section class="service-detail" data-aos="fade-up">
  <div class="container service-grid">

    <!-- LEFT COLUMN: Description -->
    <div class="service-description">
      <img class="service-feature" src="<?= base_url($service['feature_image'] ?? '/assets/img/service-placeholder.jpg') ?>" alt="<?= e($service['title']) ?>">
      <div class="content">
        <?= $service['description'] ?>
      </div>
    </div>

    <!-- RIGHT COLUMN: Get Quote Form -->
    <div class="service-quote-form">
      <div class="quote-box">
        <h3>Get a Custom Quote</h3>
        <p>Tell us what you need, and we’ll craft a personalized proposal just for you.</p>

        <form id="quoteForm" action="/service/<?= e($service['slug']) ?>/quote" method="POST">
          <input type="hidden" name="service" value="<?= e($service['title']) ?>">

          <div class="form-group">
            <label for="name">Full Name *</label>
            <input type="text" name="name" id="name" placeholder="Your name" required>
          </div>

          <div class="form-group">
            <label for="email">Email *</label>
            <input type="email" name="email" id="email" placeholder="you@example.com" required>
          </div>

          <div class="form-group">
            <label for="phone">Phone *</label>
            <input type="text" name="phone" id="phone" placeholder="+91 9876543210" required>
          </div>

          <?php if (!empty($options)): ?>
          <div class="form-group">
            <label for="option">Choose Plan *</label>
            <select name="option" id="option" required>
              <?php foreach ($options as $opt): ?>
                <option value="<?= e($opt['name']) ?>"><?= e($opt['name']) ?> — ₹<?= e($opt['price']) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
          <?php endif; ?>

          <div class="form-group">
            <label for="message">Project Requirements *</label>
            <textarea name="message" id="message" rows="4" placeholder="Tell us about your project..." required></textarea>
          </div>

          <button type="submit" class="btn primary">Request Quote</button>
        </form>
      </div>
    </div>
  </div>
</section>

<!-- ADDITIONAL SECTIONS -->
<section class="related-services" data-aos="fade-up">
  <div class="container">
    <h2>Other Services You May Like</h2>
    <div class="services-grid">
      <?php foreach (array_slice($services, 0, 3) as $related): ?>
        <div class="service-card">
          <img src="<?= base_url($related['icon'] ?? '/assets/img/default-icon.png') ?>" alt="<?= e($related['title']) ?>">
          <h4><?= e($related['title']) ?></h4>
          <p><?= e(substr(strip_tags($related['description']), 0, 90)) ?>...</p>
          <a href="/service/<?= e($related['slug']) ?>" class="btn-small">Explore</a>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>
