<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Committee;
use App\Models\CommitteeMembership;
use App\Models\CommitteePosition;
use App\Models\ReviewSeason;
use App\Models\User;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Volunteers extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────────────────────────────
     |  Public Properties
     * ───────────────────────────────────────────────────────────── */

    // Filters
    public ?string $date = null;
    public ?string $session = null; // 'am'|'pm'|null (volunteers can be either)
    public ?int $committeeFilter = null;
    public ?int $positionFilter = null;

    // Calendar navigation
    public int $calendarYear;
    public int $calendarMonth;

    // View modes
    public bool $monthlyView = false;
    public int $matrixYear;
    public int $matrixMonth;

    // Weekly analytics navigation
    public int $weeklyAnalyticsYear;
    public int $weeklyAnalyticsMonth;

    // Editing state
    public array $editingRow = [];
    public array $editingAttendance = [];
    public array $pendingChanges = [];

    // Modals
    public bool $showConfirmModal = false;
    public array $confirmModalData = [];
    public bool $showSelfEditError = false;
    public bool $showOutsideSeasonError = false;
    public array $outsideSeasonErrorData = [];

    /* ─────────────────────────────────────────────────────────────
     |  Protected Properties
     * ───────────────────────────────────────────────────────────── */

    protected AttendanceService $attendanceService;

    protected $listeners = [
        'attendanceUpdated' => '$refresh',
        'reviewSeasonUpdated' => '$refresh',
    ];

    /* ─────────────────────────────────────────────────────────────
     |  Lifecycle
     * ───────────────────────────────────────────────────────────── */

    public function boot(): void
    {
        $this->attendanceService = app(AttendanceService::class);
    }

    public function mount(): void
    {
        $now = now();
        $this->date = $now->toDateString();
        $this->calendarYear = $now->year;
        $this->calendarMonth = $now->month;
        $this->matrixYear = $now->year;
        $this->matrixMonth = $now->month;
        $this->weeklyAnalyticsYear = $now->year;
        $this->weeklyAnalyticsMonth = $now->month;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Session & Date Handling
     * ───────────────────────────────────────────────────────────── */

    public function setSession(?string $session): void
    {
        $this->session = $session ? strtolower($session) : null;
        $this->resetPage();
    }

    public function setDate(?string $date): void
    {
        if ($date) {
            $this->date = $date;
            $dt = Carbon::parse($date);
            $this->calendarYear = $dt->year;
            $this->calendarMonth = $dt->month;
        }
        $this->resetPage();
    }

    /* ─────────────────────────────────────────────────────────────
     |  Calendar Navigation
     * ───────────────────────────────────────────────────────────── */

    public function prevCalendarMonth(): void
    {
        $dt = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarYear = $dt->year;
        $this->calendarMonth = $dt->month;
    }

    public function nextCalendarMonth(): void
    {
        $dt = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarYear = $dt->year;
        $this->calendarMonth = $dt->month;
    }

    public function toggleMonthlyView(): void
    {
        $this->monthlyView = !$this->monthlyView;
        if ($this->monthlyView && $this->date) {
            $dt = Carbon::parse($this->date);
            $this->matrixYear = $dt->year;
            $this->matrixMonth = $dt->month;
        }
        $this->resetPage();
    }

    public function prevMatrixMonth(): void
    {
        $dt = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->subMonth();
        $this->matrixYear = $dt->year;
        $this->matrixMonth = $dt->month;
        $this->resetPage();
    }

    public function nextMatrixMonth(): void
    {
        $dt = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->addMonth();
        $this->matrixYear = $dt->year;
        $this->matrixMonth = $dt->month;
        $this->resetPage();
    }

    public function prevWeeklyAnalyticsMonth(): void
    {
        $dt = Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->subMonth();
        $this->weeklyAnalyticsYear = $dt->year;
        $this->weeklyAnalyticsMonth = $dt->month;
    }

    public function nextWeeklyAnalyticsMonth(): void
    {
        $dt = Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->addMonth();
        $this->weeklyAnalyticsYear = $dt->year;
        $this->weeklyAnalyticsMonth = $dt->month;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Filters
     * ───────────────────────────────────────────────────────────── */

    public function applyFilters(): void
    {
        $this->session = $this->session ? strtolower($this->session) : null;
        $this->resetPage();
    }

    /* ─────────────────────────────────────────────────────────────
     |  Editing
     * ───────────────────────────────────────────────────────────── */

    public function startEditing(int $userId): void
    {
        $this->editingRow[$userId] = true;
    }

    public function cancelEditing(int $userId): void
    {
        unset($this->editingRow[$userId]);

        // Clear pending changes for this user
        foreach ($this->editingAttendance as $key => $change) {
            if (($change['user_id'] ?? null) === $userId) {
                unset($this->editingAttendance[$key]);
                unset($this->pendingChanges[$key]);
            }
        }
    }

    public function updateAttendanceTime(
        ?int $attendanceId,
        int $userId,
        string $field, // 'time_in' or 'time_out'
        ?string $newTime,
        string $fullName,
        string $date
    ): void {
        $key = $attendanceId ?: "new_{$userId}_{$date}";

        if (!isset($this->editingAttendance[$key])) {
            $this->editingAttendance[$key] = [
                'attendance_id' => $attendanceId,
                'user_id' => $userId,
                'full_name' => $fullName,
                'date' => $date,
                'time_in' => null,
                'time_out' => null,
            ];
        }

        $this->editingAttendance[$key][$field] = $newTime;
        $this->pendingChanges[$key] = true;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Save Confirmation
     * ───────────────────────────────────────────────────────────── */

    public function prepareConfirmSave(int $userId, string $fullName): void
    {
        // Check for self-edit
        if (Auth::id() === $userId) {
            $this->showSelfEditError = true;
            return;
        }

        // Collect pending changes for this user
        $userChanges = collect($this->editingAttendance)->filter(function ($change) use ($userId) {
            return ($change['user_id'] ?? null) === $userId;
        });

        if ($userChanges->isEmpty()) {
            return;
        }

        // Check review season
        $reviewSeason = ReviewSeason::getActive();
        if ($reviewSeason) {
            foreach ($userChanges as $change) {
                $date = $change['date'] ?? null;
                if ($date && !$reviewSeason->isValidAttendanceDate($date)) {
                    $this->outsideSeasonErrorData = [
                        'date' => Carbon::parse($date)->format('F j, Y'),
                        'range' => $reviewSeason->range_label,
                    ];
                    $this->showOutsideSeasonError = true;
                    return;
                }
            }
        }

        $this->confirmModalData = [
            'user_id' => $userId,
            'full_name' => $fullName,
            'dates' => $userChanges->pluck('date')->unique()->map(fn($d) => Carbon::parse($d)->format('F j, Y'))->values()->all(),
            'changes' => $userChanges->toArray(),
        ];
        $this->showConfirmModal = true;
        $this->dispatch('open-confirm-modal');
    }

    public function confirmSaveAttendance(): void
    {
        $userId = $this->confirmModalData['user_id'] ?? null;
        if (!$userId) {
            $this->showConfirmModal = false;
            return;
        }

        $reviewSeason = ReviewSeason::getActive();

        foreach ($this->editingAttendance as $key => $change) {
            if (($change['user_id'] ?? null) !== $userId) {
                continue;
            }

            $date = $change['date'] ?? null;
            $timeIn = $change['time_in'] ?? null;
            $timeOut = $change['time_out'] ?? null;

            // Find existing record for this user + date
            $existingRecord = AttendanceRecord::where('user_id', $userId)
                ->whereDate('date', $date)
                ->first();

            if ($existingRecord) {
                if ($timeIn !== null) {
                    $existingRecord->time_in = $timeIn ?: null;
                }
                if ($timeOut !== null) {
                    $existingRecord->time_out = $timeOut ?: null;
                }
                $existingRecord->duration_minutes = $this->attendanceService->calculateVolunteerDuration($existingRecord->time_in, $existingRecord->time_out);
                $existingRecord->updated_by_id = Auth::id();
                $existingRecord->save();
            } else {
                // Create new record
                $durationMinutes = $this->attendanceService->calculateVolunteerDuration($timeIn, $timeOut);
                AttendanceRecord::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                    'duration_minutes' => $durationMinutes,
                    'review_season_id' => $reviewSeason?->id,
                    'recorded_by_id' => Auth::id(),
                ]);
            }

            unset($this->editingAttendance[$key]);
            unset($this->pendingChanges[$key]);
        }

        unset($this->editingRow[$userId]);
        $this->showConfirmModal = false;
        $this->confirmModalData = [];

        $this->dispatch('notify', type: 'success', message: 'Attendance saved successfully.');
        $this->dispatch('attendanceUpdated');
    }

    public function cancelConfirmModal(): void
    {
        $this->showConfirmModal = false;
        $this->confirmModalData = [];
    }

    public function closeSelfEditError(): void
    {
        $this->showSelfEditError = false;
    }

    public function closeOutsideSeasonError(): void
    {
        $this->showOutsideSeasonError = false;
        $this->outsideSeasonErrorData = [];
    }

    /* ─────────────────────────────────────────────────────────────
     |  Review Season Modal
     * ───────────────────────────────────────────────────────────── */

    public function openReviewSeasonModal(): void
    {
        $this->dispatch('openReviewSeasonModal');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Export Modal
     * ───────────────────────────────────────────────────────────── */

    public function openExportModal(): void
    {
        $this->dispatch('openExportModal', type: 'volunteers');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Render
     * ───────────────────────────────────────────────────────────── */

    public function render()
    {
        $actor = Auth::user();
        $reviewSeason = ReviewSeason::getActive();

        // Get all committees and positions for filter dropdowns
        $allCommittees = Committee::orderBy('name')->get();
        $allPositions = CommitteePosition::orderBy('name')->get();

        // Get weekend dates for the current view month
        $weekendDates = $this->attendanceService->getWeekendDatesForMonth(
            $this->monthlyView ? $this->matrixYear : $this->calendarYear,
            $this->monthlyView ? $this->matrixMonth : $this->calendarMonth,
            $reviewSeason
        );

        // Build memberships query - filter by volunteer roles: role_id 1=System Manager, 2=Executive, 3=Administrator, 4=Instructor
        // Query users directly with volunteer roles, then load their committee memberships
        $volunteersQuery = User::with([
            'attendanceRecords' => function ($q) use ($reviewSeason) {
                if ($this->date && !$this->monthlyView) {
                    $q->whereDate('date', $this->date);
                } elseif ($this->monthlyView) {
                    // For monthly view, load records for the entire month
                    $startOfMonth = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->startOfMonth();
                    $endOfMonth = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->endOfMonth();
                    $q->whereBetween('date', [$startOfMonth, $endOfMonth]);
                } elseif ($reviewSeason) {
                    $q->whereBetween('date', [$reviewSeason->start_date, $reviewSeason->end_date]);
                }
            },
            'committeeMemberships.committee',
            'committeeMemberships.committeePosition',
            'role',
        ])->whereIn('role_id', [1, 2, 3, 4]); // System Manager, Executive, Administrator, Instructor

        // Apply committee filter - only show users with matching committee membership
        if ($this->committeeFilter) {
            $volunteersQuery->whereHas('committeeMemberships', function ($q) {
                $q->where('committee_id', $this->committeeFilter);
            });
        }

        // Apply position filter - only show users with matching position
        if ($this->positionFilter) {
            $volunteersQuery->whereHas('committeeMemberships', function ($q) {
                $q->where('committee_position_id', $this->positionFilter);
            });
        }

        $volunteers = $volunteersQuery->get()->sortBy(fn($u) => strtolower($u->name ?? ''));

        // Build memberships collection for backward compatibility with the view
        // Each user can have multiple committee memberships, but we'll group by user for display
        $memberships = collect();
        foreach ($volunteers as $volunteer) {
            if ($volunteer->committeeMemberships->isNotEmpty()) {
                foreach ($volunteer->committeeMemberships as $membership) {
                    $membership->setRelation('user', $volunteer);
                    $memberships->push($membership);
                }
            } else {
                // Create a virtual membership for users without committee membership
                $virtualMembership = new CommitteeMembership();
                $virtualMembership->id = null;
                $virtualMembership->user_id = $volunteer->id;
                $virtualMembership->committee_id = null;
                $virtualMembership->committee_position_id = null;
                $virtualMembership->setRelation('user', $volunteer);
                $virtualMembership->setRelation('committee', null);
                $virtualMembership->setRelation('committeePosition', null);
                $memberships->push($virtualMembership);
            }
        }

        // Group memberships by committee for display (null committee = "No Committee")
        $membershipsByCommittee = $memberships->groupBy(fn($m) => $m->committee_id ?? 0);
        $committees = Committee::whereIn('id', $memberships->pluck('committee_id')->filter()->unique())->get()->keyBy('id');

        // Calculate weekly analytics using service
        // Use filtered volunteer IDs (respects committee/position filter)
        $filteredVolunteerIds = $volunteers->pluck('id')->unique();
        $analyticsRecords = AttendanceRecord::whereIn('user_id', $filteredVolunteerIds)
            ->whereBetween('date', [
                Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->startOfMonth(),
                Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->endOfMonth(),
            ])
            ->get();

        $weeklyAnalytics = $this->attendanceService->buildVolunteerWeeklyAnalytics(
            $this->weeklyAnalyticsYear,
            $this->weeklyAnalyticsMonth,
            $analyticsRecords,
            $volunteers->count(),
            $reviewSeason
        );

        // Build chart data
        $chartData = $this->attendanceService->buildVolunteerChartData($weeklyAnalytics);

        return view('livewire.attendance.volunteers', [
            'actor' => $actor,
            'reviewSeason' => $reviewSeason,
            'allCommittees' => $allCommittees,
            'allPositions' => $allPositions,
            'membershipsByCommittee' => $membershipsByCommittee,
            'committees' => $committees,
            'weekendDates' => $weekendDates,
            'weeklyAnalytics' => $weeklyAnalytics,
            'chartData' => $chartData,
        ]);
    }
}
