<?php

namespace App\Policies;

use App\Models\User;

class RosterPolicy
{
    /**
     * Determine if the given user may view roster pages.
     * Only System Manager, Executive, and Administrator can access.
     */
    public function view(User $user): bool
    {
        $allowed = [
            'System Manager',
            'Executive',
            'Administrator',
        ];

        $roleName = $user->role?->name ?? null;

        return in_array($roleName, $allowed, true);
    }

    /**
     * Determine if the given user may create roster users (students/volunteers).
     * Only System Manager and Executive can create.
     */
    public function create(User $user): bool
    {
        $allowed = [
            'System Manager',
            'Executive',
        ];

        $roleName = $user->role?->name ?? null;

        return in_array($roleName, $allowed, true);
    }

    /**
     * Determine if the given user may delete (soft delete) roster users.
     * Only System Manager and Executive can delete.
     */
    public function delete(User $user): bool
    {
        $allowed = [
            'System Manager',
            'Executive',
        ];

        $roleName = $user->role?->name ?? null;

        return in_array($roleName, $allowed, true);
    }

    /**
     * Determine if the given user may force delete roster users.
     * Only System Manager and Executive can force delete.
     */
    public function forceDelete(User $user): bool
    {
        $allowed = [
            'System Manager',
            'Executive',
        ];

        $roleName = $user->role?->name ?? null;

        return in_array($roleName, $allowed, true);
    }

    /**
     * Determine if the given user may restore deleted roster users.
     * Same as view - anyone who can view roster can restore.
     */
    public function restore(User $user): bool
    {
        return $this->view($user);
    }
}
