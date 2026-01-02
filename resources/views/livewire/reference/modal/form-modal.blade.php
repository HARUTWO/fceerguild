<div>
    @if($open)
        <div class="fixed inset-0 bg-black/20 backdrop-blur-sm flex items-center justify-center z-50">
            <flux:modal name="reference-form-modal" flyout class="w-11/12 max-w-2xl" wire:model="open" @close="$set('open', false)">
                <div class="space-y-6 text-gray-100">
                    <div>
                        <h2 class="text-2xl font-semibold text-white">{{ $actionLabel }}{{ $tableLabel ? ' ' . $tableLabel : '' }}</h2>
                        <div class="mt-3 mb-4 border-b border-gray-700"></div>
                    </div>

                    <div class="grid grid-cols-1 gap-4">
                        @include('livewire.reference.modal._form-fields', ['fields' => $fields])
                    </div>

                    <div class="flex justify-end space-x-2">
                        <flux:button wire:click="save" variant="primary">Save</flux:button>
                        <flux:button wire:click="$set('open', false)">Cancel</flux:button>
                    </div>
                </div>
            </flux:modal>
        </div>
    @endif
</div>
