<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-60">
            <flux:modal name="reference-restore-modal" class="md:w-md" wire:model="open" role="dialog" aria-modal="true" aria-labelledby="reference-restore-title">
                <div class="space-y-4">
                    <flux:heading id="reference-restore-title" size="lg">Restore</flux:heading>
                    <flux:subheading>Restore selected items</flux:subheading>

                    <div class="text-sm">
                        <p>Are you sure you want to restore {{ count($ids) }} item(s)?</p>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-2 mt-4">
                    <flux:modal.close>
                        <flux:button variant="filled">Cancel</flux:button>
                    </flux:modal.close>

                    <flux:button wire:click.prevent="confirm" icon:leading="archive-restore" aria-label="Confirm restore selected items">Confirm</flux:button>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
