<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\ScheduleDeleteOldReadNotifications;

class ScheduleNotificationsCleanup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'schedule:notifications-cleanup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Schedule the deletion of old read notifications.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            ScheduleDeleteOldReadNotifications::dispatch()->delay(now()->addMinutes(1)); 
            $this->info('Notifications cleanup job has been scheduled.');
        } catch (\Exception $e) {
            $this->error('Error scheduling notifications cleanup job: ' . $e->getMessage());
        }
    }
}
