<div class="profile-container">
    <h3 class="profile-title">Account<flux:icon name="user-round-cog" /></h3>
    <div class="space-y-6">
        @foreach($sections as $key => $section)
            @php
                $componentAlias = 'profile.account.subsections.' . str_replace('_', '-', $key) . '.' . str_replace('_', '-', $key);
            @endphp
            @livewire($componentAlias, ['user' => $user], key('account-' . $key . '-' . $user->id))
        @endforeach
    </div>
<div>
