<?php

namespace App\Services;

use App\Models\Seller;
use Exception;
use Illuminate\Support\Facades\Log;
use Telegram\Bot\Api;
use Telegram\Bot\Objects\Update;

class TelegramCommandService
{
    protected Api $telegram;

    public function __construct()
    {
        $this->telegram = new Api(env('TELEGRAM_BOT_TOKEN'));
    }

    /**
     * Handle incoming Telegram updates/commands
     */
    public function handleUpdate(Update $update): void
    {
        try {
            $message = $update->getMessage();
            
            if (!$message || !$message->getText()) {
                return;
            }

            $text = trim($message->getText());
            $chatId = $message->getChat()->getId();
            $firstName = $message->getFrom()->getFirstName();
            $lastName = $message->getFrom()->getLastName() ?? '';
            $username = $message->getFrom()->getUsername() ?? '';

            // Handle commands
            if (str_starts_with($text, '/')) {
                $this->handleCommand($text, $chatId, $firstName, $lastName, $username);
            }

        } catch (Exception $e) {
            Log::error('Error handling Telegram update', [
                'error' => $e->getMessage(),
                'update' => $update->toArray()
            ]);
        }
    }

    /**
     * Handle specific commands
     */
    protected function handleCommand(string $command, string $chatId, string $firstName, string $lastName, string $username): void
    {
        $command = strtolower(trim($command));

        switch ($command) {
            case '/start':
                $this->handleStartCommand($chatId, $firstName);
                break;
            
            case '/getid':
                $this->handleGetIdCommand($chatId, $firstName, $lastName, $username);
                break;
            
            case '/help':
                $this->handleHelpCommand($chatId);
                break;
            
            default:
                $this->handleUnknownCommand($chatId);
                break;
        }
    }

    /**
     * Handle /start command
     */
    protected function handleStartCommand(string $chatId, string $firstName): void
    {
        $message = "👋 Hello {$firstName}!\n\n" .
                  "Welcome to PADAUNG Notification Bot!\n\n" .
                  "🤖 *Available Commands:*\n" .
                  "/getId - Get your Telegram Chat ID\n" .
                  "/help - Show this help message\n\n" .
                  "💡 *How to get order notifications:*\n" .
                  "1. Use /getId to get your chat ID\n" .
                  "2. Add this chat ID to your seller profile\n" .
                  "3. You'll receive notifications for new orders!";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Handle /getId command
     */
    protected function handleGetIdCommand(string $chatId, string $firstName, string $lastName, string $username): void
    {
        $fullName = trim($firstName . ' ' . $lastName);
        $userInfo = $username ? "@{$username}" : $fullName;

        // Check if this chat ID is already associated with any seller across all tenants
        $existingSellers = \App\Models\Seller::where('telegram_chat_id', $chatId)->get();

        $message = "🆔 *Your Telegram Information:*\n\n" .
                  "📱 *Chat ID:* `{$chatId}`\n" .
                  "👤 *Name:* {$fullName}\n" .
                  ($username ? "🔗 *Username:* @{$username}\n" : "") .
                  "\n📋 *Next Steps:*\n" .
                  "1. Copy your Chat ID: `{$chatId}`\n" .
                  "2. Go to your seller admin panel (any tenant domain)\n" .
                  "3. Edit your profile and paste this Chat ID\n" .
                  "4. Save your profile\n\n";

        if ($existingSellers->count() > 0) {
            $message .= "✅ *Current Status:* This Chat ID is already linked to " . $existingSellers->count() . " seller account(s)\n";
            foreach ($existingSellers as $seller) {
                $message .= "   • {$seller->name} ({$seller->email})\n";
            }
            $message .= "\n🔔 You should already be receiving order notifications!\n";
        } else {
            $message .= "⚠️ *Current Status:* This Chat ID is not linked to any seller account yet\n";
            $message .= "👆 Follow the steps above to start receiving notifications\n";
        }

        $message .= "\n💡 *Multi-tenant Support:* This bot works across all your store domains!";

        $this->sendMessage($chatId, $message);

        // Log for debugging
        Log::info('Telegram Chat ID requested', [
            'chat_id' => $chatId,
            'name' => $fullName,
            'username' => $username,
            'existing_sellers_count' => $existingSellers->count()
        ]);
    }

    /**
     * Handle /help command
     */
    protected function handleHelpCommand(string $chatId): void
    {
        $message = "🤖 *PADAUNG Bot Help*\n\n" .
                  "*Available Commands:*\n" .
                  "/start - Welcome message\n" .
                  "/getId - Get your Telegram Chat ID\n" .
                  "/help - Show this help message\n\n" .
                  "*Purpose:*\n" .
                  "This bot sends you notifications when:\n" .
                  "• New orders are received\n" .
                  "• Order status changes\n" .
                  "• Important seller updates\n\n" .
                  "*Setup:*\n" .
                  "1. Use /getId to get your chat ID\n" .
                  "2. Add it to your seller profile\n" .
                  "3. Start receiving notifications!";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Handle unknown commands
     */
    protected function handleUnknownCommand(string $chatId): void
    {
        $message = "❓ Unknown command.\n\n" .
                  "Available commands:\n" .
                  "/getId - Get your Chat ID\n" .
                  "/help - Show help\n" .
                  "/start - Welcome message";

        $this->sendMessage($chatId, $message);
    }

    /**
     * Send message to Telegram chat
     */
    protected function sendMessage(string $chatId, string $message): void
    {
        try {
            $this->telegram->sendMessage([
                'chat_id' => $chatId,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
        } catch (Exception $e) {
            Log::error('Failed to send Telegram message', [
                'error' => $e->getMessage(),
                'chat_id' => $chatId
            ]);
        }
    }
}
