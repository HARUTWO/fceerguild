<x-layouts.app :title="__('Degree Types')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Degree Types</h1>

            <livewire:reference-crud model-class="{{ \App\Models\University::class }}" :config-key="'degree_types'" />
        </div>
    </div>
</x-layouts.app>