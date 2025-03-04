<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;
use App\Models\ProductDetail;

class ProductDetailPolicy
{
    public function create(User $user, Product $product): bool
    {
        return $user->vendors()->where('id', $product->vendor_id)->exists() || $user->isVendor();
    }

    public function update(User $user, ProductDetail $detail): bool
    {
        return $user->vendors()->where('id', $detail->product->vendor_id)->exists();
    }

    public function delete(User $user, ProductDetail $detail): bool
    {
        return $user->vendors()->where('id', $detail->product->vendor_id)->exists();
    }
}
