<div>
    @if($open ?? false)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="classroom-responsibility-restore" class="max-w-md" wire:model="open" @close="$set('open', false)">
                <flux:heading size="lg">Restore Classroom {{ count($labels) === 1 ? 'Responsibility' : 'Responsibilities' }}</flux:heading>

                @if(count($labels) > 0)
                    <flux:text class="mt-4">
                        Are you sure you want to restore the following {{ count($labels) === 1 ? 'classroom responsibility' : 'classroom responsibilities' }}?
                    </flux:text>

                    <ul class="mt-3 space-y-1 text-sm text-gray-700 dark:text-gray-300">
                        @foreach($labels as $label)
                            <li>• {{ $label }}</li>
                        @endforeach
                    </ul>
                @else
                    <flux:text class="mt-4">
                        No classroom responsibilities selected.
                    </flux:text>
                @endif

                <div class="flex gap-2 mt-6">
                    <flux:spacer />
                    <flux:button wire:click="$set('open', false)" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="confirm" variant="primary">Restore</flux:button>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
