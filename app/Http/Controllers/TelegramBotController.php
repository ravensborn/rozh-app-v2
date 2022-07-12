<?php

namespace App\Http\Controllers;


use App\Models\Log;
use Http;

class TelegramBotController extends Controller
{
    private string $authKey = "5312778529:AAF0EkMl5oKvW3WgJSPbBGWIoWqxmE7uKR4";

    public function sendMessage($chatId, $text): bool
    {
        $response = Http::post('https://api.telegram.org/bot' . $this->authKey . '/sendMessage', [
            'text' => $text,
            'chat_id' => '-100' . $chatId,
        ]);

        if ($response->successful()) {

            Log::create([
                'content' => [
                    'message' => 'Telegram bot has sent a message.',
                    'type' => 'info',
                    'extra' => [
                        'chat_id' => $chatId,
                        'message' => $text,
                    ]
                ]
            ]);

            return true;
        }

        Log::create([
            'content' => [
                'message' => 'Error sending message using telegram bot.',
                'type' => 'error',
                'extra' => [
                    'chat_id' => $chatId,
                    'message' => $text,
                    'telegram_response' => $response,
                ]
            ]
        ]);

        return false;
    }
}
