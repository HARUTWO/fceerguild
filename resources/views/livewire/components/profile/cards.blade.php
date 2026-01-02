<div class="grid grid-cols-1 gap-4">
    @forelse($rows as $row)
        <div class="p-4 bg-gray-800 rounded-md">
            @foreach($columns as $col)
                <div class="text-sm text-gray-200"><strong>{{ $col['label'] ?? ucwords(str_replace('_',' ',$col['key'])) }}:</strong> {{ data_get($row, $col['key']) }}</div>
            @endforeach
            <div class="mt-2">
                @if(isset($actions))
                    {!! $actions($row) !!}
                @endif
            </div>
        </div>
    @empty
        <div class="p-4 text-gray-400">No entries yet.</div>
    @endforelse
</div>
