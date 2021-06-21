<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\DailyUpdates::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();

        /** ----------------------------------
         * ACTIVATED DAILY UPDATE LV 0
         * EVERY 1 AM
         ------------------------------------- */
        $schedule->command('daily:update')->dailyAt('01:00');

        /** ---------------------------------------
         * ACTIVATED ROBOT SCHEDULAR LV 1
         * GLOBAL & INDO WEEKLY REPORT
         * EVERY SATURDAY AT 12 AM
         ------------------------------------------ */
         $schedule->command('set:team-monitoring-global')->weekly();
         $schedule->command('set:team-monitoring-indo')->weekly();

        /** ---------------------------------------
         * ACTIVATED ROBOT SCHEDULAR LV 2
         * GLOBAL & INDO WEEKLY REPORT
         * EVERY SATURDAY AT 12 AM
         ------------------------------------------ */
         $schedule->command('set:all-team-report-weekly')->weekly();

         /** ---------------------------------------
         * ACTIVATED ROBOT SCHEDULAR LV 2
         * MONTHLY REPORT
         * EVERY MONTH FIRST DAY AT 12 AM
         ------------------------------------------ */
         $schedule->command('set:all-team-report-monthly')->monthly();
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
