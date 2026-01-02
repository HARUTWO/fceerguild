<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords;

use Livewire\Component;

class HighschoolRecordsFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\HighschoolRecord::class;

    public array $data = [];
    public array $state = [];
    public array $fields = [];
    public array $options = [];

    protected $listeners = ['openCredentialsModal' => 'open'];

    protected string $sectionKey = 'highschool_records';

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'highschool_id', 'type' => 'select', 'label' => 'Highschool', 'options' => 'highschools', 'required' => true],
            ['key' => 'year_started', 'type' => 'number', 'label' => 'Year Started', 'required' => true],
            ['key' => 'level', 'type' => 'select', 'label' => 'Level', 'options' => ['junior' => 'Junior', 'senior' => 'Senior'], 'required' => true],
            ['key' => 'year_ended', 'type' => 'number', 'label' => 'Year Ended', 'required' => true],
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
        return view('livewire.profile.credentials.subsections.highschool_records.form-modal', [
            'fields' => $this->fields,
            'options' => $this->options,
            'state' => $this->state,
            'isOpen' => $this->isOpen,
            'itemId' => $this->itemId,
        ]);
    }
}
