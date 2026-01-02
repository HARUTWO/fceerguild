<div class="bg-zinc-900 border border-zinc-700 rounded-2xl px-4 py-3 text-sm text-gray-100">
    <div class="text-xs text-gray-400 mb-3 tracking-wide">
        {{ $a['created_at_human'] ?? ($a['created_at'] ?? '') }} @if(!empty($a['created_at_human'])) — @endif {{ $a['causer_name'] ?? 'system' }}
    </div>

    @php $rows = $a['rows'] ?? []; @endphp
    @if(!empty($rows))
        <div class="overflow-auto bg-zinc-950 rounded-xl">
            <table class="min-w-full text-sm table-auto">
                <thead class="text-left text-xs text-gray-400 uppercase tracking-wide">
                    <tr>
                        <th class="px-3 py-2">Field</th>
                        <th class="px-3 py-2">Previous</th>
                        <th class="px-3 py-2">New</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($rows as $r)
                        <tr class="border-t border-gray-800">
                            <td class="px-3 py-2 align-top text-gray-100">{{ $r['field'] ?? '—' }}</td>
                            <td class="px-3 py-2 text-gray-300">{{ strlen((string)($r['old'] ?? '')) ? $r['old'] : '—' }}</td>
                            <td class="px-3 py-2 text-gray-100">{{ strlen((string)($r['new'] ?? '')) ? $r['new'] : '—' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="mt-1 text-sm text-gray-100">{{ $a['description'] ?? '' }}</div>
    @endif
</div>
