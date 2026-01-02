<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\ProfessionalCredentials;

use Livewire\Component;
use App\Models\ProfessionalCredential;

class ProfessionalCredentialsDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showProfessionalCredential' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'field_of_work_id', 'label' => 'Field', 'type' => 'select'],
            ['key' => 'prefix_id', 'label' => 'Prefix', 'type' => 'select'],
            ['key' => 'suffix_id', 'label' => 'Suffix', 'type' => 'select'],
            ['key' => 'issued_on', 'label' => 'Title Issued On', 'type' => 'number'],
            ['key' => 'notes', 'label' => 'Notes', 'type' => 'textarea'],
        ];
    }

    public function show($id): void
    {
        $model = ProfessionalCredential::with(['createdBy', 'updatedBy', 'fieldOfWork', 'prefix', 'suffix'])->findOrFail($id);

        $this->details = app(\App\Services\ReferenceDetailsBuilder::class)->build($model, $this->fields);

        $this->details['_meta']['created_by'] = $model->createdBy ? ($model->createdBy->name ?? $model->createdBy->initials()) : ($this->details['_meta']['created_by'] ?? null);
        $this->details['_meta']['updated_by'] = $model->updatedBy ? ($model->updatedBy->name ?? $model->updatedBy->initials()) : ($this->details['_meta']['updated_by'] ?? null);
        $this->details['_meta']['created_at'] = $model->created_at ? $model->created_at->toDateTimeString() : ($this->details['_meta']['created_at'] ?? null);
        $this->details['_meta']['created_at_human'] = $model->created_at ? $model->created_at->diffForHumans() : ($this->details['_meta']['created_at_human'] ?? null);
        $this->details['_meta']['updated_at'] = $model->updated_at ? $model->updated_at->toDateTimeString() : ($this->details['_meta']['updated_at'] ?? null);
        $this->details['_meta']['updated_at_human'] = $model->updated_at ? $model->updated_at->diffForHumans() : ($this->details['_meta']['updated_at_human'] ?? null);

        $activityModel = config('activitylog.activity_model', \Spatie\Activitylog\Models\Activity::class);

        $activity = $activityModel::with('causer')
            ->where('subject_type', get_class($model))
            ->where('subject_id', $model->getKey())
            ->latest()
            ->limit(20)
            ->get();

        $this->details['_meta']['activity'] = app(\App\Services\ActivityHistoryNormalizer::class)->normalize($activity, $this->fields);

        $this->open = true;
    }

    public function render()
    {
        return view('livewire.profile.credentials.subsections.professional_credentials.details-modal');
    }
}
