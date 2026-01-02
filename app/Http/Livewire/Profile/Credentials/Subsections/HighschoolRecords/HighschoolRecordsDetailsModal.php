<?php

namespace App\Http\Livewire\Profile\Credentials\Subsections\HighschoolRecords;

use Livewire\Component;
use App\Models\HighschoolRecord;

class HighschoolRecordsDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showHighschoolRecord' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'highschool_id', 'label' => 'Highschool', 'type' => 'select'],
            ['key' => 'year_started', 'label' => 'Year Started', 'type' => 'number'],
            ['key' => 'level', 'label' => 'Level', 'type' => 'text'],
            ['key' => 'year_ended', 'label' => 'Year Ended', 'type' => 'number'],
        ];
    }

    public function show($id): void
    {
        $model = HighschoolRecord::with(['createdBy', 'updatedBy', 'highschool'])->findOrFail($id);

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
        return view('livewire.profile.credentials.subsections.highschool_records.details-modal');
    }
}
