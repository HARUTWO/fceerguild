<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\Classroom;
use App\Models\FceerProfile;
use App\Models\ReviewSeason;
use App\Policies\AttendancePolicy;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;

    /* ─────────────────────────────────────────────────────────────
     |  Public Properties
     * ───────────────────────────────────────────────────────────── */

    // Filters
    public ?string $date = null;
    public string $session = 'am';
    public ?int $committeeFilter = null;

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
    public string $analyticsSession = 'both'; // 'am', 'pm', or 'both'

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

    public function setSession(string $session): void
    {
        $this->session = strtolower($session);
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

    public function setAnalyticsSession(string $session): void
    {
        $this->analyticsSession = in_array($session, ['am', 'pm', 'both']) ? $session : 'both';
    }

    /* ─────────────────────────────────────────────────────────────
     |  Filters
     * ───────────────────────────────────────────────────────────── */

    public function applyFilters(): void
    {
        $this->session = $this->session ? strtolower($this->session) : 'am';
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
        $this->editingAttendance = array_filter($this->editingAttendance, function ($change) use ($userId) {
            return ($change['user_id'] ?? null) !== $userId;
        });
        $this->pendingChanges = array_filter($this->pendingChanges, function ($key) use ($userId) {
            return !str_contains($key, "_{$userId}_");
        }, ARRAY_FILTER_USE_KEY);
    }

    public function updateAttendanceStatus(
        ?int $attendanceId,
        int $userId,
        string $newStatus,
        string $fullName,
        string $date
    ): void {
        $key = $attendanceId ?: "new_{$userId}_{$date}";

        $this->editingAttendance[$key] = [
            'attendance_id' => $attendanceId,
            'status' => $newStatus,
            'user_id' => $userId,
            'full_name' => $fullName,
            'date' => $date,
            'session' => $this->session,
        ];
        $this->pendingChanges[$key] = true;
    }

    public function updateAttendanceTime(
        ?int $attendanceId,
        int $userId,
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
                'session' => $this->session,
            ];
        }

        $this->editingAttendance[$key]['time'] = $newTime;

        // Auto-calculate status from time
        if ($newTime) {
            $this->editingAttendance[$key]['status'] = $this->attendanceService->determineStudentStatus(
                $newTime,
                $this->session
            );
        }

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

            $attendanceId = $change['attendance_id'] ?? null;
            $newStatus = $change['status'] ?? null;
            $newTime = $change['time'] ?? null;
            $date = $change['date'] ?? null;
            $session = $change['session'] ?? 'am';

            // Resolve student_status_id from status name
            $statusName = $newStatus ?: ($newTime ? $this->attendanceService->determineStudentStatus($newTime, $session) : AttendanceService::STATUS_ABSENT);
            $studentAttendanceId = $this->attendanceService->getStudentStatusIdByName($statusName);

            if ($attendanceId) {
                // Update existing
                $attendance = AttendanceRecord::find($attendanceId);
                if ($attendance) {
                    if ($newTime !== null) {
                        $attendance->time_in = $newTime ?: null;
                    }
                    if ($studentAttendanceId) {
                        $attendance->student_status_id = $studentAttendanceId;
                    }
                    $attendance->updated_by_id = Auth::id();
                    $attendance->save();
                }
            } else {
                // Create new
                AttendanceRecord::create([
                    'user_id' => $userId,
                    'date' => $date,
                    'session' => $session,
                    'time_in' => $newTime ?: null,
                    'student_status_id' => $studentAttendanceId,
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
        $this->dispatch('openExportModal', type: 'students');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Render
     * ───────────────────────────────────────────────────────────── */

    public function render()
    {
        $actor = Auth::user();
        $reviewSeason = ReviewSeason::getActive();

        // Get all classrooms (groups) for filter dropdown
        $allCommittees = Classroom::orderBy('name')->get();

        // Get weekend dates for the current view month
        $weekendDates = $this->attendanceService->getWeekendDatesForMonth(
            $this->monthlyView ? $this->matrixYear : $this->calendarYear,
            $this->monthlyView ? $this->matrixMonth : $this->calendarMonth,
            $reviewSeason
        );

        // Build profiles query - filter by student role (id=5)
        $profilesQuery = FceerProfile::with([
            'user.attendanceRecords' => function ($q) use ($reviewSeason) {
                $q->where('session', $this->session);
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
            'user.attendanceRecords.studentStatus',
            'studentGroup',
            'batch',
            'user.role',
        ])->whereHas('user', function ($q) {
            $q->where('role_id', 5); // Filter by student role only
        });

        // Apply committee filter
        if ($this->committeeFilter) {
            $profilesQuery->where('student_group_id', $this->committeeFilter);
        }

        $profiles = $profilesQuery->get()->sortBy(fn($p) => strtolower($p->user?->name ?? ''));

        // Group profiles by room for display
        $profilesByRoom = $profiles->groupBy('student_group_id');
        $rooms = Classroom::whereIn('id', $profiles->pluck('student_group_id')->unique())->get()->keyBy('id');

        // Calculate weekly analytics using service
        // Use filtered student user IDs (respects group filter)
        $filteredStudentIds = $profiles->pluck('user_id')->unique();
        
        // Build analytics query based on selected session (AM, PM, or Both)
        $analyticsQuery = AttendanceRecord::with('studentStatus')
            ->whereIn('user_id', $filteredStudentIds)
            ->whereBetween('date', [
                Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->startOfMonth(),
                Carbon::create($this->weeklyAnalyticsYear, $this->weeklyAnalyticsMonth, 1)->endOfMonth(),
            ]);

        if ($this->analyticsSession !== 'both') {
            $analyticsQuery->where('session', $this->analyticsSession);
        }
        
        $analyticsRecords = $analyticsQuery->get();

        // Build analytics based on session mode
        if ($this->analyticsSession === 'both') {
            // Combined: get both AM and PM, then combine in the service
            $weeklyAnalytics = $this->attendanceService->buildStudentWeeklyAnalyticsCombined(
                $this->weeklyAnalyticsYear,
                $this->weeklyAnalyticsMonth,
                $analyticsRecords,
                $profiles->count(),
                $reviewSeason
            );
        } else {
            $weeklyAnalytics = $this->attendanceService->buildStudentWeeklyAnalytics(
                $this->weeklyAnalyticsYear,
                $this->weeklyAnalyticsMonth,
                $analyticsRecords,
                $this->analyticsSession,
                $profiles->count(),
                $reviewSeason
            );
        }

        // Build chart data
        $chartData = $this->attendanceService->buildStudentChartData($weeklyAnalytics);

        return view('livewire.attendance.students', [
            'actor' => $actor,
            'reviewSeason' => $reviewSeason,
            'allCommittees' => $allCommittees,
            'profilesByRoom' => $profilesByRoom,
            'rooms' => $rooms,
            'weekendDates' => $weekendDates,
            'weeklyAnalytics' => $weeklyAnalytics,
            'chartData' => $chartData,
        ]);
    }
}
