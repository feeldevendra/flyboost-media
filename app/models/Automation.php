<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Automation Model
 * ------------------------------------------------------------
 * Manages all background tasks, CRON jobs, and queues:
 * - Daily backups
 * - Email reminders
 * - AI report generation
 * - Social autoposts
 * - Subscription renewals
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use App\Models\Blog;
use App\Models\Subscription;
use App\Models\Notification;
use App\Models\AI;
use App\Config;

class Automation
{
    /**
     * Register a background job
     */
    public static function queue(string $task, array $payload = []): bool
    {
        $stmt = DB::conn()->prepare("INSERT INTO job_queue (task, payload, status, created_at) 
                                     VALUES (:task, :payload, 'PENDING', NOW())");
        return $stmt->execute([
            'task' => strtoupper($task),
            'payload' => json_encode($payload)
        ]);
    }

    /**
     * Process queued jobs (called by CRON)
     */
    public static function processQueue(): void
    {
        $pdo = DB::conn();
        $stmt = $pdo->query("SELECT * FROM job_queue WHERE status='PENDING' ORDER BY created_at ASC LIMIT 10");
        $jobs = $stmt->fetchAll();

        foreach ($jobs as $job) {
            $task = $job['task'];
            $payload = json_decode($job['payload'], true);

            try {
                switch ($task) {
                    case 'AI_REPORT':
                        $report = AI::generateReport();
                        Notification::sendEmail(Config::MAIL_FROM_EMAIL, 'Daily AI Report', $report);
                        break;

                    case 'AUTORENEW_SUBSCRIPTIONS':
                        Subscription::autoRenew();
                        break;

                    case 'AUTOPUBLISH_BLOG':
                        if (!empty($payload['blog'])) {
                            Blog::autoPostSocial($payload['blog']);
                        }
                        break;

                    case 'EMAIL_REMINDER':
                        Notification::sendEmail($payload['to'], $payload['subject'], $payload['message']);
                        break;

                    case 'BACKUP_DATABASE':
                        self::backupDatabase();
                        break;
                }

                $pdo->prepare("UPDATE job_queue SET status='DONE', completed_at=NOW() WHERE id=:id")
                    ->execute(['id' => $job['id']]);

            } catch (\Throwable $e) {
                $pdo->prepare("UPDATE job_queue SET status='FAILED', error_message=:msg WHERE id=:id")
                    ->execute(['msg' => $e->getMessage(), 'id' => $job['id']]);
            }
        }
    }

    /**
     * Create a database backup (stored in /storage/backups)
     */
    public static function backupDatabase(): void
    {
        $backupDir = BASE_PATH . '/storage/backups/';
        if (!is_dir($backupDir)) mkdir($backupDir, 0775, true);

        $filename = 'backup_' . date('Ymd_His') . '.sql';
        $filePath = $backupDir . $filename;

        $cmd = sprintf(
            'mysqldump -h%s -u%s -p%s %s > %s',
            escapeshellarg(Config::DB_HOST),
            escapeshellarg(Config::DB_USER),
            escapeshellarg(Config::DB_PASS),
            escapeshellarg(Config::DB_NAME),
            escapeshellarg($filePath)
        );

        @exec($cmd);

        Notification::log('BACKUP', 'SYSTEM', "Database backup created: $filename");
    }

    /**
     * Schedule daily CRON jobs
     */
    public static function scheduleDailyTasks(): void
    {
        // Queue key daily jobs
        self::queue('AI_REPORT');
        self::queue('AUTORENEW_SUBSCRIPTIONS');
        self::queue('BACKUP_DATABASE');
    }

    /**
     * Cleanup old job logs
     */
    public static function cleanup(int $days = 7): void
    {
        $stmt = DB::conn()->prepare("DELETE FROM job_queue WHERE created_at < NOW() - INTERVAL :days DAY");
        $stmt->bindValue(':days', $days, \PDO::PARAM_INT);
        $stmt->execute();
    }
}
