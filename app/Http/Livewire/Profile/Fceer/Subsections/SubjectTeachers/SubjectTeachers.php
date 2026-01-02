<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use App\Models\User;
use Livewire\Component;

class SubjectTeachers extends Component
{
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    public bool $selectAll = false;
    public array $fields = [
        ['key' => 'volunteer_subject', 'label' => 'Volunteer Subject', 'type' => 'select'],
        ['key' => 'subject_proficiency', 'label' => 'Proficiency', 'type' => 'text'],
    ];

    protected $listeners = [
        'savedCredential' => 'handleSavedCredential',
        'refreshList' => '$refresh',
        'refreshSubjectTeachersArchive' => '$refresh',
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
        $this->authorize('manage', $this->user);

        $this->emit('requestOpenProfileModal', [
            'instanceKey' => \App\Models\SubjectTeacher::class,
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = \App\Models\SubjectTeacher::where('user_id', $this->user->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
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
        $this->authorize('manage', $this->user);

        $this->emit('requestOpenProfileModal', [
            'instanceKey' => \App\Models\SubjectTeacher::class,
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showSubjectTeacher', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->authorize('manage', $this->user);
        $this->emit('openSubjectTeacherDelete', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openSubjectTeachersArchive');
    }

    public function deleteSelected()
    {
        $this->authorize('manage', $this->user);

        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openSubjectTeacherDelete', ['ids' => $ids]);
    }

    public function render()
    {
        $records = \App\Models\SubjectTeacher::where('user_id', $this->user->id)->orderBy('id', 'desc')->paginate(15);
        return view('livewire.profile.fceer.subsections.subject_teachers.index', compact('records'));
    }
}
