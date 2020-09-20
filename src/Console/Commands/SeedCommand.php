<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class SeedCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:seed';

    protected $description = 'Execute Lighthouse Dashboard seed for development.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info("Refreshing database ...");

        $this->call('migrate:refresh', [
            '--seeder' => 'LighthouseDashboardSeeder',
            '--path' => 'vendor/robsontenorio/lighthouse-dashboard/database/migrations',
            '--database' => config('lighthouse-dashboard.connection'),
        ]);
    }
}
