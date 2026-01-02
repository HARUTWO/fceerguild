<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\FceerProfileSection;

use Livewire\Component;
use App\Services\ReferenceDetailsBuilder;

class FceerProfileDetailsModal extends Component
{
    public bool $open = false;
    public array $details = [];
    public array $fields = [];

    protected $listeners = ['showFceerProfile' => 'show'];

    public function show($params)
    {
        $itemId = $params['itemId'] ?? null;
        if (!$itemId) return;

        $builder = app(ReferenceDetailsBuilder::class);
        $this->details = $builder->build(\App\Models\FceerProfile::class, $itemId);

        $this->fields = [
            ['key' => 'volunteer_number', 'label' => 'Volunteer Number'],
            ['key' => 'student_number', 'label' => 'Student Number'],
            ['key' => 'batch_id', 'label' => 'Batch'],
            ['key' => 'student_group_id', 'label' => 'Student Group'],
            ['key' => 'status', 'label' => 'Status'],
            ['key' => 'notes', 'label' => 'Notes'],
        ];

        $this->open = true;
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.fceer_profile_section.details-modal', [
            'details' => $this->details,
            'fields' => $this->fields,
            'open' => $this->open,
        ]);
    }
}
