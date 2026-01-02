<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class HighschoolSubjectRestoreModal extends Component
{
    use Toastable;
    public bool $open = false;
    public array $ids = [];
    public array $labels = [];
    public string $modelClass;

    protected $listeners = [
        'openHighschoolSubjectRestore' => 'open',
        'confirmHighschoolSubjectRestore' => 'confirmHighschoolSubjectRestore',
    ];

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

    public function confirmHighschoolSubjectRestore($payload = [])
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
                })->values()->all();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function close()
    {
        $this->open = false;
        $this->ids = [];
        $this->labels = [];
    }

    public function confirm()
    {
        $modelClass = $this->modelClass;

        if (empty($modelClass) || ! class_exists($modelClass)) {
            Log::error('HighschoolSubjectRestoreModal: invalid modelClass', ['modelClass' => $modelClass]);
            $this->dispatch('toast', 'Unable to restore: invalid model');
            $this->close();
            return;
        }

        if (! auth()->check() || ! Gate::allows('manageReferenceTables')) {
            abort(403);
        }

        try {
            $models = $modelClass::withTrashed()->whereIn('id', $this->ids)->get();

            $logger = app(ActivityLogger::class);

            foreach ($models as $m) {
                $m->restore();

                if ($logger && method_exists($logger, 'logRestore')) {
                    try { $logger->logRestore($m, auth()->user()); } catch (\Throwable $e) { Log::debug('ActivityLogger::logRestore failed', ['e' => $e]); }
                } else {
                    if (function_exists('activity')) {
                        try { activity()->performedOn($m)->causedBy(auth()->user())->log('restored'); } catch (\Throwable $e) { Log::debug('activity() log failed', ['e' => $e]); }
                    }
                }
            }

            $labels = $models->map(function ($m) {
                $subjectName = optional($m->subject)->name ?? ($m->name ?? (string) ($m->id ?? ''));
                $grade = trim((string) ($m->grade ?? ''));
                return trim($subjectName . ($grade ? ' - ' . ucfirst($grade) : ''), " -");
            })->values()->all();

            $count = count($labels);
            if ($count === 1) {
                $this->success('Restored: ' . $labels[0]);
            } else {
                $preview = implode(', ', array_slice($labels, 0, 3));
                $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
                $this->success('Restored ' . $count . ' items: ' . $preview . $more);
            }
            $this->dispatch('refreshReferenceTable');
            $this->dispatch('refreshHighschoolSubjectArchive');
            $this->emit('refreshList');
            $this->emit('refreshHighschoolSubjectArchive');
            $this->emit('refreshReferenceTable');
        } catch (\Throwable $e) {
            Log::error('HighschoolSubjectRestoreModal: restore failed', ['error' => $e, 'ids' => $this->ids, 'model' => $modelClass]);
            $this->error('An error occurred while restoring items');
            throw $e;
        } finally {
            $this->close();
        }
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.highschool_subject_records.restore-modal');
    }
}
