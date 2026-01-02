<?php
namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use Livewire\Component;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class SubjectTeacherDeleteModal extends Component
{
    use Toastable;

    public string $modelClass;
    public array $ids = [];
    public array $labels = [];
    public bool $open = false;

    protected $listeners = ['openSubjectTeacherDelete' => 'deleteSubjectTeacher', 'confirmSubjectTeacherDelete' => 'confirm'];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass ?? \App\Models\SubjectTeacher::class;
    }

    public function deleteSubjectTeacher($payload): void
    {
        if (is_array($payload) && isset($payload['ids'])) {
            $this->ids = array_map('strval', (array) $payload['ids']);
        } else {
            $this->ids = (array) array_map('strval', (array) $payload);
        }

        $this->resolveLabels();
        $this->open = true;
    }

    protected function resolveLabels(): void
    {
        $this->labels = [];
        $modelClass = $this->modelClass;
        try {
            $this->labels = $modelClass::whereIn('id', $this->ids)
                ->get()
                ->map(function ($m) {
                    $subj = optional($m->volunteerSubject)->name ?? ($m->name ?? (string) ($m->id ?? ''));
                    $prof = trim((string) ($m->subject_proficiency ?? ''));
                    return trim($subj . ($prof !== '' ? ' — ' . $prof : ''));
                })->toArray();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm(): void
    {
        if (! auth()->check()) {
            abort(403);
        }

        $logger = app(ActivityLogger::class);
        $modelClass = $this->modelClass;

        try {
            $deleted = 0;
            foreach ($this->ids as $id) {
                $m = $modelClass::find($id);
                if (! $m) continue;

                $attributes = $m->getAttributes();
                $m->delete();

                try { $logger->logDelete($m, $attributes, auth()->user()); } catch (\Throwable $e) { \Log::debug('ActivityLogger::logDelete failed', ['e' => $e]); }
                $deleted++;
            }

            $this->dispatch('savedCredential');
            $this->dispatch('refreshSubjectTeachersArchive');
            $this->emit('refreshList');
            $this->emit('refreshSubjectTeachersArchive');
            $this->emit('refreshReferenceTable');

            if ($deleted === 1) {
                $label = $this->labels[0] ?? ("ID {$this->ids[0]}");
                $this->success("Deleted: {$label}");
            } else {
                $preview = implode(', ', array_slice($this->labels, 0, 3));
                $more = count($this->labels) > 3 ? ' and ' . (count($this->labels) - 3) . ' more' : '';
                $this->success('Deleted ' . $deleted . ' records: ' . $preview . $more);
            }

            $this->open = false;
            $this->ids = [];
            $this->labels = [];
        } catch (\Throwable $e) {
            $this->error('An error occurred while deleting the record(s).');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.subject_teachers.delete-modal');
    }
}
