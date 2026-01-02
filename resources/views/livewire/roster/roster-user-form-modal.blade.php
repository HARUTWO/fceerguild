<div>
    @if($open)
        <flux:modal name="roster-user-form" flyout class="w-11/12 max-w-lg" wire:model="open" @close="$wire.close()">
            <div class="space-y-6">
                <div>
                    <flux:heading size="lg">
                        Create {{ $type === 'students' ? 'Student' : 'Volunteer' }}
                    </flux:heading>
                    <flux:subheading>
                        Password will be auto-generated as: username + {{ $type === 'students' ? 'student' : 'volunteer' }} number
                    </flux:subheading>
                </div>

                <div class="space-y-4">
                    {{-- Username --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Username</label>
                        <flux:input 
                            wire:model.defer="username" 
                            type="text" 
                            placeholder="Enter username"
                        />
                        @error('username')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Email</label>
                        <flux:input 
                            wire:model.defer="email" 
                            type="email" 
                            placeholder="Enter email address"
                        />
                        @error('email')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Student/Volunteer Number --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">
                            {{ $type === 'students' ? 'Student Number' : 'Volunteer Number' }}
                        </label>
                        <flux:input 
                            wire:model.defer="number" 
                            type="text" 
                            placeholder="Enter {{ $type === 'students' ? 'student' : 'volunteer' }} number"
                        />
                        @error('number')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-200 mb-1">Role</label>
                        <select wire:model="roleId"
                            class="w-full rounded-md border border-gray-200 dark:border-zinc-600 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-gray-100 px-3 py-3 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500">
                            <option value="">Select role</option>
                            @foreach($availableRoles as $id => $name)
                                <option value="{{ $id }}">{{ $name }}</option>
                            @endforeach
                        </select>
                        @error('roleId')
                            <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Info --}}
                    <div class="p-3 bg-zinc-700/30 rounded text-sm text-gray-300">
                        <span class="font-medium text-gray-200">Generated Password:</span>
                        <span class="ml-1">{{ $username ?: '[username]' }}{{ $number ?: '[number]' }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-4 border-t border-zinc-700">
                    <flux:button wire:click="close" variant="ghost">Cancel</flux:button>
                    <flux:button wire:click="save" variant="primary">Create</flux:button>
                </div>
            </div>
        </flux:modal>
    @endif
</div>
