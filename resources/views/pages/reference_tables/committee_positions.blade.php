<x-layouts.app :title="__('Positions')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Committee Positions</h1>

            <livewire:reference-crud model-class="{{ \App\Models\CommitteePosition::class }}" :config-key="'committee_positions'" />
        </div>
    </div>
</x-layouts.app>
 