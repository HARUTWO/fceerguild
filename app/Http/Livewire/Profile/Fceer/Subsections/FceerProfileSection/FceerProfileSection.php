<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection;

use App\Models\User;
use App\Models\FceerProfile;
use Livewire\Component;

class FceerProfileSection extends Component
{
    public User $user;
    public ?FceerProfile $fceerProfile = null;
    public string $view = 'table';

    protected $listeners = [
        'savedCredential' => '$refresh',
        'refreshList' => '$refresh',
        'refreshReferenceTable' => '$refresh',
    ];

    public function setView(string $view)
    {
        $this->view = $view;
    }

    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
        $this->loadProfile();
    }

    public function loadProfile()
    {
        $this->fceerProfile = FceerProfile::where('user_id', $this->user->id)->first();
    }

    public function edit()
    {
        // Only admin, executive, system administrator can edit fceer profiles
        if (! \Illuminate\Support\Facades\Gate::allows('manageFceerProfile', $this->user)) {
            abort(403, 'Only administrators, executives, and system managers can edit FCEER profiles.');
        }

        $this->emit('openFceerProfileFormModal', [
            'instanceKey' => FceerProfile::class,
            'itemId' => $this->fceerProfile?->id,
            'userId' => $this->user->id,
        ]);
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.fceer_profile_section.index', [
            'profile' => $this->fceerProfile,
        ]);
    }
}
