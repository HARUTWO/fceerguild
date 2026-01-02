<div>
<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="user" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                <div class="profile-card-title">Personal Information</div>
            </div>

            <div class="ml-4 inline-flex items-center rounded-md overflow-hidden border">
                <button type="button" wire:click.prevent="toggleView('table')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-l-md focus:outline-none {{ $viewMode === 'table' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Table view">
                    <flux:icon name="list" class="w-4 h-4" />
                </button>
                <button type="button" wire:click.prevent="toggleView('cards')"
                    class="px-2 py-1 text-sm transition inline-flex items-center justify-center rounded-r-md focus:outline-none {{ $viewMode === 'cards' ? 'bg-gray-100 text-gray-900 shadow ring-1 ring-gray-200 dark:bg-zinc-700 dark:text-white' : 'bg-white dark:bg-zinc-800 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-zinc-700' }}"
                    aria-label="Card view">
                    <flux:icon name="gallery-vertical-end" class="w-4 h-4" />
                </button>
            </div>
        </div>

        <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="openEdit" type="button" title="Edit">
            <flux:icon name="pencil" />
        </flux:button>
    </div>

    @once
        <link rel="stylesheet" href="/css/reference-table.css">
        <script src="/js/reference-table.js" defer></script>
    @endonce

    @if($viewMode === 'table')
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
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">First Name</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $first_name ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Middle Name</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $middle_name ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Suffix Name</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $suffix_name ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Lived Name</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $lived_name ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Generational Suffix</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $generational_suffix ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Phone Number</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $phone_number ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Birthday</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $birthday ?? '—' }}</td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Sex</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                            @if(isset($sex))
                                @if($sex === 'M') Male
                                @elseif($sex === 'F') Female
                                @else Other
                                @endif
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    <tr class="bg-white dark:bg-zinc-800">
                        <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Address ID</td>
                        <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $address_id ?? '—' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">First Name</dt>
                <dd class="profile-details-value mt-1">{{ $first_name ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Middle Name</dt>
                <dd class="profile-details-value mt-1">{{ $middle_name ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Suffix Name</dt>
                <dd class="profile-details-value mt-1">{{ $suffix_name ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Lived Name</dt>
                <dd class="profile-details-value mt-1">{{ $lived_name ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Generational Suffix</dt>
                <dd class="profile-details-value mt-1">{{ $generational_suffix ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Phone Number</dt>
                <dd class="profile-details-value mt-1">{{ $phone_number ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Birthday</dt>
                <dd class="profile-details-value mt-1">{{ $birthday ?? '—' }}</dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Sex</dt>
                <dd class="profile-details-value mt-1">
                    @if(isset($sex))
                        @if($sex === 'M') Male
                        @elseif($sex === 'F') Female
                        @else Other
                        @endif
                    @else
                        —
                    @endif
                </dd>
            </div>

            <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                <dt class="profile-details-label">Address ID</dt>
                <dd class="profile-details-value mt-1">{{ $address_id ?? '—' }}</dd>
            </div>
        </div>
    @endif
</div>
<livewire:profile.personal-form-modal :key="'personal-form-modal-'.$user->id" />
</div>
