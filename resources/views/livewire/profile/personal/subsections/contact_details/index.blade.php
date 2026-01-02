<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="phone" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                <div class="profile-card-title">Contact Details</div>
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
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($profile)->phone_number ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Email</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $user->email ?? '—' }}</td>
                        </tr>
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Facebook Link</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                @if(optional($profile)->facebook_link)
                                    <a href="{{ $profile->facebook_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $profile->facebook_link }}</a>
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                    <dt class="profile-details-label">Phone Number</dt>
                    <dd class="profile-details-value mt-1">{{ optional($profile)->phone_number ?? '—' }}</dd>
                </div>

                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                    <dt class="profile-details-label">Email</dt>
                    <dd class="profile-details-value mt-1">{{ $user->email ?? '—' }}</dd>
                </div>

                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                    <dt class="profile-details-label">Facebook Link</dt>
                    <dd class="profile-details-value mt-1">
                        @if(optional($profile)->facebook_link)
                            <a href="{{ $profile->facebook_link }}" target="_blank" class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">{{ $profile->facebook_link }}</a>
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </div>
        @endif

    <livewire:profile.personal.subsections.contact-details.contact-details-form-modal instance-key="App\\Models\\UserProfile" :key="'contact-details-form-'.$user->id" />
    <livewire:profile.personal.subsections.contact-details.contact-details-confirm-modal :key="'contact-details-confirm-'.$user->id" />
</div>
