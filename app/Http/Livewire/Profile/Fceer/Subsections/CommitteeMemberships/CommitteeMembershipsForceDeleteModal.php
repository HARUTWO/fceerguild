<?php

namespace App\Http\Livewire\Profile\Fceer\Subsections\CommitteeMemberships;

use Livewire\Component;
use App\Models\CommitteeMembership;
use Illuminate\Support\Facades\Gate;
use Masmerise\Toaster\Toastable;

class CommitteeMembershipsForceDeleteModal extends Component
{
    use Toastable;

    public bool $open = false;
    public array $targetIds = [];
    public array $labels = [];

    protected $listeners = [
        'openCommitteeMembershipsForceDeleteModal' => 'open',
        'confirmCommitteeMembershipsForceDelete' => 'openFromConfirm',
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
            $this->labels = CommitteeMembership::onlyTrashed()
                ->with(['committee', 'committeePosition'])
                ->whereIn('id', $this->targetIds)
                ->get()
                ->map(function ($m) {
                    $name = optional($m->committee)->name ?? (string) ($m->committee_id ?? '');
                    $pos = optional($m->committeePosition)->name ?? (string) ($m->committee_position_id ?? '');
                    return trim($name . ($pos !== '' ? ' — ' . $pos : ''));
                })->toArray();
        } catch (\Throwable $e) {
            $this->labels = [];
        }
    }

    public function confirm()
    {
        $items = CommitteeMembership::onlyTrashed()->whereIn('id', $this->targetIds)->get();

        foreach ($items as $item) {
            $policy = Gate::getPolicyFor($item);
            if ($policy && method_exists($policy, 'forceDelete')) {
                Gate::authorize('forceDelete', $item);
            } else {
                Gate::authorize('manageCommitteeMemberships', $item->user);
            }

            if (function_exists('activity')) {
                activity()
                    ->performedOn($item)
                    ->causedBy(auth()->user())
                    ->log('force_deleted');
            }

            $item->forceDelete();
        }

        $count = count($this->labels);
        if ($count === 1) {
            $this->success('Permanently deleted: ' . $this->labels[0]);
        } else {
            $preview = implode(', ', array_slice($this->labels, 0, 3));
            $more = $count > 3 ? ' and ' . ($count - 3) . ' more' : '';
            $this->success('Permanently deleted ' . $count . ' items: ' . $preview . $more);
        }

        $this->open = false;
        $this->targetIds = [];
        $this->labels = [];
        $this->dispatch('savedCredential');
        $this->dispatch('refreshReferenceTable');
        $this->dispatch('refreshCommitteeMembershipsArchive');
        $this->emit('refreshList');
        $this->emit('refreshCommitteeMembershipsArchive');
    }

    public function render()
    {
        return view('livewire.profile.fceer.subsections.committee_memberships.force-delete-modal');
    }
}
