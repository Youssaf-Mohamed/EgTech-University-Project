<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'promotion_amount',
        'promotion_priority',
        'duration',
        'status',
    ];

    protected $dates = ['deleted_at'];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'vendor_promotion')
            ->withPivot('start_date', 'end_date', 'status')
            ->withTimestamps();
    }
}
