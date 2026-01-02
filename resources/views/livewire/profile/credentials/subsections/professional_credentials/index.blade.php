<div class="profile-section">
    <div class="profile-section-header">
        <div class="flex items-center gap-3">
            <div class="inline-flex items-center gap-2">
                <flux:icon name="briefcase" class="w-5 h-5 text-gray-500 dark:text-gray-300" />
                <div class="profile-card-title">Professional Credentials</div>
            </div>

            <div class="inline-flex items-center rounded border border-gray-700/30 dark:border-zinc-700 overflow-hidden bg-transparent ml-3">
                <button type="button" wire:click.prevent="setView('table')" class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'table' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">Table</button>
                <button type="button" wire:click.prevent="setView('cards')" class="px-3 py-1.5 text-sm focus:outline-none transition {{ $view === 'cards' ? 'bg-gray-100 dark:bg-zinc-700 text-gray-900 dark:text-white font-semibold' : 'text-gray-300 hover:bg-gray-800/30 dark:hover:bg-zinc-700/40' }}">Gallery</button>
            </div>
        </div>

        <div class="inline-flex items-center space-x-2">
            <flux:button size="xs" tone="neutral" class="w-7 h-7 flex items-center justify-center text-sm" wire:click.prevent="create" type="button" title="Create">
                <flux:icon name="plus" />
            </flux:button>
        </div>
    </div>

    @if($items->isEmpty())
        <div class="mt-3 text-sm text-gray-500">No entries yet.</div>
    @else
        <div class="mt-3 overflow-auto">
            <table class="min-w-full divide-y table-fixed">
                <thead class="bg-gray-50 dark:bg-zinc-800">
                    <tr>
                        <th class="px-3 py-2 text-left text-sm text-gray-600">#</th>
                        <th class="px-3 py-2 text-left text-sm text-gray-600">Field</th>
                        <th class="px-3 py-2 text-left text-sm text-gray-600">Issued On</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    @foreach($items as $it)
                        <tr class="bg-white dark:bg-zinc-800">
                            <td class="px-3 py-2 text-sm text-gray-700">{{ $it->id }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ optional($it->fieldOfWork)->name ?? $it->field_of_work_id }}</td>
                            <td class="px-3 py-2 text-sm text-gray-700">{{ optional($it->issued_on)->format('Y-m-d') ?? '' }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    <livewire:profile.credentials.subsections.professional-credentials.professional-credentials-form-modal instance-key="App\\Models\\ProfessionalCredential" :key="'professional-credentials-form-'.$user->id" />
</div>
