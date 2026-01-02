<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="professional-credentials-form" flyout class="w-11/12 max-w-2xl" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-extrabold text-gray-900 dark:text-white">{{ $itemId ? 'Edit' : 'Create' }} Professional Credential</h2>
                        <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($fields as $f)
                            @php
                                $key = $f['key'] ?? '';
                                // Use explicit headings for prefix/suffix and custom notes label
                                if ($key === 'prefix_id') {
                                    $labelText = 'Prefix';
                                } elseif ($key === 'suffix_id') {
                                    $labelText = 'Suffix';
                                } elseif ($key === 'notes') {
                                    $labelText = 'Notes on Title';
                                } else {
                                    $labelText = $f['label'] ?? ucwords(str_replace('_',' ',$key));
                                }

                                $showConditional = true;
                                // Only show issued_on and notes when a prefix or suffix is selected
                                if (in_array($key, ['issued_on', 'notes'])) {
                                    $showConditional = ! empty($state['prefix_id'] ?? null) || ! empty($state['suffix_id'] ?? null);
                                }
                            @endphp

                            @if($showConditional)
                                <div>
                                    <label class="block text-base font-bold text-gray-700 dark:text-gray-100">{{ $labelText }}</label>
                                    @if(($f['type'] ?? '') === 'select')
                                        @php $opts = $options[$f['key']] ?? []; $sel = $state[$f['key']] ?? null; @endphp
                                        @if(in_array($f['key'], ['prefix_id','suffix_id']))
                                            @include('livewire.components.searchable-select', ['name' => $f['key'], 'options' => $options[$f['key']] ?? [], 'selected' => $state[$f['key']] ?? '', 'placeholder' => 'Select'])
                                        @else
                                            @php $selLabel = $opts[$sel] ?? 'Select'; @endphp
                                            @if($f['key'] === 'field_of_work_id')
                                                @include('livewire.components.searchable-select', ['name' => $f['key'], 'options' => $options[$f['key']] ?? [], 'selected' => $state[$f['key']] ?? '', 'placeholder' => 'Select'])
                                            @else
                                                <div x-data="{ open: false }" class="relative">
                                                    <button type="button" @click="open = !open" class="mt-1 w-full text-left rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-3 text-sm flex items-center justify-between">
                                                        <span>{{ $selLabel }}</span>
                                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                                                    </button>

                                                        <div x-show="open" x-cloak @click.outside="open = false" class="absolute z-50 mt-1 w-full bg-white dark:bg-zinc-700 border border-gray-200 dark:border-zinc-600 rounded shadow-lg">
                                                            <ul class="max-h-56 overflow-auto">
                                                                @foreach($opts as $val => $label)
                                                                    <li wire:click.prevent="setField('{{ $f['key'] }}','{{ $val }}')" @click="open = false" class="px-3 py-3 cursor-pointer hover:bg-gray-100 dark:hover:bg-zinc-600 text-sm text-gray-900 dark:text-gray-100">{{ $label }}</li>
                                                                @endforeach
                                                            </ul>
                                                        </div>
                                                </div>
                                            @endif
                                        @endif
                                        @elseif(($f['type'] ?? '') === 'textarea')
                                        <textarea wire:model.defer="state.{{ $f['key'] }}" rows="3" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-3 text-sm"></textarea>
                                    @elseif(($f['type'] ?? '') === 'checkbox')
                                        <div class="mt-2">
                                            <label class="inline-flex items-center">
                                                <input type="checkbox" wire:model.defer="state.{{ $f['key'] }}" class="form-checkbox" />
                                                <span class="ml-2">{{ $labelText }}</span>
                                            </label>
                                        </div>
                                        @else
                                        <input wire:model.defer="state.{{ $f['key'] }}" type="{{ $f['type'] ?? 'text' }}" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-3 text-sm" />
                                    @endif
                                </div>
                            @endif
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
