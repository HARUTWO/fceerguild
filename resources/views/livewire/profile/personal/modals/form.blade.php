<div>
    @php
        $modalModel = isset($isOpen) ? 'isOpen' : (isset($open) ? 'open' : 'isOpen');
    @endphp

    @if(($isOpen ?? $open) ?? false)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="personal-form-modal" flyout class="w-11/12 max-w-2xl" wire:model="{{ $modalModel }}" @close="$set('{{ $modalModel }}', false)">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">{{ $itemId ? 'Edit' : 'Create' }}</flux:heading>
                    </div>

                    @foreach($fields as $field)
                        @php $key = $field['key']; @endphp
                        <div class="mb-3">
                            @if(($field['type'] ?? 'text') === 'select')
                                @php
                                    $selectOptions = $options[$key] ?? ($field['options'] ?? []);
                                    $selectedLabel = $selectOptions[$data[$key] ?? ''] ?? ('Select ' . ($field['label'] ?? ucfirst($key)));
                                @endphp
                                @if($field['label'])
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-100">{{ __($field['label'] ?? ucfirst($key)) }}</label>
                                @endif
                                <flux:dropdown class="w-full">
                                    <flux:button icon:trailing="chevron-down" class="mt-1 w-full justify-start dark:text-gray-400">{{ $selectedLabel }}</flux:button>
                                    <flux:menu>
                                        @foreach($selectOptions as $val => $label)
                                            <flux:menu.item wire:click="setFieldValue('{{ $key }}', '{{ $val }}')">{{ $label }}</flux:menu.item>
                                        @endforeach
                                    </flux:menu>
                                </flux:dropdown>

                            @elseif(($field['type'] ?? '') === 'textarea')
                                <label class="block text-sm font-medium text-gray-700">{{ $field['label'] ?? ucfirst($key) }}</label>
                                <textarea wire:model.defer="data.{{ $key }}" class="form-textarea mt-1 block w-full dark:bg-zinc-800 dark:text-gray-100"></textarea>

                            @elseif(($field['type'] ?? '') === 'boolean')
                                <label class="inline-flex items-center">
                                    <input type="checkbox" wire:model.defer="data.{{ $key }}" class="form-checkbox" />
                                    <span class="ml-2">{{ $field['label'] ?? ucfirst($key) }}</span>
                                </label>

                            @else
                                <flux:input wire:model.defer="data.{{ $key }}" :label="__($field['label'] ?? ucfirst($key))" type="text" />
                            @endif

                            @error('data.'.$key) <span class="text-sm text-red-600">{{ $message }}</span> @enderror
                        </div>
                    @endforeach

                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="save" variant="primary">Save</flux:button>
                        <flux:button wire:click="$set('{{ $modalModel }}', false)">Cancel</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>