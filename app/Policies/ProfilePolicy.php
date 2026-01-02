<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Support\Facades\Log;

class ProfilePolicy
{
    /**
     * Role ID constants.
     * 1 = System Manager, 2 = Executive, 3 = Administrator, 4 = Instructor, 5 = Student
     */
    private const ROLE_SYSTEM_MANAGER = 1;
    private const ROLE_EXECUTIVE = 2;
    private const ROLE_ADMINISTRATOR = 3;
    private const ROLE_INSTRUCTOR = 4;
    private const ROLE_STUDENT = 5;

    /**
     * Admin role IDs that have full profile access.
     * System Manager, Executive, Administrator
     */
    private const ADMIN_ROLE_IDS = [self::ROLE_SYSTEM_MANAGER, self::ROLE_EXECUTIVE, self::ROLE_ADMINISTRATOR];

    /**
     * Role IDs that can manage committee memberships and classroom responsibilities.
     * System Manager, Executive only
     */
    private const MANAGEMENT_ROLE_IDS = [self::ROLE_SYSTEM_MANAGER, self::ROLE_EXECUTIVE];

    /**
     * Check if user has an admin role (System Manager, Executive, or Administrator).
     */
    private function isAdmin(User $user): bool
    {
        return in_array($user->role_id, self::ADMIN_ROLE_IDS);
    }

    /**
     * Check if user has a management role (System Manager or Executive).
     */
    private function isManager(User $user): bool
    {
        return in_array($user->role_id, self::MANAGEMENT_ROLE_IDS);
    }

    private function normalizeProfileUser(User $user, $profileUser): User
    {
        if (!$profileUser) {
            return $user;
        }
        
        if (!$profileUser instanceof User) {
            return User::find($profileUser) ?: $user;
        }
        
        return $profileUser;
    }

    /**
     * Determine whether the user can view the profile.
     */
    public function view(User $user, $profileUser): bool
    {
        if (!$user) return false;

        $profileUser = $this->normalizeProfileUser($user, $profileUser);

        // Users can view their own profile
        if ($user->id === $profileUser->id) return true;

        // System Manager, Executive, Administrator can view any profile
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can update the profile.
     */
    public function update(User $user, $profileUser): bool
    {
        if (!$user) return false;

        $profileUser = $this->normalizeProfileUser($user, $profileUser);

        // Users can update their own profile
        if ($user->id === $profileUser->id) return true;

        // System Manager, Executive, Administrator can update any profile
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can manage (full CRUD) others' profiles.
     */
    public function manage(User $user, $profileUser): bool
    {
        Log::info('ProfilePolicy::manage called', [
            'auth_id' => $user?->id,
            'auth_role_id' => $user?->role_id,
            'profileUser_raw' => $profileUser instanceof User ? $profileUser->id : $profileUser,
        ]);

        if (!$user) return false;

        $profileUser = $this->normalizeProfileUser($user, $profileUser);

        // Allow managing own profile
        if ($user->id === $profileUser->id) return true;

        // System Manager, Executive, Administrator can manage any profile
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can manage committee memberships.
     * Only System Managers and Executives can add/edit/delete committee memberships.
     */
    public function manageCommitteeMemberships(User $user, $profileUser): bool
    {
        if (!$user) return false;

        // System Manager, Executive can manage committee memberships
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can manage classroom responsibilities.
     * Only System Managers and Executives can add/edit/delete classroom responsibilities.
     */
    public function manageClassroomResponsibilities(User $user, $profileUser): bool
    {
        if (!$user) return false;

        // System Manager, Executive can manage classroom responsibilities
        return $this->isManager($user);
    }

    /**
     * Determine whether the user can manage FCEER profiles.
     * System Manager, Executive, Administrator can edit FCEER profiles.
     */
    public function manageFceerProfile(User $user, $profileUser): bool
    {
        if (!$user) return false;

        // System Manager, Executive, Administrator can manage FCEER profiles
        return $this->isAdmin($user);
    }

    /**
     * Determine whether the user can manage personal records (identification, contact details).
     * Users can edit their own personal records.
     * Admins can edit others' personal records.
     */
    public function managePersonal(User $user, $profileUser): bool
    {
        if (!$user) return false;

        $profileUser = $this->normalizeProfileUser($user, $profileUser);

        // Users can edit their own personal records
        if ($user->id === $profileUser->id) return true;

        // System Manager, Executive, Administrator can manage others' personal records
        return $this->isAdmin($user);
    }
}