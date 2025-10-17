<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Home Controller
 * ------------------------------------------------------------
 * Handles homepage rendering with dynamic data
 * (sections, services, blogs, testimonials, etc.)
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Config;
use App\DB;
use App\Models\Service;
use App\Models\Blog;
use App\Models\Portfolio;
use App\Models\Testimonial;
use App\Models\Setting;

class HomeController
{
    /**
     * Homepage view
     */
    public function index(): void
    {
        // Load dynamic homepage data
        $services = Service::getFeatured(6);
        $blogs = Blog::latest(3);
        $portfolio = Portfolio::featured(6);
        $testimonials = Testimonial::all();
        $settings = Setting::all();

        // SEO defaults
        $meta = [
            'title' => Config::DEFAULT_META_TITLE,
            'description' => Config::DEFAULT_META_DESC,
        ];

        // Pass data to the view
        view('home/index', compact('services', 'blogs', 'portfolio', 'testimonials', 'settings', 'meta'));
    }
}
