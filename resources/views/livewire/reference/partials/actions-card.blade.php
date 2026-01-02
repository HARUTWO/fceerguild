<div class="inline-flex items-center justify-center">
    <flux:dropdown position="top" align="end">
        <!-- Use Flux ellipsis icon inside the trigger button -->
        <button slot="trigger" class="btn-ghost" aria-label="Options">
            <flux:icon name="ellipsis-vertical" class="size-4" />
        </button>

        <flux:menu>
            @unless($readOnly ?? false)
                <flux:menu.item as="button" wire:click="edit({{ $id }})" icon="pencil">{{ __('Edit') }}</flux:menu.item>
            @endunless

            <flux:menu.item as="button" wire:click="show({{ $id }})" icon="eye">{{ __('Show') }}</flux:menu.item>

            @unless($readOnly ?? false)
                <flux:menu.separator />
                <flux:menu.item as="button" wire:click="confirmDelete({{ $id }})" icon="trash" class="text-danger">{{ __('Delete') }}</flux:menu.item>
            @endunless
        </flux:menu>
    </flux:dropdown>
</div>
