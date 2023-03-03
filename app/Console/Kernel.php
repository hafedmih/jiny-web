<?php

namespace App\Console;

use App\Console\Commands\AssignDriversForScheduledRides;
use App\Console\Commands\expriedUserBlock;
use App\Console\Commands\ChangeTripRequestToNextDriver;
use App\Console\Commands\LowRatingBlockDriver;
use App\Console\Commands\GetDriverToOffline;
use App\Console\Commands\ChangeDriverLogs;
use App\Console\Commands\NightUploadPhoto;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\DocumentExpiry;


class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        ChangeTripRequestToNextDriver::class,
        AssignDriversForScheduledRides::class,
        expriedUserBlock::class,
        LowRatingBlockDriver::class,
        GetDriverToOffline::class,
        ChangeDriverLogs::class,
        NightUploadPhoto::class,
        DocumentExpiry::class,
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('assign:driver')
                    ->everyMinute();

        $schedule->command('assign_drivers:for_schedule_rides')
                    ->everyFiveMinutes();

        $schedule->command('driver:block')
                    ->everyMinute();

        $schedule->command('lowrate:blocked_driver')
                    ->everyMinute();

        $schedule->command('get_driver:offline')
                    ->everyMinute();

        $schedule->command('available:set_drivers')
                    ->everyMinute();

        $schedule->command('delete:uploadimage')
                    ->everyMinute();

        $schedule->command('change:driver_logs')
                    ->everyMinute();
                    
        $schedule->command('night:uploadphoto')
                    ->everyThreeMinutes();

        $schedule->command('document:expiry')
                   ->dailyAt('09:00');


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
