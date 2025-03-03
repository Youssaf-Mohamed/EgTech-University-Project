<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vendor;
use Carbon\Carbon;

class DeleteOldVendors extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'vendors:delete-old';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete vendors that were soft-deleted more than 30 days ago.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $deletedVendors = Vendor::onlyTrashed()
                ->where('deleted_at', '<=', Carbon::now()->subDays(30))
                ->get();

            if ($deletedVendors->isEmpty()) {
                $this->info('No old vendors to delete.');
                return;
            }

            $deletedVendors->each(function ($vendor) {
                $vendor->forceDelete();
            });

            $this->info("Deleted {$deletedVendors->count()} old vendor(s).");
        } catch (\Exception $e) {
            $this->error('Error while deleting old vendors: ' . $e->getMessage());
        }
    }
}
