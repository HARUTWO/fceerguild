<div>
    @if(($isOpen ?? false))
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
            <flux:modal name="profile-component-delete" class="max-w-lg" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-4">
                    <flux:heading size="md">Confirm Delete</flux:heading>
                    <p class="text-sm text-gray-300">Are you sure you want to delete this item? This action is reversible if you restore from archive.</p>
                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="confirmDelete" variant="danger">Delete</flux:button>
                        <flux:button wire:click="$set('isOpen', false)">Cancel</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
