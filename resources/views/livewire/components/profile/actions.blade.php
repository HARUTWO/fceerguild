<div class="flex items-center space-x-2">
    @if(isset($canEdit) ? $canEdit : true)
        <flux:button size="xs" wire:click.prevent="relayEdit({{ $row->id }})">Edit</flux:button>
    @endif
    <flux:button size="xs" wire:click.prevent="relayShow({{ $row->id }})">Show</flux:button>
    <flux:menu>
        <flux:button size="xs" icon:trailing="chevron-down">Actions</flux:button>
        <flux:menu>
            <flux:menu.item wire:click.prevent="relayDelete({{ $row->id }})">Delete</flux:menu.item>
            <flux:menu.item wire:click.prevent="openArchive">Archive</flux:menu.item>
        </flux:menu>
    </flux:menu>
</div>
