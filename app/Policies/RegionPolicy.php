<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Region;

class RegionPolicy
{
    /**
     * Determine if the user can view any models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function viewAny(User $user)
    {
        return true; // Anyone can view regions
    }

    /**
     * Determine if the user can create models.
     *
     * @param  \App\Models\User  $user
     * @return mixed
     */
    public function create(User $user)
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Region  $region
     * @return mixed
     */
    public function update(User $user, Region $region)
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the model.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Region  $region
     * @return mixed
     */
    public function delete(User $user, Region $region)
    {
        return $user->isAdmin();
    }
}
