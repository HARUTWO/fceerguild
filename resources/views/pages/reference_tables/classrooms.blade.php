<x-layouts.app :title="__('classrooms')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">classrooms</h1>

            <livewire:reference-crud model-class="{{ \App\Models\Classroom::class }}" :config-key="'classrooms'" />
        </div>
    </div>
</x-layouts.app>
