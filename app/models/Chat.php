<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Chat Model
 * ------------------------------------------------------------
 * Handles all chat interactions:
 * - AI chatbot conversation logs
 * - Client ↔ Admin/Project Manager messaging
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Chat
{
    /**
     * Log chatbot messages for analytics
     */
    public static function logAIMessage(string $session_id, string $sender, string $message): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO chat_ai_logs 
            (session_id, sender, message, created_at) 
            VALUES (:sid, :sender, :msg, NOW())");
        return $stmt->execute([
            'sid' => $session_id,
            'sender' => strtoupper($sender),
            'msg' => $message
        ]);
    }

    /**
     * Retrieve chatbot conversation by session
     */
    public static function getAIConversation(string $session_id): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM chat_ai_logs 
                                     WHERE session_id=:sid ORDER BY created_at ASC");
        $stmt->execute(['sid' => $session_id]);
        return $stmt->fetchAll();
    }

    /**
     * Log a new client <-> admin chat message
     */
    public static function logMessage(int $project_id, int $user_id, string $message, ?string $file_path = null): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO chat_threads 
            (project_id, user_id, message, file_path, created_at) 
            VALUES (:pid, :uid, :msg, :file, NOW())");
        return $stmt->execute([
            'pid' => $project_id,
            'uid' => $user_id,
            'msg' => $message,
            'file' => $file_path
        ]);
    }

    /**
     * Retrieve all messages for a project
     */
    public static function getThread(int $project_id): array
    {
        $stmt = DB::conn()->prepare("SELECT ct.*, u.name AS sender_name, u.role AS sender_role 
                                     FROM chat_threads ct
                                     JOIN users u ON ct.user_id = u.id
                                     WHERE ct.project_id = :pid 
                                     ORDER BY ct.created_at ASC");
        $stmt->execute(['pid' => $project_id]);
        return $stmt->fetchAll();
    }

    /**
     * Mark all unread messages as seen
     */
    public static function markAsRead(int $project_id, int $user_id): void
    {
        $stmt = DB::conn()->prepare("UPDATE chat_threads 
                                     SET is_read = 1 
                                     WHERE project_id = :pid AND user_id != :uid");
        $stmt->execute(['pid' => $project_id, 'uid' => $user_id]);
    }

    /**
     * Get unread message count for notification badge
     */
    public static function unreadCount(int $user_id): int
    {
        $stmt = DB::conn()->prepare("SELECT COUNT(*) FROM chat_threads 
                                     WHERE is_read = 0 
                                     AND project_id IN (SELECT id FROM projects WHERE client_id = :uid OR manager_id = :uid)");
        $stmt->execute(['uid' => $user_id]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Delete chat thread (Admin cleanup)
     */
    public static function deleteThread(int $project_id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM chat_threads WHERE project_id=:pid");
        return $stmt->execute(['pid' => $project_id]);
    }
}
