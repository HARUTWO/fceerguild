<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords;

use App\Models\User;
use Livewire\Component;

class HighschoolSubjectRecords extends Component
{
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    public bool $selectAll = false;
    public array $fields = [
        ['key' => 'subject', 'label' => 'Subject', 'type' => 'select'],
        ['key' => 'grade', 'label' => 'Grade', 'type' => 'text'],
    ];

    protected $listeners = [
        'savedCredential' => 'handleSavedCredential',
        'refreshList' => '$refresh',
        'refreshHighschoolSubjectArchive' => '$refresh',
        'refreshReferenceTable' => '$refresh',
        'highschoolSubjectDeleted' => 'handleSavedCredential',
    ];

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
            'instanceKey' => 'App\\Models\\HighschoolSubjectRecord',
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = \App\Models\HighschoolSubjectRecord::where('user_id', $this->user->id)->pluck('id')->map(fn($id) => (string) $id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function toggleRow($key)
    {
        if (in_array($key, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$key]));
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
            'instanceKey' => 'App\\Models\\HighschoolSubjectRecord',
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showHighschoolSubjectRecord', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->emit('openHighschoolSubjectDelete', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openHighschoolSubjectArchive');
    }

    public function deleteSelected()
    {
        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openHighschoolSubjectDelete', ['ids' => $ids]);
    }

    public function handleSavedCredential()
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->emit('refreshList');
    }

    public function render()
    {
        $items = \App\Models\HighschoolSubjectRecord::with('subject')->where('user_id', $this->user->id)->orderBy('id', 'desc')->paginate(15);
        return view('livewire.profile.credentials.subsections.highschool_subject_records.index', compact('items'));
    }
}
