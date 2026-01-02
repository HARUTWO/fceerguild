<div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
    @foreach($items as $item)
        <div class="bg-white/5 dark:bg-zinc-800 rounded p-4 flex items-center justify-between border border-neutral-200 dark:border-neutral-700" wire:key="reference-card-{{ $item->getKey() }}">
            <div class="text-sm font-medium">
                @php $firstField = $fields[0] ?? ['key' => 'id', 'type' => 'text']; @endphp
                @if(($firstField['type'] ?? '') === 'select')
                    {{ collect($firstField['options'] ?? [])->get(data_get($item, $firstField['key'])) ?? data_get($item, $firstField['key']) }}
                @else
                    {{ data_get($item, $firstField['key'] ?? 'id') }}
                @endif
            </div>
            <div class="flex items-center gap-2">
                @include('livewire.reference.partials.actions-card', ['id' => $item->getKey()])
            </div>
        </div>
    @endforeach
</div>
