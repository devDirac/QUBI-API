<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Http\Controllers\API\UsuariosController;
use Carbon\Carbon;

class RunMethod extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:RunMethod';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $a = new UsuariosController();
        $a->insertaUser();
        \Log::debug('testco At time: '. Carbon::now());
        return Command::SUCCESS;
    }
}
