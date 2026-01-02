<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords;

use Livewire\Component;

class EducationalRecordsFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\EducationalRecord::class;

    public array $data = [];
    public array $state = [];
    public array $fields = [];
    public array $options = [];

    protected $listeners = ['openCredentialsModal' => 'open'];

    protected string $sectionKey = 'educational_records';

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'degree_program_id', 'type' => 'select', 'label' => 'Degree Program', 'options' => 'degree_programs', 'required' => true],
            ['key' => 'university_id', 'type' => 'select', 'label' => 'University', 'options' => 'universities', 'required' => true],
            ['key' => 'year_started', 'type' => 'number', 'label' => 'Year Started', 'required' => true],
            ['key' => 'year_graduated', 'type' => 'number', 'label' => 'Year Graduated', 'required' => true],
            ['key' => 'dost_scholarship', 'type' => 'checkbox', 'label' => 'DOST Scholarship'],
            ['key' => 'latin_honor', 'type' => 'select', 'label' => 'Latin Honor', 'options' => ['summa cum laude' => 'Summa Cum Laude','magna cum laude' => 'Magna Cum Laude','cum laude' => 'Cum Laude']],
        ];
    }

    public function open($params = [])
    {
        $instance = $params['instanceKey'] ?? null;
        if ($instance && $instance !== $this->modelClass) {
            return;
        }

        $this->itemId = $params['itemId'] ?? null;
        $this->data = $params['data'] ?? [];
        $this->fields = $this->getDefaultFields();
        $this->options = [];
        foreach ($this->fields as $f) {
            if (($f['type'] ?? '') !== 'select') continue;
            $key = $f['key'];

            if ($key === 'degree_program_id') {
                try {
                    $this->options[$key] = \App\Models\DegreeProgram::orderBy('name')->pluck('name', 'id')->toArray();
                } catch (\Throwable $e) {
                    $this->options[$key] = [];
                }
                continue;
            }

            if ($key === 'university_id') {
                try {
                    $this->options[$key] = \App\Models\University::orderBy('name')->pluck('name', 'id')->toArray();
                } catch (\Throwable $e) {
                    $this->options[$key] = [];
                }
                continue;
            }

            if (is_array($f['options'] ?? null)) {
                $this->options[$key] = $f['options'];
            } else {
                $this->options[$key] = [];
            }
        }

        // Populate state from model or provided data
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
    }

    public function save()
    {
        $this->validate($this->rules());

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

        $this->dispatch('confirmCredentialSave', [
            'id' => $this->itemId,
            'state' => $this->state,
            'changes' => $changes,
            'modelClass' => $this->modelClass,
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
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.educational_records.form-modal', [
            'fields' => $this->fields,
            'options' => $this->options,
            'state' => $this->state,
            'isOpen' => $this->isOpen,
            'itemId' => $this->itemId,
        ]);
    }
}
