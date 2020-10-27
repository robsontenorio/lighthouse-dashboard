<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class MigrateCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:migrate';

    protected $description = 'Execute Lighthouse Dashboard migrations';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->call('migrate', [
            '--force' => true,
            '--path' => 'vendor/robsontenorio/lighthouse-dashboard/database/migrations',
            '--database' => config('lighthouse-dashboard.connection'),
        ]);
        $this->info("Finished Lighthouse Dashboard migrations.");
    }
}
