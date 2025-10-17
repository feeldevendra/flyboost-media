<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Media Model
 * ------------------------------------------------------------
 * Handles upload, retrieval, and deletion of media files
 * (images, PDFs, videos, and documents).
 * Integrated with Admin → Media Library.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use App\Config;
use PDO;

class Media
{
    /**
     * Fetch all media files
     */
    public static function all(int $limit = 200): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM media ORDER BY uploaded_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Upload a new media file entry
     */
    public static function add(array $data): bool
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO media 
            (filename, filepath, filetype, uploaded_by, uploaded_at)
            VALUES (:filename, :filepath, :filetype, :uploaded_by, NOW())");

        return $stmt->execute([
            'filename' => $data['filename'],
            'filepath' => $data['filepath'],
            'filetype' => $data['filetype'],
            'uploaded_by' => $data['uploaded_by']
        ]);
    }

    /**
     * Delete a media record and remove file from disk
     */
    public static function delete(int $id): bool
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT filepath FROM media WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $file = $stmt->fetchColumn();

        if ($file && file_exists(PUBLIC_PATH . $file)) {
            unlink(PUBLIC_PATH . $file);
        }

        $stmt = $pdo->prepare("DELETE FROM media WHERE id = :id");
        return $stmt->execute(['id' => $id]);
    }

    /**
     * Upload handler (validates and moves uploaded file)
     */
    public static function handleUpload(array $file, int $user_id): ?string
    {
        if ($file['error'] !== UPLOAD_ERR_OK) {
            return null;
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, Config::ALLOWED_FILE_TYPES)) {
            return null;
        }

        $uploadDir = PUBLIC_PATH . Config::UPLOAD_DIR;
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = uniqid('media_') . '.' . $ext;
        $target = $uploadDir . '/' . $filename;

        if (move_uploaded_file($file['tmp_name'], $target)) {
            $relPath = Config::UPLOAD_DIR . '/' . $filename;
            self::add([
                'filename' => $file['name'],
                'filepath' => $relPath,
                'filetype' => $ext,
                'uploaded_by' => $user_id
            ]);
            return $relPath;
        }

        return null;
    }

    /**
     * Fetch media by ID
     */
    public static function find(int $id): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM media WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $media = $stmt->fetch();
        return $media ?: null;
    }
}
