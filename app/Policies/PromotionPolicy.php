<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Promotion;

class PromotionPolicy
{
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    public function update(User $user, Promotion $promotion)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Promotion $promotion)
    {
        return $user->isAdmin();
    }

    public function approve(User $user, Promotion $promotion)
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Promotion $promotion)
    {
        return $user->isAdmin();
    }
}
