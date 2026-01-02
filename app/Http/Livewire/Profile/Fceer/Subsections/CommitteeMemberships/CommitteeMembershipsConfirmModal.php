<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class CommitteeMembershipsConfirmModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?string $modelClass = null;
    public ?int $id = null;
    public array $state = [];
    public array $changes = [];

    protected $listeners = ['confirmCommitteeMembershipSave' => 'confirmPayload'];

    public function confirmPayload($payload)
    {
        $modelClass = $payload['modelClass'] ?? null;
        if ($modelClass !== \App\Models\CommitteeMembership::class && ($payload['instanceKey'] ?? null) !== \App\Models\CommitteeMembership::class) {
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
        if (! auth()->check()) abort(403);

        $logger = app(ActivityLogger::class);

        try {
            DB::transaction(function () use (&$m, $logger) {
                $modelClass = $this->modelClass ?? \App\Models\CommitteeMembership::class;
                if ($this->id) {
                    $m = ($modelClass)::findOrFail($this->id);
                    $m->fill($this->state);
                    $m->updated_by_id = auth()->id();
                    $m->save();
                    try { $logger->logCreateOrUpdate($m, 'updated', $this->changes, auth()->user()); } catch (\Throwable $e) { \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]); }
                } else {
                    $m = new ($modelClass)();
                    $m->fill($this->state);
                    $m->created_by_id = auth()->id();
                    $m->updated_by_id = auth()->id();
                    $m->save();
                    try { $logger->logCreateOrUpdate($m, 'created', $this->changes, auth()->user()); } catch (\Throwable $e) { \Log::debug('ActivityLogger::logCreateOrUpdate failed', ['e' => $e]); }
                }
            });

            $this->emit('savedCredential');
            if (! empty($this->modelClass)) {
                $this->emit('savedCredential:'.$this->modelClass, ['id' => $m->getKey() ?? null]);
            }

            $label = $this->state['label'] ?? null;
            if (! $label) {
                $name = optional($m->committee)->name ?? ($m->committee_id ? 'Committee #' . $m->committee_id : null);
                $pos = optional($m->committeePosition)->name ?? ($m->committee_position_id ? 'Position #' . $m->committee_position_id : null);
                $label = trim($name . ($pos ? ' — ' . $pos : ''));
            }

            $this->success("Saved: {$label}");
            $this->open = false;
        } catch (\Throwable $e) {
            $this->error('An error occurred while saving.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.committee_memberships.confirm-changes-modal', [
            'changes' => $this->changes,
            'open' => $this->open,
        ]);
    }
}
