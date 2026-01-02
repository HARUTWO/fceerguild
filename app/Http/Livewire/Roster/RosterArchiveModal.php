<?php

namespace App\Http\Livewire\Roster;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;
use Masmerise\Toaster\Toastable;
use Spatie\Activitylog\Models\Activity;

class RosterArchiveModal extends Component
{
    use WithPagination;
    use Toastable;

    public bool $open = false;
    public array $selected = [];
    public bool $selectAll = false;
    public string $type = 'volunteers'; // 'volunteers' or 'students'
    public int $perPage = 15;

    protected $listeners = [
        'openRosterArchive' => 'handleOpen',
        'open-archive-modal' => 'handleOpen',
        'refreshRosterArchive' => 'handleRefresh',
    ];

    public function handleOpen($type = null, array $params = [])
    {
        if (is_string($type)) {
            $this->type = $type;
        } elseif (is_array($type)) {
            $this->type = $type['type'] ?? 'volunteers';
        } else {
            $this->type = $params['type'] ?? 'volunteers';
        }
        $this->open = true;
        $this->clearSelection();
        $this->resetPage();
    }

    public function close()
    {
        $this->open = false;
        $this->clearSelection();
    }

    public function toggleRow(string $id): void
    {
        if (in_array($id, $this->selected)) {
            $this->selected = array_values(array_diff($this->selected, [$id]));
        } else {
            $this->selected[] = $id;
        }
    }

    public function updatedSelectAll(): void
    {
        if ($this->selectAll) {
            $this->selected = $this->getQuery()
                ->paginate($this->perPage)
                ->pluck('id')
                ->map(fn($id) => (string) $id)
                ->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    public function restore($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        Gate::authorize('viewRoster');
        
        $user->restore();
        $user->update(['deleted_by_id' => null]);
        
        if (function_exists('activity')) {
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->log('restored');
        }

        $this->success('Restored: ' . $user->name);
        $this->dispatch('refreshRosterTable');
        $this->resetPage();
    }

    public function restoreSelected()
    {
        Gate::authorize('viewRoster');
        
        $users = User::onlyTrashed()->whereIn('id', $this->selected)->get();
        
        foreach ($users as $user) {
            $user->restore();
            $user->update(['deleted_by_id' => null]);
            
            if (function_exists('activity')) {
                activity()
                    ->performedOn($user)
                    ->causedBy(Auth::user())
                    ->log('restored');
            }
        }

        $this->clearSelection();
        
        $count = $users->count();
        $labels = $users->pluck('name')->take(3)->toArray();
        
        if ($count === 1) {
            $this->success('Restored: ' . $labels[0]);
        } else {
            $preview = implode(', ', $labels);
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Restored ' . $count . ' users: ' . $preview . $more);
        }
        
        $this->dispatch('refreshRosterTable');
        $this->resetPage();
    }

    public function forceDelete($id)
    {
        $user = User::onlyTrashed()->findOrFail($id);
        
        Gate::authorize('viewRoster');
        
        $userName = $user->name;
        $user->forceDelete();
        
        if (function_exists('activity')) {
            activity()
                ->causedBy(Auth::user())
                ->log('force_deleted user: ' . $userName);
        }

        $this->success('Permanently deleted: ' . $userName);
        $this->dispatch('refreshRosterTable');
        $this->resetPage();
    }

    public function forceDeleteSelected()
    {
        Gate::authorize('viewRoster');
        
        $users = User::onlyTrashed()->whereIn('id', $this->selected)->get();
        
        $count = $users->count();
        $labels = $users->pluck('name')->take(3)->toArray();
        
        foreach ($users as $user) {
            $userName = $user->name;
            $user->forceDelete();
            
            if (function_exists('activity')) {
                activity()
                    ->causedBy(Auth::user())
                    ->log('force_deleted user: ' . $userName);
            }
        }

        $this->clearSelection();
        
        if ($count === 1) {
            $this->success('Permanently deleted: ' . $labels[0]);
        } else {
            $preview = implode(', ', $labels);
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Permanently deleted ' . $count . ' users: ' . $preview . $more);
        }
        
        $this->dispatch('refreshRosterTable');
        $this->resetPage();
    }

    protected function getQuery()
    {
        $roles = $this->type === 'students'
            ? config('roster.student_roles', ['Student'])
            : config('roster.volunteer_roles', []);

        $eagerLoad = ['role', 'deletedBy', 'fceerProfile'];

        return User::query()
            ->onlyTrashed()
            ->with($eagerLoad)
            ->whereHas('role', function ($q) use ($roles) {
                $q->whereIn('name', $roles);
            });
    }

    public function render()
    {
        $items = $this->getQuery()->paginate($this->perPage);
        
        // Get the volunteer/student number field name
        $numberField = $this->type === 'students' ? 'student_number' : 'volunteer_number';

        return view('livewire.roster.archive-modal', [
            'items' => $items,
            'numberField' => $numberField,
        ]);
    }

    public function handleRefresh(): void
    {
        $this->clearSelection();
        $this->resetPage();
    }
}
