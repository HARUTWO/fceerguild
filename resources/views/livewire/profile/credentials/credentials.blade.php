<div class="profile-container">
    <h3 class="profile-title">Credentials <flux:icon name="file-badge" /></h3>


    @foreach($sections as $key => $section)
        <div class="mt-6">
            @if(View::exists('livewire.profile.credentials.subsections.'.$key.'.index'))
                @php $componentAlias = 'profile.credentials.subsections.'.str_replace('_','-',$key).'.'.str_replace('_','-',$key); @endphp
                @livewire($componentAlias, ['user' => $user], key($componentAlias.'-'.$user->id))
            @else
                <div class="profile-section">
                    <div class="flex items-center justify-between">
                        <div class="font-medium">{{ $section['label'] }}</div>
                        <div>
                            <flux:button size="sm" wire:click="$emitUp('requestOpenProfileModal', ['instanceKey' => $section['model'], 'modelClass' => $section['model'], 'fields' => json_decode(json_encode($section['fields']), true), 'userId' => $user->id])">Add</flux:button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endforeach
    {{-- modal instance for credentials area --}}
    {{-- Each `profile-crud` now renders its own `profile-form-modal` instance. --}}
    @livewire(\App\Http\Livewire\Profile\Modal\ProfileConfirmChangesModal::class, ['modelClass' => null], key('profile-confirm-'.($user->id ?? uniqid())))

</div>
