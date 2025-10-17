<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Service Controller
 * ------------------------------------------------------------
 * Handles:
 * - Services listing page
 * - Individual service detail
 * - Get Quote form submission
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Service;
use App\Models\Lead;
use App\Models\Notification;
use App\Config;

class ServiceController
{
    /**
     * List all services
     */
    public function list(): void
    {
        $services = Service::all();
        $meta = [
            'title' => 'Our Services | ' . Config::SITE_NAME,
            'description' => 'Explore Flyboost Media’s complete digital solutions — from website design to marketing automation and app development.'
        ];

        view('services/index', compact('services', 'meta'));
    }

    /**
     * Show service detail page
     */
    public function detail(string $slug): void
    {
        $service = Service::findBySlug($slug);

        if (!$service) {
            http_response_code(404);
            view('errors/404');
            return;
        }

        $options = Service::getOptions($service['id']);
        $meta = [
            'title' => $service['title'] . ' | ' . Config::SITE_NAME,
            'description' => substr(strip_tags($service['description']), 0, 150)
        ];

        view('services/detail', compact('service', 'options', 'meta'));
    }

    /**
     * Handle Get Quote form submission
     */
    public function quote(string $slug): void
    {
        $service = Service::findBySlug($slug);
        if (!$service) {
            http_response_code(404);
            echo json_encode(['success' => false, 'message' => 'Service not found']);
            return;
        }

        $data = [
            'name' => $_POST['name'] ?? '',
            'email' => $_POST['email'] ?? '',
            'phone' => $_POST['phone'] ?? '',
            'message' => $_POST['message'] ?? '',
            'service' => $service['title'],
            'source' => 'Get Quote',
            'referral_code' => $_COOKIE['ref'] ?? null
        ];

        if (Lead::create($data)) {
            // Trigger notifications
            Notification::broadcast([
                'subject' => 'New Quote Request — ' . $service['title'],
                'message' => "New quote request received for {$service['title']}.\nName: {$data['name']}\nEmail: {$data['email']}\nPhone: {$data['phone']}\nMessage: {$data['message']}"
            ]);

            echo json_encode(['success' => true, 'message' => 'Thank you! Our team will contact you shortly.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to submit request. Please try again.']);
        }
    }
}
