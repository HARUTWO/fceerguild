<x-layouts.app :title="__('Provinces')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <div class="flex items-center gap-3 mb-4">
                <flux:icon name="map-pin-house" />
                <h1 class="text-2xl font-semibold mb-0">Provinces</h1>
            </div>

            <livewire:reference-crud model-class="{{ \App\Models\Province::class }}" :config-key="'provinces'" />
        </div>
    </div>
</x-layouts.app>
