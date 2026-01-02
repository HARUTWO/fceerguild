<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Livewire\Component;

class FceerRecords extends Component
{
    public User $user;

    protected $listeners = [
        'requestOpenProfileModal' => 'forwardOpenProfileModal',
    ];
    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
    }

    public function render()
    {
        $sections = config('profile.fceer.sections');
        return view('livewire.profile.fceer-records', compact('sections'));
    }
    public function forwardOpenProfileModal($params)
    {
        $this->emit('openCredentialsModal', $params);
    }
}
