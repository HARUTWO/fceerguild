<div class="mb-4 flex items-center justify-between gap-3">
    <div class="flex items-center gap-3">
        @if(empty($selected))
            <flux:button size="sm" icon:trailing="plus" wire:click="create" type="button" title="Create">Create</flux:button>
        @else
            <div class="flex items-center gap-3">
                <div class="text-sm text-indigo-600 dark:text-gray-100">{{ is_countable($selected) ? count($selected) : (empty($selected) ? 0 : 1) }} selected</div>
                <button type="button" wire:click="deleteSelected" class="inline-flex items-center gap-2 px-2 py-1 text-sm rounded-md border border-red-200 text-red-600 hover:bg-red-50">Delete</button>
            </div>
        @endif
    </div>

    <div class="flex items-center gap-2">
        <flux:button size="sm" icon:leading="archive-restore" tone="neutral" title="Archive" wire:click="openArchive">Archive</flux:button>

        <flux:dropdown>
            <flux:button icon:leading="funnel-plus" size="sm">Filter</flux:button>
            <flux:menu keep-open>
                <div class="p-3 w-72">
                    <label class="block text-xs text-gray-300 mb-1">Field</label>
                    <flux:select size="sm" wire:model="filterField" placeholder="Select field">
                        <option value="">Select field</option>
                        @foreach($fields as $f)
                            @if(! empty($f['key']))
                                <option value="{{ $f['key'] }}">{{ $f['label'] ?? $f['key'] }}</option>
                            @endif
                        @endforeach
                    </flux:select>

                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-300 mb-1">Operator</label>
                            <flux:select size="sm" wire:model="filterOperator" placeholder="Select operator">
                                <option value="contains">contains</option>
                                <option value="equals">equals</option>
                                <option value="starts_with">starts with</option>
                                <option value="ends_with">ends with</option>
                                <option value="gt">&gt;</option>
                                <option value="lt">&lt;</option>
                                <option value="between">between</option>
                                <option value="in">in (comma)</option>
                                <option value="is_null">is null</option>
                                <option value="is_not_null">is not null</option>
                            </flux:select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-300 mb-1">Value</label>
                            <flux:input size="sm" wire:model.debounce.300ms="filterValue" type="text" placeholder="Value" @keydown.stop />
                        </div>
                    </div>

                        <div class="mt-3 flex gap-2">
                            <flux:button variant="ghost" size="sm" wire:click.prevent="addFilter">Add</flux:button>
                            <flux:button variant="ghost" size="sm" tone="neutral" wire:click.prevent="clearFilters">Clear</flux:button>
                        </div>

                        {{-- Active filters (inside dropdown) --}}
                        @if(! empty($filters))
                            <div class="mt-3 space-y-2">
                                @foreach($filters as $i => $f)
                                    @php
                                        $field = collect($fields ?? [])->first(fn($x) => ($x['key'] ?? null) === ($f['field'] ?? null));
                                        $label = $field['label'] ?? ($f['field'] ?? '');
                                        $op = $f['op'] ?? '';
                                        $val = $f['value'] ?? null;
                                    @endphp
                                    <div class="flex items-center justify-between gap-2 px-2 py-1 rounded bg-gray-700/10 dark:bg-zinc-700/20 text-sm">
                                        <div class="flex items-center gap-2">
                                            <span class="font-medium">{{ $label }}</span>
                                            <span class="opacity-80">{{ $op === 'is_null' ? 'is null' : ($op === 'is_not_null' ? 'is not null' : ($val ?? '')) }}</span>
                                        </div>
                                        <div>
                                            <button type="button" wire:click.prevent="removeFilter({{ $i }})" class="text-xs px-2 py-0.5 rounded bg-transparent border">Remove</button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </flux:menu>
            </flux:dropdown>

        <flux:dropdown>
            <flux:button size="sm" icon:leading="columns-3" title="Toggle visible columns">Columns</flux:button>
            <flux:menu keep-open>
                @foreach($fields as $f)
                    @php $k = $f['key'] ?? null; @endphp
                    @if($k)
                        <flux:menu.item wire:click.prevent="toggleVisibleField('{{ $k }}')" class="flex items-center justify-between px-3 py-1">
                            <div class="flex items-center gap-2 text-sm">
                                <span class="w-4 h-4 flex items-center justify-center rounded border border-gray-200">
                                    @if(in_array($k, $visibleFields ?? []))
                                        ✓
                                    @endif
                                </span>
                                <span>{{ $f['label'] ?? ucfirst($k) }}</span>
                            </div>
                        </flux:menu.item>
                    @endif
                @endforeach
                <flux:menu.separator />
                <flux:menu.item wire:click.prevent="resetVisibleFields">Reset</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <flux:dropdown>
            <flux:button size="sm" icon:leading="file-down">Export</flux:button>
            <flux:menu>
                @php
                    $exportFields = collect($fields ?? [])->filter(function ($f) use ($visibleFields) {
                        return ! empty($f['key']) && in_array($f['key'], $visibleFields ?? []);
                    })->map(function ($f) {
                        return ['key' => $f['key'] ?? null, 'label' => $f['label'] ?? ($f['key'] ?? '')];
                    })->values()->toArray();
                @endphp
                <flux:menu.item>
                    <a href="#" onclick="printReferenceTable(); return false;" class="block px-3 py-2">Print</a>
                </flux:menu.item>
                <flux:menu.item>
                    <a class="block px-3 py-2" target="_blank" href="{{ route('references.export', [
                        'modelClass' => $modelClass,
                        'format' => 'csv',
                        'fields' => json_encode($exportFields),
                        'filters' => json_encode($filters ?? []),
                        'search' => $search ?? '',
                        'sort' => $sort ?? 'id',
                        'direction' => $direction ?? 'desc',
                    ]) }}">Download as CSV</a>
                </flux:menu.item>
                <flux:menu.item>
                    <a class="block px-3 py-2" target="_blank" href="{{ route('references.export', [
                        'modelClass' => $modelClass,
                        'format' => 'xlsx',
                        'fields' => json_encode($exportFields),
                        'filters' => json_encode($filters ?? []),
                        'search' => $search ?? '',
                        'sort' => $sort ?? 'id',
                        'direction' => $direction ?? 'desc',
                    ]) }}">Download as XLSX</a>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        <div class="inline-flex items-center rounded border border-gray-700/30 dark:border-zinc-700 overflow-hidden bg-transparent">
            <button type="button" wire:click.prevent="setView('rows')"
                class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'rows' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">
                Table
            </button>
            <button type="button" wire:click.prevent="setView('cards')"
                class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'cards' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">
                Gallery
            </button>
        </div>
    </div>
</div>
