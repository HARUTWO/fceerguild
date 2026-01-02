<x-layouts.app :title="__('Profile')">
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-3">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:profile.show :user="$user ?? null" :section="$section ?? null" />
        </div>
    </div>
</x-layouts.app>