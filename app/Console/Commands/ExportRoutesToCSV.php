<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Route;

class ExportRoutesToCSV extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'route:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export all routes to a CSV file';


    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
      $routes = Route::getRoutes();

        $csvData = [];
        $csvData[] = ['Method', 'URI', 'Name', 'Action', 'Middleware'];

        foreach ($routes as $route) {
            $csvData[] = [
                implode('|', $route->methods()),
                $route->uri(),
                $route->getName(),
                $route->getActionName(),
                implode('|', $route->middleware())
            ];
        }

        $filePath = storage_path('routes.csv');
        $file = fopen($filePath, 'w');

        foreach ($csvData as $row) {
            fputcsv($file, $row);
        }

        fclose($file);

        $this->info("Routes have been exported to {$filePath}");
    }
}
