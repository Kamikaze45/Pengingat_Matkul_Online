<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class KirimPengingatJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // opsional: kamu bisa terima parameter jika butuh
    public function __construct()
    {
        //
    }

    public function handle()
    {
        try {
            // panggil artisan command yang sudah kamu buat
            Artisan::call('pengingat:kirim');

            // opsional: log hasil
            Log::info('KirimPengingatJob: success');
        } catch (\Throwable $e) {
            Log::error('KirimPengingatJob failed: '.$e->getMessage());
            // biarkan queue menangani retry sesuai konfigurasi
            throw $e;
        }
    }
}
