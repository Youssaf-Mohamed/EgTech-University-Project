<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScheduleVendorsCleanup;

class ScheduleVendorsCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:vendors-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule the cleanup of old deleted vendors.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            ScheduleVendorsCleanup::dispatch()->delay(now()->addMinutes(1));
            $this->info('Vendors cleanup job has been scheduled.');
        } catch (\Exception $e) {
            $this->error('Error scheduling vendors cleanup job: ' . $e->getMessage());
        }
    }
}
