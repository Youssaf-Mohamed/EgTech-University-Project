<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Product;

class ProductPolicy
{
    public function create(User $user): bool
    {
        return $user->isActive() && ($user->isAdmin() || $user->vendors()->exists());
    }


    public function update(User $user, Product $product): bool
    {
        return $user->vendors()->where('id', $product->vendor_id)->exists();
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->vendors()->where('id', $product->vendor_id)->exists() || $user->isAdmin();
    }
}
