<?php
/**
 * ------------------------------------------------------------
 * Flyboost Media 360° — AI Model
 * ------------------------------------------------------------
 * Centralized AI integration handler.
 * Includes:
 *  - AI Quote Assistant (OpenAI GPT)
 *  - AI Marketing Report Generator (GA4 + Matomo insights)
 *  - Chatbot (Dialogflow or Custom AI)
 * ------------------------------------------------------------
 */

namespace App\Models;

use App\Env;
use App\Models\Analytics;

class AI
{
    /**
     * Send prompt to OpenAI API
     */
    private static function openAIRequest(string $prompt, string $model = 'gpt-3.5-turbo'): ?string
    {
        $apiKey = Env::get('OPENAI_API_KEY');
        if (!$apiKey) return '⚠️ OpenAI API key not configured.';

        $data = [
            'model' => $model,
            'messages' => [['role' => 'user', 'content' => $prompt]],
            'temperature' => 0.7,
        ];

        $ch = curl_init('https://api.openai.com/v1/chat/completions');
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $apiKey
            ],
            CURLOPT_POSTFIELDS => json_encode($data)
        ]);
        $response = curl_exec($ch);
        curl_close($ch);

        $json = json_decode($response, true);
        return $json['choices'][0]['message']['content'] ?? 'AI response unavailable.';
    }

    /**
     * Quote Assistant — Suggest service plans and pricing
     */
    public static function suggestQuote(string $requirement): string
    {
        $prompt = "You are Flyboost Media’s AI Quote Assistant. 
        Based on the client's description below, suggest an ideal service package, 
        timeline, and estimated price range in INR. 
        Respond in a short, professional message with bullet points.

        Client request: $requirement";

        return self::openAIRequest($prompt);
    }

    /**
     * Marketing Report Generator — Analyze analytics data and summarize insights
     */
    public static function generateReport(): string
    {
        $data = Analytics::getSummary();

        $prompt = "You are a digital marketing analyst. 
        Based on this analytics data, write a short insight report 
        (3 paragraphs + bullet points for actions).

        Analytics Data:
        Active Users: {$data['active_users'] ?? $data['visitors']}
        Page Views: {$data['page_views'] ?? $data['visits']}
        Bounce Rate: {$data['bounce_rate']}%
        Avg Session Duration: {$data['avg_session_duration'] ?? $data['avg_visit_duration']} sec";

        return self::openAIRequest($prompt);
    }

    /**
     * Chatbot (OpenAI or Dialogflow)
     */
    public static function chat(string $message, ?string $sessionId = null): string
    {
        $botType = Env::get('CHATBOT_TYPE', 'OPENAI');

        if ($botType === 'DIALOGFLOW') {
            $projectId = Env::get('DIALOGFLOW_PROJECT_ID');
            if (!$projectId) return '⚠️ Dialogflow credentials not set.';

            $url = "https://dialogflow.googleapis.com/v2/projects/{$projectId}/agent/sessions/" . ($sessionId ?? uniqid()) . ":detectIntent";
            $data = [
                'queryInput' => [
                    'text' => [
                        'text' => $message,
                        'languageCode' => 'en'
                    ]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'Authorization: Bearer ' . Env::get('DIALOGFLOW_TOKEN')
                ],
                CURLOPT_POST => true,
                CURLOPT_POSTFIELDS => json_encode($data)
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $json = json_decode($response, true);
            return $json['queryResult']['fulfillmentText'] ?? 'Dialogflow response unavailable.';
        }

        // Default → OpenAI
        return self::openAIRequest("You are Flyboost Media’s website assistant. 
        Answer helpfully and concisely. 
        User says: $message");
    }

    /**
     * Generate AI-written blog idea or content draft
     */
    public static function generateBlogIdea(string $topic): string
    {
        $prompt = "Suggest 5 engaging blog title ideas for the topic: \"$topic\" 
        and write a 3-line summary for each (for a digital agency audience).";
        return self::openAIRequest($prompt);
    }
}
