<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="map-pin" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                <div class="profile-card-title">Address</div>
            </div>

            <div class="ml-4 inline-flex items-center rounded-md overflow-hidden border">
                <button type="button" wire:click.prevent="setView('table')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-l-md focus:outline-none {{ $view === 'table' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Table view">
                    <flux:icon name="list" class="w-4 h-4" />
                </button>
                <button type="button" wire:click.prevent="setView('cards')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-r-md focus:outline-none {{ $view === 'cards' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Card view">
                    <flux:icon name="gallery-vertical-end" class="w-4 h-4" />
                </button>
            </div>
        </div>

        @php
            $canManage = Gate::allows('managePersonal', $user);
        @endphp

        @if($canManage)
            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="edit" type="button" title="Edit">
                <flux:icon name="pencil" />
            </flux:button>
        @endif
    </div>

    @once
        <link rel="stylesheet" href="/css/reference-table.css">
        <script src="/js/reference-table.js" defer></script>
    @endonce

    @if($view === 'table')
            <div class="reference-table-container relative overflow-x-auto bg-white dark:bg-zinc-800 rounded shadow" style="overflow-y: visible;">
                <table class="min-w-full divide-y">
                    <thead class="bg-gray-50 dark:bg-zinc-900">
                        <tr>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Field</th>
                            <th class="px-4 py-2 text-left text-sm font-medium text-gray-500 dark:text-gray-300">Value</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">House Number</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($address)->house_number ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Block Number</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($address)->block_number ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Street</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($address)->street ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Barangay</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional(optional($address)->barangay)->name ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">City</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional(optional($address)->city)->name ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Province</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional(optional($address)->province)->name ?? '—' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    @else
            <div class="reference-table-container relative overflow-x-auto bg-white dark:bg-zinc-800 rounded shadow p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @if(optional($address)->house_number)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">House Number</dt>
                            <dd class="profile-details-value mt-1">{{ $address->house_number }}</dd>
                        </div>
                    @endif

                    @if(optional($address)->block_number)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">Block Number</dt>
                            <dd class="profile-details-value mt-1">{{ $address->block_number }}</dd>
                        </div>
                    @endif

                    @if(optional($address)->street)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">Street</dt>
                            <dd class="profile-details-value mt-1">{{ $address->street }}</dd>
                        </div>
                    @endif

                    @if(optional(optional($address)->barangay)->name)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">Barangay</dt>
                            <dd class="profile-details-value mt-1">{{ $address->barangay->name }}</dd>
                        </div>
                    @endif

                    @if(optional(optional($address)->city)->name)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">City</dt>
                            <dd class="profile-details-value mt-1">{{ $address->city->name }}</dd>
                        </div>
                    @endif

                    @if(optional(optional($address)->province)->name)
                        <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                            <dt class="profile-details-label">Province</dt>
                            <dd class="profile-details-value mt-1">{{ $address->province->name }}</dd>
                        </div>
                    @endif
                </div>
            </div>
    @endif

    @livewire('profile.personal.subsections.address.address-form-modal')
    @livewire('profile.personal.subsections.address.address-confirm-modal')
</div>
