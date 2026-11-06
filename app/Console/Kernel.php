<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;


class Kernel extends ConsoleKernel
{
    /**
     * Register the application's command schedule.
     */
protected function schedule(Schedule $schedule)
{
    // Jalankan setiap menit
       $schedule->command('pengingat:kirim')
        ->everyMinute()
        ->withoutOverlapping()
        ->runInBackground();

}


    /**
     * Register the commands for the application.
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
