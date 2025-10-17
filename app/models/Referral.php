<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Referral Model
 * ------------------------------------------------------------
 * Manages affiliate/referral tracking for partners.
 * Handles unique codes, clicks, leads, and payouts.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Referral
{
    /**
     * Generate a unique referral code
     */
    public static function generateCode(int $user_id): string
    {
        $code = strtoupper(bin2hex(random_bytes(4))) . $user_id;
        $stmt = DB::conn()->prepare("INSERT INTO referrals (user_id, code, created_at) VALUES (:uid, :code, NOW())");
        $stmt->execute(['uid' => $user_id, 'code' => $code]);
        return $code;
    }

    /**
     * Record a referral click
     */
    public static function logClick(string $code, string $ip): void
    {
        $pdo = DB::conn();

        // Validate referral code
        $stmt = $pdo->prepare("SELECT id FROM referrals WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $ref = $stmt->fetch();

        if (!$ref) return;

        $pdo->prepare("INSERT INTO referral_clicks (referral_id, ip_address, clicked_at) VALUES (:rid, :ip, NOW())")
            ->execute(['rid' => $ref['id'], 'ip' => $ip]);
    }

    /**
     * Log a conversion (lead or payment)
     */
    public static function logConversion(string $code, string $type, int $ref_id): void
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT id FROM referrals WHERE code = :code LIMIT 1");
        $stmt->execute(['code' => $code]);
        $ref = $stmt->fetch();
        if (!$ref) return;

        $pdo->prepare("INSERT INTO referral_conversions (referral_id, conversion_type, related_id, created_at)
                       VALUES (:rid, :type, :rel, NOW())")
            ->execute(['rid' => $ref['id'], 'type' => $type, 'rel' => $ref_id]);
    }

    /**
     * Get all referrals with stats
     */
    public static function allWithStats(): array
    {
        $sql = "SELECT r.id, r.user_id, r.code, r.created_at,
                       COUNT(DISTINCT c.id) AS clicks,
                       COUNT(DISTINCT cv.id) AS conversions
                FROM referrals r
                LEFT JOIN referral_clicks c ON r.id = c.referral_id
                LEFT JOIN referral_conversions cv ON r.id = cv.referral_id
                GROUP BY r.id
                ORDER BY r.created_at DESC";
        $stmt = DB::conn()->query($sql);
        return $stmt->fetchAll();
    }

    /**
     * Fetch referrals for a specific user
     */
    public static function byUser(int $user_id): array
    {
        $sql = "SELECT r.*, 
                       (SELECT COUNT(*) FROM referral_clicks WHERE referral_id=r.id) AS clicks,
                       (SELECT COUNT(*) FROM referral_conversions WHERE referral_id=r.id) AS conversions
                FROM referrals r
                WHERE r.user_id=:uid
                ORDER BY r.created_at DESC";
        $stmt = DB::conn()->prepare($sql);
        $stmt->execute(['uid' => $user_id]);
        return $stmt->fetchAll();
    }

    /**
     * Calculate affiliate performance summary (Admin dashboard)
     */
    public static function summary(): array
    {
        $pdo = DB::conn();
        $sql = "SELECT 
                    COUNT(DISTINCT r.id) AS total_referrals,
                    COUNT(DISTINCT c.id) AS total_clicks,
                    COUNT(DISTINCT cv.id) AS total_conversions
                FROM referrals r
                LEFT JOIN referral_clicks c ON r.id = c.referral_id
                LEFT JOIN referral_conversions cv ON r.id = cv.referral_id";
        $stmt = $pdo->query($sql);
        return $stmt->fetch() ?: [];
    }

    /**
     * Delete a referral and all related records
     */
    public static function delete(int $id): bool
    {
        $pdo = DB::conn();
        $pdo->prepare("DELETE FROM referral_clicks WHERE referral_id=:id")->execute(['id' => $id]);
        $pdo->prepare("DELETE FROM referral_conversions WHERE referral_id=:id")->execute(['id' => $id]);
        $stmt = $pdo->prepare("DELETE FROM referrals WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
