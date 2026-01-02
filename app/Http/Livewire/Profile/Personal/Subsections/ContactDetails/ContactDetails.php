<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\ContactDetails;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ContactDetails extends Component
{
    public User $user;
    public $profile;
    public string $view = 'table';

    protected $listeners = ['refreshContactDetails' => 'loadProfile'];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->loadProfile();
    }

    public function loadProfile()
    {
        $this->profile = $this->user->profile;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function edit()
    {
        if (!Gate::allows('managePersonal', $this->user)) {
            return;
        }

        $config = config('profile.personal.sections.contact_details');
        $fields = [];

        foreach ($config['fields'] as $key => $field) {
            $fields[] = array_merge(['key' => $key], $field);
        }

        $this->emit('openContactDetailsModal', [
            'instanceKey' => $config['model'],
            'modelClass' => $config['model'],
            'fields' => $fields,
            'userId' => $this->user->id,
            'itemId' => $this->profile->id ?? null,
        ]);
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.contact_details.index');
    }
}
