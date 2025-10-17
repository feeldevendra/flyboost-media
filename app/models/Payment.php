<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Payment Model
 * ------------------------------------------------------------
 * Handles all payment records, order tracking,
 * webhook updates, and invoice generation.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;
use App\Config;

class Payment
{
    /**
     * Create a new order record before redirecting to gateway
     */
    public static function create(array $data): int
    {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("INSERT INTO payments 
            (user_id, order_id, gateway, amount, currency, status, description, created_at)
            VALUES (:user_id, :order_id, :gateway, :amount, :currency, 'PENDING', :description, NOW())");
        $stmt->execute([
            'user_id' => $data['user_id'],
            'order_id' => $data['order_id'],
            'gateway' => $data['gateway'],
            'amount' => $data['amount'],
            'currency' => $data['currency'] ?? 'INR',
            'description' => $data['description']
        ]);
        return (int) $pdo->lastInsertId();
    }

    /**
     * Update payment status (e.g. after webhook)
     */
    public static function updateStatus(string $order_id, string $status, string $txn_id = null, ?string $response = null): bool
    {
        $stmt = DB::conn()->prepare("UPDATE payments 
            SET status = :status, transaction_id = :txn, response_data = :resp, updated_at = NOW() 
            WHERE order_id = :order_id");
        return $stmt->execute([
            'status' => strtoupper($status),
            'txn' => $txn_id,
            'resp' => $response,
            'order_id' => $order_id
        ]);
    }

    /**
     * Find payment by order ID
     */
    public static function findByOrder(string $order_id): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM payments WHERE order_id = :oid LIMIT 1");
        $stmt->execute(['oid' => $order_id]);
        $payment = $stmt->fetch();
        return $payment ?: null;
    }

    /**
     * Fetch all payments (Admin Dashboard)
     */
    public static function all(int $limit = 100): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM payments ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Generate a unique order ID
     */
    public static function generateOrderId(): string
    {
        return 'FBM' . strtoupper(bin2hex(random_bytes(4))) . time();
    }

    /**
     * Generate an invoice PDF (placeholder — actual PDF generator later)
     */
    public static function generateInvoice(array $payment): string
    {
        $invoiceDir = BASE_PATH . '/storage/invoices/';
        if (!is_dir($invoiceDir)) {
            mkdir($invoiceDir, 0775, true);
        }

        $filename = 'invoice_' . $payment['order_id'] . '.txt';
        $filePath = $invoiceDir . $filename;

        $content = "Invoice - Flyboost Media 360°\n";
        $content .= "-----------------------------------\n";
        $content .= "Order ID: {$payment['order_id']}\n";
        $content .= "Transaction ID: {$payment['transaction_id']}\n";
        $content .= "Gateway: {$payment['gateway']}\n";
        $content .= "Amount: {$payment['amount']} {$payment['currency']}\n";
        $content .= "Status: {$payment['status']}\n";
        $content .= "Date: {$payment['created_at']}\n";
        $content .= "-----------------------------------\n";
        $content .= "Thank you for your business!\n";

        file_put_contents($filePath, $content);
        return $filePath;
    }

    /**
     * Log webhook callback data
     */
    public static function logWebhook(string $gateway, string $payload): void
    {
        $dir = BASE_PATH . '/storage/webhooks/';
        if (!is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $file = $dir . $gateway . '_' . date('Ymd_His') . '.log';
        file_put_contents($file, $payload);
    }

    /**
     * Delete old or test payments (Admin cleanup)
     */
    public static function cleanup(int $days = 30): void
    {
        $stmt = DB::conn()->prepare("DELETE FROM payments WHERE created_at < NOW() - INTERVAL :days DAY AND status = 'PENDING'");
        $stmt->bindValue(':days', $days, PDO::PARAM_INT);
        $stmt->execute();
    }
}
