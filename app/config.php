<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Application Configuration
 * ------------------------------------------------------------
 * Define global constants, environment, and app-level settings.
 * This file is included at the start of every request.
 * ------------------------------------------------------------
 */

namespace App;

class Config
{
    /**
     * Database configuration
     * (Update these values after importing SQL on Hostinger)
     */
    const DB_HOST = 'localhost';      // Usually 'localhost' on Hostinger
    const DB_NAME = 'u665729447_f';    // Your database name
    const DB_USER = 'u665729447_f';  // Your database username
    const DB_PASS = 'Ansh_admin@12345';  // Your database password

    /**
     * Application paths and environment
     */
    const BASE_URL = '';              // Leave empty if installed in root, or '/subfolder'
    const SITE_NAME = 'Flyboost Media 360°';
    const DEBUG = true;               // Set false in production

    /**
     * Default timezone & locale
     */
    const TIMEZONE = 'Asia/Kolkata';
    const DEFAULT_LANG = 'en';

    /**
     * Email settings (used for notifications)
     * These can be overridden in admin panel
     */
    const MAIL_FROM_NAME = 'Flyboost Media';
    const MAIL_FROM_EMAIL = 'no-reply@flyboost.in';

    /**
     * File upload settings
     */
    const UPLOAD_DIR = '/uploads';
    const MAX_UPLOAD_SIZE = 5 * 1024 * 1024; // 5 MB limit
    const ALLOWED_FILE_TYPES = ['jpg', 'jpeg', 'png', 'gif', 'svg', 'pdf', 'webp'];

    /**
     * App meta data for SEO defaults
     */
    const DEFAULT_META_TITLE = 'Flyboost Media 360° | Digital Agency Platform';
    const DEFAULT_META_DESC = 'We build, scale, and automate digital success with web, app, and AI-driven marketing solutions.';
}
