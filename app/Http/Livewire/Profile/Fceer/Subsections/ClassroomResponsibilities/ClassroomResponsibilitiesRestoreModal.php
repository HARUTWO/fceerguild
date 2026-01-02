<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use Livewire\Component;
use App\Models\ClassroomResponsibility;
use Illuminate\Support\Facades\Gate;
use Masmerise\Toaster\Toastable;

class ClassroomResponsibilitiesRestoreModal extends Component
{
    use Toastable;

    public bool $open = false;
    public array $targetIds = [];
    public array $labels = [];

    protected $listeners = [
        'openClassroomResponsibilitiesRestoreModal' => 'open',
        'confirmClassroomResponsibilitiesRestore' => 'openFromConfirm',
    ];

    public function open($data)
    {
        if (is_array($data) && isset($data['ids'])) {
            $this->targetIds = $data['ids'];
        } elseif (is_numeric($data)) {
            $this->targetIds = [(int) $data];
        } else {
            $this->targetIds = [];
        }

        $this->resolveLabels();
        $this->open = true;
    }

    public function openFromConfirm($data)
    {
        $this->open($data);
    }

    protected function resolveLabels(): void
    {
        $this->labels = [];
        try {
            $this->labels = ClassroomResponsibility::onlyTrashed()
                ->with(['classroom', 'classroomPosition'])
                ->whereIn('id', $this->targetIds)
                ->get()
                ->map(function ($m) {
                    $name = optional($m->classroom)->name ?? (string) ($m->classroom_id ?? '');
                    $pos = optional($m->classroomPosition)->name ?? (string) ($m->classroom_position_id ?? '');
                    return trim($name . ($pos !== '' ? ' — ' . $pos : ''));
                })->values()->all();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm()
    {
        $items = ClassroomResponsibility::onlyTrashed()->whereIn('id', $this->targetIds)->get();

        foreach ($items as $item) {
            $policy = Gate::getPolicyFor($item);
            if ($policy && method_exists($policy, 'restore')) {
                Gate::authorize('restore', $item);
            } else {
                Gate::authorize('manage', $item->user);
            }

            $item->restore();

            if (function_exists('activity')) {
                activity()
                    ->performedOn($item)
                    ->causedBy(auth()->user())
                    ->log('restored');
            }
        }

        $count = count($this->labels);
        if ($count === 1) {
            $this->success('Restored: ' . $this->labels[0]);
        } else {
            $preview = implode(', ', array_slice($this->labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Restored ' . $count . ' items: ' . $preview . $more);
        }

        $this->open = false;
        $this->targetIds = [];
        $this->labels = [];
        $this->dispatch('savedCredential');
        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshClassroomResponsibilitiesArchive');
        $this->emit('refreshList');
        $this->emit('refreshClassroomResponsibilitiesArchive');
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.classroom_responsibilities.restore-modal');
    }
}
