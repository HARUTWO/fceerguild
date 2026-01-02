<div>
    @if($open ?? false)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="classroom-responsibility-details" flyout class="w-11/12 max-w-2xl" wire:model="open" @close="$set('open', false)">
                <div class="space-y-6">
                    <div>
                        <h2 class="profile-details-title">Classroom Responsibility Details</h2>
                        <div class="profile-details-divider"></div>
                    </div>

                    <div class="space-y-3">
                        <h4 class="profile-details-section-title">Responsibility Details</h4>
                        @foreach($fields as $f)
                            @php
                                $key = $f['key'];
                                if (in_array($key, ['created_at','updated_at'])) continue;
                                $val = $details[$key] ?? null;
                                $isEmpty = is_null($val) || trim((string)$val) === '' || (string)$val === '—';
                            @endphp

                            @if(! $isEmpty)
                                <div class="profile-details-field-row">
                                    <div class="profile-details-label">{{ $f['label'] ?? ucfirst($key) }}</div>
                                    <div class="col-span-2 profile-details-value">{{ $val }}</div>
                                </div>
                            @endif
                        @endforeach

                        <div class="profile-details-metadata-section">
                                <h4 class="profile-details-section-title">Metadata</h4>
                                <div class="profile-details-field-row">
                                    <div class="profile-details-label">Created</div>
                                    <div class="col-span-2 profile-details-value">
                                        {{ $details['_meta']['created_at_human'] ?? ($details['_meta']['created_at'] ?? '—') }} by {{ $details['_meta']['created_by'] ?? '—' }}
                                    </div>
                                </div>
                                <div class="profile-details-field-row mt-2">
                                    <div class="profile-details-label">Updated</div>
                                    <div class="col-span-2 profile-details-value">
                                        {{ $details['_meta']['updated_at_human'] ?? ($details['_meta']['updated_at'] ?? '—') }} by {{ $details['_meta']['updated_by'] ?? '—' }}
                                    </div>
                                </div>
                        </div>

                        @if(! empty($details['_meta']['activity']))
                            <div class="profile-details-metadata-section">
                                <h4 class="profile-details-section-title">Recent changes</h4>
                                <div class="profile-details-activity-container">
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
