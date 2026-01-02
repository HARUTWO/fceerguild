<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\ContactDetails;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\UserProfile;
use App\Models\Address;

class ContactDetailsFormModal extends Component
{
    public bool $isOpen = false;
    public ?int $itemId = null;
    public ?int $userId = null;
    public ?string $instanceKey = null;
    public string $modelClass = \App\Models\UserProfile::class;

    public array $state = [];
    public array $fields = [];
    public array $options = [];

    protected $listeners = ['openContactDetailsModal' => 'open'];

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
        if (!$user || !Gate::allows('managePersonal', $user)) {
            abort(403, 'You are not authorized to edit this profile.');
        }

        // Define fields
        $this->fields = $params['fields'] ?? [];

        // Load existing data
        if ($this->itemId) {
            $item = UserProfile::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $key = $f['key'] ?? null;
                    if ($key) {
                        // Check if it's a user field (email)
                        if (isset($f['user_field']) && $f['user_field']) {
                            $this->state[$key] = data_get($item->user, $key);
                        } else {
                            $this->state[$key] = data_get($item, $key);
                        }
                    }
                }
            }
        } else {
            // Ensure user_id is set
            $this->state['user_id'] = $this->userId;
        }

        $this->isOpen = true;
    }

    public function save()
    {
        // Build validation rules
        $rules = [];
        foreach ($this->fields as $f) {
            $key = $f['key'] ?? null;
            if (!$key) continue;

            $fieldRules = [];
            if ($f['required'] ?? false) $fieldRules[] = 'required';
            else $fieldRules[] = 'nullable';

            if (isset($f['max'])) $fieldRules[] = 'max:' . $f['max'];

            $rules["state.{$key}"] = implode('|', $fieldRules);
        }

        $this->validate($rules);

        // Authorization check again
        $user = \App\Models\User::find($this->userId);
        if (!$user || !Gate::allows('managePersonal', $user)) {
            abort(403, 'You are not authorized to edit this profile.');
        }

        $original = [];
        if ($this->itemId) {
            $item = $this->modelClass::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $key = $f['key'] ?? null;
                    if ($key) {
                        // Check if it's a user field (email)
                        if (isset($f['user_field']) && $f['user_field']) {
                            $original[$key] = data_get($item->user, $key);
                        } else {
                            $original[$key] = data_get($item, $key);
                        }
                    }
                }
            }
        }

        $changes = \App\Support\ChangeSetBuilder::from($this->fields, $original, $this->state);

        // Ensure user_id is set
        $this->state['user_id'] = $this->userId;

        $this->dispatch('confirmContactDetailsSave', [
            'id' => $this->itemId,
            'state' => $this->state,
            'changes' => $changes,
            'modelClass' => $this->modelClass,
        ]);

        $this->isOpen = false;
    }

    public function rules()
    {
        return [];
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.contact_details.form-modal');
    }
}
