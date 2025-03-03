<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Review;

class ReviewPolicy
{
    public function update(User $user, Review $review): bool
    {
        return $user->id === $review->user_id;
    }

    public function delete(User $user, Review $review): bool
    {
        return $user->id === $review->user_id || $user->is_admin;
    }
}
