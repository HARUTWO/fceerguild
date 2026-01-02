<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="reference-details-modal" flyout class="w-11/12 max-w-2xl" wire:model="open" @close="$set('open', false)">
            <div class="space-y-6 text-gray-100">
                <div>
                    <h2 class="text-3xl font-semibold text-white">Record Details</h2>
                    <div class="mt-3 mb-4 border-b border-gray-700"></div>
                </div>

                <div class="space-y-6">
                    <section class="space-y-4">
                        <h4 class="text-base font-semibold text-gray-300">Information</h4>

                        <div class="space-y-3">
                            @foreach($fields as $f)
                                @php
                                    $key = $f['key'];
                                    if (in_array($key, ['created_at','updated_at'])) {
                                        continue;
                                    }
                                    $val = $details[$key] ?? null;
                                    $isEmpty = is_null($val) || trim((string)$val) === '' || (string)$val === '—';
                                @endphp

                                @if(! $isEmpty)
                                    <div class="grid grid-cols-[140px_minmax(0,1fr)] gap-4">
                                        <div class="text-sm font-medium text-gray-500">{{ $f['label'] ?? ucfirst($key) }}</div>
                                        <div class="text-sm text-gray-100">{{ $val }}</div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </section>

                    <section class="space-y-3">
                        <h4 class="text-base font-semibold text-gray-300">Metadata</h4>
                        <div class="space-y-2 border-t border-b border-gray-700 py-3">
                            <div class="grid grid-cols-[140px_minmax(0,1fr)] gap-4">
                                <div class="text-sm font-medium text-gray-500">Created</div>
                                <div class="text-sm text-gray-100">
                                    {{ $details['_meta']['created_at_human'] ?? ($details['_meta']['created_at'] ?? '—') }} by {{ $details['_meta']['created_by'] ?? '—' }}
                                </div>
                            </div>
                            <div class="grid grid-cols-[140px_minmax(0,1fr)] gap-4">
                                <div class="text-sm font-medium text-gray-500">Updated</div>
                                <div class="text-sm text-gray-100">
                                    {{ $details['_meta']['updated_at_human'] ?? ($details['_meta']['updated_at'] ?? '—') }} by {{ $details['_meta']['updated_by'] ?? '—' }}
                                </div>
                            </div>
                        </div>
                    </section>

                    @if(! empty($details['_meta']['activity']))
                        <section class="space-y-3">
                            <h4 class="text-base font-semibold text-gray-300">Recent changes</h4>
                            <div class="space-y-3 max-h-64 overflow-y-auto pr-2">
                                @foreach($details['_meta']['activity'] as $a)
                                    @include('livewire.reference.partials.activity-row', ['a' => $a])
                                @endforeach
                            </div>
                        </section>
                    @endif
                </div>

                <div class="flex justify-end">
                    <flux:button wire:click="$set('open', false)">Close</flux:button>
                </div>
            </div>
            </flux:modal>
        </div>
    @endif
</div>
