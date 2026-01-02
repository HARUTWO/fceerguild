<?php

namespace App\Http\Livewire\Reference\Modal;

use Livewire\Component;
use App\Services\ReferenceDetailsBuilder;
use App\Services\ActivityHistoryNormalizer;
use App\Services\ReferenceFieldOptionResolver;

class ReferenceDetailsModal extends Component
{
    public string $modelClass;
    public ?string $configKey;
    public array $fields = [];
    public array $details = [];
    public bool $open = false;
    protected array $rawFields = [];

    protected $listeners = ['showReference'];

    public function mount($modelClass = null, $configKey = null)
    {
        $this->modelClass = $modelClass;
        $this->configKey = $configKey;
        $this->rawFields = config('reference-tables.' . $this->configKey, []);
        $this->fields = collect($this->rawFields)->map(function ($f) {
            if (isset($f['options']) && is_callable($f['options'])) {
                $f['options'] = [];
            }
            return $f;
        })->toArray();
    }

    public function showReference($id): void
    {
        $model = ($this->modelClass)::with(['createdBy', 'updatedBy'])->findOrFail($id);

        $source = ! empty($this->rawFields) ? $this->rawFields : $this->fields;
        $resolver = app(ReferenceFieldOptionResolver::class);
        $resolvedFields = $resolver->resolve($source);

        $this->details = app(ReferenceDetailsBuilder::class)->build($model, $resolvedFields);

        $activityModel = config('activitylog.activity_model', \Spatie\Activitylog\Models\Activity::class);

        $activity = $activityModel::with('causer')
            ->where('subject_type', get_class($model))
            ->where('subject_id', $model->getKey())
            ->latest()
            ->limit(20)
            ->get();

        $this->details['_meta']['activity'] = app(ActivityHistoryNormalizer::class)->normalize($activity, $resolvedFields);

        $this->open = true;
    }

    public function render()
    {
        return view('livewire.reference.modal.details-modal');
    }
}
