<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ClassroomResponsibility;
use Illuminate\Support\Facades\Gate;
use Masmerise\Toaster\Toastable;
use App\Http\Livewire\Traits\SelectRows;

class ClassroomResponsibilitiesArchive extends Component
{
    use WithPagination;
    use SelectRows;
    use Toastable;

    public bool $open = false;
    public array $selected = [];
    public bool $selectAll = false;
    public string $modelClass = ClassroomResponsibility::class;
    public int $perPage = 15;

    protected $listeners = [
        'openClassroomResponsibilitiesArchive' => 'open',
        'refreshClassroomResponsibilitiesArchive' => 'handleRefresh',
    ];

    protected function getSelectablePageIds(): array
    {
        return ($this->modelClass)::onlyTrashed()
            ->paginate($this->perPage)
            ->pluck('id')
            ->map(fn($id) => (string) $id)
            ->toArray();
    }

    public function open()
    {
        $this->open = true;
        $this->clearSelection();
        $this->dispatch('savedCredential');
        $this->resetPage();
    }

    public function close()
    {
        $this->open = false;
    }

    public function restore($id)
    {
        $this->emit('confirmClassroomResponsibilitiesRestore', [
            'ids' => [$id],
            'modelClass' => $this->modelClass,
        ]);

        $this->open = false;
    }

    public function forceDelete($id)
    {
        $this->emit('confirmClassroomResponsibilitiesForceDelete', [
            'ids' => [$id],
            'modelClass' => $this->modelClass,
        ]);

        $this->open = false;
    }

    public function prepareBulkAction(string $action)
    {
        if ($action === 'restoreSelected') {
            $this->emit('confirmClassroomResponsibilitiesRestore', [
                'ids' => $this->selected,
                'modelClass' => $this->modelClass,
            ]);
        } elseif ($action === 'forceDeleteSelected') {
            $this->emit('confirmClassroomResponsibilitiesForceDelete', [
                'ids' => $this->selected,
                'modelClass' => $this->modelClass,
            ]);
        }

        $this->open = false;
    }

    public function render()
    {
        $items = ($this->modelClass)::onlyTrashed()
            ->with('deletedBy', 'classroom', 'classroomPosition')
            ->paginate($this->perPage);

        return view('livewire.profile.fceer.subsections.classroom_responsibilities.archive-modal', compact('items'));
    }
}
