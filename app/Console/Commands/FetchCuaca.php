<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BMKGForecast;
use Illuminate\Support\Facades\Storage;

class FetchCuaca extends Command
{
    protected $signature = 'cuaca:fetch-jatim';
    protected $description = 'Menarik data cuaca seluruh kecamatan Jatim dari API BMKG secara perlahan';

    public function handle(BMKGForecast $bmkgForecast)
    {
        $this->info('Memulai penarikan data dari BMKG...');

        $data = $bmkgForecast->downloadDataTerkini($this);

        if (!empty($data)) {
            Storage::put('data/cuaca_terkini.json', json_encode($data));
            $this->info('Selesai! Data berhasil disimpan.');
        } else {
            $this->error('Gagal menarik data.');
        }
    }
}