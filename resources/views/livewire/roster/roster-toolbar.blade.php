<div class="flex flex-col gap-3 mb-3">
    {{-- Header Row - Title + Action buttons --}}
    <div class="flex items-center justify-between gap-3 px-1">
        <div class="flex items-center gap-3">
            <flux:icon :name="$headerIcon" class="w-8 h-8 text-gray-400" />
            <h1 class="text-2xl font-bold text-gray-100">{{ $headerTitle }}</h1>
            @if(!empty($selected) && !empty($selectedRowNumbers))
                <div class="text-sm text-indigo-600 dark:text-gray-100 font-medium">Count selected: {{ implode(', ', $selectedRowNumbers) }}</div>
            @endif
        </div>

        <div class="flex items-center gap-2">
            @can('createRosterUser')
            <flux:button size="sm" icon:leading="plus" variant="primary" wire:click="openCreateForm">Create</flux:button>
            @endcan
            <flux:button size="sm" icon:leading="archive-restore" tone="neutral" title="Archive" wire:click="openArchive"></flux:button>

            {{-- Filter Dropdown --}}
        <flux:dropdown>
            <flux:button icon:leading="funnel-plus" size="sm" :disabled="!empty($selected)"></flux:button>
            <flux:menu keep-open>
                <div class="p-3 w-80">
                    <div class="text-xs font-semibold text-gray-300 uppercase mb-2">Add Filter</div>
                    
                    <label class="block text-xs text-gray-300 mb-1">Field</label>
                    <flux:select size="sm" wire:model.live="filterField" placeholder="Select field">
                        <option value="">Select field</option>
                        @foreach($availableFilters as $filterKey => $filter)
                            <option value="{{ $filterKey }}">{{ $filter['label'] }}</option>
                        @endforeach
                    </flux:select>

                    <div class="mt-2 grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-xs text-gray-300 mb-1">Operator</label>
                            <flux:select size="sm" wire:model.live="filterOperator" placeholder="Select operator">
                                <option value="equals">equals</option>
                                <option value="contains">contains</option>
                                <option value="is_null">is empty</option>
                                <option value="is_not_null">is not empty</option>
                            </flux:select>
                        </div>

                        <div>
                            <label class="block text-xs text-gray-300 mb-1">Value</label>
                            @php
                                $selectedFilter = $availableFilters[$filterField] ?? null;
                                $isBoolean = $selectedFilter && ($selectedFilter['type'] ?? '') === 'boolean';
                                $isNullOperator = in_array($filterOperator, ['is_null', 'is_not_null']);
                            @endphp
                            
                            @if($isNullOperator)
                                <flux:input size="sm" type="text" placeholder="N/A" disabled />
                            @elseif($isBoolean)
                                <flux:select size="sm" wire:model="filterValue">
                                    <option value="">Select...</option>
                                    <option value="1">{{ $selectedFilter['true_label'] ?? 'Yes' }}</option>
                                    <option value="0">{{ $selectedFilter['false_label'] ?? 'No' }}</option>
                                </flux:select>
                            @else
                                <flux:input size="sm" wire:model.debounce.300ms="filterValue" type="text" placeholder="Search..." @keydown.stop />
                            @endif
                        </div>
                    </div>

                    <div class="mt-3 flex gap-2">
                        <flux:button variant="ghost" size="sm" wire:click.prevent="addFilter">Add</flux:button>
                        <flux:button variant="ghost" size="sm" tone="neutral" wire:click.prevent="clearFilters">Clear All</flux:button>
                    </div>

                    {{-- Active filters below --}}
                    @if(!empty($activeFilters))
                        <flux:menu.separator class="my-3" />
                        <div class="text-xs font-semibold text-gray-300 uppercase mb-2">Active Filters</div>
                        <div class="space-y-2">
                            @foreach($activeFilters as $filterKey => $filterData)
                                @php
                                    $filter = $availableFilters[$filterKey] ?? null;
                                    $label = $filter['label'] ?? $filterKey;
                                    $filterValue = is_array($filterData) ? ($filterData['value'] ?? $filterData) : $filterData;
                                    $filterOperatorDisplay = is_array($filterData) ? ($filterData['operator'] ?? 'equals') : 'equals';
                                    $displayValue = $filterValue;
                                    if ($filter && ($filter['type'] ?? '') === 'select' && isset($filter['options'])) {
                                        $displayValue = $filter['options'][$filterValue] ?? $filterValue;
                                    } elseif ($filter && ($filter['type'] ?? '') === 'boolean') {
                                        $displayValue = $filterValue === '1' ? ($filter['true_label'] ?? 'Yes') : ($filter['false_label'] ?? 'No');
                                    }
                                    if ($filterOperatorDisplay === 'is_null') {
                                        $displayValue = 'is empty';
                                    } elseif ($filterOperatorDisplay === 'is_not_null') {
                                        $displayValue = 'is not empty';
                                    }
                                @endphp
                                <div class="flex items-center justify-between gap-2 px-2 py-1.5 rounded bg-zinc-700/30 text-sm">
                                    <div class="flex items-center gap-1 flex-wrap text-gray-200">
                                        <span class="font-medium">{{ $label }}</span>
                                        @if($filterOperatorDisplay === 'contains')
                                            <span class="text-xs text-gray-400">contains</span>
                                        @endif
                                        <span class="opacity-80">{{ $displayValue }}</span>
                                    </div>
                                    <button type="button" wire:click.prevent="removeFilter('{{ $filterKey }}')" class="text-red-500 hover:text-red-400 shrink-0">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </flux:menu>
        </flux:dropdown>

        {{-- Columns Dropdown --}}
        <flux:dropdown>
            <flux:button size="sm" icon:leading="columns-3" title="Toggle visible columns"></flux:button>
            <flux:menu keep-open>
                @foreach($availableColumns as $section => $sectionColumns)
                    <div x-data="{ open: false }" class="border-b border-zinc-700/50 last:border-0">
                        <button type="button" @click="open = !open" class="w-full flex items-center justify-between px-3 py-2 text-xs font-semibold text-gray-300 uppercase hover:bg-zinc-700/30 transition">
                            <span>{{ $section }}</span>
                            <svg x-show="!open" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                            <svg x-show="open" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </button>
                        <div x-show="open" x-collapse>
                            @foreach($sectionColumns as $key => $col)
                                <flux:menu.item wire:click.prevent="toggleColumn('{{ $key }}')" class="flex items-center justify-between px-3 py-1">
                                    <div class="flex items-center gap-2 text-sm">
                                        <span class="w-4 h-4 flex items-center justify-center rounded text-xs {{ in_array($key, $visibleColumns ?? []) ? 'bg-blue-600 text-white border-blue-600' : 'border border-gray-400 dark:border-gray-600' }}">
                                            @if(in_array($key, $visibleColumns ?? []))
                                                ✓
                                            @endif
                                        </span>
                                        <span>{{ $col['label'] }}</span>
                                    </div>
                                </flux:menu.item>
                            @endforeach
                        </div>
                    </div>
                @endforeach
                <flux:menu.separator />
                <flux:menu.item wire:click.prevent="resetColumns">Reset</flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- Export Dropdown --}}
        <flux:dropdown>
            <flux:button size="sm" icon:leading="file-down"></flux:button>
            <flux:menu>
                <flux:menu.item>
                    <button type="button" onclick="window.print(); return false;" class="w-full text-left px-3 py-2">Print</button>
                </flux:menu.item>
                <flux:menu.item>
                    <button type="button" wire:click="exportCsv" class="w-full text-left px-3 py-2">Download as CSV</button>
                </flux:menu.item>
                <flux:menu.item>
                    <button type="button" wire:click="exportXlsx" class="w-full text-left px-3 py-2">Download as XLSX</button>
                </flux:menu.item>
            </flux:menu>
        </flux:dropdown>

        {{-- View Toggle - Grouped button style with border --}}
        <flux:button.group class="border border-gray-300 dark:border-zinc-600 rounded">
            <flux:button size="sm" wire:click.prevent="setView('table')" :variant="$view === 'table' ? 'filled' : 'ghost'" icon="list" title="Table View" />
            <flux:button size="sm" wire:click.prevent="setView('gallery')" :variant="$view === 'gallery' ? 'filled' : 'ghost'" icon="gallery-vertical-end" title="Gallery View" />
        </flux:button.group>
        </div>
    </div>

    {{-- Search and Per Page - Full width row --}}
    <div class="w-full">
        <flux:input.group>
            <flux:input size="md" wire:model.live.debounce.300ms="search" autocomplete="off" icon="search" placeholder="Search..." />
            <flux:dropdown>
                <flux:button icon:trailing="chevron-down">{{ $perPage }}</flux:button>
                <flux:menu>
                    <flux:menu.item wire:click.prevent="$set('perPage', 15)">15</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click.prevent="$set('perPage', 25)">25</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click.prevent="$set('perPage', 50)">50</flux:menu.item>
                    <flux:menu.separator />
                    <flux:menu.item wire:click.prevent="$set('perPage', 100)">100</flux:menu.item>
                </flux:menu>
            </flux:dropdown>
        </flux:input.group>
    </div></div>