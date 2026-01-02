<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="reference-delete-modal" class="w-11/12 max-w-md" wire:model="open" @close="$set('open', false)">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Confirm delete</flux:heading>
                    <flux:text>Are you sure you want to delete this record? This action cannot be undone.</flux:text>
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
