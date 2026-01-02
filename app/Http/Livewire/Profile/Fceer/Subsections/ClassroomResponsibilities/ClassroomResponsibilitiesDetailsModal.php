<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use Livewire\Component;
use App\Models\ClassroomResponsibility;

class ClassroomResponsibilitiesDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showClassroomResponsibilities' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'classroom_id', 'label' => 'Classroom', 'type' => 'select'],
            ['key' => 'classroom_position_id', 'label' => 'Position', 'type' => 'select'],
            ['key' => 'note', 'label' => 'Note', 'type' => 'textarea'],
        ];
    }

    public function show($id): void
    {
        $model = ClassroomResponsibility::with(['createdBy', 'updatedBy', 'classroom', 'classroomPosition'])->findOrFail($id);

        $this->details = app(\App\Services\ReferenceDetailsBuilder::class)->build($model, $this->fields);

        $this->details['classroom_id'] = optional($model->classroom)->name ?? $model->classroom_id;
        $this->details['classroom_position_id'] = optional($model->classroomPosition)->name ?? $model->classroom_position_id;

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
        return view('livewire.profile.fceer.subsections.classroom_responsibilities.details-modal');
    }
}
