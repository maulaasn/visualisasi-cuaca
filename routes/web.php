<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WarningController;

Route::get('/', [WeatherController::class, 'index'])->name('map.index');
Route::get('/api/weather', [WeatherController::class, 'getWeatherData'])->name('api.weather');
Route::get('/peringatan-dini', [WarningController::class, 'index'])->name('warning.index');