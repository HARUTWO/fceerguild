<?php

namespace App\Http\Livewire\Roster;

use App\Models\FceerProfile;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Masmerise\Toaster\Toastable;

class RosterUserFormModal extends Component
{
    use Toastable;

    public bool $open = false;
    public string $type = 'volunteers'; // 

    // Form fields
    public string $username = '';
    public string $email = '';
    public string $number = ''; // 
    public ?int $roleId = null;

    protected $listeners = [
        'openRosterUserForm' => 'handleOpen',
    ];

    protected function rules(): array
    {
        return [
            'username' => 'required|string|min:3|max:50|unique:users,name',
            'email' => 'required|email|unique:users,email',
            'number' => 'required|string|max:50',
            'roleId' => 'required|exists:user_roles,id',
        ];
    }

    protected function messages(): array
    {
        return [
            'username.required' => 'Username is required.',
            'username.unique' => 'This username is already taken.',
            'email.required' => 'Email is required.',
            'email.unique' => 'This email is already registered.',
            'number.required' => $this->type === 'students' ? 'Student number is required.' : 'Volunteer number is required.',
            'roleId.required' => 'Please select a role.',
        ];
    }

    public function handleOpen(string $type = 'volunteers'): void
    {
        Gate::authorize('createRosterUser');

        $this->type = $type;
        $this->reset(['username', 'email', 'number', 'roleId']);
        $this->resetValidation();

        // Set default role based on type
        $this->setDefaultRole();

        $this->open = true;
    }

    protected function setDefaultRole(): void
    {
        $roleNames = $this->getFilteredRoleNames();

        // Get first matching role
        $role = UserRole::whereIn('name', $roleNames)->first();
        $this->roleId = $role?->id;
    }

    protected function getFilteredRoleNames(): array
    {
        $baseRoles = $this->type === 'students'
            ? config('roster.student_roles', ['Student'])
            : config('roster.volunteer_roles', []);

        // If current user is Administrator, restrict to lower roles (no System Manager, Executive)
        $currentUserRole = Auth::user()->role?->name;
        if ($currentUserRole === 'Administrator') {
            return array_filter($baseRoles, fn($role) => !in_array($role, ['System Manager', 'Executive']));
        }

        // System Manager and Executive can see all roles
        return $baseRoles;
    }

    public function getAvailableRolesProperty(): array
    {
        $roleNames = $this->getFilteredRoleNames();

        return UserRole::whereIn('name', $roleNames)
            ->pluck('name', 'id')
            ->toArray();
    }

    public function close(): void
    {
        $this->open = false;
        $this->reset(['username', 'email', 'number', 'roleId']);
        $this->resetValidation();
    }

    public function save(): void
    {
        Gate::authorize('createRosterUser');

        $this->validate();

        $password = $this->username . $this->number;

        $user = User::create([
            'name' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($password),
            'role_id' => $this->roleId,
            'created_by_id' => Auth::id(),
            'is_active' => true,
        ]);

        $profileData = [
            'user_id' => $user->id,
            'created_by_id' => Auth::id(),
        ];

        if ($this->type === 'students') {
            $profileData['student_number'] = $this->number;
        } else {
            $profileData['volunteer_number'] = $this->number;
        }

        FceerProfile::create($profileData);

        if (function_exists('activity')) {
            activity()
                ->performedOn($user)
                ->causedBy(Auth::user())
                ->withProperties([
                    'type' => $this->type,
                    'number' => $this->number,
                ])
                ->log('created');
        }

        $label = $this->type === 'students' ? 'Student' : 'Volunteer';
        $this->success("{$label} created: {$this->username}");

        $this->dispatch('refreshRosterTable');
        $this->close();
    }

    public function render()
    {
        return view('livewire.roster.roster-user-form-modal', [
            'availableRoles' => $this->availableRoles,
        ]);
    }
}
