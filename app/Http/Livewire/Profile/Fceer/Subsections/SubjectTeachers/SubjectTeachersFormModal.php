<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

class SubjectTeachersFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\SubjectTeacher::class;

    public array $data = [];
    public array $state = [];
    public array $fields = [];
    public array $options = [];
    public bool $hasDuplicate = false;
    public ?array $duplicate = null;

    protected $listeners = ['openCredentialsModal' => 'open'];

    protected string $sectionKey = 'subject_teachers';

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'volunteer_subject_id', 'type' => 'select', 'label' => 'Volunteer Subject', 'options' => 'volunteer_subjects', 'required' => true],
            ['key' => 'subject_proficiency', 'type' => 'select', 'label' => 'Proficiency', 'options' => ['Beginner' => 'Beginner', 'Competent' => 'Competent', 'Proficient' => 'Proficient'], 'required' => true],
        ];
    }

    public function open($params = [])
    {
        $instance = $params['instanceKey'] ?? null;
        if ($instance && $instance !== $this->modelClass) return;

        $this->reset(['itemId', 'data', 'state']);

        $this->itemId = $params['itemId'] ?? null;
        $this->data = $params['data'] ?? [];

        $this->fields = $this->getDefaultFields();

        $this->options = [];
        $resolver = app(\App\Services\ReferenceFieldOptionResolver::class);
        foreach ($this->fields as $f) {
            if (($f['type'] ?? '') === 'select' && isset($f['options'])) {
                if (is_string($f['options'])) {
                    $this->options[$f['key']] = $resolver->resolveFromReferenceTable($f['options']);
                } elseif (is_array($f['options'])) {
                    $this->options[$f['key']] = $f['options'];
                }
            }
        }

        $this->state = [];
        if ($this->itemId) {
            $item = $this->modelClass::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $this->state[$f['key']] = data_get($item, $f['key']);
                }
            }
        } else {
            foreach ($this->fields as $f) {
                $this->state[$f['key']] = $this->data[$f['key']] ?? null;
            }
        }

        if (isset($params['userId']) && ! isset($this->state['user_id'])) {
            $this->state['user_id'] = $params['userId'];
        }
        $this->isOpen = true;

        $this->checkDuplicate();
    }

    public function updatedStateVolunteerSubjectId($val)
    {
        $this->checkDuplicate();
    }

    public function updatedStateUserId($val)
    {
        $this->checkDuplicate();
    }

    protected function checkDuplicate(): void
    {
        $this->hasDuplicate = false;
        $this->duplicate = null;

        if (empty($this->state['volunteer_subject_id']) || empty($this->state['user_id'])) {
            return;
        }
        $existing = \App\Models\SubjectTeacher::withTrashed()
            ->where('user_id', $this->state['user_id'])
            ->where('volunteer_subject_id', $this->state['volunteer_subject_id'])
            ->first();

        if ($existing && (! $this->itemId || $existing->getKey() != $this->itemId)) {
            $this->hasDuplicate = true;
            $this->duplicate = [
                'id' => $existing->getKey(),
                'subject_proficiency' => $existing->subject_proficiency ?? null,
                'trashed' => method_exists($existing, 'trashed') ? $existing->trashed() : false,
            ];
        }
    }

    public function save()
    {
        $this->validate($this->rules());

        $this->checkDuplicate();
        if ($this->hasDuplicate) {
            $msg = 'You already have a subject assignment for this subject.';
            if (! empty($this->duplicate['trashed'])) {
                $msg .= ' (An archived record exists.)';
            }
            $this->addError('state.volunteer_subject_id', $msg);
            return;
        }

        $profileUser = null;
        if (! empty($this->state['user_id'])) {
            $profileUser = User::find($this->state['user_id']);
        }
        if ($profileUser && ! Gate::allows('manage', $profileUser)) {
            abort(403);
        }

        $original = [];
        if ($this->itemId) {
            $item = $this->modelClass::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $original[$f['key']] = data_get($item, $f['key']);
                }
            }
        }

        $changes = \App\Support\ChangeSetBuilder::from($this->fields, $original, $this->state);

        $subjectLabel = null;
        if (! empty($this->state['volunteer_subject_id'])) {
            $sid = $this->state['volunteer_subject_id'];
            $subjectLabel = $this->options['volunteer_subject_id'][$sid] ?? null;
        }
        $proficiency = $this->state['subject_proficiency'] ?? null;
        $displayLabel = trim(implode(' — ', array_filter([$subjectLabel, $proficiency])));

        $this->dispatch('confirmSubjectTeacherSave', [
            'id' => $this->itemId,
            'state' => $this->state,
            'changes' => $changes,
            'modelClass' => $this->modelClass,
            'label' => $displayLabel,
        ]);

        $this->isOpen = false;
    }

    public function rules(): array
    {
        $rules = [];
        $fields = ! empty($this->fields) ? $this->fields : $this->getDefaultFields();
        foreach ($fields as $f) {
            $k = $f['key'] ?? null;
            if (! $k) continue;

            if (! empty($f['rules'] ?? null)) {
                $rules['state.'.$k] = $f['rules'];
                continue;
            }

            $parts = [];
            $parts[] = ! empty($f['required']) ? 'required' : 'nullable';

            switch ($f['type'] ?? '') {
                case 'number':
                    $parts[] = 'integer';
                    break;
                case 'date':
                    $parts[] = 'date';
                    break;
                case 'checkbox':
                    $parts[] = 'boolean';
                    break;
            }

            $rules['state.'.$k] = implode('|', $parts);
        }

        return $rules;
    }

    public function setField($key, $value)
    {
        $this->state[$key] = $value;
        $this->checkDuplicate();
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.subject_teachers.form-modal', [
            'fields' => $this->fields,
            'options' => $this->options,
            'state' => $this->state,
            'isOpen' => $this->isOpen,
            'itemId' => $this->itemId,
            'hasDuplicate' => $this->hasDuplicate,
            'duplicate' => $this->duplicate,
        ]);
    }
}
