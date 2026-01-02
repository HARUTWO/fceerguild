<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials;

use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class ProfessionalCredentials extends Component
{
    use WithPagination;
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    protected $listeners = [
        'savedCredential' => '$refresh',
        'refreshList' => '$refresh',
    ];

    public bool $selectAll = false;

    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
    }

    public function setView(string $view)
    {
        $this->view = $view;
    }

    public function create()
    {
        $this->emit('requestOpenProfileModal', [
            'instanceKey' => 'App\\Models\\ProfessionalCredential',
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = \App\Models\ProfessionalCredential::where('user_id', $this->user->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function toggleRow($key)
    {
        if (in_array($key, $this->selected)) {
            $this->selected = array_diff($this->selected, [$key]);
        } else {
            $this->selected[] = $key;
        }
    }

    public function selectEnsure($key)
    {
        if (! in_array($key, $this->selected)) {
            $this->selected[] = $key;
        }
    }

    public function relayEdit($itemId)
    {
        $this->emit('requestOpenProfileModal', [
            'instanceKey' => 'App\\Models\\ProfessionalCredential',
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showProfessionalCredential', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->emit('openProfessionalDelete', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openProfessionalArchive');
    }

    public function deleteSelected()
    {
        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openProfessionalDelete', ['ids' => $ids]);
    }

    public function render()
    {
        $items = \App\Models\ProfessionalCredential::where('user_id', $this->user->id)->orderBy('issued_on','desc')->paginate(10);
        return view('livewire.profile.credentials.subsections.professional_credentials.index_with_modals', compact('items'));
    }
}
