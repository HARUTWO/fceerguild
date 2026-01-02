{{-- User Attendance View - Light/Dark Mode Minimalist Design --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
        <div class="flex items-start gap-4">
            {{-- Avatar --}}
            <div class="w-16 h-16 rounded-full bg-gray-200 dark:bg-zinc-700 flex items-center justify-center text-gray-600 dark:text-gray-300 text-xl font-bold">
                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $fullName }}</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400">
                    @if($this->isStudent())
                        Student · {{ $user->fceerProfile?->student_number ?? '' }}
                    @else
                        Volunteer
                        @if($committeeMemberships->isNotEmpty())
                            · {{ $committeeMemberships->first()->committee?->name ?? '' }}
                        @endif
                    @endif
                </p>
                <p class="mt-1 text-xs text-gray-400 dark:text-gray-500">
                    @if($monthlyView)
                        {{ \Carbon\Carbon::create($matrixYear, $matrixMonth, 1)->format('F Y') }}
                    @else
                        {{ \Carbon\Carbon::parse($selectedDate)->format('F j, Y') }}
                    @endif
                    @if($this->isStudent())
                        · {{ strtoupper($session) }} Session
                    @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            @if($this->isStudent())
            {{-- Session Toggle (Students only) --}}
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
            @endif

            {{-- View Toggle --}}
            <button type="button" wire:click="toggleMonthlyView"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                @if($monthlyView)
                    <flux:icon name="calendar" class="w-4 h-4" /> Daily
                @else
                    <flux:icon name="calendar-days" class="w-4 h-4" /> Monthly
                @endif
            </button>

            {{-- Excuse Letter List Button (Students only) --}}
            @if($this->isStudent())
            <button type="button" wire:click="openExcuseLetterList"
                class="inline-flex items-center gap-2 px-3 py-2 text-sm font-medium text-gray-600 dark:text-gray-300 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-700 transition">
                <flux:icon name="document-text" class="w-4 h-4" /> Excuse Letters
            </button>
            @endif
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
        @if($this->isStudent())
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['on_time'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">On Time</div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['late'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Late</div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['excused'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Excused</div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['absent'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Absent</div>
            </div>
        @else
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 col-span-2">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_days'] ?? 0 }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Days Present</div>
            </div>
            <div class="p-4 bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 col-span-2">
                <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $summary['total_hours'] ?? '0h 0m' }}</div>
                <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider">Total Hours</div>
            </div>
        @endif
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

    {{-- Main Content Grid --}}
    <div class="grid grid-cols-1 {{ !$monthlyView ? 'lg:grid-cols-[1fr_300px]' : '' }} gap-6">
        {{-- Left: Attendance Records --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
            @if(!$monthlyView)
                {{-- Daily View --}}
                @php
                    $selectedDateCarbon = \Carbon\Carbon::parse($selectedDate ?? now()->toDateString());
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

                <div class="p-4">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-4">
                        {{ \Carbon\Carbon::parse($selectedDate)->format('l, F j, Y') }}
                    </h3>

                    @if($this->isStudent())
                        {{-- Student Daily View --}}
                        @php
                            $record = $attendanceRecords->first();
                            $isEditing = isset($editingRow[$user->id]);
                            
                            if ($showAsNA) {
                                $status = 'N/A';
                                $statusColor = 'gray';
                            } elseif (!$record) {
                                $status = 'Absent';
                                $statusColor = 'red';
                            } else {
                                $status = $record->studentStatus?->name ?? 'Absent';
                                $statusColor = match(strtolower($status)) {
                                    'on time' => 'green',
                                    'late' => 'yellow',
                                    'excused' => 'blue',
                                    default => 'red',
                                };
                            }
                        @endphp
                        
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Time In</div>
                                    @if($isEditing && !$showAsNA)
                                        <input type="time"
                                            wire:change="updateStudentTime({{ $record?->id ?? 'null' }}, $event.target.value, '{{ $selectedDate }}', '{{ $session }}')"
                                            value="{{ $record?->time_in ? \Carbon\Carbon::parse($record->time_in)->format('H:i') : '' }}"
                                            class="rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    @else
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $record?->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}
                                        </div>
                                    @endif
                                </div>
                                <div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Status</div>
                                    @if($isEditing && !$showAsNA)
                                        <select
                                            wire:change="updateAttendanceStatus({{ $record?->id ?? 'null' }}, $event.target.value, '{{ $selectedDate }}', '{{ $session }}')"
                                            class="rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                                            <option value="On Time" @selected($status === 'On Time')>On Time</option>
                                            <option value="Late" @selected($status === 'Late')>Late</option>
                                            <option value="Excused" @selected($status === 'Excused')>Excused</option>
                                            <option value="Absent" @selected($status === 'Absent')>Absent</option>
                                        </select>
                                    @else
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                            {{ $statusColor === 'green' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                            {{ $statusColor === 'yellow' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                            {{ $statusColor === 'blue' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                            {{ $statusColor === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                            {{ $statusColor === 'gray' ? 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-400' : '' }}
                                        ">
                                            {{ $status }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($canEdit && !$showAsNA)
                            <div class="flex justify-end gap-2">
                                @if($isEditing)
                                    <button type="button" wire:click="cancelEditing"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition">
                                        Cancel
                                    </button>
                                    <button type="button" wire:click="prepareConfirmSave"
                                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-zinc-600 dark:hover:bg-zinc-500 rounded-lg transition">
                                        Save Changes
                                    </button>
                                @else
                                    <button type="button" wire:click="startEditing"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-600 rounded-lg transition">
                                        <flux:icon name="pencil-square" class="w-4 h-4 inline mr-1" /> Edit
                                    </button>
                                @endif
                            </div>
                            @endif

                            {{-- Excuse Letter Option for Students --}}
                            @if($this->isStudent() && !$showAsNA && ($status === 'Absent' || $status === 'Late'))
                            <div class="pt-4 border-t border-gray-200 dark:border-zinc-700">
                                <button type="button" wire:click="openExcuseModal('{{ $selectedDate }}', '{{ $session }}')"
                                    class="w-full px-4 py-2 text-sm font-medium text-slate-700 dark:text-slate-300 bg-slate-100 dark:bg-zinc-700 hover:bg-slate-200 dark:hover:bg-zinc-600 rounded-lg transition">
                                    <flux:icon name="document-text" class="w-4 h-4 inline mr-1" /> Submit Excuse Letter
                                </button>
                            </div>
                            @endif
                        </div>

                    @else
                        {{-- Volunteer Daily View --}}
                        @php
                            $record = $attendanceRecords->first();
                            $isEditing = isset($editingRow[$user->id]);
                            $timeIn = $record?->time_in;
                            $timeOut = $record?->time_out;
                            $duration = null;
                            if ($timeIn && $timeOut) {
                                $in = \Carbon\Carbon::parse($timeIn);
                                $out = \Carbon\Carbon::parse($timeOut);
                                $mins = $in->diffInMinutes($out);
                                $duration = floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
                            }
                        @endphp
                        
                        <div class="space-y-4">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Time In</div>
                                    @if($isEditing && !$showAsNA)
                                        <input type="time"
                                            wire:change="updateAttendanceTime({{ $record?->id ?? 'null' }}, 'time_in', $event.target.value, '{{ $selectedDate }}')"
                                            value="{{ $timeIn ? \Carbon\Carbon::parse($timeIn)->format('H:i') : '' }}"
                                            class="w-full rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    @else
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $timeIn ? \Carbon\Carbon::parse($timeIn)->format('h:i A') : '—' }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 bg-gray-50 dark:bg-zinc-700/50 rounded-lg">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Time Out</div>
                                    @if($isEditing && !$showAsNA)
                                        <input type="time"
                                            wire:change="updateAttendanceTime({{ $record?->id ?? 'null' }}, 'time_out', $event.target.value, '{{ $selectedDate }}')"
                                            value="{{ $timeOut ? \Carbon\Carbon::parse($timeOut)->format('H:i') : '' }}"
                                            class="w-full rounded border border-gray-300 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    @else
                                        <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ $timeOut ? \Carbon\Carbon::parse($timeOut)->format('h:i A') : '—' }}
                                        </div>
                                    @endif
                                </div>
                                <div class="p-4 bg-slate-100 dark:bg-zinc-700 rounded-lg">
                                    <div class="text-xs text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-1">Duration</div>
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $duration ?? ($showAsNA ? 'N/A' : '—') }}
                                    </div>
                                </div>
                            </div>

                            @if($canEdit && !$showAsNA)
                            <div class="flex justify-end gap-2">
                                @if($isEditing)
                                    <button type="button" wire:click="cancelEditing"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition">
                                        Cancel
                                    </button>
                                    <button type="button" wire:click="prepareConfirmSave"
                                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-zinc-600 dark:hover:bg-zinc-500 rounded-lg transition">
                                        Save Changes
                                    </button>
                                @else
                                    <button type="button" wire:click="startEditing"
                                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-600 rounded-lg transition">
                                        <flux:icon name="pencil-square" class="w-4 h-4 inline mr-1" /> Edit
                                    </button>
                                @endif
                            </div>
                            @endif
                        </div>
                    @endif
                </div>
            @else
                {{-- Monthly View --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-600">
                            <tr>
                                <th colspan="{{ $this->isStudent() ? 3 : 4 }}" class="py-4 px-4">
                                    <div class="flex items-center justify-between">
                                        <button type="button" wire:click="prevMatrixMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                                            <flux:icon name="chevron-left" class="w-5 h-5" />
                                        </button>
                                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::create($matrixYear, $matrixMonth, 1)->format('F Y') }}
                                        </span>
                                        <button type="button" wire:click="nextMatrixMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                                            <flux:icon name="chevron-right" class="w-5 h-5" />
                                        </button>
                                    </div>
                                </th>
                            </tr>
                            <tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                                <th class="py-3 px-4">Date</th>
                                @if($this->isStudent())
                                    <th class="py-3 px-4">Time In</th>
                                    <th class="py-3 px-4">Status</th>
                                @else
                                    <th class="py-3 px-4">Time In</th>
                                    <th class="py-3 px-4">Time Out</th>
                                    <th class="py-3 px-4">Duration</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                            @forelse($weekendDates as $dateStr)
                                @php
                                    $dateCarbon = \Carbon\Carbon::parse($dateStr);
                                    $record = $attendanceRecords->firstWhere('date', $dateStr);
                                    $isWithin = !$reviewSeason || $reviewSeason->isValidAttendanceDate($dateStr);
                                    
                                    if (!$isWithin) {
                                        $status = 'N/A';
                                        $statusColor = 'gray';
                                    } elseif ($this->isStudent()) {
                                        $status = $record?->studentStatus?->name ?? 'Absent';
                                        $statusColor = match(strtolower($status)) {
                                            'on time' => 'green',
                                            'late' => 'yellow',
                                            'excused' => 'blue',
                                            'n/a' => 'gray',
                                            default => 'red',
                                        };
                                    }
                                @endphp
                                <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition">
                                    <td class="py-3 px-4">
                                        <div class="font-medium text-gray-900 dark:text-white">{{ $dateCarbon->format('M j') }}</div>
                                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dateCarbon->format('l') }}</div>
                                    </td>
                                    @if($this->isStudent())
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                            {{ $record?->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium
                                                {{ $statusColor === 'green' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                {{ $statusColor === 'yellow' ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ $statusColor === 'blue' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                                {{ $statusColor === 'red' ? 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                {{ $statusColor === 'gray' ? 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-400' : '' }}
                                            ">
                                                {{ $status }}
                                            </span>
                                        </td>
                                    @else
                                        @php
                                            $timeIn = $record?->time_in;
                                            $timeOut = $record?->time_out;
                                            $duration = null;
                                            if ($timeIn && $timeOut) {
                                                $in = \Carbon\Carbon::parse($timeIn);
                                                $out = \Carbon\Carbon::parse($timeOut);
                                                $mins = $in->diffInMinutes($out);
                                                $duration = floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
                                            }
                                        @endphp
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                            {{ $timeIn ? \Carbon\Carbon::parse($timeIn)->format('h:i A') : '—' }}
                                        </td>
                                        <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                            {{ $timeOut ? \Carbon\Carbon::parse($timeOut)->format('h:i A') : '—' }}
                                        </td>
                                        <td class="py-3 px-4">
                                            @if($duration)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400">
                                                    {{ $duration }}
                                                </span>
                                            @elseif(!$isWithin)
                                                <span class="text-gray-400 dark:text-gray-500">N/A</span>
                                            @else
                                                <span class="text-gray-400 dark:text-gray-500">—</span>
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ $this->isStudent() ? 3 : 4 }}" class="py-12 text-center text-gray-500 dark:text-gray-400">
                                        No weekend dates in this month.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Right: Calendar --}}
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
                            $isSelected = $selectedDate === $thisDate->format('Y-m-d');
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

            {{-- Volunteer Committees (if volunteer) --}}
            @if($this->isVolunteer() && $committeeMemberships->isNotEmpty())
            <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Committees</h3>
                <div class="space-y-2">
                    @foreach($committeeMemberships as $membership)
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-700 dark:text-gray-300">{{ $membership->committee?->name ?? '—' }}</span>
                            <span class="text-xs text-gray-500 dark:text-gray-400">{{ $membership->committeePosition?->name ?? '' }}</span>
                        </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- Confirm Save Modal --}}
    @if($showConfirmModal)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="cancelConfirmModal"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-md w-full p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Confirm Changes</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
                    Save attendance changes for <strong>{{ $confirmModalData['full_name'] ?? $fullName }}</strong>?
                </p>
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

    {{-- Excuse Letter Modal (Flyout) --}}
    @if($showExcuseModal)
    <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50" wire:click.self="closeExcuseModal">
        <flux:modal name="excuse-letter-form" flyout class="w-11/12 max-w-lg" wire:model.live="showExcuseModal">
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Submit Excuse Letter</h2>
                    <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                </div>

                <form wire:submit.prevent="submitExcuseLetter" class="space-y-5">
                    {{-- Student Info --}}
                    <div class="p-3 rounded-lg bg-gray-50 dark:bg-zinc-700/50 border border-gray-200 dark:border-zinc-600">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-gray-200 dark:bg-zinc-600 flex items-center justify-center text-gray-600 dark:text-gray-300 font-bold">
                                {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $fullName }}</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">{{ $user->fceerProfile?->student_number ?? '' }}</p>
                            </div>
                        </div>
                    </div>

                    {{-- Date of Attendance --}}
                    <div>
                        <label class="block text-base font-medium text-gray-700 dark:text-gray-100">Date of Attendance</label>
                        <input type="date" wire:model.defer="excuseDate"
                            class="mt-1 w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-3 text-sm focus:border-gray-400 focus:ring-gray-400"
                            value="{{ $selectedDate ?? now()->toDateString() }}"
                        >
                        @error('excuseDate') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Session --}}
                    <div>
                        <label class="block text-base font-medium text-gray-700 dark:text-gray-100 mb-2">Session</label>
                        <div class="flex gap-2">
                            <button type="button" wire:click="$set('excuseSession', 'am')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg border transition {{ ($excuseSession ?? $session) === 'am' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                <i class="fa fa-sun-o mr-1"></i> AM
                            </button>
                            <button type="button" wire:click="$set('excuseSession', 'pm')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg border transition {{ ($excuseSession ?? $session) === 'pm' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                <i class="fa fa-moon-o mr-1"></i> PM
                            </button>
                            <button type="button" wire:click="$set('excuseSession', 'both')"
                                class="flex-1 px-4 py-2.5 text-sm font-medium rounded-lg border transition {{ ($excuseSession ?? '') === 'both' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                Both
                            </button>
                        </div>
                    </div>

                    {{-- Reason --}}
                    <div>
                        <label class="block text-base font-medium text-gray-700 dark:text-gray-100">Reason</label>
                        <textarea wire:model.defer="reason" rows="3"
                            class="mt-1 w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm"
                            placeholder="Explain your reason for absence..."></textarea>
                        @error('reason') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- File Attachment --}}
                    <div>
                        <label class="block text-base font-medium text-gray-700 dark:text-gray-100">Attachment (optional)</label>
                        <div class="mt-1 flex items-center gap-3">
                            <label class="flex-1 flex items-center justify-center gap-2 px-4 py-3 border-2 border-dashed border-gray-300 dark:border-zinc-600 rounded-lg cursor-pointer hover:border-gray-400 dark:hover:border-zinc-500 transition">
                                <flux:icon name="arrow-up-tray" class="w-5 h-5 text-gray-400" />
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    @if($letter_file)
                                        {{ $letter_file->getClientOriginalName() }}
                                    @else
                                        Upload file (PDF, JPG, PNG)
                                    @endif
                                </span>
                                <input type="file" wire:model="letter_file" accept=".pdf,.jpg,.jpeg,.png" class="hidden">
                            </label>
                            @if($letter_file)
                                <button type="button" wire:click="$set('letter_file', null)" class="p-2 text-gray-400 hover:text-red-500 transition">
                                    <flux:icon name="x-mark" class="w-5 h-5" />
                                </button>
                            @endif
                        </div>
                        @error('letter_file') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    {{-- Letter Link (optional) --}}
                    <div>
                        <label class="block text-base font-medium text-gray-700 dark:text-gray-100">Letter Link (optional)</label>
                        <input type="url" wire:model.defer="letterLink"
                            class="mt-1 w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm"
                            placeholder="https://drive.google.com/...">
                        @error('letterLink') <span class="text-xs text-red-500 mt-1">{{ $message }}</span> @enderror
                    </div>

                    <div class="flex justify-end space-x-2 pt-2">
                        <flux:button type="submit" variant="primary">Submit</flux:button>
                        <flux:button wire:click="closeExcuseModal" type="button">Cancel</flux:button>
                    </div>
                </form>
            </div>
        </flux:modal>
    </div>
    @endif

    {{-- Excuse Letter List Modal (Flyout) --}}
    @if($this->isStudent() && $showExcuseListModal)
    <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <flux:modal name="excuse-letter-list" flyout class="w-11/12 max-w-2xl" wire:model="showExcuseListModal" @close="$wire.closeExcuseLetterList()">
            <div class="space-y-6">
                {{-- Header --}}
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">My Excuse Letters</h2>
                    <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                </div>

                {{-- Letter List --}}
                <div class="space-y-3">
                    @forelse($excuseLetters as $letter)
                    <div class="border border-gray-200 dark:border-zinc-700 rounded-lg p-4 bg-white dark:bg-zinc-800">
                        @if($editingExcuseLetterId === $letter['id'])
                            {{-- Edit Mode --}}
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Date</label>
                                    <input type="date" wire:model.defer="editDate"
                                        class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm">
                                    @error('editDate') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Reason</label>
                                    <textarea wire:model.defer="editReason" rows="3"
                                        class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm"></textarea>
                                    @error('editReason') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Letter Link</label>
                                    <input type="url" wire:model.defer="editLetterLink"
                                        class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-2 text-sm"
                                        placeholder="https://...">
                                    @error('editLetterLink') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                                </div>
                                <div class="flex gap-2">
                                    <flux:button wire:click="saveEditExcuseLetter" variant="primary" size="sm">Save</flux:button>
                                    <flux:button wire:click="cancelEditExcuseLetter" size="sm">Cancel</flux:button>
                                </div>
                            </div>
                        @else
                            {{-- View Mode --}}
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center gap-2 mb-2">
                                        <span class="font-semibold text-gray-900 dark:text-white">
                                            {{ \Carbon\Carbon::parse($letter['date_attendance'])->format('M j, Y') }}
                                        </span>
                                        <span class="px-2 py-0.5 text-xs rounded-full {{ 
                                            $letter['letter_status'] === 'approved' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                                            ($letter['letter_status'] === 'rejected' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' :
                                            ($letter['letter_status'] === 'withdrawn' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' :
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400'))
                                        }}">
                                            {{ ucfirst($letter['letter_status']) }}
                                        </span>
                                    </div>
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">{{ $letter['reason'] }}</p>
                                    @if($letter['letter_link'])
                                    <a href="{{ $letter['letter_link'] }}" target="_blank" 
                                        class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                                        <flux:icon name="link" class="w-3 h-3" /> View Letter
                                    </a>
                                    @endif
                                    <p class="text-xs text-gray-400 dark:text-gray-500 mt-2">
                                        Submitted {{ \Carbon\Carbon::parse($letter['created_at'])->diffForHumans() }}
                                    </p>
                                </div>
                                <div class="flex gap-1">
                                    <button wire:click="startEditExcuseLetter({{ $letter['id'] }})" 
                                        class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition">
                                        <flux:icon name="pencil" class="w-4 h-4" />
                                    </button>
                                    <button wire:click="deleteExcuseLetter({{ $letter['id'] }})" 
                                        onclick="return confirm('Are you sure you want to delete this excuse letter?')"
                                        class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition">
                                        <flux:icon name="trash" class="w-4 h-4" />
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-12">
                        <flux:icon name="document-text" class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-3" />
                        <p class="text-gray-500 dark:text-gray-400">No excuse letters submitted yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </flux:modal>
    </div>
    @endif
</div>
