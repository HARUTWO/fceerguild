<div class="profile-container">
    <div class="flex items-center justify-between">
        <h3 class="profile-title">Account Settings</h3>
        <div class="flex items-center gap-2">
            <button wire:click.prevent="toggleView('cards')" class="px-2 py-1 rounded-md text-sm bg-white/5">Cards</button>
            <button wire:click.prevent="toggleView('table')" class="px-2 py-1 rounded-md text-sm bg-white/5">Table</button>
            <a href="{{ route('user-password.edit') }}" title="Edit Password" class="p-2 rounded-md text-gray-500 hover:bg-white/5">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path d="M13.586 3.586a2 2 0 112.828 2.828l-8.486 8.486a1 1 0 01-.464.263l-4 1a1 1 0 01-1.213-1.213l1-4a1 1 0 01.263-.464l8.486-8.486z"/></svg>
            </a>
        </div>
    </div>

    @if($viewMode === 'table')
        <div class="mt-4 overflow-auto">
            <table class="w-full text-left">
                <tbody>
                    <tr><th class="py-2">Email</th><td class="py-2">{{ $user->email ?? '-' }}</td></tr>
                    <tr><th class="py-2">Username</th><td class="py-2">{{ $user->username ?? ($user->name ?? '-') }}</td></tr>
                    <tr><th class="py-2">Registered</th><td class="py-2">{{ optional($user->created_at)->toDayDateTimeString() ?? '-' }}</td></tr>
                </tbody>
            </table>
        </div>
    @else
        <div class="grid gap-4">
        <div>
            <div class="profile-label">Email</div>
            <div class="profile-value">{{ $user->email ?? '-' }}</div>
        </div>

        <div>
            <div class="profile-label">Username</div>
            <div class="profile-value">{{ $user->username ?? ($user->name ?? '-') }}</div>
        </div>

        <div>
            <div class="profile-label">Registered</div>
            <div class="profile-value">{{ optional($user->created_at)->toDayDateTimeString() ?? '-' }}</div>
        </div>
</div>
    @endif
</div>
