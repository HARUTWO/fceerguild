<?php

namespace App\Http\Livewire\Reference\Modal;

use Livewire\Component;
use Masmerise\Toaster\Toastable;

class ReferenceForceDeleteModal extends Component
{
    use \Masmerise\Toaster\Toastable;
    public bool $open = false;
    public array $ids = [];
    public string $modelClass;

    protected $listeners = [
        'openReferenceForceDelete' => 'open',
        'confirmReferenceForceDelete' => 'confirmReferenceForceDelete',
    ];

    public function open($ids = [])
    {
        $this->ids = is_array($ids) ? $ids : [$ids];
        $this->open = true;
    }

    public function confirmReferenceForceDelete($payload = [])
    {
        if (is_array($payload) && isset($payload['ids'])) {
            $this->ids = is_array($payload['ids']) ? $payload['ids'] : [$payload['ids']];
            if (isset($payload['modelClass'])) $this->modelClass = $payload['modelClass'];
        } else {
            $this->ids = is_array($payload) ? $payload : [$payload];
        }

        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
        $this->ids = [];
    }

    public function confirm()
    {
        $modelClass = $this->modelClass;
        $models = $modelClass::withTrashed()->whereIn('id', $this->ids)->get();

        $labels = $models->map(function ($m) {
            return $m->name ?? $m->title ?? (string) ($m->id ?? '');
        })->values()->all();

        foreach ($models as $m) {
            $m->forceDelete();
            if (function_exists('activity')) activity()->performedOn($m)->causedBy(auth()->user())->log('force_deleted');
        }

        $count = count($labels);
        if ($count === 1) {
            $this->success('Deleted permanently: ' . $labels[0]);
        } else {
            $preview = implode(', ', array_slice($labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Deleted permanently: ' . $preview . $more);
        }
        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshReferenceArchive');
        $this->close();
    }

    public function render()
    {
        return view('livewire.reference.modal.force-delete-modal');
    }
}
