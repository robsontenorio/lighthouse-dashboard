<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:install';

    protected $description = 'Install Lighthouse Dashboard (migrations + publish assets)';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->warn("\n-------- Lighthouse Dashboard --------");
        $this->call('migrate');
        $this->callSilent('vendor:publish', [
            '--tag' => 'lighthouse-dashboard',
            '--force' => true
        ]);
        $this->info("Published fresh assets.");
        $this->warn("--------------------------------------\n");
    }
}
