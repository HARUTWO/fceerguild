<div class="overflow-auto">
    <table class="min-w-full text-sm">
        <thead>
            <tr>
                @foreach($columns as $col)
                    <th class="text-left p-2 text-gray-300">{{ $col['label'] ?? ucwords(str_replace('_',' ',$col['key'])) }}</th>
                @endforeach
                <th class="p-2"></th>
            </tr>
        </thead>
        <tbody>
            @forelse($rows as $row)
                <tr class="border-t border-gray-700">
                    @foreach($columns as $col)
                        <td class="p-2 text-gray-200">{{ data_get($row, $col['key']) }}</td>
                    @endforeach
                    <td class="p-2">
                        @if(isset($actions))
                            {!! $actions($row) !!}
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="p-4 text-gray-400" colspan="{{ count($columns) + 1 }}">No entries yet.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
