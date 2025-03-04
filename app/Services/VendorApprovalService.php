<?php

namespace App\Services;

use App\Models\Vendor;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorRejectedNotification;
use Illuminate\Support\Facades\Log;

class VendorApprovalService
{
    public function approve(Vendor $vendor)
    {
        $vendor->update(['status' => 'active']);

        $user = $vendor->users()->first();

        if ($user) {
            $user->update(['role' => 'vendor']);

            if ($user->role === 'vendor') {
                $user->notify(new VendorApprovedNotification($vendor));
            } else {
                Log::error('Failed to update role for user: ' . $user->id);
            }
        } else {
            Log::error('No associated user found for vendor: ' . $vendor->id);
        }
    }

    public function reject(Vendor $vendor)
    {
        $vendor->delete();

        if ($vendor->user) {
            $vendor->user->notify(new VendorRejectedNotification($vendor));
        }
    }
}
