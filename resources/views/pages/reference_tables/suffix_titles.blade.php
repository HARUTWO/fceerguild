<x-layouts.app :title="__('Suffix Titles')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Suffix Titles</h1>

            <livewire:reference-crud model-class="{{ \App\Models\SuffixTitle::class }}" :config-key="'suffix_titles'" />
        </div>
    </div>
</x-layouts.app>
