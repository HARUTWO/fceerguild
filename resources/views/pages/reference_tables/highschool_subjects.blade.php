<x-layouts.app :title="__('Highschool Subjects')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Highschool Subjects</h1>

            <livewire:reference-crud model-class="{{ \App\Models\HighschoolSubject::class }}" :config-key="'highschool_subjects'" />
        </div>
    </div>
</x-layouts.app>
