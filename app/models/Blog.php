<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Blog Model
 * ------------------------------------------------------------
 * Handles all blog-related queries and admin CMS operations.
 * Includes AMP, SEO fields, and AdSense toggle.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Blog
{
    /**
     * Fetch all published blogs
     */
    public static function all(int $limit = 50): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM blogs WHERE is_published = 1 ORDER BY published_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Fetch the latest N blog posts
     */
    public static function latest(int $limit = 3): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM blogs WHERE is_published = 1 ORDER BY published_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find a blog by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM blogs WHERE slug = :slug AND is_published = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $blog = $stmt->fetch();
        return $blog ?: null;
    }

    /**
     * Save or update a blog post (Admin)
     */
    public static function save(array $data, ?int $id = null): bool
    {
        $pdo = DB::conn();

        if ($id) {
            $stmt = $pdo->prepare("UPDATE blogs 
                SET title=:title, slug=:slug, content=:content, feature_image=:feature_image,
                    meta_title=:meta_title, meta_description=:meta_description, 
                    is_published=:is_published, show_ads=:show_ads, updated_at=NOW()
                WHERE id=:id");

            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'],
                'feature_image' => $data['feature_image'],
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'is_published' => $data['is_published'] ?? 0,
                'show_ads' => $data['show_ads'] ?? 0,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO blogs 
                (title, slug, content, feature_image, meta_title, meta_description, 
                 is_published, show_ads, published_at) 
                VALUES (:title, :slug, :content, :feature_image, :meta_title, 
                        :meta_description, :is_published, :show_ads, NOW())");

            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'content' => $data['content'],
                'feature_image' => $data['feature_image'],
                'meta_title' => $data['meta_title'],
                'meta_description' => $data['meta_description'],
                'is_published' => $data['is_published'] ?? 0,
                'show_ads' => $data['show_ads'] ?? 0
            ]);
        }
    }

    /**
     * Delete a blog post (Admin)
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM blogs WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Automatically post to social media (Admin automation)
     */
    public static function autoPostSocial(array $post): void
    {
        // Placeholder: this will later use LinkedIn, X (Twitter), and Facebook APIs
        // Example stub: push to background queue
        $log = BASE_PATH . '/storage/autopost.log';
        $msg = "[" . date('Y-m-d H:i:s') . "] Auto-post scheduled: {$post['title']} ({$post['slug']})" . PHP_EOL;
        if (!is_dir(dirname($log))) mkdir(dirname($log), 0775, true);
        file_put_contents($log, $msg, FILE_APPEND);
    }
}
