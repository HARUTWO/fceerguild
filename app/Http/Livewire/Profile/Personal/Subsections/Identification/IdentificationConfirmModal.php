<?php

namespace App\Http\Livewire\Profile\Personal\Subsections\Identification;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class IdentificationConfirmModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?string $modelClass = null;
    public ?int $id = null;
    public array $state = [];
    public array $changes = [];

    protected $listeners = ['confirmIdentificationSave' => 'confirmPayload'];

    public function confirmPayload($payload)
    {
        $modelClass = $payload['modelClass'] ?? null;
        if ($modelClass !== \App\Models\UserProfile::class && ($payload['instanceKey'] ?? null) !== \App\Models\UserProfile::class) {
            return;
        }

        $this->modelClass = $modelClass;
        $this->id = $payload['id'] ?? null;
        $this->state = $payload['state'] ?? [];
        $this->changes = $payload['changes'] ?? [];
        $this->open = true;
    }

    public function confirm()
    {
        if (!auth()->check()) abort(403);

        $logger = app(ActivityLogger::class);

        try {
            DB::transaction(function () use (&$m, $logger) {
                $modelClass = $this->modelClass ?? \App\Models\UserProfile::class;
                if ($this->id) {
                    $m = ($modelClass)::findOrFail($this->id);
                    $m->fill($this->state);
                    $m->updated_by_id = auth()->id();
                    $m->save();
                    try {
                        $logger->logCreateOrUpdate($m, 'updated', $this->changes, auth()->user());
                    } catch (\Throwable $e) {
                        \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]);
                    }
                } else {
                    $m = new ($modelClass)();
                    $m->fill($this->state);
                    $m->created_by_id = auth()->id();
                    $m->updated_by_id = auth()->id();
                    $m->save();
                    try {
                        $logger->logCreateOrUpdate($m, 'created', $this->changes, auth()->user());
                    } catch (\Throwable $e) {
                        \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]);
                    }
                }
            });

            $this->emit('refreshIdentification');

            $this->success("Identification information saved successfully.");
            $this->open = false;
        } catch (\Throwable $e) {
            $this->error('An error occurred while saving.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.profile.personal.subsections.identification.confirm-modal', [
            'changes' => $this->changes,
            'open' => $this->open,
        ]);
    }
}
