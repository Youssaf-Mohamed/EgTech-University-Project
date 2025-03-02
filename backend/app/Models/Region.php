<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Region extends Model
{
    protected $fillable = ['name'];

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class, 'region_vendor')
            ->withPivot('delivery_cost', 'discount', 'description');
    }
}
