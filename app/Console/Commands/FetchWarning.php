<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BMKGWarning;

class FetchWarning extends Command
{
    protected $signature = 'cuaca:fetch-warning-jatim';
    protected $description = 'Menarik data peringatan dini BMKG khusus Jawa Timur';

    public function handle(BMKGWarning $service)
    {
        $service->fetchAndCacheJatimWarning();
    }
}