<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Admin Controller
 * ------------------------------------------------------------
 * Handles:
 * - Admin Dashboard (overview)
 * - Services management
 * - Blog CMS entry point
 * - Leads/CRM
 * - System logs and analytics
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Service;
use App\Models\Blog;
use App\Models\Lead;
use App\Models\Project;
use App\Models\User;
use App\Models\Referral;
use App\Models\Payment;
use App\Models\Notification;
use App\Models\Analytics;
use App\Models\Automation;
use App\Models\Backup;
use App\Config;

class AdminController
{
    /**
     * Admin Dashboard Overview
     */
    public function dashboard(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $stats = [
            'services'      => count(Service::all()),
            'blogs'         => count(Blog::all(9999)),
            'leads'         => count(Lead::all(999)),
            'projects'      => count(Project::all(999)),
            'clients'       => count(User::all(999)),
            'referrals'     => count(Referral::allWithStats()),
            'payments'      => count(Payment::all(999)),
        ];

        $analytics = Analytics::getSummary();
        $notifications = Notification::all(10);
        $recentBackups = Backup::listBackups();

        $meta = [
            'title' => 'Admin Dashboard | ' . Config::SITE_NAME,
            'description' => 'Manage all Flyboost Media content, automation, clients, and analytics from one dashboard.'
        ];

        view('admin/dashboard', compact('stats', 'analytics', 'notifications', 'recentBackups', 'meta'));
    }

    /**
     * Manage Services
     */
    public function services(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $services = Service::all();
        $meta = [
            'title' => 'Manage Services | Admin',
            'description' => 'Add, edit, or delete your Flyboost Media services.'
        ];

        view('admin/services', compact('services', 'meta'));
    }

    /**
     * Manage Leads / CRM
     */
    public function leads(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $leads = Lead::all(200);
        $meta = [
            'title' => 'Leads & Inquiries | Admin',
            'description' => 'View and manage client inquiries, quote requests, and contact submissions.'
        ];

        view('admin/leads', compact('leads', 'meta'));
    }

    /**
     * View and manage automation logs
     */
    public function automation(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        Automation::cleanup();
        $meta = [
            'title' => 'Automation & Cron Jobs | Admin',
            'description' => 'Monitor and manage background jobs, reports, and system tasks.'
        ];

        view('admin/automation', compact('meta'));
    }

    /**
     * View and manage notifications
     */
    public function notifications(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $notifications = Notification::all(100);
        $meta = [
            'title' => 'System Notifications | Admin',
            'description' => 'View all recent email, webhook, and system notifications.'
        ];

        view('admin/notifications', compact('notifications', 'meta'));
    }

    /**
     * Manage backups (manual or scheduled)
     */
    public function backups(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $backups = Backup::listBackups();
        $meta = [
            'title' => 'System Backups | Admin',
            'description' => 'Create, download, or delete system backups.'
        ];

        view('admin/backups', compact('backups', 'meta'));
    }

    /**
     * Create new manual backup
     */
    public function createBackup(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $path = Backup::createFullBackup();
        echo json_encode(['success' => true, 'message' => 'Backup created successfully.', 'file' => basename($path)]);
    }

    /**
     * Delete a backup
     */
    public function deleteBackup(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $filename = $_POST['filename'] ?? '';
        if (Backup::delete($filename)) {
            echo json_encode(['success' => true, 'message' => 'Backup deleted.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete backup.']);
        }
    }

    /**
     * System diagnostics page (for debugging & server checks)
     */
    public function diagnostics(): void
    {
        if (!hasRole('ADMIN')) redirect('/login');

        $meta = [
            'title' => 'System Diagnostics | Admin',
            'description' => 'Check system configuration, PHP version, database connection, and cron status.'
        ];

        $env = \App\Env::all();
        $dbStatus = \App\DB::testConnection();

        view('admin/diagnostics', compact('meta', 'env', 'dbStatus'));
    }
}
