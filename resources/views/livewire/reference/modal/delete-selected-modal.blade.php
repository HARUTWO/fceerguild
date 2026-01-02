<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="reference-delete-selected-modal" class="w-11/12 max-w-lg" wire:model="open" @close="$set('open', false)">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Confirm delete</flux:heading>
                        <flux:text>Are you sure you want to delete these <strong>{{ count($ids) }}</strong> records? This action cannot be undone.</flux:text>
                        @if(!empty($labels))
                            <ul class="mt-3 list-disc pl-6 text-sm text-zinc-700 dark:text-zinc-300 max-h-40 overflow-auto">
                                @foreach($labels as $label)
                                    <li>{{ $label }}</li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="$set('open', false)">Cancel</flux:button>
                        <flux:button variant="danger" wire:click="confirm">Delete</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
