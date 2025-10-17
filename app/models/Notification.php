<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Notification Model
 * ------------------------------------------------------------
 * Sends and logs notifications across multiple channels:
 * Email, Slack, Discord, and WhatsApp Cloud API.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use App\Env;
use App\Config;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Notification
{
    /**
     * Send Email Notification via SMTP
     */
    public static function sendEmail(string $to, string $subject, string $message): bool
    {
        $mail = new PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host       = Env::get('SMTP_HOST', 'smtp.hostinger.com');
            $mail->SMTPAuth   = true;
            $mail->Username   = Env::get('SMTP_USER');
            $mail->Password   = Env::get('SMTP_PASS');
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = Env::get('SMTP_PORT', 587);

            $mail->setFrom(Env::get('SMTP_FROM', Config::MAIL_FROM_EMAIL), Config::MAIL_FROM_NAME);
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($message);

            $mail->send();
            self::log('EMAIL', $to, $subject);
            return true;
        } catch (Exception $e) {
            self::log('EMAIL_ERROR', $to, $e->getMessage());
            return false;
        }
    }

    /**
     * Send Slack or Discord notification
     */
    public static function sendWebhook(string $type, string $message): bool
    {
        $url = Env::get($type === 'SLACK' ? 'SLACK_WEBHOOK_URL' : 'DISCORD_WEBHOOK_URL');
        if (!$url) return false;

        $payload = json_encode(['text' => $message]);
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        curl_close($ch);

        self::log($type, $url, substr($message, 0, 120));
        return (bool)$res;
    }

    /**
     * Send WhatsApp Cloud API notification
     */
    public static function sendWhatsApp(string $phone, string $message): bool
    {
        $token = Env::get('WHATSAPP_TOKEN');
        $fromNumber = Env::get('WHATSAPP_NUMBER_ID');
        if (!$token || !$fromNumber) return false;

        $url = "https://graph.facebook.com/v17.0/{$fromNumber}/messages";
        $data = [
            'messaging_product' => 'whatsapp',
            'to' => $phone,
            'type' => 'text',
            'text' => ['body' => $message]
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
            'Authorization: Bearer ' . $token
        ]);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $response = curl_exec($ch);
        curl_close($ch);

        self::log('WHATSAPP', $phone, substr($message, 0, 120));
        return (bool)$response;
    }

    /**
     * Log notifications for audit trail
     */
    public static function log(string $channel, string $recipient, string $message): void
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO notifications 
            (channel, recipient, message, created_at) 
            VALUES (:channel, :recipient, :msg, NOW())");
        $stmt->execute([
            'channel' => strtoupper($channel),
            'recipient' => $recipient,
            'msg' => $message
        ]);
    }

    /**
     * Retrieve all notification logs (Admin panel)
     */
    public static function all(int $limit = 200): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM notifications ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, \PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Trigger multi-channel notification
     */
    public static function broadcast(array $data): void
    {
        $msg = $data['message'] ?? '';
        $email = $data['email'] ?? null;
        $phone = $data['phone'] ?? null;
        $slack = Env::get('SLACK_WEBHOOK_URL');
        $discord = Env::get('DISCORD_WEBHOOK_URL');

        if ($email) self::sendEmail($email, $data['subject'] ?? 'Notification', $msg);
        if ($phone) self::sendWhatsApp($phone, $msg);
        if ($slack) self::sendWebhook('SLACK', $msg);
        if ($discord) self::sendWebhook('DISCORD', $msg);
    }
}
