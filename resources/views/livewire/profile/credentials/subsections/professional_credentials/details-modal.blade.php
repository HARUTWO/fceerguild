<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="professional-credential-details-modal" flyout class="w-11/12 max-w-2xl" wire:model="open" @close="$set('open', false)">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Professional Credential Details</flux:heading>
                    </div>

                    <div class="space-y-3">
                        @foreach($fields as $f)
                            @php
                                $key = $f['key'];
                                if (in_array($key, ['created_at','updated_at'])) continue;
                                $val = $details[$key] ?? null;
                                $isEmpty = is_null($val) || trim((string)$val) === '' || (string)$val === '—';
                            @endphp

                            @if(! $isEmpty)
                                <div class="grid grid-cols-3 gap-4 items-start">
                                    <div class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $f['label'] ?? ucfirst($key) }}</div>
                                    <div class="col-span-2 text-sm text-gray-800 dark:text-gray-200">{{ $val }}</div>
                                </div>
                            @endif
                        @endforeach

                        <div class="pt-4 border-t">
                            <div class="grid grid-cols-3 gap-4">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-300">Created</div>
                                <div class="col-span-2 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $details['_meta']['created_at_human'] ?? ($details['_meta']['created_at'] ?? '—') }} by {{ $details['_meta']['created_by'] ?? '—' }}
                                </div>
                            </div>
                            <div class="grid grid-cols-3 gap-4 mt-2">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-300">Updated</div>
                                <div class="col-span-2 text-sm text-gray-800 dark:text-gray-200">
                                    {{ $details['_meta']['updated_at_human'] ?? ($details['_meta']['updated_at'] ?? '—') }} by {{ $details['_meta']['updated_by'] ?? '—' }}
                                </div>
                            </div>
                        </div>

                        @if(! empty($details['_meta']['activity']))
                            <div class="pt-4 border-t">
                                <h4 class="text-sm font-semibold text-gray-700 dark:text-gray-200 mb-2">Recent changes</h4>
                                <div class="space-y-2 max-h-40 overflow-auto text-sm text-gray-700 dark:text-gray-300">
                                    @foreach($details['_meta']['activity'] as $a)
                                        @include('livewire.reference.partials.activity-row', ['a' => $a])
                                    @endforeach
                                </div>
                            </div>
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
