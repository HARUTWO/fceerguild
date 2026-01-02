<?php

namespace App\Livewire\Attendance;

use App\Exports\AttendanceExport;
use App\Models\Committee;
use App\Models\Classroom;
use App\Models\CommitteePosition;
use App\Models\FceerBatch;
use App\Models\ReviewSeason;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

class ExportModal extends Component
{
    public bool $showModal = false;
    
    // Export type: 'students' or 'volunteers'
    public string $exportType = 'students';
    
    // Date range mode: 'review_season' or 'custom'
    public string $dateRangeMode = 'review_season';
    public ?int $reviewSeasonId = null;
    public ?string $customStartDate = null;
    public ?string $customEndDate = null;
    
    // Format: 'xlsx' or 'csv'
    public string $format = 'xlsx';
    
    // Filters
    public ?int $batchFilter = null;
    public ?int $committeeFilter = null;
    public ?int $positionFilter = null;
    public ?string $sessionFilter = null; // For students: 'am', 'pm', or null (both)
    
    // Data for dropdowns
    public $reviewSeasons = [];
    public $batches = [];
    public $committees = [];
    public $classrooms = [];
    public $positions = [];

    protected $listeners = [
        'openExportModal' => 'openModal',
    ];

    public function mount(): void
    {
        $this->loadDropdownData();
        
        // Set default review season to active one
        $activeSeason = ReviewSeason::getActive();
        if ($activeSeason) {
            $this->reviewSeasonId = $activeSeason->id;
        }
    }

    public function loadDropdownData(): void
    {
        $this->reviewSeasons = ReviewSeason::orderBy('start_date', 'desc')
            ->get();
        
        $this->batches = FceerBatch::orderBy('batch_no', 'desc')->get();
        $this->committees = Committee::orderBy('name')->get();
        $this->classrooms = Classroom::orderBy('name')->get();
        $this->positions = CommitteePosition::orderBy('name')->get();
    }

    public function openModal(string $type = 'students'): void
    {
        if (!Gate::allows('exportAttendance')) {
            return;
        }

        $this->exportType = $type;
        $this->resetFilters();
        $this->showModal = true;
    }

    public function closeModal(): void
    {
        $this->showModal = false;
        $this->resetFilters();
    }

    public function resetFilters(): void
    {
        $this->dateRangeMode = 'review_season';
        $this->customStartDate = null;
        $this->customEndDate = null;
        $this->batchFilter = null;
        $this->committeeFilter = null;
        $this->positionFilter = null;
        $this->sessionFilter = null;
        $this->format = 'xlsx';
        
        // Reset to active review season
        $activeSeason = ReviewSeason::getActive();
        $this->reviewSeasonId = $activeSeason?->id;
    }

    public function export()
    {
        if (!Gate::allows('exportAttendance')) {
            return;
        }

        // Validate
        if ($this->dateRangeMode === 'custom') {
            $this->validate([
                'customStartDate' => 'required|date',
                'customEndDate' => 'required|date|after_or_equal:customStartDate',
            ]);
        } elseif ($this->dateRangeMode === 'review_season') {
            $this->validate([
                'reviewSeasonId' => 'required|exists:review_seasons,id',
            ]);
        }

        // Build filters array
        $filters = [];
        if ($this->batchFilter) {
            $filters['batch_id'] = $this->batchFilter;
        }
        if ($this->committeeFilter) {
            $filters['committee_id'] = $this->committeeFilter;
        }
        if ($this->positionFilter) {
            $filters['position_id'] = $this->positionFilter;
        }

        // Build export
        $export = new AttendanceExport(
            type: $this->exportType,
            startDate: $this->dateRangeMode === 'custom' ? $this->customStartDate : null,
            endDate: $this->dateRangeMode === 'custom' ? $this->customEndDate : null,
            reviewSeasonId: $this->dateRangeMode === 'review_season' ? $this->reviewSeasonId : null,
            filters: $filters,
            session: $this->exportType === 'students' ? $this->sessionFilter : null,
            meta: [
                'exported_at' => now()->format('Y-m-d H:i:s'),
                'exported_by' => Auth::user()?->name ?? 'System',
            ]
        );

        // Generate filename
        $typeLabel = ucfirst($this->exportType);
        $timestamp = now()->format('Y-m-d_His');
        $filename = "Attendance_{$typeLabel}_{$timestamp}";

        $this->closeModal();

        // Return download
        if ($this->format === 'csv') {
            return Excel::download($export, "{$filename}.csv", \Maatwebsite\Excel\Excel::CSV);
        }

        return Excel::download($export, "{$filename}.xlsx", \Maatwebsite\Excel\Excel::XLSX);
    }

    public function render()
    {
        return view('livewire.attendance.export-modal');
    }
}
