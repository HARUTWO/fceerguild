<div>
    @if($open && $user)
        <flux:modal name="roster-restore" class="max-w-lg" wire:model="open" @close="$wire.close()">
            <div class="space-y-4">
                <flux:heading size="md">Restore User</flux:heading>
                
                <div class="space-y-3">
                    <p class="text-sm text-gray-300">
                        Are you sure you want to restore <strong class="text-gray-100">{{ $user->name }}</strong>?
                    </p>

                    <div class="p-3 bg-zinc-700/30 rounded">
                        <div class="text-sm text-gray-400">User Details:</div>
                        <div class="text-sm text-gray-200 mt-1">
                            <div><strong>Name:</strong> {{ $user->name }}</div>
                            <div><strong>Email:</strong> {{ $user->email }}</div>
                            <div><strong>Deleted at:</strong> {{ $user->deleted_at?->format('M d, Y H:i') ?? '—' }}</div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-700">
                    <flux:button wire:click="close" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="confirmRestore" variant="primary">Restore</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
