<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Settings;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        // Definir el rango horario
        $settings = Settings::get();

        $start = trim($settings[0]["value"], '"');
        $timeStart = Carbon::parse($start) ?? Carbon::parse("06:00");

        $end = trim($settings[1]["value"], '"');
        $timeEnd = Carbon::parse($end) ?? Carbon::parse("21:00");

        $interval = trim($settings[2]["value"], '"') ?? 'hourly';

            // $schedule->command('app:data-clients')->dailyAt('6:20');
            // $schedule->command('app:data-loans')->dailyAt('6:25');
            // $schedule->command('app:data-movements')->dailyAt('6:30');

           $now = now();

           if ($now->between($timeStart, $timeEnd)) {

               $clients = $schedule->command('app:data-clients')
                   ->days([1, 2, 3, 4, 5, 6])
                   ->between($timeStart, $timeEnd)
                   ->name('data-clients');
                   
               $loans = $schedule->command('app:data-loans')
                   ->days([1, 2, 3, 4, 5, 6])
                   ->between($this->addMinutes($timeStart, 5), $this->addMinutes($timeEnd, 5))
                   ->name('data-loans');

               $movements = $schedule->command('app:data-movements')
                   ->days([1, 2, 3, 4, 5, 6])
                   ->between($this->addMinutes($timeStart, 10), $this->addMinutes($timeEnd, 10))
                   ->name('data-movements');

                if ($interval == 'everyFifteenMinutes') {
                    
                    $clients->everyFifteenMinutes();
                    $loans->everyFifteenMinutes();
                    $movements->everyFifteenMinutes();

                }else if ($interval == 'everyThirtyMinutes') {
                    
                    $clients->everyFifteenMinutes();
                    $loans->everyFifteenMinutes();
                    $movements->everyFifteenMinutes();

                }else if ($interval == 'hourly') {

                    $clients->hourly();
                    $loans->hourly();
                    $movements->hourly();
                    
                }else if ($interval == 'everyTwoHours') {

                    $clients->everyTwoHours($minutes = 0);
                    $loans->everyTwoHours($minutes = 0);
                    $movements->everyTwoHours($minutes = 0);
                    
                }else if ($interval == 'everyThreeHours') {

                    $clients->everyThreeHours($minutes = 0);
                    $loans->everyThreeHours($minutes = 0);
                    $movements->everyThreeHours($minutes = 0);
                    
                }else if ($interval == 'everyFourHours') {

                    $clients->everyFourHours($minutes = 0);
                    $loans->everyFourHours($minutes = 0);
                    $movements->everyFourHours($minutes = 0);
                    
                }else if ($interval == 'everySixHours') {

                    $clients->everySixHours($minutes = 0);
                    $loans->everySixHours($minutes = 0);
                    $movements->everySixHours($minutes = 0);
                    
                }

           }
           
    }

    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }

    protected function addMinutes($time, $minutes){
        if (!($time instanceof Carbon)) {
            $time = Carbon::createFromFormat('H:i', $time);
        }
        $time->addMinutes($minutes);
        $updatedTime = $time->format('H:i');
        return $updatedTime;
    }

}
