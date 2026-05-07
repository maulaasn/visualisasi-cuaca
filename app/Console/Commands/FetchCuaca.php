<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\BMKGForecast;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log; 

class FetchCuaca extends Command
{
    protected $signature = 'cuaca:fetch-jatim';
    protected $description = 'Menarik data cuaca seluruh kecamatan Jatim dari API BMKG (Eksekusi Manual)';

    public function handle(BMKGForecast $bmkgForecast)
    {
        $this->info('Memulai penarikan data dari BMKG...');

        try {
            $data = $bmkgForecast->downloadDataTerkini($this);

            if (!empty($data)) {
                Storage::put('data/cuaca_terkini.json', json_encode($data));
                
                $this->info('Selesai! Data berhasil disimpan.');
                Log::info('Berhasil memperbarui data cuaca Jatim.');
            } else {
                $this->warn('Data dari BMKG kosong, file JSON lama dipertahankan.');
                Log::warning('Respon API berhasil, tetapi data kosong.');
            }

        } catch (\Exception $e) {
            $this->error('Gagal menarik data: Koneksi ke BMKG bermasalah.');
            
            Log::error('Fetch BMKG Gagal' . $e->getMessage());
        }
    }
}