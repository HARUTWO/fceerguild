<?php

namespace App\Http\Livewire\Roster;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Masmerise\Toaster\Toastable;

class RosterForceDeleteModal extends Component
{
    use Toastable;

    public bool $open = false;
    public ?int $userId = null;
    public ?User $user = null;
    public string $confirmEmail = '';

    protected $listeners = [
        'openRosterForceDeleteModal' => 'handleOpen',
    ];

    public function handleOpen(int $userId): void
    {
        Gate::authorize('forceDeleteRosterUser');

        $this->userId = $userId;
        $this->user = User::onlyTrashed()->find($userId);
        $this->confirmEmail = '';
        $this->resetValidation();

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
        $this->confirmEmail = '';
        $this->resetValidation();
    }

    public function confirmForceDelete(): void
    {
        Gate::authorize('forceDeleteRosterUser');

        if (!$this->user) {
            $this->error('User not found.');
            $this->close();
            return;
        }

        // Validate email matches
        if ($this->confirmEmail !== $this->user->email) {
            $this->addError('confirmEmail', 'Email does not match. Please type the full email to confirm permanent deletion.');
            return;
        }

        $userName = $this->user->name;

        // Log activity before deletion
        if (function_exists('activity')) {
            activity()
                ->causedBy(Auth::user())
                ->withProperties([
                    'deleted_user_id' => $this->user->id,
                    'deleted_user_name' => $userName,
                    'deleted_user_email' => $this->user->email,
                ])
                ->log('force_deleted_user');
        }

        // Permanently delete the user
        $this->user->forceDelete();

        $this->success('User permanently deleted: ' . $userName);
        $this->dispatch('refreshRosterTable');
        $this->dispatch('refreshRosterArchive');
        $this->close();
    }

    public function render()
    {
        return view('livewire.roster.roster-force-delete-modal');
    }
}
