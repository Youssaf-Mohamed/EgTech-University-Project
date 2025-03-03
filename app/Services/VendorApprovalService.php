<?php

namespace App\Services;

use App\Models\Vendor;
use App\Notifications\VendorApprovedNotification;
use App\Notifications\VendorRejectedNotification;

class VendorApprovalService
{
    public function approve(Vendor $vendor)
    {
        $vendor->update(['status' => 'active']);

        if ($vendor->user) {
            $vendor->user->notify(new VendorApprovedNotification($vendor));
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
