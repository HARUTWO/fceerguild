<div>
    @if($renderTrigger)
        <div class="ml-3">
            <flux:modal.trigger name="reference-archive">
                <flux:button size="sm" icon:leading="archive-restore" tone="neutral" title="Archive" wire:click="open">Archive</flux:button>
            </flux:modal.trigger>
        </div>
    @endif

    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-end justify-center z-50">
            <flux:modal name="reference-archive" flyout position="bottom" class="md:w-lg" wire:model="open" @close="$set('open', false)" role="dialog" aria-modal="true">
                <div class="space-y-4">
                    <flux:heading size="lg">Archive</flux:heading>
                    <flux:subheading>Manage soft-deleted records. Restore or permanently delete items.</flux:subheading>

                    <div class="space-y-3" x-data="{ selectedLocal: @entangle('selected'), pendingAction: @entangle('pendingAction'), pendingIds: @entangle('pendingIds') }">
                            @php
                                $selectedCount = isset($selected) ? (is_countable($selected) ? count($selected) : (empty($selected) ? 0 : 1)) : 0;
                            @endphp
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <input type="checkbox" wire:model="selectAll" wire:click="toggleSelectAll" class="form-checkbox h-4 w-4" x-cloak x-show="false" />
                                </div>
                                <div class="flex items-center gap-2">
                                    <div class="text-sm text-indigo-600 dark:text-gray-100">{{ $selectedCount }} selected</div>
                                            <flux:button size="sm" wire:click.prevent="prepareBulkAction('restoreSelected')" x-bind:disabled="selectedLocal.length === 0" icon:leading="archive-restore">Restore</flux:button>
                                            <flux:button size="sm" tone="danger" wire:click.prevent="prepareBulkAction('forceDeleteSelected')" x-bind:disabled="selectedLocal.length === 0" icon:leading="trash">Permanently Delete</flux:button>
                                </div>
                            </div>

                            @if(isset($items) && (is_object($items) || is_array($items)))
                                <div class="overflow-x-auto" x-on:mouseenter="$store.rowHover.hovered = 'archive'" x-on:mouseleave="$store.rowHover.hovered = null">
                                    <table class="min-w-full divide-y">
                                    <thead class="bg-gray-50 dark:bg-zinc-900" x-on:mouseenter="$store.rowHover.hovered = 'archive'" x-on:mouseleave="$store.rowHover.hovered = null">
                                        <tr>
                                            <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-12">
                                                <span class="text-sm text-gray-400" x-cloak x-show="$store.rowHover.hovered === null && (selectedLocal.length === 0)">#</span>
                                                <input type="checkbox" @click.stop wire:model="selectAll" wire:change="$refresh" wire:key="selectAllHeaderArchive" class="form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" aria-label="Select all on page" x-cloak x-show="$store.rowHover.hovered !== null || (selectedLocal.length > 0)" />
                                            </th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">ID</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Name</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Deleted By</th>
                                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Deleted At</th>
                                            <th class="px-2 py-2 text-center text-sm font-medium text-gray-500 dark:text-gray-300 w-20"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y">
                                        @forelse($items as $item)
                                            @php
                                                $rowKey = $item->getKey();
                                                $rowKeyStr = (string) $rowKey;
                                            @endphp
                                            <tr class="group bg-white dark:bg-zinc-800" wire:key="archive-row-{{ $rowKeyStr }}" @click.prevent="if ($event.target.closest('button, input, a')) return; $wire.toggleRow('{{ $rowKeyStr }}')" x-on:mouseenter="$store.rowHover.hovered = '{{ $rowKeyStr }}'" x-on:mouseleave="$store.rowHover.hovered = null">
                                                <td class="px-2 py-2 text-sm text-gray-700 dark:text-gray-200">
                                                    <div class="flex items-center justify-center">
                                                            <span class="w-6 text-sm text-gray-400 text-center" x-cloak x-show="$store.rowHover.hovered !== '{{ $rowKeyStr }}' && !selectedLocal.includes('{{ $rowKeyStr }}')">{{ ($items->firstItem() ?? 0) + $loop->iteration - 1 }}</span>
                                                        <input type="checkbox" @click.stop wire:model="selected" wire:change="$refresh" wire:key="selected.{{ $rowKeyStr }}" value="{{ $rowKeyStr }}" class="form-checkbox mx-auto accent-blue-600 dark:accent-gray-300" x-cloak x-show="$store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}')" />
                                                    </div>
                                                </td>
                                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $item->getKey() }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $item->name }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $item->deleted_by_name ?? '-' }}</td>
                                                <td class="px-4 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $item->deleted_at->diffForHumans() }}</td>
                                                <td class="w-20 px-2 py-2 text-sm text-gray-700 dark:text-gray-200 text-right">
                                                    <div x-data="{
                                                        id: 'archive-menu-{{ $rowKeyStr }}',
                                                        top: 0,
                                                        left: 0,
                                                        init() { if (!Alpine.store('menu')) Alpine.store('menu', { openId: null }); },
                                                        get open() { return Alpine.store('menu').openId === this.id },
                                                        disableScroll() {
                                                            const container = document.querySelector('.reference-archive-modal-content');
                                                            if (container) container.style.overflow = 'hidden';
                                                            document.body.style.overflow = 'hidden';
                                                        },
                                                        restoreScroll() {
                                                            const container = document.querySelector('.reference-archive-modal-content');
                                                            if (container) container.style.overflow = '';
                                                            document.body.style.overflow = '';
                                                        },
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
                                                    }">
                                                        <button x-cloak x-show="$store.rowHover.hovered === '{{ $rowKeyStr }}' || selectedLocal.includes('{{ $rowKeyStr }}' )" type="button" @click.stop="openMenu($el)" class="btn-ghost" aria-label="Options">
                                                            <flux:icon name="ellipsis-vertical" class="w-4 h-4" />
                                                        </button>

                                                        <div x-show="open" x-cloak @keydown.escape.window="close()" @click.away="close()" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100"
                                                            :style="`position: fixed; top: ${top}px; left: ${left}px;`"
                                                            class="w-48 bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded shadow-lg dark:shadow-black/60 z-50">
                                                            <div class="flex flex-col divide-y">
                                                                <button type="button" @click.stop="close(); $wire.restore({{ $item->id }})" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                                    Restore
                                                                </button>
                                                                <button type="button" @click.stop="close(); $wire.forceDelete({{ $item->id }})" class="flex items-center gap-2 px-3 py-2 text-sm text-red-600 hover:bg-gray-50 dark:hover:bg-zinc-700">
                                                                    Delete permanently
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="6" class="px-4 py-6 text-sm text-muted">No archived items found.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                    </table>
                                </div>

                                <div class="pt-3">
                                    @if(method_exists($items, 'links'))
                                        {{ $items->links() }}
                                    @endif
                                </div>
                            @else
                                <div class="px-4 py-6 text-sm text-muted">No archived items available.</div>
                            @endif
                    </div>
                </div>
            </flux:modal>
        @endif
</div>
