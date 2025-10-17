<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Backup Model
 * ------------------------------------------------------------
 * Handles database and file system backups.
 * Supports manual and scheduled backups (via CRON).
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\Config;
use App\Models\Notification;

class Backup
{
    /**
     * Create a full backup (database + uploads)
     */
    public static function createFullBackup(): string
    {
        $backupDir = BASE_PATH . '/storage/backups/';
        if (!is_dir($backupDir)) mkdir($backupDir, 0775, true);

        $timestamp = date('Ymd_His');
        $dbFile = $backupDir . "db_{$timestamp}.sql";
        $zipFile = $backupDir . "flyboost_backup_{$timestamp}.zip";

        // 1️⃣ Database dump
        $cmd = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            escapeshellarg(Config::DB_HOST),
            escapeshellarg(Config::DB_USER),
            escapeshellarg(Config::DB_PASS),
            escapeshellarg(Config::DB_NAME),
            escapeshellarg($dbFile)
        );
        @exec($cmd);

        // 2️⃣ Zip database + uploads folder
        $uploadsDir = PUBLIC_PATH . Config::UPLOAD_DIR;
        $zip = new \ZipArchive();
        if ($zip->open($zipFile, \ZipArchive::CREATE) === true) {
            $zip->addFile($dbFile, basename($dbFile));
            self::addFolderToZip($zip, $uploadsDir, 'uploads');
            $zip->close();
        }

        // Remove SQL dump after zipping
        @unlink($dbFile);

        Notification::log('BACKUP', 'SYSTEM', "Full backup created: " . basename($zipFile));
        return $zipFile;
    }

    /**
     * Add folder contents recursively to ZIP
     */
    private static function addFolderToZip(\ZipArchive $zip, string $folder, string $subfolder): void
    {
        if (!is_dir($folder)) return;
        $files = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($folder, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($files as $file) {
            $localPath = $subfolder . '/' . str_replace($folder . '/', '', $file->getPathname());
            if ($file->isDir()) {
                $zip->addEmptyDir($localPath);
            } else {
                $zip->addFile($file->getPathname(), $localPath);
            }
        }
    }

    /**
     * List all backups available in storage
     */
    public static function listBackups(): array
    {
        $backupDir = BASE_PATH . '/storage/backups/';
        if (!is_dir($backupDir)) return [];

        $files = glob($backupDir . '*.zip');
        $backups = [];
        foreach ($files as $file) {
            $backups[] = [
                'name' => basename($file),
                'size' => round(filesize($file) / 1024 / 1024, 2) . ' MB',
                'date' => date('Y-m-d H:i:s', filemtime($file)),
                'path' => $file
            ];
        }
        return array_reverse($backups);
    }

    /**
     * Delete a specific backup
     */
    public static function delete(string $filename): bool
    {
        $filePath = BASE_PATH . '/storage/backups/' . basename($filename);
        if (file_exists($filePath)) {
            unlink($filePath);
            Notification::log('BACKUP_DELETE', 'SYSTEM', "Deleted backup: " . basename($filePath));
            return true;
        }
        return false;
    }

    /**
     * Clean up old backups (older than X days)
     */
    public static function cleanup(int $days = 15): void
    {
        $backupDir = BASE_PATH . '/storage/backups/';
        if (!is_dir($backupDir)) return;

        foreach (glob($backupDir . '*.zip') as $file) {
            if (filemtime($file) < (time() - ($days * 86400))) {
                unlink($file);
            }
        }
    }
}
