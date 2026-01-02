<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships;

use App\Models\User;
use Livewire\Component;

class CommitteeMemberships extends Component
{
    public User $user;
    public string $view = 'table';
    public array $selected = [];
    public bool $selectAll = false;
    public array $fields = [
        ['key' => 'committee', 'label' => 'Committee', 'type' => 'select'],
        ['key' => 'committee_position', 'label' => 'Position', 'type' => 'text'],
    ];

    protected $listeners = [
        'savedCredential' => 'handleSavedCredential',
        'refreshList' => '$refresh',
        'refreshCommitteeMembershipsArchive' => '$refresh',
        'refreshReferenceTable' => '$refresh',
    ];

    public function handleSavedCredential()
    {
        $this->selected = [];
        $this->selectAll = false;
        $this->emit('refreshList');
    }

    public function mount(User $user)
    {
        $this->user = $user;
        $this->authorize('view', $this->user);
    }

    public function setView(string $view)
    {
        $this->view = $view;
    }

    public function create()
    {
        $this->authorize('manageCommitteeMemberships', $this->user);

        $this->emit('requestOpenProfileModal', [
            'instanceKey' => \App\Models\CommitteeMembership::class,
            'modalView' => null,
            'userId' => $this->user->id,
        ]);
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selected = \App\Models\CommitteeMembership::where('user_id', $this->user->id)->pluck('id')->map(fn($id) => (string)$id)->toArray();
        } else {
            $this->selected = [];
        }
    }

    public function toggleRow($key)
    {
        if (in_array($key, $this->selected)) {
            $this->selected = array_diff($this->selected, [$key]);
        } else {
            $this->selected[] = $key;
        }
    }

    public function selectEnsure($key)
    {
        if (!in_array($key, $this->selected)) {
            $this->selected[] = $key;
        }
    }

    public function relayEdit($itemId)
    {
        $this->authorize('manageCommitteeMemberships', $this->user);

        $this->emit('requestOpenProfileModal', [
            'instanceKey' => \App\Models\CommitteeMembership::class,
            'itemId' => $itemId,
            'userId' => $this->user->id,
        ]);
    }

    public function relayShow($itemId)
    {
        $this->emit('showCommitteeMemberships', $itemId);
    }

    public function relayDelete($itemId)
    {
        $this->authorize('manageCommitteeMemberships', $this->user);
        $this->emit('openCommitteeMembershipsDeleteModal', (int) $itemId);
    }

    public function openArchive()
    {
        $this->emit('openCommitteeMembershipsArchive');
    }

    public function deleteSelected()
    {
        $this->authorize('manageCommitteeMemberships', $this->user);

        $ids = array_values(array_map('strval', (array) $this->selected));
        $this->emit('openCommitteeMembershipsDeleteModal', ['ids' => $ids]);
    }

    public function render()
    {
        $records = \App\Models\CommitteeMembership::where('user_id', $this->user->id)->orderBy('id', 'desc')->paginate(15);
        return view('livewire.profile.fceer.subsections.committee_memberships.index', compact('records'));
    }
}
