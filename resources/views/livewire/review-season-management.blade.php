{{-- Review Season Management - Light/Dark Mode Minimalist Design --}}
<div class="space-y-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white flex items-center gap-3">
                <flux:icon name="calendar-days" class="w-7 h-7 text-gray-500 dark:text-gray-400" />
                Review Seasons
            </h1>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                Manage attendance review periods and active seasons
            </p>
        </div>
        @if($mode === 'list')
        <button type="button" wire:click="create"
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-zinc-600 hover:bg-gray-700 dark:hover:bg-zinc-500 rounded-lg transition">
            <flux:icon name="plus" class="w-4 h-4" />
            Create Season
        </button>
        @endif
    </div>

    {{-- Flash Messages --}}
    @if(session()->has('message'))
    <div class="flex items-center gap-3 p-4 rounded-lg bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
        <flux:icon name="check-circle" class="w-5 h-5 text-green-600 dark:text-green-400" />
        <span class="text-sm font-medium text-green-700 dark:text-green-300">
            {{ session('message') }}
        </span>
    </div>
    @endif

    @if($mode === 'list')
        {{-- List View --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-gray-50 dark:bg-zinc-700/50 border-b border-gray-200 dark:border-zinc-600">
                        <tr class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                            <th class="py-3 px-4">Season Name</th>
                            <th class="py-3 px-4">Date Range</th>
                            <th class="py-3 px-4">Duration</th>
                            <th class="py-3 px-4">Status</th>
                            <th class="py-3 px-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-zinc-700">
                        @forelse($seasons as $season)
                            @php
                                $start = \Carbon\Carbon::parse($season->start_date);
                                $end = \Carbon\Carbon::parse($season->end_date);
                                $weeks = ceil($start->diffInDays($end) / 7);
                                $isActive = $season->is_active;
                                $isCurrent = now()->between($start, $end);
                                $isPast = now()->isAfter($end);
                                $isFuture = now()->isBefore($start);
                            @endphp
                            <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/30 transition">
                                <td class="py-3 px-4">
                                    <div class="flex items-center gap-3">
                                        @if($isActive)
                                            <div class="w-2 h-2 rounded-full bg-gray-500 dark:bg-gray-400 animate-pulse"></div>
                                        @endif
                                        <div>
                                            <div class="font-medium text-gray-900 dark:text-white">{{ $season->name }}</div>
                                            @if($isCurrent)
                                                <div class="text-xs text-gray-600 dark:text-gray-400 font-medium">Current Period</div>
                                            @elseif($isFuture)
                                                <div class="text-xs text-gray-500 dark:text-gray-400">Upcoming</div>
                                            @elseif($isPast)
                                                <div class="text-xs text-gray-400 dark:text-gray-500">Past</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="text-gray-700 dark:text-gray-300">
                                        {{ $start->format('M j, Y') }} – {{ $end->format('M j, Y') }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ $start->format('l') }} to {{ $end->format('l') }}
                                    </div>
                                </td>
                                <td class="py-3 px-4 text-gray-700 dark:text-gray-300">
                                    {{ $weeks }} {{ \Illuminate\Support\Str::plural('week', $weeks) }}
                                </td>
                                <td class="py-3 px-4">
                                    <button type="button" wire:click="toggleActive({{ $season->id }})"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium transition
                                            {{ $isActive ? 'bg-gray-200 text-gray-700 dark:bg-zinc-600 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-zinc-500' : 'bg-gray-100 text-gray-600 dark:bg-zinc-700 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                                        {{ $isActive ? 'Active' : 'Inactive' }}
                                    </button>
                                </td>
                                <td class="py-3 px-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" wire:click="edit({{ $season->id }})"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition"
                                            title="Edit">
                                            <flux:icon name="pencil-square" class="w-4 h-4" />
                                        </button>
                                        <button type="button" wire:click="confirmDelete({{ $season->id }})"
                                            class="p-2 text-gray-600 dark:text-gray-400 hover:text-red-600 dark:hover:text-red-400 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition"
                                            title="Delete">
                                            <flux:icon name="trash" class="w-4 h-4" />
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <flux:icon name="calendar-days" class="w-12 h-12 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                                    <p class="text-gray-500 dark:text-gray-400">No review seasons created yet.</p>
                                    <button type="button" wire:click="create"
                                        class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:underline">
                                        <flux:icon name="plus" class="w-4 h-4" />
                                        Create your first season
                                    </button>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            @if($seasons->hasPages())
            <div class="border-t border-gray-200 dark:border-zinc-700 px-4 py-3">
                {{ $seasons->links() }}
            </div>
            @endif
        </div>

    @else
        {{-- Create/Edit Form --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl border border-gray-200 dark:border-zinc-700 p-6">
            <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                {{ $mode === 'create' ? 'Create Review Season' : 'Edit Review Season' }}
            </h2>

            <form wire:submit.prevent="save" class="space-y-5">
                {{-- Season Name --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                        Season Name <span class="text-red-500">*</span>
                    </label>
                    <input type="text" wire:model.defer="name"
                        class="w-full rounded-lg border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:border-gray-400 focus:ring-1 focus:ring-gray-400"
                        placeholder="e.g. Fall 2026 Review">
                    @error('name') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                </div>

                {{-- Date Range --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Start Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.defer="start_date"
                            class="w-full rounded-lg border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:border-gray-400 focus:ring-1 focus:ring-gray-400">
                        @error('start_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            End Date <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model.defer="end_date"
                            class="w-full rounded-lg border border-gray-200 dark:border-zinc-600 bg-white dark:bg-zinc-700 px-3 py-2 text-sm text-gray-700 dark:text-gray-200 focus:border-gray-400 focus:ring-1 focus:ring-gray-400">
                        @error('end_date') <span class="text-xs text-red-500">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Active Status --}}
                <div class="flex items-start gap-3 p-4 rounded-lg bg-gray-50 dark:bg-zinc-700/50">
                    <input type="checkbox" wire:model.defer="is_active" id="is_active"
                        class="mt-1 rounded border-gray-300 dark:border-zinc-600 text-gray-600 focus:ring-gray-500 dark:bg-zinc-700">
                    <div class="flex-1">
                        <label for="is_active" class="block text-sm font-medium text-gray-700 dark:text-gray-300 cursor-pointer">
                            Set as Active Season
                        </label>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">
                            Only one season can be active at a time. Activating this will deactivate all others.
                        </p>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <button type="button" wire:click="cancelForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-gray-800 dark:bg-zinc-600 hover:bg-gray-700 dark:hover:bg-zinc-500 rounded-lg transition">
                        {{ $mode === 'create' ? 'Create Season' : 'Save Changes' }}
                    </button>
                </div>
            </form>
        </div>
    @endif

    {{-- Delete Confirmation Modal --}}
    @if($showDeleteConfirm)
    <div class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
        <div class="flex min-h-screen items-center justify-center p-4">
            <div class="fixed inset-0 bg-black/50 transition-opacity" wire:click="cancelDelete"></div>
            <div class="relative bg-white dark:bg-zinc-800 rounded-xl shadow-xl max-w-sm w-full p-6 text-center">
                <flux:icon name="exclamation-triangle" class="w-12 h-12 text-red-500 mx-auto mb-4" />
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Delete Review Season?</h3>
                <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                    This action cannot be undone. All attendance data will remain, but this season definition will be removed.
                </p>
                <div class="flex justify-center gap-3">
                    <button type="button" wire:click="cancelDelete"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition">
                        Cancel
                    </button>
                    <button type="button" wire:click="delete"
                        class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                        Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
