<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\Address;

use Livewire\Component;
use App\Models\Address;
use App\Models\UserProfile;
use Illuminate\Support\Facades\DB;
use Masmerise\Toaster\Toaster;
use App\Services\ActivityLogger;

class AddressConfirmModal extends Component
{
    public $open = false;
    public $id = null;
    public $userId = null;
    public $state = [];
    public $changes = [];
    public $modelClass;

    protected $listeners = ['confirmAddressSave' => 'confirmPayload'];

    public function confirmPayload($payload)
    {
        $this->id = $payload['id'] ?? null;
        $this->userId = $payload['userId'] ?? null;
        $this->state = $payload['state'] ?? [];
        $this->changes = $payload['changes'] ?? [];
        $this->modelClass = $payload['modelClass'] ?? Address::class;
        $this->open = true;
    }

    public function confirm()
    {
        try {
            $logger = app(ActivityLogger::class);
            
            DB::beginTransaction();

            if ($this->id) {
                // Update existing address
                $item = $this->modelClass::find($this->id);
                if ($item) {
                    $item->update($this->state);
                    
                    // Ensure profile is still linked to this address
                    if ($this->userId) {
                        $profile = UserProfile::firstOrCreate(['user_id' => $this->userId]);
                        if ($profile->address_id != $item->id) {
                            $profile->update(['address_id' => $item->id]);
                        }
                    }
                    
                    try {
                        $logger->logCreateOrUpdate($item, 'update', $this->changes, auth()->user());
                    } catch (\Throwable $e) {
                        \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]);
                    }
                }
            } else {
                // Create new address
                $item = $this->modelClass::create($this->state);
                
                // Link to user profile
                if ($this->userId) {
                    $profile = UserProfile::firstOrCreate(['user_id' => $this->userId]);
                    $profile->update(['address_id' => $item->id]);
                }
                
                try {
                    $logger->logCreateOrUpdate($item, 'create', $this->changes, auth()->user());
                } catch (\Throwable $e) {
                    \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]);
                }
            }

            DB::commit();

            Toaster::success('Address saved successfully.');
            $this->dispatch('refreshAddress');
            $this->open = false;
            $this->reset(['id', 'userId', 'state', 'changes']);
        } catch (\Exception $e) {
            DB::rollBack();
            Toaster::error('Error saving address: ' . $e->getMessage());
        }
    }

    public function close()
    {
        $this->open = false;
        $this->reset(['id', 'userId', 'state', 'changes']);
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.address.confirm-modal');
    }
}
