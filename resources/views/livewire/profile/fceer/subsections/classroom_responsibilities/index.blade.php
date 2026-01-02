<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="academic-cap" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                <div class="profile-card-title">Classroom Responsibilities</div>
            </div>

            <div class="ml-4 inline-flex items-center rounded-md overflow-hidden border">
                <button type="button" wire:click.prevent="setView('table')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-l-md focus:outline-none {{ $view === 'table' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Table view">
                    <flux:icon name="list" class="w-4 h-4" />
                </button>
                <button type="button" wire:click.prevent="setView('cards')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-r-md focus:outline-none {{ $view === 'cards' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Card view">
                    <flux:icon name="gallery-vertical-end" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <div class="inline-flex items-center space-x-2">
            @php
                $selectedRowNumbers = [];
                foreach($records as $loopIndex => $rec) {
                    $rk = (string) $rec->getKey();
                    if(in_array($rk, $selected ?? [])) {
                        $rowNum = ($records->firstItem() ?? 0) + ($loopIndex + 1) - 1;
                        $selectedRowNumbers[] = '#'.$rowNum;
                    }
                }
                // Check if user is executive or system manager
                $canManage = Gate::allows('manageClassroomResponsibilities', $user);
            @endphp
            @if(count($selected) && $canManage)
                <div class="text-sm text-indigo-600 dark:text-gray-300 mr-2">{{ count($selected) }} record/s selected @if(!empty($selectedRowNumbers)): <span class="font-mono text-sm">{{ implode(',', $selectedRowNumbers) }}</span>@endif</div>
                <flux:button size="xs" tone="danger" class="ml-2 w-7 h-7 flex items-center justify-center text-sm" wire:click="deleteSelected" type="button" title="Delete selected">
                    <flux:icon name="trash" />
                    <span class="sr-only">Delete selected</span>
                </flux:button>
            @endif

            @if($canManage)
                <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="create" type="button" title="Create">
                    <flux:icon name="plus" />
                </flux:button>
                <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" type="button" title="Archive" wire:click.prevent="openArchive">
                    <flux:icon name="archive-restore" />
                </flux:button>
            @endif
        </div>
    </div>

    @once
        <link rel="stylesheet" href="/css/reference-table.css">
        <script src="/js/reference-table.js" defer></script>
    @endonce

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
                                <input x-ref="selectAll" type="checkbox" @click.stop wire:model="selectAll" wire:change="$refresh" wire:key="selectAllHeader" x-bind:checked="selectedLocal.length === {{ $records->count() }}" x-effect="$refs.selectAll.indeterminate = (selectedLocal.length > 0 && selectedLocal.length < {{ $records->count() }})" class="select-all form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak />
                            </th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Classroom</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Position</th>
                        <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Note</th>
                        <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($records as $item)
                        @php
                            $rowKey = $item->getKey();
                            $rowKeyStr = (string) $rowKey;
                            $isSelected = in_array($rowKeyStr, $selected ?? []);
                        @endphp
                        <tr
                            class="group {{ $isSelected ? 'bg-gray-50 dark:bg-zinc-700 selected' : 'bg-white dark:bg-zinc-800' }}"
                            wire:key="profile-row-{{ $rowKeyStr }}"
                            data-row-key="{{ $rowKeyStr }}"
                            @click.prevent="if ($event.target.closest('button, input, a')) return; $wire.toggleRow('{{ $rowKeyStr }}')"
                            x-data="{ id: 'classroom-responsibility-menu-{{ $rowKeyStr }}', top: 0, left: 0, init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); }, get open() { return Alpine.store('menu').openId === this.id }, disableScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = 'hidden'; document.body.style.overflow = 'hidden'; }, restoreScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = ''; document.body.style.overflow = ''; }, openMenu(ref) { const rect = ref.getBoundingClientRect(); const menuWidth = 220; const minLeft = 8; const desiredLeft = rect.right - menuWidth; const maxLeft = Math.max(minLeft, window.innerWidth - menuWidth - 8); this.top = Math.round(rect.bottom); this.left = Math.min(Math.max(minLeft, Math.round(desiredLeft)), maxLeft); const newId = (Alpine.store('menu').openId === this.id ? null : this.id); Alpine.store('menu').openId = newId; if (newId === this.id) this.disableScroll(); else this.restoreScroll(); }, close() { if (Alpine.store('menu').openId === this.id) { Alpine.store('menu').openId = null; this.restoreScroll(); } } }"
                            x-on:mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'"
                            x-on:mouseleave="$store.rowHover.hovered = null">
                            <td class="px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-center justify-center">
                                    <span class="w-6 text-sm text-gray-400 text-center row-number" x-cloak>{{ ($records->firstItem() ?? 0) + $loop->iteration - 1 }}</span>
                                    <input type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" x-cloak />
                                </div>
                            </td>

                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($item->classroom)->name ?? ($item->classroom_id ? $item->classroom_id : '—') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($item->classroomPosition)->name ?? ($item->classroom_position_id ? $item->classroom_position_id : '—') }}</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $item->note ?? '—' }}</td>

                            <td class="w-20 px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                <div class="flex items-center justify-center">
                                    <button x-cloak x-show="$store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')" x-ref="actionBtn" type="button" @click.stop="$wire.selectEnsure('{{ $rowKeyStr }}'); openMenu($refs.actionBtn)" class="transition bg-transparent border-0 hover:bg-gray-50 dark:hover:bg-zinc-700/50 rounded px-2 py-1 flex items-center text-gray-400 dark:text-gray-300" aria-label="Options">
                                        <flux:icon name="grip-vertical" class="w-4 h-4 text-current" />
                                    </button>

                                    <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                                         :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                                         class="w-36 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                                        <div class="flex flex-col divide-y">
                                            <button type="button" @click.stop="close()" wire:click.prevent="relayShow({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                <flux:icon name="eye" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                <span>View</span>
                                            </button>

                                            @if($canManage)
                                                <button type="button" @click.stop="close()" wire:click.prevent="relayEdit({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <flux:icon name="pencil" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                    <span>Edit</span>
                                                </button>

                                                <button type="button" @click.stop="close()" wire:click.prevent="relayDelete({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <flux:icon name="trash" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                                    <span>Delete</span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="grid grid-cols-1 {{ $records->count() > 1 ? 'md:grid-cols-2' : 'md:grid-cols-1' }} gap-4" x-data="{ selectedLocal: @entangle('selected') }">
            @foreach($records as $item)
                @php
                    $rowKey = $item->getKey();
                    $rowKeyStr = (string) $rowKey;
                @endphp
                <div wire:key="card-row-{{ $rowKeyStr }}" class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4 relative"
                     @mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'"
                     @mouseleave="$store.rowHover.hovered = null">
                    <div class="absolute inset-y-2 right-2 flex flex-col justify-between items-center">
                        <div class="w-6 h-6 relative">
                            <button x-cloak type="button" x-ref="actionBtnCard" @click.stop="$wire.selectEnsure('{{ $rowKeyStr }}'); openMenu($refs.actionBtnCard)"
                                :class="($store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')) ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'"
                                class="btn-ghost transition-opacity duration-150 absolute inset-0 flex items-center justify-center">
                                <flux:icon name="grip-vertical" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                            </button>

                            <span x-cloak class="text-xs text-gray-400 dark:text-gray-500 absolute inset-0 flex items-center justify-center"
                                  :class="($store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')) ? 'opacity-0 pointer-events-none' : 'opacity-100'">
                                @php $rowNumberDisplay = ($records->firstItem() ?? 0) + $loop->iteration - 1; @endphp
                                #{{ $rowNumberDisplay }}
                            </span>
                        </div>

                        <input x-cloak type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" aria-label="Select record"
                            class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300 h-4 w-4 transition-opacity duration-150"
                            :class="($store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')) ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'" />
                    </div>

                    <div class="space-y-1">
                        <div class="text-md text-gray-900 dark:text-white">{{ optional($item->classroom)->name ?? '—' }}</div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">{{ optional($item->classroomPosition)->name ?? '—' }}</div>
                    </div>

                    <div x-data="{ id: 'classroom-responsibility-card-menu-{{ $rowKeyStr }}', top: 0, left: 0, init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); }, get open() { return Alpine.store('menu').openId === this.id }, disableScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = 'hidden'; document.body.style.overflow = 'hidden'; }, restoreScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = ''; document.body.style.overflow = ''; }, openMenu(ref) { const rect = ref.getBoundingClientRect(); const menuWidth = 160; const minLeft = 8; const desiredLeft = rect.right - menuWidth; const maxLeft = Math.max(minLeft, window.innerWidth - menuWidth - 8); this.top = Math.round(rect.bottom); this.left = Math.min(Math.max(minLeft, Math.round(desiredLeft)), maxLeft); const newId = (Alpine.store('menu').openId === this.id ? null : this.id); Alpine.store('menu').openId = newId; if (newId === this.id) this.disableScroll(); else this.restoreScroll(); }, close() { if (Alpine.store('menu').openId === this.id) { Alpine.store('menu').openId = null; this.restoreScroll(); } } }">
                        <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                            :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                            class="w-40 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                            <div class="flex flex-col divide-y">
                            <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayShow({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50">
                                <flux:icon name="eye" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                <span>View</span>
                            </button>
                            @if($canManage)
                                <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayEdit({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50">
                                    <flux:icon name="pencil" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                    <span>Edit</span>
                                </button>
                                <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayDelete({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <flux:icon name="trash" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span>Delete</span>
                                </button>
                            @endif
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    @if($records->isEmpty())
        <p class="text-center py-4 text-gray-500 dark:text-gray-400">No entries yet.</p>
    @endif

    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-form-modal instance-key="App\\Models\\ClassroomResponsibility" :key="'classroom-responsibilities-form-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-confirm-modal :key="'classroom-responsibilities-confirm-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-archive :key="'classroom-responsibilities-archive-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-delete-modal :key="'classroom-responsibilities-delete-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-details-modal :key="'classroom-responsibilities-details-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-force-delete-modal :key="'classroom-responsibilities-force-delete-'.$user->id" />
    <livewire:profile.fceer.subsections.classroom-responsibilities.classroom-responsibilities-restore-modal :key="'classroom-responsibilities-restore-'.$user->id" />
</div>
