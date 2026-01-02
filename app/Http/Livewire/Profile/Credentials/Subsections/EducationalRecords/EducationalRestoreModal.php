<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Gate;
use Masmerise\Toaster\Toastable;

class EducationalRestoreModal extends Component
{
    use WithPagination;
    use Toastable;

    public bool $open = false;
    public array $selected = [];
    public bool $selectAll = false;
    public array $ids = [];
    public array $labels = [];
    public string $modelClass = \App\Models\EducationalRecord::class;
    public int $perPage = 15;

    protected $listeners = [
        'openEducationalRestore' => 'open',
        'confirmEducationalRestore' => 'confirmEducationalRestore',
        'refreshEducationalArchive' => 'handleRefresh',
    ];

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
        $this->emit('confirmEducationalRestore', [
            'ids' => [$id],
            'modelClass' => $this->modelClass,
        ]);

        $this->open = false;
    }

    protected function resolveLabels(): void
    {
        $this->labels = [];
        $modelClass = $this->modelClass;
        try {
            $this->labels = $modelClass::withTrashed()->whereIn('id', $this->ids)
                ->get()
                ->map(function ($m) {
                    $prog = optional($m->degreeProgram)->name ?? ($m->degree_program_id ?? '');
                    $uni = optional($m->university)->name ?? ($m->university_id ?? '');
                    return trim($prog . ' - ' . $uni, " -");
                })->values()->all();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirmEducationalRestore($payload = [])
    {
        if (is_array($payload) && isset($payload['ids'])) {
            $this->ids = is_array($payload['ids']) ? $payload['ids'] : [$payload['ids']];
            if (isset($payload['modelClass'])) $this->modelClass = $payload['modelClass'];
        } else {
            $this->ids = is_array($payload) ? $payload : [$payload];
        }

        $this->resolveLabels();

        $this->open = true;
    }

    public function confirm()
    {
        $modelClass = $this->modelClass;

        if (empty($modelClass) || ! class_exists($modelClass)) {
            $this->dispatch('toast', 'Unable to restore: invalid model');
            $this->close();
            return;
        }

        if (! auth()->check() || ! \Illuminate\Support\Facades\Gate::allows('manageReferenceTables')) {
            abort(403);
        }

        try {
            $models = $modelClass::withTrashed()->whereIn('id', $this->ids)->get();

            foreach ($models as $m) {
                $m->restore();
                if (function_exists('activity')) activity()->performedOn($m)->causedBy(auth()->user())->log('restored');
            }

            $labels = $models->map(function ($m) {
                $prog = optional($m->degreeProgram)->name ?? ($m->degree_program_id ?? '');
                $uni = optional($m->university)->name ?? ($m->university_id ?? '');
                return trim($prog . ' - ' . $uni, " -");
            })->values()->all();

            $count = count($labels);
            if ($count === 1) {
                $this->success('Restored: ' . $labels[0]);
            } else {
                $preview = implode(', ', array_slice($labels, 0, 3));
                $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
                $this->success('Restored ' . $count . ' items: ' . $preview . $more);
            }

            $this->dispatch('savedCredential');
            $this->dispatch('refreshReferenceTable');
            $this->dispatch('refreshEducationalArchive');
            $this->emit('refreshList');
            $this->emit('refreshEducationalArchive');
            $this->emit('refreshReferenceTable');
        } catch (\Throwable $e) {
            $this->error('An error occurred while restoring items');
            throw $e;
        } finally {
            $this->close();
        }
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.educational_records.restore-modal');
    }

    public function handleRefresh(): void
    {
        $this->clearSelection();
        $this->resetPage();
    }

    protected function clearSelection()
    {
        $this->selected = [];
        $this->selectAll = false;
    }
}
