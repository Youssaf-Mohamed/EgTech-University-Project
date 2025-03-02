<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
    /**
     * Determine if the user can view the users list.
     */
    public function viewAny(User $user)
    {
        return $user->role === 'admin';
    }

    /**
     * Determine if the user can delete another user.
     */
    public function delete(User $user, User $targetUser)
    {
        return $user->role === 'admin' || $user->id === $targetUser->id;
    }
}
