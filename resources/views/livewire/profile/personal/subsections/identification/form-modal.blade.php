@props(['fields' => [], 'options' => [], 'state' => [], 'isOpen' => false, 'itemId' => null])

<div>
    @if($isOpen)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="identification-form" flyout class="w-11/12 max-w-2xl" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-6">
                    <div>
                        <h2 class="text-2xl font-semibold text-gray-900 dark:text-white">Edit Identification</h2>
                        <div class="mt-3 mb-4 border-b border-gray-200 dark:border-zinc-700"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @foreach($fields as $f)
                            @php
                                $fieldKey = isset($f['key']) ? (string)$f['key'] : null;
                                $fieldType = isset($f['type']) ? (string)$f['type'] : 'text';
                                $fieldLabel = isset($f['label']) ? (string)$f['label'] : ($fieldKey ? ucwords(str_replace('_',' ', $fieldKey)) : '');
                                $fieldPlaceholder = isset($f['placeholder']) ? (string)$f['placeholder'] : '';
                                
                                // Extract select options safely for PHP 8.4
                                $opts = [];
                                if ($fieldType === 'select' && is_array($options)) {
                                    $optionsKey = isset($f['options']) ? (is_array($f['options']) ? $fieldKey : (string)$f['options']) : $fieldKey;
                                    if ($optionsKey && isset($options[$optionsKey])) {
                                        $opts = $options[$optionsKey];
                                    }
                                }
                            @endphp
                            @if($fieldKey && !is_array($fieldKey))
                                <div>
                                    <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ $fieldLabel }}</label>
                                    @if($fieldType === 'select')
                                        @php
                                            $sel = $state[$fieldKey] ?? '';
                                        @endphp
                                        @include('livewire.components.styled-select', ['name' => $fieldKey, 'options' => $opts, 'selected' => $sel, 'placeholder' => 'Select'])
                                    @elseif($fieldType === 'date')
                                        <input wire:model.defer="state.{{ $fieldKey }}" type="date" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 px-3 py-3 text-sm" />
                                    @else
                                        <input wire:model.defer="state.{{ $fieldKey }}" type="{{ $fieldType }}" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" placeholder="{{ $fieldPlaceholder }}" />
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
