<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ClearCacheWithoutDb extends Command
{
    protected $signature = 'cache:clear-no-db';
    protected $description = 'Clear cache without database operations';

    public function handle(): int
    {
        $this->info('Clearing cache without database operations...');

        $cachePath = storage_path('framework/cache');
        if (File::exists($cachePath)) {
            File::cleanDirectory($cachePath);
            $this->info('Application cache cleared.');
        }

        $viewsPath = storage_path('framework/views');
        if (File::exists($viewsPath)) {
            File::cleanDirectory($viewsPath);
            $this->info('Compiled views cleared.');
        }

        $configCache = base_path('bootstrap/cache/config.php');
        if (File::exists($configCache)) {
            File::delete($configCache);
            $this->info('Config cache cleared.');
        }

        $routeCache = base_path('bootstrap/cache/routes-v7.php');
        if (File::exists($routeCache)) {
            File::delete($routeCache);
            $this->info('Route cache cleared.');
        }

        $eventsCache = base_path('bootstrap/cache/events.php');
        if (File::exists($eventsCache)) {
            File::delete($eventsCache);
            $this->info('Events cache cleared.');
        }

        $this->info('Cache cleared successfully without database operations.');

        return self::SUCCESS;
    }
}
