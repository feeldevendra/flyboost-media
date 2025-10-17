<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Setting Model
 * ------------------------------------------------------------
 * Manages all global site settings (key-value store)
 * for editable content, integrations, and configurations.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Setting
{
    /**
     * Get a setting by key
     */
    public static function get(string $key, $default = null)
    {
        $stmt = DB::conn()->prepare("SELECT value FROM settings WHERE `key` = :key LIMIT 1");
        $stmt->execute(['key' => $key]);
        $result = $stmt->fetchColumn();
        return $result !== false ? $result : $default;
    }

    /**
     * Get all settings as an associative array
     */
    public static function all(): array
    {
        $stmt = DB::conn()->query("SELECT `key`, `value` FROM settings");
        $rows = $stmt->fetchAll();
        $settings = [];
        foreach ($rows as $row) {
            $settings[$row['key']] = $row['value'];
        }
        return $settings;
    }

    /**
     * Save or update a setting
     */
    public static function save(string $key, $value): bool
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO settings (`key`, `value`) VALUES (:key, :value)
                               ON DUPLICATE KEY UPDATE `value` = :value2");
        return $stmt->execute([
            'key' => $key,
            'value' => $value,
            'value2' => $value
        ]);
    }

    /**
     * Bulk update multiple settings at once
     */
    public static function bulkSave(array $data): void
    {
        foreach ($data as $key => $value) {
            self::save($key, $value);
        }
    }

    /**
     * Delete a setting by key
     */
    public static function delete(string $key): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM settings WHERE `key` = :key");
        return $stmt->execute(['key' => $key]);
    }
}
