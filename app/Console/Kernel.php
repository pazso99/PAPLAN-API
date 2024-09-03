<?php

namespace App\Console;

use App\Jobs\CalculateMonthMetadata;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $schedule->call(function () {
            $year = now()->format('Y');
            $month = now()->format('m');
            CalculateMonthMetadata::dispatch($year, $month);
        })->lastDayOfMonth('19:00');
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
