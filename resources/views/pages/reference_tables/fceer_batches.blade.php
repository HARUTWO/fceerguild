<x-layouts.app :title="__('FCEER Batches')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">FCEER Batches</h1>

            <livewire:reference-crud model-class="{{ \App\Models\FceerBatch::class }}" :config-key="'fceer_batches'" />
        </div>
    </div>
</x-layouts.app>
