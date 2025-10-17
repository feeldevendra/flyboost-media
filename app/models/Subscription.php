<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Subscription Model
 * ------------------------------------------------------------
 * Manages recurring plans, client subscriptions,
 * and automated renewals (Cashfree, Razorpay, Stripe).
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;
use App\Models\Payment;

class Subscription
{
    /**
     * Create a new subscription record
     */
    public static function create(array $data): int
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO subscriptions
            (user_id, plan_name, plan_code, amount, interval_unit, interval_count, 
             next_billing_date, gateway, status, created_at)
             VALUES (:uid, :pname, :pcode, :amount, :unit, :count, :next_date, :gateway, 'ACTIVE', NOW())");
        $stmt->execute([
            'uid' => $data['user_id'],
            'pname' => $data['plan_name'],
            'pcode' => $data['plan_code'],
            'amount' => $data['amount'],
            'unit' => $data['interval_unit'],
            'count' => $data['interval_count'],
            'next_date' => $data['next_billing_date'],
            'gateway' => $data['gateway']
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Fetch all active subscriptions
     */
    public static function allActive(): array
    {
        $stmt = DB::conn()->query("SELECT * FROM subscriptions WHERE status = 'ACTIVE' ORDER BY next_billing_date ASC");
        return $stmt->fetchAll();
    }

    /**
     * Get subscriptions for a specific user
     */
    public static function byUser(int $user_id): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM subscriptions WHERE user_id = :uid ORDER BY created_at DESC");
        $stmt->execute(['uid' => $user_id]);
        return $stmt->fetchAll();
    }

    /**
     * Update subscription status (Admin or webhook)
     */
    public static function updateStatus(int $id, string $status): bool
    {
        $stmt = DB::conn()->prepare("UPDATE subscriptions SET status = :status, updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['status' => strtoupper($status), 'id' => $id]);
    }

    /**
     * Cancel a subscription (manual or via API)
     */
    public static function cancel(int $id, string $reason = 'User Requested'): bool
    {
        $stmt = DB::conn()->prepare("UPDATE subscriptions 
            SET status='CANCELLED', cancel_reason=:reason, cancelled_at=NOW() WHERE id=:id");
        return $stmt->execute(['reason' => $reason, 'id' => $id]);
    }

    /**
     * Renew subscription automatically (cron job)
     */
    public static function autoRenew(): void
    {
        $pdo = DB::conn();
        $today = date('Y-m-d');
        $stmt = $pdo->prepare("SELECT * FROM subscriptions WHERE next_billing_date <= :today AND status='ACTIVE'");
        $stmt->execute(['today' => $today]);
        $subs = $stmt->fetchAll();

        foreach ($subs as $sub) {
            $orderId = Payment::generateOrderId();
            Payment::create([
                'user_id' => $sub['user_id'],
                'order_id' => $orderId,
                'gateway' => $sub['gateway'],
                'amount' => $sub['amount'],
                'currency' => 'INR',
                'description' => 'Subscription Renewal - ' . $sub['plan_name']
            ]);

            // Update next billing date
            $interval = "{$sub['interval_count']} {$sub['interval_unit']}";
            $pdo->prepare("UPDATE subscriptions 
                SET next_billing_date = DATE_ADD(next_billing_date, INTERVAL $interval), 
                    last_renewal_date = NOW()
                WHERE id = :id")->execute(['id' => $sub['id']]);
        }
    }

    /**
     * Delete inactive subscriptions (optional cleanup)
     */
    public static function cleanup(int $days = 90): void
    {
        $stmt = DB::conn()->prepare("DELETE FROM subscriptions 
                                     WHERE status='CANCELLED' 
                                     AND cancelled_at < NOW() - INTERVAL :days DAY");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
    }
}
