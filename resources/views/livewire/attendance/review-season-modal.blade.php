{{-- Review Season Modal (Flyout) --}}
<div>
@if($showModal)
<div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
    <flux:modal name="review-season" flyout class="w-11/12 max-w-2xl" wire:model="showModal" @close="$wire.closeModal()">
        <div class="space-y-6">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">
                    @if($mode === 'list')
                        Manage Review Seasons
                    @elseif($mode === 'create')
                        Create Review Season
                    @else
                        Edit Review Season
                    @endif
                </h2>
                <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
            </div>

            <div class="space-y-5">
                @if($mode === 'list')
                    {{-- List View --}}
                    <div class="space-y-3">
                        @forelse($seasons as $season)
                            <div class="p-4 rounded-lg border {{ $season->is_active ? 'border-gray-400 dark:border-zinc-500 bg-gray-50 dark:bg-zinc-700/50' : 'border-gray-200 dark:border-zinc-700 bg-white dark:bg-zinc-800' }}">
                                <div class="flex items-start justify-between gap-4">
                                    <div class="flex-1">
                                        <div class="flex items-center gap-2">
                                            <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">
                                                {{ $season->range_label }}
                                            </span>
                                            @if($season->is_active)
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-200 dark:bg-zinc-600 text-gray-700 dark:text-gray-300">
                                                    <i class="fa fa-check-circle mr-1"></i> Active
                                                </span>
                                            @endif
                                        </div>
                                        
                                        @if($season->fceerBatches->count() > 0)
                                            <div class="mt-1 flex flex-wrap gap-1">
                                                @foreach($season->fceerBatches as $batch)
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs bg-gray-100 dark:bg-zinc-700 text-gray-600 dark:text-gray-400">
                                                        Batch {{ $batch->batch_no }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if($season->setBy)
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                                                Set by {{ $season->setBy->name }}
                                            </p>
                                        @endif
                                    </div>

                                    <div class="flex items-center gap-2">
                                        @if(!$season->is_active)
                                            <button 
                                                type="button"
                                                wire:click="setAsActive({{ $season->id }})"
                                                class="px-3 py-1.5 text-xs font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-zinc-600 hover:bg-gray-200 dark:hover:bg-zinc-500 rounded-md transition"
                                                title="Set as Active"
                                            >
                                                <i class="fa fa-check mr-1"></i> Activate
                                            </button>
                                        @endif
                                        <button 
                                            type="button"
                                            wire:click="showEditForm({{ $season->id }})"
                                            class="p-2 text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 transition"
                                            title="Edit"
                                        >
                                            <i class="fa fa-pencil"></i>
                                        </button>
                                        @if(!$season->is_active)
                                            <button 
                                                type="button"
                                                wire:click="deleteSeason({{ $season->id }})"
                                                wire:confirm="Are you sure you want to delete this review season?"
                                                class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition"
                                                title="Delete"
                                            >
                                                <i class="fa fa-trash-o"></i>
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fa fa-calendar-o text-4xl text-gray-300 dark:text-gray-600 mb-3"></i>
                                <p class="text-sm text-gray-500 dark:text-gray-400">No review seasons created yet.</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Footer for List Mode --}}
                    <div class="flex justify-end pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button 
                            type="button" 
                            wire:click="showCreateForm"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-zinc-600 dark:hover:bg-zinc-500 rounded-lg transition flex items-center gap-2"
                        >
                            <i class="fa fa-plus"></i> New Season
                        </button>
                    </div>
                @else
                    {{-- Create/Edit Form --}}
                    <p class="text-sm text-gray-600 dark:text-gray-400">
                        Set the review season date range. Only attendance records within this range can be created or edited.
                    </p>

                    {{-- Date Range --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">Start</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select 
                                    wire:model="startMonth"
                                    class="rounded-md border-gray-200 dark:border-zinc-600 dark:bg-zinc-700 text-sm focus:border-gray-400 focus:ring-gray-400"
                                >
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endforeach
                                </select>
                                <input 
                                    type="number" 
                                    wire:model="startYear"
                                    min="2000" 
                                    max="2100"
                                    class="rounded-md border-gray-200 dark:border-zinc-600 dark:bg-zinc-700 text-sm focus:border-gray-400 focus:ring-gray-400"
                                    placeholder="Year"
                                >
                            </div>
                            @error('startMonth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @error('startYear') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">End</label>
                            <div class="grid grid-cols-2 gap-2">
                                <select 
                                    wire:model="endMonth"
                                    class="rounded-md border-gray-200 dark:border-zinc-600 dark:bg-zinc-700 text-sm focus:border-gray-400 focus:ring-gray-400"
                                >
                                    @foreach(range(1, 12) as $m)
                                        <option value="{{ $m }}">{{ \Carbon\Carbon::create()->month($m)->format('F') }}</option>
                                    @endforeach
                                </select>
                                <input 
                                    type="number" 
                                    wire:model="endYear"
                                    min="2000" 
                                    max="2100"
                                    class="rounded-md border-gray-200 dark:border-zinc-600 dark:bg-zinc-700 text-sm focus:border-gray-400 focus:ring-gray-400"
                                    placeholder="Year"
                                >
                            </div>
                            @error('endMonth') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                            @error('endYear') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Linked Batches --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider mb-2">
                            Linked Batches (Optional)
                        </label>
                        <div class="max-h-40 overflow-y-auto border border-gray-200 dark:border-zinc-600 rounded-lg p-2 space-y-1">
                            @forelse($batches as $batch)
                                <label class="flex items-center gap-2 px-2 py-1.5 rounded hover:bg-gray-50 dark:hover:bg-zinc-700 cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        wire:model="linkedBatchIds"
                                        value="{{ $batch->id }}"
                                        class="rounded text-gray-600 focus:ring-gray-500"
                                    >
                                    <span class="text-sm text-gray-700 dark:text-gray-300">
                                        Batch {{ $batch->batch_no }} 
                                        @if($batch->year)
                                            <span class="text-gray-500 dark:text-gray-400">({{ $batch->year }})</span>
                                        @endif
                                    </span>
                                </label>
                            @empty
                                <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-2">No batches available.</p>
                            @endforelse
                        </div>
                        <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">
                            Link batches to this review season for easier filtering.
                        </p>
                    </div>

                    {{-- Info Box --}}
                    <div class="p-3 rounded-lg border border-gray-300 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700/50">
                        <p class="text-xs text-gray-600 dark:text-gray-400">
                            <i class="fa fa-info-circle mr-1"></i>
                            Only weekend dates (Saturday & Sunday) within this range will be available for attendance recording.
                        </p>
                    </div>

                    {{-- Footer for Create/Edit Mode --}}
                    <div class="flex justify-between pt-4 border-t border-gray-200 dark:border-zinc-700">
                        <button 
                            type="button" 
                            wire:click="backToList"
                            class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition flex items-center gap-1"
                        >
                            <i class="fa fa-arrow-left"></i> Back
                        </button>
                        <button 
                            type="button" 
                            wire:click="save"
                            class="px-4 py-2 text-sm font-medium text-white bg-gray-800 hover:bg-gray-900 dark:bg-zinc-600 dark:hover:bg-zinc-500 rounded-lg transition flex items-center gap-2"
                        >
                            <i class="fa fa-check"></i> {{ $mode === 'create' ? 'Create' : 'Save Changes' }}
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </flux:modal>
</div>
@endif
</div>
