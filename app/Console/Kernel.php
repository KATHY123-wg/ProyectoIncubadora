<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Services\AlarmaService;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule)//: void
    {
        // $schedule->command('inspire')->hourly();
        $schedule->call(function(){
        AlarmaService::revisarComunicacion();
        })->everyFiveMinutes(); // o ->everyMinute() si quieres más sensibilidad

    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
