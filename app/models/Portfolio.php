<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Portfolio Model
 * ------------------------------------------------------------
 * Handles all project and case study data.
 * Used on homepage (featured projects) and admin CMS.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Portfolio
{
    /**
     * Fetch all active portfolio projects
     */
    public static function all(): array
    {
        $stmt = DB::conn()->query("SELECT * FROM portfolio WHERE is_active = 1 ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    /**
     * Fetch featured projects (for homepage)
     */
    public static function featured(int $limit = 6): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM portfolio WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find portfolio project by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM portfolio WHERE slug = :slug AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $item = $stmt->fetch();
        return $item ?: null;
    }

    /**
     * Save or update project (Admin side)
     */
    public static function save(array $data, ?int $id = null): bool
    {
        $pdo = DB::conn();

        if ($id) {
            $stmt = $pdo->prepare("UPDATE portfolio 
                SET title=:title, slug=:slug, description=:description, 
                    cover_image=:cover_image, client_name=:client_name, 
                    project_url=:project_url, is_featured=:is_featured, 
                    is_active=:is_active, updated_at=NOW() 
                WHERE id=:id");
            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'cover_image' => $data['cover_image'],
                'client_name' => $data['client_name'],
                'project_url' => $data['project_url'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO portfolio 
                (title, slug, description, cover_image, client_name, project_url, is_featured, is_active, created_at)
                VALUES (:title, :slug, :description, :cover_image, :client_name, :project_url, :is_featured, :is_active, NOW())");
            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'cover_image' => $data['cover_image'],
                'client_name' => $data['client_name'],
                'project_url' => $data['project_url'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1
            ]);
        }
    }

    /**
     * Delete a project (Admin)
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM portfolio WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
