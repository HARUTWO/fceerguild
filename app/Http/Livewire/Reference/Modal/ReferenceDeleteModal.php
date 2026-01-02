<?php

namespace App\Http\Livewire\Reference\Modal;

use Livewire\Component;
use App\Services\ActivityLogger;
use Masmerise\Toaster\Toastable;

class ReferenceDeleteModal extends Component
{
    use Toastable;
    public string $modelClass;
    public array $ids = [];
    public array $labels = [];
    public bool $open = false;

    protected $listeners = ['deleteReference', 'deleteReferences'];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass;
    }

    public function deleteReference($id): void
    {
        $this->ids = [(string) $id];
        $this->resolveLabels();
        $this->open = true;
    }

    public function deleteReferences($ids): void
    {
        $this->ids = array_map('strval', (array) $ids);
        $this->resolveLabels();
        $this->open = true;
    }

    protected function resolveLabels(): void
    {
        $this->labels = [];
        $modelClass = $this->modelClass;
        try {
            $this->labels = $modelClass::whereIn('id', $this->ids)
                ->get()
                ->map(function ($m) {
                    return $m->name ?? $m->title ?? (string) ($m->id ?? '');
                })->toArray();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm(): void
    {
        if (! auth()->check() || ! auth()->user()->can('manageReferenceTables', $this->modelClass)) {
            abort(403);
        }

        $logger = app(ActivityLogger::class);
        $modelClass = $this->modelClass;

        try {
            $deleted = 0;
            foreach ($this->ids as $id) {
                $m = $modelClass::find($id);
                if (! $m) {
                    continue;
                }

                $attributes = $m->getAttributes();
                $m->delete();

                $logger->logDelete($m, $attributes, auth()->user());
                $deleted++;
            }

            $this->dispatch('savedReference');
            $this->dispatch('refreshReferenceArchive');

            if ($deleted === 1) {
                $label = $this->labels[0] ?? ("ID {$this->ids[0]}");
                $this->success("Deleted: {$label}");
            } else {
                $this->success('Deleted ' . $deleted . ' records.');
            }

            $this->open = false;
            $this->ids = [];
            $this->labels = [];
        } catch (\Throwable $e) {
            $this->error('An error occurred while deleting the record(s).');
            throw $e;
        }
    }

    public function render()
    {
        return view('livewire.reference.modal.delete-modal');
    }
}
