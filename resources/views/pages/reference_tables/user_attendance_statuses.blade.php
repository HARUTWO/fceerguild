<x-layouts.app :title="__('User Attendance Statuses')">
    <div class="flex h-full w-full flex-1 flex-col gap-4 rounded-xl">
        <div class="container mx-auto p-6">
            <h1 class="text-2xl font-semibold mb-4">User Attendance Statuses</h1>

            <livewire:reference-crud model-class="{{ \App\Models\UserAttendanceStatus::class }}" :config-key="'user_attendance_statuses'" />
        </div>
    </div>
</x-layouts.app>
