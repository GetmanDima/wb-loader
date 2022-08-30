<?php

namespace App\Console;

use App\Jobs\WBJob;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Варианты использования:
        // $schedule->job(new WBJob())->everyMinute();
        // $schedule->job(new WBJob())->dailyAt('23:00')->timezone('Europe/Moscow');
        $schedule->job(new WBJob())->everyTwoHours();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
