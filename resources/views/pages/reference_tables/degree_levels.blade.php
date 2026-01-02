<x-layouts.app :title="__('Degree Levels')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Degree Levels</h1>

            <livewire:reference-crud model-class="{{ \App\Models\DegreeLevel::class }}" :config-key="'degree_levels'" />
        </div>
    </div>
</x-layouts.app>