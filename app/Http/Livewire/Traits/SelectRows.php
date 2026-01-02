<?php

namespace App\Http\Livewire\Traits;

trait SelectRows
{
    /**
     * Implementing component must return current page IDs (stringable).
     *
     * @return array<int|string>
     */
    abstract protected function getSelectablePageIds(): array;

    /**
     * Livewire hook called when $selectAll updates.
     *
     * @param mixed $value
     * @return void
     */
    public function updatedSelectAll($value): void
    {
        if ($value) {
            $this->selected = $this->getSelectablePageIds();
        } else {
            $this->selected = [];
        }

        $this->updatedSelected();
    }

    /**
     * Livewire hook called when $selected updates.
     *
     * Normalizes IDs and sets $selectAll when all page IDs are selected.
     *
     * @return void
     */
    public function updatedSelected(): void
    {
        $pageIds = $this->getSelectablePageIds();

        if (empty($pageIds)) {
            $this->selectAll = false;
            return;
        }

        $pageIds = $this->normalizeIds($pageIds);
        $selected = $this->normalizeIds($this->selected ?? []);

        $intersection = array_intersect($pageIds, $selected);
        $this->selectAll = count($intersection) === count($pageIds);
    }

    /**
     * Toggle "select all" for the current page.
     *
     * @return void
     */
    public function toggleSelectAll(): void
    {
        $this->selectAll = ! ($this->selectAll ?? false);

        if ($this->selectAll) {
            $this->selected = $this->getSelectablePageIds();
        } else {
            $this->selected = [];
        }

        $this->updatedSelected();
    }

    /**
     * Toggle a single row's selection.
     *
     * @param string $id
     * @return void
     */
    public function toggleRow(string $id): void
    {
        $id = (string) $id;
        $selected = $this->selected ?? [];

        if (in_array($id, $selected, true)) {
            $this->selected = array_values(array_diff($selected, [$id]));
        } else {
            $selected[] = $id;
            $this->selected = $selected;
        }

        $this->updatedSelected();
    }

    /**
     * Ensure a single id is selected (append if missing).
     *
     * @param string $id
     * @return void
     */
    public function selectEnsure(string $id): void
    {
        $id = (string) $id;
        $selected = $this->selected ?? [];

        if (! in_array($id, $selected, true)) {
            $selected[] = $id;
            $this->selected = $selected;
            $this->updatedSelected();
        }
    }

    /**
     * Select only the given id (deselect others).
     *
     * @param string $id
     * @return void
     */
    public function selectOnly(string $id): void
    {
        $this->selected = [(string) $id];
        $this->selectAll = false;

        $this->updatedSelected();
    }

    /**
     * Clear all selection for the component.
     *
     * @return void
     */
    public function clearSelection(): void
    {
        $this->selected = [];
        $this->selectAll = false;
    }

    /**
     * Normalize an array of IDs to string values.
     *
     * @param iterable $ids
     * @return array<string>
     */
    protected function normalizeIds(iterable $ids): array
    {
        return array_map('strval', (array) $ids);
    }
}