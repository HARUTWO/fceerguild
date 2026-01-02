<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use App\Models\User;
use App\Models\ClassroomResponsibility;
use Livewire\Component;

class ClassroomResponsibilities extends Component
{
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    public bool $selectAll = false;

    protected $listeners = [
        'savedCredential' => 'handleSavedCredential',
        'refreshList' => '$refresh',
        'refreshClassroomResponsibilitiesArchive' => '$refresh',
        'refreshReferenceTable' => '$refresh',
    ];

    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
    }

    public function handleSavedCredential()
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function setView(string $view)
    {
        $this->view = $view;
    }

    public function create()
    {
        $this->authorize('manage', $this->user);

        $this->emit('openClassroomResponsibilitiesFormModal', [
            'instanceKey' => ClassroomResponsibility::class,
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = ClassroomResponsibility::where('user_id', $this->user->id)
                ->pluck('id')
                ->map(fn($id) => (string)$id)
                ->toArray();
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

        $this->emit('openClassroomResponsibilitiesFormModal', [
            'instanceKey' => ClassroomResponsibility::class,
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showClassroomResponsibilities', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->authorize('manage', $this->user);
        $this->emit('openClassroomResponsibilitiesDeleteModal', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openClassroomResponsibilitiesArchive');
    }

    public function deleteSelected()
    {
        $this->authorize('manage', $this->user);

        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openClassroomResponsibilitiesDeleteModal', ['ids' => $ids]);
    }

    public function render()
    {
        $records = ClassroomResponsibility::where('user_id', $this->user->id)
            ->with(['classroom', 'classroomPosition'])
            ->orderBy('id', 'desc')
            ->paginate(15);
            
        return view('livewire.profile.fceer.subsections.classroom_responsibilities.index', compact('records'));
    }
}
