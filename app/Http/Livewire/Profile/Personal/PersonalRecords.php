<?php

namespace App\Http\Livewire\Profile\Personal;

use App\Models\User;
use Livewire\Component;

class PersonalRecords extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
    }

    public function render()
    {
        $sections = config('profile.personal.sections');
        return view('livewire.profile.personal.layout', compact('sections'));
    }
}