<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Account Controller
 * ------------------------------------------------------------
 * Handles client portal features:
 * - Dashboard overview
 * - Project list & details
 * - Messages (chat threads)
 * - Subscriptions & invoices
 * - Profile updates
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Project;
use App\Models\Subscription;
use App\Models\Payment;
use App\Models\Chat;
use App\Models\User;
use App\Config;

class AccountController
{
    /**
     * Render dashboard (overview)
     */
    public function dashboard(): void
    {
        if (!isLoggedIn()) {
            redirect('/login');
        }

        $user = user();
        $projects = Project::byClient($user['id']);
        $subscriptions = Subscription::byUser($user['id']);
        $payments = Payment::all(20);
        $unreadCount = Chat::unreadCount($user['id']);

        $meta = [
            'title' => 'My Dashboard | ' . Config::SITE_NAME,
            'description' => 'Manage your projects, messages, and billing through the Flyboost Media client portal.'
        ];

        view('account/dashboard', compact('user', 'projects', 'subscriptions', 'payments', 'unreadCount', 'meta'));
    }

    /**
     * View a single project with milestones, files, and discussions
     */
    public function project(string $id): void
    {
        if (!isLoggedIn()) redirect('/login');

        $user = user();
        $project = Project::find((int)$id);

        if (!$project || ($project['client_id'] != $user['id'] && !hasRole('ADMIN') && !hasRole('PROJECT_MANAGER'))) {
            http_response_code(403);
            view('errors/403');
            return;
        }

        $meta = [
            'title' => $project['title'] . ' | My Project',
            'description' => substr(strip_tags($project['description']), 0, 160)
        ];

        view('account/project', compact('project', 'meta'));
    }

    /**
     * View chat messages for a project
     */
    public function messages(string $id): void
    {
        if (!isLoggedIn()) redirect('/login');

        $user = user();
        $project = Project::find((int)$id);

        if (!$project) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $messages = Chat::getThread($project['id']);
        Chat::markAsRead($project['id'], $user['id']);

        $meta = [
            'title' => 'Messages | ' . Config::SITE_NAME,
            'description' => 'Collaborate with your Flyboost Media project team.'
        ];

        view('account/messages', compact('project', 'messages', 'meta'));
    }

    /**
     * Handle message posting (AJAX)
     */
    public function sendMessage(string $id): void
    {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first.']);
            return;
        }

        $user = user();
        $message = trim($_POST['message'] ?? '');
        $file = $_FILES['file'] ?? null;
        $filePath = null;

        if ($file && $file['tmp_name']) {
            $uploadDir = PUBLIC_PATH . '/uploads/chat/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0775, true);

            $filename = uniqid('chat_') . '_' . basename($file['name']);
            move_uploaded_file($file['tmp_name'], $uploadDir . $filename);
            $filePath = '/uploads/chat/' . $filename;
        }

        Chat::logMessage((int)$id, $user['id'], $message, $filePath);
        echo json_encode(['success' => true, 'message' => 'Message sent successfully!']);
    }

    /**
     * Profile settings view
     */
    public function profile(): void
    {
        if (!isLoggedIn()) redirect('/login');
        $user = user();

        $meta = [
            'title' => 'My Profile | ' . Config::SITE_NAME,
            'description' => 'Update your account information and preferences.'
        ];

        view('account/profile', compact('user', 'meta'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(): void
    {
        if (!isLoggedIn()) {
            echo json_encode(['success' => false, 'message' => 'Please login first.']);
            return;
        }

        $user = user();
        $name = trim($_POST['name'] ?? '');
        $password = $_POST['password'] ?? null;

        if ($name) {
            $pdo = \App\DB::conn();
            $stmt = $pdo->prepare("UPDATE users SET name=:name WHERE id=:id");
            $stmt->execute(['name' => $name, 'id' => $user['id']]);
            $_SESSION['user']['name'] = $name;
        }

        if ($password) {
            User::updatePassword($user['id'], $password);
        }

        echo json_encode(['success' => true, 'message' => 'Profile updated successfully.']);
    }
}
