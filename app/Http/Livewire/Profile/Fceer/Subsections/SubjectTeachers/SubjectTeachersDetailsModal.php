<?php
namespace App\Http\Livewire\Profile\Fceer\Subsections\SubjectTeachers;

use Livewire\Component;
use App\Models\SubjectTeacher;

class SubjectTeachersDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showSubjectTeacher' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'volunteer_subject_id', 'label' => 'Volunteer Subject', 'type' => 'select'],
            ['key' => 'subject_proficiency', 'label' => 'Proficiency', 'type' => 'text'],
        ];
    }

    public function show($id): void
    {
        $model = SubjectTeacher::with(['createdBy', 'updatedBy', 'volunteerSubject'])->findOrFail($id);

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
        return view('livewire.profile.fceer.subsections.subject_teachers.details-modal');
    }
}
