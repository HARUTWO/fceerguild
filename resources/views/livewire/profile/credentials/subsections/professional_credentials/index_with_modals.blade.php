<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="briefcase" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                <div class="profile-card-title">Professional Credentials</div>
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
                foreach($items as $loopIndex => $rec) {
                    $rk = (string) $rec->getKey();
                    if(in_array($rk, $selected ?? [])) {
                        $rowNum = ($items->firstItem() ?? 0) + ($loopIndex + 1) - 1;
                        $selectedRowNumbers[] = '#'.$rowNum;
                    }
                }
            @endphp
            @if(count($selected))
                <div class="text-sm text-indigo-600 mr-2">{{ count($selected) }} selected @if(!empty($selectedRowNumbers))— <span class="font-mono text-sm">({{ implode(',', $selectedRowNumbers) }})</span>@endif</div>
                <flux:button size="xs" tone="danger" class="ml-2 w-7 h-7 flex items-center justify-center text-sm" wire:click="deleteSelected" type="button" title="Delete selected">
                    <flux:icon name="trash" />
                    <span class="sr-only">Delete selected</span>
                </flux:button>
            @endif

            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="create" type="button" title="Create">
                <flux:icon name="plus" />
            </flux:button>
            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" type="button" title="Archive" wire:click.prevent="openArchive">
                <flux:icon name="archive-restore" />
            </flux:button>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="mt-3 text-sm text-gray-500">No entries yet.</div>
    @else
        @once
            <link rel="stylesheet" href="/css/reference-table.css">
            <script src="/js/reference-table.js" defer></script>
        @endonce

        @if($view === 'table')
            <div class="mt-3 reference-table-container relative overflow-x-auto bg-white dark:bg-zinc-800 rounded shadow" style="overflow-y: visible;"
                 x-data="{ selectedLocal: @entangle('selected') }"
                 x-bind:class="{ 'has-selection': selectedLocal.length > 0 }">

                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-12">
                                <span class="text-sm text-gray-400 header-number" x-cloak>#</span>
                                <input type="checkbox" @click.stop wire:model="selectAll" wire:change="$refresh" wire:key="selectAllHeader" x-bind:checked="selectedLocal.length === {{ $items->count() }}" class="select-all form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak />
                            </th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Field</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Title</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Year</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Notes</th>
                            <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-20"></th>
                        </tr>
                    </thead>

                    <tbody class="divide-y">
                        @foreach($items as $it)
                            @php
                                $rowKey = $it->getKey();
                                $rowKeyStr = (string) $rowKey;
                                $isSelected = in_array($rowKeyStr, $selected ?? []);
                            @endphp
                            <tr
                                class="group {{ $isSelected ? 'bg-gray-50 dark:bg-zinc-700 selected' : 'bg-white dark:bg-zinc-800' }}"
                                wire:key="profile-row-{{ $rowKeyStr }}"
                                x-data="{
                                    id: 'professional-menu-{{ $rowKeyStr }}',
                                    top: 0,
                                    left: 0,
                                    init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); },
                                    get open() { return Alpine.store('menu').openId === this.id },
                                    disableScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = 'hidden'; document.body.style.overflow = 'hidden'; },
                                    restoreScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = ''; document.body.style.overflow = ''; },
                                    openMenu(ref) {
                                        const rect = ref.getBoundingClientRect();
                                        const menuWidth = 220;
                                        const minLeft = 8;
                                        const desiredLeft = rect.right - menuWidth;
                                        const maxLeft = Math.max(minLeft, window.innerWidth - menuWidth - 8);
                                        this.top = Math.round(rect.bottom);
                                        this.left = Math.min(Math.max(minLeft, Math.round(desiredLeft)), maxLeft);
                                        const newId = (Alpine.store('menu').openId === this.id ? null : this.id);
                                        Alpine.store('menu').openId = newId;
                                        if (newId === this.id) this.disableScroll(); else this.restoreScroll();
                                    },
                                    close() { if (Alpine.store('menu').openId === this.id) { Alpine.store('menu').openId = null; this.restoreScroll(); } }
                                }"
                                x-on:mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'"
                                x-on:mouseleave="$store.rowHover.hovered = null">
                                <td class="px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="flex items-center justify-center">
                                        <span class="w-6 text-sm text-gray-400 text-center row-number" x-cloak>{{ ($items->firstItem() ?? 0) + $loop->iteration - 1 }}</span>
                                        <input type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" x-cloak />
                                    </div>
                                </td>

                                @php
                                    $prefix = optional($it->prefix)->name ? (optional($it->prefix)->name . (optional($it->prefix)->abbreviation ? ' (' . optional($it->prefix)->abbreviation . ')' : '')) : null;
                                    $suffix = optional($it->suffix)->title ? (optional($it->suffix)->title . (optional($it->suffix)->abbreviation ? ' (' . optional($it->suffix)->abbreviation . ')' : '')) : null;
                                    $titleLabel = $prefix ?: $suffix ?: null;
                                    $fieldLabel = optional($it->fieldOfWork)->name ?? null;
                                @endphp
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $fieldLabel ?: '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $titleLabel ?: '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $it->issued_on ?? '—' }}</td>
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ \Illuminate\Support\Str::limit($it->notes ?? '—', 120) }}</td>

                                <td class="w-20 px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    <div class="flex items-center justify-center">
                                        <button x-cloak x-show="$store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}' )" x-ref="actionBtn" type="button" @click.stop="$wire.selectEnsure('{{ $rowKeyStr }}'); openMenu($refs.actionBtn)" class="transition bg-transparent border-0 hover:bg-gray-50 dark:hover:bg-zinc-700/50 rounded px-2 py-1 flex items-center text-gray-400 dark:text-gray-300" aria-label="Options">
                                            <flux:icon name="grip-vertical" class="w-4 h-4 text-current" />
                                        </button>

                                        <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                                             :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                                             class="w-36 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                                            <div class="flex flex-col divide-y">
                                                <button type="button" @click.stop="close()" wire:click.prevent="relayShow({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <flux:icon name="eye" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                    <span>View</span>
                                                </button>

                                                <button type="button" @click.stop="close()" wire:click.prevent="relayEdit({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <flux:icon name="pencil" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                    <span>Edit</span>
                                                </button>

                                                <button type="button" @click.stop="close()" wire:click.prevent="relayDelete({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                    <flux:icon name="trash" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                                    <span>Delete</span>
                                                </button>
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
            <div class="grid grid-cols-1 {{ $items->count() > 1 ? 'md:grid-cols-2' : 'md:grid-cols-1' }} gap-4" x-data="{ selectedLocal: @entangle('selected') }">
                @foreach($items as $it)
                    @php
                        $rowKey = $it->getKey();
                        $rowKeyStr = (string) $rowKey;
                    @endphp
                          <div wire:key="card-row-{{ $rowKeyStr }}" class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4 relative"
                                 x-data="{
                                     id: 'professional-card-menu-{{ $rowKeyStr }}',
                            top: 0,
                            left: 0,
                            init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); },
                            get open() { return Alpine.store('menu').openId === this.id },
                            disableScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = 'hidden'; document.body.style.overflow = 'hidden'; },
                            restoreScroll() { const container = document.querySelector('.reference-table-container'); if (container) container.style.overflow = ''; document.body.style.overflow = ''; },
                            openMenu(ref) {
                                const rect = ref.getBoundingClientRect();
                                const menuWidth = 160;
                                const minLeft = 8;
                                const desiredLeft = rect.right - menuWidth;
                                const maxLeft = Math.max(minLeft, window.innerWidth - menuWidth - 8);
                                this.top = Math.round(rect.bottom);
                                this.left = Math.min(Math.max(minLeft, Math.round(desiredLeft)), maxLeft);
                                const newId = (Alpine.store('menu').openId === this.id ? null : this.id);
                                Alpine.store('menu').openId = newId;
                                if (newId === this.id) this.disableScroll(); else this.restoreScroll();
                            },
                            close() { if (Alpine.store('menu').openId === this.id) { Alpine.store('menu').openId = null; this.restoreScroll(); } }
                         }"
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
                                                                        @php $rowNumberDisplay = ($items->firstItem() ?? 0) + $loop->iteration - 1; @endphp
                                                                        #{{ $rowNumberDisplay }}
                                                                </span>
                            </div>

                            <input x-cloak type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" aria-label="Select record"
                                class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300 h-4 w-4 transition-opacity duration-150"
                                :class="($store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')) ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none'" />
                        </div>

                        @php
                            $prefix = optional($it->prefix)->name ? (optional($it->prefix)->name . (optional($it->prefix)->abbreviation ? ' (' . optional($it->prefix)->abbreviation . ')' : '')) : null;
                            $suffix = optional($it->suffix)->title ? (optional($it->suffix)->title . (optional($it->suffix)->abbreviation ? ' (' . optional($it->suffix)->abbreviation . ')' : '')) : null;
                            $titleLabel = $prefix ?: $suffix ?: null;
                        @endphp
                        <div class="space-y-1">
                            <div class="text-sm font-semibold text-gray-800 dark:text-gray-100">{{ optional($it->fieldOfWork)->name ?? '—' }}</div>
                            @if($titleLabel)
                                <div class="text-xs text-gray-500 dark:text-gray-400">{{ $titleLabel }}</div>
                            @endif
                            <div class="text-sm text-gray-600 dark:text-gray-300">Year: {{ $it->issued_on ?? '—' }}</div>
                            <div class="text-xs text-gray-500 dark:text-gray-400">{{ \Illuminate\Support\Str::limit($it->notes ?? '—', 120) }}</div>
                        </div>

                        <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                            :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                            class="w-40 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                            <div class="flex flex-col divide-y">
                                <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayShow({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50">
                                    <flux:icon name="eye" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                    <span>View</span>
                                </button>
                                <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayEdit({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50">
                                    <flux:icon name="pencil" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                    <span>Edit</span>
                                </button>
                                <button type="button" @click.stop="Alpine.store('menu').openId = null; $wire.relayDelete({{ $it->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50">
                                    <flux:icon name="trash" class="w-4 h-4 text-red-600 dark:text-red-400" />
                                    <span>Delete</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endif

    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-form-modal instance-key="App\\Models\\ProfessionalCredential" :key="'professional-credentials-form-'.$user->id" />
    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-details-modal :key="'professional-credentials-details-'.$user->id" />
    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-archive :key="'professional-credentials-archive-'.$user->id" />
    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-delete-modal :key="'professional-credentials-delete-'.$user->id" />
    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-restore-modal :key="'professional-credentials-restore-'.$user->id" />
    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-force-delete-modal :key="'professional-credentials-force-delete-'.$user->id" />
</div>
