<th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">
    <div class="flex items-center space-x-2">
        <span>{{ $f['label'] ?? ucfirst($key) }}</span>
        <div class="flex items-center">
            @php
                // Use the Livewire component's properties `sort` and `direction`
                $currentKey = $sort ?? null;
                $currentDir = $direction ?? null;
                $active = $currentKey === $key;

                if ($active) {
                    $icon = $currentDir === 'desc' ? 'chevron-down' : 'chevron-up';
                    $next = $currentDir === 'asc' ? 'desc' : 'asc';
                    $title = $currentDir === 'asc' ? 'Sorted ascending — click to sort descending' : 'Sorted descending — click to sort ascending';
                } else {
                    $icon = 'chevron-up';
                    $next = 'asc';
                    $title = 'Sort';
                }
            @endphp

            <flux:button variant="subtle" size="xs" icon="{{ $icon }}" title="{{ $title }}" wire:click="sortBy('{{ $key }}', '{{ $next }}')" class="p-1" />
        </div>
    </div>
</th>
