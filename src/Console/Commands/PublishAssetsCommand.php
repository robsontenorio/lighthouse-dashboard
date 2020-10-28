<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PublishAssetsCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:publish-assets';

    protected $description = 'Publish Lighthouse Dashboard assets only.';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->callSilent('vendor:publish', [
            '--tag' => 'lighthouse-dashboard-assets',
            '--force' => true
        ]);

        $this->info("Published fresh assets for Lighthouse Dashboard.");
    }
}
