<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Http\Controllers\API\UsuariosController;
class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')->hourly();
        $schedule->command('sanctum:prune-expired --hours=24')->daily();
        $schedule->command('websockets:serve')->everyMinute();
        $schedule->command('command:RunMethod')->everyMinute();
        //$schedule->call('App\Http\Controllers\API\UsuariosController@insertaUser')->everyMinute();
        /* $schedule->call(function () {
            $a = new UsuariosController();
            $a->insertaUser();
         })->everyMinute(); */
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
