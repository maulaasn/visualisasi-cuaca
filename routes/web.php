<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WeatherController;
use App\Http\Controllers\WarningController;
use App\Http\Controllers\PublicNewsController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\NewsController;

Route::get('/', [WeatherController::class, 'index'])->name('map.index');
Route::get('/api/weather', [WeatherController::class, 'getWeatherData'])->name('api.weather');
Route::get('/peringatan-dini', [WarningController::class, 'index'])->name('warning.index');
Route::get('/peringatan-dini/detail', [WarningController::class, 'detail'])->name('warning.detail');

Route::get('/berita', [PublicNewsController::class, 'index'])->name('news.index');
Route::get('/berita/{slug}', [PublicNewsController::class, 'show'])->name('news.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.process');
});

Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {
    
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/dashboard', function () {
        return view('admin.map');
    })->name('dashboard');

    Route::get('/peringatan-dini', function (\App\Services\BMKGWarning $service) {
        $warning = \Illuminate\Support\Facades\Cache::get('bmkg.warning.jatim');
        if (!$warning && !\Illuminate\Support\Facades\Cache::has('bmkg.warning.jatim.checked_at')) {
            $warning = $service->fetchAndCacheJatimWarning();
        }
        $checkedAt = \Illuminate\Support\Facades\Cache::get('bmkg.warning.jatim.checked_at', date('d F Y, H:i') . ' WIB');
        
        return view('admin.warning', compact('warning', 'checkedAt'));
    })->name('warning');

    Route::get('/peringatan-dini/detail', function (\App\Services\BMKGWarning $service) {
        $warning = \Illuminate\Support\Facades\Cache::get('bmkg.warning.jatim');
        if (!$warning && !\Illuminate\Support\Facades\Cache::has('bmkg.warning.jatim.checked_at')) {
            $warning = $service->fetchAndCacheJatimWarning();
        }
        $checkedAt = \Illuminate\Support\Facades\Cache::get('bmkg.warning.jatim.checked_at', date('d F Y, H:i') . ' WIB');
        
        return view('admin.detail-warning', compact('warning', 'checkedAt'));
    })->name('warning.detail');

    Route::get('/berita', [NewsController::class, 'index'])->name('news.index'); // /admin/berita
    Route::get('/berita/tambah', [NewsController::class, 'create'])->name('news.create'); // /admin/berita/tambah
    Route::post('/berita/simpan', [NewsController::class, 'store'])->name('news.store'); // /admin/berita/simpan
    
    Route::get('/berita/{id}', [NewsController::class, 'show'])->name('news.show'); // /admin/berita/{id}
    
    Route::get('/berita/{id}/edit', [NewsController::class, 'edit'])->name('news.edit'); // /admin/berita/{id}/edit
    Route::put('/berita/{id}/update', [NewsController::class, 'update'])->name('news.update'); // /admin/berita/{id}/update
    Route::delete('/berita/{id}/hapus', [NewsController::class, 'destroy'])->name('news.destroy'); // /admin/berita/{id}/hapus
});