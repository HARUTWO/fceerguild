<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\ClassroomResponsibilities;

use Livewire\Component;
use App\Models\ClassroomResponsibility;
use Illuminate\Support\Facades\Gate;
use Masmerise\Toaster\Toastable;

class ClassroomResponsibilitiesDeleteModal extends Component
{
    use Toastable;

    public bool $open = false;
    public array $targetIds = [];
    public array $labels = [];

    protected $listeners = ['openClassroomResponsibilitiesDeleteModal' => 'open'];

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

    protected function resolveLabels(): void
    {
        $this->labels = [];
        try {
            $this->labels = ClassroomResponsibility::with(['classroom', 'classroomPosition'])
                ->whereIn('id', $this->targetIds)
                ->get()
                ->map(function ($m) {
                    $name = optional($m->classroom)->name ?? (string) ($m->classroom_id ?? '');
                    $pos = optional($m->classroomPosition)->name ?? (string) ($m->classroom_position_id ?? '');
                    return trim($name . ($pos !== '' ? ' — ' . $pos : ''));
                })->toArray();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm()
    {
        $items = ClassroomResponsibility::whereIn('id', $this->targetIds)->get();

        foreach ($items as $item) {
            $policy = Gate::getPolicyFor($item);
            if ($policy && method_exists($policy, 'delete')) {
                Gate::authorize('delete', $item);
            } else {
                Gate::authorize('manage', $item->user);
            }

            $item->deleted_by_id = auth()->id();
            $item->save();
            $item->delete();

            if (function_exists('activity')) {
                activity()
                    ->performedOn($item)
                    ->causedBy(auth()->user())
                    ->log('deleted');
            }
        }

        $count = count($this->labels);
        if ($count === 1) {
            $this->success('Deleted: ' . $this->labels[0]);
        } else {
            $preview = implode(', ', array_slice($this->labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Deleted ' . $count . ' items: ' . $preview . $more);
        }

        $this->open = false;
        $this->targetIds = [];
        $this->labels = [];
        $this->dispatch('savedCredential');
        $this->dispatch('refreshReferenceTable');
        $this->emit('refreshList');
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.classroom_responsibilities.delete-modal');
    }
}
