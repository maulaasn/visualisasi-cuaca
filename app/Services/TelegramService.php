<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class TelegramService
{
    protected $token;
    protected $channelId;

    public function __construct()
    {
        $this->token = env('TELEGRAM_BOT_TOKEN');
        $this->channelId = env('TELEGRAM_CHANNEL_ID');
    }

    public function sendBroadcastMessage($message, $url)
    {
        if (!$this->token || !$this->channelId) {
            return false;
        }

        return Http::post("https://api.telegram.org/bot{$this->token}/sendMessage", [
            'chat_id' => $this->channelId,
            'text' => $message,
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [
                        ['text' => '🗺️ Lihat Area Terdampak di Peta', 'url' => $url]
                    ]
                ]
            ])
        ]);
    }
}