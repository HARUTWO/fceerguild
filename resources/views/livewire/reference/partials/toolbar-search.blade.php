<div class="mb-3">
    <div class="flex items-center gap-3">
        <div class="flex-1">
            <flux:input.group>
                <flux:input size="md" wire:model.debounce.300ms="search" wire:input="$set('search', $event.target.value)" autocomplete="off" icon="search" placeholder="Search..."/>
                <flux:dropdown>
                    <flux:button icon:trailing="chevron-down">{{ $perPage }}</flux:button>
                    <flux:menu>
                        <flux:menu.item wire:click.prevent="$set('perPage', 10)">10</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item wire:click.prevent="$set('perPage', 15)">15</flux:menu.item>
                        <flux:menu.separator />
                        <flux:menu.item wire:click.prevent="$set('perPage', 25)">25</flux:menu.item>
                    </flux:menu>
                </flux:dropdown>
            </flux:input.group>
        </div>
    </div>
</div>
