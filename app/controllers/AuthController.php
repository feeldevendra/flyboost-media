<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Auth Controller
 * ------------------------------------------------------------
 * Handles user authentication:
 * - Login
 * - Register
 * - Logout
 * - Two-Factor (2FA) Verification
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\User;
use App\Models\Notification;
use App\Config;

class AuthController
{
    /**
     * Render login page
     */
    public function login(): void
    {
        if (isLoggedIn()) {
            redirect('/account');
        }

        $meta = [
            'title' => 'Login | ' . Config::SITE_NAME,
            'description' => 'Access your Flyboost Media account to track your projects and manage subscriptions.'
        ];

        view('auth/login', compact('meta'));
    }

    /**
     * Handle login submission
     */
    public function loginPost(): void
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (!$email || !$password) {
            echo json_encode(['success' => false, 'message' => 'Please enter both email and password.']);
            return;
        }

        $user = User::login($email, $password);

        if ($user) {
            // Generate and send 2FA code
            $otp = User::generate2FA($user['id']);
            Notification::sendEmail($user['email'], 'Your Flyboost 2FA Code', "Your verification code is: <b>$otp</b>");

            $_SESSION['pending_2fa'] = $user['id'];
            echo json_encode(['success' => true, 'redirect' => '/verify-2fa']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid credentials.']);
        }
    }

    /**
     * Render registration page
     */
    public function register(): void
    {
        if (isLoggedIn()) {
            redirect('/account');
        }

        $meta = [
            'title' => 'Register | ' . Config::SITE_NAME,
            'description' => 'Create a Flyboost Media account to access your client dashboard and manage projects.'
        ];

        view('auth/register', compact('meta'));
    }

    /**
     * Handle registration form
     */
    public function registerPost(): void
    {
        $data = [
            'name' => trim($_POST['name'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'password' => $_POST['password'] ?? '',
            'role' => 'CLIENT'
        ];

        if (!$data['name'] || !$data['email'] || !$data['password']) {
            echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
            return;
        }

        if (User::exists($data['email'])) {
            echo json_encode(['success' => false, 'message' => 'This email is already registered.']);
            return;
        }

        if (User::register($data)) {
            Notification::sendEmail($data['email'], 'Welcome to Flyboost Media', 'Your account has been created successfully!');
            echo json_encode(['success' => true, 'redirect' => '/login']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Registration failed. Please try again.']);
        }
    }

    /**
     * Verify 2FA code (OTP)
     */
    public function verify2FA(): void
    {
        $id = $_SESSION['pending_2fa'] ?? null;
        $code = trim($_POST['code'] ?? '');

        if (!$id || !$code) {
            echo json_encode(['success' => false, 'message' => 'Missing verification data.']);
            return;
        }

        if (User::verify2FA($id, $code)) {
            $user = User::find($id);
            $_SESSION['user'] = $user;
            unset($_SESSION['pending_2fa']);
            echo json_encode(['success' => true, 'redirect' => '/account']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid or expired code.']);
        }
    }

    /**
     * Logout and destroy session
     */
    public function logout(): void
    {
        session_destroy();
        redirect('/');
    }
}
