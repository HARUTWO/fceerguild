@props(['fields' => [], 'options' => [], 'state' => [], 'isOpen' => false, 'itemId' => null])

<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="fceer-profile-form" flyout class="w-11/12 max-w-2xl" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit FCEER Profile</h2>
                        <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($fields as $f)
                            <div>
                                <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ $f['label'] ?? ucwords(str_replace('_',' ',$f['key'])) }}</label>
                                @if(($f['type'] ?? '') === 'searchable-select')
                                    @php
                                        $opts = $options[$f['options'] ?? $f['key']] ?? [];
                                        $sel = $state[$f['key']] ?? null;
                                    @endphp
                                    @include('livewire.components.searchable-select', ['name' => $f['key'], 'options' => $opts, 'selected' => $sel ?? '', 'placeholder' => 'Select'])
                                @elseif(($f['type'] ?? '') === 'select')
                                    @php
                                        $opts = $options[$f['options'] ?? $f['key']] ?? [];
                                        $sel = $state[$f['key']] ?? null;
                                    @endphp
                                    @include('livewire.components.styled-select', ['name' => $f['key'], 'options' => $opts, 'selected' => $sel ?? '', 'placeholder' => 'Select'])
                                @elseif(($f['type'] ?? '') === 'textarea')
                                    <textarea wire:model.defer="state.{{ $f['key'] }}" rows="3" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" placeholder="{{ $f['placeholder'] ?? '' }}"></textarea>
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
