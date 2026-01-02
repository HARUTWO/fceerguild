<div>
    @if($open ?? false)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="committee-memberships-force-delete" class="w-11/12 max-w-md" wire:model="open" @close="$set('open', false)">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Confirm permanent delete</flux:heading>
                    <flux:text>Are you sure you want to permanently delete the following record(s)? This cannot be undone.</flux:text>
                    @if(!empty($labels))
                        <ul class="mt-3 list-disc list-inside text-sm text-gray-700 dark:text-gray-200">
                            @foreach($labels as $lab)
                                <li>{{ $lab }}</li>
                            @endforeach
                        </ul>
                    @endif
                </div>

                <div class="flex justify-end space-x-2">
                    <flux:button wire:click="$set('open', false)">Cancel</flux:button>
                    <flux:button variant="danger" wire:click="confirm">Delete permanently</flux:button>
                </div>
            </div>
            </flux:modal>
        </div>
    @endif
</div>
