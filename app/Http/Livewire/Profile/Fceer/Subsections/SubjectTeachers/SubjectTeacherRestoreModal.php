<?php
namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use Livewire\Component;
use Spatie\Activitylog\Models\Activity;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class SubjectTeacherRestoreModal extends Component
{
    use Toastable;
    public bool $open = false;
    public array $ids = [];
    public array $labels = [];
    public string $modelClass;

    protected $listeners = [
        'openSubjectTeacherRestore' => 'open',
        'confirmSubjectTeacherRestore' => 'confirmSubjectTeacherRestore',
    ];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass ?? \App\Models\SubjectTeacher::class;
    }

    public function open($ids = [])
    {
        $this->ids = is_array($ids) ? $ids : [$ids];
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
                    $subj = optional($m->volunteerSubject)->name ?? ($m->name ?? (string) ($m->id ?? ''));
                    $prof = trim((string) ($m->subject_proficiency ?? ''));
                    return trim($subj . ($prof !== '' ? ' — ' . $prof : ''));
                })->values()->all();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirmSubjectTeacherRestore($payload = [])
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

    public function close()
    {
        $this->open = false;
        $this->ids = [];
    }

    public function confirm()
    {
        $modelClass = $this->modelClass;

        if (empty($modelClass) || ! class_exists($modelClass)) {
            Log::error('SubjectTeacherRestoreModal: invalid modelClass', ['modelClass' => $modelClass]);
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
                $subj = optional($m->volunteerSubject)->name ?? ($m->name ?? (string) ($m->id ?? ''));
                $prof = trim((string) ($m->subject_proficiency ?? ''));
                return trim($subj . ($prof !== '' ? ' — ' . $prof : ''));
            })->values()->all();

            $this->labels = $labels;

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
            $this->dispatch('refreshSubjectTeachersArchive');
            $this->emit('refreshList');
            $this->emit('refreshSubjectTeachersArchive');
            $this->emit('refreshReferenceTable');
        } catch (\Throwable $e) {
            Log::error('SubjectTeacherRestoreModal: restore failed', ['error' => $e, 'ids' => $this->ids, 'model' => $modelClass]);
            $this->error('An error occurred while restoring items');
            throw $e;
        } finally {
            $this->close();
        }
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.subject_teachers.restore-modal');
    }
}
