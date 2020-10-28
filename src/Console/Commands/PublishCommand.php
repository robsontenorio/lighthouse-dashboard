<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:publish';

    protected $description = 'Publish Lighthouse Dashboard assets and config file.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'lighthouse-dashboard',
            '--force' => true
        ]);

        $this->info("Published assets and config file for Lighthouse Dashboard.");
    }
}
