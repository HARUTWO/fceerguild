<?php

namespace App\Http\Livewire\Roster;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Masmerise\Toaster\Toastable;

class RosterDeleteModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?int $userId = null;
    public ?User $user = null;
    public string $confirmEmail = '';

    protected $listeners = [
        'openRosterDeleteModal' => 'handleOpen',
    ];

    public function handleOpen(int $userId): void
    {
        Gate::authorize('deleteRosterUser');

        $this->userId = $userId;
        $this->user = User::find($userId);
        $this->confirmEmail = '';
        $this->resetValidation();

        if (!$this->user) {
            $this->error('User not found.');
            return;
        }

        $this->open = true;
    }

    public function close(): void
    {
        $this->open = false;
        $this->userId = null;
        $this->user = null;
        $this->confirmEmail = '';
        $this->resetValidation();
    }

    public function confirmDelete(): void
    {
        Gate::authorize('deleteRosterUser');

        if (!$this->user) {
            $this->error('User not found.');
            $this->close();
            return;
        }

        // Validate email matches
        if ($this->confirmEmail !== $this->user->email) {
            $this->addError('confirmEmail', 'Email does not match. Please type the full email to confirm.');
            return;
        }

        // Soft delete the user
        $this->user->deleted_by_id = Auth::id();
        $this->user->save();
        $this->user->delete();

        // Log activity
        if (function_exists('activity')) {
            activity()
                ->performedOn($this->user)
                ->causedBy(Auth::user())
                ->log('deleted');
        }

        $this->success('User deleted: ' . $this->user->name);
        $this->dispatch('refreshRosterTable');
        $this->close();
    }

    public function render()
    {
        return view('livewire.roster.roster-delete-modal');
    }
}
