<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="educational-records-form" flyout class="w-11/12 max-w-2xl" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $itemId ? 'Edit' : 'Create' }} Educational Record</h2>
                        <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($fields as $f)
                            @php
                                if (in_array($f['key'], ['latin_honor','dost_scholarship'])) {
                                    $degLabel = $options['degree_program_id'][$state['degree_program_id']] ?? null;
                                    if (! $degLabel || !\Illuminate\Support\Str::contains(\Illuminate\Support\Str::lower($degLabel), 'bachel')) {
                                        continue;
                                    }
                                }
                            @endphp
                            <div>
                                <label class="block text-base font-bold text-gray-700 dark:text-gray-100">{{ $f['label'] ?? ucwords(str_replace('_',' ',$f['key'])) }}</label>
                                @if(($f['type'] ?? '') === 'select')
                                    @php $opts = $options[$f['key']] ?? []; $sel = $state[$f['key']] ?? null; @endphp
                                    @include('livewire.components.searchable-select', ['name' => $f['key'], 'options' => $options[$f['key']] ?? [], 'selected' => $state[$f['key']] ?? '', 'placeholder' => 'Select'])
                                @elseif(($f['type'] ?? '') === 'textarea')
                                    <textarea wire:model.defer="state.{{ $f['key'] }}" rows="3" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" placeholder="{{ $f['placeholder'] ?? '' }}"></textarea>
                                @elseif(($f['type'] ?? '') === 'checkbox')
                                    @if(($f['key'] ?? '') === 'dost_scholarship')
                                        <div class="mt-4">
                                            <div class="text-sm font-medium text-zinc-700 dark:text-zinc-200">Scholarships</div>
                                            <div class="mt-2 p-3 rounded-lg border border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20">
                                                <flux:field variant="inline">
                                                    <flux:checkbox wire:model.defer="state.{{ $f['key'] }}" label="{{ $f['label'] }}" />
                                                </flux:field>
                                            </div>
                                        </div>
                                    @else
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" wire:model.defer="state.{{ $f['key'] }}" class="form-checkbox" />
                                                <span class="ml-2">{{ $f['label'] ?? '' }}</span>
                                            </label>
                                        </div>
                                    @endif
                                @else
                                    <input wire:model.defer="state.{{ $f['key'] }}" type="{{ $f['type'] ?? 'text' }}" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" placeholder="{{ $f['placeholder'] ?? '' }}" />
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="save" variant="primary">Save</flux:button>
                        <flux:button wire:click="$set('isOpen', false)">Cancel</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>