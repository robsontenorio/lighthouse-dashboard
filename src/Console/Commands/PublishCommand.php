<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Console\Migrations\MigrateCommand;

class PublishCommand extends Command
{
    protected $signature = 'lighthouse-dashboard:publish';

    protected $description = 'Publish Lighthouse Dashboard assets';

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
        $this->info("Published fresh assets for Lighthouse Dashboard.");
    }
}
