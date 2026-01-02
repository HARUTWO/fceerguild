<div>
    @if($open && $user)
        <flux:modal name="roster-delete" class="max-w-lg" wire:model="open" @close="$wire.close()">
            <div class="space-y-4">
                <flux:heading size="md" class="text-red-400">Delete User</flux:heading>
                
                <div class="space-y-3">
                    <p class="text-sm text-gray-300">
                        You are about to delete <strong class="text-gray-100">{{ $user->name }}</strong>.
                        This action is reversible - you can restore the user from the archive.
                    </p>

                    <div class="p-3 bg-zinc-700/30 rounded">
                        <div class="text-sm text-gray-400">User Details:</div>
                        <div class="text-sm text-gray-200 mt-1">
                            <div><strong>Name:</strong> {{ $user->name }}</div>
                            <div><strong>Email:</strong> {{ $user->email }}</div>
                            <div><strong>Role:</strong> {{ $user->role?->name ?? '—' }}</div>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">
                            Type the email to confirm: <span class="text-gray-400 font-normal">{{ $user->email }}</span>
                        </label>
                        <flux:input 
                            wire:model.defer="confirmEmail" 
                            type="email" 
                            placeholder="Enter email to confirm"
                        />
                        @error('confirmEmail')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-700">
                    <flux:button wire:click="close" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="confirmDelete" variant="danger">Delete</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
