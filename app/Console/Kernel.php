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
        // Daily backup at 02:00
        $schedule->command('backup:run')->dailyAt('02:00')->withoutOverlapping();

        // Daily monitor at 08:00
        $schedule->command('backup:monitor')->dailyAt('08:00')->withoutOverlapping();

        // Weekly cleanup on Sundays at 03:00
        $schedule->command('backup:clean')->weeklyOn(0, '03:00')->withoutOverlapping();
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
