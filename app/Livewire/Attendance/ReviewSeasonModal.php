<?php

namespace App\Livewire\Attendance;

use App\Models\FceerBatch;
use App\Models\ReviewSeason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ReviewSeasonModal extends Component
{
    public bool $showModal = false;
    
    public string $mode = 'list';
    
    // Form fields
    public ?int $editingSeasonId = null;
    public int $startMonth = 1;
    public int $startYear;
    public int $endMonth = 12;
    public int $endYear;
    public array $linkedBatchIds = [];
    
    // Data
    public $seasons = [];
    public $batches = [];

    protected $listeners = [
        'openReviewSeasonModal' => 'openModal',
    ];

    protected $rules = [
        'startMonth' => 'required|integer|between:1,12',
        'startYear' => 'required|integer|min:2000|max:2100',
        'endMonth' => 'required|integer|between:1,12',
        'endYear' => 'required|integer|min:2000|max:2100',
    ];

    protected $messages = [
        'startMonth.required' => 'Start month is required.',
        'startYear.required' => 'Start year is required.',
        'endMonth.required' => 'End month is required.',
        'endYear.required' => 'End year is required.',
    ];

    public function mount(): void
    {
        $this->startYear = now()->year;
        $this->endYear = now()->year;
        $this->loadData();
    }

    public function loadData(): void
    {
        $this->seasons = ReviewSeason::with('fceerBatches', 'setBy')
            ->orderBy('is_active', 'desc')
            ->orderBy('start_date', 'desc')
            ->get();
        
        $this->batches = FceerBatch::orderBy('batch_no', 'desc')->get();
    }

    public function openModal(): void
    {
        if (!Gate::allows('manageReviewSeason')) {
            return;
        }

        $this->loadData();
        $this->mode = 'list';
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetForm();
    }

    public function resetForm(): void
    {
        $this->mode = 'list';
        $this->editingSeasonId = null;
        $this->startMonth = 1;
        $this->startYear = now()->year;
        $this->endMonth = 12;
        $this->endYear = now()->year;
        $this->linkedBatchIds = [];
        $this->resetValidation();
    }

    public function showCreateForm(): void
    {
        $this->resetForm();
        $this->mode = 'create';
    }

    public function showEditForm(int $seasonId): void
    {
        $season = ReviewSeason::with('fceerBatches')->find($seasonId);
        if (!$season) {
            return;
        }

        $this->editingSeasonId = $season->id;
        $startDate = Carbon::parse($season->start_date);
        $endDate = Carbon::parse($season->end_date);
        $this->startMonth = $startDate->month;
        $this->startYear = $startDate->year;
        $this->endMonth = $endDate->month;
        $this->endYear = $endDate->year;
        $this->linkedBatchIds = $season->fceerBatches->pluck('id')->toArray();
        $this->mode = 'edit';
    }

    public function backToList(): void
    {
        $this->resetForm();
        $this->loadData();
    }

    public function save(): void
    {
        if (!Gate::allows('manageReviewSeason')) {
            return;
        }

        $this->validate();

        // Validate date range
        $startDate = \Carbon\Carbon::create($this->startYear, $this->startMonth, 1);
        $endDate = \Carbon\Carbon::create($this->endYear, $this->endMonth, 1)->endOfMonth();

        if ($endDate->lt($startDate)) {
            $this->addError('endMonth', 'End date must be after start date.');
            return;
        }

        $data = [
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ];

        if ($this->mode === 'create') {
            $data['created_by_id'] = Auth::id();
            $season = ReviewSeason::create($data);
        } else {
            $season = ReviewSeason::find($this->editingSeasonId);
            if (!$season) {
                return;
            }
            $data['updated_by_id'] = Auth::id();
            $season->update($data);
        }

        // Update linked batches
        FceerBatch::where('review_season_id', $season->id)->update(['review_season_id' => null]);
        if (!empty($this->linkedBatchIds)) {
            FceerBatch::whereIn('id', $this->linkedBatchIds)->update(['review_season_id' => $season->id]);
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => $this->mode === 'create' ? 'Review season created successfully.' : 'Review season updated successfully.',
        ]);

        $this->backToList();
    }

    public function setAsActive(int $seasonId): void
    {
        if (!Gate::allows('manageReviewSeason')) {
            return;
        }

        $season = ReviewSeason::find($seasonId);
        if (!$season) {
            return;
        }

        $season->setAsActive(Auth::id());

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Review season \"{$season->range_label}\" is now active.",
        ]);

        $this->dispatch('reviewSeasonUpdated');
        $this->loadData();
    }

    public function deleteSeason(int $seasonId): void
    {
        if (!Gate::allows('manageReviewSeason')) {
            return;
        }

        $season = ReviewSeason::find($seasonId);
        if (!$season) {
            return;
        }

        // Don't allow deleting active season
        if ($season->is_active) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Cannot delete the active review season. Set another season as active first.',
            ]);
            return;
        }

        // Unlink batches
        FceerBatch::where('review_season_id', $season->id)->update(['review_season_id' => null]);

        $season->delete();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Review season deleted.',
        ]);

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.attendance.review-season-modal');
    }
}
