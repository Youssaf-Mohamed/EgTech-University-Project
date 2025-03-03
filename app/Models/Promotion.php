<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Promotion extends Model
{
    protected $fillable = [
        'name',
        'promotion_amount',
        'promotion_priority',
        'duration',
        'status',
    ];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_promotion')
            ->withPivot('start_date', 'end_date', 'status')
            ->withTimestamps();
    }
}
