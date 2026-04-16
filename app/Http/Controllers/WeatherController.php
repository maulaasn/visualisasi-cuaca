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

        if (empty($data)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Gagal mengambil data BMKG',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'data'   => $data,
        ]);
    }
}