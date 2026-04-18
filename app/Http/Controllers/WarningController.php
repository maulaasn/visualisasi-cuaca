<?php

namespace App\Http\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Cache;
use App\Services\BMKGWarning;

class WarningController extends Controller
{
    public function index(BMKGWarning $service)
    {
        $warning = Cache::get('bmkg.warning.jatim');
        
        if (!$warning && !Cache::has('bmkg.warning.jatim.checked_at')) {
            $warning = $service->fetchAndCacheJatimWarning();
        }

        $checkedAt = Cache::get('bmkg.warning.jatim.checked_at', date('d F Y, H:i') . ' WIB');

        return view('pages.warning', compact('warning', 'checkedAt'));
    }
}