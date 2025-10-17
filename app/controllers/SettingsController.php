<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Settings Controller
 * ------------------------------------------------------------
 * Handles:
 * - General site settings
 * - SEO / Meta defaults
 * - API keys & integrations
 * - Chatbot / AdSense / Analytics configuration
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Setting;
use App\Models\Notification;
use App\Config;

class SettingsController
{
    /**
     * Render Settings Page
     */
    public function index(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $settings = Setting::all();
        $meta = [
            'title' => 'Site Settings | Admin',
            'description' => 'Manage all global configuration for Flyboost Media 360° — branding, integrations, and SEO.'
        ];

        view('admin/settings', compact('settings', 'meta'));
    }

    /**
     * Save settings (form submission)
     */
    public function save(): void
    {
        if (!hasRole('ADMIN')) {
            echo json_encode(['success' => false, 'message' => 'Unauthorized access.']);
            return;
        }

        $data = $_POST ?? [];

        // Bulk save all settings from admin form
        Setting::bulkSave($data);

        // Optional: update .env if sensitive values were changed
        self::updateEnvFile($data);

        // Notify admin success
        Notification::log('SETTINGS_UPDATE', 'ADMIN', 'Settings updated successfully.');

        echo json_encode(['success' => true, 'message' => '✅ Settings saved successfully.']);
    }

    /**
     * Update .env file for sensitive credentials
     */
    private static function updateEnvFile(array $data): void
    {
        $envFile = APP_PATH . '/.env';
        if (!file_exists($envFile)) return;

        $env = file_get_contents($envFile);

        $sensitiveKeys = [
            'CASHFREE_APP_ID', 'CASHFREE_SECRET_KEY', 'RAZORPAY_KEY_ID', 'RAZORPAY_SECRET',
            'STRIPE_KEY', 'STRIPE_SECRET', 'OPENAI_API_KEY', 'SMTP_HOST', 'SMTP_USER',
            'SMTP_PASS', 'GOOGLE_ANALYTICS_ID', 'SLACK_WEBHOOK_URL', 'DISCORD_WEBHOOK_URL',
            'WHATSAPP_TOKEN', 'WHATSAPP_NUMBER_ID', 'MATOMO_URL', 'MATOMO_TOKEN', 'CHATBOT_TYPE'
        ];

        foreach ($sensitiveKeys as $key) {
            if (isset($data[$key])) {
                $pattern = "/^$key=.*/m";
                $replacement = "$key=" . trim($data[$key]);
                if (preg_match($pattern, $env)) {
                    $env = preg_replace($pattern, $replacement, $env);
                } else {
                    $env .= "\n$replacement";
                }
            }
        }

        file_put_contents($envFile, $env);
    }
}
