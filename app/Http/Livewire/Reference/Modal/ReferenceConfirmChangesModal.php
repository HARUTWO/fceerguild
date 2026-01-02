<?php

namespace App\Http\Livewire\Reference\Modal;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class ReferenceConfirmChangesModal extends Component
{
    use Toastable;
    public string $modelClass;
    public array $changes = [];
    public array $state = [];
    public ?int $id = null;
    public bool $open = false;

    protected $listeners = ['confirmReferenceSave'];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass;
    }

    public function confirmReferenceSave($payload): void
    {
        $this->id = $payload['id'] ?? null;
        $this->state = $payload['state'] ?? [];
        $this->changes = $payload['changes'] ?? [];
        $this->modelClass = $payload['modelClass'] ?? $this->modelClass;
        $this->open = true;
    }

    public function confirm(): void
    {
        if (! auth()->check() || ! auth()->user()->can('manageReferenceTables', $this->modelClass)) {
            abort(403);
        }

        $logger = app(ActivityLogger::class);
        $modelClass = $this->modelClass;

        try {
            DB::transaction(function () use ($modelClass, $logger, &$m) {
                if ($this->id) {
                    $m = ($modelClass)::findOrFail($this->id);
                    $m->fill($this->state);
                    if (auth()->check()) {
                        $m->updated_by_id = auth()->id();
                    }
                    $m->save();
                    $logger->logCreateOrUpdate($m, 'updated', $this->changes, auth()->user());
                } else {
                    $m = new ($modelClass)();
                    $m->fill($this->state);
                    if (auth()->check()) {
                        $m->created_by_id = auth()->id();
                        $m->updated_by_id = auth()->id();
                    }
                    $m->save();
                    $logger->logCreateOrUpdate($m, 'created', $this->changes, auth()->user());
                }
            });

            $payload = ['modelClass' => $this->modelClass ?? null, 'configKey' => $this->configKey ?? null];
            $this->emit('savedReference', $payload);

            $label = $m->name ?? $m->title ?? $m->label ?? (method_exists($m, 'getKey') ? "ID {$m->getKey()}" : class_basename($m));

            $this->success("Saved: {$label}");

            $this->open = false;
        } catch (\Throwable $e) {
            $this->error('An error occurred while saving.');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.reference.modal.confirm-changes-modal');
    }
}
