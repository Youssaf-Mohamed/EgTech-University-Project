<?php

namespace App\Services;

use App\Models\Vendor;

class VendorApprovalService
{
    public function approve(Vendor $vendor)
    {
        $vendor->update(['status' => 'active']);
    }

    public function reject(Vendor $vendor)
    {
        $vendor->delete();
    }
}
