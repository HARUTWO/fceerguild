<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="identification" class="w-5 h-5 text-gray-700 dark:text-gray-300" />
                <div class="profile-card-title">FCEER Profile</div>
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
            $canManage = Gate::allows('manageFceerProfile', $user);
        @endphp

        @if($canManage && $profile)
            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="edit" type="button" title="Edit">
                <flux:icon name="pencil" />
            </flux:button>
        @endif
    </div>

    @once
        <link rel="stylesheet" href="/css/reference-table.css">
        <script src="/js/reference-table.js" defer></script>
    @endonce

    @if($profile)
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
                        @php
                            $roleName = strtolower($user->role->name ?? '');
                            $isStudent = str_contains($roleName, 'student');
                            $isVolunteer = str_contains($roleName, 'system') || str_contains($roleName, 'executive') || str_contains($roleName, 'instructor') || str_contains($roleName, 'administrator');
                        @endphp

                        @if($isVolunteer)
                            <tr class="bg-white dark:bg-zinc-800">
                                <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Volunteer Number</td>
                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $profile->volunteer_number ?? '—' }}</td>
                            </tr>
                        @endif

                        @if($isStudent)
                            <tr class="bg-white dark:bg-zinc-800">
                                <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Student Number</td>
                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ $profile->student_number ?? '—' }}</td>
                            </tr>
                        @endif

                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Batch</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                @if($profile->batch_id)
                                    {{ optional($profile->batch)->batch_no ?? '—' }} ({{ optional($profile->batch)->year ?? '—' }})
                                @else
                                    —
                                @endif
                            </td>
                        </tr>

                        @if($isStudent)
                            <tr class="bg-white dark:bg-zinc-800">
                                <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Student Group</td>
                                <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">{{ optional($profile->studentGroup)->name ?? '—' }}</td>
                            </tr>
                        @endif

                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300">Status</td>
                            <td class="px-4 py-2 text-sm text-gray-500 dark:text-gray-400">
                                @if($profile->status !== null)
                                    {{ $profile->status == 1 ? 'Active' : 'Inactive' }}
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        @else
            @php
                $roleName = strtolower($user->role->name ?? '');
                $isStudent = str_contains($roleName, 'student');
                $isVolunteer = str_contains($roleName, 'system') || str_contains($roleName, 'executive') || str_contains($roleName, 'instructor') || str_contains($roleName, 'administrator');
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @if($isVolunteer)
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                        <dt class="profile-details-label">Volunteer Number</dt>
                        <dd class="profile-details-value mt-1">{{ $profile->volunteer_number ?? '—' }}</dd>
                    </div>
                @endif

                @if($isStudent)
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                        <dt class="profile-details-label">Student Number</dt>
                        <dd class="profile-details-value mt-1">{{ $profile->student_number ?? '—' }}</dd>
                    </div>
                @endif

                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                    <dt class="profile-details-label">Batch</dt>
                    <dd class="profile-details-value mt-1">
                        @if($profile->batch_id)
                            {{ optional($profile->batch)->batch_no ?? '—' }} ({{ optional($profile->batch)->year ?? '—' }})
                        @else
                            —
                        @endif
                    </dd>
                </div>

                @if($isStudent)
                    <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                        <dt class="profile-details-label">Student Group</dt>
                        <dd class="profile-details-value mt-1">{{ optional($profile->studentGroup)->name ?? '—' }}</dd>
                    </div>
                @endif

                <div class="bg-white dark:bg-zinc-800 border border-gray-200 dark:border-zinc-700 rounded p-4">
                    <dt class="profile-details-label">Status</dt>
                    <dd class="profile-details-value mt-1">
                        @if($profile->status !== null)
                            {{ $profile->status == 1 ? 'Active' : 'Inactive' }}
                        @else
                            —
                        @endif
                    </dd>
                </div>
            </div>
        @endif
    @else
        <p class="text-center py-4 text-gray-500 dark:text-gray-400">No FCEER profile found for this user.</p>
    @endif

    <livewire:profile.fceer.subsections.fceer-profile-section.fceer-profile-form-modal instance-key="App\\Models\\FceerProfile" :key="'fceer-profile-form-'.$user->id" />
    <livewire:profile.fceer.subsections.fceer-profile-section.fceer-profile-confirm-modal :key="'fceer-profile-confirm-'.$user->id" />
    <livewire:profile.fceer.subsections.fceer-profile-section.fceer-profile-details-modal :key="'fceer-profile-details-'.$user->id" />
</div>
