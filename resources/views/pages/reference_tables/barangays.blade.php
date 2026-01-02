<x-layouts.app :title="__('Barangays')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Barangays</h1>

            <livewire:reference-crud model-class="{{ \App\Models\Barangay::class }}" :config-key="'barangays'" />
        </div>
    </div>
</x-layouts.app>