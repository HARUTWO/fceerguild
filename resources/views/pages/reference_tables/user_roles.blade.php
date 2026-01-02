<x-layouts.app :title="__('User Roles')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">User Roles</h1>

            <livewire:reference-crud model-class="{{ \App\Models\UserRole::class }}" :config-key="'user_roles'" />
        </div>
    </div>
</x-layouts.app>
