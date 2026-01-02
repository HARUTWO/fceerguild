<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolSubjectRecords;

use Livewire\Component;

class HighschoolSubjectRecordsDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showHighschoolSubjectRecord' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'highschool_subject_id', 'label' => 'Subject', 'type' => 'select'],
            ['key' => 'grade', 'label' => 'Grade', 'type' => 'text'],
            ['key' => 'highschool_record_id', 'label' => 'Highschool Record', 'type' => 'select'],
        ];
    }

    public function show($id): void
    {
        $model = \App\Models\HighschoolSubjectRecord::with(['createdBy', 'updatedBy', 'subject', 'highschoolRecord.highschool'])->findOrFail($id);

        $this->details = app(\App\Services\ReferenceDetailsBuilder::class)->build($model, $this->fields);

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
        return view('livewire.profile.credentials.subsections.highschool_subject_records.details-modal');
    }
}
