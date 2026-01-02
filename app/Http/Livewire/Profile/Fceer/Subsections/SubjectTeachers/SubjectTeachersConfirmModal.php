<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class SubjectTeachersConfirmModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?string $modelClass = null;
    public ?int $id = null;
    public array $state = [];
    public array $changes = [];

    protected $listeners = ['confirmSubjectTeacherSave' => 'confirmCredentialSave'];

    public function confirmCredentialSave($payload): void
    {
        $modelClass = $payload['modelClass'] ?? $this->modelClass;
        if ($modelClass !== \App\Models\SubjectTeacher::class && ($payload['instanceKey'] ?? null) !== \App\Models\SubjectTeacher::class) {
            return;
        }

        $this->modelClass = $modelClass;
        $this->id = $payload['id'] ?? null;
        $this->state = $payload['state'] ?? [];
        $this->changes = $payload['changes'] ?? [];
        $this->open = true;
    }

    public function confirm(): void
    {
        if (! auth()->check()) abort(403);

        $logger = app(ActivityLogger::class);
        $modelClass = $this->modelClass ?? \App\Models\SubjectTeacher::class;

        try {
            DB::transaction(function () use ($modelClass, $logger, &$m) {
                if ($this->id) {
                    $m = ($modelClass)::findOrFail($this->id);
                    $m->fill($this->state);
                    if (auth()->check()) $m->updated_by_id = auth()->id();
                    $m->save();
                    try { $logger->logCreateOrUpdate($m, 'updated', $this->changes, auth()->user()); } catch (\Throwable $e) { \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]); }
                } else {
                    $m = new ($modelClass)();
                    $m->fill($this->state);
                    if (auth()->check()) { $m->created_by_id = auth()->id(); $m->updated_by_id = auth()->id(); }
                    $m->save();
                    try { $logger->logCreateOrUpdate($m, 'created', $this->changes, auth()->user()); } catch (\Throwable $e) { \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]); }
                }
            });

            $this->emit('savedCredential');
            if (! empty($this->modelClass)) {
                $this->emit('savedCredential:'.$this->modelClass, ['id' => $m->getKey() ?? null]);
            }

            $label = $this->state['_label'] ?? ($this->state['label'] ?? null);
            if (! $label) {
                $subjectName = optional($m->volunteerSubject)->name ?? ($m->volunteer_subject_id ? 'Volunteer Subject #' . $m->volunteer_subject_id : null);
                $prof = trim((string) ($m->subject_proficiency ?? ''));
                $label = trim($subjectName . ($prof !== '' ? ' - ' . $prof : ''));
            }

            $this->success("Saved: {$label}");
            $this->open = false;
        } catch (\Throwable $e) {
            $this->error('An error occurred while saving.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.subject_teachers.confirm-changes-modal', [
            'changes' => $this->changes,
            'open' => $this->open,
        ]);
    }
}
