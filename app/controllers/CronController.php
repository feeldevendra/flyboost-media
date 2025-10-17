<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Cron Controller
 * ------------------------------------------------------------
 * Handles all scheduled jobs, executed by Hostinger CRON.
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Automation;
use App\Models\Analytics;
use App\Models\Notification;
use App\Models\Backup;
use App\Env;

class CronController
{
    /**
     * Main CRON entry point
     * Example CRON URL: /cron/run?key=your_secret_key
     */
    public function run(): void
    {
        $key = $_GET['key'] ?? '';
        $secret = Env::get('CRON_SECRET_KEY', 'FLYBOOST123');

        if ($key !== $secret) {
            http_response_code(403);
            echo "🚫 Unauthorized access";
            return;
        }

        echo "🕒 Flyboost CRON Job Started at " . date('Y-m-d H:i:s') . "<br>";

        // 1️⃣ Run queued background jobs
        Automation::processQueue();
        echo "✅ Processed job queue<br>";

        // 2️⃣ Log analytics snapshot
        Analytics::logDailySnapshot();
        echo "📊 Analytics snapshot logged<br>";

        // 3️⃣ Schedule daily tasks (report, renewal, backup)
        Automation::scheduleDailyTasks();
        echo "🧭 Daily jobs re-queued<br>";

        // 4️⃣ Run database backup every 24h
        Backup::cleanup(15);
        echo "💾 Old backups cleaned<br>";

        // 5️⃣ Send system summary (optional)
        $msg = "Daily CRON Summary — " . date('d M Y H:i') . "\nJobs processed successfully.";
        Notification::sendWebhook('SLACK', $msg);
        Notification::sendWebhook('DISCORD', $msg);

        echo "📤 Summary notification sent<br>";
        echo "✅ All tasks completed successfully.";
    }
}
