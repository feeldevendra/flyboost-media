<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Blog Controller
 * ------------------------------------------------------------
 * Handles:
 * - Blog listing
 * - Single blog view
 * - AMP version
 * - AdSense display
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Blog;
use App\Config;

class BlogController
{
    /**
     * Display all published blog posts
     */
    public function index(): void
    {
        $blogs = Blog::all(30);

        $meta = [
            'title' => 'Blog & Insights | ' . Config::SITE_NAME,
            'description' => 'Read the latest insights on design, development, and digital marketing from the Flyboost Media team.'
        ];

        view('blog/index', compact('blogs', 'meta'));
    }

    /**
     * Display a single blog post
     */
    public function show(string $slug): void
    {
        $blog = Blog::findBySlug($slug);

        if (!$blog) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $meta = [
            'title' => $blog['meta_title'] ?: $blog['title'],
            'description' => $blog['meta_description'] ?: substr(strip_tags($blog['content']), 0, 150)
        ];

        // Include AdSense if toggled ON
        $showAds = $blog['show_ads'] ?? 0;

        view('blog/show', compact('blog', 'meta', 'showAds'));
    }

    /**
     * Serve AMP version of a blog post
     */
    public function amp(string $slug): void
    {
        $blog = Blog::findBySlug($slug);
        if (!$blog) {
            http_response_code(404);
            echo "<h2>AMP Page Not Found</h2>";
            return;
        }

        header('Content-Type: text/html; charset=utf-8');
        ?>
        <!doctype html>
        <html ⚡ lang="en">
        <head>
            <meta charset="utf-8">
            <title><?= htmlspecialchars($blog['meta_title'] ?: $blog['title']) ?></title>
            <meta name="description" content="<?= htmlspecialchars($blog['meta_description'] ?: substr(strip_tags($blog['content']), 0, 150)) ?>">
            <link rel="canonical" href="<?= Config::BASE_URL ?>/blog/<?= $blog['slug'] ?>">
            <meta name="viewport" content="width=device-width,minimum-scale=1,initial-scale=1">
            <style amp-custom>
                body { font-family: 'Inter', sans-serif; color: #333; padding: 20px; line-height: 1.6; }
                h1 { color: #000; }
                img { max-width: 100%; border-radius: 8px; margin: 10px 0; }
                a { color: #0078ff; text-decoration: none; }
            </style>
            <script async src="https://cdn.ampproject.org/v0.js"></script>
        </head>
        <body>
            <h1><?= htmlspecialchars($blog['title']) ?></h1>
            <p><em>Published on <?= date('F d, Y', strtotime($blog['published_at'])) ?></em></p>
            <amp-img src="<?= htmlspecialchars($blog['feature_image']) ?>" width="600" height="400" layout="responsive"></amp-img>
            <div><?= $blog['content'] ?></div>
        </body>
        </html>
        <?php
    }
}
