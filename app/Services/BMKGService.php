<?php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Exception;

class BMKGService
{
    protected $baseUrl = 'https://api.bmkg.go.id/publik/prakiraan-cuaca?adm1=35';

    public function getWeatherData()
    {
        if (Cache::has('bmkg_weather_jatim')) {
            return Cache::get('bmkg_weather_jatim');
        }

        try {
            $response = Http::withoutVerifying()
                ->withHeaders([
                    'Accept' => 'application/json',
                    'User-Agent' => 'PostmanRuntime/7.36.3'
                ])
                ->timeout(60)
                ->get($this->baseUrl);

            if ($response->successful() && is_array($response->json())) {
                $data = $response->json();
                Cache::put('bmkg_weather_jatim', $data, 1800);
                return $data;
            }

            Log::error('BMKG API Body: ' . substr($response->body(), 0, 500));
            return null;
        } catch (Exception $e) {
            Log::error('BMKG Conn Error: ' . $e->getMessage());
            return null;
        }
    }
}