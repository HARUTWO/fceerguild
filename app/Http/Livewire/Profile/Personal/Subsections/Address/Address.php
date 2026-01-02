<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\Address;

use Livewire\Component;

class Address extends Component
{
    public $user;
    public $profile;
    public $address;
    public $view = 'table';

    protected $listeners = ['refreshAddress' => 'loadAddress'];

    public function mount($user)
    {
        $this->user = $user;
        $this->loadAddress();
    }

    public function loadAddress()
    {
        $this->profile = $this->user->profile;
        $this->address = optional($this->profile)->address;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function edit()
    {
        $config = config('profile.personal.sections.address');
        $fields = [];
        
        foreach ($config['fields'] ?? [] as $key => $field) {
            $fields[] = array_merge(['key' => $key], $field);
        }

        $this->dispatch('openAddressFormModal', [
            'id' => optional($this->address)->id,
            'userId' => $this->user->id,
            'fields' => $fields,
            'modelClass' => \App\Models\Address::class,
        ]);
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.address.index');
    }
}
