<?php

namespace App\Policies;

use App\Models\AttendanceRecord;
use App\Models\ReviewSeason;
use App\Models\User;
use Carbon\Carbon;

class AttendancePolicy
{
    /**
     * Roles that can manage all attendance records.
     */
    protected array $managerRoles = [
        'System Manager',
        'Executive',
        'Administrator',
    ];

    /**
     * Roles that can only view/manage their own attendance.
     */
    protected array $selfOnlyRoles = [
        'Instructor',
        'Student',
    ];

    /**
     * Get the user's role name.
     */
    protected function getRoleName(User $user): ?string
    {
        return $user->role?->name ?? null;
    }

    /**
     * Check if user is a manager (can manage all attendance).
     */
    protected function isManager(User $user): bool
    {
        return in_array($this->getRoleName($user), $this->managerRoles, true);
    }

    /**
     * Check if user is Executive (highest permission level).
     */
    protected function isExecutive(User $user): bool
    {
        return $this->getRoleName($user) === 'Executive';
    }

    /**
     * Determine if user can view any attendance records (roster view).
     */
    public function viewAny(User $user): bool
    {
        $allowed = ['System Manager', 'Executive', 'Administrator', 'Instructor'];
        return in_array($this->getRoleName($user), $allowed, true);
    }

    /**
     * Determine if user can view a specific attendance record.
     */
    public function view(User $user, AttendanceRecord $attendance): bool
    {
        // Managers can view all
        if ($this->isManager($user)) {
            return true;
        }

        // Others can only view their own
        return $attendance->user_id === $user->id;
    }

    /**
     * Determine if user can create attendance records.
     */
    public function create(User $user): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine if user can update an attendance record.
     */
    public function update(User $user, AttendanceRecord $attendance): bool
    {
        // Only managers can update
        if (!$this->isManager($user)) {
            return false;
        }

        // Prevent self-editing (must have another admin edit your attendance)
        if ($attendance->user_id === $user->id) {
            return false;
        }

        // Check if date is within active review season
        $reviewSeason = ReviewSeason::getActive();
        if ($reviewSeason && !$reviewSeason->isValidAttendanceDate($attendance->date)) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user can delete an attendance record.
     */
    public function delete(User $user, AttendanceRecord $attendance): bool
    {
        // Only Executive and System Manager can delete
        $allowed = ['System Manager', 'Executive'];
        if (!in_array($this->getRoleName($user), $allowed, true)) {
            return false;
        }

        // Prevent deleting own record
        if ($attendance->user_id === $user->id) {
            return false;
        }

        return true;
    }

    /**
     * Determine if user can perform bulk updates.
     */
    public function bulkUpdate(User $user): bool
    {
        return $this->isManager($user);
    }

    /**
     * Determine if user can export attendance data.
     */
    public function export(User $user): bool
    {
        $allowed = ['System Manager', 'Executive', 'Administrator'];
        return in_array($this->getRoleName($user), $allowed, true);
    }

    /**
     * Determine if user can manage review seasons.
     */
    public function manageReviewSeason(User $user): bool
    {
        // Only System Manager and Executive can manage review seasons
        $allowed = ['System Manager', 'Executive'];
        return in_array($this->getRoleName($user), $allowed, true);
    }

    /**
     * Check if a specific date can be edited (within active season + is weekend).
     */
    public function canEditDate(User $user, string $date): bool
    {
        if (!$this->isManager($user)) {
            return false;
        }

        $reviewSeason = ReviewSeason::getActive();
        if (!$reviewSeason) {
            // No active season - allow any weekend
            $dateCarbon = Carbon::parse($date);
            return $dateCarbon->isSaturday() || $dateCarbon->isSunday();
        }

        return $reviewSeason->isValidAttendanceDate($date);
    }

    /**
     * Check if user can edit attendance for a specific user.
     */
    public function canEditForUser(User $actor, User $targetUser): bool
    {
        if (!$this->isManager($actor)) {
            return false;
        }

        // Prevent self-edit
        return $actor->id !== $targetUser->id;
    }
}
