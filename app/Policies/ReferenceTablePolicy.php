<?php

namespace App\Policies;

use App\Models\User;

class ReferenceTablePolicy
{
    /**
     * Determine if the given user may manage reference tables.
     */
    public function manage(User $user): bool
    {
        $allowed = [
            'System Manager',
            'Executive',
        ];

        $roleName = $user->role?->name ?? null;

        return in_array($roleName, $allowed, true);
    }
}
