{{-- Volunteers Monthly Matrix View --}}
<div class="overflow-x-auto">
    <table class="w-full text-left text-sm">
        <thead class="sticky top-0 bg-gray-50 dark:bg-zinc-700/50 border-b-2 border-gray-200 dark:border-zinc-600">
            <tr>
                <th colspan="{{ count($weekendDates) + 1 }}" class="py-4 px-4">
                    <div class="flex items-center justify-between">
                        <button type="button" wire:click="prevMatrixMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            <flux:icon name="chevron-left" class="w-5 h-5" />
                        </button>
                        <span class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ \Carbon\Carbon::create($matrixYear ?? now()->year, $matrixMonth ?? now()->month, 1)->format('F Y') }}
                        </span>
                        <button type="button" wire:click="nextMatrixMonth" class="p-1 text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 transition">
                            <flux:icon name="chevron-right" class="w-5 h-5" />
                        </button>
                    </div>
                </th>
            </tr>
            <tr>
                <th class="py-3 px-4 text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider whitespace-nowrap border-r border-gray-200 dark:border-zinc-600">
                    Volunteer
                </th>
                @foreach($weekendDates as $dateStr)
                    @php
                        $dateCarbon = \Carbon\Carbon::parse($dateStr);
                        $isWithinSeason = !$reviewSeason || $reviewSeason->isValidAttendanceDate($dateStr);
                    @endphp
                    <th class="py-3 px-3 text-center {{ !$isWithinSeason ? 'opacity-40' : '' }}">
                        <div class="text-xs font-semibold text-gray-900 dark:text-white">{{ $dateCarbon->format('M j') }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">{{ $dateCarbon->format('D') }}</div>
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
            @forelse($membershipsByCommittee as $committeeId => $memberships)
                {{-- Committee Header Row --}}
                <tr class="bg-gray-100 dark:bg-zinc-700/50">
                    <td colspan="{{ count($weekendDates) + 1 }}" class="py-2 px-4">
                        <div class="flex items-center gap-2">
                            <flux:icon name="user-group" class="w-4 h-4 text-gray-600 dark:text-gray-400" />
                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-300">
                                {{ $committees[$committeeId]->name ?? 'Unknown Committee' }}
                            </span>
                            <span class="text-xs text-gray-600 dark:text-gray-400">
                                ({{ $memberships->count() }} {{ \Illuminate\Support\Str::plural('volunteer', $memberships->count()) }})
                            </span>
                        </div>
                    </td>
                </tr>
                
                {{-- Volunteer Rows --}}
                @foreach($memberships as $membership)
                    @php
                        $user = $membership->user;
                        if (!$user) continue;
                    @endphp
                    <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition">
                        <td class="py-3 px-4 border-r border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-800">
                            <div>
                                <div class="font-medium text-gray-900 dark:text-white">{{ $user->name ?? 'Unknown' }}</div>
                                <div class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ $membership->committeePosition?->name ?? '' }}
                                </div>
                            </div>
                        </td>
                        @foreach($weekendDates as $dateStr)
                            @php
                                $isWithinSeason = !$reviewSeason || $reviewSeason->isValidAttendanceDate($dateStr);
                                $record = $user->attendanceRecords->firstWhere('date', $dateStr);
                                
                                $timeIn = $record?->time_in;
                                $timeOut = $record?->time_out;
                                $duration = null;
                                
                                if ($timeIn && $timeOut) {
                                    $in = \Carbon\Carbon::parse($timeIn);
                                    $out = \Carbon\Carbon::parse($timeOut);
                                    $mins = $in->diffInMinutes($out);
                                    $duration = floor($mins / 60) . 'h ' . ($mins % 60) . 'm';
                                }
                                
                                $cellBg = 'bg-white dark:bg-zinc-800';
                                $cellText = 'text-gray-400 dark:text-gray-500';
                                if (!$isWithinSeason) {
                                    $cellBg = 'bg-gray-100 dark:bg-zinc-900';
                                    $cellText = 'text-gray-400 dark:text-gray-600';
                                } elseif ($duration) {
                                    $cellBg = 'bg-green-50 dark:bg-green-900/20';
                                    $cellText = 'text-green-700 dark:text-green-400';
                                }
                            @endphp
                            <td class="py-3 px-3 text-center {{ $cellBg }} {{ $cellText }}">
                                @if(!$isWithinSeason)
                                    <span class="text-xs">—</span>
                                @elseif($duration)
                                    <div class="text-xs font-medium whitespace-nowrap">{{ $duration }}</div>
                                @else
                                    <span class="text-xs">—</span>
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @endforeach
            @empty
                <tr>
                    <td colspan="{{ count($weekendDates) + 1 }}" class="py-12 text-center text-gray-500 dark:text-gray-400">
                        <flux:icon name="users" class="w-12 h-12 mx-auto mb-2 opacity-30" />
                        <p>No volunteers found with the current filters.</p>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
