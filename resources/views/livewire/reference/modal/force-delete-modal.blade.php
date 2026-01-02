<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-60">
            <flux:modal name="reference-force-delete" class="md:w-md" wire:model="open">
                <div class="space-y-6">
                    <div>
                        <flux:heading size="lg">Delete permanently</flux:heading>

                        <flux:text class="mt-2">
                            Are you sure you want to permanently delete {{ count($ids) }} item(s)?<br>
                            This action cannot be undone.
                        </flux:text>
                    </div>

                    <div class="flex gap-2">
                        <flux:spacer />
                        <flux:modal.close>
                            <flux:button variant="ghost">Cancel</flux:button>
                        </flux:modal.close>
                        <flux:button size="sm" tone="danger" wire:click.prevent="confirm">Permanently Delete</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
