<div class="profile-container">
    <h3 class="profile-title">Personal Records <flux:icon name="user" /></h3>

    @foreach($sections as $key => $section)
        <div class="mt-6">
            @php $componentAlias = 'profile.personal.subsections.'.str_replace('_','-',$key).'.'.str_replace('_','-',$key); @endphp
            @if(View::exists('livewire.profile.personal.subsections.'.$key.'.index'))
                @livewire($componentAlias, ['user' => $user], key($componentAlias.'-'.$user->id))
            @else
                <div class="profile-section">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $section['label'] }}</div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
</div>
