<?php

namespace App\Http\Livewire\Profile\Account\Subsections\AccountInformation;

use Livewire\Component;

class AccountInformation extends Component
{
    public $user;
    public $view = 'table';

    public function mount($user)
    {
        $this->user = $user;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    public function render()
    {
        return view('livewire.profile.account.subsections.account_information.index');
    }
}
