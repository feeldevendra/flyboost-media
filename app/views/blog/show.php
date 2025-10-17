<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Blog Detail Page
 * ------------------------------------------------------------
 * Displays a single blog post with:
 * - Feature image
 * - Content
 * - AdSense (if enabled)
 * - Related posts
 * ------------------------------------------------------------
 */
?>

<article class="blog-single" data-aos="fade-up">
  <div class="container blog-container">

    <!-- BLOG COVER -->
    <div class="blog-cover">
      <img src="<?= base_url($blog['feature_image'] ?? '/assets/img/blog-placeholder.jpg') ?>" alt="<?= e($blog['title']) ?>">
    </div>

    <!-- BLOG HEADER -->
    <header class="blog-header">
      <h1><?= e($blog['title']) ?></h1>
      <p class="blog-meta">
        Published on <?= date('F d, Y', strtotime($blog['published_at'])) ?>
      </p>
    </header>

    <!-- BLOG CONTENT -->
    <div class="blog-content">
      <?= $blog['content'] ?>
    </div>

    <!-- GOOGLE ADSENSE -->
    <?php if (!empty($showAds)): ?>
      <div class="adsense-block" style="margin:40px 0;">
        <!-- AdSense Placeholder -->
        <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-xxxxxxxxxxxxxx" crossorigin="anonymous"></script>
        <ins class="adsbygoogle"
             style="display:block; text-align:center;"
             data-ad-layout="in-article"
             data-ad-format="fluid"
             data-ad-client="ca-pub-xxxxxxxxxxxxxx"
             data-ad-slot="1234567890"></ins>
        <script>(adsbygoogle = window.adsbygoogle || []).push({});</script>
      </div>
    <?php endif; ?>

    <!-- SHARE BUTTONS -->
    <div class="share-section">
      <p>Share this article:</p>
      <div class="share-buttons">
        <?php $url = urlencode(Config::BASE_URL . '/blog/' . $blog['slug']); ?>
        <a href="https://www.facebook.com/sharer/sharer.php?u=<?= $url ?>" target="_blank">Facebook</a>
        <a href="https://twitter.com/intent/tweet?url=<?= $url ?>&text=<?= urlencode($blog['title']) ?>" target="_blank">Twitter</a>
        <a href="https://www.linkedin.com/shareArticle?mini=true&url=<?= $url ?>&title=<?= urlencode($blog['title']) ?>" target="_blank">LinkedIn</a>
        <a href="https://wa.me/?text=<?= urlencode($blog['title'] . ' ' . $url) ?>" target="_blank">WhatsApp</a>
      </div>
    </div>

    <!-- RELATED POSTS -->
    <?php if (!empty($related = \App\Models\Blog::related($blog['id']))): ?>
      <section class="related-posts" data-aos="fade-up">
        <h2>Related Articles</h2>
        <div class="related-grid">
          <?php foreach ($related as $r): ?>
            <a href="/blog/<?= e($r['slug']) ?>" class="related-card">
              <img src="<?= base_url($r['feature_image'] ?? '/assets/img/blog-placeholder.jpg') ?>" alt="<?= e($r['title']) ?>">
              <h4><?= e($r['title']) ?></h4>
            </a>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endif; ?>
  </div>
</article>

<!-- STYLES -->
<style>
.blog-container {
  max-width: 800px;
  margin: 80px auto;
  line-height: 1.8;
}
.blog-cover img {
  width: 100%;
  border-radius: 16px;
  margin-bottom: 30px;
}
.blog-header h1 {
  font-size: 2rem;
  color: #111;
  margin-bottom: 10px;
}
.blog-meta {
  color: #777;
  font-size: 0.9rem;
  margin-bottom: 30px;
}
.blog-content {
  font-size: 1.05rem;
  color: #333;
}
.blog-content img {
  border-radius: 12px;
  margin: 20px 0;
}
.share-section {
  margin-top: 50px;
  text-align: center;
}
.share-buttons a {
  display: inline-block;
  margin: 8px;
  padding: 8px 14px;
  border-radius: 6px;
  background: #007aff;
  color: white;
  font-size: 0.9rem;
  transition: 0.2s ease;
}
.share-buttons a:hover {
  background: #005ecb;
}
.related-posts {
  margin-top: 60px;
}
.related-posts h2 {
  text-align: center;
  margin-bottom: 30px;
}
.related-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
  gap: 20px;
}
.related-card {
  text-decoration: none;
  color: #111;
  text-align: center;
}
.related-card img {
  border-radius: 12px;
  margin-bottom: 10px;
}
.related-card:hover h4 {
  color: #007aff;
}
</style>
