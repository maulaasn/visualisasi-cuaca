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

        $rawCheckedAt = Cache::get('bmkg.warning.jatim.checked_at', now());
        
        $checkedAt = \Carbon\Carbon::parse(str_replace(' WIB', '', $rawCheckedAt))
                        ->translatedFormat('d F Y, H:i') . ' WIB';

        return view('pages.warning', compact('warning', 'checkedAt'));
    }

    public function detail(BMKGWarning $service)
    {
        $warning = Cache::get('bmkg.warning.jatim');
        
        if (!$warning && !Cache::has('bmkg.warning.jatim.checked_at')) {
            $warning = $service->fetchAndCacheJatimWarning();
        }

        $rawCheckedAt = Cache::get('bmkg.warning.jatim.checked_at', now());

        $checkedAt = \Carbon\Carbon::parse(str_replace(' WIB', '', $rawCheckedAt))
                        ->translatedFormat('d F Y, H:i') . ' WIB';

        return view('pages.detail-warning', compact('warning', 'checkedAt'));
    }
}