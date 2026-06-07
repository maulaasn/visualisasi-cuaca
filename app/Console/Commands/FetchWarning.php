<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BMKGWarning;
use App\Services\TelegramService;
use Illuminate\Support\Facades\Cache;

class FetchWarning extends Command
{
    protected $signature = 'cuaca:fetch-warning-jatim';
    protected $description = 'Menarik data peringatan dini BMKG khusus Jawa Timur';

    public function handle(BMKGWarning $service, TelegramService $telegram)
    {
        $warning = $service->fetchAndCacheJatimWarning();

        if ($warning) {
            $lastSentTitle = Cache::get('bmkg.warning.last_sent_title');

            if ($lastSentTitle !== $warning['title']) {
                $description = trim(preg_replace('/\s+/', ' ', strip_tags($warning['description'])));
                
                $waktuMulai = trim(str_replace([' WIB', 'WIB'], '', $warning['effective_wib']));
                $waktuSelesai = trim(str_replace([' WIB', 'WIB'], '', $warning['expires_wib'])) . ' WIB';

                $message = "⚠️ <b>PERINGATAN DINI CUACA EKSTREM</b> ⚠️\n\n";
                $message .= "<b>" . $warning['event'] . "</b>\n";
                $message .= "<b>" . $warning['title'] . "</b>\n\n";
                $message .= "<b>Berlaku:</b> " . $waktuMulai . " - " . $waktuSelesai . "\n\n";
                $message .= "<b>Detail:</b>\n" . $description;

                $mapUrl = route('warning.detail');
                
                $response = $telegram->sendBroadcastMessage($message, $mapUrl);

                if ($response && $response->successful()) {
                    Cache::put('bmkg.warning.last_sent_title', $warning['title'], now()->addHours(6));
                    $this->info('Peringatan dini berhasil dikirim ke Channel Telegram.');
                } else {
                    $this->error('GAGAL MENGIRIM KE TELEGRAM. Pesan Error:');
                    $this->error($response ? $response->body() : 'Tidak ada respons dari Telegram');
                }
            } else {
                $this->info('Peringatan dini sudah dikirim sebelumnya.');
            }
        } else {
            $this->info('Tidak ada peringatan dini saat ini.');
        }
    }
}