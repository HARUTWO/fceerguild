<x-layouts.app :title="__('Fields of Work')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Fields of Work</h1>

            <livewire:reference-crud model-class="{{ \App\Models\FieldOfWork::class }}" :config-key="'fields_of_work'" />
        </div>
    </div>
</x-layouts.app>
