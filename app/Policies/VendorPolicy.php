<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Vendor;
use Illuminate\Auth\Access\HandlesAuthorization;

class VendorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->isAdmin();
    }

    public function create(User $user)
    {
        return $user->isActive();
    }

    public function update(User $user, Vendor $vendor)
    {
        return $vendor->users->contains($user);
    }

    public function approve(User $user, Vendor $vendor)
    {
        return $user->isAdmin();
    }

    public function reject(User $user, Vendor $vendor)
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Vendor $vendor)
    {
        return $user->isAdmin() || $vendor->users->contains($user);
    }

    public function restore(User $user, Vendor $vendor)
    {
        return $user->isAdmin();
    }

    public function forceDelete(User $user, Vendor $vendor)
    {
        return $user->isAdmin();
    }
}
