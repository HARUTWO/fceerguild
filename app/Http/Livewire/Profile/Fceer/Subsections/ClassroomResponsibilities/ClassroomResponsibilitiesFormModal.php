<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\Classroom;
use App\Models\ClassroomPosition;
use App\Models\User;

class ClassroomResponsibilitiesFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\ClassroomResponsibility::class;

    public array $state = [];
    public array $fields = [];
    public array $options = [];
    public bool $hasDuplicate = false;
    public ?array $duplicate = null;

    protected $listeners = ['openCredentialsModal' => 'open', 'openClassroomResponsibilitiesFormModal' => 'open'];

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'classroom_id', 'type' => 'select', 'label' => 'Classroom', 'options' => 'classrooms', 'required' => true],
            ['key' => 'classroom_position_id', 'type' => 'select', 'label' => 'Position', 'options' => 'classroom_positions', 'required' => true],
            ['key' => 'note', 'type' => 'textarea', 'label' => 'Note'],
        ];
    }

    public function open($params = [])
    {
        $instance = $params['instanceKey'] ?? null;
        if ($instance && $instance !== $this->modelClass) return;

        $this->reset(['itemId','state','fields','options']);
        $this->itemId = $params['itemId'] ?? null;
        $this->fields = $this->getDefaultFields();

        $this->options['classroom_id'] = Classroom::orderBy('name')->pluck('name', 'id')->toArray();
        $this->options['classroom_position_id'] = ClassroomPosition::orderBy('name')->pluck('name', 'id')->toArray();

        $this->state = [];
        if ($this->itemId) {
            $item = $this->modelClass::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $this->state[$f['key']] = data_get($item, $f['key']);
                }
                if (!isset($this->state['user_id'])) {
                    $this->state['user_id'] = $item->user_id;
                }
            }
        } else {
            foreach ($this->fields as $f) {
                $this->state[$f['key']] = $params[$f['key']] ?? null;
            }
        }

        if (isset($params['userId']) && !isset($this->state['user_id'])) {
            $this->state['user_id'] = $params['userId'];
        }

        $this->isOpen = true;
        $this->checkDuplicate();
        
    }
    
    public function updatedStateClassroomId($val)
    {
        $this->checkDuplicate();
    }

    public function updatedStateClassroomPositionId($val)
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

        if (empty($this->state['classroom_id']) || empty($this->state['classroom_position_id'])) {
            return;
        }

        $existing = \App\Models\ClassroomResponsibility::withTrashed()
            ->with('user')
            ->where('classroom_id', $this->state['classroom_id'])
            ->where('classroom_position_id', $this->state['classroom_position_id'])
            ->when($this->itemId, fn($q) => $q->where('id', '!=', $this->itemId))
            ->first();

        if ($existing) {
            $this->hasDuplicate = true;
            $userName = optional($existing->user)->name ?? 'Unknown User';
            $this->duplicate = [
                'id' => $existing->getKey(),
                'user_id' => $existing->user_id,
                'user_name' => $userName,
                'note' => $existing->note ?? null,
                'trashed' => method_exists($existing, 'trashed') ? $existing->trashed() : false,
            ];
        }
    }
    public function save()
    {
        $this->validate($this->rules());
        $this->checkDuplicate();
        if ($this->hasDuplicate) {
            $msg = 'You already have a classroom responsibility for this classroom and position.';
            if (! empty($this->duplicate['trashed'])) {
                $msg .= ' (An archived record exists.)';
            }
            $this->addError('state.classroom_id', $msg);
            return;
        }

        $profileUser = null;
        if (! empty($this->state['user_id'])) {
            $profileUser = \App\Models\User::find($this->state['user_id']);
        }
        if ($profileUser && ! Gate::allows('manageClassroomResponsibilities', $profileUser)) {
            $this->dispatch('toaster', 
                message: 'You are not authorized to manage classroom responsibilities. Only system managers and executives can perform this action.',
                type: 'error'
            );
            return;
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

        $label = null;
        if (! empty($this->state['classroom_id'])) {
            $cid = $this->state['classroom_id'];
            $label = $this->options['classroom_id'][$cid] ?? null;
        }
        $position = $this->state['classroom_position_id'] ?? null;
        $displayLabel = trim(implode(' — ', array_filter([$label, $position])));

        $this->dispatch('confirmClassroomResponsibilitySave', [
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
        foreach ($this->getDefaultFields() as $f) {
            if (! empty($f['required'])) {
                $rules['state.'.$f['key']] = 'required';
            }
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
        return view('livewire.profile.fceer.subsections.classroom_responsibilities.form-modal', [
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
