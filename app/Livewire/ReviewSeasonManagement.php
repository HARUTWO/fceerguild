<?php

namespace App\Livewire;

use App\Models\ReviewSeason;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ReviewSeasonManagement extends Component
{
    use WithPagination;

    public string $mode = 'list'; // list, create, edit
    public ?int $editingId = null;

    // Form fields
    public ?string $name = null;
    public ?string $start_date = null;
    public ?string $end_date = null;
    public bool $is_active = false;

    // Modals
    public bool $showDeleteConfirm = false;
    public ?int $deleteId = null;

    protected $listeners = [
        'reviewSeasonUpdated' => '$refresh',
    ];

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ];
    }

    protected function messages()
    {
        return [
            'name.required' => 'Season name is required',
            'start_date.required' => 'Start date is required',
            'end_date.required' => 'End date is required',
            'end_date.after' => 'End date must be after start date',
        ];
    }

    public function mount(): void
    {
        // Can be extended with initialization logic
    }

    public function create(): void
    {
        $this->mode = 'create';
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $season = ReviewSeason::findOrFail($id);
        $this->editingId = $id;
        $this->mode = 'edit';
        $this->name = $season->name;
        $this->start_date = $season->start_date ? $season->start_date->format('Y-m-d') : null;
        $this->end_date = $season->end_date ? $season->end_date->format('Y-m-d') : null;
        $this->is_active = $season->is_active;
    }

    public function save(): void
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date,
            'is_active' => $this->is_active,
        ];

        if ($this->mode === 'create') {
            ReviewSeason::create(array_merge($data, [
                'created_by_id' => Auth::id(),
            ]));
            session()->flash('message', 'Review season created successfully.');
        } else {
            $season = ReviewSeason::findOrFail($this->editingId);
            $season->update(array_merge($data, [
                'updated_by_id' => Auth::id(),
            ]));
            session()->flash('message', 'Review season updated successfully.');
        }

        $this->dispatch('reviewSeasonUpdated');
        $this->cancelForm();
    }

    public function cancelForm(): void
    {
        $this->mode = 'list';
        $this->resetForm();
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
        $this->showDeleteConfirm = true;
    }

    public function delete(): void
    {
        if ($this->deleteId) {
            $season = ReviewSeason::findOrFail($this->deleteId);
            $season->delete();
            session()->flash('message', 'Review season deleted successfully.');
            $this->dispatch('reviewSeasonUpdated');
        }
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function cancelDelete(): void
    {
        $this->showDeleteConfirm = false;
        $this->deleteId = null;
    }

    public function toggleActive(int $id): void
    {
        $season = ReviewSeason::findOrFail($id);
        
        // If activating this season, deactivate all others
        if (!$season->is_active) {
            ReviewSeason::where('is_active', true)->update(['is_active' => false]);
        }
        
        $season->update([
            'is_active' => !$season->is_active,
            'updated_by_id' => Auth::id(),
        ]);
        
        $this->dispatch('reviewSeasonUpdated');
        session()->flash('message', 'Review season status updated.');
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name = null;
        $this->start_date = null;
        $this->end_date = null;
        $this->is_active = false;
        $this->resetValidation();
    }

    public function render()
    {
        $seasons = ReviewSeason::with(['createdBy', 'updatedBy'])
            ->orderBy('start_date', 'desc')
            ->paginate(10);

        return view('livewire.review-season-management', [
            'seasons' => $seasons,
        ]);
    }
}
