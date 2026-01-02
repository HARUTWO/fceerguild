<x-layouts.app :title="__('Review Seasons')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">Review Seasons</h1>

            <livewire:reference-crud model-class="{{ \App\Models\ReviewSeason::class }}" :config-key="'review_seasons'" />
        </div>
    </div>
</x-layouts.app>
