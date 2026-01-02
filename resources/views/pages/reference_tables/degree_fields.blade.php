<x-layouts.app :title="__('Provinces')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Degree Fields</h1>

            <livewire:reference-crud model-class="{{ \App\Models\DegreeField::class }}" :config-key="'degree_fields'" />
        </div>
    </div>
</x-layouts.app>
