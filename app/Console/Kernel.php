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

            // $schedule->command('app:data-clients')->dailyAt('6:20');
            // $schedule->command('app:data-loans')->dailyAt('6:25');
            // $schedule->command('app:data-movements')->dailyAt('6:30');

           $now = now();

           if ($now->between($timeStart, $timeEnd)) {

               $schedule->command('app:data-clients')
                   ->weekdays()
                   ->between($timeStart, $timeEnd)
                   ->everyThreeHours($minutes = 0)
                   ->name('data-clients');

               $schedule->command('app:data-loans')
                   ->weekdays()
                   ->between($this->addMinutes($timeStart, 5), $this->addMinutes($timeEnd, 5))
                   ->everyThreeHours($minutes = 0)
                   ->name('data-loans');

               $schedule->command('app:data-movements')
                   ->weekdays()
                   ->between($this->addMinutes($timeStart, 10), $this->addMinutes($timeEnd, 10))
                   ->everyThreeHours($minutes = 0)
                   ->name('data-movements');
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
        $time = Carbon::createFromFormat('H:i', $time);
        $time->addMinutes($minutes);
        $updatedTime = $time->format('H:i');
        return $updateTime;
    }

}
