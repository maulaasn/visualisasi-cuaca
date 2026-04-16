<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;

Route::get('/', [WeatherController::class, 'index'])->name('map.index');
Route::get('/api/weather', [WeatherController::class, 'getWeatherData'])->name('api.weather');