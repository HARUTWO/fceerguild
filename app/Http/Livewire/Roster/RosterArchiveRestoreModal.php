<?php

namespace App\Http\Livewire\Roster;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Masmerise\Toaster\Toastable;

class RosterArchiveRestoreModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?int $userId = null;
    public ?User $user = null;

    protected $listeners = [
        'openArchiveRestoreModal' => 'handleOpen',
    ];

    public function handleOpen(int $userId): void
    {
        $this->userId = $userId;
        $this->user = User::onlyTrashed()->find($userId);

        if (!$this->user) {
            $this->error('User not found in archive.');
            return;
        }

        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->userId = null;
        $this->user = null;
    }

    public function confirmRestore(): void
    {
        if (!$this->user) {
            $this->error('User not found.');
            $this->close();
            return;
        }

        $this->user->restore();
        $this->user->update(['deleted_by_id' => null]);

        // Log activity
        if (function_exists('activity')) {
            activity()
                ->performedOn($this->user)
                ->causedBy(Auth::user())
                ->log('restored');
        }

        $this->success('User restored: ' . $this->user->name);
        $this->dispatch('refreshRosterTable');
        $this->dispatch('refreshRosterArchive');
        $this->close();
    }

    public function render()
    {
        return view('livewire.roster.roster-archive-restore-modal');
    }
}
