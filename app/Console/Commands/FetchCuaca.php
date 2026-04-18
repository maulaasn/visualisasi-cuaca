<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BMKGService;
use Illuminate\Support\Facades\Storage;

class FetchCuaca extends Command
{
    protected $signature = 'cuaca:fetch-jatim';
    protected $description = 'Menarik data cuaca seluruh kecamatan Jatim dari API BMKG secara perlahan';

    public function handle(BMKGService $bmkgService)
    {
        $this->info('Memulai penarikan data dari BMKG. Proses ini butuh waktu beberapa menit...');

        $data = $bmkgService->downloadDataTerkini($this);

        if (!empty($data)) {
            Storage::put('data/cuaca_terkini.json', json_encode($data));
            $this->info('Selesai! Data berhasil disimpan.');
        } else {
            $this->error('Gagal menarik data.');
        }
    }
}