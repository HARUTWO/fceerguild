<div>
    <div class="flex items-center justify-between mb-3">
        <div class="flex-1">
            @livewire('reference.reference-toolbar', [
                'modelClass' => $modelClass ?? null,
                'configKey' => $configKey ?? null,
                'fields' => $fields ?? [],
                'visibleFields' => $visibleFields ?? [],
                'filters' => $filters ?? [],
                'search' => $search ?? '',
                'perPage' => $perPage ?? 15,
                'view' => $view ?? 'rows',
                'selected' => $selected ?? [],
                'sort' => $sort ?? 'id',
                'direction' => $direction ?? 'desc',
                'readOnly' => $readOnly ?? false,
            ])
        </div>
    </div>

    @if($view === 'cards')
        @include('livewire.reference.partials.cards', ['items' => $items, 'fields' => $fields])
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
                    {{-- render both; toggle visibility via Alpine class binding for reliability --}}
                          <span class="text-sm text-gray-400 header-number" x-cloak>#</span>
                              <input type="checkbox" @click.stop wire:model="selectAll" wire:change="$refresh" wire:key="selectAllHeader" class="select-all form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak />
                </th>
                @foreach($fields as $f)
                    @php $key = $f['key']; @endphp
                    @if(in_array($key, $visibleFields ?? []))
                        @include('livewire.reference.partials.header-sort', ['key' => $key, 'f' => $f])
                    @endif
                @endforeach
                {{-- Header for action column to match styling of other headers --}}
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
                    wire:key="reference-row-{{ $rowKeyStr }}"
                    data-row-key="{{ $rowKeyStr }}"
                    @click.prevent="if ($event.target.closest('button, input, a')) return; $wire.toggleRow('{{ $rowKeyStr }}')"
                    x-data="{
                        id: 'menu-{{ $rowKeyStr }}',
                        top: 0,
                        left: 0,
                        init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); },
                        get open() { return Alpine.store('menu').openId === this.id },
                        disableScroll() {
                            const container = document.querySelector('.reference-table-container');
                            if (container) container.style.overflow = 'hidden';
                            document.body.style.overflow = 'hidden';
                        },
                        restoreScroll() {
                            const container = document.querySelector('.reference-table-container');
                            if (container) container.style.overflow = '';
                            document.body.style.overflow = '';
                        },
                        openMenu(ref) {
                            const rect = ref.getBoundingClientRect();
                            const menuWidth = 220; // a bit wider to accommodate content

                            // Use fixed positioning anchored to the viewport so the menu
                            // stays aligned with the clicked row even when the table
                            // has its own scrollbar.
                            const minLeft = 8;
                            const desiredLeft = rect.right - menuWidth;
                            const maxLeft = Math.max(minLeft, window.innerWidth - menuWidth - 8);

                            // top/left in viewport coordinates
                            this.top = Math.round(rect.bottom);
                            this.left = Math.min(Math.max(minLeft, Math.round(desiredLeft)), maxLeft);

                            const newId = (Alpine.store('menu').openId === this.id ? null : this.id);
                            Alpine.store('menu').openId = newId;
                            if (newId === this.id) {
                                this.disableScroll();
                            } else {
                                // closed
                                this.restoreScroll();
                            }
                        },
                        close() {
                            if (Alpine.store('menu').openId === this.id) {
                                Alpine.store('menu').openId = null;
                                this.restoreScroll();
                            }
                        }
                    }"
                    x-on:mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'"
                    x-on:mouseleave="$store.rowHover.hovered = null"
                >
                    <td class="px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                        <div class="flex items-center justify-center">
                            {{-- render both; show number only when NOT hovered and NOT selected --}}
                            <span class="w-6 text-sm text-gray-400 text-center row-number" x-cloak>{{ ($items->firstItem() ?? 0) + $loop->iteration - 1 }}</span>
                            {{-- show checkbox when hovered for this row OR when this row is selected (CSS handles visibility) --}}
                            <input type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" class="row-checkbox form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" x-cloak />
                        </div>
                    </td>
                    @foreach($fields as $f)
                        @php $key = $f['key']; @endphp
                        @if(in_array($key, $visibleFields ?? []))
                        @php $isName = isset($f['key']) && $f['key'] === 'name'; @endphp
                        <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">
                            @if($isName)
                                <div>
                                    <button x-ref="btnName" @click.stop="$wire.toggleRow('{{ $rowKeyStr }}')" type="button" class="flex items-center gap-3 w-full text-left">
                                        <span class="truncate">{{ $displayValues[$rowKeyStr][$key] ?? data_get($item, $key) }}</span>
                                    </button>
                                </div>
                            @else
                                {{ $displayValues[$rowKeyStr][$key] ?? data_get($item, $key) }}
                            @endif
                        </td>
                        @endif
                    @endforeach

                    {{-- Action column: small reserved space centered for ellipsis/menu --}}
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

    <div class="pt-3" @click.stop>
        @if(method_exists($items, 'links'))
            {{ $items->links() }}
        @endif
    </div>
    {{-- Archive modal is mounted in the parent `referencecrud` to avoid duplicate mounts --}}
</div>