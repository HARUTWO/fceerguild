{{-- Student Attendance View - Light/Dark Mode Minimalist Design --}}
<div class="space-y-6" 
    x-data="{ 
        init() {
            if (!Alpine.store('studentRowHover')) {
                Alpine.store('studentRowHover', { hovered: null });
            }
        }
    }">
    {{-- No special hover styles needed - edit button always visible --}}

    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <flux:icon name="academic-cap" class="w-7 h-7 text-gray-500 dark:text-gray-400" />
                Student Attendance
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($monthlyView)
                    {{ \Carbon\Carbon::create($matrixYear, $matrixMonth, 1)->format('F Y') }} · {{ strtoupper($session) }} Session
                @else
                    {{ \Carbon\Carbon::parse($date)->format('F j, Y') }} · {{ strtoupper($session) }} Session
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Session Toggle --}}
            <div class="inline-flex items-center rounded-lg border border-gray-200 dark:border-zinc-700 overflow-hidden bg-white dark:bg-zinc-800">
                <button type="button" wire:click="setSession('am')"
                    class="px-3 py-2 text-sm font-medium transition {{ $session === 'am' ? 'bg-gray-100 text-gray-900 dark:bg-zinc-700 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                    <flux:icon name="sun" class="w-4 h-4 inline mr-1" /> AM
                </button>
                <button type="button" wire:click="setSession('pm')"
                    class="px-3 py-2 text-sm font-medium transition {{ $session === 'pm' ? 'bg-gray-100 text-gray-900 dark:bg-zinc-700 dark:text-white' : 'text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                    <flux:icon name="moon" class="w-4 h-4 inline mr-1" /> PM
                </button>
            </div>

            {{-- View Toggle --}}
            <button type="button" wire:click="toggleMonthlyView"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                @if($monthlyView)
                    <flux:icon name="calendar" class="w-4 h-4" /> Daily
                @else
                    <flux:icon name="calendar-days" class="w-4 h-4" /> Monthly
                @endif
            </button>

            {{-- Export Button --}}
            @can('export', App\Models\AttendanceRecord::class)
            <button type="button" wire:click="openExportModal"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                <flux:icon name="arrow-down-tray" class="w-4 h-4" /> Export
            </button>
            @endcan

            {{-- Review Season Button (Exec only) --}}
            @can('manageReviewSeason', App\Models\AttendanceRecord::class)
            <button type="button" wire:click="openReviewSeasonModal"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                <flux:icon name="cog-6-tooth" class="w-4 h-4" /> Review Season
            </button>
            @endcan
        </div>
    </div>

    {{-- Review Season Info --}}
    @if($reviewSeason)
    <div class="flex items-center gap-3 p-3 rounded-lg bg-gray-50 dark:bg-zinc-700/50 border border-gray-200 dark:border-zinc-600">
        <flux:icon name="calendar-date-range" class="w-5 h-5 text-gray-500 dark:text-gray-400" />
        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
            Review Season: {{ $reviewSeason->range_label }}
        </span>
    </div>
    @endif

    {{-- Filters --}}
    <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
        <form wire:submit.prevent="applyFilters" class="flex flex-wrap items-end gap-4">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Group</label>
                <select wire:model.defer="committeeFilter"
                    class="w-full rounded-lg border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 dark:focus:border-indigo-400">
                    <option value="">All Groups</option>
                    @foreach($allCommittees as $committee)
                        <option value="{{ $committee->id }}">{{ $committee->group ?? $committee->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit"
                class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-zinc-600 hover:bg-gray-700 dark:hover:bg-zinc-500 rounded-lg transition">
                Apply Filter
            </button>
        </form>
    </div>

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6">
        {{-- Left: Attendance Table --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
            @if(!$monthlyView)
                {{-- Daily View --}}
                @php
                    $selectedDateCarbon = \Carbon\Carbon::parse($date ?? now()->toDateString());
                    $isWeekend = $selectedDateCarbon->isSaturday() || $selectedDateCarbon->isSunday();
                    $isWithinSeason = !$reviewSeason || $reviewSeason->isValidAttendanceDate($selectedDateCarbon);
                    $showAsNA = !$isWeekend || !$isWithinSeason;
                @endphp

                @if($showAsNA)
                <div class="p-4 bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-600">
                    <div class="flex items-center gap-2 text-sm text-gray-600 dark:text-gray-300">
                        <flux:icon name="information-circle" class="w-5 h-5" />
                        @if(!$isWeekend)
                            This date is a weekday. Review sessions are only on weekends.
                        @else
                            This date is outside the current review season.
                        @endif
                    </div>
                </div>
                @endif

                <div class="overflow-x-auto student-table-container">
                    <table class="w-full text-left text-sm student-table">
                        <thead class="bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-600">
                            <tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-4 w-12 text-center">
                                    <span class="text-sm text-gray-400">#</span>
                                </th>
                                <th class="py-3 px-4">Student No.</th>
                                <th class="py-3 px-4">Name</th>
                                <th class="py-3 px-4">Group</th>
                                <th class="py-3 px-4">Time In</th>
                                <th class="py-3 px-4">Status</th>
                                <th class="py-3 px-4">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                            @php $globalIndex = 0; @endphp
                            @forelse($profilesByRoom as $roomId => $profiles)
                                @foreach($profiles as $profile)
                                    @php
                                        $user = $profile->user;
                                        $userId = $user?->id;
                                        $attendance = $user?->attendanceRecords->first();
                                        $isEditing = isset($editingRow[$userId]);
                                        
                                        if ($showAsNA) {
                                            $status = 'N/A';
                                            $statusColor = 'gray';
                                        } elseif (!$attendance) {
                                            $status = 'Absent';
                                            $statusColor = 'red';
                                        } else {
                                            $status = $attendance->studentStatus?->name ?? 'Absent';
                                            $statusColor = match(strtolower($status)) {
                                                'on time' => 'green',
                                                'late' => 'yellow',
                                                'excused' => 'blue',
                                                default => 'red',
                                            };
                                        }
                                    @endphp
                                    <tr class="group hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition {{ $isEditing ? 'bg-amber-50 dark:bg-amber-900/10' : '' }}" 
                                        wire:key="row-{{ $userId }}">
                                        <td class="py-3 px-4 text-center">
                                            @php $globalIndex++; @endphp
                                            <span class="text-gray-400 text-sm">{{ $globalIndex }}</span>
                                        </td>
                                        <td class="py-3 px-4 text-gray-600 dark:text-gray-300 font-mono text-xs">
                                            {{ $profile->student_number ?? '—' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <a href="{{ route('attendance.user', $userId) }}" class="font-medium text-gray-900 dark:text-white hover:text-gray-700 dark:hover:text-gray-300 transition">
                                                {{ $user?->name ?? 'Unknown' }}
                                            </a>
                                        </td>
                                        <td class="py-3 px-4 text-gray-500 dark:text-gray-400">
                                            {{ $rooms[$roomId]?->group ?? '—' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($isEditing && !$showAsNA)
                                                <input type="time"
                                                    wire:change="updateAttendanceTime({{ $attendance?->id ?? 'null' }}, {{ $userId }}, $event.target.value, '{{ $user?->name }}', '{{ $date }}')"
                                                    value="{{ $attendance?->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('H:i') : '' }}"
                                                    class="w-24 rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-2 py-1 text-sm text-gray-700 dark:text-gray-200">
                                            @else
                                                <span class="text-gray-700 dark:text-gray-300">
                                                    {{ $attendance?->time_in ? \Carbon\Carbon::parse($attendance->time_in)->format('h:i A') : '—' }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($isEditing && !$showAsNA)
                                                <select
                                                    wire:change="updateAttendanceStatus({{ $attendance?->id ?? 'null' }}, {{ $userId }}, $event.target.value, '{{ $user?->name }}', '{{ $date }}')"
                                                    class="rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-2 py-1 text-xs text-gray-700 dark:text-gray-200">
                                                    <option value="On Time" @selected($status === 'On Time')>On Time</option>
                                                    <option value="Late" @selected($status === 'Late')>Late</option>
                                                    <option value="Excused" @selected($status === 'Excused')>Excused</option>
                                                    <option value="Absent" @selected($status === 'Absent')>Absent</option>
                                                </select>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                    {{ $statusColor === 'green' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                    {{ $statusColor === 'yellow' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                    {{ $statusColor === 'blue' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                    {{ $statusColor === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                    {{ $statusColor === 'gray' ? 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-400' : '' }}
                                                ">
                                                    {{ $status }}
                                                </span>
                                            @endif
                                        </td>
                                        <td class="py-3 px-4">
                                            @if(!$showAsNA)
                                                @if($isEditing)
                                                    <div class="flex items-center gap-1">
                                                        <button type="button" wire:click="prepareConfirmSave({{ $userId }}, '{{ addslashes($user?->name) }}')"
                                                            class="p-1.5 text-gray-600 hover:bg-gray-100 dark:text-gray-400 dark:hover:bg-zinc-700 rounded transition">
                                                            <flux:icon name="check" class="w-4 h-4" />
                                                        </button>
                                                        <button type="button" wire:click="cancelEditing({{ $userId }})"
                                                            class="p-1.5 text-gray-500 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded transition">
                                                            <flux:icon name="x-mark" class="w-4 h-4" />
                                                        </button>
                                                    </div>
                                                @else
                                                    <button type="button" wire:click="startEditing({{ $userId }})"
                                                        class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded transition"
                                                        title="Edit">
                                                        <flux:icon name="pencil-square" class="w-4 h-4" />
                                                    </button>
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="7" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                        No students found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @else
                {{-- Monthly View --}}
                @include('livewire.attendance.partials.students-monthly', [
                    'profilesByRoom' => $profilesByRoom,
                    'rooms' => $rooms,
                    'weekendDates' => $weekendDates,
                    'session' => $session,
                    'reviewSeason' => $reviewSeason,
                ])
            @endif
        </div>

        {{-- Right: Calendar & Analytics --}}
        <div class="space-y-6">
            {{-- Mini Calendar (Daily View Only) --}}
            @if(!$monthlyView)
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-4">
                    <button type="button" wire:click="prevCalendarMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <flux:icon name="chevron-left" class="w-5 h-5" />
                    </button>
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">
                        {{ \Carbon\Carbon::create($calendarYear, $calendarMonth, 1)->format('F Y') }}
                    </span>
                    <button type="button" wire:click="nextCalendarMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                        <flux:icon name="chevron-right" class="w-5 h-5" />
                    </button>
                </div>
                <div class="grid grid-cols-7 gap-1 text-center text-xs">
                    @foreach(['S', 'M', 'T', 'W', 'T', 'F', 'S'] as $day)
                        <div class="py-1 text-gray-400 dark:text-gray-500 font-medium">{{ $day }}</div>
                    @endforeach
                    @php
                        $firstDay = \Carbon\Carbon::create($calendarYear, $calendarMonth, 1);
                        $daysInMonth = $firstDay->daysInMonth;
                        $startDayOfWeek = $firstDay->dayOfWeek;
                    @endphp
                    @for($i = 0; $i < $startDayOfWeek; $i++)
                        <div></div>
                    @endfor
                    @for($d = 1; $d <= $daysInMonth; $d++)
                        @php
                            $thisDate = \Carbon\Carbon::create($calendarYear, $calendarMonth, $d);
                            $isWeekend = $thisDate->isSaturday() || $thisDate->isSunday();
                            $isSelected = $date === $thisDate->format('Y-m-d');
                            $isToday = $thisDate->isToday();
                        @endphp
                        <button type="button" wire:click="setDate('{{ $thisDate->format('Y-m-d') }}')"
                            class="py-1.5 rounded-md text-sm transition
                                {{ $isSelected ? 'bg-gray-800 dark:bg-zinc-600 text-white' : '' }}
                                {{ !$isSelected && $isToday ? 'ring-1 ring-gray-400' : '' }}
                                {{ !$isSelected && $isWeekend ? 'bg-slate-100 dark:bg-zinc-700 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-zinc-600' : '' }}
                                {{ !$isSelected && !$isWeekend ? 'text-gray-400 dark:text-gray-600' : '' }}
                            ">
                            {{ $d }}
                        </button>
                    @endfor
                </div>
            </div>
            @endif

            {{-- Weekly Analytics Chart --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Weekly Analytics</h3>
                    <div class="flex items-center gap-1">
                        <button type="button" wire:click="prevWeeklyAnalyticsMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <flux:icon name="chevron-left" class="w-4 h-4" />
                        </button>
                        <span class="text-xs text-gray-500 dark:text-gray-400">
                            {{ \Carbon\Carbon::create($weeklyAnalyticsYear, $weeklyAnalyticsMonth, 1)->format('M Y') }}
                        </span>
                        <button type="button" wire:click="nextWeeklyAnalyticsMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300">
                            <flux:icon name="chevron-right" class="w-4 h-4" />
                        </button>
                    </div>
                </div>
                {{-- AM/PM/Both Toggle --}}
                <div class="flex items-center justify-center gap-1 mb-3">
                    <button type="button" wire:click="setAnalyticsSession('am')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition
                            {{ $analyticsSession === 'am' ? 'bg-gray-800 dark:bg-zinc-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                        AM
                    </button>
                    <button type="button" wire:click="setAnalyticsSession('pm')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition
                            {{ $analyticsSession === 'pm' ? 'bg-gray-800 dark:bg-zinc-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                        PM
                    </button>
                    <button type="button" wire:click="setAnalyticsSession('both')"
                        class="px-3 py-1 text-xs font-medium rounded-md transition
                            {{ $analyticsSession === 'both' ? 'bg-gray-800 dark:bg-zinc-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                        Both
                    </button>
                </div>
                <x-attendance-chart-bar
                    :chart-id="'student-bar-' . $weeklyAnalyticsYear . '-' . $weeklyAnalyticsMonth . '-' . $analyticsSession"
                    :labels="$chartData['labels'] ?? []"
                    :datasets="$chartData['datasets'] ?? []"
                    height="200px"
                />
                {{-- Refresh Button --}}
                <div class="flex justify-end mt-3">
                    <button type="button" wire:click="$refresh" 
                        class="inline-flex items-center gap-1 px-2 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded transition">
                        <flux:icon name="arrow-path" class="w-3.5 h-3.5" />
                        Refresh
                    </button>
                </div>
            </div>

            {{-- Legend --}}
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Status Legend</h3>
                <div class="grid grid-cols-2 gap-2 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-green-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">On Time</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-yellow-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Late</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-blue-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Excused</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="w-3 h-3 rounded-full bg-red-500"></span>
                        <span class="text-gray-600 dark:text-gray-400">Absent</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modals --}}
    <livewire:attendance.export-modal />
    <livewire:attendance.review-season-modal />

    {{-- Confirm Save Modal --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="cancelConfirmModal"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Changes</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Save attendance changes for <strong>{{ $confirmModalData['full_name'] ?? '' }}</strong>?
                </p>
                @if(!empty($confirmModalData['dates']))
                <div class="mb-4 p-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                    <p class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Dates affected:</p>
                    <p class="text-sm text-gray-700 dark:text-gray-300">{{ implode(', ', $confirmModalData['dates']) }}</p>
                </div>
                @endif
                <div class="flex justify-end gap-3">
                    <button type="button" wire:click="cancelConfirmModal"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="confirmSaveAttendance"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-zinc-600 dark:hover:bg-zinc-500 rounded-lg transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Self Edit Error Modal --}}
    @if($showSelfEditError)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeSelfEditError"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-sm w-full p-6 text-center">
                <flux:icon name="exclamation-triangle" class="w-12 h-12 text-amber-500 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cannot Edit Own Attendance</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    You are not allowed to edit your own attendance records.
                </p>
                <button type="button" wire:click="closeSelfEditError"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-zinc-600 hover:bg-gray-700 dark:hover:bg-zinc-500 rounded-lg transition">
                    Got it
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Outside Season Error Modal --}}
    @if($showOutsideSeasonError)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="closeOutsideSeasonError"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-sm w-full p-6 text-center">
                <flux:icon name="calendar-x-mark" class="w-12 h-12 text-red-500 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Date Outside Review Season</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">
                    {{ $outsideSeasonErrorData['date'] ?? '' }} is outside the current review season.
                </p>
                <p class="text-xs text-gray-500 dark:text-gray-500 mb-4">
                    Season: {{ $outsideSeasonErrorData['range'] ?? '' }}
                </p>
                <button type="button" wire:click="closeOutsideSeasonError"
                    class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-zinc-600 hover:bg-gray-700 dark:hover:bg-zinc-500 rounded-lg transition">
                    Got it
                </button>
            </div>
        </div>
    </div>
    @endif
</div>
