<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BMKGService;
use Illuminate\Routing\Controller;

class WeatherController extends Controller
{
    protected $bmkg;

    public function __construct(BMKGService $bmkg)
    {
        $this->bmkg = $bmkg;
    }

    public function index()
    {
        return view('map');
    }

    public function getWeatherData()
    {
        $data = $this->bmkg->getWeatherData();

        if (!$data || !isset($data['data'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data BMKG'
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $this->transform($data)
        ]);
    }

    private function normalize($text)
    {
        if (!$text) return '';
        $text = strtolower($text);
        return trim(preg_replace('/(kecamatan |kec\.|kab\.|kota )/i', '', $text));
    }

    private function transform($data)
    {
        $result = [];

        foreach ($data['data'] ?? [] as $wilayah) {
            $lokasi = $wilayah['lokasi'] ?? [];
            $namaWilayah = $lokasi['kecamatan'] ?? $lokasi['kotkab'] ?? '';
            
            if (empty($namaWilayah)) continue;

            $kecamatan = $this->normalize($namaWilayah);
            $cuacaArray = $wilayah['cuaca'] ?? [];
            
            if (!isset($cuacaArray[0][0])) continue;

            $cuaca = $cuacaArray[0][0]; 

            $result[$kecamatan] = [
                'raw_name' => $namaWilayah,
                'weather_desc' => $cuaca['weather_desc'] ?? '-',
                'weather_code' => $cuaca['weather'] ?? 0,
                'temp' => $cuaca['t'] ?? 0,
                'humidity' => $cuaca['hu'] ?? 0,
                'wind_speed' => $cuaca['ws'] ?? 0,
                'wind_dir' => $cuaca['wd'] ?? '-',
                'visibility' => $cuaca['vs'] ?? 0,
                'last_update' => $cuaca['local_datetime'] ?? $cuaca['datetime'] ?? ''
            ];
        }

        return $result;
    }
}