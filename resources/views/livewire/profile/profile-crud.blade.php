<div class="profile-section">
    {{-- Avatar moved to parent view so it's shown once per profile --}}
    <!-- Header (styled like reference toolbar: no search, cards/table toggle, create button) -->
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="profile-card-title">{{ $title ?? '' }}</div>

            <div class="inline-flex items-center rounded border border-gray-700/30 dark:border-zinc-700 overflow-hidden bg-transparent ml-3">
                <button type="button" wire:click.prevent="setView('table')"
                    class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'table' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">
                    Table
                </button>
                <button type="button" wire:click.prevent="setView('cards')"
                    class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'cards' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">
                    Gallery
                </button>
            </div>
        </div>

        <div class="inline-flex items-center space-x-2">
            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="create" type="button" title="Create">
                <flux:icon name="plus" />
            </flux:button>
            @if(count($selected))
                <flux:button size="xs" tone="danger" class="ml-2 w-7 h-7 flex items-center justify-center text-sm" wire:click="deleteSelected" type="button" title="Delete selected">
                    <flux:icon name="trash" />
                    <span class="sr-only">Delete selected</span>
                </flux:button>
            @endif
        </div>
    </div>

    <!-- Data Display -->
    @if($view === 'cards')
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($items as $item)
                <div class="profile-card">
                    @foreach($fields as $field)
                        <div class="mb-2">
                            <strong class="block text-sm profile-label">{{ $field['label'] }}:</strong>
                            <div class="profile-value">@if($field['type'] === 'select')
                                    {{ $item->{$field['key']} ? $item->{$field['key']}->name ?? $item->{$field['key']} : 'N/A' }}
                                @else
                                    {{ $item->{$field['key']} }}
                                @endif
                            </div>
                        </div>
                    @endforeach
                    <div class="flex space-x-2 mt-4">
                        <flux:button size="xs" tone="neutral" class="px-2 py-1 text-xs" wire:click.prevent="relayEdit({{ $item->getKey() }})">Edit</flux:button>
                        <button wire:click="delete({{ $item->id }})" class="inline-flex items-center gap-2 px-2 py-0.5 text-xs rounded-md border border-red-200 dark:border-red-700 text-red-600 hover:bg-red-50 dark:hover:bg-red-700/20">Delete</button>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        @once
            <link rel="stylesheet" href="/css/reference-table.css">
            <script src="/js/reference-table.js" defer></script>
        @endonce

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
                            <input type="checkbox" @click.stop wire:model="selectAll" wire:change="$refresh" wire:key="selectAllHeader" class="select-all form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak />
                        </th>
                        @foreach($fields as $field)
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">{{ $field['label'] }}</th>
                        @endforeach
                        <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-20"></th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($items as $item)
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
                            x-data="{
                                id: 'menu-{{ $rowKeyStr }}',
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
                            @foreach($fields as $field)
                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                                    @if($field['type'] === 'select')
                                        {{ $item->{$field['key']} ? $item->{$field['key']}->name ?? $item->{$field['key']} : 'N/A' }}
                                    @else
                                        {{ $item->{$field['key']} }}
                                    @endif
                                </td>
                            @endforeach

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

                                            <button type="button" @click.stop="close()" wire:click.prevent="relayEdit({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                <flux:icon name="pencil" class="w-4 h-4 text-gray-400 dark:text-gray-300" />
                                                <span>Edit</span>
                                            </button>

                                            <button type="button" @click.stop="close()" wire:click.prevent="relayDelete({{ $item->getKey() }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-zinc-700">
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
    @endif

    <!-- Pagination -->
    {{ $items->links() }}

    <!-- Modals: mounted per-section; removed shared profile-form-modal mount -->
</div>