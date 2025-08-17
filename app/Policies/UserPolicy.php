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
        // Use Spatie role 'admin' to grant full access
        return method_exists($user, 'hasRole') && $user->hasRole('admin') ? true : null;
    }

    public function viewAny(User $user): bool { return $user->can('manage users'); }
    public function view(User $user, User $model): bool { return $user->can('manage users'); }
    public function create(User $user): bool { return $user->can('manage users'); }
    public function update(User $user, User $model): bool { return $user->can('manage users'); }
    public function delete(User $user, User $model): bool { return $user->can('manage users'); }
    public function restore(User $user, User $model): bool { return $user->can('manage users'); }
    public function forceDelete(User $user, User $model): bool { return $user->can('manage users'); }
    // Specific permission for password update
    public function updatePassword(User $user, User $model): bool { return $user->can('manage users'); }
}