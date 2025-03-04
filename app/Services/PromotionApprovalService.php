<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;

class PromotionApprovalService
{
    /**
     * Approve a subscription request.
     *
     * @param Model $pivot
     * @return void
     */
    public function approve(Model $pivot)
    {
        $pivot->update([
            'status' => 'approved',
            'start_date' => now(),
            'end_date' => now()->addDays($pivot->duration),
        ]);

        $vendor = $pivot->vendor;
        if ($vendor && $vendor->user) {
            $vendor->user->notify(new \App\Notifications\PromotionApprovedNotification($vendor, $pivot->promotion));
        }
    }

    /**
     * Reject a subscription request.
     *
     * @param Model $pivot
     * @return void
     */
    public function reject(Model $pivot)
    {
        $pivot->update([
            'status' => 'rejected',
        ]);

        $vendor = $pivot->vendor;
        if ($vendor && $vendor->user) {
            $vendor->user->notify(new \App\Notifications\PromotionRejectedNotification($vendor, $pivot->promotion));
        }
    }
}
