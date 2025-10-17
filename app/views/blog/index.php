<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Blog Listing Page
 * ------------------------------------------------------------
 * Displays all published blog posts dynamically.
 * ------------------------------------------------------------
 */
?>

<section class="blog-hero" data-aos="fade-up">
  <div class="container text-center">
    <h1>Insights & Updates</h1>
    <p class="hero-subtext">Explore expert insights on web design, marketing, and business growth from the Flyboost Media team.</p>
  </div>
</section>

<section class="blog-list" data-aos="fade-up">
  <div class="container">
    <div class="blog-grid">
      <?php if (!empty($blogs)): ?>
        <?php foreach ($blogs as $post): ?>
          <article class="blog-card" data-aos="fade-up">
            <div class="blog-image">
              <img src="<?= base_url($post['feature_image'] ?? '/assets/img/blog-placeholder.jpg') ?>" alt="<?= e($post['title']) ?>">
            </div>
            <div class="blog-info">
              <h2><a href="/blog/<?= e($post['slug']) ?>"><?= e($post['title']) ?></a></h2>
              <p class="excerpt"><?= e(substr(strip_tags($post['content']), 0, 120)) ?>...</p>
              <p class="meta">
                <span><?= date('M d, Y', strtotime($post['published_at'])) ?></span> |
                <a href="/blog/<?= e($post['slug']) ?>">Read More →</a>
              </p>
            </div>
          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="text-align:center; color:#777;">No blog posts published yet. Check back soon!</p>
      <?php endif; ?>
    </div>
  </div>
</section>
