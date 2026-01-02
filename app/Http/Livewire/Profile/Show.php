<?php

namespace App\Http\Livewire\Profile;

use App\Models\User;
use App\Models\ProfilePicture;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Show extends Component
{
    public User $user;
    public string $active = 'personal'; // personal | account | credentials | fceer
    protected $queryString = [
        'active' => ['except' => 'personal'],
    ];
    protected array $availableSections = ['personal', 'account', 'credentials', 'fceer'];
    
    protected $listeners = ['refreshSidebarAvatar' => 'refreshUser'];

    public function mount($user = null, $section = null)
    {
        // If $user is a User model (from route binding), use it directly
        // If $user is an ID, find the user
        // If null, use the authenticated user
        if ($user instanceof User) {
            $this->user = $user;
        } elseif ($user) {
            $this->user = User::findOrFail($user);
        } else {
            // Default to authenticated user when no user is provided
            $this->user = Auth::user() ?? abort(401);
        }
        
        $this->authorize('view', $this->user);

        // Prefer route segment (path) like /profile/{user}/{section}
        $sectionFromRoute = $section ?? request()->route('section');
        $sectionFromQuery = request()->query('active');

        $resolvedSection = $sectionFromRoute ?? $sectionFromQuery ?? $this->active;
        if (in_array($resolvedSection, $this->availableSections)) {
            $this->active = $resolvedSection;
        }
    }

    public function refreshUser()
    {
        $this->user = User::findOrFail($this->user->id);
    }

    public function setActive(string $section)
    {
        if (! in_array($section, $this->availableSections)) {
            return;
        }

        $this->active = $section;
    }

    public function render()
    {
        $currentPicture = ProfilePicture::where('user_id', $this->user->id)
            ->where('is_current', true)
            ->with('attachment')
            ->first();

        return view('livewire.profile.show', [
            'user' => $this->user,
            'active' => $this->active,
            'currentPicture' => $currentPicture,
        ]);
    }
}