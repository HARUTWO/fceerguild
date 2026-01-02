<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords;

use Livewire\Component;
use Masmerise\Toaster\Toastable;

class EducationalForceDeleteModal extends Component
{
    use Toastable;

    public string $modelClass;
    public array $ids = [];
    public array $labels = [];
    public bool $open = false;

    protected $listeners = ['confirmEducationalForceDelete' => 'confirm'];

    public function mount($modelClass = null)
    {
        $this->modelClass = $modelClass ?? \App\Models\EducationalRecord::class;
    }

    public function confirm($payload): void
    {
        if (is_array($payload) && isset($payload['ids'])) {
            $this->ids = array_map('strval', (array) $payload['ids']);
        } else {
            $this->ids = (array) array_map('strval', (array) $payload);
        }

        $models = ($this->modelClass)::withTrashed()->whereIn('id', $this->ids)->get();
        foreach ($models as $m) {
            $m->forceDelete();
        }

        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshEducationalArchive');
        $this->emit('refreshList');
        $this->emit('refreshEducationalArchive');
        $this->emit('refreshReferenceTable');

        $this->success('Deleted permanently');
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.educational_records.force-delete-modal');
    }
}
