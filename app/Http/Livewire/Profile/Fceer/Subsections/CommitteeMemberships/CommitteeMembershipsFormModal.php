<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;

class CommitteeMembershipsFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\CommitteeMembership::class;

    public array $state = [];
    public array $fields = [];
    public array $options = [];
    public bool $hasDuplicate = false;
    public ?array $duplicate = null;

    protected $listeners = ['openCredentialsModal' => 'open', 'openCommitteeMembershipsFormModal' => 'open'];

    protected function getDefaultFields(): array
    {
        return [
            ['key' => 'committee_id', 'type' => 'select', 'label' => 'Committee', 'options' => 'committees', 'required' => true],
            ['key' => 'committee_position_id', 'type' => 'select', 'label' => 'Position', 'options' => 'committee_positions', 'required' => true],
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
    
    public function updatedStateCommitteeId($val)
    {
        $this->checkDuplicate();
    }

    public function updatedStateCommitteePositionId($val)
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

        if (empty($this->state['committee_id']) || empty($this->state['committee_position_id']) || empty($this->state['user_id'])) {
            return;
        }

        $existing = \App\Models\CommitteeMembership::withTrashed()
            ->where('user_id', $this->state['user_id'])
            ->where('committee_id', $this->state['committee_id'])
            ->where('committee_position_id', $this->state['committee_position_id'])
            ->first();

        if ($existing && (! $this->itemId || $existing->getKey() != $this->itemId)) {
            $this->hasDuplicate = true;
            $this->duplicate = [
                'id' => $existing->getKey(),
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
            $msg = 'You already have a committee membership for this committee.';
            if (! empty($this->duplicate['trashed'])) {
                $msg .= ' (An archived record exists.)';
            }
            $this->addError('state.committee_id', $msg);
            return;
        }

        $profileUser = null;
        if (! empty($this->state['user_id'])) {
            $profileUser = \App\Models\User::find($this->state['user_id']);
        }
        if ($profileUser && ! Gate::allows('manageCommitteeMemberships', $profileUser)) {
            $this->dispatch('toaster', 
                message: 'You are not authorized to manage committee memberships. Only system managers and executives can perform this action.',
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
        if (! empty($this->state['committee_id'])) {
            $cid = $this->state['committee_id'];
            $label = $this->options['committee_id'][$cid] ?? null;
        }
        $position = $this->state['committee_position_id'] ?? null;
        $displayLabel = trim(implode(' — ', array_filter([$label, $position])));

        $this->dispatch('confirmCommitteeMembershipSave', [
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
        return view('livewire.profile.fceer.subsections.committee_memberships.form-modal', [
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
