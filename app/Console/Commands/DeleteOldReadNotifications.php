<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Carbon\Carbon;

class DeleteOldReadNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:delete-old-read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete read notifications older than 7 days.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $deletedCount = User::withTrashed()
                ->get()
                ->each(function ($user) {
                    $user->notifications()->whereNotNull('read_at')
                        ->where('read_at', '<=', Carbon::now()->subDays(7))
                        ->delete();
                });

            $this->info("Deleted {$deletedCount} old read notifications.");
        } catch (\Exception $e) {
            $this->error('Error while deleting old read notifications: ' . $e->getMessage());
        }
    }
}
