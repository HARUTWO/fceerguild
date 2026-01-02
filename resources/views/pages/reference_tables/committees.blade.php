<x-layouts.app :title="__('Committees')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Committees</h1>

            <livewire:reference-crud model-class="{{ \App\Models\Committee::class }}" :config-key="'committees'" />
        </div>
    </div>
</x-layouts.app>
