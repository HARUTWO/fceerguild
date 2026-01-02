<x-layouts.app :title="__('classroom positions')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Classroom Positions</h1>

            <livewire:reference-crud model-class="{{ \App\Models\ClassroomPosition::class }}" :config-key="'classroom_positions'" />
        </div>
    </div>
</x-layouts.app>
