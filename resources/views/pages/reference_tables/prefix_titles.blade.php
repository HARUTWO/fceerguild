<x-layouts.app :title="__('Prefix Titles')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Prefix Titles</h1>

            <livewire:reference-crud model-class="{{ \App\Models\PrefixTitle::class }}" :config-key="'prefix_titles'" />
        </div>
    </div>
</x-layouts.app>
