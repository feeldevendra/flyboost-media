<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Database Connection (PDO)
 * ------------------------------------------------------------
 * Creates a single, reusable PDO connection.
 * All models interact through this class.
 * ------------------------------------------------------------
 */

namespace App;

use PDO;
use PDOException;

class DB
{
    private static ?PDO $pdo = null;

    /**
     * Establish and return a PDO database connection
     */
    public static function conn(): PDO
    {
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        try {
            $dsn = 'mysql:host=' . Config::DB_HOST . ';dbname=' . Config::DB_NAME . ';charset=utf8mb4';

            self::$pdo = new PDO(
                $dsn,
                Config::DB_USER,
                Config::DB_PASS,
                [
                    PDO::ATTR_ERRMODE => Config::DEBUG ? PDO::ERRMODE_EXCEPTION : PDO::ERRMODE_SILENT,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET time_zone = '" . Config::TIMEZONE . "'"
                ]
            );

        } catch (PDOException $e) {
            if (Config::DEBUG) {
                die('<b>Database Connection Failed:</b> ' . $e->getMessage());
            }
            die('Database error. Please try again later.');
        }

        return self::$pdo;
    }

    /**
     * Utility method to test DB connection (for admin diagnostics)
     */
    public static function testConnection(): string
    {
        try {
            self::conn()->query("SELECT 1");
            return '✅ Database connection successful.';
        } catch (PDOException $e) {
            return '❌ DB connection failed: ' . $e->getMessage();
        }
    }
}
