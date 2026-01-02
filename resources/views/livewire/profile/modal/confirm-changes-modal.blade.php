<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="profile-changes-modal" class="w-11/12 max-w-2xl" wire:model="open" @close="$set('open', false)">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">Confirm Changes</flux:heading>
                </div>

                @if(empty($changes))
                    <p class="text-sm text-gray-600 dark:text-gray-300">No visible changes detected.</p>
                @else
                    <div class="overflow-auto max-h-64">
                        <table class="min-w-full divide-y table-fixed">
                            <thead class="bg-gray-50 dark:bg-zinc-800">
                                <tr>
                                    <th class="px-3 py-2 text-left text-sm text-gray-600 dark:text-gray-300">Field</th>
                                    <th class="px-3 py-2 text-left text-sm text-gray-600 dark:text-gray-300">Previous</th>
                                    <th class="px-3 py-2 text-left text-sm text-gray-600 dark:text-gray-300">New</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y">
                                @foreach($changes as $key => $c)
                                    <tr class="bg-white dark:bg-zinc-800">
                                        <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-200">{{ $c['label'] ?? $key }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-600 dark:text-gray-300">{{ strlen((string)($c['old'] ?? '')) ? (is_array($c['old']) ? json_encode($c['old']) : $c['old']) : '—' }}</td>
                                        <td class="px-3 py-2 text-sm text-gray-700 dark:text-gray-200">{{ strlen((string)($c['new'] ?? '')) ? (is_array($c['new']) ? json_encode($c['new']) : $c['new']) : '—' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <div class="flex justify-end space-x-2">
                    <flux:button wire:click="$set('open', false)">Cancel</flux:button>
                    <flux:button variant="primary" wire:click="confirm">Confirm</flux:button>
                </div>
            </div>
            </flux:modal>
        </div>
    @endif
</div>
