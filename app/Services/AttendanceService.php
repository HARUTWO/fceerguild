<?php

namespace App\Services;

use App\Models\AttendanceRecord;
use App\Models\ReviewSeason;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    public const CACHE_TTL = 300;
    public const CACHE_PREFIX = 'attendance:';

    public const LATE_THRESHOLD_AM = '08:15';
    public const LATE_THRESHOLD_PM = '12:15';

    public const STATUS_ON_TIME = 'On Time';
    public const STATUS_LATE = 'Late';
    public const STATUS_EXCUSED = 'Excused';
    public const STATUS_ABSENT = 'Absent';
    public const STATUS_NA = 'N/A';

    protected static ?array $studentStatusIdCache = null;

    public function getStudentStatusIdByName(string $statusName): ?int
    {
        if (self::$studentStatusIdCache === null) {
            self::$studentStatusIdCache = \App\Models\StudentAttendanceStatus::pluck('id', 'name')->toArray();
        }
        return self::$studentStatusIdCache[$statusName] ?? null;
    }

    public function getStudentStatusNameById(?int $statusId): ?string
    {
        if (!$statusId) {
            return null;
        }
        if (self::$studentStatusIdCache === null) {
            self::$studentStatusIdCache = \App\Models\StudentAttendanceStatus::pluck('id', 'name')->toArray();
        }
        return array_search($statusId, self::$studentStatusIdCache) ?: null;
    }

    public function getStudentStatusWithPoints(?int $statusId): ?array
    {
        if (!$statusId) {
            return null;
        }
        $status = \App\Models\StudentAttendanceStatus::find($statusId);
        return $status ? ['name' => $status->name, 'point' => $status->point] : null;
    }

    /**
     * Determine student attendance status based on time and session.
     *
     * @param string|null $time Time string (H:i or H:i:s)
     * @param string $session 'am' or 'pm'
     * @return string Status constant
     */
    public function determineStudentStatus(?string $time, string $session): string
    {
        if (!$time) {
            return self::STATUS_ABSENT;
        }

        $session = strtolower($session);
        $timeCarbon = Carbon::parse($time);
        $timeStr = $timeCarbon->format('H:i');

        if ($session === 'am') {
            return $timeStr > self::LATE_THRESHOLD_AM ? self::STATUS_LATE : self::STATUS_ON_TIME;
        }

        if ($session === 'pm') {
            return $timeStr > self::LATE_THRESHOLD_PM ? self::STATUS_LATE : self::STATUS_ON_TIME;
        }

        return self::STATUS_ON_TIME;
    }

    /**
     * Calculate volunteer duration between time in and time out.
     *
     * @param string|null $timeIn
     * @param string|null $timeOut
     * @return int Duration in minutes
     */
    public function calculateVolunteerDuration(?string $timeIn, ?string $timeOut): int
    {
        if (!$timeIn || !$timeOut) {
            return 0;
        }

        $in = Carbon::parse($timeIn);
        $out = Carbon::parse($timeOut);

        if ($out->lt($in)) {
            return 0;
        }

        return $in->diffInMinutes($out);
    }

    public function formatDuration(int $minutes): string
    {
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;

        return "{$hours}h {$mins}m";
    }

    /* ─────────────────────────────────────────────────────────────
     |  Date Utilities
     * ───────────────────────────────────────────────────────────── */

    /**
     * Get weekend dates for a specific month.
     *
     * @param int $year
     * @param int $month
     * @param ReviewSeason|null $reviewSeason Optional season to clamp dates
     * @return array<string>
     */
    public function getWeekendDatesForMonth(int $year, int $month, ?ReviewSeason $reviewSeason = null): array
    {
        if ($reviewSeason) {
            return $reviewSeason->getWeekendDatesForMonth($year, $month);
        }

        $start = Carbon::create($year, $month, 1)->startOfMonth();
        $end = $start->copy()->endOfMonth();
        $dates = [];

        $current = $start->copy();
        while ($current->lte($end)) {
            if ($current->isSaturday() || $current->isSunday()) {
                $dates[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        return $dates;
    }

    /**
     * Check if a date is valid for attendance editing.
     */
    public function canEditDate($date, ?ReviewSeason $reviewSeason = null): bool
    {
        $check = Carbon::parse($date);

        // Must be a weekend
        if (!$check->isSaturday() && !$check->isSunday()) {
            return false;
        }

        // If review season is set, must be within season
        if ($reviewSeason && !$reviewSeason->isDateWithinSeason($check)) {
            return false;
        }

        return true;
    }

    /**
     * Group weekend dates into Saturday-Sunday week pairs.
     *
     * @param array<string> $dates Y-m-d formatted dates
     * @return array<array{week_number: int, label: string, dates: array}>
     */
    public function groupDatesIntoWeeks(array $dates): array
    {
        $weeks = [];
        $currentWeek = [];
        $weekNumber = 0;

        foreach ($dates as $dateStr) {
            $date = Carbon::parse($dateStr);

            if ($date->isSaturday()) {
                // Start new week
                if (!empty($currentWeek)) {
                    $weekNumber++;
                    $weeks[] = $this->buildWeekEntry($weekNumber, $currentWeek);
                }
                $currentWeek = [$dateStr];
            } elseif ($date->isSunday()) {
                $currentWeek[] = $dateStr;
                $weekNumber++;
                $weeks[] = $this->buildWeekEntry($weekNumber, $currentWeek);
                $currentWeek = [];
            }
        }

        // Handle trailing Saturday without Sunday
        if (!empty($currentWeek)) {
            $weekNumber++;
            $weeks[] = $this->buildWeekEntry($weekNumber, $currentWeek);
        }

        return $weeks;
    }

    private function buildWeekEntry(int $weekNumber, array $dates): array
    {
        $first = Carbon::parse($dates[0]);
        $last = Carbon::parse(end($dates));

        $label = count($dates) > 1
            ? $first->format('M j') . '-' . $last->format('j')
            : $first->format('M j');

        return [
            'week_number' => $weekNumber,
            'label' => "Week {$weekNumber} ({$label})",
            'dates' => $dates,
        ];
    }

    /* ─────────────────────────────────────────────────────────────
     |  Student Analytics
     * ───────────────────────────────────────────────────────────── */

    /**
     * Calculate student analytics for given dates and records.
     *
     * @param array<string> $weekendDates
     * @param Collection $records AttendanceRecord collection
     * @param string $session 'am' or 'pm'
     * @param ReviewSeason|null $reviewSeason
     * @return array{on_time: int, late: int, absent: int, excused: int, na: int, total_sessions: int}
     */
    public function calculateStudentAnalytics(
        array $weekendDates,
        Collection $records,
        string $session,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $session = strtolower($session);
        $result = [
            'on_time' => 0,
            'late' => 0,
            'absent' => 0,
            'excused' => 0,
            'na' => 0,
            'total_sessions' => count($weekendDates),
        ];

        // Build lookup: date_session => record
        $recordMap = $records->keyBy(function ($r) {
            return Carbon::parse($r->date)->format('Y-m-d') . '_' . strtolower($r->session ?? '');
        });

        foreach ($weekendDates as $dateStr) {
            // Check if within season
            if ($reviewSeason && !$reviewSeason->isDateWithinSeason($dateStr)) {
                $result['na']++;
                continue;
            }

            $key = $dateStr . '_' . $session;
            $record = $recordMap->get($key);

            if (!$record) {
                $result['absent']++;
                continue;
            }

            // Get status name from the related StudentAttendanceStatus model
            $statusName = $record->studentStatus?->name ?? null;
            $status = strtolower($statusName ?? '');

            if ($status === 'excused') {
                $result['excused']++;
            } elseif ($status === 'n/a') {
                $result['na']++;
            } elseif ($status === 'late') {
                $result['late']++;
            } elseif ($status === 'on time') {
                $result['on_time']++;
            } elseif ($status === 'absent') {
                $result['absent']++;
            } elseif ($record->time_in) {
                // Fallback: calculate from time
                $computed = $this->determineStudentStatus($record->time_in, $session);
                if ($computed === self::STATUS_LATE) {
                    $result['late']++;
                } else {
                    $result['on_time']++;
                }
            } else {
                $result['absent']++;
            }
        }

        return $result;
    }

    /**
     * Calculate combined student analytics (AM + PM) for given dates.
     */
    public function calculateStudentAnalyticsCombined(
        array $weekendDates,
        Collection $records,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $am = $this->calculateStudentAnalytics($weekendDates, $records, 'am', $reviewSeason);
        $pm = $this->calculateStudentAnalytics($weekendDates, $records, 'pm', $reviewSeason);

        return [
            'on_time' => $am['on_time'] + $pm['on_time'],
            'late' => $am['late'] + $pm['late'],
            'absent' => $am['absent'] + $pm['absent'],
            'excused' => $am['excused'] + $pm['excused'],
            'na' => $am['na'] + $pm['na'],
            'total_sessions' => $am['total_sessions'] + $pm['total_sessions'],
        ];
    }

    /* ─────────────────────────────────────────────────────────────
     |  Volunteer Analytics
     * ───────────────────────────────────────────────────────────── */

    /**
     * Calculate volunteer analytics for given dates and records.
     *
     *
     * @param array<string> $weekendDates
     * @param Collection $records AttendanceRecord collection (one record per user per date)
     * @param int $totalVolunteers Total volunteers in scope (used to compute absences)
     * @param ReviewSeason|null $reviewSeason
     * @return array{present: int, absent: int, total_minutes: int, total_days: int, avg_minutes_per_day: int}
     */
    public function calculateVolunteerAnalytics(
        array $weekendDates,
        Collection $records,
        int $totalVolunteers,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $result = [
            'present' => 0,
            'absent' => 0,
            'total_minutes' => 0,
            'total_days' => count($weekendDates),
            'avg_minutes_per_day' => 0,
        ];

        // Filter dates within review season
        $validDates = collect($weekendDates)->filter(function ($dateStr) use ($reviewSeason) {
            return !$reviewSeason || $reviewSeason->isDateWithinSeason($dateStr);
        })->values()->all();

        if (empty($validDates)) {
            return $result;
        }

        // Get unique volunteers who were present at least once during this period
        $presentVolunteerIds = $records
            ->filter(fn($r) => !empty($r->time_in) && in_array(Carbon::parse($r->date)->format('Y-m-d'), $validDates))
            ->pluck('user_id')
            ->unique();

        $result['present'] = $presentVolunteerIds->count();
        $result['absent'] = max($totalVolunteers - $result['present'], 0);

        // Calculate total minutes for all records in the period
        foreach ($records as $record) {
            $dateStr = Carbon::parse($record->date)->format('Y-m-d');
            if (in_array($dateStr, $validDates) && !empty($record->time_in) && !empty($record->time_out)) {
                $result['total_minutes'] += $this->calculateVolunteerDuration(
                    $record->time_in,
                    $record->time_out
                );
            }
        }

        // Calculate average
        if ($result['present'] > 0) {
            $result['avg_minutes_per_day'] = (int) round($result['total_minutes'] / $result['present']);
        }

        return $result;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Weekly Analytics (Chart.js Data)
     * ───────────────────────────────────────────────────────────── */

    /**
     * Build weekly analytics for students (for Chart.js).
     *
     * @param int $year
     * @param int $month
     * @param Collection $records All records for the month
     * @param string $session 'am' or 'pm'
     * @param int $totalStudents Total count of students for percentage calculation
     * @param ReviewSeason|null $reviewSeason
     * @return array
     */
    public function buildStudentWeeklyAnalytics(
        int $year,
        int $month,
        Collection $records,
        string $session,
        int $totalStudents,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $weekendDates = $this->getWeekendDatesForMonth($year, $month, $reviewSeason);
        $weeks = $this->groupDatesIntoWeeks($weekendDates);
        $session = strtolower($session);

        $result = [
            'weeks' => [],
            'labels' => [],
            'on_time' => [],
            'late' => [],
            'absent' => [],
            'excused' => [],
            'month' => $month,
            'year' => $year,
            'month_label' => Carbon::create($year, $month, 1)->format('F Y'),
        ];

        foreach ($weeks as $week) {
            // Filter records for this week and session
            $weekRecords = $records->filter(function ($r) use ($week, $session) {
                $dateStr = Carbon::parse($r->date)->format('Y-m-d');
                $recordSession = strtolower($r->session ?? '');
                return in_array($dateStr, $week['dates']) && $recordSession === $session;
            });

            // Count status OCCURRENCES across all days in the week for this session
            $onTimeCount = 0;
            $lateCount = 0;
            $excusedCount = 0;
            $absentCount = 0;

            foreach ($weekRecords as $record) {
                $status = strtolower($record->studentStatus?->name ?? '');
                if ($status === 'on time') $onTimeCount++;
                elseif ($status === 'late') $lateCount++;
                elseif ($status === 'excused') $excusedCount++;
                elseif ($status === 'absent') $absentCount++;
            }

            // Calculate total possible records (days × students for this session)
            $daysInWeek = count($week['dates']);
            $totalPossibleRecords = $daysInWeek * $totalStudents;
            
            // Add missing records (no attendance record at all) to absent count
            $absentCount += ($totalPossibleRecords - $weekRecords->count());

            // Calculate proportions normalized to student count
            $totalRecords = max($onTimeCount + $lateCount + $excusedCount + $absentCount, 1);
            
            $analytics = [
                'on_time' => round(($onTimeCount / $totalRecords) * $totalStudents, 2),
                'late' => round(($lateCount / $totalRecords) * $totalStudents, 2),
                'excused' => round(($excusedCount / $totalRecords) * $totalStudents, 2),
                'absent' => round(($absentCount / $totalRecords) * $totalStudents, 2),
            ];

            $result['weeks'][] = array_merge($week, $analytics);
            $result['labels'][] = $week['label'];
            $result['on_time'][] = $analytics['on_time'];
            $result['late'][] = $analytics['late'];
            $result['absent'][] = $analytics['absent'];
            $result['excused'][] = $analytics['excused'];
        }

        return $result;
    }

    public function buildStudentWeeklyAnalyticsCombined(
        int $year,
        int $month,
        Collection $records,
        int $totalStudents,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $weekendDates = $this->getWeekendDatesForMonth($year, $month, $reviewSeason);
        $weeks = $this->groupDatesIntoWeeks($weekendDates);

        $result = [
            'weeks' => [],
            'labels' => [],
            'on_time' => [],
            'late' => [],
            'absent' => [],
            'excused' => [],
            'month' => $month,
            'year' => $year,
            'month_label' => Carbon::create($year, $month, 1)->format('F Y'),
        ];

        foreach ($weeks as $week) {
            $weekRecords = $records->filter(function ($r) use ($week) {
                $dateStr = Carbon::parse($r->date)->format('Y-m-d');
                return in_array($dateStr, $week['dates']);
            });

            $onTimeCount = 0;
            $lateCount = 0;
            $excusedCount = 0;
            $absentCount = 0;
            $presentUsers = collect();

            foreach ($weekRecords as $record) {
                $presentUsers->push($record->user_id);
                $status = strtolower($record->studentStatus?->name ?? '');
                if ($status === 'on time') $onTimeCount++;
                elseif ($status === 'late') $lateCount++;
                elseif ($status === 'excused') $excusedCount++;
                elseif ($status === 'absent') $absentCount++;
            }

            // Calculate total possible records (days × sessions per day × students)
            $daysInWeek = count($week['dates']);
            $sessionsPerDay = 2; // AM and PM
            $totalPossibleRecords = $daysInWeek * $sessionsPerDay * $totalStudents;
            
            // Add missing records (no attendance record at all) to absent count
            $absentCount += ($totalPossibleRecords - $weekRecords->count());

            // Calculate proportions normalized to student count
            // Each status shows what portion of the student attended with that status
            $totalRecords = max($onTimeCount + $lateCount + $excusedCount + $absentCount, 1);
            
            $analytics = [
                'on_time' => round(($onTimeCount / $totalRecords) * $totalStudents, 2),
                'late' => round(($lateCount / $totalRecords) * $totalStudents, 2),
                'excused' => round(($excusedCount / $totalRecords) * $totalStudents, 2),
                'absent' => round(($absentCount / $totalRecords) * $totalStudents, 2),
            ];

            $result['weeks'][] = array_merge($week, $analytics);
            $result['labels'][] = $week['label'];
            $result['on_time'][] = $analytics['on_time'];
            $result['late'][] = $analytics['late'];
            $result['absent'][] = $analytics['absent'];
            $result['excused'][] = $analytics['excused'];
        }

        return $result;
    }

    /**
     * Build weekly analytics for volunteers (for Chart.js).
     */
    public function buildVolunteerWeeklyAnalytics(
        int $year,
        int $month,
        Collection $records,
        int $totalVolunteers,
        ?ReviewSeason $reviewSeason = null
    ): array {
        $weekendDates = $this->getWeekendDatesForMonth($year, $month, $reviewSeason);
        $weeks = $this->groupDatesIntoWeeks($weekendDates);

        $result = [
            'weeks' => [],
            'labels' => [],
            'present' => [],
            'absent' => [],
            'avg_hours' => [],
            'month' => $month,
            'year' => $year,
            'month_label' => Carbon::create($year, $month, 1)->format('F Y'),
        ];

        foreach ($weeks as $week) {
            $weekRecords = $records->filter(function ($r) use ($week) {
                $dateStr = Carbon::parse($r->date)->format('Y-m-d');
                return in_array($dateStr, $week['dates']);
            });

            $analytics = $this->calculateVolunteerAnalytics(
                $week['dates'],
                $weekRecords,
                $totalVolunteers,
                $reviewSeason
            );

            $daysInWeek = count($week['dates']);
            $avgPresent = $daysInWeek > 0 ? round($analytics['present'] / $daysInWeek, 1) : 0;
            $avgAbsent = $daysInWeek > 0 ? round($analytics['absent'] / $daysInWeek, 1) : 0;
            $avgHours = $daysInWeek > 0 ? round(($analytics['total_minutes'] / 60) / $daysInWeek, 1) : 0;

            $result['weeks'][] = array_merge($week, [
                'present' => $analytics['present'],
                'absent' => $analytics['absent'],
                'total_minutes' => $analytics['total_minutes'],
                'avg_present' => $avgPresent,
                'avg_absent' => $avgAbsent,
                'avg_hours' => $avgHours,
            ]);

            $result['labels'][] = $week['label'];
            // Use totals for chart (not averages) - shows actual count of present/absent
            $result['present'][] = $analytics['present'];
            $result['absent'][] = $analytics['absent'];
            $result['avg_hours'][] = $avgHours;
        }

        return $result;
    }

    /* ─────────────────────────────────────────────────────────────
     |  Bulk Operations
     * ───────────────────────────────────────────────────────────── */

    /**
     * Bulk update attendance status for multiple records.
     *
     * @param array<int> $attendanceIds
     * @param string $status New status to set
     * @param int|null $updatedById User ID who performed the update
     * @return int Number of records updated
     */
    public function bulkUpdateStatus(array $attendanceIds, string $status, ?int $updatedById = null): int
    {
        if (empty($attendanceIds)) {
            return 0;
        }

        // Convert status name to ID
        $statusId = $this->getStudentStatusIdByName($status);
        if (!$statusId) {
            return 0;
        }

        $updateData = [
            'student_status_id' => $statusId,
            'updated_at' => now(),
        ];

        if ($updatedById) {
            $updateData['updated_by_id'] = $updatedById;
        }

        return AttendanceRecord::whereIn('id', $attendanceIds)->update($updateData);
    }

    /* ─────────────────────────────────────────────────────────────
     |  Chart Data Builders
     * ───────────────────────────────────────────────────────────── */

    /**
     * Build Chart.js compatible dataset for student stacked bar chart.
     */
    public function buildStudentChartData(array $weeklyAnalytics): array
    {
        return [
            'labels' => $weeklyAnalytics['labels'] ?? [],
            'datasets' => [
                [
                    'label' => 'On Time',
                    'data' => $weeklyAnalytics['on_time'] ?? [],
                    'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                    'borderColor' => 'rgb(34, 197, 94)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Late',
                    'data' => $weeklyAnalytics['late'] ?? [],
                    'backgroundColor' => 'rgba(234, 179, 8, 0.8)',
                    'borderColor' => 'rgb(234, 179, 8)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Excused',
                    'data' => $weeklyAnalytics['excused'] ?? [],
                    'backgroundColor' => 'rgba(59, 130, 246, 0.8)',
                    'borderColor' => 'rgb(59, 130, 246)',
                    'borderWidth' => 1,
                ],
                [
                    'label' => 'Absent',
                    'data' => $weeklyAnalytics['absent'] ?? [],
                    'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                    'borderColor' => 'rgb(239, 68, 68)',
                    'borderWidth' => 1,
                ],
            ],
        ];
    }

    /**
     * Build Chart.js compatible dataset for volunteer charts.
     */
    public function buildVolunteerChartData(array $weeklyAnalytics): array
    {
        return [
            'bar' => [
                'labels' => $weeklyAnalytics['labels'] ?? [],
                'datasets' => [
                    [
                        'label' => 'Present',
                        'data' => $weeklyAnalytics['present'] ?? [],
                        'backgroundColor' => 'rgba(34, 197, 94, 0.8)',
                        'borderColor' => 'rgb(34, 197, 94)',
                        'borderWidth' => 1,
                    ],
                    [
                        'label' => 'Absent',
                        'data' => $weeklyAnalytics['absent'] ?? [],
                        'backgroundColor' => 'rgba(239, 68, 68, 0.8)',
                        'borderColor' => 'rgb(239, 68, 68)',
                        'borderWidth' => 1,
                    ],
                ],
            ],
            'line' => [
                'labels' => $weeklyAnalytics['labels'] ?? [],
                'datasets' => [
                    [
                        'label' => 'Avg Hours',
                        'data' => $weeklyAnalytics['avg_hours'] ?? [],
                        'borderColor' => 'rgb(99, 102, 241)',
                        'backgroundColor' => 'rgba(99, 102, 241, 0.1)',
                        'fill' => true,
                        'tension' => 0.3,
                    ],
                ],
            ],
        ];
    }

    /**
     * Get cached student weekly analytics or compute and cache.
     */
    public function getCachedStudentWeeklyAnalytics(
        int $year,
        int $month,
        string $session,
        ?int $reviewSeasonId = null
    ): array {
        $cacheKey = self::CACHE_PREFIX . "student_weekly:{$year}:{$month}:{$session}:" . ($reviewSeasonId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year, $month, $session, $reviewSeasonId) {
            $reviewSeason = $reviewSeasonId ? ReviewSeason::find($reviewSeasonId) : ReviewSeason::getActive();

            $records = AttendanceRecord::where('session', $session)
                ->whereHas('user', fn($q) => $q->where('role_id', 5))
                ->whereBetween('date', [
                    Carbon::create($year, $month, 1)->startOfMonth(),
                    Carbon::create($year, $month, 1)->endOfMonth(),
                ])
                ->get();

            $studentCount = User::where('role_id', 5)->count();

            return $this->buildStudentWeeklyAnalytics($year, $month, $records, $session, $studentCount, $reviewSeason);
        });
    }

    /**
     * Get cached volunteer weekly analytics or compute and cache.
     */
    public function getCachedVolunteerWeeklyAnalytics(
        int $year,
        int $month,
        ?int $reviewSeasonId = null
    ): array {
        $cacheKey = self::CACHE_PREFIX . "volunteer_weekly:{$year}:{$month}:" . ($reviewSeasonId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($year, $month, $reviewSeasonId) {
            $reviewSeason = $reviewSeasonId ? ReviewSeason::find($reviewSeasonId) : ReviewSeason::getActive();

            // Volunteer roles: 1=System Manager, 2=Executive, 3=Administrator, 4=Instructor
            $records = AttendanceRecord::whereHas('user', fn($q) => $q->whereIn('role_id', [1, 2, 3, 4]))
                ->whereBetween('date', [
                    Carbon::create($year, $month, 1)->startOfMonth(),
                    Carbon::create($year, $month, 1)->endOfMonth(),
                ])
                ->get();

            $volunteerCount = User::whereIn('role_id', [1, 2, 3, 4])->count();

            return $this->buildVolunteerWeeklyAnalytics($year, $month, $records, $volunteerCount, $reviewSeason);
        });
    }

    /**
     * Get cached user attendance summary.
     */
    public function getCachedUserSummary(int $userId, ?int $reviewSeasonId = null): array
    {
        $cacheKey = self::CACHE_PREFIX . "user_summary:{$userId}:" . ($reviewSeasonId ?? 'all');

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($userId, $reviewSeasonId) {
            $user = User::find($userId);
            if (!$user) {
                return [];
            }

            $reviewSeason = $reviewSeasonId ? ReviewSeason::find($reviewSeasonId) : ReviewSeason::getActive();

            $query = AttendanceRecord::where('user_id', $userId);
            if ($reviewSeason) {
                $query->whereBetween('date', [$reviewSeason->start_date, $reviewSeason->end_date]);
            }
            $records = $query->get();

            // Check if student (role_id=5) or volunteer (role_id 1,2,3,4)
            $isStudent = $user->role_id === 5;
            if ($isStudent) {
                // Student
                return [
                    'type' => 'student',
                    'total' => $records->count(),
                    'on_time' => $records->where('student_status_id', self::STATUS_ON_TIME)->count(),
                    'late' => $records->where('student_status_id', self::STATUS_LATE)->count(),
                    'excused' => $records->where('student_status_id', self::STATUS_EXCUSED)->count(),
                    'absent' => $records->where('student_status_id', self::STATUS_ABSENT)->count(),
                ];
            } else {
                // Volunteer
                $totalMinutes = $records->sum('duration_minutes');
                return [
                    'type' => 'volunteer',
                    'total_days' => $records->whereNotNull('time_in')->count(),
                    'total_hours' => $this->formatDuration($totalMinutes),
                    'total_minutes' => $totalMinutes,
                ];
            }
        });
    }

    public function invalidateMonthCache(int $year, int $month): void
    {
        $patterns = [
            self::CACHE_PREFIX . "student_weekly:{$year}:{$month}:am:*",
            self::CACHE_PREFIX . "student_weekly:{$year}:{$month}:pm:*",
            self::CACHE_PREFIX . "volunteer_weekly:{$year}:{$month}:*",
        ];

        foreach (['am', 'pm'] as $session) {
            Cache::forget(self::CACHE_PREFIX . "student_weekly:{$year}:{$month}:{$session}:all");
            $activeSeason = ReviewSeason::getActive();
            if ($activeSeason) {
                Cache::forget(self::CACHE_PREFIX . "student_weekly:{$year}:{$month}:{$session}:{$activeSeason->id}");
            }
        }

        Cache::forget(self::CACHE_PREFIX . "volunteer_weekly:{$year}:{$month}:all");
        if ($activeSeason ?? null) {
            Cache::forget(self::CACHE_PREFIX . "volunteer_weekly:{$year}:{$month}:{$activeSeason->id}");
        }
    }

    public function invalidateUserCache(int $userId): void
    {
        Cache::forget(self::CACHE_PREFIX . "user_summary:{$userId}:all");
        $activeSeason = ReviewSeason::getActive();
        if ($activeSeason) {
            Cache::forget(self::CACHE_PREFIX . "user_summary:{$userId}:{$activeSeason->id}");
        }
    }

    public function invalidateAllCaches(): void
    {
        $now = now();
        for ($m = 1; $m <= 12; $m++) {
            $this->invalidateMonthCache($now->year, $m);
        }
    }

    public function onAttendanceChanged(AttendanceRecord $record): void
    {
        $date = Carbon::parse($record->date);
        $this->invalidateMonthCache($date->year, $date->month);
        $this->invalidateUserCache($record->user_id);
    }
}
