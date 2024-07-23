<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Models\Settings;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $settings = Settings::get();

        // if ($settings[0]["value"]) {
            
            // $schedule->command('app:data-clients')->dailyAt('6:20');
            // $schedule->command('app:data-loans')->dailyAt('6:25');
            // $schedule->command('app:data-movements')->dailyAt('6:30');

           
           // Definir el rango horario
           $start = '08:00';
           $end = '20:00';
           $interval = 5; // Intervalo en minutos

           // Hora actual
           $now = now();

           // Verificar si estamos en el rango horario
           if ($now->between($start, $end)) {
               // Ejecutar el primer comando cada 10 minutos, entre 8 AM y 8 PM
               $schedule->command('app:data-clients')
                   ->weekdays()
                   ->between('08:00', '20:00')
                   ->hourly()
                   ->name('data-clients');

               // Ejecutar el segundo comando 5 minutos después del primer comando
               $schedule->command('app:data-loans')
                   ->weekdays()
                   ->between('08:05', '20:05')
                   ->hourly()
                   ->name('data-loans');

               // Ejecutar el tercer comando 5 minutos después del segundo comando
               $schedule->command('app:data-movements')
                   ->weekdays()
                   ->between('08:10', '20:10')
                   ->hourly()
                   ->name('data-movements');
           }
           
        // }

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
