<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Lead Model
 * ------------------------------------------------------------
 * Handles all contact form and quote inquiries.
 * Integrated with CRM, notifications, and automation.
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\DB;
use PDO;

class Lead
{
    /**
     * Save a new lead from contact or quote form
     */
    public static function create(array $data): bool
    {
        $pdo = DB::conn();

        $stmt = $pdo->prepare("INSERT INTO leads 
            (name, email, phone, service, message, source, status, referral_code, created_at)
            VALUES (:name, :email, :phone, :service, :message, :source, :status, :referral_code, NOW())");

        return $stmt->execute([
            'name' => $data['name'] ?? '',
            'email' => $data['email'] ?? '',
            'phone' => $data['phone'] ?? '',
            'service' => $data['service'] ?? '',
            'message' => $data['message'] ?? '',
            'source' => $data['source'] ?? 'website',
            'status' => 'NEW',
            'referral_code' => $data['referral_code'] ?? null
        ]);
    }

    /**
     * Retrieve all leads (for Admin CRM)
     */
    public static function all(int $limit = 100): array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM leads ORDER BY created_at DESC LIMIT :limit");
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Get a specific lead by ID
     */
    public static function find(int $id): ?array
    {
        $stmt = DB::conn()->prepare("SELECT * FROM leads WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => $id]);
        $lead = $stmt->fetch();
        return $lead ?: null;
    }

    /**
     * Update lead status (Admin CRM)
     */
    public static function updateStatus(int $id, string $status): bool
    {
        $stmt = DB::conn()->prepare("UPDATE leads SET status = :status, updated_at = NOW() WHERE id = :id");
        return $stmt->execute(['status' => strtoupper($status), 'id' => $id]);
    }

    /**
     * Export all leads to CSV
     */
    public static function exportCSV(): string
    {
        $leads = self::all(5000);
        $csv = "Name,Email,Phone,Service,Message,Source,Status,Date\n";
        foreach ($leads as $l) {
            $csv .= sprintf(
                "\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\",\"%s\"\n",
                $l['name'], $l['email'], $l['phone'], $l['service'],
                str_replace(["\n", "\r"], ' ', $l['message']),
                $l['source'], $l['status'], $l['created_at']
            );
        }
        $filePath = BASE_PATH . '/storage/leads_export_' . date('Ymd_His') . '.csv';
        if (!is_dir(dirname($filePath))) {
            mkdir(dirname($filePath), 0775, true);
        }
        file_put_contents($filePath, $csv);
        return $filePath;
    }

    /**
     * Delete a lead (optional cleanup)
     */
    public static function delete(int $id): bool
    {
        $stmt = DB::conn()->prepare("DELETE FROM leads WHERE id=:id");
        return $stmt->execute(['id' => $id]);
    }
}
