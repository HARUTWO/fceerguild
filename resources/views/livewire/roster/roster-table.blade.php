<div class="roster-container">
    @once
        <link rel="stylesheet" href="/css/reference-table.css">
        <script src="/js/reference-table.js" defer></script>
    @endonce

    {{-- Toolbar Component --}}
    @php
        $selectedRowNumbers = [];
        if (!empty($selected)) {
            $currentPageUsers = $users ?? collect();
            foreach ($currentPageUsers as $idx => $u) {
                if (in_array((string) $u->id, $selected)) {
                    $selectedRowNumbers[] = '#'.(($currentPageUsers->firstItem() ?? 0) + $idx);
                }
            }
        }
    @endphp
    @livewire('roster.roster-toolbar', [
        'headerIcon' => $type === 'students' ? 'academic-cap' : 'users',
        'headerTitle' => $type === 'students' ? 'Students' : 'Volunteers',
        'type' => $type,
        'availableColumns' => $availableColumns,
        'visibleColumns' => $visibleColumns,
        'availableFilters' => $availableFilters,
        'activeFilters' => $activeFilters,
        'selected' => $selected,
        'selectedRowNumbers' => $selectedRowNumbers,
        'search' => $search,
        'perPage' => $perPage,
        'view' => $view,
    ], key('roster-toolbar-' . $type))
    @if($view === 'table')
        <div class="reference-table-container relative overflow-x-auto bg-white dark:bg-zinc-800 rounded shadow" style="overflow-y: visible;"
            x-data="{ selectedLocal: @entangle('selected') }"
            x-bind:class="{ 'has-selection': selectedLocal.length > 0 }"
            x-on:mouseenter="$store.rowHover.hovered = 'table'"
            x-on:mouseleave="$store.rowHover.hovered = null">
            <table class="min-w-full divide-y">
                <thead class="bg-gray-50 dark:bg-zinc-900"
                    x-on:mouseenter="$store.rowHover.hovered = 'table'"
                    x-on:mouseleave="$store.rowHover.hovered = null">
                    <tr>
                        <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-12">
                            <span class="text-sm text-gray-400 header-number" x-cloak>#</span>
                            <input type="checkbox" @click.stop wire:model.live="selectAll" class="select-all form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak />
                        </th>
                        @foreach($visibleColumns as $colKey)
                            @php $col = $columns[$colKey] ?? null; @endphp
                            @if($col && $colKey !== 'row_number')
                                <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300 whitespace-nowrap {{ ($col['sortable'] ?? false) && empty($selected) ? 'cursor-pointer hover:bg-zinc-800' : '' }}"
                                    @if(($col['sortable'] ?? false) && empty($selected)) wire:click="sortBy('{{ $colKey }}')" @endif
                                >
                                    <div class="flex items-center gap-1">
                                        {{ $col['label'] }}
                                        @if($col['sortable'] ?? false)
                                            @if($sort === $colKey)
                                                <flux:icon name="{{ $direction === 'asc' ? 'chevron-up' : 'chevron-down' }}" class="w-3 h-3" />
                                            @endif
                                        @endif
                                    </div>
                                </th>
                            @endif
                        @endforeach
                        <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @forelse($users as $index => $user)
                        @php
                            $rowKey = $user->getKey();
                            $rowKeyStr = (string) $rowKey;
                            $isSelected = in_array($rowKeyStr, $selected ?? []);
                        @endphp
                        <tr
                            class="group {{ $isSelected ? 'bg-gray-50 dark:bg-zinc-700 selected' : 'bg-white dark:bg-zinc-800' }}"
                            wire:key="roster-row-{{ $rowKeyStr }}"
                            data-row-key="{{ $rowKeyStr }}"
                            @click.prevent="if ($event.target.closest('button, input, a')) return; $wire.toggleRow('{{ $rowKeyStr }}')"
                            x-data="{
                                id: 'roster-menu-{{ $rowKeyStr }}',
                                top: 0,
                                left: 0,
                                init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); },
                                get open() { return Alpine.store('menu').openId === this.id },
                                openMenu(ref) {
                                    const rect = ref.getBoundingClientRect();
                                    const menuWidth = 160;
                                    this.top = Math.round(rect.bottom);
                                    this.left = Math.max(8, Math.round(rect.right - menuWidth));
                                    const newId = (Alpine.store('menu').openId === this.id ? null : this.id);
                                    Alpine.store('menu').openId = newId;
                                },
                                close() {
                                    if (Alpine.store('menu').openId === this.id) {
                                        Alpine.store('menu').openId = null;
                                    }
                                }
                            }"
                            x-on:mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'"
                            x-on:mouseleave="$store.rowHover.hovered = null"
                        >
                            <td class="px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-center justify-center">
                                    <span class="w-6 text-sm text-gray-400 text-center row-number" x-cloak>{{ ($users->currentPage() - 1) * $users->perPage() + $index + 1 }}</span>
                                    <input type="checkbox" @click.stop wire:model.live="selected" value="{{ $rowKeyStr }}" class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" x-cloak />
                                </div>
                            </td>
                            @foreach($visibleColumns as $colKey)
                                @php 
                                    $col = $columns[$colKey] ?? null;
                                    $value = $this->getColumnValue($user, $colKey);
                                @endphp
                                @if($col && $colKey !== 'row_number')
                                    <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200 max-w-md">
                                        @if(($col['format'] ?? null) === 'boolean')
                                            @if($value)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-900 text-green-300">Yes</span>
                                            @else
                                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-zinc-700 text-gray-400">No</span>
                                            @endif
                                        @else
                                            {!! $value ?? '—' !!}
                                        @endif
                                    </td>
                                @endif
                            @endforeach

                            {{-- Action column --}}
                            <td class="w-20 px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-center justify-center">
                                    <button x-cloak x-show="$store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')" x-ref="actionBtn" type="button" @click.stop="openMenu($refs.actionBtn)" class="transition bg-transparent border-0 hover:bg-gray-50 dark:hover:bg-zinc-700/50 rounded px-2 py-1 flex items-center text-gray-400 dark:text-gray-300" aria-label="Options">
                                        <flux:icon name="grip-vertical" class="w-4 h-4 text-current" />
                                    </button>

                                    <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                                        :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                                        class="w-40 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                                        <div class="flex flex-col divide-y divide-gray-100 dark:divide-zinc-700">
                                            <a href="{{ route('profile.show.other', $user) }}" @click="close()" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700" wire:navigate>
                                                <flux:icon name="eye" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                <span>View Profile</span>
                                            </a>
                                            @can('deleteRosterUser')
                                            <button type="button" @click.stop="close(); $dispatch('openRosterDeleteModal', { userId: {{ $user->id }} })" class="flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-zinc-700 w-full text-left">
                                                <flux:icon name="trash" class="w-4 h-4" />
                                                <span>Delete</span>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="{{ count($visibleColumns) + 1 }}" class="px-4 py-8 text-center text-gray-500">
                                No {{ $type }} found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <!-- Gallery/Cards View -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            @forelse($users as $index => $user)
                @php
                    $rowKey = $user->getKey();
                    $rowKeyStr = (string) $rowKey;
                    $isSelected = in_array($rowKeyStr, $selected ?? []);
                    $rowNumber = ($users->currentPage() - 1) * $users->perPage() + $index + 1;
                @endphp
                <div 
                    class="relative bg-white/5 dark:bg-zinc-800 rounded-lg border transition {{ $isSelected ? 'border-blue-500 bg-blue-50/5' : 'border-neutral-200 dark:border-neutral-700 hover:border-zinc-500' }}"
                    wire:key="roster-card-{{ $rowKeyStr }}"
                    x-data="{ 
                        hovered: false,
                        menuOpen: false,
                        openMenu() { this.menuOpen = !this.menuOpen; },
                        closeMenu() { this.menuOpen = false; }
                    }"
                    @mouseenter="hovered = true"
                    @mouseleave="hovered = false; closeMenu()"
                >
                    <div class="flex">
                        {{-- Left column: Row number / Checkbox / Grip --}}
                        <div class="flex flex-col items-center justify-between py-3 px-2 border-r border-zinc-700/30 bg-zinc-800/50 rounded-l-lg min-w-[40px]">
                            {{-- Row number or Checkbox --}}
                            <div class="flex flex-col items-center gap-2">
                                <span x-show="!hovered && !{{ $isSelected ? 'true' : 'false' }}" class="text-xs text-gray-400 font-medium">#{{ $rowNumber }}</span>
                                <input 
                                    x-show="hovered || {{ $isSelected ? 'true' : 'false' }}"
                                    type="checkbox" 
                                    @click.stop 
                                    wire:model.live="selected" 
                                    value="{{ $rowKeyStr }}" 
                                    class="form-checkbox accent-blue-600 dark:accent-gray-300"
                                />
                            </div>
                            
                            {{-- Grip icon (bottom, visible on hover) --}}
                            <div x-show="hovered || menuOpen" x-cloak class="relative">
                                <button @click.stop="openMenu()" type="button" class="p-1 rounded hover:bg-zinc-700 text-gray-400 hover:text-gray-200">
                                    <flux:icon name="grip-vertical" class="w-4 h-4" />
                                </button>
                                
                                <div x-show="menuOpen" @click.away="closeMenu()" x-transition
                                    class="absolute top-full left-0 mt-1 w-40 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg z-50">
                                    <div class="flex flex-col divide-y divide-gray-100 dark:divide-zinc-700">
                                        <a href="{{ route('profile.show.other', $user) }}" @click="closeMenu()" wire:navigate class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                            <flux:icon name="eye" class="w-4 h-4 text-gray-400" />
                                            <span>View Profile</span>
                                        </a>
                                        @can('deleteRosterUser')
                                        <button type="button" @click.stop="closeMenu(); $dispatch('openRosterDeleteModal', { userId: {{ $user->id }} })" class="flex items-center gap-2 px-3 py-2 text-sm text-red-500 hover:bg-gray-50 dark:hover:bg-zinc-700 w-full text-left">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                            <span>Delete</span>
                                        </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        {{-- Right column: Card Content --}}
                        <div class="flex-1 p-4">
                            {{-- User info --}}
                            <div class="flex items-center gap-3 mb-3">
                                <div class="w-12 h-12 rounded-full bg-zinc-700 flex items-center justify-center text-lg font-semibold text-gray-200 flex-shrink-0">
                                    {{ $user->initials() }}
                                </div>
                                <div class="min-w-0">
                                    <div class="font-medium text-gray-200 truncate">{{ $this->getColumnValue($user, 'full_name') }}</div>
                                    <div class="text-sm text-gray-400">{{ $user->role?->name }}</div>
                                </div>
                            </div>
                            
                            <div class="space-y-1 text-sm">
                                <div class="flex justify-between">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="text-gray-300 truncate max-w-[180px]">{{ $user->email }}</span>
                                </div>
                                @if($type === 'volunteers')
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Volunteer #:</span>
                                        <span class="text-gray-300">{{ $user->fceerProfile?->volunteer_number ?? '—' }}</span>
                                    </div>
                                @else
                                    <div class="flex justify-between">
                                        <span class="text-gray-500">Student #:</span>
                                        <span class="text-gray-300">{{ $user->fceerProfile?->student_number ?? '—' }}</span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-8 text-center text-gray-500">
                    No {{ $type }} found.
                </div>
            @endforelse
        </div>
    @endif

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="mt-4">
            {{ $users->links() }}
        </div>
    @endif

    {{-- Modals --}}
    @livewire('roster.roster-archive-modal')
    @livewire('roster.roster-archive-restore-modal')
    @livewire('roster.roster-user-form-modal')
    @livewire('roster.roster-delete-modal')
    @livewire('roster.roster-restore-modal')
    @livewire('roster.roster-force-delete-modal')
</div>
