<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords;

use App\Models\User;
use Livewire\Component;

class HighschoolRecords extends Component
{
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    public bool $selectAll = false;
    public array $fields = [
        ['key' => 'highschool', 'label' => 'Highschool', 'type' => 'select'],
        ['key' => 'level', 'label' => 'Level', 'type' => 'text'],
        ['key' => 'year_started', 'label' => 'Year Started', 'type' => 'text'],
        ['key' => 'year_ended', 'label' => 'Year Ended', 'type' => 'text'],
    ];
    protected $listeners = [
        'savedCredential' => 'handleSavedCredential',
        'refreshList' => '$refresh',
        'refreshHighschoolArchive' => '$refresh',
        'refreshReferenceTable' => '$refresh',
    ];

    public function handleSavedCredential()
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->emit('refreshList');
    }

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
            'instanceKey' => 'App\\Models\\HighschoolRecord',
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = \App\Models\HighschoolRecord::where('user_id', $this->user->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
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
        if (!in_array($key, $this->selected)) {
            $this->selected[] = $key;
        }
    }

    public function relayEdit($itemId)
    {
        $this->emit('requestOpenProfileModal', [
            'instanceKey' => 'App\\Models\\HighschoolRecord',
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showHighschoolRecord', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->emit('openHighschoolDelete', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openHighschoolArchive');
    }

    public function deleteSelected()
    {
        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openHighschoolDelete', ['ids' => $ids]);
    }

    public function render()
    {
        $records = \App\Models\HighschoolRecord::where('user_id', $this->user->id)->orderBy('year_started', 'desc')->paginate(15);
        return view('livewire.profile.credentials.subsections.highschool_records.index', compact('records'));
    }
}
