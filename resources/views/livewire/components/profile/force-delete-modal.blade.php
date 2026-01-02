<div>
    @if(($isOpen ?? false))
        <div class="fixed inset-0 bg-black/30 flex items-center justify-center z-50">
            <flux:modal name="profile-component-force-delete" class="max-w-lg" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-4">
                    <flux:heading size="md">Force Delete</flux:heading>
                    <p class="text-sm text-gray-300">This will permanently delete the item. This action cannot be undone.</p>
                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="confirmForceDelete" variant="danger">Delete Permanently</flux:button>
                        <flux:button wire:click="$set('isOpen', false)">Cancel</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
