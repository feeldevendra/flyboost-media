<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Contact Controller
 * ------------------------------------------------------------
 * Handles:
 * - Contact Us page rendering
 * - Lead form submission
 * - Multi-channel notifications
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Lead;
use App\Models\Notification;
use App\Config;

class ContactController
{
    /**
     * Display Contact Us page
     */
    public function index(): void
    {
        $meta = [
            'title' => 'Contact Us | ' . Config::SITE_NAME,
            'description' => 'Let’s talk about your next project. Get in touch with Flyboost Media for web development, marketing, and branding services.'
        ];

        view('contact/index', compact('meta'));
    }

    /**
     * Handle contact form submission
     */
    public function submit(): void
    {
        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'message' => $_POST['message'] ?? '',
            'service' => $_POST['service'] ?? 'General Inquiry',
            'source' => 'Contact Page',
            'referral_code' => $_COOKIE['ref'] ?? null
        ];

        if (Lead::create($data)) {
            // Send notifications (email + webhook)
            $msg = "📩 *New Contact Inquiry*\n"
                 . "Name: {$data['name']}\n"
                 . "Email: {$data['email']}\n"
                 . "Phone: {$data['phone']}\n"
                 . "Service: {$data['service']}\n"
                 . "Message: {$data['message']}\n";

            Notification::broadcast([
                'subject' => 'New Contact Inquiry — Flyboost Media',
                'message' => $msg,
                'email' => Config::MAIL_FROM_EMAIL
            ]);

            echo json_encode([
                'success' => true,
                'message' => '✅ Thank you! We’ll get back to you soon.'
            ]);
        } else {
            echo json_encode([
                'success' => false,
                'message' => '❌ Submission failed. Please try again later.'
            ]);
        }
    }
}
