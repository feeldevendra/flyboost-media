<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — Payment Controller
 * ------------------------------------------------------------
 * Handles:
 * - Payment initialization (checkout)
 * - Success callback
 * - Webhook verification
 * - Gateway abstraction (Cashfree, Razorpay, Stripe)
 * ------------------------------------------------------------
 */

namespace App\Controllers;

use App\Models\Payment;
use App\Models\Notification;
use App\Env;
use App\Config;

class PaymentController
{
    /**
     * Initialize payment
     */
    public function pay(string $order_id): void
    {
        if (!isLoggedIn()) redirect('/login');

        $payment = Payment::findByOrder($order_id);
        if (!$payment) {
            echo "❌ Invalid Order ID";
            return;
        }

        $gateway = strtolower($payment['gateway']);
        $amount = number_format((float)$payment['amount'], 2, '.', '');
        $currency = $payment['currency'] ?? 'INR';
        $user = user();

        switch ($gateway) {
            case 'cashfree':
                $appId = Env::get('CASHFREE_APP_ID');
                $secret = Env::get('CASHFREE_SECRET_KEY');
                $env = Env::get('CASHFREE_ENV', 'PROD');
                $baseUrl = $env === 'PROD'
                    ? 'https://api.cashfree.com/pg/orders'
                    : 'https://sandbox.cashfree.com/pg/orders';

                $orderPayload = [
                    'order_id' => $order_id,
                    'order_amount' => $amount,
                    'order_currency' => $currency,
                    'customer_details' => [
                        'customer_id' => $user['id'],
                        'customer_email' => $user['email'],
                        'customer_phone' => $user['phone'] ?? '9999999999'
                    ],
                    'order_note' => 'Flyboost Media Payment',
                    'return_url' => Config::BASE_URL . '/payment-success?order_id=' . $order_id,
                ];

                $ch = curl_init($baseUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_POST => true,
                    CURLOPT_HTTPHEADER => [
                        'Content-Type: application/json',
                        "x-client-id: $appId",
                        "x-client-secret: $secret"
                    ],
                    CURLOPT_POSTFIELDS => json_encode($orderPayload)
                ]);
                $response = json_decode(curl_exec($ch), true);
                curl_close($ch);

                if (!empty($response['payment_link'])) {
                    header('Location: ' . $response['payment_link']);
                    exit;
                }

                echo "❌ Failed to initiate Cashfree payment.";
                break;

            case 'razorpay':
                $key = Env::get('RAZORPAY_KEY_ID');
                $amountPaise = $amount * 100;
                echo "<script src='https://checkout.razorpay.com/v1/checkout.js'
                    data-key='$key'
                    data-amount='$amountPaise'
                    data-currency='$currency'
                    data-order_id='$order_id'
                    data-name='Flyboost Media'
                    data-description='Service Payment'
                    data-prefill.name='{$user['name']}'
                    data-prefill.email='{$user['email']}'
                    data-theme.color='#0077ff'></script>";
                break;

            case 'stripe':
                $pubKey = Env::get('STRIPE_KEY');
                $secret = Env::get('STRIPE_SECRET');
                $sessionUrl = "https://api.stripe.com/v1/checkout/sessions";

                $ch = curl_init($sessionUrl);
                curl_setopt_array($ch, [
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_USERPWD => "$secret:",
                    CURLOPT_POST => true,
                    CURLOPT_POSTFIELDS => http_build_query([
                        'payment_method_types[]' => 'card',
                        'mode' => 'payment',
                        'line_items[0][price_data][currency]' => $currency,
                        'line_items[0][price_data][product_data][name]' => 'Flyboost Media Services',
                        'line_items[0][price_data][unit_amount]' => (int) ($amount * 100),
                        'line_items[0][quantity]' => 1,
                        'success_url' => Config::BASE_URL . '/payment-success?order_id=' . $order_id,
                        'cancel_url' => Config::BASE_URL . '/account'
                    ])
                ]);
                $res = json_decode(curl_exec($ch), true);
                curl_close($ch);

                if (!empty($res['url'])) {
                    header('Location: ' . $res['url']);
                    exit;
                }

                echo "❌ Stripe session failed.";
                break;

            default:
                echo "❌ Unknown payment gateway.";
        }
    }

    /**
     * Payment success callback
     */
    public function success(): void
    {
        $order_id = $_GET['order_id'] ?? '';
        $payment = Payment::findByOrder($order_id);

        if (!$payment) {
            view('errors/404');
            return;
        }

        Payment::updateStatus($order_id, 'SUCCESS', uniqid('TXN'));
        Notification::broadcast([
            'subject' => '💰 Payment Successful',
            'message' => "Order ID: $order_id\nAmount: ₹{$payment['amount']}\nUser: {$payment['user_id']}"
        ]);

        $meta = [
            'title' => 'Payment Success | ' . Config::SITE_NAME,
            'description' => 'Your payment was processed successfully.'
        ];

        view('payment/success', compact('payment', 'meta'));
    }

    /**
     * Webhook for asynchronous payment updates
     */
    public function webhook(): void
    {
        $payload = file_get_contents('php://input');
        Payment::logWebhook('cashfree', $payload);

        $data = json_decode($payload, true);
        if (!empty($data['order_id']) && !empty($data['order_status'])) {
            $status = strtoupper($data['order_status']);
            Payment::updateStatus($data['order_id'], $status, $data['cf_payment_id'] ?? '');
        }

        http_response_code(200);
        echo json_encode(['success' => true]);
    }
}
