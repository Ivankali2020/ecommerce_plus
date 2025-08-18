<?php

namespace App\Http\Controllers;

use App\Services\TelegramCommandService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;

class TelegramWebhookController extends Controller
{
    public function __construct(
        protected TelegramCommandService $telegramService
    ) {}

    /**
     * Handle incoming webhook from Telegram
     */
    public function webhook(Request $request): Response
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            // Get the webhook update from the request
            $update = $telegram->getWebhookUpdate();
            
            // Log for debugging
            Log::info('Telegram webhook received', [
                'update' => $update->toArray()
            ]);
            
            // Handle the update with our service
            $this->telegramService->handleUpdate($update);
            
            return response('OK', 200);
            
        } catch (\Exception $e) {
            Log::error('Telegram webhook error', [
                'error' => $e->getMessage(),
                'request' => $request->all()
            ]);
            
            return response('Error', 500);
        }
    }

    /**
     * Set webhook URL (for setup)
     */
    public function setWebhook(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            
            // Remove existing webhook first
            $telegram->removeWebhook();
            
            // Set new webhook URL
            $webhookUrl = route('telegram.webhook');
            $response = $telegram->setWebhook(['url' => $webhookUrl]);
            
            return response()->json([
                'success' => true,
                'webhook_url' => $webhookUrl,
                'telegram_response' => $response
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get webhook info
     */
    public function getWebhookInfo(): \Illuminate\Http\JsonResponse
    {
        try {
            $telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
            $info = $telegram->getWebhookInfo();
            
            return response()->json([
                'success' => true,
                'webhook_info' => $info
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
