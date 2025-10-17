<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Service Model
 * ------------------------------------------------------------
 * Handles all queries related to services and their options.
 * Used in homepage, service configurator, and admin.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Service
{
    /**
     * Fetch all active services
     */
    public static function all(): array
    {
        $stmt = DB::conn()->query("SELECT * FROM services WHERE is_active = 1 ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    /**
     * Fetch featured services for homepage
     */
    public static function getFeatured(int $limit = 6): array
    {
        $sql = "SELECT * FROM services WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT :limit";
        $stmt = DB::conn()->prepare($sql);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Find a service by slug
     */
    public static function findBySlug(string $slug): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM services WHERE slug = :slug AND is_active = 1 LIMIT 1");
        $stmt->execute(['slug' => $slug]);
        $service = $stmt->fetch();
        return $service ?: null;
    }

    /**
     * Fetch all options/configurations for a service
     */
    public static function getOptions(int $service_id): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM service_options WHERE service_id = :sid ORDER BY id ASC");
        $stmt->execute(['sid' => $service_id]);
        return $stmt->fetchAll();
    }

    /**
     * Save or update a service (Admin side)
     */
    public static function save(array $data, ?int $id = null): bool
    {
        $pdo = DB::conn();

        if ($id) {
            $stmt = $pdo->prepare("UPDATE services 
                SET title=:title, slug=:slug, description=:description, base_price=:base_price, 
                    thumbnail=:thumbnail, is_featured=:is_featured, is_active=:is_active 
                WHERE id=:id");
            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'base_price' => $data['base_price'],
                'thumbnail' => $data['thumbnail'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO services 
                (title, slug, description, base_price, thumbnail, is_featured, is_active) 
                VALUES (:title, :slug, :description, :base_price, :thumbnail, :is_featured, :is_active)");
            return $stmt->execute([
                'title' => $data['title'],
                'slug' => $data['slug'],
                'description' => $data['description'],
                'base_price' => $data['base_price'],
                'thumbnail' => $data['thumbnail'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1
            ]);
        }
    }

    /**
     * Delete a service (Admin side)
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM services WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
