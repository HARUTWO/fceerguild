<?php
namespace App\Http\Livewire\Profile\Credentials\Subsections\EducationalRecords;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\EducationalRecord;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Auth;
use Spatie\Activitylog\Models\Activity;
use Masmerise\Toaster\Toastable;
use App\Http\Livewire\Traits\SelectRows;

class EducationalRecordsArchiveModal extends Component
{
    use WithPagination;
    use SelectRows;
    use Toastable;

    public bool $open = false;
    public array $selected = [];
    public bool $selectAll = false;
    public bool $renderTrigger = false;
    public string $modelClass = EducationalRecord::class;
    public int $perPage = 15;

    protected $listeners = [
        'openEducationalArchive' => 'open',
        'refreshEducationalArchive' => 'handleRefresh',
    ];

    public function open()
    {
        $this->open = true;
        $this->clearSelection();
        $this->dispatch('savedCredential');
        $this->resetPage();
    }

    public function close()
    {
        $this->open = false;
    }

    public function restore($id)
    {
        $this->emit('confirmEducationalRestore', [
            'ids' => [$id],
            'modelClass' => $this->modelClass,
        ]);

        $this->open = false;
    }

    public function restoreSelected()
    {
        $models = ($this->modelClass)::withTrashed()->whereIn('id', $this->selected)->get();
        foreach ($models as $m) {
            $policy = Gate::getPolicyFor($m);
            if ($policy && method_exists($policy, 'restore')) {
                Gate::authorize('restore', $m);
            } else {
                Gate::authorize('manageReferenceTables');
            }
            $m->restore();
            if (function_exists('activity')) activity()->performedOn($m)->causedBy(Auth::user())->log('restored');
        }
        $this->clearSelection();
        $this->dispatch('savedCredential');
        $labels = $models->map(function ($m) {
            $prog = optional($m->degreeProgram)->name ?? ($m->degree_program_id ?? '');
            $uni = optional($m->university)->name ?? ($m->university_id ?? '');
            return trim($prog . ' - ' . $uni, " -");
        })->values()->all();
        $count = count($labels);
        if ($count === 1) {
            $this->success('Restored: ' . $labels[0]);
        } else {
            $preview = implode(', ', array_slice($labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Restored ' . $count . ' items: ' . $preview . $more);
        }
        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshEducationalArchive');
        $this->emit('refreshList');
        $this->emit('refreshEducationalArchive');
        $this->emit('refreshReferenceTable');
    }

    public function prepareBulkAction(string $action)
    {
        if ($action === 'restoreSelected') {
            $this->emit('confirmEducationalRestore', [
                'ids' => $this->selected,
                'modelClass' => $this->modelClass,
            ]);
        } elseif ($action === 'forceDeleteSelected') {
            $this->emit('confirmEducationalForceDelete', [
                'ids' => $this->selected,
                'modelClass' => $this->modelClass,
            ]);
        }

        $this->open = false;
    }

    public function forceDelete($id)
    {
        $this->emit('confirmEducationalForceDelete', [
            'ids' => [$id],
            'modelClass' => $this->modelClass,
        ]);

        $this->open = false;
    }

    public function forceDeleteSelected()
    {
        $models = ($this->modelClass)::withTrashed()->whereIn('id', $this->selected)->get();
        foreach ($models as $m) {
            $policy = Gate::getPolicyFor($m);
            if ($policy && method_exists($policy, 'forceDelete')) {
                Gate::authorize('forceDelete', $m);
            } else {
                Gate::authorize('manageReferenceTables');
            }
            $m->forceDelete();
            if (function_exists('activity')) activity()->performedOn($m)->causedBy(Auth::user())->log('force_deleted');
        }
        $this->clearSelection();
        $labels = $models->map(function ($m) {
            return $m->name ?? $m->title ?? (string) ($m->id ?? '');
        })->values()->all();
        $count = count($labels);
        if ($count === 1) {
            $this->success('Deleted permanently: ' . $labels[0]);
        } else {
            $preview = implode(', ', array_slice($labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Deleted permanently: ' . $preview . $more);
        }
        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshEducationalArchive');
        $this->emit('refreshList');
        $this->emit('refreshEducationalArchive');
        $this->emit('refreshReferenceTable');
    }

    public function render()
    {
        $items = ($this->modelClass)::onlyTrashed()->with('deletedBy')->paginate($this->perPage);
        $ids = $items->pluck('id')->all();
        if (!empty($ids)) {
            $activities = Activity::where('subject_type', $this->modelClass)
                ->whereIn('subject_id', $ids)
                ->where('description', 'deleted')
                ->latest()
                ->get()
                ->groupBy('subject_id');

            $items->getCollection()->transform(function ($item) use ($activities) {
                $group = $activities[(string) $item->getKey()] ?? $activities[$item->getKey()] ?? null;
                $actor = $item->deletedBy->name ?? $item->deletedBy->email ?? null;
                if (!$actor && $group && $group->first()?->causer) {
                    $actor = $group->first()->causer->name ?? $group->first()->causer->email ?? null;
                }
                $item->deleted_by_name = $actor;
                return $item;
            });
        }

        return view('livewire.profile.credentials.subsections.educational_records.archive-modal', compact('items'));
    }

    public function handleRefresh(): void
    {
        $this->clearSelection();
        $this->resetPage();
    }

    protected function getSelectablePageIds(): array
    {
        return ($this->modelClass)::onlyTrashed()->paginate($this->perPage, ['*'], 'page', $this->page ?? 1)
            ->pluck('id')
            ->map(fn($i) => (string) $i)
            ->toArray();
    }
}