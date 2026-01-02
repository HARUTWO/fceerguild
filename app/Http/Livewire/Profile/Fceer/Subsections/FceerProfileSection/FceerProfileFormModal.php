<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\FceerProfile;
use App\Models\FceerBatch;
use App\Models\Classroom;

class FceerProfileFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?int $userId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\FceerProfile::class;

    public array $state = [];
    public array $fields = [];
    public array $options = [];
    public ?string $userRole = null;

    protected $listeners = ['openFceerProfileFormModal' => 'open'];

    public function open($params = [])
    {
        $instance = $params['instanceKey'] ?? null;
        if ($instance && $instance !== $this->modelClass) return;

        $this->reset(['itemId', 'state', 'fields', 'options', 'userId']);
        $this->itemId = $params['itemId'] ?? null;
        $this->userId = $params['userId'] ?? null;

        if (!$this->userId) {
            $this->addError('userId', 'User ID is required');
            return;
        }

        // Check authorization
        $user = \App\Models\User::find($this->userId);
        if (!$user || !Gate::allows('manageFceerProfile', $user)) {
            $this->dispatch('toaster', 
                message: 'You are not authorized to edit FCEER profiles. Only administrators, executives, and system managers can perform this action.',
                type: 'error'
            );
            return;
        }

        $this->userRole = $user->role->name ?? null;
        
        // Load options
        $this->options['batch_id'] = FceerBatch::orderBy('year', 'desc')
            ->orderBy('batch_no', 'desc')
            ->get()
            ->mapWithKeys(function ($batch) {
                return [$batch->id => $batch->batch_no . ' (' . $batch->year . ')'];
            })
            ->toArray();
        
        $this->options['student_group_id'] = Classroom::orderBy('name')->pluck('name', 'id')->toArray();
        
        $this->options['status'] = [
            '1' => 'Active',
            '0' => 'Inactive',
        ];

        // Define fields based on role
        $this->fields = $this->getFieldsForRole($this->userRole);

        // Load existing data
        if ($this->itemId) {
            $item = FceerProfile::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $this->state[$f['key']] = data_get($item, $f['key']);
                }
            }
        } else {
            // Ensure user_id is set
            $this->state['user_id'] = $this->userId;
        }

        $this->isOpen = true;
    }

    public function setField($field, $value)
    {
        $this->state[$field] = $value;
    }

    protected function getFieldsForRole(?string $role): array
    {
        $roleLower = strtolower($role ?? '');
        $isStudent = str_contains($roleLower, 'student');
        $isVolunteer = str_contains($roleLower, 'system') || str_contains($roleLower, 'executive') || str_contains($roleLower, 'instructor') || str_contains($roleLower, 'administrator');

        $fields = [];

        // Role-specific fields
        if ($isVolunteer) {
            $fields[] = ['key' => 'volunteer_number', 'type' => 'text', 'label' => 'Volunteer Number', 'required' => false];
        }

        if ($isStudent) {
            $fields[] = ['key' => 'student_number', 'type' => 'text', 'label' => 'Student Number', 'required' => false];
            $fields[] = ['key' => 'student_group_id', 'type' => 'searchable-select', 'label' => 'Student Group', 'options' => 'student_group_id', 'required' => false];
        }
        
        // Batch (for both roles)
        $fields[] = ['key' => 'batch_id', 'type' => 'searchable-select', 'label' => 'Batch', 'options' => 'batch_id', 'required' => false];
        
        // Status
        $fields[] = ['key' => 'status', 'type' => 'select', 'label' => 'Status', 'options' => 'status', 'required' => false];

        return $fields;
    }

    public function save()
    {
        // Only validate if there are required fields
        if (!empty($this->rules())) {
            $this->validate();
        }

        // Authorization check again
        $user = \App\Models\User::find($this->userId);
        if (!$user || !Gate::allows('manageFceerProfile', $user)) {
            $this->dispatch('toaster', 
                message: 'You are not authorized to edit FCEER profiles. Only administrators, executives, and system managers can perform this action.',
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

        // Ensure user_id is set
        $this->state['user_id'] = $this->userId;

        $this->dispatch('confirmFceerProfileSave', [
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
        foreach ($this->fields as $f) {
            if (!empty($f['required'])) {
                $rules['state.' . $f['key']] = 'required';
            }
        }
        return $rules;
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.fceer_profile_section.form-modal', [
            'fields' => $this->fields,
            'options' => $this->options,
            'state' => $this->state,
            'isOpen' => $this->isOpen,
            'itemId' => $this->itemId,
        ]);
    }
}
