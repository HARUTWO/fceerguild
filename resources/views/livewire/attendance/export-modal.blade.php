{{-- Export Modal Flyout --}}
<div>
@if($showModal)
    <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
        <flux:modal name="export-attendance" flyout class="w-11/12 max-w-lg" wire:model="showModal" @close="$wire.closeModal()">
            <div class="space-y-6">
                <div>
                    <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Export {{ ucfirst($exportType) }} Attendance</h2>
                    <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                </div>

                <div class="space-y-5">
                    {{-- Export Type Toggle --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">Export Type</label>
                        <div class="flex gap-2">
                            <button 
                                type="button"
                                wire:click="$set('exportType', 'students')"
                                class="flex-1 px-4 py-2 text-sm font-medium rounded-lg border transition {{ $exportType === 'students' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                            >
                                <i class="fa fa-graduation-cap mr-2"></i> Students
                            </button>
                            <button 
                                type="button"
                                wire:click="$set('exportType', 'volunteers')"
                                class="flex-1 px-4 py-2 text-sm font-medium rounded-lg border transition {{ $exportType === 'volunteers' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-600 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                            >
                                <i class="fa fa-handshake-o mr-2"></i> Volunteers
                            </button>
                        </div>
                    </div>

                    {{-- Date Range Mode --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">Date Range</label>
                        <div class="space-y-3">
                            {{-- Review Season Option --}}
                            <label class="flex items-center gap-3 p-3 rounded-lg border border-gray-200 dark:border-zinc-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition {{ $dateRangeMode === 'review_season' ? 'ring-2 ring-slate-400 bg-slate-100 dark:ring-zinc-500 dark:bg-zinc-700/50' : '' }}">
                                <input 
                                    type="radio" 
                                    wire:model.live="dateRangeMode" 
                                    value="review_season" 
                                    class="text-gray-600 focus:ring-gray-500"
                                >
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Review Season</span>
                                    @if($dateRangeMode === 'review_season')
                                        @php
                                            $reviewSeasonOptions = $reviewSeasons->mapWithKeys(fn($s) => [$s->id => $s->range_label . ($s->is_active ? ' (Active)' : '')])->toArray();
                                        @endphp
                                        <div x-data='{
                                            open: false,
                                            search: "",
                                            options: @json($reviewSeasonOptions),
                                            list: [],
                                            selected: "{{ $reviewSeasonId ?? "" }}",
                                            init() { this.list = Object.keys(this.options).map(k => ({ id: k, label: this.options[k] })); },
                                            get display() { return this.options[this.selected] ?? "Select a review season..."; },
                                            get filtered() { if (! this.search) return this.list; return this.list.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase())); },
                                            select(id) { this.selected = id; $wire.set("reviewSeasonId", id); this.open = false; this.search = ""; }
                                        }' x-init="init()" class="relative mt-2">
                                            <button type="button" @click="open = !open" class="w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-2.5 text-sm flex items-center justify-between">
                                                <span x-text="display" :class="selected === '' ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-700 dark:text-gray-100'"></span>
                                                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                            </button>
                                            <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
                                                <div class="px-3 py-2 border-b border-gray-100 dark:border-zinc-600">
                                                    <div class="relative">
                                                        <span class="absolute left-3 top-2.5 text-zinc-400"><flux:icon name="magnifying-glass" class="w-4 h-4" /></span>
                                                        <input type="search" x-model="search" placeholder="Search..." class="w-full pl-10 pr-3 py-2 rounded text-sm bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-600 focus:outline-none" />
                                                    </div>
                                                </div>
                                                <ul class="max-h-48 overflow-auto">
                                                    <template x-for="item in filtered" :key="item.id">
                                                        <li @click.prevent="select(item.id)" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-700 dark:text-gray-200" x-text="item.label"></li>
                                                    </template>
                                                    <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-zinc-500">No results</div>
                                                </ul>
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </label>

                            {{-- Custom Range Option --}}
                            <label class="flex items-start gap-3 p-3 rounded-lg border border-gray-200 dark:border-zinc-600 cursor-pointer hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition {{ $dateRangeMode === 'custom' ? 'ring-2 ring-slate-400 bg-slate-100 dark:ring-zinc-500 dark:bg-zinc-700/50' : '' }}">
                                <input 
                                    type="radio" 
                                    wire:model.live="dateRangeMode" 
                                    value="custom" 
                                    class="text-gray-600 focus:ring-gray-500 mt-1"
                                >
                                <div class="flex-1">
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Custom Range</span>
                                    @if($dateRangeMode === 'custom')
                                        <div class="mt-2 grid grid-cols-2 gap-3">
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Start Date</label>
                                                <input 
                                                    type="date" 
                                                    wire:model="customStartDate"
                                                    class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:border-gray-400 focus:ring-gray-400"
                                                >
                                            </div>
                                            <div>
                                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">End Date</label>
                                                <input 
                                                    type="date" 
                                                    wire:model="customEndDate"
                                                    class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 text-sm px-3 py-2 focus:border-gray-400 focus:ring-gray-400"
                                                >
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </label>
                        </div>
                        @error('reviewSeasonId') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('customStartDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('customEndDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>

                    {{-- Session Filter (Students Only) --}}
                    @if($exportType === 'students')
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">Session</label>
                            <div class="flex gap-2">
                                <button 
                                    type="button"
                                    wire:click="$set('sessionFilter', null)"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition {{ $sessionFilter === null ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                                >
                                    Both
                                </button>
                                <button 
                                    type="button"
                                    wire:click="$set('sessionFilter', 'am')"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition {{ $sessionFilter === 'am' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                                >
                                    <i class="fa fa-sun-o mr-1"></i> AM
                                </button>
                                <button 
                                    type="button"
                                    wire:click="$set('sessionFilter', 'pm')"
                                    class="flex-1 px-3 py-2 text-sm font-medium rounded-lg border transition {{ $sessionFilter === 'pm' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500 text-slate-700 dark:text-slate-200' : 'border-gray-200 dark:border-zinc-600 text-gray-500 dark:text-gray-400 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                                >
                                    <i class="fa fa-moon-o mr-1"></i> PM
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Filters --}}
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-3">
                            Filters (Optional)
                        </label>
                        <div class="grid grid-cols-2 gap-3">
                            {{-- Batch Filter --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Batch</label>
                                @php
                                    $batchOptions = collect($batches)->mapWithKeys(fn($b) => [$b->id => 'Batch ' . $b->batch_no . ' (' . $b->year . ')'])->toArray();
                                @endphp
                                <div x-data='{
                                    open: false,
                                    search: "",
                                    options: @json($batchOptions),
                                    list: [],
                                    selected: "{{ $batchFilter ?? "" }}",
                                    init() { this.list = Object.keys(this.options).map(k => ({ id: k, label: this.options[k] })); },
                                    get display() { return this.options[this.selected] ?? "All batches"; },
                                    get filtered() { if (! this.search) return this.list; return this.list.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase())); },
                                    select(id) { this.selected = id; $wire.set("batchFilter", id); this.open = false; this.search = ""; },
                                    clear() { this.selected = ""; $wire.set("batchFilter", ""); this.open = false; }
                                }' x-init="init()" class="relative">
                                    <button type="button" @click="open = !open" class="w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-2.5 text-sm flex items-center justify-between">
                                        <span x-text="display" :class="selected === '' ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-700 dark:text-gray-100'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
                                        <div class="px-3 py-2 border-b border-gray-100 dark:border-zinc-600">
                                            <div class="relative">
                                                <span class="absolute left-3 top-2.5 text-zinc-400"><flux:icon name="magnifying-glass" class="w-4 h-4" /></span>
                                                <input type="search" x-model="search" placeholder="Search..." class="w-full pl-10 pr-3 py-2 rounded text-sm bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-600 focus:outline-none" />
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-auto">
                                            <li @click.prevent="clear()" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-500 dark:text-gray-400 italic">All batches</li>
                                            <template x-for="item in filtered" :key="item.id">
                                                <li @click.prevent="select(item.id)" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-700 dark:text-gray-200" x-text="item.label"></li>
                                            </template>
                                            <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-zinc-500">No results</div>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Committee/Group Filter --}}
                            <div>
                                <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">
                                    {{ $exportType === 'students' ? 'Group' : 'Committee' }}
                                </label>
                                @php
                                    if ($exportType === 'students') {
                                        $committeeOptions = collect($classrooms)->mapWithKeys(fn($c) => [$c->id => $c->group])->toArray();
                                        $committeePlaceholder = 'All groups';
                                    } else {
                                        $committeeOptions = collect($committees)->mapWithKeys(fn($c) => [$c->id => $c->name])->toArray();
                                        $committeePlaceholder = 'All committees';
                                    }
                                @endphp
                                <div x-data='{
                                    open: false,
                                    search: "",
                                    options: @json($committeeOptions),
                                    list: [],
                                    selected: "{{ $committeeFilter ?? "" }}",
                                    placeholder: "{{ $committeePlaceholder }}",
                                    init() { this.list = Object.keys(this.options).map(k => ({ id: k, label: this.options[k] })); },
                                    get display() { return this.options[this.selected] ?? this.placeholder; },
                                    get filtered() { if (! this.search) return this.list; return this.list.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase())); },
                                    select(id) { this.selected = id; $wire.set("committeeFilter", id); this.open = false; this.search = ""; },
                                    clear() { this.selected = ""; $wire.set("committeeFilter", ""); this.open = false; }
                                }' x-init="init()" class="relative">
                                    <button type="button" @click="open = !open" class="w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-2.5 text-sm flex items-center justify-between">
                                        <span x-text="display" :class="selected === '' ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-700 dark:text-gray-100'"></span>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                    </button>
                                    <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
                                        <div class="px-3 py-2 border-b border-gray-100 dark:border-zinc-600">
                                            <div class="relative">
                                                <span class="absolute left-3 top-2.5 text-zinc-400"><flux:icon name="magnifying-glass" class="w-4 h-4" /></span>
                                                <input type="search" x-model="search" placeholder="Search..." class="w-full pl-10 pr-3 py-2 rounded text-sm bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-600 focus:outline-none" />
                                            </div>
                                        </div>
                                        <ul class="max-h-48 overflow-auto">
                                            <li @click.prevent="clear()" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-500 dark:text-gray-400 italic" x-text="placeholder"></li>
                                            <template x-for="item in filtered" :key="item.id">
                                                <li @click.prevent="select(item.id)" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-700 dark:text-gray-200" x-text="item.label"></li>
                                            </template>
                                            <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-zinc-500">No results</div>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {{-- Position Filter (Volunteers Only) --}}
                            @if($exportType === 'volunteers')
                                <div class="col-span-2">
                                    <label class="block text-xs text-gray-500 dark:text-gray-400 mb-1">Position</label>
                                    @php
                                        $positionOptions = collect($positions)->mapWithKeys(fn($p) => [$p->id => $p->name])->toArray();
                                    @endphp
                                    <div x-data='{
                                        open: false,
                                        search: "",
                                        options: @json($positionOptions),
                                        list: [],
                                        selected: "{{ $positionFilter ?? "" }}",
                                        init() { this.list = Object.keys(this.options).map(k => ({ id: k, label: this.options[k] })); },
                                        get display() { return this.options[this.selected] ?? "All positions"; },
                                        get filtered() { if (! this.search) return this.list; return this.list.filter(i => i.label.toLowerCase().includes(this.search.toLowerCase())); },
                                        select(id) { this.selected = id; $wire.set("positionFilter", id); this.open = false; this.search = ""; },
                                        clear() { this.selected = ""; $wire.set("positionFilter", ""); this.open = false; }
                                    }' x-init="init()" class="relative">
                                        <button type="button" @click="open = !open" class="w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-2.5 text-sm flex items-center justify-between">
                                            <span x-text="display" :class="selected === '' ? 'text-gray-400 dark:text-zinc-500' : 'text-gray-700 dark:text-gray-100'"></span>
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                        </button>
                                        <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
                                            <div class="px-3 py-2 border-b border-gray-100 dark:border-zinc-600">
                                                <div class="relative">
                                                    <span class="absolute left-3 top-2.5 text-zinc-400"><flux:icon name="magnifying-glass" class="w-4 h-4" /></span>
                                                    <input type="search" x-model="search" placeholder="Search..." class="w-full pl-10 pr-3 py-2 rounded text-sm bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 border border-gray-200 dark:border-zinc-600 focus:outline-none" />
                                                </div>
                                            </div>
                                            <ul class="max-h-48 overflow-auto">
                                                <li @click.prevent="clear()" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-500 dark:text-gray-400 italic">All positions</li>
                                                <template x-for="item in filtered" :key="item.id">
                                                    <li @click.prevent="select(item.id)" class="px-3 py-2.5 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-700 dark:text-gray-200" x-text="item.label"></li>
                                                </template>
                                                <div x-show="filtered.length === 0" class="px-4 py-3 text-sm text-gray-500 dark:text-zinc-500">No results</div>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Format Selection --}}
                    <div class="border-t border-gray-200 dark:border-zinc-700 pt-4">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-100 mb-2">Export Format</label>
                        <div class="flex gap-3">
                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition {{ $format === 'xlsx' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500' : 'border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                <input type="radio" wire:model="format" value="xlsx" class="text-gray-600 focus:ring-gray-500">
                                <i class="fa fa-file-excel-o text-gray-600"></i>
                                <span class="text-sm font-medium {{ $format === 'xlsx' ? 'text-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-400' }}">Excel (.xlsx)</span>
                            </label>
                            <label class="flex items-center gap-2 px-4 py-2 rounded-lg border cursor-pointer transition {{ $format === 'csv' ? 'bg-slate-100 dark:bg-zinc-700 border-slate-400 dark:border-zinc-500' : 'border-gray-200 dark:border-zinc-600 hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                <input type="radio" wire:model="format" value="csv" class="text-gray-600 focus:ring-gray-500">
                                <i class="fa fa-file-text-o text-gray-600"></i>
                                <span class="text-sm font-medium {{ $format === 'csv' ? 'text-gray-700 dark:text-gray-200' : 'text-gray-600 dark:text-gray-400' }}">CSV (.csv)</span>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <flux:button wire:click="export" variant="primary">
                        <i class="fa fa-download mr-2"></i> Export
                    </flux:button>
                    <flux:button wire:click="closeModal">Cancel</flux:button>
                </div>
            </div>
        </flux:modal>
    </div>
@endif
</div>
