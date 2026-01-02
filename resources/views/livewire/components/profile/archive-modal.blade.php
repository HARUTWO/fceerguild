<div>
    @if(($isOpen ?? false))
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="profile-component-archive" class="w-11/12 max-w-4xl" wire:model="isOpen" @close="$set('isOpen', false)">
                <div class="space-y-4">
                    <flux:heading size="md">Archive</flux:heading>
                    <p class="text-sm text-gray-300">Showing archived items. You can restore or permanently delete records.</p>
                    <div class="mt-4">
                        {{-- Expect $rows and $columns to render a small table --}}
                        <div class="overflow-auto max-h-96">
                            <table class="min-w-full text-sm">
                                <thead>
                                    <tr>
                                        @foreach($columns as $col)
                                            <th class="text-left p-2 text-gray-300">{{ $col['label'] ?? ucwords(str_replace('_',' ',$col['key'])) }}</th>
                                        @endforeach
                                        <th class="p-2"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($rows as $row)
                                        <tr class="border-t border-gray-700">
                                            @foreach($columns as $col)
                                                <td class="p-2 text-gray-200">{{ data_get($row, $col['key']) }}</td>
                                            @endforeach
                                            <td class="p-2">
                                                <flux:button size="xs" wire:click.prevent="restore({{ $row->id }})">Restore</flux:button>
                                                <flux:button size="xs" variant="danger" wire:click.prevent="forceDelete({{ $row->id }})">Delete</flux:button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <flux:button wire:click="$set('isOpen', false)">Close</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
