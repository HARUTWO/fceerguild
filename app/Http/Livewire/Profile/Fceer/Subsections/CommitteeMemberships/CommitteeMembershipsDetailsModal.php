<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships;

use Livewire\Component;
use App\Models\CommitteeMembership;

class CommitteeMembershipsDetailsModal extends Component
{
    public bool $open = false;
    public array $fields = [];
    public array $details = [];

    protected $listeners = ['showCommitteeMemberships' => 'show'];

    public function mount()
    {
        $this->fields = [
            ['key' => 'committee_id', 'label' => 'Committee', 'type' => 'select', 'relation_name' => 'committee', 'relation_label_key' => 'name'],
            ['key' => 'committee_position_id', 'label' => 'Position', 'type' => 'select', 'relation_name' => 'committeePosition', 'relation_label_key' => 'name'],
            ['key' => 'note', 'label' => 'Note', 'type' => 'textarea'],
        ];
    }

    public function show($id): void
    {
        $model = CommitteeMembership::with(['createdBy', 'updatedBy', 'committee', 'committeePosition'])->findOrFail($id);

        $this->details = app(\App\Services\ReferenceDetailsBuilder::class)->build($model, $this->fields);

        $this->details['committee_id'] = optional($model->committee)->name ?? $model->committee_id;
        $this->details['committee_position_id'] = optional($model->committeePosition)->name ?? $model->committee_position_id;

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
        return view('livewire.profile.fceer.subsections.committee_memberships.details-modal');
    }
}
