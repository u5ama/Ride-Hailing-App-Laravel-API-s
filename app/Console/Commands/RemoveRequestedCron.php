<?php

namespace App\Console\Commands;

use App\Utility\Utility;
use Illuminate\Console\Command;

class RemoveRequestedCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'removeRequest:run';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        \Log::info("Cron is working fine!");
        Utility::removeRequestedJobExpired();
        \Log::info("Cron is working fine!");

        $this->info('Demo:Cron Cummand Run successfully!');
    }
}
