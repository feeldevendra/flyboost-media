<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Portfolio Page
 * ------------------------------------------------------------
 * Displays your project showcases dynamically with:
 * - Responsive grid layout
 * - Hover preview
 * - Modal detail popup (future)
 * ------------------------------------------------------------
 */
?>

<section class="portfolio-hero" data-aos="fade-up">
  <div class="container text-center">
    <h1>Our Work Speaks for Itself</h1>
    <p class="hero-subtext">
      Explore a selection of our recent projects — crafted with precision, creativity, and results in mind.
    </p>
  </div>
</section>

<section class="portfolio-gallery" data-aos="fade-up">
  <div class="container">
    <div class="portfolio-grid">
      <?php if (!empty($portfolio)): ?>
        <?php foreach ($portfolio as $item): ?>
          <div class="portfolio-item" data-aos="zoom-in">
            <div class="portfolio-thumb">
              <img src="<?= base_url($item['image'] ?? '/assets/img/portfolio-placeholder.jpg') ?>" 
                   alt="<?= e($item['title']) ?>">
              <div class="portfolio-overlay">
                <div class="overlay-content">
                  <h3><?= e($item['title']) ?></h3>
                  <p><?= e($item['category'] ?? 'Creative Work') ?></p>
                  <?php if (!empty($item['project_url'])): ?>
                    <a href="<?= e($item['project_url']) ?>" target="_blank" class="btn-small">View Live</a>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; color:#777;">No portfolio projects added yet. Check back soon!</p>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="cta-section" data-aos="fade-up">
  <div class="container text-center">
    <h2>Want to Start Your Own Project?</h2>
    <p>Let’s turn your ideas into a beautifully functional digital experience.</p>
    <a href="/contact" class="btn primary">Get a Free Consultation</a>
  </div>
</section>

<style>
.portfolio-gallery {
  padding: 80px 0;
}
.portfolio-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 24px;
}
.portfolio-item {
  position: relative;
  overflow: hidden;
  border-radius: 16px;
  cursor: pointer;
  box-shadow: 0 4px 16px rgba(0,0,0,0.05);
  transition: transform 0.3s ease;
}
.portfolio-item:hover {
  transform: translateY(-4px);
}
.portfolio-thumb img {
  width: 100%;
  border-radius: 16px;
  transition: transform 0.4s ease;
}
.portfolio-item:hover img {
  transform: scale(1.06);
}
.portfolio-overlay {
  position: absolute;
  inset: 0;
  background: rgba(0,0,0,0.6);
  opacity: 0;
  display: flex;
  justify-content: center;
  align-items: center;
  transition: 0.3s ease;
  border-radius: 16px;
}
.portfolio-item:hover .portfolio-overlay {
  opacity: 1;
}
.overlay-content {
  text-align: center;
  color: #fff;
}
.overlay-content h3 {
  margin-bottom: 8px;
  font-size: 1.3rem;
}
.overlay-content p {
  font-size: 0.95rem;
  margin-bottom: 10px;
  color: #ddd;
}
.overlay-content .btn-small {
  background: #007aff;
  color: white;
  border-radius: 8px;
  padding: 8px 18px;
}
</style>
