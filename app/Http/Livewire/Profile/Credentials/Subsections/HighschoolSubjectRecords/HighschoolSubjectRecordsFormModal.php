<?php

    namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords;

    use Livewire\Component;

    class HighschoolSubjectRecordsFormModal extends Component
    {
        public bool $isOpen = false;
        public ?int $itemId = null;
        public ?string $instanceKey = null;
        public string $modelClass = \App\Models\HighschoolSubjectRecord::class;

        public array $data = [];
        public array $state = [];
        public array $fields = [];
        public array $options = [];

        protected $listeners = ['openCredentialsModal' => 'open'];

        protected string $sectionKey = 'highschool_subject_records';

        protected function getDefaultFields(): array
        {
            return [
                ['key' => 'highschool_subject_id', 'type' => 'select', 'label' => 'Subject', 'options' => 'highschool_subjects', 'required' => true],
                ['key' => 'grade', 'type' => 'select', 'label' => 'Grade', 'options' => [
                    'fair (70-80)' => 'Fair (70-80)',
                    'good (80-90)' => 'Good (80-90)',
                    'great (91-95)' => 'Great (91-95)',
                    'exceptional (96-100)' => 'Exceptional (96-100)'
                ], 'required' => true],
                ['key' => 'highschool_record_id', 'type' => 'select', 'label' => 'Highschool Record', 'options' => 'highschool_records', 'required' => true],
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

            $userId = $params['userId'] ?? $this->data['user_id'] ?? null;
            if (! isset($this->options['highschool_record_id']) || empty($this->options['highschool_record_id'])) {
                if ($userId) {
                    try {
                        $records = \App\Models\HighschoolRecord::with('highschool')->where('user_id', $userId)->orderBy('year_started', 'desc')->get();
                        $map = [];
                        foreach ($records as $r) {
                            $label = optional($r->highschool)->name ?? 'Highschool';
                            if (! empty($r->year_started) || ! empty($r->year_ended)) {
                                $label .= ' (' . ($r->year_started ?? '—') . '–' . ($r->year_ended ?? '—') . ')';
                            }
                            $map[$r->id] = $label;
                        }
                        $this->options['highschool_record_id'] = $map;
                    } catch (\Throwable $e) {
                        \Log::error('HighschoolSubjectRecordsFormModal: failed to build highschool_record_id options', ['error' => $e->getMessage()]);
                        $this->options['highschool_record_id'] = [];
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
            return view('livewire.profile.credentials.subsections.highschool_subject_records.form-modal', [
                'fields' => $this->fields,
                'options' => $this->options,
                'state' => $this->state,
                'isOpen' => $this->isOpen,
                'itemId' => $this->itemId,
            ]);
        }
    }