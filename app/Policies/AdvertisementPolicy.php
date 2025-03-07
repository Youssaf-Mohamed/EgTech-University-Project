<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Advertisement;
use Illuminate\Auth\Access\Response;

class AdvertisementPolicy
{
    /**
     * Determine if the given user can create advertisements.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the given user can update the advertisement.
     */
    public function update(User $user, Advertisement $advertisement): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the given user can delete the advertisement.
     */
    public function delete(User $user, Advertisement $advertisement): bool
    {
        return $user->isAdmin();
    }
}
