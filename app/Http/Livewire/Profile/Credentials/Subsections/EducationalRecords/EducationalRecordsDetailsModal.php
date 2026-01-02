<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords;

use Livewire\Component;
use App\Models\EducationalRecord;

class EducationalRecordsDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showEducationalRecord' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'degree_program_id', 'label' => 'Program', 'type' => 'select'],
            ['key' => 'university_id', 'label' => 'University', 'type' => 'select'],
            ['key' => 'year_started', 'label' => 'Year Started', 'type' => 'number'],
            ['key' => 'year_graduated', 'label' => 'Year Graduated', 'type' => 'number'],
            ['key' => 'dost_scholarship', 'label' => 'DOST Scholarship', 'type' => 'checkbox'],
            ['key' => 'latin_honor', 'label' => 'Latin Honor', 'type' => 'text'],
        ];
    }

    public function show($id): void
    {
        $model = EducationalRecord::with(['createdBy', 'updatedBy', 'degreeProgram', 'university'])->findOrFail($id);

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
        return view('livewire.profile.credentials.subsections.educational_records.details-modal');
    }
}
