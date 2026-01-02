<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Livewire\Component;

class Credentials extends Component
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
        $sections = config('profile.credentials.sections');
        return view('livewire.profile.credentials', compact('sections'));
    }

    public function forwardOpenProfileModal($params)
    {
        // Use global emit; the CredentialsFormModal listens for 'openCredentialsModal' and filters by instanceKey
        $this->emit('openCredentialsModal', $params);
    }
}