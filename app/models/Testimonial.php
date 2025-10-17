<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Testimonial Model
 * ------------------------------------------------------------
 * Manages client feedback and ratings displayed on
 * the homepage, and in the admin Testimonials section.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Testimonial
{
    /**
     * Fetch all active testimonials
     */
    public static function all(): array
    {
        $stmt = DB::conn()->query("SELECT * FROM testimonials WHERE is_active = 1 ORDER BY sort_order ASC");
        return $stmt->fetchAll();
    }

    /**
     * Fetch featured testimonials (optional homepage highlight)
     */
    public static function featured(int $limit = 5): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM testimonials WHERE is_active = 1 AND is_featured = 1 ORDER BY sort_order ASC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Add or update testimonial (Admin)
     */
    public static function save(array $data, ?int $id = null): bool
    {
        $pdo = DB::conn();

        if ($id) {
            $stmt = $pdo->prepare("UPDATE testimonials 
                SET client_name=:client_name, company=:company, 
                    content=:content, rating=:rating, avatar=:avatar, 
                    is_featured=:is_featured, is_active=:is_active 
                WHERE id=:id");
            return $stmt->execute([
                'client_name' => $data['client_name'],
                'company' => $data['company'],
                'content' => $data['content'],
                'rating' => $data['rating'],
                'avatar' => $data['avatar'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1,
                'id' => $id
            ]);
        } else {
            $stmt = $pdo->prepare("INSERT INTO testimonials 
                (client_name, company, content, rating, avatar, is_featured, is_active)
                VALUES (:client_name, :company, :content, :rating, :avatar, :is_featured, :is_active)");
            return $stmt->execute([
                'client_name' => $data['client_name'],
                'company' => $data['company'],
                'content' => $data['content'],
                'rating' => $data['rating'],
                'avatar' => $data['avatar'],
                'is_featured' => $data['is_featured'] ?? 0,
                'is_active' => $data['is_active'] ?? 1
            ]);
        }
    }

    /**
     * Delete a testimonial
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM testimonials WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
