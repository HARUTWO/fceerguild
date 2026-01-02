<x-layouts.app.sidebar :title="$title ?? null">
    <flux:main class="transition-all duration-150">
        {{ $slot }}
    </flux:main>
</x-layouts.app.sidebar>
