<x-layouts.app :title="__('My Attendance')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        @livewire('attendance.user', ['userId' => $userId ?? null])
    </div>

    {{-- Modals --}}
    @livewire('attendance.export-modal')
</x-layouts.app>
