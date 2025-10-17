<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — User Model
 * ------------------------------------------------------------
 * Handles all user account operations:
 * authentication, registration, roles, 2FA, and session history.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use App\Config;
use PDO;
use DateTime;

class User
{
    /**
     * Create a new user account
     */
    public static function register(array $data): bool
    {
        $pdo = DB::conn();
        $hashed = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $stmt = $pdo->prepare("INSERT INTO users 
            (name, email, password, role, is_verified, created_at) 
            VALUES (:name, :email, :password, :role, :is_verified, NOW())");

        return $stmt->execute([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'password' => $hashed,
            'role' => strtoupper($data['role'] ?? 'CLIENT'),
            'is_verified' => 0
        ]);
    }

    /**
     * Authenticate user (login)
     */
    public static function login(string $email, string $password): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute(['email' => strtolower($email)]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Update last login details
            self::logSession($user['id']);
            return $user;
        }

        return null;
    }

    /**
     * Save session history for audit
     */
    public static function logSession(int $user_id): void
    {
        $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        $ua = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        $pdo = DB::conn();

        $stmt = $pdo->prepare("INSERT INTO user_sessions (user_id, ip_address, user_agent, created_at)
                               VALUES (:user_id, :ip, :ua, NOW())");
        $stmt->execute([
            'user_id' => $user_id,
            'ip' => $ip,
            'ua' => substr($ua, 0, 255)
        ]);

        // Update main user record with last login
        $pdo->prepare("UPDATE users SET last_login = NOW(), last_ip = :ip WHERE id = :id")
            ->execute(['ip' => $ip, 'id' => $user_id]);
    }

    /**
     * Get user by ID
     */
    public static function find(int $id): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    /**
     * Fetch all users (Admin side)
     */
    public static function all(int $limit = 100): array
    {
        $stmt = DB::conn()->prepare("SELECT id, name, email, role, is_verified, last_login 
                                     FROM users ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Check if an email is already registered
     */
    public static function exists(string $email): bool
    {
        $stmt = DB::conn()->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
        $stmt->execute(['email' => strtolower($email)]);
        return (bool)$stmt->fetchColumn();
    }

    /**
     * Update user verification status
     */
    public static function verifyEmail(int $id): bool
    {
        $stmt = DB::conn()->prepare("UPDATE users SET is_verified = 1 WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Generate & save a 2FA code (email OTP)
     */
    public static function generate2FA(int $id): string
    {
        $otp = random_int(100000, 999999);
        $stmt = DB::conn()->prepare("UPDATE users SET twofa_code = :otp, twofa_expiry = DATE_ADD(NOW(), INTERVAL 10 MINUTE) WHERE id = :id");
        $stmt->execute(['otp' => $otp, 'id' => $id]);
        return (string)$otp;
    }

    /**
     * Validate 2FA code
     */
    public static function verify2FA(int $id, string $code): bool
    {
        $stmt = DB::conn()->prepare("SELECT twofa_code, twofa_expiry FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $row = $stmt->fetch();

        if (!$row) return false;
        if ((int)$row['twofa_code'] !== (int)$code) return false;

        $now = new DateTime();
        $expiry = new DateTime($row['twofa_expiry']);
        if ($now > $expiry) return false;

        // Clear code after successful verification
        DB::conn()->prepare("UPDATE users SET twofa_code=NULL, twofa_expiry=NULL WHERE id=:id")->execute(['id' => $id]);
        return true;
    }

    /**
     * Update user password
     */
    public static function updatePassword(int $id, string $newPassword): bool
    {
        $hashed = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        $stmt = DB::conn()->prepare("UPDATE users SET password = :pass WHERE id = :id");
        return $stmt->execute(['pass' => $hashed, 'id' => $id]);
    }

    /**
     * Delete user (Admin)
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM users WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }
}
