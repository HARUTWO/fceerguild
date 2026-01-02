<?php

namespace App\Livewire\Attendance;

use App\Models\AttendanceRecord;
use App\Models\CommitteeMembership;
use App\Models\ReviewSeason;
use App\Models\User as UserModel;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithFileUploads;

class User extends Component
{
    use WithFileUploads;

    /* ─────────────────────────────────────────────────────────────
     |  Public Properties
     * ───────────────────────────────────────────────────────────── */

    public UserModel $user;
    public string $fullName = '';

    // View mode toggle
    public bool $monthlyView = false;

    // Session filter (am/pm) - for students
    public string $session = 'am';

    // Calendar state for daily view
    public int $calendarYear;
    public int $calendarMonth;
    public ?string $selectedDate = null;

    // Matrix month for monthly view
    public int $matrixYear;
    public int $matrixMonth;

    // Editable attendance tracking
    public array $editingRow = [];
    public array $editingAttendance = [];
    public array $pendingChanges = [];
    public bool $showConfirmModal = false;
    public array $confirmModalData = [];
    public bool $showSelfEditError = false;
    public bool $showOutsideSeasonError = false;
    public array $outsideSeasonErrorData = [];

    // Excuse letter modal (for students)
    public bool $showExcuseModal = false;
    public ?string $reason = null;
    public $letter_file;
    public ?string $excuseDate = null;
    public string $excuseSession = 'am';
    public string $letterStatus = 'received';
    public ?string $letterLink = null;

    // Excuse letter list modal
    public bool $showExcuseListModal = false;
    public $excuseLetters = [];
    public ?int $editingExcuseLetterId = null;
    public ?string $editReason = null;
    public ?string $editDate = null;
    public ?string $editLetterLink = null;
    public ?string $editLetterStatus = null;

    /* ─────────────────────────────────────────────────────────────
     |  Protected Properties
     * ───────────────────────────────────────────────────────────── */

    protected AttendanceService $attendanceService;

    protected $listeners = [
        'attendanceUpdated' => '$refresh',
    ];

    /* ─────────────────────────────────────────────────────────────
     |  Lifecycle
     * ───────────────────────────────────────────────────────────── */

    public function boot(): void
    {
        $this->attendanceService = app(AttendanceService::class);
    }

    public function mount(?int $userId = null): void
    {
        $actor = Auth::user();
        $targetUserId = $userId ?? $actor->id;

        // Load the target user
        $this->user = UserModel::with([
            'profile',
            'professionalCredentials.prefix',
            'professionalCredentials.suffix',
            'fceerProfile.studentGroup',
            'fceerProfile.batch',
            'committeeMemberships.committee',
            'committeeMemberships.committeePosition',
        ])->findOrFail($targetUserId);

        // Authorization check - can view this user's attendance?
        if (!Gate::allows('view-attendance', $this->user)) {
            abort(403, 'You are not authorized to view this user\'s attendance.');
        }

        // Build full name
        $this->fullName = $this->buildFullName();

        // Initialize calendar state
        $now = now();
        $this->calendarYear = $now->year;
        $this->calendarMonth = $now->month;
        $this->selectedDate = $now->toDateString();
        $this->matrixYear = $now->year;
        $this->matrixMonth = $now->month;
    }

    protected function buildFullName(): string
    {
        $name = $this->user->name;

        // Add professional titles if available (get first credential)
        $credentials = $this->user->professionalCredentials()->first();
        if ($credentials) {
            $prefix = $credentials->prefix?->name ?? '';
            $suffix = $credentials->suffix?->name ?? '';
            if ($prefix) {
                $name = $prefix . ' ' . $name;
            }
            if ($suffix) {
                $name = $name . ', ' . $suffix;
            }
        }

        return $name;
    }

    /* ─────────────────────────────────────────────────────────────
     |  View Helpers
     * ───────────────────────────────────────────────────────────── */

    public function isStudent(): bool
    {
        $roleName = strtolower($this->user->role?->name ?? '');
        return $roleName === 'student';
    }

    public function isVolunteer(): bool
    {
        $roleName = strtolower($this->user->role?->name ?? '');
        return in_array($roleName, ['administrator', 'instructor', 'system administrator', 'executive']);
    }

    /* ─────────────────────────────────────────────────────────────
     |  View Mode & Session
     * ───────────────────────────────────────────────────────────── */

    public function toggleMonthlyView(): void
    {
        $this->monthlyView = !$this->monthlyView;
    }

    public function setSession(string $session): void
    {
        $this->session = strtolower($session);
    }

    public function setDate(?string $date): void
    {
        if ($date) {
            $this->selectedDate = $date;
            $parsed = Carbon::parse($date);
            $this->calendarYear = $parsed->year;
            $this->calendarMonth = $parsed->month;
        }
    }

    /* ─────────────────────────────────────────────────────────────
     |  Calendar Navigation
     * ───────────────────────────────────────────────────────────── */

    public function prevCalendarMonth(): void
    {
        $dt = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->subMonth();
        $this->calendarYear = $dt->year;
        $this->calendarMonth = $dt->month;
        $this->selectedDate = $dt->toDateString();
    }

    public function nextCalendarMonth(): void
    {
        $dt = Carbon::create($this->calendarYear, $this->calendarMonth, 1)->addMonth();
        $this->calendarYear = $dt->year;
        $this->calendarMonth = $dt->month;
        $this->selectedDate = $dt->toDateString();
    }

    public function prevMatrixMonth(): void
    {
        $dt = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->subMonth();
        $this->matrixYear = $dt->year;
        $this->matrixMonth = $dt->month;
    }

    public function nextMatrixMonth(): void
    {
        $dt = Carbon::create($this->matrixYear, $this->matrixMonth, 1)->addMonth();
        $this->matrixYear = $dt->year;
        $this->matrixMonth = $dt->month;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Editing
     * ───────────────────────────────────────────────────────────── */

    public function startEditing(): void
    {
        $this->editingRow[$this->user->id] = true;
    }

    public function cancelEditing(): void
    {
        unset($this->editingRow[$this->user->id]);
        $this->editingAttendance = [];
        $this->pendingChanges = [];
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
     |  Student Attendance Updates
     * ───────────────────────────────────────────────────────────── */

    public function updateAttendanceStatus(?int $attendanceId, string $newStatus, string $date, string $session = 'am'): void
    {
        $key = $attendanceId ?: "new_{$this->user->id}_{$date}_{$session}";

        $this->editingAttendance[$key] = [
            'attendance_id' => $attendanceId,
            'status' => $newStatus,
            'user_id' => $this->user->id,
            'full_name' => $this->fullName,
            'date' => $date,
            'session' => $session,
        ];
        $this->pendingChanges[$key] = true;
    }

    public function updateStudentTime(?int $attendanceId, ?string $newTime, string $date, string $session = 'am'): void
    {
        $key = $attendanceId ?: "new_{$this->user->id}_{$date}_{$session}";

        if (!isset($this->editingAttendance[$key])) {
            $this->editingAttendance[$key] = [
                'attendance_id' => $attendanceId,
                'user_id' => $this->user->id,
                'full_name' => $this->fullName,
                'date' => $date,
                'session' => $session,
            ];
        }

        $this->editingAttendance[$key]['student_time'] = $newTime;

        // Auto-calculate status
        if ($newTime) {
            $this->editingAttendance[$key]['status'] = $this->attendanceService->determineStudentStatus($newTime, $session);
        }

        $this->pendingChanges[$key] = true;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Volunteer Attendance Updates
     * ───────────────────────────────────────────────────────────── */

    public function updateAttendanceTime(?int $attendanceId, string $field, ?string $newTime, string $date): void
    {
        $key = $attendanceId ?: "new_{$this->user->id}_{$date}";

        if (!isset($this->editingAttendance[$key])) {
            $this->editingAttendance[$key] = [
                'attendance_id' => $attendanceId,
                'user_id' => $this->user->id,
                'full_name' => $this->fullName,
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

    public function prepareConfirmSave(): void
    {
        // Check for self-edit
        if (Auth::id() === $this->user->id) {
            $this->showSelfEditError = true;
            return;
        }

        if (empty($this->editingAttendance)) {
            return;
        }

        // Check review season
        $reviewSeason = ReviewSeason::getActive();
        if ($reviewSeason) {
            foreach ($this->editingAttendance as $change) {
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

        $dates = collect($this->editingAttendance)->pluck('date')->unique()
            ->map(fn($d) => Carbon::parse($d)->format('F j, Y'))
            ->values()
            ->all();

        $this->confirmModalData = [
            'user_id' => $this->user->id,
            'full_name' => $this->fullName,
            'dates' => $dates,
            'changes' => $this->editingAttendance,
        ];
        $this->showConfirmModal = true;
    }

    public function confirmSaveAttendance(): void
    {
        $reviewSeason = ReviewSeason::getActive();

        foreach ($this->editingAttendance as $key => $change) {
            $attendanceId = $change['attendance_id'] ?? null;
            $date = $change['date'] ?? null;

            // Handle student time/status changes
            if (isset($change['student_time']) || isset($change['status'])) {
                $session = $change['session'] ?? 'am';
                $newTime = $change['student_time'] ?? null;
                $newStatus = $change['status'] ?? null;

                // Resolve student_status_id from status name
                $statusName = $newStatus ?: ($newTime ? $this->attendanceService->determineStudentStatus($newTime, $session) : AttendanceService::STATUS_ABSENT);
                $studentAttendanceId = $this->attendanceService->getStudentStatusIdByName($statusName);

                if ($attendanceId) {
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
                    AttendanceRecord::create([
                        'user_id' => $this->user->id,
                        'date' => $date,
                        'session' => $session,
                        'time_in' => $newTime ?: null,
                        'student_status_id' => $studentAttendanceId,
                        'review_season_id' => $reviewSeason?->id,
                        'recorded_by_id' => Auth::id(),
                    ]);
                }
            }

            // Handle volunteer time changes
            if (isset($change['time_in']) || isset($change['time_out'])) {
                $timeIn = $change['time_in'] ?? null;
                $timeOut = $change['time_out'] ?? null;

                if ($attendanceId) {
                    $attendance = AttendanceRecord::find($attendanceId);
                    if ($attendance) {
                        if ($timeIn !== null) {
                            $attendance->time_in = $timeIn ?: null;
                        }
                        if ($timeOut !== null) {
                            $attendance->time_out = $timeOut ?: null;
                        }
                        $attendance->duration_minutes = $this->attendanceService->calculateVolunteerDuration($attendance->time_in, $attendance->time_out);
                        $attendance->updated_by_id = Auth::id();
                        $attendance->save();
                    }
                } else {
                    $durationMinutes = $this->attendanceService->calculateVolunteerDuration($timeIn, $timeOut);
                    AttendanceRecord::create([
                        'user_id' => $this->user->id,
                        'date' => $date,
                        'time_in' => $timeIn,
                        'time_out' => $timeOut,
                        'duration_minutes' => $durationMinutes,
                        'review_season_id' => $reviewSeason?->id,
                        'recorded_by_id' => Auth::id(),
                    ]);
                }
            }

            unset($this->editingAttendance[$key]);
            unset($this->pendingChanges[$key]);
        }

        unset($this->editingRow[$this->user->id]);
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

    /* ─────────────────────────────────────────────────────────────
     |  Excuse Letter Modal (Students)
     * ───────────────────────────────────────────────────────────── */

    public function openExcuseModal(string $date, string $session = 'am'): void
    {
        $this->excuseDate = $date;
        $this->excuseSession = $session;
        $this->reason = null;
        $this->letter_file = null;
        $this->letterStatus = 'pending';
        $this->letterLink = null;
        $this->showExcuseModal = true;
    }

    public function closeExcuseModal(): void
    {
        $this->showExcuseModal = false;
        $this->excuseDate = null;
        $this->excuseSession = 'am';
        $this->reason = null;
        $this->letter_file = null;
        $this->letterStatus = 'pending';
        $this->letterLink = null;
    }

    public function submitExcuseLetter(): void
    {
        $this->validate([
            'excuseDate' => 'required|date',
            'excuseSession' => 'required|in:am,pm,both',
            'reason' => 'required|string|max:500',
            'letterStatus' => 'required|in:pending,approved,rejected',
            'letter_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'letterLink' => 'nullable|url|max:500',
        ]);

        // Handle file upload if provided
        $filePath = null;
        if ($this->letter_file) {
            $filePath = $this->letter_file->store('excuse-letters', 'public');
        }

        // Determine sessions to process
        $sessions = $this->excuseSession === 'both' ? ['am', 'pm'] : [$this->excuseSession];

        // Create excuse letter record
        $excuseLetter = \App\Models\StudentExcuseLetter::create([
            'user_id' => $this->user->id,
            'reason' => $this->reason,
            'date_attendance' => $this->excuseDate,
            'letter_status' => 'received',
            'letter_link' => $this->letterLink ?? $filePath,
            'updated_by_id' => Auth::id(),
        ]);

        // Link excuse letter to existing attendance records (without changing status)
        foreach ($sessions as $sess) {
            $attendance = AttendanceRecord::where('user_id', $this->user->id)
                ->whereDate('date', $this->excuseDate)
                ->where('session', $sess)
                ->first();

            if ($attendance) {
                // Link to excuse letter (status can be manually updated by admin later)
                $excuseLetter->attendanceRecords()->attach($attendance->id);
            }
        }

        $this->closeExcuseModal();
        $this->dispatch('notify', type: 'success', message: 'Excuse letter submitted successfully.');
        $this->dispatch('attendanceUpdated');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Excuse Letter List Management
     * ───────────────────────────────────────────────────────────── */

    public function openExcuseLetterList()
    {
        $this->loadExcuseLetters();
        $this->showExcuseListModal = true;
    }

    public function closeExcuseLetterList()
    {
        $this->showExcuseListModal = false;
        $this->reset(['editingExcuseLetterId', 'editReason', 'editDate', 'editLetterLink', 'editLetterStatus']);
    }

    public function loadExcuseLetters()
    {
        $this->excuseLetters = \App\Models\StudentExcuseLetter::where('user_id', $this->user->id)
            ->orderBy('date_attendance', 'desc')
            ->get()
            ->map(function($letter) {
                return [
                    'id' => $letter->id,
                    'date_attendance' => $letter->date_attendance,
                    'reason' => $letter->reason,
                    'letter_status' => $letter->letter_status,
                    'letter_link' => $letter->letter_link,
                    'created_at' => $letter->created_at,
                ];
            })
            ->toArray();
    }

    public function startEditExcuseLetter($letterId)
    {
        $letter = \App\Models\StudentExcuseLetter::find($letterId);
        if (!$letter || $letter->user_id !== $this->user->id) {
            $this->dispatch('toaster', message: 'Excuse letter not found.', type: 'error');
            return;
        }

        $this->editingExcuseLetterId = $letter->id;
        $this->editReason = $letter->reason;
        $this->editDate = $letter->date_attendance;
        $this->editLetterLink = $letter->letter_link;
        $this->editLetterStatus = $letter->letter_status;
    }

    public function cancelEditExcuseLetter()
    {
        $this->reset(['editingExcuseLetterId', 'editReason', 'editDate', 'editLetterLink', 'editLetterStatus']);
    }

    public function saveEditExcuseLetter()
    {
        $this->validate([
            'editReason' => 'required|string',
            'editDate' => 'required|date',
            'editLetterLink' => 'nullable|url',
        ]);

        $letter = \App\Models\StudentExcuseLetter::find($this->editingExcuseLetterId);
        if (!$letter || $letter->user_id !== $this->user->id) {
            $this->dispatch('toaster', message: 'Excuse letter not found.', type: 'error');
            return;
        }

        $letter->update([
            'reason' => $this->editReason,
            'date_attendance' => $this->editDate,
            'letter_link' => $this->editLetterLink,
            'updated_by_id' => Auth::id(),
        ]);

        $this->cancelEditExcuseLetter();
        $this->loadExcuseLetters();
        $this->dispatch('toaster', message: 'Excuse letter updated successfully.', type: 'success');
    }

    public function deleteExcuseLetter($letterId)
    {
        $letter = \App\Models\StudentExcuseLetter::find($letterId);
        if (!$letter || $letter->user_id !== $this->user->id) {
            $this->dispatch('toaster', message: 'Excuse letter not found.', type: 'error');
            return;
        }

        $letter->delete();
        $this->loadExcuseLetters();
        $this->dispatch('toaster', message: 'Excuse letter deleted successfully.', type: 'success');
        $this->dispatch('attendanceUpdated');
    }

    /* ─────────────────────────────────────────────────────────────
     |  Render
     * ───────────────────────────────────────────────────────────── */

    public function render()
    {
        $actor = Auth::user();
        $reviewSeason = ReviewSeason::getActive();
        $canEdit = Gate::allows('update-attendance', $this->user) && Auth::id() !== $this->user->id;

        // Get weekend dates for the current view month
        $weekendDates = $this->attendanceService->getWeekendDatesForMonth(
            $this->monthlyView ? $this->matrixYear : $this->calendarYear,
            $this->monthlyView ? $this->matrixMonth : $this->calendarMonth,
            $reviewSeason
        );

        // Load attendance records for current view
        $attendanceQuery = AttendanceRecord::where('user_id', $this->user->id)
            ->with('studentExcuseLetters');

        if ($this->isStudent()) {
            $attendanceQuery->where('session', $this->session);
        }

        if (!$this->monthlyView && $this->selectedDate) {
            $attendanceQuery->whereDate('date', $this->selectedDate);
        } elseif ($this->monthlyView) {
            $attendanceQuery->whereYear('date', $this->matrixYear)
                ->whereMonth('date', $this->matrixMonth);
        }

        $attendanceRecords = $attendanceQuery->orderBy('date', 'desc')->get();

        // Get volunteer committees if applicable
        $committeeMemberships = collect();
        if ($this->isVolunteer()) {
            $committeeMemberships = $this->user->committeeMemberships ?? collect();
        }

        // Calculate summary statistics for this user
        $summary = $this->calculateUserSummary($reviewSeason);

        return view('livewire.attendance.user', [
            'actor' => $actor,
            'reviewSeason' => $reviewSeason,
            'canEdit' => $canEdit,
            'weekendDates' => $weekendDates,
            'attendanceRecords' => $attendanceRecords,
            'committeeMemberships' => $committeeMemberships,
            'summary' => $summary,
        ]);
    }

    protected function calculateUserSummary(?ReviewSeason $reviewSeason): array
    {
        $query = AttendanceRecord::where('user_id', $this->user->id);

        if ($reviewSeason) {
            $query->whereBetween('date', [$reviewSeason->start_date, $reviewSeason->end_date]);
        }

        $records = $query->get();

        if ($this->isStudent()) {
            // Only count records that have explicit status set
            // Don't count missing attendance dates
            $onTime = $records->filter(function($r) {
                return $r->studentStatus && strtolower($r->studentStatus->name) === 'on time';
            })->count();
            
            $late = $records->filter(function($r) {
                return $r->studentStatus && strtolower($r->studentStatus->name) === 'late';
            })->count();
            
            $excused = $records->filter(function($r) {
                return $r->studentStatus && strtolower($r->studentStatus->name) === 'excused';
            })->count();
            
            $absent = $records->filter(function($r) {
                return $r->studentStatus && strtolower($r->studentStatus->name) === 'absent';
            })->count();

            return [
                'total' => $records->count(),
                'on_time' => $onTime,
                'late' => $late,
                'excused' => $excused,
                'absent' => $absent,
            ];
        } else {
            $totalMinutes = $records->sum('duration_minutes');
            $hours = floor($totalMinutes / 60);
            $minutes = $totalMinutes % 60;

            return [
                'total_days' => $records->whereNotNull('time_in')->count(),
                'total_hours' => $this->attendanceService->formatDuration($totalMinutes),
                'total_hours_raw' => $hours,
                'total_minutes_raw' => $minutes,
            ];
        }
    }
}
