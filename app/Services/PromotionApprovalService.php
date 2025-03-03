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
        ]);
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
    }
}
