<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\Address;

use Livewire\Component;
use Illuminate\Support\Facades\Gate;
use App\Models\Address;
use App\Models\UserProfile;

class AddressFormModal extends Component
{
    public $isOpen = false;
    public $itemId = null;
    public $userId = null;
    public $profileId = null;
    public $fields = [];
    public $state = [];
    public $modelClass;
    public $options = [];

    protected $listeners = ['openAddressFormModal' => 'open'];

    public function open($params)
    {
        // Authorization check
        $user = \App\Models\User::find($params['userId'] ?? null);
        if (!$user || !Gate::allows('managePersonal', $user)) {
            abort(403, 'You are not authorized to edit this profile.');
        }

        $this->userId = $params['userId'];
        $this->itemId = $params['id'] ?? null;
        $this->modelClass = $params['modelClass'] ?? Address::class;

        // Define fields
        $this->fields = $params['fields'] ?? [];

        // Load options
        $this->options['barangays'] = \App\Models\Barangay::with('city')
            ->get()
            ->mapWithKeys(function ($barangay) {
                return [$barangay->id => $barangay->name . ' (' . ($barangay->city->name ?? '') . ')'];
            })
            ->toArray();
        
        $this->options['cities'] = \App\Models\City::with('province')
            ->get()
            ->mapWithKeys(function ($city) {
                return [$city->id => $city->name . ' (' . ($city->province->name ?? '') . ')'];
            })
            ->toArray();
        
        $this->options['provinces'] = \App\Models\Province::orderBy('name')->pluck('name', 'id')->toArray();

        // Load existing data
        if ($this->itemId) {
            $item = Address::find($this->itemId);
            if ($item) {
                foreach ($this->fields as $f) {
                    $key = $f['key'] ?? null;
                    if ($key) {
                        $this->state[$key] = data_get($item, $key);
                    }
                }
            }
        } else {
            // Get profile to check if address exists
            $profile = UserProfile::where('user_id', $this->userId)->first();
            if ($profile && $profile->address_id) {
                $address = Address::find($profile->address_id);
                if ($address) {
                    $this->itemId = $address->id;
                    foreach ($this->fields as $f) {
                        $key = $f['key'] ?? null;
                        if ($key) {
                            $this->state[$key] = data_get($address, $key);
                        }
                    }
                }
            }
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
            if ($key === 'barangay_id') $fieldRules[] = 'exists:barangays,id';
            if ($key === 'city_id') $fieldRules[] = 'exists:cities,id';
            if ($key === 'province_id') $fieldRules[] = 'exists:provinces,id';

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
                        $original[$key] = data_get($item, $key);
                    }
                }
            }
        }

        $changes = \App\Support\ChangeSetBuilder::from($this->fields, $original, $this->state);

        $this->dispatch('confirmAddressSave', [
            'id' => $this->itemId,
            'userId' => $this->userId,
            'state' => $this->state,
            'changes' => $changes,
            'modelClass' => $this->modelClass,
        ]);

        $this->isOpen = false;
    }

    public function close()
    {
        $this->isOpen = false;
        $this->reset(['itemId', 'userId', 'fields', 'state', 'options']);
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.address.form-modal');
    }
}
