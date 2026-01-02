@foreach($fields as $field)
    @php
        $key = $field['key'];
    @endphp
    <div>
        @if(in_array($field['type'] ?? 'text', ['text','string']))
            <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ __($field['label'] ?? ucfirst($key)) }}</label>
            <input wire:model.defer="state.{{ $key }}" type="text" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" />
        @elseif(($field['type'] ?? '') === 'textarea')
            <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ $field['label'] ?? ucfirst($key) }}</label>
            <textarea wire:model.defer="state.{{ $key }}" rows="3" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm"></textarea>
        @elseif(in_array($field['type'] ?? '', ['select', 'searchable-select']))
                @php
                    // Prefer resolved options passed in via $options (set by the modal component).
                    // Fallback to $field['options'] only when it's an array.
                    $selectOptions = [];
                    if (isset($options) && is_array($options) && isset($options[$key]) && (is_array($options[$key]) || $options[$key] instanceof \Illuminate\Support\Collection)) {
                        $selectOptions = (array) $options[$key];
                    } elseif (isset($field['options']) && is_array($field['options'])) {
                        $selectOptions = $field['options'];
                    }
                @endphp
            <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ __($field['label'] ?? ucfirst($key)) }}</label>
            @include('livewire.components.searchable-select', [
                'name' => $key,
                'options' => $selectOptions,
                'selected' => $state[$key] ?? '',
                'placeholder' => 'Select',
            ])
        @elseif(($field['type'] ?? '') === 'boolean')
            <label class="inline-flex items-center">
                <input type="checkbox" wire:model.defer="state.{{ $key }}" class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50" />
                <span class="ml-2 text-base font-medium text-gray-700 dark:text-gray-100">{{ $field['label'] ?? ucfirst($key) }}</span>
            </label>
        @elseif(($field['type'] ?? '') === 'number')
            <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ __($field['label'] ?? ucfirst($key)) }}</label>
            <input wire:model.defer="state.{{ $key }}" type="number" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" />
        @else
            <label class="block text-base font-medium text-gray-700 dark:text-gray-100">{{ __($field['label'] ?? ucfirst($key)) }}</label>
            <input wire:model.defer="state.{{ $key }}" type="text" class="mt-1 block w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-zinc-500 px-3 py-3 text-sm" />
        @endif

        @error('state.'.$key) <span class="text-sm text-red-600">{{ $message }}</span> @enderror
    </div>
@endforeach
