<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\BMKGForecast; 
use Illuminate\Routing\Controller;

class WeatherController extends Controller
{
    protected $bmkgForecast; 

    public function __construct(BMKGForecast $bmkgForecast)
    {
        $this->bmkgForecast = $bmkgForecast;
    }

    public function index()
    {
        return view('pages.map');
    }

    public function getWeatherData()
    {
        $data = $this->bmkgForecast->getWeatherData();

        if (empty($data)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal mengambil data BMKG',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data' => $data,
        ]);
    }
}