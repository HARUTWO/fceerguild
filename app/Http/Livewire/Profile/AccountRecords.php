<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use Livewire\Component;

class AccountRecords extends Component
{
    public User $user;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function render()
    {
        $sections = config('profile.account.sections', []);
        return view('livewire.profile.account.layout', compact('sections'));
    }
}