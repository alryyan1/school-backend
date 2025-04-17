<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization; // Correct import

class UserPolicy
{
    use HandlesAuthorization; // Correct trait

    // Allow admins to do anything before other checks
    public function before(User $user, string $ability): bool|null
    {
        return $user->role === 'admin' ? true : null;
    }

    public function viewAny(User $user): bool { return false; /* Allow only via before() */ }
    public function view(User $user, User $model): bool { return false; /* Allow only via before() */ }
    public function create(User $user): bool { return false; /* Allow only via before() */ }
    public function update(User $user, User $model): bool { return false; /* Allow only via before() */ }
    public function delete(User $user, User $model): bool { return false; /* Allow only via before() */ }
    public function restore(User $user, User $model): bool { return false; /* Allow only via before() */ }
    public function forceDelete(User $user, User $model): bool { return false; /* Allow only via before() */ }
    // Specific permission for password update, handled by before() for admins
    public function updatePassword(User $user, User $model): bool { return false; /* Allow only via before() */ }
}