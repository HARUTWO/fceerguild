<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords;

use Livewire\Component;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class HighschoolSubjectForceDeleteModal extends Component
{
    use Toastable;
    public string $modelClass;
    public array $ids = [];
    public array $labels = [];
    public bool $open = false;

    protected $listeners = ['openHighschoolSubjectForceDelete' => 'open', 'confirmHighschoolSubjectForceDelete' => 'confirmHighschoolSubjectForceDelete'];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass ?? \App\Models\HighschoolSubjectRecord::class;
    }

    public function open($ids = [])
    {
        $this->ids = is_array($ids) ? $ids : [$ids];
        $this->resolveLabels();
        $this->open = true;
    }

    public function confirmHighschoolSubjectForceDelete($payload = [])
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

    protected function resolveLabels(): void
    {
        $this->labels = [];
        $modelClass = $this->modelClass;
        try {
            $this->labels = $modelClass::withTrashed()->whereIn('id', $this->ids)
                ->get()
                ->map(function ($m) {
                    $subjectName = optional($m->subject)->name ?? ($m->name ?? (string) ($m->id ?? ''));
                    $grade = trim((string) ($m->grade ?? ''));
                    return trim($subjectName . ($grade ? ' - ' . ucfirst($grade) : ''), " -");
                })->toArray();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm(): void
    {
        if (! auth()->check() || ! Gate::allows('manageReferenceTables')) {
            abort(403);
        }

        $logger = app(ActivityLogger::class);
        $modelClass = $this->modelClass;

        try {
            $deleted = 0;
            foreach ($this->ids as $id) {
                $m = $modelClass::withTrashed()->find($id);
                if (! $m) continue;

                $m->forceDelete();
                try { $logger->logDelete($m, [], auth()->user()); } catch (\Throwable $e) { Log::debug('ActivityLogger::logDelete failed', ['e' => $e]); }
                $deleted++;
            }

            $this->dispatch('savedCredential');
            $this->dispatch('refreshHighschoolSubjectArchive');
            $this->emit('refreshList');
            $this->emit('refreshHighschoolSubjectArchive');
            $this->emit('refreshReferenceTable');

            if ($deleted === 1) {
                $label = $this->labels[0] ?? ("ID {$this->ids[0]}");
                $this->success("Deleted permanently: {$label}");
            } else {
                $preview = implode(', ', array_slice($this->labels, 0, 3));
                $more = count($this->labels) > 3 ? ' and ' . (count($this->labels) - 3) . ' more' : '';
                $this->success('Deleted permanently: ' . $preview . $more);
            }

            $this->open = false;
            $this->ids = [];
            $this->labels = [];
        } catch (\Throwable $e) {
            $this->error('An error occurred while deleting items permanently.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.highschool_subject_records.force-delete-modal');
    }
}
