@php
    $sections = [
        'account' => 'Account',
        'fceer' => 'FCEER Records',
        'personal' => 'Personal Records',
        'credentials' => 'Credentials',
    ];
@endphp

<div class="flex gap-8">
    <!-- Sidebar Navigation + Avatar -->
    <div class="w-64 sticky top-25 self-start">
        <div class="mb-6 flex flex-col items-center px-6 text-center">
            @if($currentPicture && $currentPicture->attachment)
                <div class="w-20 h-20 aspect-square flex-none rounded-full bg-white p-0 overflow-hidden flex items-center justify-center ring-2 ring-gray-200 shadow-sm">
                    <img src="{{ asset('storage/' . $currentPicture->attachment->path) }}" 
                         alt="{{ $user->name }}" 
                         class="w-full h-full object-cover">
                </div>
            @else
                <div class="w-20 h-20 aspect-square flex-none rounded-full bg-white p-0 overflow-hidden flex items-center justify-center text-gray-900 text-2xl font-semibold ring-2 ring-gray-200 shadow-sm">
                    {{ $user->initials() }}
                </div>
            @endif
            <div class="mt-3 profile-value font-medium">{{ $user->name ?? $user->email }}</div>
            @php
                $roleText = '';
                if (method_exists($user, 'getRoleNames')) {
                    $roles = $user->getRoleNames();
                    if (is_iterable($roles)) {
                        $names = [];
                        foreach ($roles as $r) {
                            if (is_string($r)) {
                                $names[] = $r;
                            } elseif (is_object($r) && isset($r->name)) {
                                $names[] = $r->name;
                            } elseif (is_array($r) && isset($r['name'])) {
                                $names[] = $r['name'];
                            } else {
                                $names[] = (string) $r;
                            }
                        }
                        $roleText = implode(', ', $names);
                    } else {
                        $roleText = (string) $roles;
                    }
                } elseif (isset($user->role)) {
                    if (is_string($user->role)) {
                        $roleText = $user->role;
                    } elseif (is_object($user->role) && isset($user->role->name)) {
                        $roleText = $user->role->name;
                    } elseif (is_array($user->role) && isset($user->role['name'])) {
                        $roleText = $user->role['name'];
                    } else {
                        $roleText = (string) $user->role;
                    }
                }
            @endphp
            <div class="mt-1 text-sm profile-label">{{ $roleText }}</div>
        </div>

        <nav class="profile-sidebar-nav">
            @foreach($sections as $key => $label)
                <a href="{{ route('profile.show.section', ['user' => $user->id, 'section' => $key]) }}"
                   class="profile-sidebar-btn {{ $active === $key ? 'profile-sidebar-btn--active' : 'profile-sidebar-btn--inactive' }}">
                    {{ $label }}
                </a>
            @endforeach
        </nav>
    </div>

    <!-- Main Content -->
    <div class="flex-1">
        @if($active === 'personal')
            <livewire:profile.personal-records :user="$user" :key="'personal-'.$user->id" />
        @elseif($active === 'account')
            <livewire:profile.account-records :user="$user" :key="'account-'.$user->id" />
        @elseif($active === 'credentials')
            <livewire:profile.credentials :user="$user" :key="'credentials-'.$user->id" />
        @elseif($active === 'fceer')
            <livewire:profile.fceer-records :user="$user" :key="'fceer-'.$user->id" />
        @endif
    </div>
</div>