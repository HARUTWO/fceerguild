<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials;

use Livewire\Component;

class ProfessionalCredentialsFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\ProfessionalCredential::class;

    public array $data = [];
    public array $state = [];
    public array $fields = [];
    public array $options = [];

    protected $listeners = ['openCredentialsModal' => 'open'];

    protected string $sectionKey = 'professional_credentials';

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'field_of_work_id', 'type' => 'select', 'label' => 'Field of Work', 'options' => 'fields_of_work', 'required' => true],
            ['key' => 'prefix_id', 'type' => 'select', 'label' => 'Prefix', 'options' => 'prefix_titles'],
            ['key' => 'suffix_id', 'type' => 'select', 'label' => 'Suffix', 'options' => 'suffix_titles'],
            ['key' => 'issued_on', 'type' => 'number', 'label' => 'Issued On', 'required' => true],
            ['key' => 'notes', 'type' => 'textarea', 'label' => 'Notes'],
        ];
    }

    public function open($params = [])
    {
        $instance = $params['instanceKey'] ?? null;
        if ($instance && $instance !== $this->modelClass) return;

        $this->itemId = $params['itemId'] ?? null;
        $this->data = $params['data'] ?? [];

        $this->fields = $this->getDefaultFields();

        $resolver = app(\App\Services\ReferenceFieldOptionResolver::class);
        $this->options = [];
        foreach ($this->fields as $f) {
            if (($f['type'] ?? '') === 'select' && isset($f['options'])) {
                if (in_array($f['key'], ['prefix_id', 'suffix_id'], true)) {
                    continue;
                }

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

        $this->refreshDependentOptions($this->state['field_of_work_id'] ?? null);

        $this->isOpen = true;
    }

    public function updated($name, $value)
    {
        if ($name === 'state.field_of_work_id') {
            $this->refreshDependentOptions($value);
        }
    }

    protected function refreshDependentOptions($fieldOfWorkId)
    {
        if ($fieldOfWorkId) {
            try {
                $query = \App\Models\PrefixTitle::where('field_of_work_id', $fieldOfWorkId);
                try { $query = $query->orderBy('name', 'asc'); } catch (\Throwable $_) {}
                $this->options['prefix_id'] = $query->get()->mapWithKeys(function($r){
                    $base = $r->name ?? null;
                    $label = $base ? ($r->abbreviation ? $base . ' (' . $r->abbreviation . ')' : $base) : ($r->abbreviation ?: $r->id);
                    return [$r->id => $label];
                })->toArray();
            } catch (\Throwable $e) {
                $this->options['prefix_id'] = $this->options['prefix_id'] ?? [];
            }

            try {
                $q2 = \App\Models\SuffixTitle::where('field_of_work_id', $fieldOfWorkId);
                try { $q2 = $q2->orderBy('name', 'asc'); } catch (\Throwable $_) {}
                $this->options['suffix_id'] = $q2->get()->mapWithKeys(function($r){
                    $base = $r->name ?? null;
                    $label = $base ? ($r->abbreviation ? $base . ' (' . $r->abbreviation . ')' : $base) : ($r->abbreviation ?: $r->id);
                    return [$r->id => $label];
                })->toArray();
            } catch (\Throwable $e) {
                $this->options['suffix_id'] = $this->options['suffix_id'] ?? [];
            }
        } else {
            try {
                $q = \App\Models\PrefixTitle::query();
                try { $q = $q->orderBy('name', 'asc'); } catch (\Throwable $_) {}
                $this->options['prefix_id'] = $q->get()->mapWithKeys(function($r){
                    $base = $r->name ?? null;
                    $label = $base ? ($r->abbreviation ? $base . ' (' . $r->abbreviation . ')' : $base) : ($r->abbreviation ?: $r->id);
                    return [$r->id => $label];
                })->toArray();
            } catch (\Throwable $e) {
                $this->options['prefix_id'] = $this->options['prefix_id'] ?? [];
            }

            try {
                $q = \App\Models\SuffixTitle::query();
                try { $q = $q->orderBy('name', 'asc'); } catch (\Throwable $_) {}
                $this->options['suffix_id'] = $q->get()->mapWithKeys(function($r){
                    $base = $r->name ?? null;
                    $label = $base ? ($r->abbreviation ? $base . ' (' . $r->abbreviation . ')' : $base) : ($r->abbreviation ?: $r->id);
                    return [$r->id => $label];
                })->toArray();
            } catch (\Throwable $e) {
                $this->options['suffix_id'] = $this->options['suffix_id'] ?? [];
            }
        }
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
        return view('livewire.profile.credentials.modals.professional_credentials', [
            'fields' => $this->fields,
            'options' => $this->options,
            'state' => $this->state,
            'isOpen' => $this->isOpen,
            'itemId' => $this->itemId,
        ]);
    }
}
