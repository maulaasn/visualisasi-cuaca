<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class BMKGService
{
    protected string $baseUrl = 'https://api.bmkg.go.id/publik/prakiraan-cuaca';

    private function getAdm4CodesFromCsv(): array
    {
        $codes = [];
        $csvPath = storage_path('app/data/adm4_jatim.csv');

        if (!file_exists($csvPath)) {
            Log::error("File CSV tidak ditemukan di: " . $csvPath);
            return [];
        }

        if (($handle = fopen($csvPath, "r")) !== false) {
            $header = fgetcsv($handle, 1000, ";");
            while (($data = fgetcsv($handle, 1000, ";")) !== false) {
                $code = trim(end($data));
                if (!empty($code) && str_contains($code, '.')) {
                    $codes[] = $code;
                }
            }
            fclose($handle);
        }

        return $codes;
    }

    public function getWeatherData(): array
    {
        $path = 'data/cuaca_terkini.json';
        if (Storage::exists($path)) {
            $content = Storage::get($path);
            return json_decode($content, true) ?? [];
        }
        return [];
    }

    public function downloadDataTerkini($commandOutput = null): array
    {
        set_time_limit(0);
        $result = [];
        $seenAdm3 = [];
        $adm4Codes = $this->getAdm4CodesFromCsv();

        $bar = $commandOutput ? $commandOutput->getOutput()->createProgressBar(count($adm4Codes)) : null;

        foreach ($adm4Codes as $adm4) {
            $parts = explode('.', $adm4);
            $adm3 = implode('.', array_slice($parts, 0, 3));

            if (isset($seenAdm3[$adm3])) {
                if ($bar)
                    $bar->advance();
                continue;
            }

            // Jika API BMKG error, script tidak akan nge-spam desa lain di kecamatan yg sama
            $seenAdm3[$adm3] = true;

            $data = $this->fetchAdm4($adm4);

            if ($data) {
                $key = $this->normalizeKey($data['kecamatan']);
                $result[$key] = $data;
            }

            if ($bar)
                $bar->advance();

            // Jeda dinaikkan menjadi 1 detik agar tidak terkena blokir dari BMKG
            usleep(1000000);
        }

        if ($bar)
            $bar->finish();
        if ($commandOutput)
            $commandOutput->newLine();

        return $result;
    }

    private function fetchAdm4(string $adm4): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
                ])
                ->timeout(20)
                ->get($this->baseUrl, ['adm4' => $adm4]);

            if (!$response->successful()) {
                Log::warning("BMKG gagal adm4={$adm4} HTTP=" . $response->status());
                return null;
            }

            $json = $response->json();
            $lokasi = $json['data'][0]['lokasi'] ?? null;
            $cuacaArray = $json['data'][0]['cuaca'] ?? null;

            if (!$lokasi || !$cuacaArray) {
                return null;
            }

            $allDataWaktu = [];
            foreach ($cuacaArray as $hari) {
                if (is_array($hari)) {
                    foreach ($hari as $jam) {
                        if (is_array($jam) && isset($jam['local_datetime'])) {
                            $allDataWaktu[] = $jam;
                        }
                    }
                }
            }

            $cuaca = null;
            $forecasts = [];
            $currentTimestamp = time();
            $closestTimeDiff = PHP_INT_MAX;
            $closestIndex = 0;

            foreach ($allDataWaktu as $index => $dataWaktu) {
                $apiTime = strtotime($dataWaktu['local_datetime']);
                $diff = abs($currentTimestamp - $apiTime);

                if ($diff < $closestTimeDiff) {
                    $closestTimeDiff = $diff;
                    $cuaca = $dataWaktu;
                    $closestIndex = $index;
                }
            }

            if (!$cuaca) {
                $cuaca = $allDataWaktu[0] ?? null;
                $closestIndex = 0;
            }

            if (!$cuaca) {
                return null;
            }

            $targetHours = ['00', '06', '12', '18'];
            $count = 0;
            $seenDates = [];

            // SKENARIO UTAMA: Cari sesuai target jam (00, 06, 12, 18)
            for ($i = $closestIndex + 1; $i < count($allDataWaktu); $i++) {
                $fData = $allDataWaktu[$i];
                $timeString = $fData['local_datetime'] ?? '';
                if (empty($timeString)) continue;

                $timestamp = strtotime($timeString);
                if (!$timestamp) continue;

                $hour = date('H', $timestamp);
                $dateHourKey = date('Y-m-d H', $timestamp);

                if (in_array($hour, $targetHours) && !isset($seenDates[$dateHourKey])) {
                    $forecasts[] = [
                        'datetime'     => $timeString,
                        'weather_desc' => $fData['weather_desc'] ?? '-',
                        'weather_code' => (string) ($fData['weather'] ?? 0),
                    ];
                    $seenDates[$dateHourKey] = true;
                    $count++;
                    if ($count >= 4) break;
                }
            }

            // SKENARIO FALLBACK (PLAN B)
            // Jika ternyata kosong (karena stasiun BMKG di kec tersebut format jamnya beda),
            // Jadikan saja 4 data ke depan dengan dilompati 1 index agar jaraknya tetap panjang
            if (empty($forecasts)) {
                $count = 0;
                $seenDates = []; 
                
                for ($i = $closestIndex + 1; $i < count($allDataWaktu); $i++) {
                    $fData = $allDataWaktu[$i];
                    $timeString = $fData['local_datetime'] ?? '';
                    if (empty($timeString)) continue;

                    $timestamp = strtotime($timeString);
                    if (!$timestamp) continue;
                    
                    $dateHourKey = date('Y-m-d H', $timestamp);
                    if (!isset($seenDates[$dateHourKey])) {
                        $forecasts[] = [
                            'datetime'     => $timeString,
                            'weather_desc' => $fData['weather_desc'] ?? '-',
                            'weather_code' => (string) ($fData['weather'] ?? 0),
                        ];
                        $seenDates[$dateHourKey] = true;
                        $count++;
                        
                        // Skip 1 index di API BMKG (karena aslinya per 3 jam, kalau di-skip 1 jadi jarak 6 jam)
                        $i++; 
                        
                        if ($count >= 4) break;
                    }
                }
            }

            return [
                'kecamatan'    => trim($lokasi['kecamatan'] ?? ''),
                'kabupaten'    => trim($lokasi['kotkab'] ?? ''),
                'weather_desc' => $cuaca['weather_desc'] ?? '-',
                'weather_code' => (string) ($cuaca['weather'] ?? 0),
                'temp'         => $cuaca['t'] ?? 0,
                'humidity'     => $cuaca['hu'] ?? 0,
                'wind_speed'   => $cuaca['ws'] ?? 0,
                'wind_dir'     => $cuaca['wd'] ?? '-',
                'visibility'   => $cuaca['vs_text'] ?? '-',
                'last_update'  => $cuaca['local_datetime'] ?? '',
                'forecasts'    => $forecasts,
            ];
        } catch (Exception $e) {
            Log::error("BMKG error adm4={$adm4}: " . $e->getMessage());
            return null;
        }
    }

    public function normalizeKey(string $text): string
    {
        $text = mb_strtolower(trim($text), 'UTF-8');
        $text = preg_replace('/\b(kecamatan|kec\.|kab\.|kota)\b\s*/i', '', $text);
        $text = preg_replace('/[^a-z0-9\s]/u', '', $text);
        $text = preg_replace('/\s+/', ' ', $text);
        return trim($text);
    }
}