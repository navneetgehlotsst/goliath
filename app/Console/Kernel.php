<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // $schedule->command('inspire')->hourly();
        //$schedule->command('app:add-question-to-match')->twiceDaily(1, 13);
        $schedule->command('app:add-question-to-match')->everyFourHours();
        // $schedule->command('app:insert-competition-list')->everyMinute();
        $schedule->command('app:live-data-for-matches')->everyMinute();
        $schedule->command('app:prediction-result')->everyThreeMinutes(); //min dubble then "live-data-for-matches"
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
