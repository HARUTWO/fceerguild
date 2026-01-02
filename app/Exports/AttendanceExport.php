<?php

namespace App\Exports;

use App\Models\AttendanceRecord;
use App\Models\ReviewSeason;
use App\Services\AttendanceService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\FromArray;

class AttendanceExport implements FromArray
{
    protected string $type; // 'students' or 'volunteers'
    protected ?string $startDate;
    protected ?string $endDate;
    protected ?int $reviewSeasonId;
    protected array $filters;
    protected ?string $session;
    protected array $meta;

    protected AttendanceService $attendanceService;

    public function __construct(
        string $type,
        ?string $startDate = null,
        ?string $endDate = null,
        ?int $reviewSeasonId = null,
        array $filters = [],
        ?string $session = null,
        array $meta = []
    ) {
        $this->type = $type;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->reviewSeasonId = $reviewSeasonId;
        $this->filters = $filters;
        $this->session = $session ? strtolower($session) : null;
        $this->meta = $meta;
        $this->attendanceService = new AttendanceService();
    }

    public function array(): array
    {
        $rows = [];

        // ─────────────────────────────────────────────────────────
        // Metadata Header Rows
        // ─────────────────────────────────────────────────────────
        $typeLabel = ucfirst($this->type);
        $rows[] = ["Attendance Export: {$typeLabel}"];
        
        // Date range info
        if ($this->reviewSeasonId) {
            $season = ReviewSeason::find($this->reviewSeasonId);
            if ($season) {
                $rows[] = ["Review Season: {$season->range_label}"];
            }
        } elseif ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->format('M j, Y');
            $end = Carbon::parse($this->endDate)->format('M j, Y');
            $rows[] = ["Date Range: {$start} – {$end}"];
        }

        // Session (students only)
        if ($this->type === 'students' && $this->session) {
            $rows[] = ["Session: " . strtoupper($this->session)];
        }

        // Applied filters
        $filterLabels = $this->buildFilterLabels();
        if (!empty($filterLabels)) {
            $rows[] = ["Filters: " . implode(', ', $filterLabels)];
        }

        // Export metadata
        $exportedAt = $this->meta['exported_at'] ?? now()->format('Y-m-d H:i:s');
        $exportedBy = $this->meta['exported_by'] ?? (Auth::user()?->name ?? 'System');
        $rows[] = ["Exported At: {$exportedAt}"];
        $rows[] = ["Exported By: {$exportedBy}"];
        
        // Blank separator
        $rows[] = [];

        // ─────────────────────────────────────────────────────────
        // Headers
        // ─────────────────────────────────────────────────────────
        if ($this->type === 'students') {
            $rows[] = $this->getStudentHeaders();
        } else {
            $rows[] = $this->getVolunteerHeaders();
        }

        // ─────────────────────────────────────────────────────────
        // Data Rows
        // ─────────────────────────────────────────────────────────
        $query = $this->buildQuery();
        $index = 0;

        foreach ($query->cursor() as $record) {
            $index++;
            if ($this->type === 'students') {
                $rows[] = $this->mapStudentRow($record, $index);
            } else {
                $rows[] = $this->mapVolunteerRow($record, $index);
            }
        }

        return $rows;
    }

    protected function getStudentHeaders(): array
    {
        return [
            '#',
            'Name',
            'Student Number',
            'Batch',
            'Group',
            'Date',
            'Day',
            'Session',
            'Time In',
            'Status',
            'Excuse Letter',
        ];
    }

    protected function getVolunteerHeaders(): array
    {
        return [
            '#',
            'Name',
            'Committee',
            'Position',
            'Date',
            'Day',
            'Time In',
            'Time Out',
            'Duration',
            'Status',
        ];
    }

    protected function mapStudentRow(AttendanceRecord $record, int $index): array
    {
        $user = $record->user;
        $profile = $user?->fceerProfile;
        $date = Carbon::parse($record->date);

        // Determine status from studentStatus relationship
        $status = $record->studentStatus?->name ?? null;
        if (!$status && $record->attendance_time) {
            $status = $this->attendanceService->determineStudentStatus(
                $record->attendance_time,
                $record->session ?? 'am'
            );
        } elseif (!$status) {
            $status = 'Absent';
        }

        // Get excuse letter info
        $excuseLetter = $record->studentExcuseLetters->first();
        $excuseInfo = $excuseLetter ? ($excuseLetter->letter_status ?? 'Submitted') : '';

        return [
            $index,
            $user?->name ?? 'Unknown',
            $profile?->student_number ?? '',
            $profile?->batch?->batch_no ?? '',
            $profile?->classroom?->group ?? '',
            $date->format('Y-m-d'),
            $date->format('l'),
            strtoupper($record->session ?? ''),
            $record->attendance_time ? Carbon::parse($record->attendance_time)->format('H:i:s') : '',
            $status,
            $excuseInfo,
        ];
    }

    protected function mapVolunteerRow(AttendanceRecord $record, int $index): array
    {
        $user = $record->user;
        $date = Carbon::parse($record->date);

        // Get committee and position
        $committee = $user?->committeeMemberships->first()?->committee?->name ?? '';
        $position = $user?->committeeMemberships->first()?->committeePosition?->name ?? '';

        // Determine status
        $status = 'Present';
        if ($record->letter_id) {
            $status = 'Leave';
        } elseif (!$record->time_in) {
            $status = 'Absent';
        }

        // Calculate duration if both time_in and time_out exist
        $duration = '';
        if ($record->time_in && $record->time_out) {
            $in = Carbon::parse($record->time_in);
            $out = Carbon::parse($record->time_out);
            $mins = $in->diffInMinutes($out);
            $duration = floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
        }

        return [
            $index,
            $user?->name ?? 'Unknown',
            $committee,
            $position,
            $date->format('Y-m-d'),
            $date->format('l'),
            $record->time_in ? Carbon::parse($record->time_in)->format('H:i:s') : '—',
            $record->time_out ? Carbon::parse($record->time_out)->format('H:i:s') : '—',
            $duration ?: '—',
            $status,
        ];
    }

    protected function buildQuery(): Builder
    {
        $query = AttendanceRecord::query()
            ->with(['user.fceerProfile.batch', 'user.fceerProfile.classroom', 'user.committeeMemberships.committee', 'user.committeeMemberships.committeePosition', 'studentExcuseLetters', 'studentStatus']);

        // Filter by user role (students = role_id 5, volunteers = role_id 1,2,3,4)
        $query->whereHas('user', function ($q) {
            if ($this->type === 'students') {
                $q->where('role_id', 5);
            } else {
                $q->whereIn('role_id', [1, 2, 3, 4]);
            }
        });

        // Date range filter
        if ($this->reviewSeasonId) {
            $season = ReviewSeason::find($this->reviewSeasonId);
            if ($season) {
                $query->whereBetween('date', [$season->start_date, $season->end_date]);
            }
        } elseif ($this->startDate && $this->endDate) {
            $query->whereBetween('date', [$this->startDate, $this->endDate]);
        }

        // Session filter (students only)
        if ($this->type === 'students' && $this->session) {
            $query->where('session', $this->session);
        }

        // Additional filters
        if (!empty($this->filters['batch_id'])) {
            $query->whereHas('user.fceerProfile', function ($q) {
                $q->where('batch_id', $this->filters['batch_id']);
            });
        }

        if (!empty($this->filters['committee_id'])) {
            if ($this->type === 'students') {
                // For students, filter by classroom/group
                $query->whereHas('user.fceerProfile.classroom', function ($q) {
                    $q->where('id', $this->filters['committee_id']);
                });
            } else {
                // For volunteers, filter by committee
                $query->whereHas('user.committeeMemberships', function ($q) {
                    $q->where('committee_id', $this->filters['committee_id']);
                });
            }
        }

        if (!empty($this->filters['position_id'])) {
            $query->whereHas('user.committeeMemberships', function ($q) {
                $q->where('committee_position_id', $this->filters['position_id']);
            });
        }

        // Order by date, then by user
        $query->orderBy('date', 'asc')
              ->orderBy('user_id', 'asc');

        if ($this->type === 'students') {
            $query->orderBy('session', 'asc');
        } else {
            $query->orderBy('time_in', 'asc');
        }

        return $query;
    }

    protected function buildFilterLabels(): array
    {
        $labels = [];

        if (!empty($this->filters['batch_id'])) {
            $labels[] = "Batch #{$this->filters['batch_id']}";
        }

        if (!empty($this->filters['committee_id'])) {
            $labels[] = $this->type === 'students' ? "Group #{$this->filters['committee_id']}" : "Committee #{$this->filters['committee_id']}";
        }

        if (!empty($this->filters['position_id'])) {
            $labels[] = "Position #{$this->filters['position_id']}";
        }

        return $labels;
    }
}
